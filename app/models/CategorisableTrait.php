<?php namespace Feenance\models;


trait CategorisableTrait {

    /*** @var integer    */  private $category_id = null;
    /*** @var integer    */  private $payee_id    = null;

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return $this->category_id;
    }

    /**
     * @param int $category_id
     */
    public function setCategoryId($category_id)
    {
        $this->category_id = $category_id;
    }

    /**
     * @return int
     */
    public function getPayeeId()
    {
        return $this->payee_id;
    }

    /**
     * @param int $payee_id
     */
    public function setPayeeId($payee_id)
    {
        $this->payee_id = $payee_id;
    }

    /*** @return bool*/
    public function isCategorised()
    {
        return empty($this->category_id) && empty($this->payee_id);
    }

}