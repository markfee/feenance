<?php namespace Feenance\models;

use Feenance\models\StandingOrderIterator;
use Markfee\MyCarbon;
use Feenance\services\Currency\NullCurrencyConverter;
use Iterator;
use IteratorAggregate;
use Traversable;

class StandingOrder extends DomainModel implements IteratorAggregate
{
    use HasCurrencyTrait;

    /*** @var string */
    private $name = null;
    /*** @var MyCarbon */
    private $previous_date = null;
    /*** @var MyCarbon */
    private $next_date = null;
    /*** @var MyCarbon */
    private $finish_date = null;
    /*** @var integer */
    private $increment = 1;
    /*** @var string */
    private $increment_unit = "d"; // default 1 day
    /*** @var string */
    private $exceptions = null; // Eg months to exclude
    /*** @var integer */
    private $amount = null;
    /*** @var integer */
    private $next_bank_day = null; // skip to the previous (-1) or next (+1) valid bank day.
    /*** @var string */
    private $modifier = null; // eg "last day of month" -- see Carbon
    /*** @var integer */
    private $account_id = null;
    /*** @var integer */
    private $destination_account_id = null; // for transfers
    /*** @var integer */
    private $payee_id = null;
    /*** @var integer */
    private $category_id = null;
    /*** @var integer */
    private $bank_string_id = null;
    /*** @var string */
    private $notes = null;

    function __clone()
    { // Make sure the dates aren't modified when cloned.
        $this->next_date        = $this->next_date      ? clone $this->next_date        : null;
        $this->previous_date    = $this->previous_date  ? clone $this->previous_date    : null;
        $this->finish_date      = $this->finish_date    ? clone $this->finish_date      : null;
    }

    /**
     * Create the model from an externally supplied array
     * such as a posted restful array
     * @param $setValues array
     */
    public function fromArray($setValues)
    {
        $values = array_merge($this->toArray(), $setValues);

        $this->setCurrencyCode($values["currency_code"]);
        $this->setName($values["name"]);
        $this->setPreviousDate($values["previous_date"]);
        $this->setNextDate($values["next_date"]);
        $this->setFinishDate($values["finish_date"]);
        $this->setIncrement($values["increment"]);
        $this->setIncrementUnit($values["increment_unit"]);
        $this->setExceptions($values["exceptions"]);
        $this->setAmount($values["amount"]);
        $this->setNextBankDay($values["next_bank_day"]);
        $this->setModifier($values["modifier"]);
        $this->setAccountId($values["account_id"]);
        $this->setDestinationAccountId($values["destination_account_id"]);
        $this->setPayeeId($values["payee_id"]);
        $this->setCategoryId($values["category_id"]);
        $this->setBankStringId($values["bank_string_id"]);
        $this->setNotes($values["notes"]);
    }

    public function isValid()
    {
        return
            ($this->next_date instanceof MyCarbon) &&
            (!empty($this->amount)) &&
            (!empty($this->account_id));
    }

    /**
     * Get the instance as an array for internal storage.
     * such as from an eloquent database query
     * @return array
     */
    public function toStorageArray()
    {
        $oldConverterCode = $this->setCurrencyCode("XXX_pence");
        $array = $this->toArray();
        $this->setCurrencyCode($oldConverterCode);
        return $array;
    }

    public function toArray()
    {
        $array = [
            "currency_code" => $this->getCurrencyCode(),
            "name" => $this->getName(),
            "previous_date" => $this->getPreviousDate(),
            "next_date" => $this->getNextDate(),
            "finish_date" => $this->getFinishDate(),
            "increment" => $this->getIncrement(),
            "increment_unit" => $this->getIncrementUnit(),
            "exceptions" => $this->getExceptions(),
            "amount" => $this->getAmount(),
            "next_bank_day" => $this->getNextBankDay(),
            "modifier" => $this->getModifier(),
            "account_id" => $this->getAccountId(),
            "destination_account_id" => $this->getDestinationAccountId(),
            "payee_id" => $this->getPayeeId(),
            "category_id" => $this->getCategoryId(),
            "bank_string_id" => $this->getBankStringId(),
            "notes" => $this->getNotes(),
        ];

        $array = array_map(function ($item)
        {
            if ($item instanceof MyCarbon) {
                /*** @var $item MyCarbon */
                return $item->format("Y-m-d");
            }
            return $item;
        }, $array);
        return $array;
    }

