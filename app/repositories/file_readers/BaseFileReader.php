<?php namespace Feenance\repositories\file_readers;

use \SplFileObject;
use Feenance\repositories\file_readers\FirstDirectCSVReader;
use Feenance\repositories\file_readers\TescoCSVReader;

class BaseFileReader implements FileReaderInterface {

    /** @return array */    function getExpectedHeader()        { return []; }
    /** @return int */      function getExpectedFieldCount()    { return 0;  }
    /** @var SplFileObject */    protected $file;

    protected $header;

    private function open($filePath)
    {
        $this->file = new SplFileObject($filePath, "r");
        $this->file->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
    }

    protected function __construct($parent = null)
    {
        if (!empty($parent)) {
            $this->file = $parent->file;
        }
    }

    static public function getReaderForFile($filePath)
    {
        $reader = new BaseFileReader();
        $reader->open($filePath);
        if ($reader->file->isReadable()) {
            if ( $instance = (new FirstDirectCSVReader($reader))->readHeader() ) return $instance;
            if ( $instance = (new TescoCSVReader($reader))->readHeader() ) return $instance;
        }
        return null;
    }


    /**
     * returns true if the header can be read an it is a valid header for the Reader.
     * @return bool
     */
    protected function readHeader()
    {
        $this->header = $this->file->current();
        if (count($this->header) != $this->getExpectedFieldCount()) {
            return null;
        }
        $diff = array_udiff($this->getExpectedHeader(), $this->header, "self::strcmp_with_trim");
        if (empty($diff)) {
            $this->next();
            return $this;
        }
        return null;
    }

    protected function strcmp_with_trim($str1, $str2)
    {
        return strcmp(trim($str1), trim($str2));
    }

    public function current() {
        return $this->file->current();
    }

    public function next() {
        $this->file->next();
    }

    public function key() {
        return $this->file->key() -1;
    }

    public function valid() {
        return (false === $this->file->eof());
    }

    public function rewind() {
        $this->file->rewind();
        $this->readHeader();
    }
}