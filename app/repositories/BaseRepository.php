<?php namespace Feenance\repositories;

use Markfee\Responder\RepositoryResponse;

abstract class BaseRepository extends RepositoryResponse implements RepositoryInterface {

    /** @return int */
    public function startBatch()
    {
        return 0;
    }

    public function finishBatch()
    {
        // TODO: Implement finishBatch() method.
    }
}