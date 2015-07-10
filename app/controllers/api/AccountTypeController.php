<?php namespace Feenance\controllers\Api;

use Feenance\repositories\EloquentAccountTypeRepository;

class AccountTypeController extends RestfulController {
    /* @var EloquentAccountTypeRepository; */
    protected $repository;

    function __construct(EloquentAccountTypeRepository $repository) {
        parent::__construct($repository);
    }
}