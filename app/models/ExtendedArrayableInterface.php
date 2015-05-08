<?php namespace Feenance\models;

use Illuminate\Support\Contracts\ArrayableInterface;
use \JsonSerializable;

interface ExtendedArrayableInterface extends ArrayableInterface, JsonSerializable {

    /**
     * Create the model from an externally supplied array
     * such as a posted restful array
     * @param $setValues array
     */
    public function fromArray($setValues);

    /**
     * Create the model from an internally supplied array
     * such as from an eloquent database query
     * @param $setValues array
     */
    public function toStorageArray();
}

trait ExtendedArrayableTrait {

    public function toStorageArray() {
        return $this->toArray();
    }

}