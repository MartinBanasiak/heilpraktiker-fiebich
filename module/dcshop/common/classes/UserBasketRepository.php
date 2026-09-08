<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\traits\hookableTrait;
use DynCom\dc\common\traits\reflectionIDSetter;
use DynCom\dc\dcShop\abstracts\DiscountBase;
use DynCom\dc\dcShop\interfaces\ItemAvailabilityProvider;
use DynCom\dc\dcShop\interfaces\UserBasket;
use DynCom\dc\dcShop\interfaces\WebshopItemOrderabilityService;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 06.10.2015
 * Time: 11:28
 */
class UserBasketRepository
{
    use reflectionIDSetter,hookableTrait;

    
    public const HEADER_TABLE = 'shop_user_basket_header_new';
    public const LINE_TABLE = 'shop_user_basket_line_new';
    public const VALUE_SOURCE_TABLE = 'shop_user_basket_value_source_new';

    private $db;
    private $webshopItemRepository;
    private $webshopItemVariantRepository;
    private $advancedPriceProvider;
    private $VATManager;
    private $itemBuilder;

    private $currUserMainBasket;
    /**
     * @var ItemAvailabilityProvider
     */
    private $availabilityProvider;

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * @var WebshopItemOrderabilityService
     */
    private $itemOrderabilityService;
    /**
     * @var CouponLineRepository
     */
    private $couponLineRepository;

    /**
     * UserBasketRepository constructor.
     * @param PDOQueryWrapper $PDOQueryWrapper
     * @param WebshopItemRepository $webshopItemRepository
     * @param WebshopItemVariantRepository $webshopItemVariantRepository
     * @param AdvancedPriceProvider $advancedPriceProvider
     * @param ItemAvailabilityProvider $availabilityProvider
     * @param WebshopItemBuilder $itemBuilder
     * @param CustomerRepository $customerRepository
     * @param WebshopItemOrderabilityService $itemOrderabilityService
     * @param CouponLineRepository $couponLineRepository
     */
    public function __construct(
        PDOQueryWrapper $PDOQueryWrapper, WebshopItemRepository $webshopItemRepository, WebshopItemVariantRepository $webshopItemVariantRepository, AdvancedPriceProvider $advancedPriceProvider, ItemAvailabilityProvider $availabilityProvider, WebshopItemBuilder $itemBuilder, CustomerRepository $customerRepository, WebshopItemOrderabilityService $itemOrderabilityService, CouponLineRepository $couponLineRepository
    )
    {
        $this->db = $PDOQueryWrapper;
        $this->webshopItemRepository = $webshopItemRepository;
        $this->webshopItemVariantRepository = $webshopItemVariantRepository;
        $this->advancedPriceProvider = $advancedPriceProvider;
        $this->VATManager = $advancedPriceProvider->getVATManager();
        $this->itemBuilder = $itemBuilder;
        $this->customerRepository = $customerRepository;
        $this->itemOrderabilityService = $itemOrderabilityService;
        $this->couponLineRepository = $couponLineRepository;
    }



    /**
     * @param CurrShopConfiguration $configuration
     * @param bool $cache
     * @return GenericUserBasket|UserBasket
     * @throws \ErrorException
     */
    public function getCurrUserMainBasket(CurrShopConfiguration $configuration, $cache = true) {

        if($this->currUserMainBasket instanceof UserBasket && $cache) {
            return $this->currUserMainBasket;
        }

        $basket = $this->getMainBasketHeaderForVisitorID($configuration);
        if ($basket->getID() == 0) {
            $basket->setCompany($configuration->getCompany());
            $basket->setShopCode($configuration->getShopCode());
            $basket->setShopLanguageCode($configuration->getShopLanguageCode());
            return $basket;
        }
        $hasNonExistentItems = false;
        $basketLines = $this->getBasketEntitiesFromDB($basket->getID(),$hasNonExistentItems);
        if ($hasNonExistentItems) {
            $basket->forcePersist();
        }

        $user = $configuration->getUser();
        $customerWithPermissionGroups = $this->customerRepository->getCustomerWithPermissionGroupsForCurrUserAndLangCode($user,$configuration->getUseCustomersFromShopCode());


        foreach($basketLines as $basketLine) {

            if(!($basketLine instanceof BasketEntity)) {
                throw new \ErrorException('basketLine is not an instance of BasketEntity.');
            }

            $orderableEntity = $basketLine->getOrderableEntity();
            $errorTextCode = '';
            $isOrderable = $this->itemOrderabilityService->isWebshopItemOrderable($user,$customerWithPermissionGroups,$orderableEntity,$errorTextCode);
            if (($orderableEntity->getOrderableType() !== WebshopItemOrderableEntityDecorator::ORDERABLE_TYPE_ITEM) || $isOrderable) {
                $basket->addItem($basketLine,true);
            } elseif ($errorTextCode) {
                //echo "addtobasketerror";
                $eventName = AddToBasketErrorListener::EVENT_NAME;
                $data['error_text_code'] = $errorTextCode;
                $data['item_no'] = $orderableEntity->getIdentifier();
                //echo "update hooks with eventname $eventName";
                $this->updateHooks($eventName, $data);
            }
        }
        $invoiceDiscounts = $this->getAppliedInvoiceDiscountsForHeaderID($basket->getID());
        foreach($invoiceDiscounts as $invoiceDiscount) {
            if(!($invoiceDiscount instanceof GenericInvoiceDiscount)) {
                throw new \ErrorException('invoiceDiscount is not an instance of GenericInvoiceDiscount.');
            }
            $basket->applyInvoiceDiscount($invoiceDiscount);
        }
        $this->currUserMainBasket = $basket;
        $this->currUserMainBasket->setMinMaxQuantitiesForLinkedItems();
        return $basket;
    }


