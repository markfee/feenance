<?php namespace Feenance\controllers\Api;

use Feenance\repositories\EloquentTransactionRepository;

use Feenance\models\eloquent\BankString;

use Feenance\repositories\file_readers\BaseFileReader;
use Feenance\Services\StatementImporter;
use Markfee\Responder\Respond;
use \Exception;
use \Input;
use \Validator;
use \SplFileObject;
use Illuminate\Support\MessageBag;
use \Carbon\Carbon;


class TransactionsController extends RestfulController {

    protected $paginateCount = 110; // alter with ?perPage=nnn in url

    /* @var EloquentTransactionRepository; */
    protected $repository;

    function __construct(EloquentTransactionRepository $repository) {
        parent::__construct($repository);
    }

    /**
     * Display a listing of transactions
     *
     * @param null $account_id : integer
     * @return \Illuminate\Support\Facades\Response
     */
    public function index($account_id = null) {
        $this->repository->filterAccount($account_id)->paginate($this->paginateCount);
        return $this->respond();
    }

    /**
     * @param null $account_id
     * @return mixed
     */
    public function reconciled($account_id = null) {
        $this->repository->filterAccount($account_id)->filterReconciled(true)->paginate($this->paginateCount);
        return $this->respond();
    }

    /**
     * @param null $account_id
     * @return mixed
     */
    public function unreconciled($account_id = null) {
        $this->repository
            ->filterAccount($account_id)
            ->filterReconciled(false)
            ->paginate($this->paginateCount);
        return $this->respond();
    }

    /**
     * @param null $account_id
     * @return mixed
     */
    public function unreconciledCount($account_id = null) {
        $this->repository
            ->filterAccount($account_id)
            ->filterReconciled(false)
            ->count();
        return $this->respondRaw();
    }

    /**
     * @param null $account_id
     * @return mixed
     */
    public function deleteUnreconciled($account_id) {
        $this->repository->deleteUnreconciled($account_id);
        return $this->respond();
    }

    /**
     * @param null $account_id
     * @return mixed
     */
    public function reconcileAll($account_id) {
        $this->repository->reconcileAll($account_id);
        return $this->respond();

    }

    /**
     * Return list of transactions with a specific bank string
     *
     * @return Response
     */
    public function bank_strings($bank_string_id) {
        $records = Transaction::where("bank_string_id", $bank_string_id)->orderBy('date', "DESC")->orderBy('id', "DESC")->with(TransactionsController::$default_with)->paginate($this->paginateCount);
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

        $payeeId = Input::get("payee_id", 0);
        $categoryId = Input::get("category_id", 0);

        $this->repository->setPayeeAndCategoryForBankString($bank_string_id, $payeeId, $categoryId);

        $queryBuilder = BankString::where("id", "=", $bank_string_id)
            ->whereNull(\DB::Raw("NULLIF(payee_id, $payeeId)"), 'AND')
            ->whereNull(\DB::Raw("NULLIF(category_id, $categoryId)"), 'AND');

        $bankStringUpdateCount = $queryBuilder->update(Input::only(["payee_id", "category_id"]));

        return $this->respond();
    }

    /**
     * Update the specified transaction in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id) {
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
    public function upload()
    {
        try {
            $file = Input::file('file');

            $account_id = Input::get("account_id");

            if (empty($file)) {
                return Respond::ValidationFailed("Invalid file uploaded");
            }

            if (empty($account_id)) {
                return Respond::ValidationFailed("No Account Id Specified");
            }

            if ($reader = BaseFileReader::getReaderForFile($file)) {
                $statementImport = new StatementImporter($this->repository);
                $statementImport->importTransactionsToAccount($account_id, $reader);
            }

        } catch (Exception $ex) {
            $messageBag = new MessageBag();
            $messageBag->add("badFormat", "Unable to process uploaded csv file");
            $messageBag->add($ex->getCode(), $ex->getMessage());

            return Respond::WithErrors($messageBag);

        }
    }
}
