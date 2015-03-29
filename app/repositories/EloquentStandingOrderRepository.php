<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 29/03/15
 * Time: 07:37
 */

namespace Feenance\repositories;


use Feenance\Misc\Transformers\StandingOrderTransformer;

class EloquentStandingOrderRepository extends BaseRepository {

    function __construct(StandingOrderTransformer $transformer)
    {
        parent::__construct($transformer);
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