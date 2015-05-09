<?php namespace Feenance\models;
use Markfee\MyCarbon;
use Feenance\models\StandingOrder;

class StandingOrderIterator implements \Iterator {
    /** @var  StandingOrder */
    private $standingOrder;
    private $finish_date = null;

    function __construct($standingOrder, $modifySource = false)
    {
        $this->standingOrder = ($modifySource) ? $standingOrder: clone($standingOrder);
        $this->finish_date = $this->standingOrder->getFinishDate();
//        print "\nModify: {$modifySource}, This: {$this->standingOrder->getNextDate()}\Passed: {$standingOrder->getNextDate()}\n";
    }

    /**
     * @param MyCarbon $finish_date
     * @return StandingOrderIterator
     */
    public function setFinishDate($finish_date)
    {
        $this->finish_date = (new MyCarbon($finish_date))->earliest($this->standingOrder->getFinishDate());
        return $this;
    }



    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->standingOrder->getNextTransaction();
    }


    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->standingOrder->increment();

    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        if (    empty($this->finish_date)
            ||  empty($this->standingOrder->getNextDate())
            ||  $this->finish_date->diffInDays($this->standingOrder->getNextDate(), false) > 0
        ) {
//            print "\nNot Valid: FinishDate: {$this->finish_date}, NextDate: {$this->standingOrder->getNextDate()}";
            return false;
        }
//        print "\nValid: FinishDate: {$this->finish_date}, NextDate: {$this->standingOrder->getNextDate()}";
        return true;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        if (empty($this->finish_date)) {
            throw new \Feenance\models\InfiniteStandingOrderIteratorException;
        }
    }
}