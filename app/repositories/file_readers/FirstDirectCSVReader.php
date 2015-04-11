<?php namespace Feenance\repositories\file_readers;


use Feenance\models\Transaction;
use \Carbon\Carbon;

class FirstDirectCSVReader extends ReverseFileReader {
    /** @return array */ function getExpectedHeader()
    {
        return [
            "Date",
            "Description",
            "Amount",
            "Balance",
        ];
    }

    /** @return int */    function getExpectedFieldCount()    {        return 4;    }

    public function current()
    {
        $line = $this->file->current();

        $transaction = new Transaction(
            Carbon::createFromFormat("d/m/Y", $line[0]),
            $line[2]
        );
        $transaction->setBankString(trim($line[1], '"'));
        $transaction->setBankBalance(trim($line[3], '"'));
        return $transaction;
    }

}