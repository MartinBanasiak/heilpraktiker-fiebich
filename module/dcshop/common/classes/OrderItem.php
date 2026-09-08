<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\traits\hookableTrait;
use DynCom\dc\dcShop\interfaces\OrderableEntityInterface;
use DynCom\dc\dcShop\interfaces\OrderItemInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.07.2015
 * Time: 10:23
 */
class OrderItem implements OrderItemInterface
{

    use hookableTrait;

    protected $order;
    protected $item;

    /**
     * OrderItem constructor.
     * @param DefaultOrder $order
     * @param OrderableEntityInterface $item
     */
    public function __construct(DefaultOrder $order, OrderableEntityInterface $item) {
        $this->order = $order;
        $this->item = $item;
    }

    /**
     * @return DefaultOrder
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * @return string
     */
    public function getOrderID() {
        return $this->order->getId();
    }

    /**
     * @return OrderableEntityInterface
     */
    public function getItem() {
        return $this->item;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->item->getCompany();
    }

    /**
     * @return int
     */
    public function getOrderableType()
    {
        return $this->item->getOrderableType();
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->item->getIdentifier();
    }

    /**
     * @return string
     */
    public function getSubIdentifier()
    {
        return $this->item->getSubIdentifier();
    }

    /**
     * @return string
     */
    public function getUnitCode()
    {
        return $this->item->getUnitCode();
    }

    /**
     * @return float
     */
    public function getQuantity()
    {
        return $this->item->getQuantity();
    }

    /**
     * @param float $quantity
     */
    public function setQuantity($quantity) {
        $this->item->setQuantity($quantity);
        $this->updateHooks('quantityChanged',$this);
    }

    /**
     * @return float
     */
    public function getUnitPrice()
    {
        return $this->item->getUnitPrice();
    }

    /**
     * @return float
     */
    public function getLineAmount()
    {
        return $this->item->getLineAmount();
    }

    /**
     * @return mixed
     */
    public function getVATCode() {
        return $this->item->getVATCode();
    }

    /**
     * @return mixed
     */
    public function getVATAmount() {
        return $this->item->getVATAmount();
    }

    /**
     * @return mixed
     */
    public function getVATPercent() {
        return $this->item->getVATPercent();
    }

    /**
     * @return float|false
     */
    public function getWeight()
    {
        return $this->item->getWeight();
    }

    /**
     * @return bool
     */
    public function allowsInvoiceDiscount()
    {
        return $this->item->allowsInvoiceDiscount();
    }

    /**
     * @return mixed
     */
    public function getDescription() {
        return $this->item->getDescription();
    }

    /**
     * @return mixed
     */
    public function getMinQty()
    {
        return $this->item->getMinQty();
    }

    /**
     * @return mixed
     */
    public function getMaxQty()
    {
        return $this->item->getMaxQty();
    }

    /**
     * @return mixed
     */
    public function getQtyStep()
    {
        return $this->item->getQtyStep();
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->item->getCustomer();
    }

    /**
     * @param Customer $customer
     */
    public function setCustomer(Customer $customer)
    {
        $this->item->setCustomer($customer);
    }

    /**
     * @return mixed
     */
    public function getID() {
        return $this->item->getID();
    }


}