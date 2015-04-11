<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 11/04/15
 * Time: 15:25
 */

namespace Feenance\repositories\file_readers;


class ReverseFileReader extends BaseFileReader {
    private $recordCount  = 0;
    private $headerLength = 1;
    private $currentRecord  = 0;

    protected function advanceFromHeader() {
        $this->file->next();
        $this->recordCount = 0;

        while(!$this->file->eof()) {
            $this->recordCount++;
            $this->file->next();
        }
        $this->currentRecord = $this->recordCount ;
        $this->file->seek($this->currentRecord);
    }

    public function next() {
        $this->currentRecord--;
        $this->file->seek($this->currentRecord);
    }

    public function valid() {
        return ($this->currentRecord > 0);
    }


}