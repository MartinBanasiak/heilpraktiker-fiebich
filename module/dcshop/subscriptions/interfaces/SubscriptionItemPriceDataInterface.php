<?php
namespace DynCom\dc\dcShop\subscriptions\interfaces;
use DynCom\dc\dcShop\interfaces\ItemPriceDataInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/26/2015
 * Time: 4:53 PM
 */
interface SubscriptionItemPriceDataInterface extends ItemPriceDataInterface
{
    /**
     * @return mixed
     */
    public function setPriceSourceSubscriptionFixPrice();

    /**
     * @return mixed
     */
    public function setPriceSourceSubscriptionPricePerBillingInterval();

    /**
     * @return mixed
     */
    public function setDiscountStatusSubscriptionDiscountApplied();

    /**
     * @param $amnt
     * @return mixed
     */
    public function setSubscriptionSavingsAmount($amnt);

    /**
     * @param $percent
     * @return mixed
     */
    public function setSubscriptionSavingsPercent($percent);

    /**
     * @return mixed
     */
    public function getSavingsPerUnitDesc();

    /**
     * @param int $noOfTurns
     * @param int $itemQty
     * @return mixed
     */
    public function getSavingsTotalDesc($noOfTurns = 1, $itemQty = 1);

    /**
     * @param int $noOfTurns
     * @param int $itemQty
     * @return mixed
     */
    public function getSubscriptionPriceTotalDesc($noOfTurns = 1, $itemQty = 1);

    /**
     * @return mixed
     */
    public function getFormattedSubscriptionSequenceTotal();

    /**
     * @param $description
     * @return mixed
     */
    public function setSubscriptionPayIntervalDescription($description);

    /**
     * @return mixed
     */
    public function getSubscriptionPayIntervalDescription();

    /**
     * @return mixed
     */
    public function getSubscriptionHeader();

    /**
     * @return mixed
     */
    public function getSubscriptionItemLink();

    /**
     * @return mixed
     */
    public function getItemVATProdPostingGroup();

    /**
     * @param $sourceType
     * @return mixed
     */
    public function setQtySourceType($sourceType);
}