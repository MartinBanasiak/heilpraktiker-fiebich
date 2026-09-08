<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\dcShop\interfaces\Order;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;
use DynCom\dc\dcShop\subscriptions\SubscriptionItemDecorator;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.07.2015
 * Time: 15:16
 */
class CouponApplicator
{

    const ORDER_TYPE_STD = 'DEFAULT_ORDER';
    const ORDER_TYPE_SUBSCRIPTION = 'SUBSCRIPTION_ORDER';

    protected $currShopConfiguration;


    /**
     * CouponApplicator constructor.
     * @param CurrShopConfiguration $currShopConfiguration
     */
    public function __construct(CurrShopConfiguration $currShopConfiguration) {
        $this->currShopConfiguration = $currShopConfiguration;
    }

    /**
     * @param Order $order
     * @param $couponCodeInput
     * @param array $errors
     * @return OrderCouponDTO
     */
    public function applyCouponToOrder(Order $order, $couponCodeInput, array &$errors) {
        if($order->getOrderType() === self::ORDER_TYPE_STD) {
            return $this->applyCouponToDefaultOrder($order,$couponCodeInput,$errors);
        } elseif($order->getOrderType() === self::ORDER_TYPE_SUBSCRIPTION) {
            return $this->applyCouponToSubscriptionOrder($order,$couponCodeInput,$errors);
        }
        throw new \InvalidArgumentException(
            "Order-Type of Order must be either DEFAULT_ORDER or SUBSCRIPTION_ORDER"
        );
    }

    /**
     * @param Order $order
     * @param $couponCodeInput
     * @param $errors
     * @return OrderCouponDTO
     */
    public function applyCouponToDefaultOrder(Order $order,$couponCodeInput,&$errors) {
        $data = new OrderCouponDTO();
        return $data;
    }

    /**
     * @param Order $order
     * @param $couponCodeInput
     * @param $errors
     * @return OrderCouponDTO
     */
    public function applyCouponToSubscriptionOrder(Order $order,$couponCodeInput,&$errors) {
        $firstItem = $order->getItems()->getFirst();
        if($firstItem instanceof SubscriptionItemDecorator) {
            $subscHeader = $firstItem->getSubscriptionHeader();
            $payTypeIsSingle = $subscHeader->isPayTypeSinglePayment();
            $isSequenceItem = $firstItem->isTypeSequenceItem();
            $isOrderOption = $firstItem->isTypeOrderOption();
            if(!$payTypeIsSingle) {
                $errors[] = 'coupon_not_allowed_for_return_payment_subscription';
                return false;
            }
        }
        $data = new OrderCouponDTO();
        return $data;
    }

    /**
     * @param Order $order
     * @param CouponHeader $header
     * @param CouponLine $line
     * @return OrderCouponDTO
     */
    protected function applyShipmentCouponToOrder(Order $order, CouponHeader $header, CouponLine $line) {
        $appliedAmnt = $order->applyShippingCoupon();
        $data = new OrderCouponDTO($header,$line,$appliedAmnt);
        return $data;
    }

    /**
     * @param Order $order
     * @param CouponHeader $header
     * @param CouponLine $line
     * @param WebshopItemInterface $item
     * @return OrderCouponDTO
     */
    protected function applyItemCouponToOrder(Order $order, CouponHeader $header, CouponLine $line, WebshopItemInterface $item) {
        $appliedAmnt = 0.00;
        $data = new OrderCouponDTO($header,$line,$appliedAmnt,$item);
        return $data;
    }

}