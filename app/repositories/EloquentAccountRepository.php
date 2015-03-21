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

  public function create(array $input) {
      if (! $this->Validate($input, Account::$rules)->isValid()) {
          return $this->ValidationFailed();
      }
      return $this->Created(Account::create($this->getData()));
  }

  public function find($id, $columns = array('*')) {
      try {
          $record = Account::findOrFail($id);
          return $this->Found($record);
      } catch (ModelNotFoundException $e) {
          return $this->NotFound($e->getMessage());
      }
  }

  public function updateWithIdAndInput($id, array $input) {
      try {
          $account = Account::findOrFail($id);
      } catch (ModelNotFoundException $e) {
          return $this->NotFound($e->getMessage());
      }

      if (! $this->Validate($input, Account::$rules)->isValid()) {
          return $this->ValidationFailed();
      }

      $account->update($this->getData());
      return $this->Updated($account);
  }

  public function destroy($id) {
      try {
          if (!Account::destroy($id)) {
              return $this->NotFound();
          }
      } catch (QueryException $e) {
          return $this->QueryException($e);
      } catch (Exception $e) {
          return $this->InternalError($e->getMessage());
      }
      return $this->Deleted();
  }

}