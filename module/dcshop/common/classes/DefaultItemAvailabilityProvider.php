<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\dcShop\interfaces\ItemAvailabilityProvider;
use DynCom\dc\dcShop\interfaces\ItemInventoryStrategyInterface;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;
use Whoops\Exception\ErrorException;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 10.07.2015
 * Time: 10:11
 */
class DefaultItemAvailabilityProvider implements ItemAvailabilityProvider
{

    const INVENTORY_LIMIT_UNAVAILABLE = 0;

    const INVENTORY_NOT_AVAILABLE = 'not_available';
    const INVENTORY_LOW_AVAILABILITY = 'low_availability';
    const INVENTORY_AVAILABLE = 'available';

    const INVENTORY_NOT_AVAILABLE_ORDERABLE = 'not_available_orderable';
    const INVENTORY_AVAILABLE_NOT_ORDERABLE = 'available_not_orderable';
    const INVENTORY_LOW_AVAILABILITY_NOT_ORDERABLE = 'low_availability_not_orderable';

    private static $availabilityIsOrderable = [
        self::INVENTORY_AVAILABLE => true,
        self::INVENTORY_LOW_AVAILABILITY => true,
        self::INVENTORY_NOT_AVAILABLE => false,
        self::INVENTORY_NOT_AVAILABLE_ORDERABLE => true,
        self::INVENTORY_AVAILABLE_NOT_ORDERABLE => false,
        self::INVENTORY_LOW_AVAILABILITY_NOT_ORDERABLE => false
    ];

    private $cache;

    /**
     * @var ItemInventoryStrategyInterface
     */
    protected $inventoryStrategy;

    /**
     * @var WebshopItemRepository $webshopItemRepository
     */
    protected $webshopItemRepository;
    /**
     * @var WebshopItemVariantRepository
     */
    private $variantRepository;
    /**
     * @var Shop
     */
    private $shop;

    /**
     * DefaultItemAvailabilityProvider constructor.
     * @param ItemInventoryStrategyInterface $strategy
     * @param WebshopItemRepository $itemRepository
     * @param WebshopItemVariantRepository $variantRepository
     * @param Shop $shop
     */
    public function __construct(
        ItemInventoryStrategyInterface $strategy, WebshopItemRepository $itemRepository, WebshopItemVariantRepository $variantRepository, Shop $shop
    ) {
        $this->webshopItemRepository = $itemRepository;
        $this->inventoryStrategy = $strategy;
        $this->cache = [];
        $this->variantRepository = $variantRepository;
        $this->shop = $shop;
    }


    /**
     * @param WebshopItemInterface $item
     * @return string
     * @throws \ErrorException
     */
    public function getItemAvailability(WebshopItemInterface $item)
    {
        $key = $item->getID() . '|' . $item->getVariantCode();
        if(array_key_exists($key,$this->cache)) {
            $inventory = $this->cache[$key];
        } else {
            $inventory = $this->inventoryStrategy->getItemInventory($item);
            $this->cache[$key] = $inventory;
        }

        $itemAvailability = $this->getItemAvailabilityStatus($item);


        switch ($itemAvailability) {
            case 1: //no
                if ($inventory > $item->getLowInventoryLimit()) return self::INVENTORY_AVAILABLE_NOT_ORDERABLE;
                if ($inventory <= $item->getLowInventoryLimit() && $inventory > self::INVENTORY_LIMIT_UNAVAILABLE) return self::INVENTORY_LOW_AVAILABILITY_NOT_ORDERABLE;
                return self::INVENTORY_NOT_AVAILABLE;
                break;
            case 2: //yes
                if ($inventory > $item->getLowInventoryLimit()) return self::INVENTORY_AVAILABLE;
                if ($inventory <= $item->getLowInventoryLimit() && $inventory > self::INVENTORY_LIMIT_UNAVAILABLE) return self::INVENTORY_LOW_AVAILABILITY;
                return self::INVENTORY_NOT_AVAILABLE_ORDERABLE;
                break;
            default: //0 inventory
                if ($inventory > $item->getLowInventoryLimit()) return self::INVENTORY_AVAILABLE;
                if ($inventory <= $item->getLowInventoryLimit() && $inventory > self::INVENTORY_LIMIT_UNAVAILABLE) return self::INVENTORY_LOW_AVAILABILITY;
                return self::INVENTORY_NOT_AVAILABLE;
                break;
        }
    }