    /**
     * @param UserBasket $userBasket
     * @return UserBasket
     * @throws \Exception
     */
    public function saveUserBasket(UserBasket $userBasket)
    {
        $this->db->startTransaction();
        try {
            $userBasket = $this->saveUserBasketHeader($userBasket);
            $headerID = $userBasket->getID();
            $this->deleteAllValueSourceLinesForBasket($headerID);
            //$this->deleteAllLinesForHeader($headerID);
            $userBasket = $this->saveUserBasketHeaderValueSource($userBasket);
            $userBasket = $this->saveUserBasketLines($userBasket);
            $userBasket = $this->saveUserBasketLineValueSources($userBasket);
        } catch(\Exception $e) {
            $this->db->rollbackTransaction();
            throw $e;
        }
        $this->db->commitTransaction();
        return $userBasket;
    }

    /**
     * @param UserBasket $userBasket
     * @return UserBasket
     */
    private function saveUserBasketHeader(UserBasket $userBasket)
    {
        $header = $this->getHeaderArrayFromBasket($userBasket);
        $initID = $userBasket->getID();
        $updateOrInsert = ($initID > 0) ? 'update' : 'insert';
        $headerQuery = $this->db->getUpdateInsertQueryFromArray($header,self::HEADER_TABLE,$updateOrInsert);

        if(!$this->db->setQuery($headerQuery)->doQuery()) {
            throw new \ErrorException('Query was not successful. Query: ' . $headerQuery . ' ErrorMsg: ' . $this->db->getErrorMessage());
        }
        $headerID = ($initID > 0) ? $initID : $this->db->getLastInsertId();
        $this->setID($userBasket,$headerID);
        return $userBasket;
    }

    /**
     * @param UserBasket $userBasket
     * @return UserBasket
     */
    private function saveUserBasketHeaderValueSource(UserBasket $userBasket)
    {
        $arr = $this->getHeaderValueSourcesArrayFromBasket($userBasket);
        foreach($arr as $headerValueSource) {
            $query = $this->db->getUpdateInsertQueryFromArray($headerValueSource, self::VALUE_SOURCE_TABLE, 'insert');
            $this->db->setQuery($query)->doQuery();
        }
        return $userBasket;
    }

    /**
     * @param UserBasket $userBasket
     * @return UserBasket
     */
    private function saveUserBasketLines(UserBasket $userBasket)
    {
        $this->setBasketLinesToDelete($userBasket->getID());
        /**
         * @var $line BasketEntity
         */
        foreach($userBasket as $line) {
            $lineArr = $this->getLineArrayFromBasketEntity($userBasket,$line);

			$lineID = $line->getDBID();
            $lineArr['qty_protected'] = (int) $lineArr['qty_protected'];
            $lineArr['unit_price_protected'] = (int) $lineArr['unit_price_protected'];
            $lineArr['to_delete'] = (int) $lineArr['to_delete'];
			$lineArr['id'] = $lineID;

            $updateOrInsert =  ((int)$lineID > 0) ? 'update' : 'insert';

            $lineQuery = $this->db->getUpdateInsertQueryFromArray($lineArr,self::LINE_TABLE,$updateOrInsert);
            $this->db->setQuery($lineQuery)->doQuery();
            if($this->db->isErrorState()) {
                throw new \ErrorException('Query was not successful. Query: ' . $lineQuery . ' ErrorInfo: ' . $this->db->getErrorMessage());
            }
            $lineID = ((int)$lineID > 0) ? $lineID: $this->db->getLastInsertId();
            $this->setID($line,$lineID);
        }
        $this->deleteMarkedLines($userBasket->getID());
        return $userBasket;
    }

