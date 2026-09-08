<?php
/**
 * Created by PhpStorm.
 * User: lorenz
 * Date: 19.03.2018
 * Time: 14:04
 */

namespace DynCom\dc\dcShop\classes;


use DynCom\dc\common\interfaces\Observer;
use DynCom\dc\common\interfaces\SessionFlashMessageBag;
use DynCom\dc\dcShop\abstracts\DiscountBase;
use DynCom\dc\dcShop\interfaces\IVATManager;
use DynCom\dc\dcShop\interfaces\TextProviderInterface;

class BasketChangeListener implements Observer
{

    public const EVENT_NAME_BASKET_CHANGED = 'basket_changed';
    /**
     * @var CouponLineRepository
     */
    private $couponLineRepository;
    /**
     * @var CurrShopConfiguration
     */
    private $currShopConfig;
    /**
     * @var IVATManager
     */
    private $VATManager;
    /**
     * @var SessionFlashMessageBag
     */
    private $msgBag;
    /**
     * @var TextProviderInterface
     */
    private $textProvider;
    /**
     * @var WebshopItemBuilder
     */
    private $itemBuilder;

    /**
     * BasketChangeListener constructor.
     * @param CouponLineRepository $couponLineRepository
     * @param CurrShopConfiguration $currShopConfig
     * @param IVATManager $VATManager
     * @param SessionFlashMessageBag $msgBag
     * @param TextProviderInterface $textProvider
     * @param WebshopItemBuilder $itemBuilder
     */
    public function __construct(CouponLineRepository $couponLineRepository, CurrShopConfiguration $currShopConfig, IVATManager $VATManager, SessionFlashMessageBag $msgBag, TextProviderInterface $textProvider, WebshopItemBuilder $itemBuilder)
    {
        $this->couponLineRepository = $couponLineRepository;
        $this->currShopConfig = $currShopConfig;
        $this->VATManager = $VATManager;
        $this->msgBag = $msgBag;
        $this->textProvider = $textProvider;
        $this->itemBuilder = $itemBuilder;
    }


