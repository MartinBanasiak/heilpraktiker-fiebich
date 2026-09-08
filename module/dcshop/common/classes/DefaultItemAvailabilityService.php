<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\dcShop\interfaces\ItemAvailabilityProvider;
use DynCom\dc\dcShop\interfaces\ItemAvailabilityService;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 16.10.2016
 * Time: 16:00
 */
class DefaultItemAvailabilityService implements ItemAvailabilityService
{
    /**
     * @var ShopRepository
     */
    private $shopRepository;
    /**
     * @var InventoryStrategyFactory
     */
    private $inventoryStrategyFactory;
    /**
     * @var WebshopItemRepository
     */
    private $itemRepository;
    /**
     * @var WebshopItemVariantRepository
     */
    private $variantRepository;

    /**
     * @var array
     */
    private $availabilityProviderInstancesByKey = [];

    /**
     * DefaultItemAvailabilityService constructor.
     * @param ShopRepository $shopRepository
     * @param InventoryStrategyFactory $inventoryStrategyFactory
     * @param WebshopItemRepository $itemRepository
     * @param WebshopItemVariantRepository $variantRepository
     */
    public function __construct(ShopRepository $shopRepository, InventoryStrategyFactory $inventoryStrategyFactory, WebshopItemRepository $itemRepository, WebshopItemVariantRepository $variantRepository)
    {
        $this->shopRepository = $shopRepository;
        $this->inventoryStrategyFactory = $inventoryStrategyFactory;
        $this->itemRepository = $itemRepository;
        $this->variantRepository = $variantRepository;
    }

    /**
     * @param $company
     * @param $shopCode
     * @param $languageCode
     * @param $itemNo
     * @param $variantCode
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function isItemAvailable($company,$shopCode,$languageCode,$itemNo,$variantCode)
    {
        $availabilityProvider = $this->getAvailabilityProvider($company,$shopCode,$languageCode);

        /**
         * @var WebshopItemInterface
         */
        $item = $this->getItemObject($company,$shopCode,$languageCode,$itemNo,$variantCode);
        if (!$item->getID() > 0 || ($variantCode && !$item->getVariantCode())) { //No item found - throw \Exception
            throw new \InvalidArgumentException("No item was found for company [$company] and shop_code [$shopCode] and language_code [$languageCode] and item_no [$itemNo] and variant_code [$variantCode].");
        }
        return $availabilityProvider->isItemAvailable($item);
    }

    /**
     * @param $company
     * @param $shopCode
     * @param $languageCode
     * @param $itemNo
     * @param $variantCode
     * @return bool|mixed
     * @throws \InvalidArgumentException
     */
    public function isItemOrderable($company,$shopCode,$languageCode,$itemNo,$variantCode)
    {
        $availabilityProvider = $this->getAvailabilityProvider($company,$shopCode,$languageCode);
        /**
         * @var WebshopItemInterface
         */
        $item = $this->getItemObject($company,$shopCode,$languageCode,$itemNo,$variantCode);
        if (!$item->getID() > 0 || ($variantCode && !$item->getVariantCode())) { //No item found - throw \Exception
            throw new \InvalidArgumentException("No item was found for company [$company] and shop_code [$shopCode] and language_code [$languageCode] and item_no [$itemNo] and variant_code [$variantCode].");
        }
        return $availabilityProvider->isItemOrderable($item);
    }

    /**
     * @param $company
     * @param $shopCode
     * @param $languageCode
     * @return DefaultItemAvailabilityProvider|mixed
     */
    private function getAvailabilityProvider($company, $shopCode, $languageCode)
    {
        $availabilityProviderKey = $company . '|' . $shopCode . '|' . $languageCode;
        if (array_key_exists($availabilityProviderKey,$this->availabilityProviderInstancesByKey) && $this->availabilityProviderInstancesByKey[$availabilityProviderKey] instanceof ItemAvailabilityProvider) {
            $availabilityProvider = $this->availabilityProviderInstancesByKey[$availabilityProviderKey];
        } else {
            $shop = $this->shopRepository->findByAltPrimary(['company' => $company, 'code' => $shopCode]);
            $inventoryStrategy = $this->inventoryStrategyFactory->getInventoryStrategy($shop);
            $availabilityProvider = new DefaultItemAvailabilityProvider($inventoryStrategy, $this->itemRepository, $this->variantRepository,$shop);
            $this->availabilityProviderInstancesByKey[$availabilityProviderKey] = $availabilityProvider;
        }
        return $availabilityProvider;
    }

    /**
     * @param $company
     * @param $shopCode
     * @param $languageCode
     * @param $itemNo
     * @param $variantCode
     * @return WebshopItemNAVVariantDecorator|mixed
     */
    private function getItemObject($company, $shopCode, $languageCode, $itemNo, $variantCode)
    {
        $item = $this->itemRepository->findByAltPrimary(['company' => $company, 'shop_code' => $shopCode, 'language_code' => $languageCode, 'item_no' => $itemNo]);
        if ($variantCode) {
            $variant = $this->variantRepository->findByAltPrimary(['company' => $company, 'item_no' => $itemNo, 'code' => $variantCode]);
            $item = new WebshopItemNAVVariantDecorator($item,$variant);
        }
        return $item;
    }


}