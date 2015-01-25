<?php
namespace Feenance\controllers\Api;

use Feenance\Model\Transaction;
use Feenance\Model\Transfer;
use Feenance\Model\BankString;
use Feenance\Model\TransactionStatus;

use Markfee\Responder\Respond;
use Markfee\Responder\Transformer;
use Feenance\Misc\Transformers\TransactionTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use \Exception;
use \Input;
use \Validator;
use Symfony\Component\HttpFoundation\Response as ResponseCodes;
use \DB;
use \SplFileObject;
use Illuminate\Support\MessageBag;
use \Carbon\Carbon;

class TransactionsController extends BaseController {

  protected $paginateCount = 110; // alter with ?perPage=nnn in url
  protected static $default_with = ["balance", "source.sourceAccount", "destination.destinationAccount", "bankString", "payee", "category.parent", "status"];

  /**
   * @return Transformer
  */
  protected function getTransformer() {
    return $this->transformer ?: new TransactionTransformer;
  }

  /**
   * Display a listing of transactions
   *
   * @param null $account_id: integer
   * @return \Illuminate\Support\Facades\Response
   */
  public function index($account_id = null)
  {
    if (!empty($account_id)) {
      $records = Transaction::where("account_id", $account_id)->orderBy('date', "DESC")->orderBy('id', "DESC")->with(TransactionsController::$default_with)->paginate($this->paginateCount);
    } else {
      $records = Transaction::orderBy('date', "DESC")->orderBy('id', "DESC")->with(TransactionsController::$default_with)->paginate($this->paginateCount);
    }
    return Respond::Paginated($records, $this->transformCollection($records->all()));
  }

  /**
   * Display a listing of transactions
   * @param $reconciled: bool
   * @param $account_id
   * @return \Illuminate\Support\Facades\Response
   */
  private function _reconciled($reconciled, $account_id)
  {
    $query = Transaction::where("reconciled", $reconciled);
    if (!empty($account_id)) {
      $query->where("account_id", $account_id);
    }
    $records = $query->orderBy('date', "DESC")->orderBy('id', "DESC")->with(TransactionsController::$default_with)->paginate($this->paginateCount);
    return Respond::Paginated($records, $this->transformCollection($records->all()));
  }

  /**
   * @param null $account_id
   * @return mixed
   */
  public static function unreconciledCount($account_id = null) {
    $query = Transaction::where("reconciled", false);
    if (!empty($account_id)) {
      $query->where("account_id", $account_id);
    }
    return $query->count();
  }



  /**
   * @param null $account_id
   * @return mixed
   */
  public function reconciled($account_id = null) {
    return $this->_reconciled(true, $account_id);
  }

  /**
   * @param null $account_id
   * @return mixed
   */
  public function unreconciled($account_id = null) {
    return $this->_reconciled(false, $account_id);
  }

  /**
   * @param null $account_id
   * @return mixed
   */
  public function deleteUnreconciled($account_id) {
//    return $this->unreconciled($account_id);

    Transaction::startBulk();
    $query = Transaction::where("reconciled", false)->where("account_id", $account_id)->delete();
    Transaction::finishBulk(true);
  }

  /**
   * @param null $account_id
   * @return mixed
   */
  public function reconcileAll($account_id) {
    Transaction::startBulk();
    $query = Transaction::where("account_id", $account_id)->update(["reconciled" => true, "status_id" => TransactionStatus::RECONCILED]);
    Transaction::finishBulk(true);
  }

  /**
   * Return list of transactions with a specific bank string
   *
   * @return Response
   */
  public function bank_strings($bank_string_id)
  {
    $records = Transaction::where("bank_string_id", $bank_string_id)->orderBy('date', "DESC")->orderBy('id', "DESC")->with(TransactionsController::$default_with)->paginate($this->paginateCount);
//    dd($records);
    return Respond::Paginated($records, $this->transformCollection($records->all()));
  }

  /**
   * Updates all transactions that contain the specific bank string and do not already have a Payee or category set
   *
   * Will also update bank_strings likewise.
   *
   * @param $bank_string_id
   * @return \Illuminate\Http\JsonResponse
   */
  public function bank_strings_update($bank_string_id) {

    $payeeId    = Input::get("payee_id", 0);
    $categoryId = Input::get("category_id", 0);


    $queryBuilder = Transaction::where("bank_string_id", "=", $bank_string_id)
      ->whereNull(DB::Raw("NULLIF(payee_id, $payeeId)"), 'AND')
      ->whereNull(DB::Raw("NULLIF(category_id, $categoryId)"), 'AND');

    $transactionUpdateCount = $queryBuilder->update(Input::only(["payee_id", "category_id"]));

    $queryBuilder = BankString::where("id", "=", $bank_string_id)
      ->whereNull(DB::Raw("NULLIF(payee_id, $payeeId)"), 'AND')
      ->whereNull(DB::Raw("NULLIF(category_id, $categoryId)"), 'AND');

    $bankStringUpdateCount = $queryBuilder->update(Input::only(["payee_id", "category_id"]));




    return Respond::Updated($transactionUpdateCount);
  }


