<?php namespace Feenance\models;

interface BatchInterface
{
    /** @return int */
    public function startBatch();

    public function finishBatch();

    /** @return int */
    public function getBatchId();

    /** @param $batchId int */
    public function setBatchId($batchId);
}