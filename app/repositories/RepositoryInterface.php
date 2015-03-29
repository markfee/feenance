<?php namespace Feenance\repositories;

use Markfee\Responder\RepositoryResponseInterface;
use Markfee\Responder\Transformer;
use Markfee\Responder\TransformerInterface;

interface RepositoryInterface extends RepositoryResponseInterface {

    public function all($columns = array('*'));

    public function paginate($perPage = 15, $columns = array('*'));

    public function create(array $input);

    public function find($id, $columns = array('*'));

    public function updateWithIdAndInput($id, array $input);

    public function destroy($id);

}