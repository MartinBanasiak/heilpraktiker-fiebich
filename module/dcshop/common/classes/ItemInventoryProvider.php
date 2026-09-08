<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 02.07.2015
 * Time: 17:50
 */
class ItemInventoryProvider
{

    /**
     * @var PDOQueryWrapper
     */
    protected $db;

    /**
     * @var CurrShopConfiguration
     */
    protected $currShopConfiguration;

    /**
     * @param PDOQueryWrapper $db
     * @param CurrShopConfiguration $currShopConfiguration
     */
    public function __construct(PDOQueryWrapper $db,CurrShopConfiguration $currShopConfiguration) {
        $this->db = $db;
        $this->currShopConfiguration = $currShopConfiguration;
    }

    /**
     * @param WebshopItemInterface $item
     * @return float
     */
    public function getWebshopItemInventory(WebshopItemInterface $item) {
        return $this->currShopConfiguration->getVariantType() === 2 ? $this->getInventorySumOverNAVVariants($item) : $this->getInventorySumOverWebVariants($item);
    }

    /**
     * @param WebshopItemInterface $item
     * @return float
     */
    protected function getInventorySumOverWebVariants(WebshopItemInterface $item) {
        $db = $this->db;
        $querySuccess =
            $db->select('SUM(inventory)')
                ->from('shop_item_variant')
                ->where('company','=',$item->getCompany())
                ->andWhere('item_no','=',$item->getItemNo())
                ->andWhere('to_delete','=',0)
                ->setConstructedQuery()->doQuery();
        if(!$querySuccess) return 0.00;
        $resArr = $db->getResultArray();
        return isset($resArr[0][0]) ? $resArr[0][0] : 0.00;
    }

    /**
     * @param WebshopItemInterface $item
     * @return float
     */
    protected function getInventorySumOverNAVVariants(WebshopItemInterface $item) {
        $db = $this->db;
        $querySuccess =
         $db->select('SUM(svai.inventory)')
            ->from('shop_item_link','sil')
            ->join('inner','shop_view_active_item','svai')
                ->on('svai.company','=','sil.company')
                ->andOn('svai.shop_code', '=', 'sil.shop_code')
                ->andOn('svai.item_no', '=', 'sil.linked_item_no')
            ->where('svai.item_no','=',$item->getItemNo())
            ->andWhere('sil.type','=',0)
            ->andWhere('svai.company','=',$item->getCompany())
            ->andWhere('svai.shop_code','=',$item->getShopCode())
            ->andWhere('svai.language_code','=',$item->getLanguageCode())
            ->orderBy('svai.item_no','asc')
            ->setConstructedQuery()->doQuery();
        if(!$querySuccess) return 0.00;
        $resArr = $db->getResultArray();
        return isset($resArr[0][0]) ? $resArr[0][0] : 0.00;
    }


}