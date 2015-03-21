<?php namespace Feenance\repositories;

use Feenance\Misc\Transformers\TransactionTransformer;
use Feenance\models\eloquent\Transaction;
use Feenance\models\eloquent\Transfer;
use \DB;

class EloquentTransactionRepository  extends BaseRepository implements RepositoryInterface {

    protected static $default_with = ["balance", "source.sourceAccount", "destination.destinationAccount", "bankString", "payee", "category.parent", "status"];


    function __construct(TransactionTransformer $transformer) {
        parent::__construct($transformer);
    }

    public function all($columns = array('*')) {
        // TODO: Implement all() method.
    }

    public function newInstance(array $attributes = array()) {
        // TODO: Implement newInstance() method.
    }

    public function paginateForAccount($accountId, $perPage = 15, $columns = array('*')) {
        $records = Transaction::where("account_id", $account_id)->orderBy('date', "DESC")->orderBy('id', "DESC")->with(static::$default_with)->paginate($perPage);
        return $this->Paginated($records, $this->transformCollection($records->all()));
    }

    public function paginate($perPage = 15, $columns = array('*')) {
        $records = Transaction::orderBy('date', "DESC")->orderBy('id', "DESC")->with(static::$default_with)->paginate($perPage);
        return $this->Paginated($records, $this->transformCollection($records->all()));
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


    public function find($id, $columns = array('*')) {
        // TODO: Implement find() method.
    }

    public function updateWithIdAndInput($id, array $input) {
        // TODO: Implement updateWithIdAndInput() method.
    }

    public function destroy($id) {
        // TODO: Implement destroy() method.
    }

}