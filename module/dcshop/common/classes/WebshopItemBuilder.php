<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\classes\Visitor;
use DynCom\dc\dcShop\interfaces\ItemAvailabilityProvider;
use DynCom\dc\dcShop\interfaces\OrderableEntityInterface;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;
use DynCom\dc\dcShop\ShippingOptions\ShippingClassPriorityProvider;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 08.10.2015
 * Time: 12:47
 */
class WebshopItemBuilder
{

    /**
     * @var Visitor
     */
    private $visitor;

    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var WebshopItemVariantRepository
     */
    private $webshopItemVariantRepository;

    /**
     * @var AdvancedPriceProvider
     */
    private $advancedPriceProvider;

    /**
     * @var WebshopItemVariantService
     */
    private $variantService;

    /**
     * @var string
     */
    private $currencyCode;

    /**
     * @var string
     */
    private $itemShopLanguage;

    /**
     * @var Shop
     */
    private $globalShop;

    /**
     * @var PDOQueryWrapper
     */
    private $db;

    /**
     * @var ItemAvailabilityProvider
     */
    private $availability;
    /**
     * @var ShippingClassPriorityProvider
     */
    private $shippingClassPriorityProvider;

    /**
     * WebshopItemBuilder constructor.
     * @param Visitor $visitor
     * @param Customer $customer
     * @param Shop $globalShop
     * @param ShopLanguage $itemShopLanguage
     * @param WebshopItemRepository $webshopItemRepository
     * @param WebshopItemVariantRepository $webshopItemVariantRepository
     * @param WebshopItemVariantService $variantService
     * @param AdvancedPriceProvider $advancedPriceProvider
     * @param ItemAvailabilityProvider $availabilityProvider
     * @param ShippingClassPriorityProvider $shippingClassPriorityProvider
     * @param PDOQueryWrapper $queryWrapper
     * @param $currencyCode
     */
    public function __construct(
        Visitor $visitor,
        Customer $customer,
        Shop $globalShop,
        ShopLanguage $itemShopLanguage,
        WebshopItemRepository $webshopItemRepository,
        WebshopItemVariantRepository $webshopItemVariantRepository,
        WebshopItemVariantService $variantService,
        AdvancedPriceProvider $advancedPriceProvider,
        ItemAvailabilityProvider $availabilityProvider,
        ShippingClassPriorityProvider $shippingClassPriorityProvider,
        PDOQueryWrapper $queryWrapper, $currencyCode
        )
    {
        $this->visitor = $visitor;
        $this->customer = $customer;
        $this->webshopItemRepository = $webshopItemRepository;
        $this->webshopItemVariantRepository = $webshopItemVariantRepository;
        $this->variantService = $variantService;
        $this->advancedPriceProvider = $advancedPriceProvider;
        $this->currencyCode = (string)$currencyCode;
        $this->itemShopLanguage = $itemShopLanguage;
        $this->globalShop = $globalShop;
        $this->availability = $availabilityProvider;
        $this->db = $queryWrapper;
        $this->shippingClassPriorityProvider = $shippingClassPriorityProvider;
    }

    /**
     * @param $id
     * @return WebshopItem
     */
    public function getWebshopItemByID($id) {
        $id = (int)$id;
        if(!($id > 0)) {
            throw new \InvalidArgumentException('ID must be a positive integer');
        }
        return $this->webshopItemRepository->findByID($id);
    }

    /**
     * @param $itemNo
     * @return WebshopItem
     */
    public function getWebshopItemByItemNo($itemNo) {
		$dummyItem = new WebshopItem(new WebshopItemConfig);
        $criteria = [
            [
                ['company','=',$this->itemShopLanguage->company],
                ['shop_code','=',$this->globalShop->getUseItemsFromShopCode()],
                ['language_code','=',$this->itemShopLanguage->code],
                ['item_no','=',$itemNo]
            ]
        ];
        $collection = $this->webshopItemRepository->findByCriteria($criteria,0,1);
		if (count($collection) === 1) {
			return $collection->getFirst();
		}
		return $dummyItem;
    }