    /**
     * @param UserBasket $userBasket
     * @return UserBasket
     */
    private function saveUserBasketLineValueSources(UserBasket $userBasket)
    {
        $linesArray = $this->getLineValueSourcesArrayFromBasket($userBasket);
        foreach($linesArray as $lineValueSourceEntry) {
            if(count($lineValueSourceEntry) > 0) {
                $query = $this->db->getUpdateInsertQueryFromArray($lineValueSourceEntry,self::VALUE_SOURCE_TABLE,'insert');
                $this->db->setQuery($query)->doQuery();
            }
        }
        return $userBasket;
    }


    /**
     * @param UserBasket $userBasket
     * @return array
     */
    private function getHeaderArrayFromBasket(UserBasket $userBasket)
    {
        $header = [];
        $header['id'] = $userBasket->getID();
        $header['main_basket'] = $userBasket->isMainBasket();
        $header['shop_user_id'] = $userBasket->getUserID();
        $header['shop_visitor_id'] = $userBasket->getVisitorID();
        $header['company'] = $userBasket->getCompany();
        $header['shop_code'] = $userBasket->getShopCode();
        $header['shop_language_code'] = $userBasket->getShopLanguageCode();
        $header['description'] = $userBasket->getDescription();
        $header['basket_total'] = $userBasket->getBasketTotal();
        $header['basket_total_gross'] = $userBasket->getBasketTotalGross();
        $header['basket_total_net'] = $userBasket->getBasketTotalNet();
        $header['last_hash'] = $userBasket->getHash();
        return $header;
    }

    /**
     * @param UserBasket $userBasket
     * @return array
     */
    private function getHeaderValueSourcesArrayFromBasket(UserBasket $userBasket)
    {
        $headerInvoiceDiscounts = $userBasket->getAppliedInvoiceDiscounts();
        $headerValueSources = [];
        $i = 0;
        foreach ($headerInvoiceDiscounts as $invoiceDiscount) {
            if ($invoiceDiscount instanceof AppliedDiscount) {
                $headerValueSources[$i]['applied_to_header_id'] = $userBasket->getID();
                $headerValueSources[$i]['invoice_discount_source_type'] = $invoiceDiscount->getSourceType();
                $headerValueSources[$i]['invoice_discount_source_id'] = $invoiceDiscount->getSourceID();
                $headerValueSources[$i]['invoice_discount_applied'] = $invoiceDiscount->getDiscountedAmount();
                $headerValueSources[$i]['basket_total_before_invoice_discount'] = $invoiceDiscount->getPriceBeforeApplication();
                $headerValueSources[$i]['basket_total_after_invoice_discount'] = $invoiceDiscount->getPriceAfterApplication();
                ++$i;
            }
        }
        return $headerValueSources;
    }

    /**
     * @param $headerID
     * @param BasketEntity $basketLine
     * @return array
     */
    private function getLineArrayFromBasketEntity(UserBasket $basket, BasketEntity $basketLine)
    {
        $headerID = $basket->getID();
        $entityLink = $basketLine->getBasketEntityLink();
        $line = [];
        $line['id'] = ($basketLine->getDBID() > 0) ? $basketLine->getDBID() : null;
        $line['header_id'] = $headerID;
        $line['line_no'] = $basketLine->getLineNo();
        $line['orderable_item_type'] = $basketLine->getOrderableType();
        $line['identifier'] = $basketLine->getIdentifier();
        $line['subidentifier'] = $basketLine->getSubIdentifier();
        $line['description'] = $basketLine->getDescription();
        $line['quantity'] = $basketLine->getQuantity();
        $line['unit_price'] = $basketLine->getUnitPrice();
        $line['vat_percent'] = $basketLine->getVATPercent();
        $line['vat_amount'] = $basketLine->getVATAmount();
        $line['vat_prod_posting_group'] = $basketLine->getVATCode();
        $line['unit_price_gross'] = $basketLine->getUnitPriceGross($this->VATManager);
        $line['unit_price_net'] = $basketLine->getUnitPriceNet($this->VATManager);
        $line['line_amount'] = $basketLine->getLineAmount();
        $line['line_amount_gross'] = (float)$line['unit_price_gross'] * (float)$line['quantity'];
        $line['line_amount_net'] = (float)$line['unit_price_net'] * (float)$line['quantity'];
        $line['item_id'] = $basketLine->getID();
        $line['qty_protected'] = (int)$basketLine->isQtyProtected();
        $line['unit_price_protected'] = (int)$basketLine->isUnitPriceProtected();
		$line['company'] = $basketLine->getCompany();
		$line['shop_code'] = $basketLine->getShopCode();
		$line['shop_language_code'] = $basketLine->getShopLanguageCode();
		$line['customization_hash'] = $basketLine->getCustomizationHash();
        $line['greeting_card_text'] = $basketLine->getGreetingCardText();
        $line['entity_key'] = $basket->getKey($basketLine);
        $line['links_to_entity_key'] = $entityLink['links_to_entity_key'];
        $line['entity_link_type'] = $entityLink['entity_link_type'];
        $line['to_delete'] = 0;
            
       return $line;
    }

