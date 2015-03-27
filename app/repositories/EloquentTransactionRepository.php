<?php namespace Feenance\repositories;

use Feenance\Misc\Transformers\TransactionTransformer;
use Feenance\models\eloquent\Transaction;
use Feenance\models\eloquent\Transfer;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Feenance\models\eloquent\TransactionStatus;
use \Exception;
use \DB;

class EloquentTransactionRepository extends BaseRepository implements RepositoryInterface {

    protected static $default_with = ["balance", "source.sourceAccount", "destination.destinationAccount", "bankString", "payee", "category.parent", "status"];

    private $accountId = null;
    private $reconciled = null;

    function __construct(TransactionTransformer $transformer) {
        parent::__construct($transformer);
    }

    public function all($columns = array('*')) {
        // TODO: Implement all() method.
    }

    public function newInstance(array $attributes = array()) {
        // TODO: Implement newInstance() method.
    }

    public function filterAccount($accountId)   {   $this->accountId    = $accountId;   return $this; }
    public function filterReconciled($val)      {   $this->reconciled   = $val;         return $this; }

    private function Transactions()
    {
        $query = Transaction::
              orderBy('date', "DESC")
            ->orderBy('id', "DESC")
            ->with(static::$default_with);
        if (!empty($this->accountId)) {
            $query = $query->where("account_id", $this->accountId);
        }
        if (!is_null($this->reconciled)) {
            $query = $query->where("reconciled", $this->reconciled);
        }
        return $query;
    }

    public function paginateForAccount($account_id, $perPage = 15, $columns = array('*')) {
        return $this->Paginated(
            $this->Transactions()

                ->paginate($perPage)
        );
    }

    public function paginate($perPage = 15, $columns = array('*')) {
        return $this->Paginated(
            $this->Transactions()
                ->paginate($perPage)
        );
    }

    public function count()
    {
        return $this->setCount($this->Transactions()->count());
    }

    public function create(array $input) {
        $transfer_id = (empty($input["transfer_id"]) ? null : $input["transfer_id"]);

        if ( ! $this->Validate($input, Transaction::$rules)->isValid()) {
            return $this;
        }

        if (!empty($transfer_id)) {
            return $this->createTransfer($this->getData(), $transfer_id);
        }
        try {
            return $this->Created(Transaction::create($this->getData()));
        } catch (\Exception $ex) {
            $messageBag = new MessageBag();
            $messageBag->add($ex->getCode(), $ex->getMessage());
            return $this->WithErrors($messageBag);
        }
    }

    public function setPayeeAndCategoryForBankString($bank_string_id, $payeeId, $categoryId) {

        $queryBuilder = Transaction::where("bank_string_id", "=", $bank_string_id)
            ->whereNull(DB::Raw("NULLIF(payee_id, $payeeId)"), 'AND')
            ->whereNull(DB::Raw("NULLIF(category_id, $categoryId)"), 'AND');

        $transactionUpdateCount = $queryBuilder->update(["payee_id"=>$payeeId, "category_id"=>$categoryId]);
        return $this->BulkUpdated($transactionUpdateCount);
    }

    private function createTransfer($data, $transfer_id) {
        $transfer = $data;
        $transfer["account_id"] = $transfer_id;
        $data["amount"] *= -1;
        DB::beginTransaction();
        {

            $source = Transaction::create($data);
            $destination = Transaction::create($transfer);
            $transfer = new Transfer();
            $transfer->source = $source->id;
            $transfer->destination = $destination->id;
            $transfer->save();
        }
        DB::commit();
        $collection = [];
        $collection[] = $source->first();
        $collection[] = $destination->first();

        return $this->CreatedMultiple($collection);
    }


    public function find($id, $columns = array('*'))
    {
        try {
            return $this->Found(Transaction::findOrFail($id));
        } catch (ModelNotFoundException $e) {
            return $this->NotFound($e->getMessage());
        }
    }

    public function updateWithIdAndInput($id, array $input) {
        // TODO: Implement updateWithIdAndInput() method.
    }

    public function reconcileAll($account_id) {
        Transaction::startBulk();
        $transactionUpdateCount = Transaction::where("account_id", $account_id)->update(["reconciled" => true, "status_id" => TransactionStatus::RECONCILED]);
        Transaction::finishBulk(true);
        return $this->BulkUpdated($transactionUpdateCount);
    }

    public function deleteUnreconciled($account_id) {
        Transaction::startBulk();
        $query = Transaction::where("reconciled", false)->where("account_id", $account_id)->delete();
        Transaction::finishBulk(true);
        return $this->Deleted();
    }

    public function destroy($id) {
        try {
            if (!Transaction::destroy($id)) {
                return $this->NotFound();
            }
        } catch (QueryException $e) {
            return $this->QueryException($e);
        } catch (Exception $e) {
            return $this->InternalError($e->getMessage());
        }
        return $this->Deleted();
    }

}