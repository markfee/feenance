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
        $bankString = $this->toBankString($results);
        if (!$bankString->isCategorised() && $transaction->isCategorised() ) {
            $bankString->setCategoryId($transaction->getCategoryId());
            $bankString->setPayeeId($transaction->getPayeeId());
            try {
                $this->updateWithIdAndInput(
                    $bankString->getBankStringId(),
                    array_merge(
                        $bankString->toBankStringArray(),
                        $bankString->toCategorisableArray()
                    )
                );
            } catch(Exception $e) {
                dd($e);
            }
        }
        return $bankString;
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
        try {
            $bankString = EloquentBankString::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return $this->NotFound($e->getMessage());
        }

        if ($this->Validate($input, EloquentBankString::$rules) ) {
            $bankString->update($this->getData());
            return $this->Updated($bankString);
        }
    }

    public function destroy($id)
    {
        // TODO: Implement destroy() method.
    }
}