<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 03/04/15
 * Time: 10:08
 */

namespace Feenance\models;


interface CategorisableInterface {

    /*** @return int*/                  public function getCategoryId();
    /*** @param int $category_id*/      public function setCategoryId($category_id);

    /*** @return int*/                  public function getPayeeId();
    /*** @param int $payee_id*/         public function setPayeeId($payee_id);

    /*** @return bool*/                  public function isCategorised();

}