<?php
namespace DynCom\dc\dcShop\classes;

use DynCom\dc\common\classes\Visitor;
use DynCom\dc\common\interfaces\Observer;
use DynCom\dc\common\traits\hookableTrait;
use DynCom\dc\dcShop\abstracts\DiscountBase;
use DynCom\dc\dcShop\interfaces\IVATManager;
use DynCom\dc\dcShop\interfaces\OrderableEntityInterface;
use DynCom\dc\dcShop\interfaces\UserBasket;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 06.10.2015
 * Time: 11:55
 */
class GenericUserBasket implements UserBasket, Observer
{

    use hookableTrait;

    const NOTICE_ITEM_ADDED = 'notice_item_added_to_basket';
    const NOTICE_ITEM_REMOVED = 'notice_item_removed_from_basket';
    const NOTICE_ITEM_QTY_CHANGED = 'notice_item_qty_changed_in_basket';

    const ERROR_CANNOT_CHANGE_ITEM_QTY = 'error_cannot_change_basket_qty';
    const ERROR_CANNOT_ADD_ITEM = 'error_cannot_add_item_to_basket';
    const ERROR_CANNOT_REMOVE_ITEM = 'error_cannot_remove_item_from_basket';


    /**
     * @var int
     */
    private $id;

    /**
     * @var boolean
     */
    private $mainBasket;

    /**
     * @var \SplDoublyLinkedList
     */
    private $items;

    /**
     * @var array
     */
    private $itemIndexByKey;


    /**
     * @var int
     */
    private $noOfPos;

    /**
     * @var float
     */
    private $basketTotal;

    /**
     * @var float
     */
    private $itemTotal;

    /**
     * @var float
     */
    private $basketTotalInvDiscAllowed;

    /**
     * @var array
     */
    private $vatAmountsPerVatCode;

    /**
     * @var float
     */
    private $sumVATAmounts;

    /**
     * @var float
     */
    private $totalQty;

    /**
     * @var array
     */
    private $appliedDiscounts;

    /**
     * @var IVATManager
     */
    private $VATManager;

    /**
     * @var Visitor
     */
    private $visitor;

    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    private $description;

    private $hash;

    private $lastChangedItemIndex;

    private $qtyProtectedIndexes;

    private $unitPriceProtectedIndexes;

    private $itemArr = [];

    private $invoiceDiscountAllowed;

    private $company;

    private $shopCode;

    private $shopLanguageCode;

    private $forcePersist = false;

    /**
     * @var integer
     */
    private $shippingClassFilter;
    /**
     * @var string
     */
    private $lastPersistedHash;


    /**
     * GenericUserBasket constructor.
     * @param IVATManager $VATManager
     * @param Visitor $visitor
     * @param string $lastPersistedHash
     * @param User|null $user
     * @param int $id
     * @param null $mainBasket
     */
    public function __construct(IVATManager $VATManager, Visitor $visitor, $lastPersistedHash, User $user = null, $id = 0, $mainBasket = null)
    {
        $this->VATManager = $VATManager;
        $this->items = new \SplDoublyLinkedList();
        $this->itemKeysByTypeIdentifierSubidentifier = [];
        $this->itemKeysByTypeIDVarCode = [];
        $this->noOfPos = 0;
        $this->basketTotal = 0.00;
        $this->sumVATAmounts = 0.00;
        $this->vatAmountsPerVatCode = [];
        $this->appliedDiscounts = [];
        $this->visitor = $visitor;
        $this->user = $user;
        $this->mainBasket = (boolean)$mainBasket;
        $this->id = (int)$id;
        $this->hash = '';
        $this->shippingClassFilter = '';
        $this->lastPersistedHash = $lastPersistedHash;
    }

    /**
     * @return int
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * @param $eventName
     * @param $data
     */
    public function notify($eventName, $data)
    {
        /*
        $matches = [];
        if(preg_match('#BasketEntity\.([^\.]+\.){0,1}quantityChanged#',$eventName,$matches) === 1) {
            //Basket quantity changed
            $idStr = $matches[1];
            $idStrArr = explode('|',$idStr,3);
            $type = filter_var($idStrArr[0],FILTER_SANITIZE_NUMBER_INT);
            $identifier = filter_var($idStrArr[1],FILTER_SANITIZE_STRING);
            $subidentifier = filter_var($idStrArr[2],FILTER_SANITIZE_STRING);
            if($this->hasItem($type,$identifier,$subidentifier)) {
                $this->recalculateValues();
            }

        }
        */
    }


