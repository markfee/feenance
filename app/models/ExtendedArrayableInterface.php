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
    public function fromStorageArray($setValues);

    /**
     * Get the instance as an array for internal storage.
     * such as from an eloquent database query
     * @return array
     */
    public function toStorageArray();
}

trait ExtendedArrayableTrait {

    public function fromStorageArray($setValues) {
        return $this->fromArray($setValues);
    }

    public function toStorageArray() {
        return $this->toArray();
    }

}