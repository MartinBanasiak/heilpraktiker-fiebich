<?php
namespace DynCom\dc\dcShop\abstracts;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 06.10.2015
 * Time: 21:30
 */
abstract class DiscountBase
{
    const DISCOUNT_VALUE_TYPE_PERCENT = 'DISCOUNT_VALUE_PERCENT';
    const DISCOUNT_VALUE_TYPE_AMOUNT = 'DISCOUNT_VALUE_AMOUNT';

    const DISCOUNT_TYPE_LINE_DISCOUNT = 'DISCOUNT_TYPE_LINE_DISCOUNT';
    const DISCOUNT_TYPE_INVOICE_DISCOUNT = 'DISCOUNT_TYPE_INVOICE_DISCOUNT';

    const DISCOUNT_SOURCE_TYPE_LINE_DISCOUNT = 'DISCOUNT_SOURCE_LINE_DISCOUNT';
    const DISCOUNT_SOURCE_TYPE_INVOICE_DISCOUNT = 'DISCOUNT_SOURCE_INVOICE_DISCOUNT';
    const DISCOUNT_SOURCE_TYPE_RULE = 'DISCOUNT_SOURCE_RULE';
    const DISCOUNT_SOURCE_TYPE_SUBSCRIPTION = 'DISCOUNT_SOURCE_SUBSCRIPTION';
    const DISCOUNT_SOURCE_TYPE_COUPON = 'DISCOUNT_SOURCE_COUPON';
    const DISCOUNT_SOURCE_TYPE_ONLINE_DISCOUNT = 'DISCOUNT_SOURCE_ONLINE_DISCOUNT';


    private static $allowedSourceTypes = [
        self::DISCOUNT_SOURCE_TYPE_INVOICE_DISCOUNT,
        self::DISCOUNT_SOURCE_TYPE_LINE_DISCOUNT,
        self::DISCOUNT_SOURCE_TYPE_RULE,
        self::DISCOUNT_SOURCE_TYPE_SUBSCRIPTION,
        self::DISCOUNT_SOURCE_TYPE_COUPON,
        self::DISCOUNT_SOURCE_TYPE_ONLINE_DISCOUNT,
    ];

    private static $type;

    private $sourceType;
    private $sourceID;
    private $percent;
    private $amount;
    private $valueType;
    private $notification;


    /**
     * @param $sourceType
     * @return bool
     */
    private function isAllowedSourceType($sourceType) {
        return in_array($sourceType,self::$allowedSourceTypes,true);
    }

    /**
     * DiscountBase constructor.
     * @param $sourceType
     * @param $sourceID
     * @param $percent
     * @param float $amount
     */
    public function __construct($sourceType, $sourceID, $percent, $amount = 0.00) {
        if(!$this->isAllowedSourceType($sourceType)) {
            $msg = 'Parameter \'sourceType\' must contain a valid source type. Valid source types are: ';
            foreach(self::$allowedSourceTypes as $typeName) {
                $msg .= "
                    $typeName
                ";
            }
            throw new \InvalidArgumentException($msg);
        }
        if(!(strlen($sourceID) > 0)) {
            $msg = 'Parameter \'sourceID\' must not be empty.';
            throw new \InvalidArgumentException($msg);
        }
        if((!($percent > 0) && !($amount > 0)) || (($percent > 0) && ($amount > 0))) {
            throw new \InvalidArgumentException('Exactly one of the parameters must have a positive value.');
        }
        $this->sourceType = $sourceType;
        $this->sourceID = $sourceID;
        $this->percent = (float)$percent;
        $this->amount = (float)$amount;
        if($this->percent > 0) {
            $this->valueType = self::DISCOUNT_VALUE_TYPE_PERCENT;
        } else {
            $this->valueType = self::DISCOUNT_VALUE_TYPE_AMOUNT;
        }
    }

    public function getType()
    {
        return self::$type;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        if($this->percent !== 0.00) {
            return $this->percent;
        }
        return $this->amount;
    }

    /**
     * @param $price
     * @return float
     */
    public function getResultForPrice($price)
    {
        $price = (float)$price;
        if($this->percent > 0) {
            $val = $price * ($this->percent / 100);
        } else {
            $val = $this->amount;
        }
        $price -= $val;
        return $price;
    }

    public function getSourceType() {
        return $this->sourceType;
    }

    public function getSourceID() {
        return $this->sourceID;
    }

    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @param $notification
     */
    public function setNotification($notification)
    {
        $this->notification = $notification;
    }

    /**
     * @return float
     */
    public function getDiscountedPercentage()
    {
        return $this->percent;
    }

    /**
     * @return float
     */
    public function getDiscountedAmount()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getValueType()
    {
        return $this->valueType;
    }

}