    /**
     * @param UserBasket $userBasket
     * @return array
     */
    private function getLineValueSourcesArrayFromBasket(UserBasket $userBasket)
    {
        $lineValueSources = [];
        $i = 0;
        foreach ($userBasket as $basketLine) {
            if ($basketLine instanceof BasketEntity) {
                $appliedQtyChanges = $basketLine->getAppliedQtyChanges();
                if(!is_array($appliedQtyChanges)) {
                    $appliedQtyChanges = [];
                    $initialQty = $basketLine->getQuantity();
                } else {
                    $firstChange = $appliedQtyChanges[0];
                    $initialQty = $firstChange->getQtyBeforeSetting();
                }
                $lineValueSources[$i]['applied_to_header_id'] = $userBasket->getID();
                $lineValueSources[$i]['applied_to_line_id'] = $basketLine->getDBID();
                $lineValueSources[$i]['created_by_source_type'] = $basketLine->getCreationSourceType();
                $lineValueSources[$i]['created_by_source_id'] = $basketLine->getCreationSourceID();
                $lineValueSources[$i]['creation_notification'] = $basketLine->getCreationNotification();
                $lineValueSources[$i]['qty_set_by_source_type'] = $basketLine->getQtySourceType();
                $lineValueSources[$i]['qty_set_by_source_id'] = $basketLine->getQtySourceID();
                $lineValueSources[$i]['qty_before_setting'] = 0;
                $lineValueSources[$i]['qty_after_setting'] = $initialQty;
                $lineValueSources[$i]['unit_price_set_by_source_type'] = $basketLine->getPriceSourceType();
                $lineValueSources[$i]['unit_price_set_by_source_id'] = $basketLine->getPriceSourceID();
                ++$i;
                $appliedDiscounts = $basketLine->getAppliedLineDiscounts();
                if(!is_array($appliedDiscounts)) {
                    $appliedDiscounts = [];
                }
                foreach ($appliedDiscounts as $discount) {
                    if ($discount instanceof AppliedDiscount) {
                        $lineValueSources[$i]['applied_to_header_id'] = $userBasket->getID();
                        $lineValueSources[$i]['applied_to_line_id'] = $basketLine->getDBID();
                        $lineValueSources[$i]['unit_price_set_by_source_type'] = $discount->getSourceType();
                        $lineValueSources[$i]['unit_price_set_by_source_id'] = $discount->getSourceID();
                        $lineValueSources[$i]['unit_price_before_setting'] = $discount->getPriceBeforeApplication();
                        $lineValueSources[$i]['unit_price_after_setting'] = $discount->getPriceAfterApplication();
                        ++$i;
                    }
                }

                foreach ($appliedQtyChanges as $qtyChange) {
                    if ($qtyChange instanceof BasketValueSource) {
                        $lineValueSources[$i]['applied_to_header_id'] = $userBasket->getID();
                        $lineValueSources[$i]['applied_to_line_id'] = $basketLine->getDBID();
                        $lineValueSources[$i]['qty_set_by_source_type'] = $qtyChange->getQtySetBySourceType();
                        $lineValueSources[$i]['qty_set_by_source_id'] = $qtyChange->getQtySetBySourceID();
                        $lineValueSources[$i]['qty_before_setting'] = $qtyChange->getQtyBeforeSetting();
                        $lineValueSources[$i]['qty_after_setting'] = $qtyChange->getQtyAfterSetting();
                        ++$i;
                    }
                }
            }
        }
        return $lineValueSources;
    }


    /**
     * @param $headerID
     */
    private function deleteAllValueSourceLinesForBasket($headerID) {
        $headerID = (int)$headerID;
        if(!($headerID > 0)) {
            throw new \InvalidArgumentException("Parameter 'headerID' must be a positive integer.");
        }
        $query = 'DELETE FROM `' . self::VALUE_SOURCE_TABLE . '` WHERE `applied_to_header_id` = ' . $headerID;
        $this->db->setQuery($query)->doQuery();
    }

