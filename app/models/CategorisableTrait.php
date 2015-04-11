<?php namespace Feenance\models;


trait CategorisableTrait {

    /*** @var integer    */  private $category_id = null;
    /*** @var integer    */  private $payee_id    = null;

    /*** @return array*/
    public function toCategorisableArray()
    {
        return [
            "category_id" =>    $this->getCategoryId(),
            "payee_id" =>       $this->getPayeeId(),
        ];
    }

    /*** @param $param */
    public function fromCategorisableArray($param)
    {
        $this->setCategoryId($param["category_id"]);
        $this->setPayeeId($param["payee_id"]);
    }

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
        return !(empty($this->category_id) && empty($this->payee_id));
    }

}