    /**
     * @param BasketEntity $entity
     * @param bool $noEventTriggers
     */
    public function addItem(BasketEntity $entity, $noEventTriggers = false)
    {
        $qty = $entity->getQuantity();
        if ((float)$qty == 0.00) {
            return;
        }
        $customizationHash = $entity->getOrderableEntity()->getCustomizationHash();
        $key = $this->getKey($entity);
        if (array_key_exists($key, $this->itemIndexByKey)) {
            $index = $this->getItemIndexByKey($key);
            $item = $this->getEntityByIndex($index);
            $oldQty = $item->getQuantity();
            $newQty = (float)$oldQty + (float)$qty;
            $this->changeItemQtyByIndex($index, $newQty);
            return;
        }

        $lineNo = $this->getNextAvailableLineNo();
        $entity->setLineNo($lineNo);


        if (array_key_exists($lineNo, $this->itemArr)) {
            throw new \InvalidArgumentException("LineNo $lineNo already exists in basket.");
        }

        $key = $this->getKey($entity);
        $this->itemIndexByKey[$key] = (string)$lineNo;
        $this->itemArr[(int)$lineNo] = $entity;
        $this->recalculateValues();

        $eventName = 'afterSuccessfullAddToBasket';
        $data['notice_text_code'] = self::NOTICE_ITEM_ADDED;
        $data['item_no'] = $entity->getIdentifier();
        $data['current_item_id'] = $entity->getEntityID();
        $data['var_code'] = $entity->getSubIdentifier();
        $data['new_qty'] = $entity->getQuantity();
        $data['unit_price'] = $entity->getUnitPrice();
        $data['current_visitor_id'] = $this->getVisitorID();
        $data['current_user_id'] = $this->getUserID();
        $data['shop_type'] = $GLOBALS["shop"]["shop_typ"];
        if (isset($GLOBALS['shop_customer']['id'])) {
            $data['current_customer_id'] = $GLOBALS['shop_customer']['id'];
        }
        if (isset($GLOBALS['category']['id'])) {
            $data['current_category_id'] = $GLOBALS['category']['id'];
        }
        if (!$noEventTriggers) {
            $this->updateHooks($eventName, $data);
        }
    }

    /**
     * @param OrderableEntityInterface $entity
     * @return string
     */
    public function getKey(OrderableEntityInterface $entity)
    {
        return self::getKeyStatic($entity);
    }

    /**
     * @param OrderableEntityInterface $entity
     * @return string
     */
    public static function getKeyStatic(OrderableEntityInterface $entity)
    {
        $key = $entity->getOrderableType() . '|' . $entity->getIdentifier() . '|' . $entity->getSubIdentifier();
        if ($entity->isQtyProtected()) {
            $key .= '|QTY-PROT.TYPE:' . $entity->getQtySourceType() . '.ID:' . $entity->getQtySourceID();
        }
        if ($entity->isUnitPriceProtected()) {
            $key .= '|PRICE-PROT.TYPE:' . $entity->getPriceSourceType() . '.ID:' . $entity->getPriceSourceID();
        }
        $customizationHash = $entity->getCustomizationHash();
        if ($customizationHash) {
            $key .= '|CUSTOMIZATION:' . $customizationHash;
        }
        $link = $entity->getBasketEntityLink();
        if (is_array($link) && (int)$link['entity_link_type'] > 0) {
            $key .= '|LINKEDENTITY:' . $link['links_to_entity_key'] . '.TYPE:' . $link['entity_link_type'];
        }

        $greetingCardText = $entity->getGreetingCardText();
        if ($greetingCardText) {
            $key .= '|GREETINGCARDTEXT:' . $greetingCardText;
        }

        return $key;
    }

    /**
     * @param $key
     * @return mixed
     */
    private function getItemIndexByKey($key)
    {
        if (!array_key_exists($key, $this->itemIndexByKey)) {
            $trace = generateCallTrace();
            throw new \InvalidArgumentException('No item with key ' . $key . ' exists in this basket. TRACE: ' . $trace);
        }
        return $this->itemIndexByKey[$key];
    }

    /**
     * @param $index
     * @return BasketEntity
     */
    private function getEntityByIndex($index)
    {
        return $this->itemArr[(int)$index];
    }

    /**
     * @param $index
     * @param $newQty
     * @param bool $noEventTriggers
     */
    private function changeItemQtyByIndex($index, $newQty, $noEventTriggers = false)
    {
        if (array_key_exists($index, $this->qtyProtectedIndexes)) {
            throw new \DomainException('The qty of basket entity with line-no ' . $index . ' is protected.');
        }

        $entity = $this->getEntityByIndex($index);

        $this->checkAndHandleLinksAtItemQtyChange($entity, $newQty);

        $oldQty = $entity->getQuantity();
        if ($newQty <= 0) {
            $this->removeItemByIndex($index);
            return;
        }
        $entity->setQuantity($newQty);
        $newQty = $entity->getQuantity();

        $this->recalculateValues();

        $eventName = 'userFacedNotice';
        $data['notice_text_code'] = self::NOTICE_ITEM_QTY_CHANGED;
        $data['item_no'] = $entity->getIdentifier();
        $data['var_code'] = $entity->getSubidentifier();
        $data['old_qty'] = $oldQty;
        $data['new_qty'] = $newQty;

       $unitPrice = $entity->getUnitPrice();

        /*if($entity->getCustomizationHash() != "")
        {
            $item = $this->getItemByKey($entity->getIdentifier());
        }*/


        $data['unit_price'] = $unitPrice;
        $data['current_visitor_id'] = $this->getVisitorID();
        $data['current_user_id'] = $this->getUserID();
        $data['current_item_id'] = $entity->getEntityID();
        if (isset($GLOBALS['shop_customer']['id'])) {
            $data['current_customer_id'] = $GLOBALS['shop_customer']['id'];
        }
        if (isset($GLOBALS['category']['id'])) {
            $data['current_category_id'] = $GLOBALS['category']['id'];
        }
        $data['shop_type'] = $GLOBALS["shop"]["shop_typ"];
        //$this->updateHooks($eventName, $data);

        $eventName = 'afterSuccessfullyBasketQtyChange';
        if (!$noEventTriggers) {
            $this->updateHooks($eventName, $data);
        }


        $this->lastChangedItemIndex = $index;

    }

