<?php namespace Feenance\repositories;

use Feenance\models\eloquent\Account as EloquentAccount;
use Feenance\Misc\Transformers\AccountsTransformer;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use \Validator;
use \Exception;

class EloquentAccountRepository extends BaseRepository implements RepositoryInterface {

  function __construct(AccountsTransformer $transformer) {
    parent::__construct($transformer);
  }

  public function all($columns = array('*'))
  {
    return $this->Found(EloquentAccount::all());
  }

  public function paginate($perPage = 15, $columns = array('*')) {
      $records = EloquentAccount::orderBy("open", "DESC")->paginate();
      return $this->Paginated($records);
  }

  public function create(array $input) {
      if ($this->Validate($input, EloquentAccount::$rules)->isValid()) {
          return $this->Created(EloquentAccount::create($this->getData()));
      }
      return $this;
  }

  public function find($id, $columns = array('*')) {
      try {
          return $this->Found(EloquentAccount::findOrFail($id));
      } catch (ModelNotFoundException $e) {
          return $this->NotFound($e->getMessage());
      }
  }

  public function updateWithIdAndInput($id, array $input) {
      try {
          /** @var EloquentAccount $account */
          $account = EloquentAccount::findOrFail($id);
      } catch (ModelNotFoundException $e) {
          return $this->NotFound($e->getMessage());
      }

      if ($this->Validate($input, EloquentAccount::$rules)->isValid()) {
          $account->update($this->getData());
          return $this->Updated($account);
      }
      return $this;
  }

  public function destroy($id) {
      try {
          if (!EloquentAccount::destroy($id)) {
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