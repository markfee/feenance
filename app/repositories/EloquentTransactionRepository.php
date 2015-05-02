<?php namespace Feenance\repositories;

use Feenance\Misc\Transformers\TransactionTransformer;
use Feenance\models\eloquent\Transaction as EloquentTransaction;
use Feenance\models\Transaction;
use Feenance\models\eloquent\Transfer;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Feenance\models\eloquent\TransactionStatus;
use \Exception;
use \DB;

class EloquentTransactionRepository extends BaseRepository implements RepositoryInterface
{

    protected static $default_with = ["balance", "source.sourceAccount", "destination.destinationAccount", "bankString", "payee", "category.parent", "status"];

    private $accountId_filter = null;
    private $batchId_filter = null;
    private $reconciled_filter = null;
    private $bank_string_id_filter = null;
    private $bankStringRepository = null;

    function __construct(TransactionTransformer $transformer, EloquentBankStringRepository $bankStringRepository)
    {
        parent::__construct($transformer);
        $this->bankStringRepository = $bankStringRepository;
    }

    public function all($columns = array('*'))
    {
        // TODO: Implement all() method.
    }

    public function filterAccount($accountId)
    {
        $this->accountId_filter = $accountId;
        return $this;
    }

    public function filterBatch($batchId)
    {
        $this->batchId_filter = $batchId;
        return $this;
    }


    public function filterBankString($bank_string_id)
    {
        $this->bank_string_id_filter = $bank_string_id;
        return $this;
    }


    public function filterReconciled($val)
    {
        $this->reconciled_filter = $val;
        return $this;
    }

    private function Transactions()
    {
        $query = EloquentTransaction::
        orderBy('date', "DESC")
            ->orderBy('id', "DESC")
            ->with(static::$default_with);
        if (!empty($this->accountId_filter)) {
            $query = $query->where("account_id", $this->accountId_filter);
        }
        if (!is_null($this->reconciled_filter)) {
            $query = $query->where("reconciled", $this->reconciled_filter);
        }
        if (!empty($this->bank_string_id_filter)) {
            $query = $query->where("bank_string_id", $this->bank_string_id_filter);
        }
        if (!empty($this->batchId_filter)) {
            $query = $query->where("batch_id", $this->batchId_filter);
        }
        return $query;
    }

    public function paginateForAccount($account_id, $perPage = 15, $columns = array('*'))
    {
        return $this->Paginated(
            $this->Transactions()
                ->paginate($perPage)
        );
    }

    public function paginate($perPage = 15, $columns = array('*'))
    {
        return $this->Paginated(
            $this->Transactions()
                ->paginate($perPage)
        );
    }

    public function count()
    {
        return $this->setCount($this->Transactions()->count());
    }

    public function create(array $input)
    {
        /*      $transfer_id = (empty($input["transfer_id"]) ? null : $input["transfer_id"]); */
        if (!$this->Validate($input, EloquentTransaction::$rules)->isValid()) {
            return $this;
        }
        try {
            /*** @var Transaction $transaction **/
            $transaction = new Transaction($this->getData());
            $transaction->setBatchId($this->getBatchId());

            if ($transaction->hasBankString()) {
                $bankString = $this->bankStringRepository->find_or_create($transaction);

                $transaction->setBankStringId($bankString->getBankStringId());

                if (!$transaction->isCategorised()) {
                    // infer payee_id and category_id if they are missing - using the bank string
                    $transaction->setCategoryId($bankString->getCategoryId());
                    $transaction->setPayeeId($bankString->getPayeeId());
                }
            }

            // TODO inform Standing Orders that a standing order may have been payed

            if ( $transaction->isTransfer() ) {
                return $this->createTransfer($transaction);
            }
            // TODO try to infer a matched payment in another account and mark both as a transfer if found
            return $this->Created(EloquentTransaction::create($transaction->toStorageArray()));

        } catch (\Exception $ex) {
            return $this->InternalError($ex->getMessage());
        }
    }

    public function setPayeeAndCategoryForBankString($bank_string_id, $payeeId, $categoryId)
    {
        $queryBuilder = EloquentTransaction::where("bank_string_id", "=", $bank_string_id)
            ->whereNull(DB::Raw("NULLIF(payee_id, $payeeId)"), 'AND')
            ->whereNull(DB::Raw("NULLIF(category_id, $categoryId)"), 'AND');

        $transactionUpdateCount = $queryBuilder->update(["payee_id" => $payeeId, "category_id" => $categoryId]);
        return $this->BulkUpdated($transactionUpdateCount);
    }

    /**
     * @param Transaction $transaction
     * @return \Markfee\Responder\RepositoryResponse
     */
    private function createTransfer($transaction)
    {
        try {
            DB::beginTransaction();
            {
                $source         = EloquentTransaction::create($transaction->toStorageArray());
                $destination    = EloquentTransaction::create($transaction->getTransfer()->toStorageArray());
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
        } catch(Exception $ex) {
            dd($ex);
        }
    }


    public function find($id, $columns = array('*'))
    {
        try {
            return $this->Found(EloquentTransaction::findOrFail($id));
        } catch (ModelNotFoundException $e) {
            return $this->NotFound($e->getMessage());
        }
    }

    public function updateWithIdAndInput($id, array $input)
    {
        try {
            /** @var Transaction $transaction */
            $transaction = EloquentTransaction::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return $this->NotFound($e->getMessage());
        }

        if ($this->Validate($input, EloquentTransaction::$rules) ) {
            $transaction->update($this->getData());
            return $this->Updated($transaction);
        }
        return $this;
    }

    public function reconcileAll($account_id)
    {
        EloquentTransaction::startBulk();
        $transactionUpdateCount = EloquentTransaction::where("account_id", $account_id)->update(["reconciled" => true, "status_id" => TransactionStatus::RECONCILED]);
        EloquentTransaction::finishBulk(true);
        return $this->BulkUpdated($transactionUpdateCount);
    }

    public function deleteUnreconciled($account_id)
    {
        EloquentTransaction::startBulk();
        $query = EloquentTransaction::where("reconciled", false)->where("account_id", $account_id)->delete();
        EloquentTransaction::finishBulk(true);
        return $this->Deleted();
    }

    public function destroy($id)
    {
        try {
            if (!EloquentTransaction::destroy($id)) {
                return $this->NotFound();
            }
        } catch (QueryException $e) {
            return $this->QueryException($e);
        } catch (Exception $e) {
            return $this->InternalError($e->getMessage());
        }
        return $this->Deleted();
    }

    /** @return int */
    public function startBatch()
    {
        DB::beginTransaction();
        EloquentTransaction::startBulk();
        $results = DB::select( DB::raw("SELECT COALESCE(MAX(batch_id),0)+1 batch_id FROM transactions") );
        $this->setBatchId($results[0]->batch_id);
        return $this->getBatchId();
    }

    public function finishBatch()
    {
        EloquentTransaction::finishBulk(true);
        DB::commit();
        parent::finishBatch();
    }
}