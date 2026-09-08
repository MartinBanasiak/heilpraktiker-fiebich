<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:26
 */
class WebshopItemRepository implements Repository
{

    use genericRepositoryTrait;

    /**
     * @param PDOQueryWrapper $db
     * @param WebshopItemConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param WebshopItemCollection $collection
     * @param $cacheAll
     */
    public function __construct( PDOQueryWrapper $db, WebshopItemConfig $config, CriteriaHelperInterface $criteriaValidationService, WebshopItemCollection $collection, $cacheAll=false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @return WebshopItem
     */
    public function getNullObject() {
        return new WebshopItem($this->config);
    }

    /**
     * @param WebshopItemInterface $item
     * @return WebshopItemInterface
     */
    public function getParentItem(WebshopItemInterface $item) {
        $itemNo = $item->getItemNo();
        $parentItemNo = $item->getParentItemNo();
        if($parentItemNo === '') return $item;
        if($parentItemNo === $itemNo) return $item;
        $altPrimary = [
            'company' => $item->getCompany(),
            'shop_code' => $item->getShopCode(),
            'language_code' => $item->getLanguageCode(),
            'item_no' => $parentItemNo
        ];
        return $this->findByAltPrimary($altPrimary);
    }

    /**
     * @param WebshopItemInterface $item
     * @return WebshopItemCollection
     * @throws \ErrorException
     */
    public function getWebshopVariants(WebshopItemInterface $item) {
        $collection = $this->collection->getEmptyCollection();
        if(($item->getVariantCode() !== '') || ($item->getParentItemNo() !== '')) {
            //$collection->add($item);
            return $collection;
        }
        $company = $item->getCompany();
        $shopCode = $item->getShopCode();
        $languageCode = $item->getLanguageCode();
        $db = $this->db;
        $querySuccess =
            $db->select('si.*')
                ->from('shop_item_link','sil')
                ->join('inner','shop_item','si')
                    ->on('si.company','=','sil.company')
                    ->andOn('si.shop_code', '=', 'sil.shop_code')
                    ->andOn('si.language_code', '=', $languageCode,true)
                    ->andOn('si.item_no', '=', 'sil.linked_item_no')
                ->where('sil.item_no','=',$item->getItemNo())
                ->andWhere('sil.type','=',0)
                ->andWhere('sil.company','=',$company)
                ->andWhere('sil.shop_code','=',$shopCode)
                ->orderBy('sil.linked_item_no','asc')
                ->setConstructedQuery();
        $db->doQuery();

        $arr = $db->getResultArray();
        $collection = $this->collection->getEmptyCollection();
        $item = new WebshopItem(new WebshopItemConfig());
        foreach($arr as $row) {
            $clone = clone $item;
            $clone->mapFromArray($row);
            $collection->add($clone);
        }
        return $collection;
    }

    /**
     * @param WebshopItemInterface $item
     * @return WebshopItemCollection
     * @throws \ErrorException
     */
    public function getActiveWebshopVariants(WebshopItemInterface $item) {
        $collection = $this->collection->getEmptyCollection();
        if(($item->getVariantCode() !== '') || ($item->getParentItemNo() !== '')) {
            //$collection->add($item);
            return $collection;
        }
        $company = $item->getCompany();
        $shopCode = $item->getShopCode();
        $languageCode = $item->getLanguageCode();
        $db = $this->db;
        $querySuccess =
            $db->select('si.*')
                ->from('shop_item_link','sil')
                ->join('right','shop_item','si')
                    ->on('si.company','=','sil.company')
                    ->andOn('si.shop_code', '=', 'sil.shop_code')
                    ->andOn('si.language_code', '=', $languageCode, true)
                    ->andOn('si.item_no', '=', 'sil.linked_item_no')
                ->where('sil.item_no','=',$item->getItemNo())
                ->andWhere('sil.type','=',0)
                ->andWhere('sil.company','=',$company)
                ->andWhere('sil.shop_code','=',$shopCode)
                ->andWhere('si.active','=',1)
                ->orderBy('sil.linked_item_no','asc')
                ->setConstructedQuery();
        $db->doQuery();

        $arr = $db->getResultArray();
        $collection = $this->collection->getEmptyCollection();
        $item = new WebshopItem(new WebshopItemConfig());
        foreach($arr as $row) {
            $clone = clone $item;
            $clone->mapFromArray($row);
            $collection->add($clone);
        }
        return $collection;
    }

    /**
     * @param $itemID
     * @return WebshopItemCollection
     */
    public function getWebshopVariantsByItemId($itemID)
    {
        $item = $this->findByID($itemID);
        return $this->getWebshopVariants($item);
    }

    /**
     * @param array $altPrimary
     * @return WebshopItemCollection
     */
    public function getWebshopVariantsByAltPrimary(array $altPrimary)
    {
        $item = $this->findByAltPrimary($altPrimary);
        return $this->getWebshopVariants($item);
    }


    public function getWebshopItemByCriteria(array $criteria)
    {
        $item = $this->findByCriteria($criteria);
        return $item;
    }

}