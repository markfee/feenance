<?php namespace Feenance\controllers\Api;

use Feenance\repositories\EloquentAccountRepository;

class AccountsController extends RestfulController {

    /* @var EloquentAccountRepository; */
    protected $repository;

    function __construct(EloquentAccountRepository $repository) {
        parent::__construct($repository);
    }
}