<?php namespace Feenance\controllers\Api;

//use Feenance\models\eloquent\Account;
use Feenance\repositories\EloquentAccountRepository;
use Markfee\Responder\Respond;
use Feenance\Misc\Transformers\AccountsTransformer;
use Illuminate\Database\QueryException;
use \Exception;
use \Input;
use \Validator;

class AccountsController extends RestfulController {

    /* @var EloquentAccountRepository; */
    protected $repository;

    function __construct(EloquentAccountRepository $repository) {
        parent::__construct($repository);
    }

    public function index() {
        $this->repository->paginate();
        return $this->respond();
    }

    public function show($id) {
        if ($this->repository->find($id)->isFound()) {
            return $this->respondRaw();
        }
        return $this->respond();
    }

    /**
     * Add a new Account
     *
     * @return Respond
     */
    public function store() {
        if ($this->repository->create(Input::all())->isCreated()) {
            return $this->respondRaw();
        }
        return $this->respond();
    }

    /**
     * Update a specific account.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id) {
        $account = Account::findOrFail($id);
        $validator = Validator::make($data = $this->transformInput(Input::all()), Account::$rules);
        if ($validator->fails()) {
            return Respond::ValidationFailed();
        }

        $account->update($data);
        return Respond::Raw($this->transform($account));
    }

    /**
     * delete a specific account.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id) {
        try {
            if (!Account::destroy($id)) {
                return Respond::NotFound();
            }
        } catch (QueryException $e) {
            return Respond::QueryException($e);
        } catch (Exception $e) {
            return Respond::InternalError($e->getMessage());
        }
        return Respond::Success();
    }
}