    /**
     * @param $eventName
     * @param GenericUserBasket $data
     */
    public function notify($eventName, $data)
    {
        $currBasket = $data;
        if (isset($_SESSION["coupon"]["coupon_code"])) {
            $primary = ['company' => $this->currShopConfig->getCompany(), 'code' => $_SESSION["coupon"]["code"], 'coupon_code' => $_SESSION["coupon"]["coupon_code"]];
            /** @var CouponLine $couponLine */
            $couponLine = $this->couponLineRepository->findByAltPrimary($primary);

            $coupon = $_SESSION["coupon"];
            if (!(bool)$coupon["value_coupon"]) {
                $basketAmount = $currBasket->getBasketTotal();
                $couponAmountFrom = (float)$coupon['amount_from'];
                $isCategoryCoupon = (bool)$coupon['category_coupon'];
                $categoryCouponIsValid = false;
                if ($isCategoryCoupon) {
                    $couponCatLineNo = (int)$coupon['category_line_no'];
                    $basketAmount = 0;
                    foreach ($currBasket as $basketItem) {
                        if ($basketItem instanceof BasketEntity && $basketItem->getOrderableType() === 2) {
                            $orderableEntity = $basketItem->getOrderableEntity();
                            if ($this->itemBuilder instanceof WebshopItemBuilder) {
                                $itemWithCatgeoies = $this->itemBuilder->decorateWebshopItemCategories($orderableEntity);
                                if ($itemWithCatgeoies->isInCategory($couponCatLineNo)) {
                                    $categoryCouponIsValid = true;
                                    $basketAmount += $basketItem->getUnitPrice() * $basketItem->getQuantity();
                                }
                            }
                        }
                    }
                }
                $isCouponAmountFromGreaterZeroAndMoreThanBasket = ($couponAmountFrom > 0 && ($couponAmountFrom > $basketAmount));
                if ($isCouponAmountFromGreaterZeroAndMoreThanBasket || (!$categoryCouponIsValid && $isCategoryCoupon)) {
                    foreach ($currBasket as $basketItem) {
                        if ($basketItem instanceof BasketEntity) {
                            //Remove Items
                            if (
                                $basketItem->getCreationSourceType() == BasketEntity::CREATION_SOURCE_TYPE_COUPON
                                && $basketItem->getCreationSourceID() == $_SESSION['coupon']['id']
                            ) {
                                $key = $currBasket->getKey($basketItem);
                                $newKey = $currBasket->setItemQtyAccessibilityByKey($key, true);
                                $currBasket->removeItemByKey($newKey);
                            }

                            //Remove Line Discounts
                            $appliedLineDiscs = $basketItem->getAppliedLineDiscounts();
                            foreach ($appliedLineDiscs as $appliedLineDisc) {
                                /**
                                 * @var $appliedLineDisc AppliedDiscount
                                 */
                                if ($appliedLineDisc->getSourceType() === BasketEntity::CREATION_SOURCE_TYPE_COUPON) {
                                    $valueType = $appliedLineDisc->getDiscountValueType();
                                    $value = $appliedLineDisc->getDiscountValue();
                                    $percent = $valueType === DiscountBase::DISCOUNT_VALUE_TYPE_PERCENT ? $value : 0.00;
                                    $amnt = $valueType === DiscountBase::DISCOUNT_VALUE_TYPE_AMOUNT ? $value : 0.00;

                                    $genericDiscount = new \DynCom\dc\dcShop\classes\GenericLineDiscount($appliedLineDisc->getSourceType(), $appliedLineDisc->getSourceID(), $percent, $amnt);
                                    $basketItem->removeDiscount($genericDiscount, $this->VATManager);
                                }
                            }
                        }
                    }

                    //Remove Invoice Account
                    $basketInvoiceDiscounts = $currBasket->getAppliedInvoiceDiscounts();
                    foreach ($basketInvoiceDiscounts as $appliedInvoiceDisc) {
                        if (
                            $appliedInvoiceDisc instanceof AppliedDiscount
                            && $appliedInvoiceDisc->getSourceType() == DiscountBase::DISCOUNT_SOURCE_TYPE_COUPON
                            && $appliedInvoiceDisc->getSourceID() == $coupon['id']
                        ) {
                            $percent = null;
                            $amnt = null;
                            if ($appliedInvoiceDisc->getDiscountValueType() == DiscountBase::DISCOUNT_VALUE_TYPE_AMOUNT) {
                                $amnt = $appliedInvoiceDisc->getDiscountValue();
                            } else {
                                $percent = $appliedInvoiceDisc->getDiscountValue();
                            }
                            $invoiceDisc = new GenericInvoiceDiscount(
                                $appliedInvoiceDisc->getSourceType(),
                                $appliedInvoiceDisc->getSourceID(),
                                $percent,
                                $amnt
                            );
                            $currBasket->removeInvoiceDiscount($invoiceDisc);
                        }
                    }

                    /*$arr = $couponLine->getAllFieldsAsArray();
                    $arr["times_used"] = $arr["times_used"] - 1;
                    $couponLine->mapFromArray($arr);

                    if ($this->couponLineRepository->updateSingle($couponLine)) {*/
                        $_SESSION['coupon'] = '';
                        unset($_SESSION['coupon']);

                        $toReplace = [
                            '{%coupon_code%}',
                        ];

                        $replaceWith = [
                            $coupon["coupon_code"],
                        ];

                        $msgType = SessionFlashMessageBag::TYPE_INFO;
                        $this->setMsg($msgType, 'coupon_removed_from_basket_notification', $toReplace, $replaceWith);
                    //}
                }
            }
        }
    }

    protected function setMsg(string $type, string $textConstantName,array $toReplace = [], array $replaceWith = []) : void
    {
        $rawMsg = $this->textProvider->getText($textConstantName);
        $processedMsg = str_replace($toReplace,$replaceWith,$rawMsg);
        $this->msgBag->set($type,$processedMsg);
    }
}