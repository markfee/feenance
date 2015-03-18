<?php namespace Feenance\repositories;

use Feenance\Misc\Transformers\TransactionTransformer;

class EloquentTransactionRepository  extends BaseRepository implements RepositoryInterface {

    function __construct(TransactionTransformer $transformer) {
        parent::__construct($transformer);
    }

    public function all($columns = array('*')) {
        // TODO: Implement all() method.
    }

    public function newInstance(array $attributes = array()) {
        // TODO: Implement newInstance() method.
    }

    public function paginate($perPage = 15, $columns = array('*')) {
        // TODO: Implement paginate() method.
    }

    public function create(array $attributes) {
        // TODO: Implement create() method.
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