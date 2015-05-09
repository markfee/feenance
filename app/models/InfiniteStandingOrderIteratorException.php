<?php namespace Feenance\models;

class InfiniteStandingOrderIteratorException extends \Exception
{

    function __construct()
    {
        $this->message = "An attempt to iterate a standing order without an finish date occurred.";
    }
}