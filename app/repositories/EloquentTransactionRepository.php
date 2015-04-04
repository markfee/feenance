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

    private $accountId = null;
    private $reconciled = null;
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
        $this->accountId = $accountId;
        return $this;
    }

    public function filterReconciled($val)
    {
        $this->reconciled = $val;
        return $this;
    }

    private function Transactions()
    {
        $query = EloquentTransaction::
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

            if (! $transaction->isCategorised() ) {
                // DONE infer payee_id and category_id if they are missing - mainly using the bank string
                $bankString = $this->bankStringRepository->find_or_create($transaction);
                $transaction->setCategoryId($bankString->getCategoryId());
                $transaction->setPayeeId($bankString->getPayeeId());
            }

            // TODO inform Standing Orders that a standing order may have been payed

            if ( $transaction->isTransfer() ) {
                return $this->createTransfer($transaction);
            }
            // TODO try to infer a matched payment in another account and mark both as a transfer if found

            return $this->Created(EloquentTransaction::create($transaction->toArray()));

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
                $source         = EloquentTransaction::create($transaction->toArray());
                $destination    = EloquentTransaction::create($transaction->getTransfer()->toArray());
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
        // TODO: Implement updateWithIdAndInput() method.
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

}