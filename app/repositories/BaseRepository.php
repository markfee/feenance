<?php namespace Feenance\repositories;

use Feenance\models\BatchInterface;
use Feenance\models\BatchTrait;
use Markfee\Responder\RepositoryResponse;

abstract class BaseRepository extends RepositoryResponse implements RepositoryInterface, BatchInterface
{
    use BatchTrait;
}