<?php namespace Feenance\repositories;

use Feenance\Misc\Transformers\BankStringTransformer;
use Feenance\models\BankString;
use Feenance\models\eloquent\BankString as EloquentBankString;
use Feenance\models\BankTransactionInterface;
use Feenance\models\CategorisableInterface;

class EloquentBankStringRepository extends BaseRepository {

    function __construct(BankStringTransformer $transformer)
    {
        parent::__construct($transformer);
    }

    /**
     * @param BankTransactionInterface $transaction
     * @return CategorisableInterface $this
     */
    public function find_or_create(BankTransactionInterface $transaction)
    {
        if ($transaction->hasBankStringId()) {
            $results = EloquentBankString::findOrFail($transaction->getBankStringId())->firstOrFail();
        } else {
            $results = EloquentBankString::findOrCreate($transaction->getAccountId(), $transaction->getBankString())->firstOrFail();
        }
        return $this->toBankString($results);
    }

    private function toBankString(EloquentBankString $eloquentBankString) {
        $bankString = new BankString();
        // TODO Move this to the BankString Object and implement a to and from array
        $bankString->setBankStringId($eloquentBankString->id);
        $bankString->setBankString($eloquentBankString->name);
        $bankString->setCategoryId($eloquentBankString->category_id);
        $bankString->setPayeeId($eloquentBankString->payee_id);
        return $bankString;
    }

    public function all($columns = array('*'))
    {
        // TODO: Implement all() method.
    }

    public function paginate($perPage = 15, $columns = array('*'))
    {
        // TODO: Implement paginate() method.
    }

    public function create(array $input)
    {
        // TODO: Implement create() method.
    }

    public function find($id, $columns = array('*'))
    {
        // TODO: Implement find() method.
    }

    public function updateWithIdAndInput($id, array $input)
    {
        // TODO: Implement updateWithIdAndInput() method.
    }

    public function destroy($id)
    {
        // TODO: Implement destroy() method.
    }
}