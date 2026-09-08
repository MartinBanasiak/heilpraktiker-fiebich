<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 10.07.2015
 * Time: 09:54
 */
class InventoryStrategyFactory
{

    /**
     * @var WebshopItemRepository
     */
    protected $itemRepository;

    /**
     * @var WebshopItemVariantRepository
     */
    protected $variantRepository;

    /**
     * @var PDOQueryWrapper
     */
    protected $db;
	
	/**
	* @var CurrShopConfiguration
	*/
	protected $currShopConfig;

    /**
     * @var Shop
     */
    protected $currShop;

    /**
     * @param WebshopItemRepository $itemRepository
     * @param WebshopItemVariantRepository $variantRepository
     * @param PDOQueryWrapper $db
     * @internal param Shop $currShop
     * @internal param CurrShopConfiguration $currShopConfig
     */
    public function __construct(WebshopItemRepository $itemRepository, WebshopItemVariantRepository $variantRepository, PDOQueryWrapper $db) {
        $this->itemRepository = $itemRepository;
        $this->variantRepository = $variantRepository;
        $this->db = $db;
    }

    /**
     * @param Shop $shop
     * @return NAVVariantsInventoryStrategy|WebshopVariantsInventoryStrategy
     */
    public function getInventoryStrategy(Shop $shop) {
        return $shop->isVariantTypeNavVariants() ? new NAVVariantsInventoryStrategy($this->db,$this->itemRepository,$this->variantRepository) : new WebshopVariantsInventoryStrategy($this->itemRepository,$shop);
    }

}