    /**
     * @param $index
     * @param bool $noEventTriggers
     */
    private function removeItemByIndex($index, $noEventTriggers = false)
    {
        if (array_key_exists($index, $this->qtyProtectedIndexes)) {
            throw new \DomainException('The qty of basket entity with line-no ' . $index . ' is protected.');
        }

        $entity = $this->getEntityByIndex($index);
        if (null !== $entity) {
            $newQty = 0;
            $this->checkAndHandleLinksAtItemQtyChange($entity, $newQty);
            $key = $this->getKey($entity);
            unset($this->itemIndexByKey[$key], $this->itemArr[(int)$index]);

            $this->recalculateValues();

            $eventName = 'afterSuccessfullRemoveFromBasket';
            $data['notice_text_code'] = self::NOTICE_ITEM_REMOVED;
            $data['item_no'] = $entity->getIdentifier();
            $data['var_code'] = $entity->getSubIdentifier();
            $data['current_visitor_id'] = $this->getVisitorID();
            $data['current_user_id'] = $this->getUserID();
            $data['current_item_id'] = $entity->getEntityID();
            $data['old_qty'] = $entity->getQuantity();
            $data['unit_price'] = $entity->getUnitPrice();
            $data['shop_type'] = $GLOBALS["shop"]["shop_typ"];
            if (isset($GLOBALS['shop_customer']['id'])) {
                $data['current_customer_id'] = $GLOBALS['shop_customer']['id'];
            }
            if (isset($GLOBALS['category']['id'])) {
                $data['current_category_id'] = $GLOBALS['category']['id'];
            }
            if (!$noEventTriggers) {
                $this->updateHooks($eventName, $data);
            }
        } else {
            throw new \InvalidArgumentException('no item for index ' . $index);
        }
    }

    /**
     * @param $customizationHash
     */
    private function removeCustomizationByHash($customizationHash)
    {
        $query = "DELETE FROM shop_user_basket_customize WHERE customization_hash = '" . $customizationHash . "'";
        @mysqli_query($GLOBALS["mysql_con"], $query);

    }

    public function recalculateValues()
    {
        $this->updateItemKeyIndexes();
        $lineNo = 10000;
        $protectedQtyLineNos = [];
        $protectedUnitPriceLineNos = [];
        $indexByKey = [];

        $noOfPos = 0;
        $totalQty = 0.00;
        $basketTotal = 0.00;
        $itemTotal = 0.00;
        $basketTotalInvDiscAllowed = 0.00;
        $totalVATPerVATGroup = [];
        $invoiceDiscountTotal = 0.00;
        $sumVATAmnts = 0.00;
        $invoiceDiscountAllowed = true;
        $maxShippingClassPriority = 0;
        $shippingClassFilter = '';
        $newItemArr = [];
        foreach ($this->itemArr as $item) {
            if ($item instanceof BasketEntity) {
                $noOfPos++;
                $itemKey = $this->getKey($item);
                $totalQty += $item->getQuantity();
                $lineAmount = $item->getLineAmount();
                $basketTotal += $lineAmount;
                $itemShippingClassAndPriority = $item->getShippingClassAndPriority();
                if ($itemShippingClassAndPriority['priority'] > $maxShippingClassPriority && $shippingClassFilter != $itemShippingClassAndPriority['class']) {
                    $maxShippingClassPriority = $itemShippingClassAndPriority['priority'];
                    $shippingClassFilter = $itemShippingClassAndPriority['class'];
                }
                //Line Nos and Status
                $oldLineNo = $item->getLineNo();
                if (array_key_exists($oldLineNo, $this->qtyProtectedIndexes)) {
                    $protectedQtyLineNos[$lineNo] = $lineNo;
                }
                if (array_key_exists($oldLineNo, $this->unitPriceProtectedIndexes)) {
                    $protectedUnitPriceLineNos[$lineNo] = $lineNo;
                }
                $item->setLineNo($lineNo);
                $newItemArr[$lineNo] = $item;
                $indexByKey[$itemKey] = $item->getLineNo();

                if ($item->allowsInvoiceDiscount()) {
                    $basketTotalInvDiscAllowed += $lineAmount;
                }

                //VAT
                $itemVATGroupCode = $item->getVATCode();
                $itemVATAmount = 0.00;
                if ($item->getVATAmount() > 0) {
                    $itemVATAmount = (float)$item->getVATAmount();
                }
                if ($lineAmount > 0) {
                    if (!array_key_exists($itemVATGroupCode, $totalVATPerVATGroup)) {
                        $totalVATPerVATGroup[$itemVATGroupCode] = $itemVATAmount;
                        $sumVATAmnts += $itemVATAmount;
                    } else {
                        $totalVATPerVATGroup[$itemVATGroupCode] += $itemVATAmount;
                        $sumVATAmnts += $itemVATAmount;
                    }
                    if (!$item->allowsInvoiceDiscount()) {
                        $invoiceDiscountAllowed = false;
                    }
                }

                $lineNo += 10000;
            }
        }
        $this->itemArr = $newItemArr;
        $this->qtyProtectedIndexes = $protectedQtyLineNos;
        $this->unitPriceProtectedIndexes = $protectedUnitPriceLineNos;
        $this->itemIndexByKey = $indexByKey;
        foreach ($this->appliedDiscounts as $appliedInvoiceDiscount) {
            if ($appliedInvoiceDiscount instanceof AppliedDiscount) {
                $invoiceDiscountTotal += $appliedInvoiceDiscount->getDiscountedAmount();
            }
        }
        $this->itemTotal = $basketTotal;
        if ($invoiceDiscountTotal > 0) {
            $basketTotal -= $invoiceDiscountTotal;
            foreach ($totalVATPerVATGroup as $vatCode => $vatAmnt) {
                $fraction = 0;
                if ($sumVATAmnts && $vatAmnt) {
                    $fraction = ($sumVATAmnts / $vatAmnt);
                }
                $vatPercent = $this->VATManager->getVATPercentForProdPostingGroup($vatCode);
                $amnt = $fraction * $this->VATManager->getTaxAmountForShopVATSetting($invoiceDiscountTotal, $vatPercent);
                //Adjust VAT-Amount per group for Invoice Discount in a proportional fashion.
                $totalVATPerVATGroup[$vatCode] -= $amnt;
                $sumVATAmnts = $sumVATAmnts - $amnt;
            }
        }
        $this->noOfPos = count($this->itemArr);
        $this->totalQty = $totalQty;
        $this->basketTotal = $basketTotal;
        $this->basketTotalInvDiscAllowed = $basketTotalInvDiscAllowed;
        $this->vatAmountsPerVatCode = $totalVATPerVATGroup;
        $this->sumVATAmounts = (float)$sumVATAmnts;
        $this->invoiceDiscountAllowed = $invoiceDiscountAllowed;
        $this->shippingClassFilter = $shippingClassFilter;
        $this->updateHash();
    }