    /**
     * @param CurrShopConfiguration $configuration
     * @return GenericUserBasket
     * @throws \ErrorException
     */
    private function getMainBasketHeaderForVisitorID(CurrShopConfiguration $configuration) {
        $this->db
                ->select('id,company,shop_code,shop_language_code,last_hash')
                ->from(self::HEADER_TABLE)
                ->where('shop_visitor_id','=',$configuration->getVisitorID())
                ->andWhere('main_basket','=',1)
                ->andWhere('company','=',$configuration->getCompany())
                ->andWhere('shop_code','=',$configuration->getShopCode())
                ->andWhere('shop_language_code','=',$configuration->getShopLanguageCode())
            ->setConstructedQuery();
			$query = $this->db->getQuery();
		if (!$this->db->doQuery()) {
			$error = $this->db->getErrorMessage();
			throw new \ErrorException(print_r($error,1));
		}
        $headerID = 0;
        $company = $configuration->getCompany();
        $shopCode = $configuration->getShopCode();
        $shopLanguageCode = $configuration->getShopLanguageCode();
        $lastHash = '';
        if($this->db->getNoOfReturnedRows() === 1) {
			$basketArray = $this->db->getResultArray()[0];
            $headerID = isset($basketArray['id']) ? $basketArray['id'] : 0;
            $lastHash = isset($basketArray['last_hash']) ? $basketArray['last_hash'] : '';
        }
        $header = new GenericUserBasket($this->VATManager, $configuration->getVisitor(), $lastHash , $configuration->getUser(), $headerID, true);
		$header->setCompany($company);
		$header->setShopCode($shopCode);
		$header->setShopLanguageCode($shopLanguageCode);
        return $header;
    }

    /**
     * @param CurrShopConfiguration $configuration
     * @return GenericUserBasket
     */
    private function getMainBasketHeaderForUserID(CurrShopConfiguration $configuration) {
        $this->db
                ->select('id,last_hash')
                ->from(self::HEADER_TABLE)
                ->where('shop_user_id','=',$configuration->getUserID())
                ->andWhere('main_basket','=',1)
                ->andWhere('company','=',$configuration->getCompany())
                ->andWhere('shop_code','=',$configuration->getShopCode())
                ->andWhere('shop_language_code','=',$configuration->getShopLanguageCode())
            ->setConstructedQuery()
            ->doQuery();
        $headerID = 0;
        $company = $configuration->getCompany();
        $shopCode = $configuration->getShopCode();
        $shopLanguageCode = $configuration->getShopLanguageCode();
        $lastHash = '';
        if($this->db->getNoOfReturnedRows() === 1) {
            $resArr = $this->db->getResultArray()[0];
            $headerID = $resArr['id'];
            $lastHash = $resArr['last_hash'];
        }
        $header = new GenericUserBasket($this->VATManager, $configuration->getVisitor(), $lastHash, $configuration->getUser(), $headerID, true);
		$header->setCompany($company);
		$header->setShopCode($shopCode);
		$header->setShopLanguageCode($shopLanguageCode);
        return $header;
    }

    /**
     * @param $headerID
     * @return \SplDoublyLinkedList
     */
    private function getAppliedInvoiceDiscountsForHeaderID($headerID)
    {
        $this->db
                ->select('*')
                ->from(self::VALUE_SOURCE_TABLE)
                ->where('applied_to_header_id','=',(int)$headerID)
                ->andWhere('invoice_discount_applied','!=',0)
            ->setConstructedQuery()
            ->doQuery();
        $discountArr = $this->db->getAllResultRowAsArrayOfObjects('DynCom\dc\dcShop\classes\BasketValueSource');
        $discounts = new \SplDoublyLinkedList();
        foreach($discountArr as $discount) {
            $sourceType = $discount->getInvoiceDiscountSourceType();
            $sourceID = $discount->getInvoiceDiscountSourceID();
            if(!($discount instanceof BasketValueSource)) {
                throw new \ErrorException('DB-result is not an object of type BasketValueSource');
            } else {
                //if(!($sourceType === DiscountBase::DISCOUNT_SOURCE_TYPE_COUPON && (!isset($_GET['action']) || $_GET['action'] !== 'coupon_delete') && $_GET['shop_category'] == 'order' && is_array($_SESSION['coupon']))) {
                if ($sourceType === DiscountBase::DISCOUNT_SOURCE_TYPE_COUPON && (!isset($_SESSION['coupon']))) {
                    continue;
                }
                $discountObj = new GenericInvoiceDiscount($discount->getInvoiceDiscountSourceType(), $discount->getInvoiceDiscountSourceID(), 0, $discount->getInvoiceDiscountApplied());
                $discounts->push($discountObj);
            }
        }
        return $discounts;
    }

