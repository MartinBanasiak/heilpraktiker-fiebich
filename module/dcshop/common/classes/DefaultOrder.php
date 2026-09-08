<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\Address;
use DynCom\dc\common\classes\EMail;
use DynCom\dc\common\classes\Visitor;
use DynCom\dc\dcShop\interfaces\Order;
use DynCom\dc\dcShop\interfaces\OrderableEntityInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 14.07.2015
 * Time: 14:07
 */
class DefaultOrder implements Order
{

    const ORDER_TYPE_STD = 'DEFAULT_ORDER';
    const ORDER_TYPE_SUBSCRIPTION = 'SUBSCRIPTION_ORDER';

    protected $orderType = self::ORDER_TYPE_STD;

    protected $id;

    /**
     * @var Shop
     */
    protected $shop;

    /**
     * @var ShopLanguage
     */
    protected $shopLanguage;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Visitor
     */
    protected $visitor;

    /**
     * @var Address
     */
    protected $billingAddress;
    /**
     * @var Address
     */
    protected $shippingAddress;

    /**
     * @var PaymentOption
     */
    protected $paymentOption;

    /**
     * @var ShippingClass
     */
    protected $shippingOption;

    /**
     * @var EMail
     */
    protected $email;

    /**
     * @var OrderItemCollection
     */
    protected $items;

    /**
     * @var float
     */
    protected $subtotal;

    /**
     * @var float;
     */
    protected $total;

    /**
     * @var array
     */
    protected $itemTotalPerVATCode = [];

    /**
     * @var bool
     */
    protected $onlineDiscountApplied = false;

    /**
     * @var float
     */
    protected $onlineDiscountPercent = 0.00;

    /**
     * @var bool
     */
    protected $invoiceDiscountApplied = false;

    /**
     * @var float
     */
    protected $invoiceDiscountPercent = 0.00;

    /**
     * @var bool
     */
    protected $smallQuantityChargeApplied = false;

    /**
     * @var float
     */
    protected $smallQuantityCharge = 0.00;

    protected $couponAppliedToTotal = false;
    protected $couponAmountAppliedToTotal = 0.00;

    protected $couponAppliedToShipping = false;
    protected $couponAmountAppliedToShipping = 0.00;

    /**
     * @var bool
     */
    protected $shippingCostSet = false;

    /**
     * @var float
     */
    protected $shippingCost = 0.00;

    /**
     * @var bool
     */
    protected $paymentCostSet = false;

    /**
     * @var float
     */
    protected $paymentCost = 0.00;

    /**
     * @var OrderCouponDTO
     */
    protected $orderCouponDTO;

    /**
     * @var string
     */
    protected $yourComment;

    /**
     * @var string
     */
    protected $yourReference;


    /**
     * @var VATManager
     */
    protected $VATManager;


    /**
     * @var InvoiceDiscountRepository
     */
    protected $invoiceDiscountRepository;

    /**
     * DefaultOrder constructor.
     * @param $id
     * @param CurrShopConfiguration $currShopConfiguration
     * @param VATManager $VATManager
     * @param InvoiceDiscountRepository $invoiceDiscountRepository
     */
    public function __construct($id, CurrShopConfiguration $currShopConfiguration, VATManager $VATManager, InvoiceDiscountRepository $invoiceDiscountRepository) {
        $this->id = strip_tags($id);
        $this->items = new OrderItemCollection($this);
        $this->shopLanguage = $currShopConfiguration->getShopLanguage();
        $this->shop = $currShopConfiguration->getShop();
        $this->visitor = $currShopConfiguration->getVisitor();
        $this->user = $currShopConfiguration->getUser();
        $this->customer = $currShopConfiguration->getCustomer();
        $this->VATManager = $VATManager;
        $this->invoiceDiscountRepository = $invoiceDiscountRepository;
    }