  /**
   * Display the specified transaction.
   *444
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
    try {
      $transaction = Transaction::findOrFail($id);
    } catch(ModelNotFoundException $ex) {
      return Respond::NotFound("Transaction not found");
    }
    return Respond::Raw($this->transform($transaction));
  }


	/**
	 * Store a newly created transaction in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
    $transfer_id = (empty(Input::all()["transfer_id"]) ? null : Input::all()["transfer_id"]);

    $validator = Validator::make($data = $this->transformInput(Input::all()), Transaction::$rules);

    if ($validator->fails())		{
      Respond::WithErrors($validator->getMessageBag());
      return Respond::ValidationFailed();
    }
    if (!empty($transfer_id)) {
      return $this->createTransfer($data, $transfer_id);
    }
    try {
      $transaction = Transaction::create($data);
      Respond::setStatusCode(ResponseCodes::HTTP_CREATED);
      return Respond::Raw($this->transform($transaction));
    } catch(Exception $ex) {
      $messageBag = new MessageBag();
      $messageBag->add($ex->getCode(), $ex->getMessage());
      return Respond::WithErrors($messageBag);
    }
	}

  private function createTransfer($data, $transfer_id) {
    $transfer = $data;
    $transfer["account_id"] = $transfer_id;
    $data["amount"] *= -1;
    DB::beginTransaction();{

      $source       = Transaction::create($data);
      $destination  = Transaction::create($transfer);
      $transfer = new Transfer();
      $transfer->source = $source->id;
      $transfer->destination = $destination->id;
      $transfer->save();
    }
    DB::commit();
//    $collection = $source->all()->merge($destination->all());
    $collection=[];
    $collection[] = $source->first();
    $collection[] = $destination->first();
    return Respond::Created($this->transformCollection($collection));
  }

  /**
   * Update the specified transaction in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update($id)
	{
		$transaction = Transaction::findOrFail($id);
    $validator = Validator::make($data = $this->transformInput(Input::all()), Transaction::$rules);
    if ($validator->fails()) {
      return Respond::ValidationFailed();
    }
    $transaction->update($data);
    return Respond::Raw($this->transform($transaction));
	}

  /**
   * Upload a file to the server for import.
   *
   * @return Response
   */
  public function upload() {
    try {
      $file       = Input::file('file');
      $account_id = Input::get("account_id");
      if ( empty($file) || empty($account_id) ) {
        return Respond::ValidationFailed("Invalid file uploaded");
      }

      $SplFileObject = $file->openFile('r');
      $this->uploadFile($account_id, $SplFileObject);
    } catch (Exception $ex) {
      $messageBag = new MessageBag();
      $messageBag->add("badFormat", "Unable to process uploaded csv file");
      $messageBag->add($ex->getCode(), $ex->getMessage());

      return Respond::WithErrors($messageBag);

    }
  }

  /**
   * Upload a file to the server for import.
   *
   * @param  SplFileObject $SplFileObject
   * @return Response
   */
  static public function uploadFile($account_id, $SplFileObject) {
    try {
      // TODO - UNCOMMENT THESE PRINTS AND CREATE A LOG
      $header = $SplFileObject->getCurrentLine();
      $collection=[];
      Transaction::startBulk();

//      print "<pre>";
      $count = 0;
      $file = [];
      while(!$SplFileObject->eof()) {
        $file[$count] = $SplFileObject->getCurrentLine();
        $SplFileObject->next();
//        print "\n$count";
        $count++;
      }
      while($count > 0) {
        $count--;
        $line = array_map("trim", str_getcsv($file[$count], ",", '"'));
        if (count($line) ==4) {
          $name = trim($line[1], '"');
          $bank_string = BankString::findOrCreate($account_id, $name)->first();
//          print "\nReturned: (id: {$bank_string->id} account_id: {$bank_string->account_id}, name: {$bank_string->name})";

          $transaction = Transaction::create([
              "date"        => Carbon::createFromFormat("d/m/Y",$line[0] )
            , "amount"      =>  $line[2] * 100
            , "account_id"  =>  $account_id
            , "reconciled"  =>  false
            , "payee_id"    => $bank_string->payee_id     ? $bank_string->payee_id    : null
            , "category_id" => $bank_string->category_id  ? $bank_string->category_id : null
            , "notes"       => "imported from bank statement"
          ]);

          $transaction->bank_string_id  =  $bank_string->id;
          $transaction->bank_balance    =  $line[3] * 100;
          $transaction->save();

        }
      }
      Transaction::finishBulk(true);
    } catch(Exception $ex) {

      dd($ex->getMessage());

      Transaction::finishBulk(false);
      $messageBag = new MessageBag();
      $messageBag->add("badFormat", "Unable to process uploaded csv file");
      $messageBag->add($ex->getCode(), $ex->getMessage());
//      print $ex->getMessage();
      return Respond::WithErrors($messageBag);
    }
//    print "</pre>";

  }


	/**
	 * Remove the specified transaction from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)	{
    try {
      if (!Transaction::destroy($id)) {
        return Respond::NotFound();
      }
    } catch (QueryException $e) {
      return Respond::QueryException($e);
    } catch (Exception $e) {
      return Respond::InternalError($e->getMessage());
    }
    return Respond::Success();
	}

}