    private function updateHash()
    {
        $str = '';
        if (count($this->itemArr) > 0) {
            foreach ($this->itemArr as $item) {
                if ($item instanceof BasketEntity) {
                    $str .= $this->getKey($item) . '|' . $item->getQuantity() . '|' . $item->getUnitPrice();
                }
            }
        }
        if (count($this->appliedDiscounts) > 0) {
            $str .= ';INV-DISCOUNTS:';
            foreach ($this->appliedDiscounts as $discount) {
                if ($discount instanceof AppliedDiscount) {
                    $str .= $discount->getSourceType() . '|' . $discount->getSourceID() . '|' . $discount->getDiscountedAmount();
                }
            }
        }
        $hash = md5($str);
        $this->hash = $hash;
    }

    /**
     * @param $key
     */
    public function removeItemByKey($key,$noEventTriggers = false)
    {
        $customizationHash = $this->getItemByKey($key)->getCustomizationHash();
        $this->removeCustomizationByHash($customizationHash);
        $index = $this->getItemIndexByKey($key);
        $this->removeItemByIndex($index,$noEventTriggers);
    }

    /**
     * @param $key
     * @param $newQty
     */
    public function changeItemQtyByKey($key, $newQty)
    {
        if ($newQty !== $this->getItemQtyByKey($key)) {
            $newQty = (float)$newQty;
            $index = $this->getItemIndexByKey($key);
            $this->changeItemQtyByIndex($index, $newQty);
        }
    }

    /**
     * @return float
     */
    public function getTotalQty()
    {
        return (float)$this->totalQty;
    }

    /**
     * @return int
     */
    public function getTotalNoOfPos()
    {
        return (int)$this->noOfPos;
    }

    /**
     * @return float
     */
    public function getBasketTotal()
    {
        return (float)$this->basketTotal;
    }

    /**
     * @param $vatGroupCode
     * @return float
     */
    public function getVATAmountsForVATGroup($vatGroupCode)
    {
        if (!array_key_exists($vatGroupCode, $this->vatAmountsPerVatCode)) {
            return 0.00;
        }
        return (float)$this->vatAmountsPerVatCode[$vatGroupCode];
    }

    /**
     * @return array
     */
    public function getVATAmountsPerVATGroup()
    {
        return $this->vatAmountsPerVatCode;
    }

    /**
     * @return array
     */
    public function getVATAmountsPerVATPercent()
    {
        $perCode = $this->getVATAmountsPerVATGroup();
        $arr = [];
        $i = 0;
        foreach ($perCode as $key => $sumAmnts) {
            $percent = (float)$this->VATManager->getVATPercentForProdPostingGroup($key);
            $percentStr = (string)$percent;
            $arr[$percentStr] = $sumAmnts;
        }
        return $arr;
    }

    /**
     * @return float
     */
    public function getSumVATAmounts()
    {
        return $this->sumVATAmounts;
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasItemForKey($key)
    {
        $itemExists = array_key_exists($key, $this->itemIndexByKey);
        return $itemExists;
    }

    /**
     * @param $key
     * @return float
     */
    public function getItemQtyByKey($key)
    {
        $item = $this->getItemByKey($key);
        return $item->getQuantity();
    }

    /**
     * @param $key
     * @return BasketEntity
     */
    public function getItemByKey($key)
    {
        $index = $this->getItemIndexByKey($key);
        return $this->getEntityByIndex($index);
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->itemArr);
    }

