<?php namespace Feenance\models;

abstract class DomainModel implements ExtendedArrayableInterface {

    function __construct($array = null)
    {
        if ( is_array($array) ) {
            $this->fromArray($array);
        }
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}