    public function getNextTransaction()
    {
        $transaction = new Transaction($this->getCurrencyCode());
        $transaction->setAmount($this->getAmount());
        $transaction->setDate($this->getNextDate());
        $transaction->setAccountId($this->getAccountId());
        return $transaction;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return MyCarbon
     */
    public function getPreviousDate()
    {
        return $this->previous_date;
    }

    /**
     * @param MyCarbon $previous_date
     */
    public function setPreviousDate($previous_date)
    {
        $this->previous_date = $previous_date ? new MyCarbon($previous_date) : null;
    }

    /**
     * @return MyCarbon
     */
    public function getNextDate()
    {
        return $this->next_date;
    }

    /**
     * @param MyCarbon $next_date
     * @return boolean
     */
    public function nextDateIs($next_date)
    {
        if (empty($this->next_date) || empty($next_date) ) {
            return (empty($this->next_date) && empty($next_date));
        }
        return $this->next_date->isSameDay(new MyCarbon($next_date));
    }



    /**
     * @param MyCarbon $next_date
     */
    public function setNextDate($next_date)
    {
        $this->next_date = $next_date ? new MyCarbon($next_date) : null;
    }

    /**
     * @return MyCarbon
     */
    public function getFinishDate()
    {
        return $this->finish_date;
    }

    /**
     * @param MyCarbon $finish_date
     */
    public function setFinishDate($finish_date)
    {
        $this->finish_date = $finish_date ? new MyCarbon($finish_date) : null;
    }

    /**
     * @return int
     */
    public function getIncrement()
    {
        return $this->increment;
    }

    /**
     * @param int $increment
     */
    public function setIncrement($increment)
    {
        $this->increment = $increment;
    }

    /**
     * @return string
     */
    public function getIncrementUnit()
    {
        return $this->increment_unit;
    }

    /**
     * @param string $increment_unit
     */
    public function setIncrementUnit($increment_unit)
    {
        $this->increment_unit = $increment_unit;
    }

    /**
     * @return string
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }

    /**
     * @param string $exceptions
     */
    public function setExceptions($exceptions)
    {
        $this->exceptions = $exceptions;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->convertToMainCurrency($this->amount);
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $this->convertToSubCurrency($amount);;
    }

    /**
     * @return int
     */
    public function getNextBankDay()
    {
        return $this->next_bank_day;
    }

    /**
     * @param int $next_bank_day
     */
    public function setNextBankDay($next_bank_day)
    {
        $this->next_bank_day = $next_bank_day;
    }

    /**
     * @return string
     */
    public function getModifier()
    {
        return $this->modifier;
    }

    /**
     * @param string $modifier
     */
    public function setModifier($modifier)
    {
        $this->modifier = $modifier;
    }

    /**
     * @return int
     */
    public function getAccountId()
    {
        return $this->account_id;
    }

    /**
     * @param int $account_id
     */
    public function setAccountId($account_id)
    {
        $this->account_id = $account_id;
    }

    /**
     * @return int
     */
    public function getDestinationAccountId()
    {
        return $this->destination_account_id;
    }

    /**
     * @param int $destination_account_id
     */
    public function setDestinationAccountId($destination_account_id)
    {
        $this->destination_account_id = $destination_account_id;
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
    public function getBankStringId()
    {
        return $this->bank_string_id;
    }

    /**
     * @param int $bank_string_id
     */
    public function setBankStringId($bank_string_id)
    {
        $this->bank_string_id = $bank_string_id;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * @param boolean $modify pass true to create an iterator that will increment the standing order.
     * @return Traversable
     */
    public function getIterator()
    {
        return new StandingOrderIterator($this);
    }

    public function getIncrementer()
    {
        return new StandingOrderIterator($this, true);
    }

    /**
     * @param $finishDate MyCarbon
     * @param $modify boolean flag to indicate whether the iterator should modify $this
     * @return StandingOrderIterator
     */
    public function until($finishDate, $modify = false)
    {
        return (new StandingOrderIterator($this, $modify))->until($finishDate);
    }

    public function increment()
    {
        $this->previous_date = clone($this->next_date);

        $this->next_date->increment($this->getIncrement(), $this->getIncrementUnit());

        if ( !empty($this->finish_date) && $this->finish_date->diffInDays($this->next_date, false) > 0 ) {
            $this->next_date = null;
        }
    }
}