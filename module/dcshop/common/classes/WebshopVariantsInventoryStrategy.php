<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\dcShop\interfaces\ItemInventoryStrategyInterface;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 10.07.2015
 * Time: 08:44
 */
class WebshopVariantsInventoryStrategy implements ItemInventoryStrategyInterface
{

    /**
     * @var WebshopItemRepository
     */
    protected $itemRepository;

    /**
     * @var CurrShopConfiguration
     */
    protected $currShopConfiguration;
    /**
     * @var Shop
     */
    private $shop;

    /**
     * WebshopVariantsInventoryStrategy constructor.
     * @param WebshopItemRepository $itemRepository
     * @param Shop $shop
     */
    public function __construct(WebshopItemRepository $itemRepository, Shop $shop)
    {
        $this->itemRepository = $itemRepository;
        $this->shop = $shop;
    }


    /**
     * @param WebshopItemInterface $item
     * @return float
     */
    public function getItemInventory(WebshopItemInterface $item)
    {
        $variants = $this->itemRepository->getWebshopVariants($item);
        $variantCount = count($variants);
        if (!($variantCount > 0) || $this->shop->isVariantTypeOrderableParent()) {
            $totalInventory = $item->getInventory();
        } else {
            $totalInventory = $item->getInventory();
            for ($variants->rewind(); $variants->isCurrValid(); $variants->next()) {
                $currItem = $variants->current();
                $addition = $currItem->getInventory();
                $totalInventory += $addition;
            }
        }
        return $totalInventory;
    }

    /**
     * @param $itemID
     * @return float
     */
    public function getItemInventoryByID($itemID)
    {
        $item = $this->itemRepository->findByID($itemID);
        return $this->getItemInventory($item);
    }

    /**
     * @param array $altPrimary
     * @return float
     */
    public function getItemInventoryByAltPrimary(array $altPrimary)
    {
        $item = $this->itemRepository->findByAltPrimary($altPrimary);
        return $this->getItemInventory($item);
    }



}