    /**
     * @param $headerID
     * @param $lineID
     * @return array
     */
    private function getBasketLineValueSourcesArray($headerID, $lineID)
    {
        $this->db
                ->select('*')
                ->from(self::VALUE_SOURCE_TABLE)
                ->where('applied_to_header_id','=',(int)$headerID)
                ->andWhere('applied_to_line_id','=',(int)$lineID)
            ->setConstructedQuery()
            ->doQuery();
        $valueSourceArr = $this->db->getAllResultRowAsArrayOfObjects('DynCom\dc\dcShop\classes\BasketValueSource');
        return $valueSourceArr;
    }

    /**
     * @param $headerID
     * @param $hasNonExistentItems
     * @return \SplDoublyLinkedList
     */
    private function getBasketEntitiesFromDB($headerID, &$hasNonExistentItems)
    {
        //@TODO: F�r andere orderable-types (au�er "Item"[2]) anpassen
        $this->db
                ->select('id,orderable_item_type,identifier,subidentifier,quantity,unit_price,vat_percent,vat_amount,line_amount,item_id,parent_item_id,qty_protected,unit_price_protected,customization_hash,company,shop_code,shop_language_code,greeting_card_text,links_to_entity_key,entity_link_type')
                ->from(self::LINE_TABLE)
                ->where('header_id','=',(int)$headerID)
                ->orderBy('line_no','ASC')
            ->setConstructedQuery()->doQuery();
        $entityArr = $this->db->getResultArray();

        $basketEntities = new \SplDoublyLinkedList();
        foreach($entityArr as $basketLine) {
            $item = $this->itemBuilder->getWebshopItemOrderableEntityByPrimary($basketLine['identifier'],$basketLine['subidentifier']);
            if(!($item->getID() > 0)) {
                $this->setBasketLineToDelete($basketLine['id']);
                $this->deleteMarkedLines($headerID);
                $this->setValueSourceLinesToDelete($headerID,$basketLine['id']);
                $this->deleteMarkedValueSourceLines($headerID);
                $hasNonExistentItems = true;
                continue;
                //throw new \ErrorException('Could not build Item for itemNo: \'' . $basketLine['identifier'] . '\' and varCode: \'' . $basketLine['subidentifier'] . '\'');
            }
			$item->setCustomizationHash($basketLine['customization_hash']);
            $item->setGreetingCardText($basketLine['greeting_card_text']);
            $item->setBasketEntityLink($basketLine['links_to_entity_key'], $basketLine['entity_link_type']);
            $basketEntity = $this->itemBuilder->decorateWebshopItemBasket($item,$basketLine['quantity']);

            $this->setID($basketEntity,$basketLine['id']);
            $basketEntity->setLineNo($basketLine['line_no']);
            $lineValueSources = $this->getBasketLineValueSourcesArray($headerID,$basketEntity->getDBID());
            foreach($lineValueSources as $valueSource) {
                if(!($valueSource instanceof BasketValueSource)) {
                    throw new \ErrorException('Each entry of the \'lineValueSources\' parameter-array must be an instance of BasketValueSource.');
                } else {

                    if ($valueSource->getUnitPriceSetBySourceType() === GenericLineDiscount::DISCOUNT_SOURCE_TYPE_COUPON && (int)$valueSource->getUnitPriceSetBySourceID() > 0) {
                        $couponLine = $this->couponLineRepository->findByID($valueSource->getUnitPriceSetBySourceID());
                        $valueSource->setCouponLine($couponLine);
                    }
                    $basketEntity->applyBasketValueSource($valueSource,$this->VATManager);
                }
            }
            if($basketLine['qty_protected']) {
                $basketEntity->setQtyAccessibility(false);
            }
            if($basketLine['unit_price_protected']) {
                $priceData = $basketEntity->getPriceData();
                $priceData->setUnitPrice((float)$basketLine['unit_price']);
                $basketEntity->setUnitPriceAccessibility(false);
            }
            $basketEntities->push($basketEntity);
        }
        return $basketEntities;
    }

    /**
     * @param $headerID
     */
    private function setBasketLinesToDelete($headerID)
    {
        $headerID = (int)$headerID;
        if($headerID > 0) {
            $query = 'UPDATE ' . self::LINE_TABLE . ' SET to_delete = 1 WHERE header_id = ' . $headerID;
            $this->db->setQuery($query)->doQuery();
        }
    }

    /**
     * @param $lineID
     */
    private function setBasketLineToDelete($lineID)
    {
        $lineID = (int)$lineID;
        if($lineID > 0) {
            $query = 'UPDATE ' . self::LINE_TABLE . ' SET to_delete = 1 WHERE id = ' . $lineID;
            $this->db->setQuery($query)->doQuery();
        }
    }

