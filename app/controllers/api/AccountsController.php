<?php namespace Feenance\controllers\Api;

use Feenance\repositories\EloquentAccountRepository;
use DB;
use \Exception;

class AccountsController extends RestfulController {

    /* @var EloquentAccountRepository; */
    protected $repository;

    function __construct(EloquentAccountRepository $repository) {
        parent::__construct($repository);
    }

    public function refreshBalance($accountId) {
        $this->repository->refresh_balances_for_account($accountId);
        return $this->respondRaw();
    }
}