<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\LanguageRepository;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\classes\SelectionCriteriaHelper;
use DynCom\dc\dcShop\interfaces\CustomerInterface;
use DynCom\dc\dcShop\interfaces\ItemAvailabilityService;
use DynCom\dc\dcShop\interfaces\ItemPriceDataInterface;
use DynCom\dc\dcShop\interfaces\ItemPricingService;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;
use DynCom\dc\dcShop\ShippingOptions\ShippingClassPriorityProvider;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 18.10.2016
 * Time: 14:52
 */
class WebshopItemService
{

    /**
     * @var WebshopItemRepository
     */
    private $itemRepo;
    /**
     * @var WebshopItemVariantRepository
     */
    private $variantRepo;
    /**
     * @var ItemPricingService
     */
    private $itemPricingService;
    /**
     * @var WebshopItemCanonicalURLProvider
     */
    private $urlProvider;
    /**
     * @var WebshopItemFileRepository
     */
    private $itemFileRepository;
    /**
     * @var WebshopItemVariantService
     */
    private $variantService;
    /**
     * @var ItemAvailabilityService
     */
    private $availabilityService;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var CustomerRepository
     */
    private $customerRepository;
    /**
     * @var InventoryStrategyFactory
     */
    private $inventoryStrategyFactory;

    private $shopByKey = [];
    private $customerByKey = [];
    private $vatManagerByKey = [];
    private $priceProviderByKey = [];
    private $availabilityProviderByKey = [];
    /**
     * @var WebshopItemAttributeService
     */
    private $attributeService;
    /**
     * @var WebshopItemDescriptionRepository
     */
    private $descriptionRepository;
    /**
     * @var LanguageRepository
     */
    private $languageRepository;
    /**
     * @var PDOQueryWrapper
     */
    private $pdo;
    /**
     * @var ShopRepository
     */
    private $shopRepository;
    /**
     * @var ShippingClassPriorityProvider
     */
    private $shippingClassPriorityProvider;


    /**
     * WebshopItemService constructor.
     * @param PDOQueryWrapper $pdo
     * @param LanguageRepository $languageRepository
     * @param ShopRepository $shopRepository
     * @param WebshopItemRepository $itemRepo
     * @param WebshopItemVariantService $variantService
     * @param ItemPricingService $itemPricingService
     * @param WebshopItemCanonicalURLProvider $urlProvider
     * @param WebshopItemFileRepository $itemFileRepository
     * @param ItemAvailabilityService $availabilityService
     * @param CategoryRepository $categoryRepository
     * @param CustomerRepository $customerRepository
     * @param InventoryStrategyFactory $inventoryStrategyFactory
     * @param WebshopItemAttributeService $attributeService
     * @param WebshopItemDescriptionRepository $descriptionRepository
     * @param ShippingClassPriorityProvider $shippingClassPriorityProvider
     */
    public function __construct(
        PDOQueryWrapper $pdo, LanguageRepository $languageRepository, ShopRepository $shopRepository, WebshopItemRepository $itemRepo, WebshopItemVariantService $variantService, ItemPricingService $itemPricingService, WebshopItemCanonicalURLProvider $urlProvider, WebshopItemFileRepository $itemFileRepository, ItemAvailabilityService $availabilityService, CategoryRepository $categoryRepository, CustomerRepository $customerRepository, InventoryStrategyFactory $inventoryStrategyFactory, WebshopItemAttributeService $attributeService, WebshopItemDescriptionRepository $descriptionRepository, ShippingClassPriorityProvider $shippingClassPriorityProvider
    )
    {
        $this->itemRepo = $itemRepo;
        $this->itemPricingService = $itemPricingService;
        $this->urlProvider = $urlProvider;
        $this->itemFileRepository = $itemFileRepository;
        $this->variantService = $variantService;
        $this->availabilityService = $availabilityService;
        $this->categoryRepository = $categoryRepository;
        $this->customerRepository = $customerRepository;
        $this->inventoryStrategyFactory = $inventoryStrategyFactory;
        $this->attributeService = $attributeService;
        $this->descriptionRepository = $descriptionRepository;
        $this->languageRepository = $languageRepository;
        $this->pdo = $pdo;
        $this->shopRepository = $shopRepository;
        $this->shippingClassPriorityProvider = $shippingClassPriorityProvider;
    }

    /**
     * @param $company
     * @param $itemShopCode
     * @param $itemLanguageCode
     * @param $itemNo
     * @return array
     */
    public function getItemVariants($company, $itemShopCode, $itemLanguageCode, $itemNo)
    {
        $baseItem = $this->getItem($company, $itemShopCode, $itemLanguageCode, $itemNo, '');
        $parentItem = $this->variantService->getParentItem($baseItem);
        $variants = $this->variantService->getAllVariants($parentItem);
        return $variants;
    }

