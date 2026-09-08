<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\dcShop\interfaces\Discount;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 06.10.2015
 * Time: 21:25
 */
class AppliedDiscount
{

    private $priceBeforeApplication;
    private $priceAfterApplication;
    private $discountedAmount;
    private $appliedType;
    private $appliedSourceType;
    private $appliedSourceID;
    private $discountNotification;
    private $discountValue;
    private $discountValueType;
    private $discountEvaluationType;

    /**
     * AppliedDiscount constructor.
     * @param $priceBeforeApplication
     * @param Discount $discount
     */
    public function __construct($priceBeforeApplication, Discount $discount) {
        $this->priceBeforeApplication = (float)$priceBeforeApplication;
        $this->priceAfterApplication = $discount->getResultForPrice($this->priceBeforeApplication);
        $this->discountedAmount = $this->priceBeforeApplication - $this->priceAfterApplication;
        $this->appliedType = $discount->getType();
        $this->appliedSourceType = $discount->getSourceType();
        $this->appliedSourceID = $discount->getSourceID();
        $this->discountNotification = $discount->getNotification();
        $this->discountValue = $discount->getValue();
        $this->discountValueType = $discount->getValueType();
        $this->discountEvaluationType = $discount instanceof GenericInvoiceDiscount ? $discount->getEvaluationType() : null;
    }

    /**
     * @return float
     */
    public function getPriceBeforeApplication() {
        return $this->priceBeforeApplication;
    }

    /**
     * @return float
     */
    public function getPriceAfterApplication() {
        return $this->priceAfterApplication;
    }

    /**
     * @return float
     */
    public function getDiscountedAmount() {
        return $this->discountedAmount;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->appliedType;
    }

    /**
     * @return string
     */
    public function getSourceType() {
        return $this->appliedSourceType;
    }

    /**
     * @return mixed
     */
    public function getSourceID() {
        return $this->appliedSourceID;
    }

    /**
     * @return string
     */
    public function getNotification()
    {
        return $this->discountNotification;
    }

    /**
     * @return float
     */
    public function getDiscountValue()
    {
        return $this->discountValue;
    }

    /**
     * @return string
     */
    public function getDiscountValueType()
    {
        return $this->discountValueType;
    }

    public function getDiscountEvaluationType()
    {
        return $this->discountEvaluationType;
    }
}