    /**
     * @param $headerID
     */
    private function deleteMarkedLines($headerID)
    {
        $headerID = (int)$headerID;
        $query = 'DELETE FROM ' . self::LINE_TABLE . ' WHERE to_delete = 1 AND header_id = ' . $headerID;
        $this->db->setQuery($query)->doQuery();
    }

    /**
     * @param $headerID
     * @param $basketLineID
     */
    private function setValueSourceLinesToDelete($headerID, $basketLineID)
    {
        $headerID = (int)$headerID;
        $basketLineID = (int)$basketLineID;
        if($headerID > 0 && $basketLineID > 0) {
            $query = 'UPDATE ' . self::VALUE_SOURCE_TABLE . ' SET to_delete = 1 WHERE applied_to_header_id = ' . $headerID . ' AND applied_to_line_id = ' . $basketLineID;
            $this->db->setQuery($query)->doQuery();
        }
    }

    /**
     * @param $headerID
     */
    private function deleteMarkedValueSourceLines($headerID)
    {
        $headerID = (int)$headerID;
        $query = 'DELETE FROM ' . self::VALUE_SOURCE_TABLE . ' WHERE to_delete = 1 AND applied_to_header_id = ' . $headerID;
        $this->db->setQuery($query)->doQuery();
    }

    /**
     * @param $id
     */
    public function deleteBasketByID($id)
    {
        $id = (int)$id;
        $headerQuery = 'DELETE FROM ' . self::HEADER_TABLE . ' WHERE id = $id';
        $linesQuery = 'DELETE FROM ' . self::LINE_TABLE . ' WHERE header_id = $id';
        $valueSourcesQuery = 'DELETE FROM ' . self::VALUE_SOURCE_TABLE . ' WHERE applied_to_header_id = $id';
        if($id > 0) {
            $this->db->setQuery($headerQuery)->doQuery();
            $this->db->setQuery($linesQuery)->doQuery();
            $this->db->setQuery($valueSourcesQuery)->doQuery();
        }
    }

    /**
     * @param $id
     */
    public function removeMainBasketFlag($id)
    {
        $id = (int)$id;
        $query = 'UPDATE ' . self::HEADER_TABLE . ' SET main_basket = 0 WHERE id = ' . $id;
        $this->db->setQuery($query)->doQuery();
    }

    /**
     * @param $id
     */
    public function setMainBasketFlag($id)
    {
        $id = (int)$id;
        $this->db
                ->select('shop_visitor_id,main_basket')
                ->from(self::HEADER_TABLE)
                ->where('id','=',$id)
            ->setConstructedQuery()->doQuery();
        if(!$this->db->getNoOfReturnedRows() === 1) {
            throw new \InvalidArgumentException('No header with id \'' . $id . '\' exists.');
        }
        $resArr = $this->db->getResultArray();
        $visitorID = $resArr[0]['shop_visitor_id'];
        $mainBasket = $resArr[0]['main_basket'];
        if($mainBasket) {
            return;
        }
        $mainBasketID = $this->getMainBasketIDForVisitor($visitorID);
        if($mainBasketID > 0 && $mainBasketID !== $id) {
            throw new \InvalidArgumentException('Another main basket (id ' . $mainBasketID . ') already exists for visitor ' . $visitorID . '!');
        }
        $setQuery = 'UPDATE ' . self::HEADER_TABLE . ' SET main_basket = 1 WHERE id = ' . $id;
        $this->db->setQuery($setQuery)->doQuery();
    }

    /**
     * @param $visitorID
     * @return int
     */
    public function getMainBasketIDForVisitor($visitorID)
    {
        $visitorID = (int)$visitorID;
        $this->db
                ->select('id')
                ->from(self::HEADER_TABLE)
                ->where('main_basket','=',1)
                ->andWhere('shop_visitor_id','=',$visitorID)
            ->setConstructedQuery()->doQuery();
        $noRows = $this->db->getNoOfReturnedRows();
        if($noRows === 0) {
            return 0;
        } elseif($noRows > 1) {
            throw new \DomainException('There exists more than one main_basket for visitor with ID: ' . $visitorID);
        }
        $resArr = $this->db->getResultArray();
        $id = $resArr[0]['id'];
        return (int)$id;
    }