    /**
     * @param $company
     * @param $itemShopCode
     * @param $itemLanguageCode
     * @param $itemNo
     * @param $variantCode
     * @return WebshopItemInterface
     */
    public function getItem($company, $itemShopCode, $itemLanguageCode, $itemNo, $variantCode)
    {
        $itemPrimary = ['company' => $company, 'shop_code' => $itemShopCode, 'language_code' => $itemLanguageCode, 'item_no' => $itemNo];
        $variantPrimary = ['company' => $company, 'item_no' => $itemNo, 'code' => $variantCode];
        $item = $this->itemRepo->findByAltPrimary($itemPrimary);
        if (!empty($variantCode)) {
            $variant = $this->variantRepo->findByAltPrimary($variantPrimary);
            $item = new WebshopitemNAVVariantDecorator($item, $variant);
        }
        return $item;
    }

    /**
     * @param $company
     * @param $itemShopCode
     * @param $itemLanguageCode
     * @param $itemNo
     * @param $variantCode
     * @return WebshopItemInterface
     */
    public function getItemParent($company, $itemShopCode, $itemLanguageCode, $itemNo, $variantCode)
    {
        $baseItem = $this->getItem($company, $itemShopCode, $itemLanguageCode, $itemNo, $variantCode);
        $parentItem = $this->variantService->getParentItem($baseItem);
        return $parentItem;
    }

    /**
     * @param $company
     * @param $targetShopCode
     * @param $itemShopCode
     * @param $customerShopCode
     * @param $itemLanguageCode
     * @param $customerNo
     * @param $itemNo
     * @param $variantCode
     * @param $quantity
     * @return ItemPriceDataInterface
     */
    public function getItemCustomerPrice(
        $company,
        $targetShopCode,
        $itemShopCode,
        $customerShopCode,
        $itemLanguageCode,
        $customerNo,
        $itemNo,
        $variantCode,
        $quantity
    )
    {
        $price = $this->itemPricingService->getItemCustomerPriceByPrimary(
            $company,
            $targetShopCode,
            $itemShopCode,
            $customerShopCode,
            $itemLanguageCode,
            $customerNo,
            $itemNo,
            $variantCode,
            $quantity
        );
        return $price;
    }

    /**
     * @param $company
     * @param $targetShopCode
     * @param $itemShopCode
     * @param $customerShopCode
     * @param $itemLanguageCode
     * @param $customerNo
     * @param $itemNo
     * @param $variantCode
     * @param $quantity
     * @return float|ItemPriceDataInterface
     */
    public function getItemDiscountedCustomerPrice(
        $company,
        $targetShopCode,
        $itemShopCode,
        $customerShopCode,
        $itemLanguageCode,
        $customerNo,
        $itemNo,
        $variantCode,
        $quantity
    )
    {
        $price = $this->itemPricingService->getItemDiscountedCustomerPriceByPrimary(
            $company,
            $targetShopCode,
            $itemShopCode,
            $customerShopCode,
            $itemLanguageCode,
            $customerNo,
            $itemNo,
            $variantCode,
            $quantity
        );
        return $price;
    }

    /**
     * @param $company
     * @param $itemShopCode
     * @param $targetShopCode
     * @param $customerShopCode
     * @param $itemLanguageCode
     * @param $customerNo
     * @param $itemNo
     * @param $variantCode
     * @return GraduatedItemPriceData
     */
    public function getItemGraduatedPrices(
        $company,
        $itemShopCode,
        $targetShopCode,
        $customerShopCode,
        $itemLanguageCode,
        $customerNo,
        $itemNo,
        $variantCode
    )
    {
        $graduatedPrices = $this->itemPricingService->getItemGraduatedCustomerPricesByPrimary(
            $company,
            $targetShopCode,
            $itemShopCode,
            $customerShopCode,
            $itemLanguageCode,
            $customerNo,
            $itemNo,
            $variantCode
        );
        return $graduatedPrices;
    }

    /**
     * @param $company
     * @param $itemShopCode
     * @param $itemLanguageCode
     * @param $itemNo
     * @param $variantCode
     * @return boolean
     */
    public function isItemAvailable($company, $itemShopCode, $itemLanguageCode, $itemNo, $variantCode)
    {
        $isAvailable = $this->availabilityService->isItemAvailable(
            $company,
            $itemShopCode,
            $itemLanguageCode,
            $itemNo,
            $variantCode
        );
        return $isAvailable;
    }

