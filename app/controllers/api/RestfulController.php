<?php namespace Feenance\controllers\Api;

use Feenance\repositories\RepositoryInterface;

class RestfulController extends BaseController {

    protected $repository;

    function __construct(RepositoryInterface $repository) {
        $this->repository = $repository;
    }

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

}