    /**
     * @param WebshopItemInterface $item
     * @return bool
     */
    public function isItemAvailable(WebshopItemInterface $item) {
        $inventory = $this->inventoryStrategy->getItemInventory($item);

        $itemAvailability = $this->getItemAvailabilityStatus($item);

        switch ($itemAvailability) {
            case 1: //no
                return false;
                break;
            case 2: //yes
                return true;
                break;
            default: //inventory
                if ((($inventory > $item->getLowInventoryLimit()) || ($inventory <= $item->getLowInventoryLimit() && $inventory > self::INVENTORY_LIMIT_UNAVAILABLE))) {
                    return true;
                }
                return false;
                break;
        }

        return false;
    }

    /**
     * @param WebshopItemInterface $item
     * @return bool
     */
    public function hasItemLowAvailability(WebshopItemInterface $item) {
        $inventory = $this->inventoryStrategy->getItemInventory($item);
        if (($inventory <= $item->getLowInventoryLimit() && $inventory > self::INVENTORY_LIMIT_UNAVAILABLE) && !$item->getItemAvailability()) return true;
        return false;
    }

    /**
     * @param WebshopItemInterface $item
     * @return bool
     */
    public function isItemUnavailable(WebshopItemInterface $item) {
        $inventory = $this->inventoryStrategy->getItemInventory($item);
        if($inventory <= self::INVENTORY_LIMIT_UNAVAILABLE) return true;
        return false;
    }

    /**
     * @param WebshopItemInterface $item
     * @return mixed
     */
    public function isItemOrderable(WebshopItemInterface $item) {
        return self::$availabilityIsOrderable[$this->getItemAvailability($item)];
    }

    /**
     * @param WebshopItemInterface $item
     * @return mixed
     */
    public function getInventory(WebshopItemInterface $item) {
        return $this->inventoryStrategy->getItemInventory($item);
    }

    /**
     * @param $itemID
     * @return mixed
     */
    public function getInventoryByItemID($itemID)
    {
        return $this->inventoryStrategy->getItemInventoryByID($itemID);
    }

    /**
     * @param array $altPrimary
     * @return mixed
     */
    public function getInventoryByAltPrimary(array $altPrimary)
    {
        return $this->inventoryStrategy->getItemInventoryByAltPrimary($altPrimary);
    }

    /**
     * @param $itemID
     * @return string
     */
    public function getItemAvailabilityByID($itemID)
    {
        $item = $this->webshopItemRepository->findByID($itemID);
        return $this->getItemAvailability($item);
    }

    /**
     * @param array $altPrimary
     * @return string
     */
    public function getItemAvailabilityByAltPrimary(array $altPrimary)
    {
        $item = $this->webshopItemRepository->findByAltPrimary($altPrimary);
        return $this->getItemAvailability($item);
    }

    /**
     * @param $itemID
     * @return mixed
     */
    public function isItemOrderableByID($itemID)
    {
        $item = $this->webshopItemRepository->findByID($itemID);
        return $this->isItemOrderable($item);
    }

    /**
     * @param array $altPrimary
     * @return mixed
     */
    public function isItemOrderableByAltPrimary(array $altPrimary)
    {
        $item = $this->webshopItemRepository->findByAltPrimary($altPrimary);

        return $this->isItemOrderable($item);
    }


    /**
     * @param array $altPrimary
     * @param string $variantCode
     * @return mixed
     */
    public function isItemOrderableByAltPrimaryAndVariantCode(array $altPrimary, $variantCode = '')
    {
        /**
         * @var $item WebshopItem
         */
        $item = $this->webshopItemRepository->findByAltPrimary($altPrimary);
        if ($variantCode) {
            $variant = $this->variantRepository->findByAltPrimary(['company' => $item->company, 'item_no' => $item->getItemNo(), 'code' => $variantCode]);
            $item = new WebshopItemNAVVariantDecorator($item,$variant);
        }
        return $this->isItemOrderable($item);
    }

    /**
     * @param WebshopItemInterface $item
     * @return int
     */
    public function getItemAvailabilityStatus(WebshopItemInterface $item)
    {
        $itemAvailability = 0;
        $itemAvailabilitySetting = $item->getItemAvailability();
        switch ($item->getItemAvailability()) {
            case 0: //shop settings
                switch ($this->shop->getItemAvailability()) {
                    case 0:
                        $itemAvailability = 0;
                        break;
                    case 1:
                        $itemAvailability = 1;
                        break;
                    case 2:
                        $itemAvailability = 2;
                        break;
                }
                break;
            case 1: //inventory
                $itemAvailability = 0;
                break;
            case 2: //no
                $itemAvailability = 1;
                break;
            case 3: //yes
                $itemAvailability = 2;
                break;
        }
        return (int)$itemAvailability;
    }

}