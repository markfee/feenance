<?php namespace Feenance\Services;

use Markfee\Responder\RepositoryResponse;
use Markfee\Responder\ErrorBagTrait;
use Feenance\models\Transaction;

class StatementImporter  {
    /*** @var RepositoryInterface */
    private $repository;
    use ErrorBagTrait;
    /**
     * @param RepositoryInterface $repository
     */
    function __construct($repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Integer $account_id
     * @param Feenance\repositories\file_readers\FileReaderInterface $reader
     */
    function importTransactionsToAccount($account_id, $reader)
    {
        /*** @var Transaction $transaction **/
        foreach($reader as $transaction) {

            $transaction->setAccountId($account_id);

            /*** @var ErrorBagTrait $response **/
            $response = $this->repository->create($transaction->toArray());
            if ($response->hasErrors()) {
                $this->addErrors($response->getErrors());
            }
        }
    }
}