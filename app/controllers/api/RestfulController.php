<?php namespace Feenance\controllers\Api;

use Feenance\repositories\RepositoryInterface;
use Markfee\Responder\TransformerInterface;

class RestfulController extends BaseController {

    protected $repository;

    function __construct(RepositoryInterface $repository) {
        $this->repository = $repository;
        $this->transformer = $this->repository->getTransformer();
    }

    /**
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    function respond($headers = [])
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
    function respondRaw($headers = []) {
        if ($this->repository->hasErred()) {
            return $this->respond($headers);
        }
        return \Response::json($this->repository->getData(),  $this->repository->getStatusCode(), $headers);
    }

}