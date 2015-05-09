<?php namespace Feenance\models\eloquent;
use Feenance\models\StandingOrder;

class StandingOrderIterator implements \Iterator{
    /** @var  StandingOrder */
    private $standingOrder;

    function __construct($standingOrder)
    {
        $this->standingOrder = $standingOrder;
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
        print "\nGetting next(): IsClone={$this->standingOrder->isClone}";
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
        print "\nkey(): IsClone={$this->standingOrder->isClone}";
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
        print "\nvalid(): IsClone={$this->standingOrder->isClone}";

        if (    empty($this->standingOrder->getFinishDate())
            ||  empty($this->standingOrder->getNextDate())
            ||  $this->standingOrder->getFinishDate()->diffInDays($this->standingOrder->getNextDate()) > 0
        ) {
            return false;
        }
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
        print "\nrewind(): IsClone={$this->standingOrder->isClone}";

        if (empty($this->standingOrder->getFinishDate())) {
            $this->standingOrder->setFinishDate(clone($this->standingOrder->getNextDate()));
        }
    }
}