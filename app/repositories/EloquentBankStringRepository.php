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
        return $this->toBankString(EloquentBankString::findOrCreate($transaction->getAccountId(), $transaction->getBankString()));
    }

    private function toBankString(EloquentBankString $eloquentBankString) {
        $bankString = new BankString();
        $bankString->setBankString($eloquentBankString->name);
        $bankString->setCategoryId($eloquentBankString->category_id);
        $bankString->setPayeeId($eloquentBankString->payee_id);
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