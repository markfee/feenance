<?php namespace Feenance\controllers\Api;

use Feenance\repositories\RepositoryInterface;
use Markfee\Responder\TransformerInterface;

class RestfulController extends BaseController {

    protected $repository;

    function __construct(RepositoryInterface $repository) {
        $this->repository = $repository;
        $this->transformer = $this->repository->getTransformer();
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