    /**
     * @param GenericInvoiceDiscount $discount
     */
    public function applyInvoiceDiscount(GenericInvoiceDiscount $discount)
    {
        $discountSourceType = $discount->getSourceType();
        $key = $discountSourceType . '|' . $discount->getSourceID();
        if (($discountSourceType !== DiscountBase::DISCOUNT_SOURCE_TYPE_RULE) && (array_key_exists($key, $this->appliedDiscounts))) {
            return;
        }
        if ($discountSourceType === DiscountBase::DISCOUNT_SOURCE_TYPE_INVOICE_DISCOUNT && !($this->invoiceDiscountAllowed)) {
            return;
        }
        $appliedDiscount = new AppliedDiscount($this->basketTotal, $discount);
        $this->appliedDiscounts[$key] = $appliedDiscount;
        $this->recalculateValues();
    }

    /**
     * @param GenericInvoiceDiscount $discount
     */
    public function removeInvoiceDiscount(GenericInvoiceDiscount $discount)
    {
        $key = $discount->getSourceType() . '|' . $discount->getSourceID();
        if (!array_key_exists($key, $this->appliedDiscounts)) {
            throw new \InvalidArgumentException('This discount is not among the discounts applied to this basket.');
        }
        unset($this->appliedDiscounts[$key]);
        $this->recalculateValues();
    }

    /**
     * @return mixed
     */
    public function getVisitorID()
    {
        return (int)$this->visitor->getID();
    }

