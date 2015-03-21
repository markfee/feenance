<?php namespace Feenance\controllers\Api;

use Feenance\repositories\RepositoryInterface;
use Markfee\Responder\TransformerInterface;
use \Input;

class RestfulController extends BaseController {

    protected $repository;

    function __construct(RepositoryInterface $repository) {
        $this->repository = $repository;
        $this->transformer = $this->repository->getTransformer();
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
     * Add a new Record
     * @return Respond
     */
    public function store() {
        $this->repository->create(Input::all());
        return $this->respondRaw();
    }

    /**
     * Update a specific account.
     * @param  int $id
     * @return Response
     */
    public function update($id) {

        $this->repository->updateWithIdAndInput($id, Input::all());
        return $this->respondRaw();
    }

    /**
     * delete a specific account.
     * @param  int $id
     * @return Response
     */
    public function destroy($id) {
        $this->repository->destroy($id);
        return $this->respondRaw();
    }

    /**
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respond($headers = [])
    {
        $response = \Response::json([
             "data"         => $this->repository->getData()
            , "errors"      => $this->repository->getErrors()
            , "messages"    => $this->repository->getMessages()
            , "status_code" => $this->repository->getStatusCode()
            , "paginator"   => $this->repository->getPaginator()
        ],  $this->repository->getStatusCode(), $headers);
        return $response;
    }

    /**
     * respond with raw json, usually when a single record is to be returned
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondRaw($headers = []) {
        if ($this->repository->hasErred() || $this->repository->isMultiple()) {
            return $this->respond($headers);
        }
        return \Response::json($this->repository->getData(),  $this->repository->getStatusCode(), $headers);
    }

}