    /**
     * @param $company
     * @param $itemShopCode
     * @param $itemLanguageCode
     * @param $itemNo
     * @param $variantCode
     * @return boolean
     */
    public function isItemOrderableByInventory($company, $itemShopCode, $itemLanguageCode, $itemNo, $variantCode)
    {
        $isOrderable = $this->availabilityService->isItemOrderable(
            $company,
            $itemShopCode,
            $itemLanguageCode,
            $itemNo,
            $variantCode
        );
        return $isOrderable;
    }

    /**
     * @param $siteCode
     * @param $siteLanguageCode
     * @param $customerNo
     * @param $itemNo
     * @param $variantCode
     */
    public function getFullAggregateBySite($siteCode, $siteLanguageCode, $customerNo, $itemNo, $variantCode)
    {
    }

    /**
     * @param $company
     * @param $siteCode
     * @param $siteLanguageCode
     * @param $targetShopCode
     * @param $itemShopCode
     * @param $customerShopCode
     * @param $shopLanguageCode
     * @param $customerNo
     * @param $itemNo
     * @param $variantCode
     * @return WebshopItemFullAggregate
     */
    public function getFullAggregate(
        $company,
        $siteCode,
        $siteLanguageCode,
        $targetShopCode,
        $itemShopCode,
        $customerShopCode,
        $shopLanguageCode,
        $customerNo,
        $itemNo,
        $variantCode
    )
    {
        /**
         * @var $targetShop Shop
         */
        $targetShop = $this->getShop($company, $targetShopCode);
        $categoryShopCode = $targetShop->getUseCategoriesFromShopCode();
        $baseItem = $this->getItem($company, $itemShopCode, $shopLanguageCode, $itemNo, $variantCode);
        $parent = $this->variantService->getParentItem($baseItem);
        if (empty($variantCode) && empty($baseItem->getParentItemNo())) {
            $variants = new WebshopItemCollection(new WebshopItemConfig(), new SelectionCriteriaHelper());
        } else {
            $variants = $this->variantService->getAllVariantsAsCollection($baseItem);
        }

        $customer = $this->getCustomer($company, $customerShopCode, $shopLanguageCode, $customerNo);
        $priceProvider = $this->getPriceProvider(
            $company,
            $targetShopCode,
            $shopLanguageCode,
            $customerNo,
            $customerShopCode
        );
        $availabilityProvider = $this->getAvailabilityProvider($company, $targetShopCode);

        $currencyCode = $this->itemPricingService->getCurrencyCode(
            $company,
            $targetShopCode,
            $shopLanguageCode,
            $customerShopCode,
            $customerNo
        );


        $images = $this->getItemImages($company, $itemShopCode, $shopLanguageCode, $itemNo, $variantCode);
        $documents = $this->itemFileRepository->getAllDocumentsForItem(
            $company,
            $itemShopCode,
            $shopLanguageCode,
            $itemNo,
            $variantCode
        );
        $videos = $this->itemFileRepository->getAllVideosForItem(
            $company,
            $itemShopCode,
            $shopLanguageCode,
            $itemNo,
            $variantCode
        );
        $categories = $this->categoryRepository->getAllForItemShop(
            $company,
            $itemShopCode,
            $itemNo,
            $baseItem->getParentItemNo(),
            $categoryShopCode,
            $shopLanguageCode
        );
        $canonical = $this->getItemURL(
            $siteCode, $siteLanguageCode, $itemNo, $variantCode
        );
        $headDescriptions = $this->descriptionRepository->getAllHeaderDescriptionsForItem($baseItem);
        $bodyDescriptions = $this->descriptionRepository->getAllBodyDescriptionsForItem($baseItem);
        $attributes = $this->attributeService->getAllForItem($baseItem, $targetShop);

        $item = new WebshopItemFullAggregate(
            $baseItem, $parent, $variants, $customer, $priceProvider, $availabilityProvider, $this->shippingClassPriorityProvider, $currencyCode, $images, $documents, $videos, $categories, $canonical, $headDescriptions, $bodyDescriptions, $attributes, []
        );
        return $item;
    }

    /**
     * @param $company
     * @param $code
     * @return mixed
     */
    protected function getShop($company, $code)
    {
        $key = $company . '|' . $code;
        if (array_key_exists($key, $this->shopByKey)) {
            $shop = $this->shopByKey[$key];
        } else {
            $shop = $this->shopRepository->findByAltPrimary(['company' => $company, 'code' => $code]);
            $this->shopByKey[$key] = $shop;
        }
        return $shop;
    }

