<?php
namespace DynCom\dc\dcShop\interfaces;
use DynCom\dc\common\classes\Address;
use DynCom\dc\common\classes\EMail;
use DynCom\dc\dcShop\classes\Customer;
use DynCom\dc\dcShop\classes\CustomerPseudoPayData;
use DynCom\dc\dcShop\classes\OrderCouponDTO;
use DynCom\dc\dcShop\classes\OrderItem;
use DynCom\dc\dcShop\classes\OrderItemCollection;
use DynCom\dc\dcShop\classes\OrderPayData;
use DynCom\dc\dcShop\classes\PaymentOption;
use DynCom\dc\dcShop\classes\User;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 14.07.2015
 * Time: 14:07
 */
interface Order
{
    /**
     * @param Customer $customer
     * @return mixed
     */
    public function setCustomer(Customer $customer);

    /**
     * @param User $user
     * @return mixed
     */
    public function setUser(User $user);

    public function getUser();

    public function getCustomer();

    /**
     * @param Address $billingAddress
     * @return mixed
     */
    public function setBillingAddress(Address $billingAddress);

    public function getBillingAddress();

    /**
     * @param Address $shippingAddress
     * @return mixed
     */
    public function setShippingAddress(Address $shippingAddress);

    public function getShippingAddress();

    /**
     * @param PaymentOption $paymentOption
     * @return mixed
     */
    public function setPaymentOption(PaymentOption $paymentOption);

    public function getPaymentOption();

    public function getId();

    /**
     * @param OrderableEntityInterface $item
     * @return mixed
     */
    public function addItem(OrderableEntityInterface $item);

    /**
     * @param OrderItem $item
     * @return mixed
     */
    public function removeItem(OrderItem $item);

    public function hasValidCustomer();

    public function hasValidBillingAddress();

    public function hasValidShippingAddress();

    public function hasItems();

    /**
     * @return EMail
     */
    public function getEmail();

    /**
     * @param EMail $email
     */
    public function setEmail(EMail $email);

    public function hasValidEmail();

    /**
     * @param $text
     * @return mixed
     */
    public function setYourReference($text);

    /**
     * @param $text
     * @return mixed
     */
    public function setYourComment($text);

    public function getYourReference();

    public function getYourComment();

    /**
     * @param OrderCouponDTO $couponDTO
     * @return mixed
     */
    public function setCouponDTO(OrderCouponDTO $couponDTO);

    /**
     * @param OrderCouponDTO $couponDTO
     * @return mixed
     */
    public function unsetCouponDTO(OrderCouponDTO $couponDTO);

    /**
     * @return OrderItemCollection
     */
    public function getItems();

    /**
     * @return string
     */
    public function getOrderType();

    /**
     * @param OrderPayData $orderPayData
     * @return mixed
     */
    public function setOrderPayData(OrderPayData $orderPayData);

    /**
     * @param CustomerPseudoPayData $customerPseudoPayData
     * @return mixed
     */
    public function setCustomerPseudoPayData(CustomerPseudoPayData $customerPseudoPayData);

    public function hasValidOrderPayData();

    public function hasValidCustomerPseudoPayData();

    public function getValidationRules();

    public function setValidationRules();

    public function isValid();

    public function getDataFlat();

}