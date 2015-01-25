<?php namespace Feenance\Repositories;
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 24/01/15
 * Time: 09:10
 */

interface RepositoryInterface {

  public function all($columns = array('*'));

  public function newInstance(array $attributes = array());

  public function paginate($perPage = 15, $columns = array('*'));

  public function create(array $attributes);

  public function find($id, $columns = array('*'));

  public function updateWithIdAndInput($id, array $input);

  public function destroy($id);
}