<?php namespace Feenance\repositories\file_readers;


use Feenance\models\Transaction;
use \Carbon\Carbon;

class FirstDirectCSVReader extends BaseFileReader {

    public function current()
    {
        $line = $this->file->current();

        $transaction = new Transaction(
            Carbon::createFromFormat("d/m/Y", $line[0]),
            $line[2]
        );
        $transaction->setBankString(trim($line[1], '"'));
        $transaction->setBalance(trim($line[3], '"'));
        return $transaction;
    }

}