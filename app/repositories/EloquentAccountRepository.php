<?php namespace Feenance\repositories;

use Feenance\models\eloquent\Account;
use Feenance\Misc\Transformers\AccountsTransformer;


class EloquentAccountRepository extends BaseRepository implements RepositoryInterface {

  function __construct(AccountsTransformer $transformer) {
    parent::__construct($transformer);
  }

  public function all($columns = array('*'))
  {
    return $this->transform(Account::all());
  }

  public function paginate($perPage = 15, $columns = array('*')) {
      $records = Account::orderBy("open", "DESC")->paginate();
      return $this->Paginated($records);
  }

  public function newInstance(array $attributes = array()) {
    // TODO: Implement newInstance() method.
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