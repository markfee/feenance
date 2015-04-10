<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 10/04/15
 * Time: 06:46
 */

namespace Feenance\models;


trait BatchTrait {
    /** @var integer */ private $batchId = null;

    /** @return int */
    public function startBatch()
    {
        return $this->getBatchId();
    }

    public function finishBatch()
    {
        $this->batchId = null;
    }

    /** @return int */
    public function getBatchId()
    {
        return $this->batchId;
    }

    /** @param $batchId int */
    public function setBatchId($batchId)
    {
        $this->batchId = $batchId;
    }
}