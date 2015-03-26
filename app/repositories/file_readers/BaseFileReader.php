<?php namespace Feenance\repositories\file_readers;

use \SplFileObject;

class BaseFileReader implements FileReaderInterface {

    /** @var SplFileObject */    protected $file;
    protected $header;

    public function open($filePath)
    {
        $this->file = new SplFileObject($filePath, "r");
        $this->file->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
        $this->readHeader();
        return $this->file->isReadable();
    }

    protected function readHeader()
    {
        $this->header = $this->file->getCurrentLine();
        $this->next();
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