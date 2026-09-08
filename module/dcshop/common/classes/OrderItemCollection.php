<?php
namespace DynCom\dc\dcShop\classes;
use ArrayIterator;
use DynCom\dc\common\classes\Hook;
use DynCom\dc\common\interfaces\Observer;
use DynCom\dc\common\traits\hookableTrait;
use DynCom\dc\dcShop\interfaces\OrderableEntityInterface;
use DynCom\dc\dcShop\interfaces\OrderItemInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.07.2015
 * Time: 10:34
 */
class OrderItemCollection implements Observer, \IteratorAggregate, \Countable
{

    use hookableTrait;

    protected $order;
    protected $items = [];
    protected $total;
    protected $totalPerVAT = [];
    protected $totalPerVATPercent = [];
    protected $totalInvoiceDiscountAllowed;
    protected $totalInvoiceDiscountAllowedPerVAT = [];
    protected $totalInvoiceDiscountAllowedPerVATPercent = [];

    /**
     * OrderItemCollection constructor.
     * @param DefaultOrder $order
     */
    public function __construct(DefaultOrder $order) {
        $this->order = $order;
        Hook::registerEventListener($this,'OrderItem.quantityChanged');
    }

    /**
     * @param $eventName
     * @param $data
     */
    public function notify($eventName, $data)
    {
        if(
                ($data instanceof OrderItem)
            &&  (strpos($eventName,'quantityChanged') !== false)
        ) {
            $this->recalculateTotals();
        }

    }


    /**
     * @param OrderableEntityInterface $item
     * @return OrderItem|OrderableEntityInterface|OrderItemInterface
     */
    public function addItem(OrderableEntityInterface $item) {
        if($item instanceof OrderItemInterface) {
            $sameOrigin = ($item->getOrder() === $this->order);
            if(!$sameOrigin) {
                throw new \InvalidArgumentException(
                    "If an instance of OrderItem is passed, its Order must be the same as that of this Order Collection"
                );
            }
            $orderItem = $item;
        } else {
            $orderItem = new OrderItem($this->order,$item);
        }
        $class = get_class($orderItem->getItem());
        $id = $orderItem->getIdentifier();
        $key = $class . '-' . $id;
        $this->items[$key] = $item;
        $this->recalculateTotals();
        $this->updateHooks('itemAdded',$item);
        return $orderItem;
    }

    /**
     * @param OrderItem $item
     */
    public function removeItem(OrderItem $item) {
        $class = get_class($item->getItem());
        $id = $item->getIdentifier();
        $key = $class . '-' . $id;
        if(array_key_exists($key,$this->items)) {
            unset($this->items[$key]);
            $this->updateHooks('itemRemoved',$item);
        }
        $this->recalculateTotals();
    }

    public function recalculateTotals() {
        $total = 0.00;
        $totalPerVAT = [];
        $totalPerVATPercent = [];
        $totalInvoiceDiscountAllowed = 0.00;
        $totalInvoiceDiscountAllowedPerVAT = [];
        $totalInvoiceDiscountAllowedPerVATPercent = [];
        foreach($this->items as $item) {
            $VATCode = '';
            $VATPercent = 0.00;
            $lineAmnt = 0.00;
            $invDiscAllowed = false;
            if($item instanceof OrderableEntityInterface) {
                $VATCode = $item->getVATCode();
                $VATPercent = $item->getVATPercent();
                $lineAmnt = $item->getLineAmount();
                $invDiscAllowed = $item->allowsInvoiceDiscount();
            }
            if(
                    !array_key_exists($VATCode,$totalPerVAT)
                ||  !array_key_exists((string)$VATPercent,$totalPerVATPercent)
                ||  !is_float($totalPerVAT[$VATCode])
                ||  !is_float($totalPerVATPercent[(string)$VATPercent])
            ) {
                $totalPerVAT[$VATCode] = 0.00;
                $totalPerVATPercent[(string)$VATPercent] = 0.00;
            }
            if(
                !array_key_exists($VATCode,$totalInvoiceDiscountAllowedPerVAT)
                ||  !array_key_exists((string)$VATPercent,$totalInvoiceDiscountAllowedPerVATPercent)
                ||  !is_float($totalInvoiceDiscountAllowedPerVAT[$VATCode])
                ||  !is_float($totalInvoiceDiscountAllowedPerVATPercent[(string)$VATPercent])
            ) {
                $totalInvoiceDiscountAllowedPerVAT[$VATCode] = 0.00;
                $totalInvoiceDiscountAllowedPerVATPercent[(string)$VATPercent] = 0.00;
            }
            $totalPerVAT[$VATCode] += $lineAmnt;
            $totalPerVATPercent[(string)$VATPercent] += $lineAmnt;
            $total += $lineAmnt;
            if($invDiscAllowed) {
                $totalInvoiceDiscountAllowed += $lineAmnt;
                $totalInvoiceDiscountAllowedPerVAT[$VATCode] += $lineAmnt;
                $totalInvoiceDiscountAllowedPerVATPercent[(string)$VATPercent] += $lineAmnt;
            }
        }
        $this->total = $total;
        $this->totalPerVAT = $totalPerVAT;
        $this->totalPerVATPercent = $totalPerVATPercent;
    }

    /**
     * @return mixed
     */
    public function getTotal() {
        return $this->total;
    }

    /**
     * @return array
     */
    public function getTotalPerVATCode() {
        return $this->totalPerVAT;
    }

    /**
     * @return array
     */
    public function getTotalPerVATPercent() {
        return $this->totalPerVATPercent;
    }

    /**
     * @return mixed
     */
    public function getTotalInvoiceDiscountAllowed() {
        return $this->totalInvoiceDiscountAllowed;
    }

    /**
     * @return array
     */
    public function getTotalInvoiceDiscountAllowedPerVATCode() {
        return $this->totalInvoiceDiscountAllowedPerVAT;
    }

    /**
     * @return array
     */
    public function getTotalInvoiceDiscountAllowedPerVATPercent() {
        return $this->totalInvoiceDiscountAllowedPerVATPercent;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @return mixed
     */
    public function count() {
        $arrIt = new ArrayIterator($this->items);
        return $arrIt->count();
    }

    /**
     * @return mixed
     */
    public function getFirst() {
        return $this->items[0];
    }

}