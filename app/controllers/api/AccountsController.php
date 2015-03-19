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
        $this->repository->find($id);
        return $this->respondRaw();
    }

    /**
     * Add a new Account
     *
     * @return Respond
     */
    public function store() {
        $this->repository->create(Input::all());
        return $this->respondRaw();
    }

    /**
     * Update a specific account.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id) {

        $this->repository->updateWithIdAndInput($id, Input::all());
        return $this->respondRaw();
    }

    /**
     * delete a specific account.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id) {
        $this->repository->destroy($id);
        return $this->respondRaw();


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