    /**
     * @param $company
     * @param $languageCode
     * @param $customerShopCode
     * @param $customerNo
     * @return CustomerInterface
     */
    protected function getCustomer($company, $languageCode, $customerShopCode, $customerNo)
    {
        $delim = '|';
        $customerKey = $company . $delim . $customerShopCode . $delim . $languageCode . $delim . $customerNo;
        if (array_key_exists(
                $customerKey,
                $this->customerByKey
            ) && $this->customerByKey[$customerKey] instanceof CustomerInterface
        ) {
            $customer = $this->customerByKey[$customerKey];
            return $customer;
        } else {
            $customerPimary = ['company' => $company, 'shop_code' => $customerShopCode, 'language_code' => $languageCode, 'customer_no' => $customerNo];
            $customer = $this->customerRepository->findByAltPrimary($customerPimary);
            $this->customerByKey[$customerKey] = $customer;
            return $customer;
        }
    }

    /**
     * @param $company
     * @param $targetShopCode
     * @param $languageCode
     * @param $customerNo
     * @param $customerShopCode
     * @return AdvancedPriceProvider $provider
     */
    private function getPriceProvider($company, $targetShopCode, $languageCode, $customerNo, $customerShopCode)
    {
        $provider = $this->itemPricingService->getPriceProvider($company, $targetShopCode, $languageCode, $customerNo, $customerShopCode);
        return $provider;
    }

    /**
     * @param $company
     * @param $targetShopCode
     * @return VATManager|mixed
     */
    protected function getVatManager($company, $targetShopCode)
    {
        $delim = '|';
        $shopKey = $company . $delim . $targetShopCode;
        if (array_key_exists($shopKey, $this->vatManagerByKey)) {
            return $this->vatManagerByKey[$shopKey];
        }

        $shop = $this->getShop($company, $targetShopCode);

        $vatManager = new VATManager($this->pdo, $company, $shop->vat_bus_posting_group, $shop->prices_including_vat);
        $this->vatManagerByKey[$shopKey] = $vatManager;
        return $vatManager;
    }

    /**
     * @param $company
     * @param $targetShopCode
     * @return DefaultItemAvailabilityProvider|mixed
     */
    protected function getAvailabilityProvider($company, $targetShopCode)
    {
        $key = $company . '|' . $targetShopCode;
        if (array_key_exists($key, $this->availabilityProviderByKey)) {
            $provider = $this->availabilityProviderByKey[$key];
        } else {
            $shop = $this->getShop($company, $targetShopCode);
            $inventoryStrategy = $this->inventoryStrategyFactory->getInventoryStrategy($shop);
            $provider = new DefaultItemAvailabilityProvider(
                $inventoryStrategy, $this->itemRepo, $this->variantService->getVariantRepository(),$shop
            );
            $this->availabilityProviderByKey[$key] = $provider;
        }

        return $provider;
    }

    /**
     * @param $company
     * @param $itemShopCode
     * @param $itemLanguageCode
     * @param $itemNo
     * @param $variantCode
     * @return WebshopItemFileCollection
     */
    public function getItemImages($company, $itemShopCode, $itemLanguageCode, $itemNo, $variantCode)
    {
        $baseItem = $this->getItem($company, $itemShopCode, $itemLanguageCode, $itemNo, $variantCode);
        $images = $this->itemFileRepository->getAllImagesForItem(
            $company,
            $itemShopCode,
            $itemLanguageCode,
            $itemNo,
            $variantCode,
            $baseItem->main_picture_line_no
        );
        return $images;
    }

    /**
     * @param $siteCode
     * @param $siteLanguageCode
     * @param $itemNo
     * @param $variantCode
     * @return string
     * @internal param $company
     * @internal param $itemShopCode
     * @internal param $itemLanguageCode
     */
    public function getItemURL(
        $siteCode, $siteLanguageCode, $itemNo, $variantCode
    )
    {
        $url = $this->urlProvider->getCanonicalByPrimary(
            $siteCode, $siteLanguageCode, $itemNo, $variantCode
        );
        return $url;
    }

    /**
     * @param $company
     * @param $itemShopCode
     * @param $itemShopLanguageCode
     * @param $itemNo
     * @return WebshopItemDescriptionCollection
     */
    public function getItemHeadDescriptions($company, $itemShopCode, $itemShopLanguageCode, $itemNo)
    {
        return $this->descriptionRepository->getAllHeaderDescriptionsForItemByPrimary(
            $company,
            $itemShopCode,
            $itemShopLanguageCode,
            $itemNo
        );
    }

    /**
     * @param $company
     * @param $itemShopCode
     * @param $itemShopLanguageCode
     * @param $itemNo
     * @return WebshopItemDescriptionCollection
     */
    public function getItemBodyDescriptions($company, $itemShopCode, $itemShopLanguageCode, $itemNo)
    {
        return $this->descriptionRepository->getAllBodyDescriptionsForItemByPrimary(
            $company,
            $itemShopCode,
            $itemShopLanguageCode,
            $itemNo
        );
    }

}