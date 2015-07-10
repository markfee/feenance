<?php namespace Feenance\repositories;

use Feenance\misc\transformer\AccountTypeTransformer;
use Feenance\models\eloquent\AccountType as EloquentAccountType;

class EloquentAccountTypeRepository extends BaseRepository {

    function __construct(AccountTypeTransformer $transformer)
    {
        parent::__construct($transformer);
    }

    public function all($columns = array('*'))
    {
        return $this->Found(EloquentAccountType::all());
    }

    public function paginate($perPage = 15, $columns = array('*'))
    {
        return $this->all($columns); // TODO: Implement paginate() method.

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