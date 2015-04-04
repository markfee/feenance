<?php namespace Feenance\repositories\file_readers;


use Feenance\models\Transaction;
use \Carbon\Carbon;

class TescoCSVReader extends BaseFileReader {

    /** @return array */ function getExpectedHeader()
    {
        return [
            "Transaction Date",
            "Posting Date",
            "Billing Amount",
            "Merchant",
            "Merchant City ",
            "Merchant State",
            "Merchant Zip",
            "Reference Number",
            "Debit/Credit Flag",
            "SICMCC Code",
            ];
    }
    /** @return int */    function getExpectedFieldCount()    {        return 10;    }

    public function current()
    {
        $line = array_map("utf8_encode", $this->file->current());
        sscanf($line[2], "£%f", $amount);
        if ($line[8] == "D") {
            $amount *= -1;
        }

        $transaction = new Transaction(
            Carbon::createFromFormat("d/m/Y", $line[0]),
            $amount
        );
        $transaction->setBankString(trim($line[3] . $line[4] . $line[5] . $line[6], '"'));
        return $transaction;
    }
}