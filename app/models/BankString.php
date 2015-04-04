<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 03/04/15
 * Time: 10:12
 */

namespace Feenance\models;


class BankString implements CategorisableInterface, BankStringInterface {
    use CategorisableTrait;
    use BankStringTrait;
}