<?php namespace Feenance\repositories;

use Feenance\models\eloquent\Account;
use Feenance\Misc\Transformers\AccountsTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use \Validator;

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
      $validator = Validator::make($attributes, Account::$rules);

      if ($validator->fails()) {
          return $this->ValidationFailed();
      }
      $account = Account::create($attributes);
      return $this->Created($this->transform($account));
  }

  public function find($id, $columns = array('*')) {
      try {
          $record = Account::findOrFail($id);
          return $this->Found($this->transform($record));
      } catch (ModelNotFoundException $e) {
          return $this->NotFound($e->getMessage());
      }
  }

  public function updateWithIdAndInput($id, array $input) {
      try {
          $account = Account::findOrFail($id);
          $validator = Validator::make($data = $this->transformInput($input), Account::$rules);
          if ($validator->fails()) {
              return $this->ValidationFailed();
          }

          $account->update($data);
          return $this->Updated($this->transform($account));
      } catch (ModelNotFoundException $e) {
          return $this->NotFound($e->getMessage());
      }
  }

  public function destroy($id) {
    // TODO: Implement destroy() method.
  }

}