    /**
     * @param $itemNo
     * @param string $variantCode
     * @return WebshopItem|WebshopItemNAVVariantDecorator
     */
    public function getWebshopItemByItemNoVarCode($itemNo,$variantCode = '')
    {
        if($variantCode === '') {
            return $this->getWebshopItemByItemNo($itemNo);
        }
        return $this->getWebshopItemNAVVariant($itemNo,$variantCode);
    }

    /**
     * @param $itemNo
     * @param $variantCode
     * @return WebshopItemNAVVariantDecorator
     */
    public function getWebshopItemNAVVariant($itemNo,$variantCode) {
        $item = $this->getWebshopItemByItemNo($itemNo);
        $criteria = [
            [
                ['company','=',$this->itemShopLanguage->company],
                ['item_no','=',$itemNo],
                ['code','=',$variantCode]
            ]
        ];
        $variant = $this->webshopItemVariantRepository->findByCriteria($criteria,0,1)->getFirst();
        return new WebshopItemNAVVariantDecorator($item,$variant);
    }

    /**
     * @param $id
     * @return WebshopItemOrderableEntityDecorator
     */
    public function getWebshopItemOrderableEntityByID($id) {
        $item = $this->getWebshopItemByID($id);
        return $this->decorateWebshopItemOrderable($item);
    }

    /**
     * @param $itemNo
     * @param string $varCode
     * @return WebshopItemOrderableEntityDecorator
     */
    public function getWebshopItemOrderableEntityByPrimary($itemNo,$varCode = '') {
        if($varCode === '') {
            $item = $this->getWebshopItemByItemNo($itemNo);
        } else {
            $item = $this->getWebshopItemNAVVariant($itemNo,$varCode);
        }
        return $this->decorateWebshopItemOrderable($item);
    }

    /**
     * @param $itemNo
     * @param $quantity
     * @param string $varCode
     * @return BasketEntity
     */
    public function getWebshopItemBasketEntity($itemNo,$quantity,$varCode = '') {
        $orderable = $this->getWebshopItemOrderableEntityByPrimary($itemNo,$varCode);
        $basketEntity = $this->decorateWebshopItemBasket($orderable,$quantity);
        return $basketEntity;
    }

    /**
     * @param WebshopItemInterface $item
     * @return WebshopItemOrderableEntityDecorator
     */
    public function decorateWebshopItemOrderable(WebshopItemInterface $item) {
        $orderable = new WebshopItemOrderableEntityDecorator(
            $item, $this->customer, $this->advancedPriceProvider, $this->availability, $this->shippingClassPriorityProvider, $this->currencyCode
        );
        return $orderable;
    }

    /**
     * @param WebshopItemInterface $item
     * @param $qty
     * @return BasketEntity
     */
    public function decorateWebshopItemBasket(WebshopItemInterface $item, $qty) {
        $orderable = $item;
        if(!($item instanceof OrderableEntityInterface)) {
            $orderable = $this->decorateWebshopItemOrderable($item);
        }
        $orderable->setQuantity((float)$qty);
        $basketEntity = new BasketEntity($orderable, $this->visitor, $this->advancedPriceProvider->getVATManager(), (float)$qty);
        return $basketEntity;
    }

    /**
     * @param $id
     * @param int $varID
     * @return WebshopItem|WebshopItemNAVVariantDecorator
     */
    public function getWebshopItemByIDVarID($id, $varID = 0) {
        $item = $this->getWebshopItemByID($id);
        if($varID > 0) {
            $variant = $this->webshopItemVariantRepository->findByID($varID);
            if ($variant->id > 0) {
                $item = new WebshopItemNAVVariantDecorator($item,$variant);
            }
        }
        return $item;
    }

    /**
     * @param $id
     * @param string $varCode
     * @return WebshopItem|WebshopItemNAVVariantDecorator
     */
    public function getWebshopItemByIDVarCode($id, $varCode = '') {
        $item = $this->getWebshopItemByID($id);
        if($varCode !== '') {
            $criteria = [
                [
                    ['company','=',$this->itemShopLanguage->company],
                    ['item_no','=',$item->getItemNo()],
                    ['code','=',$varCode]
                ]
            ];
            $variant = $this->webshopItemVariantRepository->findByCriteria($criteria,0,1)->getFirst();
            $item = new WebshopItemNAVVariantDecorator($item,$variant);
        }
        return $item;
    }

