<?php namespace Feenance\controllers\Api;

use Feenance\repositories\RepositoryInterface;

class RestfulController extends BaseController {

  protected $repository;

  function __construct(RepositoryInterface $repository) {
    $this->repository = $repository;
  }
}