    /**
     * @param Customer $customer
     */
    public function setCustomer(Customer $customer) {
        $this->customer = $customer;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user) {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @return Customer
     */
    public function getCustomer() {
        return $this->customer;
    }

    /**
     * @param Address $billingAddress
     */
    public function setBillingAddress(Address $billingAddress) {
        $this->billingAddress = $billingAddress;
    }

    /**
     * @return Address
     */
    public function getBillingAddress() {
        return $this->billingAddress;
    }

    /**
     * @param Address $shippingAddress
     */
    public function setShippingAddress(Address $shippingAddress) {
        $this->shippingAddress = $shippingAddress;
    }

    /**
     * @return Address
     */
    public function getShippingAddress() {
        return $this->shippingAddress;
    }

    /**
     * @param PaymentOption $paymentOption
     */
    public function setPaymentOption(PaymentOption $paymentOption) {
        $this->paymentOption = $paymentOption;
        $this->paymentCost = $paymentOption->payment_cost;
        $this->paymentCostSet = true;
    }

    /**
     * @param ShippingClass $shippingOption
     */
    public function setShippingOption(ShippingClass $shippingOption)
    {
        $this->shippingOption = $shippingOption;
        $this->shippingCost = ($this->subtotal <= $shippingOption->exemption) ? $shippingOption->shipping_cost : 0.00;
        $this->shippingCostSet = true;
    }

    /**
     * @return PaymentOption
     */
    public function getPaymentOption() {
        return $this->paymentOption;
    }

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param OrderableEntityInterface $item
     */
    public function addItem(OrderableEntityInterface $item) {
        $this->items->addItem($item);
        $this->subtotal = $this->items->getTotal();
        $this->itemTotalPerVATCode = $this->items->getTotalPerVATCode();
    }

    /**
     * @param OrderItem $item
     */
    public function removeItem(OrderItem $item) {
        $this->items->removeItem($item);
        $this->subtotal = $this->items->getTotal();
        $this->itemTotalPerVATCode = $this->items->getTotalPerVATCode();
    }

    /**
     * @return bool
     */
    public function hasValidCustomer() {
        return (($this->customer instanceof Customer) && ($this->customer->getID() > 0));
    }

    /**
     * @return bool
     */
    public function hasValidBillingAddress() {
        return ($this->billingAddress instanceof Address && $this->billingAddress->isValid());
    }

    /**
     * @return bool
     */
    public function hasValidShippingAddress() {
        return ($this->shippingAddress instanceof Address && $this->shippingAddress->isValid());
    }

    /**
     * @return bool
     */
    public function hasItems() {
        return (count($this->items) > 0);
    }

    /**
     * @return EMail
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param EMail $email
     */
    public function setEmail(EMail $email)
    {
        $this->email = $email;
    }

    /**
     * @return bool
     */
    public function hasValidEmail() {
        return (bool)$this->email;
    }

    /**
     * @param $text
     */
    public function setYourReference($text) {
        $this->yourReference = strip_tags($text);
    }

    /**
     * @param $text
     */
    public function setYourComment($text) {
        $this->yourComment = strip_tags($text);
    }

    /**
     * @return string
     */
    public function getYourReference() {
        return $this->yourReference;
    }

    /**
     * @return string
     */
    public function getYourComment() {
        return $this->yourComment;
    }

    /**
     * @param OrderCouponDTO $couponData
     */
    public function setCouponDTO(OrderCouponDTO $couponData) {
        //@ToDo: Outsource
        $VATCodePercentArray = [];

        $this->orderCouponDTO = $couponData;
        if(($this->subtotal > 0) && ($couponData->isCouponTypeAmount() || $couponData->isCouponTypeIndividualAmount())) {
            $amountLeftToApply = $couponData->getAmountToApply();
            $VATCodes = array_keys($this->itemTotalPerVATCode);
            foreach($VATCodes as $VATCode) {
                $VATCodePercent = $this->VATManager->getVATPercentForProdPostingGroup($VATCode);
                $VATCodePercentArray[$VATCode] = $VATCodePercent;
            }
            arsort($VATCodePercentArray);
            $itemTotal = 0.00;
            $totalDiscountAmount = $amountLeftToApply;
            foreach($VATCodePercentArray as $VATCode => $VATPercent) {
                $rateAmnt = $this->itemTotalPerVATCode[$VATCode];
                $amntToSubtract = ($rateAmnt > $amountLeftToApply) ? $amountLeftToApply : $rateAmnt;
                $rateAmnt -= abs($amntToSubtract);
                $amountLeftToApply -= abs($amntToSubtract);
                $this->itemTotalPerVATCode[$VATCode] = $rateAmnt;
                $itemTotal += $rateAmnt;
            }
            $this->subtotal = $itemTotal;
        } elseif($couponData->isCouponTypePercent()) {
            $percent = 0;
        }
    }

    /**
     * @return OrderItemCollection
     */
    public function getItems() {
        return $this->items;
    }

    /**
     * @param OrderCouponDTO $couponData
     */
    public function setOrderCouponDTO(OrderCouponDTO $couponData) {
        $this->orderCouponDTO = $couponData;
    }

    /**
     * @param $amount
     */
    public function applyCouponDiscountToTotal($amount) {
        $this->couponAmountAppliedToTotal = (float)$amount;
        $this->couponAppliedToTotal = true;
    }

    /**
     * @return float
     */
    protected function applyShippingCoupon() {
        $initShipAmnt = (float)$this->shippingOption->shipping_cost;
        $newShipAmnt = (float)$this->shippingOption->coupon_shipping_cost;
        $shipDiscount = (float)($initShipAmnt - $newShipAmnt);
        $this->couponAmountAppliedToShipping = $shipDiscount;
        $this->couponAppliedToShipping = true;
        return $shipDiscount;
    }

    /**
     * @return string
     */
    public function getOrderType()
    {
        return $this->orderType;
    }

    /**
     * @return float|int|mixed
     */
    public function getTotal() {
        //Items
        $itemTotal = $this->subtotal;

        //Apply Coupon Discount to Shipping if set
        if(
            isset($this->orderCouponDTO) &&
            $this->orderCouponDTO->isCouponTypeSpecialShipping()
        ) {
            $this->applyShippingCoupon();
        }

        //Ship & Pay Charges
        $shippingCost = $this->shippingCost;
        $paymentCost = $this->paymentCost;

        //Small Quantity Charge
        $smallQuantityCharge = 0.00;
        if(((float)$this->shop->small_quantity_charge > 0) && ($itemTotal < $this->shop->small_quantity_charge_limit)) {
            $this->smallQuantityCharge = (float)$this->shop->small_quantity_charge;
            $this->smallQuantityChargeApplied = true;
            $smallQuantityCharge = $this->smallQuantityCharge;
        }

        //Sum Items and Charges
        $subtotal = $itemTotal + $shippingCost + $paymentCost + $smallQuantityCharge;
        $subtotalInvoiceDiscountAllowed = $this->items->getTotalInvoiceDiscountAllowed();

        //Invoice Discount
        $invoiceDiscountPercent = $this->invoiceDiscountRepository->getBestDiscountPercentForOrder($this->customer->company,$this->customer->invoice_disc_code,$subtotalInvoiceDiscountAllowed);
        if($invoiceDiscountPercent > 0) {
            $this->invoiceDiscountPercent = $invoiceDiscountPercent;
            $invoiceDiscountAmount = (($subtotalInvoiceDiscountAllowed / 100) * $subtotalInvoiceDiscountAllowed);
            $subtotal -= $invoiceDiscountAmount;
        }

        //Online Discount
        $onlineDiscountAmount = 0.00;
        $onlineDiscountPercent = (float)$this->shop->online_discount;
        if($onlineDiscountPercent > 0) {
            $onlineDiscountAmount = (($onlineDiscountPercent / 100) * $subtotal);
            $this->onlineDiscountPercent = $onlineDiscountPercent;
            $subtotal -= $onlineDiscountAmount;
            $this->onlineDiscountApplied = true;
        }

        //Coupon Discount
        if(
            isset($this->orderCouponDTO) &&
            !$this->orderCouponDTO->isCouponTypeSpecialShipping() &&
            !$this->orderCouponDTO->isCouponTypeFreeItem()
        ) {
            if($this->orderCouponDTO->isCouponTypePercent()) {
                $couponPercent = $this->orderCouponDTO->percent;
                $couponAmount = (($couponPercent / 100) * $subtotal);
            } else {
                $couponAmount = $this->orderCouponDTO->getAmountToApply();
            }
            $this->couponAmountAppliedToTotal = $couponAmount;
            $this->couponAppliedToTotal = true;
            $subtotal -= $couponAmount;
        }

        $total = $subtotal;
        return $total;
    }

    public function isValid() {

    }

}