    /**
     * @param $id
     * @param string $varCode
     * @return WebshopItemOrderableEntityDecorator
     */
    public function getWebshopItemOrderableEntityByIDVarCode($id, $varCode = '')
    {
        $item = $this->getWebshopItemByIDVarCode($id,$varCode);
        $orderable = $this->decorateWebshopItemOrderable($item);
        return $orderable;
    }

    /**
     * @param $id
     * @param $qty
     * @param string $varCode
     * @return BasketEntity
     */
    public function getWebshopItemBasketEntityByIDVarCodeQty($id, $qty, $varCode = '')
    {
        $item = $this->getWebshopItemByIDVarCode($id,$varCode);
        $basketEntity = $this->decorateWebshopItemBasket($item,(float)$qty);
        return $basketEntity;
    }

    /**
     * @param WebshopItemInterface $item
     * @return WebshopItemCategoryDecorator
     */
    public function decorateWebshopItemCategories(WebshopItemInterface $item)
    {
        return new WebshopItemCategoryDecorator($item,$this->db,$this->globalShop->getUseCategoriesFromShopCode());
    }

    /**
     * @param $id
     * @param string $varCode
     * @return WebshopItemInterface|WebshopItemNAVVariantDecorator|mixed
     */
    public function getFirstVariant($id, $varCode = '') {
        $item = $this->getWebshopItemByIDVarCode($id,$varCode);
        $firstOrderable = $this->variantService->getFirstVariant($item);
        return $firstOrderable;
    }

    /**
     * @param $id
     * @param string $varCode
     * @return WebshopItemInterface|WebshopItemNAVVariantDecorator|mixed
     */
    public function getFirstActiveVariant($id, $varCode = '') {
        $item = $this->getWebshopItemByIDVarCode($id,$varCode);
        $firstOrderable = $this->variantService->getFirstActiveVariant($item);
        return $firstOrderable;
    }

    /**
     * @param WebshopItemInterface $item
     * @return WebshopItemImagesDecorator
     */
    public function decorateWebshopItemImages(WebshopItemInterface $item)
    {
        return new WebshopItemImagesDecorator($item,$this->variantService,$this->db);
    }

    /**
     * @param $company
     * @param $shopCode
     * @param $languageCode
     * @param $itemNo
     * @param string $varCode
     * @return WebshopItem|WebshopItemNAVVariantDecorator
     */
    public function getWebshopItemByPrimary($company,$shopCode,$languageCode,$itemNo,$varCode = '')
    {
        $altPrimary = [
            'company' => $company,
            'shop_code' => $shopCode,
            'language_code' => $languageCode,
            'item_no' => $itemNo
        ];
        $item = $this->webshopItemRepository->findByAltPrimary($altPrimary);
        if($varCode !== '') {
            $variantPrimary = [
                'company' => $company,
                'item_no' => $itemNo,
                'code' => $varCode
            ];
            $variant = $this->webshopItemVariantRepository->findByAltPrimary($variantPrimary);
            $item = new WebshopItemNAVVariantDecorator($item,$variant);
        }
        return $item;
    }

    /**
     * @param $query
     * @return array
     */
    public function buildWebshopItemsFromItemQuery($query)
    {
        static $genArr = [];
        $queryHash = md5($query);
        if (!array_key_exists($queryHash,$genArr)) {
            $genArr[$queryHash] = $this->webshopItemRepository->getAllByQueryAsArray($query);
        }
        $generator = $genArr[$queryHash];
        while ($obj = $generator->current()) {
            yield $obj;
            $generator->next();
        }
    }

    /**
     * @param $iterable
     * @return array
     */
    public function getAllWebshopItemsDecoratedForItemListAsArray($iterable)
    {
        $returnArr = [];
        foreach($iterable as $item) {
            $orderableWithImages = $this->decorateWebshopItemForItemList($item);
            $returnArr[] = $orderableWithImages;
        }
        return $returnArr;
    }

    /**
     * @param WebshopItemInterface $item
     * @return WebshopItemImagesDecorator|WebshopItemInterface
     */
    public function decorateWebshopItemForItemList(WebshopItemInterface $item)
    {
        $orderable = $this->decorateWebshopItemOrderable($item);
        $orderableWithImages = $this->decorateWebshopItemImages($orderable);
        return $orderableWithImages;
    }
}