    /**
     * @return int|null
     */
    public function getUserID()
    {
        if ($this->user !== null) {
            return (int)$this->user->getID();
        }
        return 0;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $description
     */
    public function setDescription($description)
    {
        if (mb_strlen($description) > 80) {
            throw new \InvalidArgumentException('Description must be below 80 characters.');
        }
        $this->description = $description;
    }

    /**
     * @return float
     */
    public function getBasketTotalGross()
    {
        if ($this->VATManager->isOutputIncludingVAT()) {
            return (float)$this->basketTotal;
        } else {
            return ((float)$this->basketTotal + (float)$this->sumVATAmounts);
        }
    }

    /**
     * @return float
     */
    public function getBasketTotalNet()
    {
        if ($this->VATManager->isOutputIncludingVAT()) {
            return (float)((float)$this->basketTotal - (float)$this->sumVATAmounts);
        } else {
            return (float)$this->basketTotal;
        }
    }

    /**
     * @return bool
     */
    public function isMainBasket()
    {
        return (bool)$this->mainBasket;
    }

    /**
     * @return array
     */
    public function getAppliedInvoiceDiscounts()
    {
        return $this->appliedDiscounts;
    }

    /**
     * @param GenericInvoiceDiscount $discount
     * @return bool
     */
    public function isInvoiceDiscountApplied(GenericInvoiceDiscount $discount)
    {
        $key = $discount->getSourceType() . '|' . $discount->getSourceID();
        return array_key_exists($key, $this->appliedDiscounts);
    }

    /**
     * @param GenericLineDiscount $discount
     * @param $key
     * @return mixed
     */
    public function isLineDiscountAppliedToItemByKey(GenericLineDiscount $discount, $key)
    {
        $item = $this->getItemByKey($key);
        return $item->isDiscountApplied($discount);
    }

    /**
     * @param $key
     * @param $priceSourceType
     * @param $priceSourceID
     * @return bool
     */
    public function hasEntityKeyPriceSource($key, $priceSourceType, $priceSourceID)
    {
        $item = $this->getItemByKey($key);
        return (($item->getPriceSourceType() === $priceSourceType) && ($item->getPriceSourceID() === $priceSourceID));
    }

    /**
     * @param $key
     * @param $creatorType
     * @param $creatorID
     * @return bool
     */
    public function isEntityKeyCreatedBy($key, $creatorType, $creatorID)
    {
        $item = $this->getItemByKey($key);
        return (($item->getCreationSourceType() === $creatorType) && ($item->getCreationSourceID() === $creatorID));
    }

    /**
     * @param $key
     * @param $priceSourceType
     * @param $priceSourceID
     */
    public function setEntityKeyPriceSource($key, $priceSourceType, $priceSourceID)
    {
        $item = $this->getItemByKey($key);
        $item->setPriceSourceType($priceSourceType);
        $item->setPriceSourceID($priceSourceID);
    }

    /**
     * @param $key
     * @param $creatorType
     * @param $creatorID
     */
    public function setEntityKeyCreatedBy($key, $creatorType, $creatorID)
    {
        $item = $this->getItemByKey($key);
        $item->setCreationSourceType($creatorType);
        $item->setCreationSourceID($creatorID);
    }

    public function reset()
    {
        $itemsToDelete = [];
        $i = 0;
        foreach ($this as $entity) {
            if ($entity instanceof BasketEntity) {
                $itemsToDelete[$i]['index'] = $entity->getLineNo();
                $itemsToDelete[$i]['key'] = $this->getKey($entity);
                $i++;
            }
        }
        foreach ($itemsToDelete as $item) {
            unset($this->itemIndexByKey[$item['key']], $this->itemArr[$item['index']]);
            //$this->items->offsetUnset($item['index']);
        }
        $this->appliedDiscounts = [];
        $this->recalculateValues();
        $this->updateHash();
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @return mixed
     */
    public function getNextAvailableLineNo()
    {
        $prevMaxLineNo = max(array_keys($this->itemArr));
        $newLineNo = $prevMaxLineNo + 10000;
        return $newLineNo;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->itemArr);
    }


    /**
     * @param GenericLineDiscount $discount
     * @param $key
     */
    public function applyLineDiscountByKey(GenericLineDiscount $discount, $key)
    {
        $index = $this->getItemIndexByKey($key);
        if (array_key_exists($index, $this->unitPriceProtectedIndexes)) {
            throw new \DomainException('The price of basket entity with line-no ' . $index . ' is protected.');
        }
        $item = $this->getEntityByIndex($index);
        if ($item instanceof BasketEntity) {
            $item->applyDiscount($discount, $this->VATManager);
            $this->recalculateValues();
        }
    }

    /**
     * @param GenericLineDiscount $discount
     * @param $key
     */
    public function removeLineDiscountByKey(GenericLineDiscount $discount, $key)
    {
        $index = $this->getItemIndexByKey($key);
        if (array_key_exists($index, $this->unitPriceProtectedIndexes)) {
            throw new \DomainException('The price of basket entity with line-no ' . $index . ' is protected.');
        }
        $item = $this->getEntityByIndex($index);
        if ($item instanceof BasketEntity) {
            $item->removeDiscount($discount, $this->VATManager);
            $this->recalculateValues();
        }
    }


    /**
     * @param $key
     * @param $priceSourceType
     * @param $priceSourceID
     * @return bool
     */
    public function hasEntityPriceSourceByKey($key, $priceSourceType, $priceSourceID)
    {
        $item = $this->getItemByKey($key);
        return (($item->getPriceSourceType() === $priceSourceType) && ($item->getPriceSourceID() === $priceSourceID));
    }

    /**
     * @param $key
     * @param $priceSourceType
     * @param $priceSourceID
     */
    public function setEntityPriceSourceByKey($key, $priceSourceType, $priceSourceID)
    {
        $item = $this->getItemByKey($key);
        $item->setPriceSourceType($priceSourceType);
        $item->setPriceSourceID($priceSourceID);
    }

    /**
     * @return BasketEntity
     */
    public function getLastChangedItem()
    {
        $index = $this->lastChangedItemIndex;
        return $this->getEntityByIndex($index);
    }

    /**
     * @param GenericLineDiscount $discount
     * @param $index
     */
    private function applyLineDiscountByIndex(GenericLineDiscount $discount, $index)
    {
        $entity = $this->getEntityByIndex($index);
        $entity->applyDiscount($discount, $this->VATManager);
        $this->recalculateValues();
    }

    /**
     * @param GenericLineDiscount $discount
     * @param $index
     */
    private function removeLineDiscountByIndex(GenericLineDiscount $discount, $index)
    {
        $entity = $this->getEntityByIndex($index);
        $entity->applyDiscount($discount, $this->VATManager);
        $entity->removeDiscount($discount, $this->VATManager);
        $this->recalculateValues();
    }

    /**
     * @param $id
     */
    public function removeItemByLineID($id)
    {
        $id = (int)$id;
        $index = 0;
        $customizationHash = '';
        foreach ($this as $item) {
            if ($item instanceof BasketEntity) {
                $dbID = $item->getDBID();
                if ($dbID === $id) {
                    $index = $item->getLineNo();
                    $customizationHash = $item->getCustomizationHash();
                }
            }
        }
        $this->removeCustomizationByHash($customizationHash);
        $this->removeItemByIndex($index);
    }

    /**
     * @param $lineNo
     */
    public function removeItemByLineNo($lineNo)
    {
        // TODO: Implement removeItemByLineNo() method.
    }

    /**
     * @param $lineNo
     * @return BasketEntity
     */
    public function getItemByLineNo($lineNo)
    {
        if (!array_key_exists($lineNo, $this->itemArr)) {
            throw new \InvalidArgumentException('No item with line no \'' . $lineNo . '\' is present in this basket.');
        }
        return $this->itemArr[$lineNo];
    }

    /**
     * @param $key
     * @param $accessibilty
     * @param $noEventTriggers
     * @return string
     */
    public function setItemQtyAccessibilityByKey($key, $accessibilty, $noEventTriggers = false)
    {
        $newAccessibility = (bool)$accessibilty;
        if (!array_key_exists($key, $this->itemIndexByKey)) {
            throw new \InvalidArgumentException('No item with key \'' . $key . '\' is present in this basket.');
        }
        $itemIndex = $this->getItemIndexByKey($key);
        $item = $this->getItemByLineNo($itemIndex);
        if ($newAccessibility == !$item->isQtyProtected()) {
            return $key;
        }
        $oldKey = $this->getKey($item);
        if (array_key_exists($itemIndex, $this->qtyProtectedIndexes)) {
            unset($this->qtyProtectedIndexes[$itemIndex]);
        }
        $itemClone = clone $item;
        $itemClone->setQtyAccessibility($newAccessibility);
        $this->removeItemByKey($oldKey,$noEventTriggers);
        $newKey = $this->getKey($itemClone);
        $this->addItem($itemClone);
        return $newKey;
    }

    /**
     * @param $key
     * @param $accessibilty
     * @return string
     */
    public function setItemUnitPriceAccessibilityByKey($key, $accessibilty)
    {

        $newAccessibility = (bool)$accessibilty;
        if (!array_key_exists($key, $this->itemIndexByKey)) {
            throw new \InvalidArgumentException('No item with key \'' . $key . '\' is present in this basket.');
        }
        $itemIndex = $this->getItemIndexByKey($key);
        $item = $this->getItemByLineNo($itemIndex);
        if ($newAccessibility === !$item->isUnitPriceProtected()) {
            return $key;
        }
        $oldKey = $this->getKey($item);
        if (array_key_exists($itemIndex, $this->unitPriceProtectedIndexes)) {
            unset($this->unitPriceProtectedIndexes[$itemIndex]);
        }
        if (array_key_exists($itemIndex, $this->qtyProtectedIndexes)) {
            unset($this->qtyProtectedIndexes[$itemIndex]);
        }
        $itemClone = clone $item;
        $itemClone->setUnitPriceAccessibility($newAccessibility);
        $this->removeItemByKey($oldKey);
        $newKey = $this->getKey($itemClone);
        $this->addItem($itemClone);
        return $newKey;
    }

    /**
     * @return float
     */
    public function getInvoiceDiscountAmount()
    {
        $val = 0.00;
        foreach ($this->appliedDiscounts as $appliedDiscount) {
            if ($appliedDiscount instanceof AppliedDiscount) {
                $val += $appliedDiscount->getDiscountedAmount();
            }
        }
        return $val;
    }

    /**
     * @return array
     */
    public function getItemVATData()
    {
        $arr = [];
        foreach ($this->itemArr as $item) {
            if ($item instanceof BasketEntity) {
                $lineAmnt = $item->getLineAmount();
                $VATCode = $item->getVATCode();
                $VATPercent = $item->getVATPercent();
                $VATAmount = $item->getVATAmount();
                if (!array_key_exists($VATCode, $arr)) {
                    $arr[$VATCode] = [
                        'total_amount' => $lineAmnt,
                        'vat_group' => $VATCode,
                        'vat_percent' => $VATPercent,
                        'vat_amount' => $VATAmount
                    ];
                } else {
                    $arr[$VATCode]['total_amount'] += $lineAmnt;
                    $arr[$VATCode]['vat_amount'] += $VATAmount;
                }
            }
        }
        uasort($arr, ['GenericUserBasket', 'vatArrCmp']);
        return $arr;
    }

    /**
     * @param $a
     * @param $b
     * @return int
     */
    private static function vatArrCmp($a, $b)
    {
        if ($a['vat_percent'] == $b['vat_percent']) {
            return 0;
        }
        return ($a['vat_percent'] < $b['vat_percent']) ? -1 : 1;
    }

    /**
     * @param $identifier
     * @param $subidentifier
     * @return int
     */
    public function getQtyOfAllPositionsForItem($identifier, $subidentifier)
    {
        $totalQty = 0;
        foreach ($this as $entity) {
            $entityIdentifier = $entity->getIdentifier();
            $entitySubidentifier = $entity->getSubidentifier();
            if ($entityIdentifier === $identifier && $entitySubidentifier === $subidentifier) {
                $totalQty += $entity->getQuantity();
            }
        }
        return $totalQty;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return mixed
     */
    public function getShopCode()
    {
        return $this->shopCode;
    }

    /**
     * @return mixed
     */
    public function getShopLanguageCode()
    {
        return $this->shopLanguageCode;
    }

    /**
     * @param $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @param $shopCode
     */
    public function setShopCode($shopCode)
    {
        $this->shopCode = $shopCode;
    }

    /**
     * @param $shopLanguageCode
     */
    public function setShopLanguageCode($shopLanguageCode)
    {
        $this->shopLanguageCode = $shopLanguageCode;
    }

    /**
     * @return float
     */
    public function getTotalAmntInvoiceDiscountAllowed()
    {
        return $this->basketTotalInvDiscAllowed;
    }

    private function updateItemKeyIndexes()
    {
        foreach ($this as $basketItem) {
            if ($basketItem instanceof BasketEntity) {
                $lineNo = $basketItem->getLineNo();
                $storedKey = array_search($lineNo, $this->itemIndexByKey, true);
                $actualKey = $this->getKey($basketItem->getOrderableEntity());
                if ($storedKey !== $actualKey) {
                    if (array_key_exists($actualKey, $this->itemIndexByKey)) {
                        unset ($this->itemIndexByKey[$actualKey]);
                    }
                    unset($this->itemIndexByKey[$storedKey]);
                    $this->itemIndexByKey[$actualKey] = $lineNo;
                }
            }
        }
    }

    /**
     * @param BasketEntity $itemToBeChanged
     * @param $newQty
     */
    private function checkAndHandleLinksAtItemQtyChange(BasketEntity $itemToBeChanged, &$newQty)
    {
        $newQty = (float)$newQty;
        //check for linked gift-wrapping
        $wrappingEntities = $this->getLinkedEntitiesForEntity($itemToBeChanged, OrderableEntityInterface::BASKET_ENTITY_LINK_TYPE_WRAPPING_TO_ITEM);
        /**
         * Can only contain one item
         * @var $wrappingEntity BasketEntity
         */
        //Reduce quantity of wrapping if it is greater than the new quantity

        foreach ($wrappingEntities[OrderableEntityInterface::BASKET_ENTITY_LINK_TYPE_WRAPPING_TO_ITEM] as $wrappingEntity) {

            $wrappingEntitiesLoop = $this->getLinkedEntitiesForEntity($itemToBeChanged, OrderableEntityInterface::BASKET_ENTITY_LINK_TYPE_WRAPPING_TO_ITEM);
            $quantitySum = 0;
            foreach ($wrappingEntitiesLoop[OrderableEntityInterface::BASKET_ENTITY_LINK_TYPE_WRAPPING_TO_ITEM] as $wrappingEntityLoop) {
                $quantitySum = $quantitySum + $wrappingEntityLoop->getQuantity();
            }

            if ($quantitySum > $newQty) {
                $diffrenceValue = $quantitySum - $newQty;
                if ($diffrenceValue > $wrappingEntity->getQuantity()) {
                    $this->changeItemQtyByKey($this->getKey($wrappingEntity), 0);
                } else {
                    $diffrenceValue = $wrappingEntity->getQuantity() - $diffrenceValue;
                    $this->changeItemQtyByKey($this->getKey($wrappingEntity), $diffrenceValue);
                }
            }
        }

        //check if is gift-wrapping
        $linkData = $itemToBeChanged->getBasketEntityLink();
        $linksToEntityKey = $linkData['links_to_entity_key'];
        $linkType = $linkData['entity_link_type'];
        if ($linkType === OrderableEntityInterface::BASKET_ENTITY_LINK_TYPE_WRAPPING_TO_ITEM) {
            $linkedItemQty = $this->getItemQtyByKey($linksToEntityKey);
            if ($linkedItemQty < $newQty) {
                $newQty = $linkedItemQty;
            }
        }
    }

    /**
     * @param BasketEntity $entity
     * @param null $typeSpecification
     * @return array
     */
    private function getLinkedEntitiesForEntity(BasketEntity $entity, $typeSpecification = null)
    {
        $parentEntityKey = $this->getKey($entity);
        $foundKeys = [];
        $entities = [];
        /**
         * @var $iteratedEntity BasketEntity
         */
        foreach ($this as $iteratedEntity) {
            $entityKey = $this->getKey($iteratedEntity);
            $linkData = $iteratedEntity->getBasketEntityLink();
            $iteratedEntityKey = $linkData['links_to_entity_key'];
            $type = $linkData['entity_link_type'];
            if ($parentEntityKey === $iteratedEntityKey && !in_array($entityKey, $foundKeys, true)) {
                $foundKeys[] = $entityKey;
                if ($typeSpecification && ($type === $typeSpecification)) {
                    $entities[$type][] = $iteratedEntity;
                }
            }
        }
        return $entities;
    }

    public function setMinMaxQuantitiesForLinkedItems()
    {
        /**
         * @var $iteratedEntity BasketEntity
         */
        foreach ($this as $iteratedEntity) {
            $linkData = $iteratedEntity->getBasketEntityLink();
            $key = $linkData['links_to_entity_key'];
            if ($key) {
                $linkedItemQty = $this->getItemQtyByKey($key);
                $iteratedEntity->setMaxQty($linkedItemQty);
            }
        }
    }

    /**
     * @param $itemKey
     * @return float
     */
    public function getNoOfGiftWrappingsForItemKey($itemKey)
    {
        $no = 0.00;
        /**
         * @var $basketEntity BasketEntity
         */
        foreach ($this as $basketEntity) {
            $linkData = $basketEntity->getBasketEntityLink();
            if ($linkData['links_to_entity_key'] === $itemKey && (int)$linkData['entity_link_type'] == 1) {
                $no += (float)$basketEntity->getQuantity();
            }
        }
        return $no;
    }

    /**
     * @return float
     */
    public function getSumItemWeight()
    {
        /**
         * @var $basketEntity BasketEntity
         */
        $weightSum = 0.00;
        foreach ($this as $basketEntity) {
            $weightSum += (float)$basketEntity->getWeight();
        }
        return $weightSum;
    }

    /**
     * @return float
     */
    public function getBasketItemTotal()
    {
        return $this->itemTotal;
    }

    public function setForcePersist()
    {
        $this->forcePersist = true;
    }

    /**
     * @return bool
     */
    public function forcePersist()
    {
        return $this->forcePersist;
    }

    /**
     * @return int
     */
    public function getShippingClassFilter()
    {
        return $this->shippingClassFilter;
    }

    public function getOrderableEntityCollection()
    {
        $col = new OrderableEntityCollection();
        foreach ($this as $basketEntity) {
            $col->addOrderableEntity($basketEntity);
        }
        return $col;
    }

    public function getLastPersistedHash()
    {
        return $this->lastPersistedHash;
    }



}