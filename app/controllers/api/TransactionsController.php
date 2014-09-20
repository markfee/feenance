<?php
namespace api;

use Misc\Transformers\BankTransactionTransformer;
use \Transaction;
use Markfee\Responder\Respond;
use Misc\Transformers\TransactionTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use \Exception;
use \Input;
use \Validator;
use Symfony\Component\HttpFoundation\Response as ResponseCodes;
use \DB;
use \SplFileObject;
use Illuminate\Support\MessageBag;
use \BankString;
use \Carbon\Carbon;
use \BankTransaction;
class TransactionsController extends BaseController {

  /**
   * @return Transformer
  */
  protected function getTransformer() {
    return $this->transformer ?: new TransactionTransformer;
  }

  /**
   * Display a listing of transactions
   *
   * @return Response
   */
  public function index($account_id = null)
  {
    $with = ["balance", "source", "destination", "bankString"];
    if (!empty($account_id)) {
      $records = Transaction::where("account_id", $account_id)->orderBy('date', "DESC")->orderBy('id', "DESC")->with($with)->paginate(100);
    } else {
      $records = Transaction::orderBy('date', "DESC")->orderBy('id', "DESC")->with($with)->paginate(100);
    }
    return Respond::Paginated($records, $this->transformCollection($records->all()));
  }

  /**
   * Display a listing of transactions
   *
   * @return Response
   */
  public function bank_strings($bank_string_id)
  {
//    $with = ["balance", "source", "destination", "bankTransaction.bank_string"];
//    $records = Transaction::where("bankTransaction.bank_string.id", $bank_string_id)->orderBy('date', "DESC")->orderBy('id', "DESC")->with($with)->paginate(100);
    $records = BankTransaction::where("bank_string_id", "=", $bank_string_id)->paginate(100);
//    dd($records);
    $transformer = new BankTransactionTransformer();
    return Respond::Paginated($records, $transformer->transformCollection($records->all()));
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
    $transaction = Transaction::create($data);
    Respond::setStatusCode(ResponseCodes::HTTP_CREATED);
    return Respond::Raw($this->transform($transaction));
	}

  private function createTransfer($data, $transfer_id) {
    $transfer = $data;
    $transfer["account_id"] = $transfer_id;
    $data["amount"] *= -1;
    DB::beginTransaction();{

      $source       = Transaction::create($data);
      $destination  = Transaction::create($transfer);
      $transfer = new \Transfer();
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
  public function uploadFile($account_id, $SplFileObject) {
    try {
      $header = $SplFileObject->getCurrentLine();
      $collection=[];
      Transaction::startImport();

      print "<pre>";
      // TODO -- READ FILE INTO ARRAY, REVERSE SORT, ARRAY_MAP_IMPORT
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
        $line = array_map("trim", explode(",", $file[$count]));
        if (count($line) ==4) {
          $name = trim($line[1], '"');
          $bank_string = BankString::findOrCreate($account_id, $name)->first();
          print "\nReturned: (id: {$bank_string->id} account_id: {$bank_string->account_id}, name: {$bank_string->name})";

          $transaction = Transaction::create([
              "date"        => Carbon::createFromFormat("d/m/Y",$line[0] )
            , "amount"      =>  $line[2] * 100
            , "account_id"  =>  $account_id
            , "reconciled"  =>  false
//            , "payee_id"    => $bank_string->map ? $bank_string->map->payee_id : null
//            , "category_id" => $bank_string->map ? $bank_string->map->category_id : null
            , "notes"       => "imported from bank statement"
          ]);

          $transaction->bank_string_id  =  $bank_string->id;
          $transaction->bank_balance    =  $line[3] * 100;
          $transaction->save();

        }
      }
      Transaction::finishImport(true);
    } catch(Exception $ex) {

      dd($ex->getMessage());

      Transaction::finishImport(false);
      $messageBag = new MessageBag();
      $messageBag->add("badFormat", "Unable to process uploaded csv file");
      $messageBag->add($ex->getCode(), $ex->getMessage());
      print $ex->getMessage();
      return Respond::WithErrors($messageBag);
    }
    print "</pre>";

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