    /**
     * @param $basketID
     * @param $visitorID
     * @param int $userID
     */
    public function setBasketVisitorUser($basketID, $visitorID, $userID = 0)
    {
        $basketID = (int)$basketID;
        $visitorID = (int)$visitorID;
        $userID = (int)$userID;
        $userSnippet = ($userID > 0) ? ', shop_user_id = ' . $userID : '';
        $query = 'UPDATE ' . self::HEADER_TABLE . ' SET shop_visitor_id = ' . $visitorID . $userSnippet
            . ' WHERE id = ' . $basketID;
        $this->db->setQuery($query)->doQuery();
    }

    /**
     * @param $visitorID
     * @return int
     */
    public function getVisitorMainBasketID($visitorID)
    {
        $visitorID = (int)$visitorID;
        if(!($visitorID > 0)) {
            return 0;
        }
        $this->db
            ->select('id')
            ->from(UserBasketRepository::HEADER_TABLE)
            ->where('main_basket','=',1)
            ->andWhere('shop_visitor_id','=',$visitorID)
            ->setConstructedQuery()->doQuery();
        $noRows = $this->db->getNoOfReturnedRows();
        if($noRows > 1) {
            throw new \DomainException('Visitor has more than one main_basket!');
        } elseif($noRows === 0) {
            return 0;
        }
        $res = $this->db->getResultArray();
        $id = $res[0]['id'];
        return (int)$id;
    }

    /**
     * @param $headerID
     * @return bool
     */
    public function basketHasContent($headerID)
    {
        $headerID = (int)$headerID;
        $query = 'SELECT EXISTS (SELECT 1 FROM ' . self::LINE_TABLE  . ' WHERE header_id = ' . $headerID . ') AS "exists"';
        $this->db->setQuery($query)->doQuery();
        $resArr = $this->db->getResultArray();
        $exists = (bool)$resArr[0]['exists'];
        return $exists;
    }

    /**
     * @param $headerID
     */
    private function deleteAllLinesForHeader($headerID) {
        $headerID = (int)$headerID;
        if(!($headerID > 0)) {
            throw new \InvalidArgumentException("Parameter 'headerID' must be a positive integer.");
        }
        $query = 'DELETE FROM ' . self::LINE_TABLE . ' WHERE header_id = ' . $headerID;
        $this->db->setQuery($query)->doQuery();
    }

    /**
     * @param UserBasket $basket
     * @param BasketEntity $entity
     * @return int|null
     */
    private function getLineIDForEntity(UserBasket $basket, BasketEntity $entity)
    {
		//echo "DB-ID: " . $entity->getDBID();
		$query =  '
            SELECT id
            FROM ' . static::LINE_TABLE . '
            WHERE
                    header_id = :header_id/*
                AND line_no = :line_no*/
                AND identifier = :entity_identifier
                AND subidentifier = :entity_subidentifier
                AND qty_protected = :is_qty_protected
                AND unit_price_protected = :is_unit_price_protected
				AND customization_hash = :customization_hash
				AND greeting_card_text = :greeting_card_text
            ORDER BY id ASC
            LIMIT 1
        ';

				$fullquery =  '
            SELECT id
            FROM ' . static::LINE_TABLE . '
            WHERE
                    header_id = '.$basket->getID().'
                AND identifier = \''.$entity->getIdentifier().'\'
                AND subidentifier = \''.$entity->getSubIdentifier().'\'
                AND qty_protected = '.(int)$entity->isQtyProtected().'
                AND unit_price_protected = '.(int)$entity->isUnitPriceProtected().'
				AND customization_hash = \''.$entity->getCustomizationHash().'\'
				AND greeting_card_text = \''.$entity->getGreetingCardText().'\'
            ORDER BY id ASC
            LIMIT 1
        ';

		$paramArr = [
			[':header_id',$basket->getID(),\PDO::PARAM_INT],
			/*[':line_no',$entity->getLineNo(),\PDO::PARAM_INT],*/
			[':entity_identifier',$entity->getIdentifier(),\PDO::PARAM_STR],
			[':entity_subidentifier',$entity->getSubIdentifier(),\PDO::PARAM_STR],
			[':is_qty_protected',(int)$entity->isQtyProtected(),\PDO::PARAM_INT],
			[':is_unit_price_protected',(int)$entity->isUnitPriceProtected(),\PDO::PARAM_INT],
			[':customization_hash',(string)$entity->getCustomizationHash(),\PDO::PARAM_STR],
			[':greeting_card_text',(string)$entity->getGreetingCardText(),\PDO::PARAM_STR],
		];
				
		$this->db->setQuery($fullquery)->doQuery();
				$rows = $this->db->getNoOfReturnedRows();
        if($this->db->getNoOfReturnedRows() === 1) {
            $arr = $this->db->getResultArray();
            $id = (int)$arr[0]['id'];
            return $id;
        }
        return null;
    }

}