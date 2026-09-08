<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

/**
 * Class WebshopItemDescriptionRepository
 * @package DynCom\dc\dcShop\classes
 */
class WebshopItemDescriptionRepository implements Repository
{
    const QUERY_DESCRIPTIONS = '
        SELECT 
            description,
            content
        FROM
          shop_item_description
        WHERE
              company = :company
          AND shop_code = :shop_code
          AND (
                  all_language_codes = 1
              OR    language_code = :shop_language_code            
            )
          AND item_no = :item_no
          AND show_in_header = :show_in_header
          AND marketplace_only = :marketplace_only
          AND to_delete = 0
        ORDER BY line_no ASC
    ';


    use genericRepositoryTrait;

    /**
     * @param GenericDBQueryWrapperInterface $db
     * @param WebshopItemDescriptionConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param WebshopItemDescriptionCollection $collection
     * @param $cacheAll
     */
    public function __construct(
        GenericDBQueryWrapperInterface $db,
        WebshopItemDescriptionConfig $config,
        CriteriaHelperInterface $criteriaValidationService,
        WebshopItemDescriptionCollection $collection,
        $cacheAll
    ) {
        $this->db = $db;
        $this->config = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection = $collection;
        $this->collectionEntryClassName = $this->collection->getEntryClassName();
        $this->cacheAll = $cacheAll;
    }

    /**
     * @return WebshopItemDescription
     */
    public function getNullObject()
    {
        return new WebshopItemDescription($this->config);
    }


    /**
     * @return WebshopItemDescriptionCollection
     */
    public function getEmptyCollection()
    {
        return $this->collection->getEmptyCollection();
    }

    /**
     * @param $company
     * @param $shopCode
     * @param $shopLanguageCode
     * @param $itemNo
     * @return WebshopItemDescriptionCollection
     */
    public function getAllHeaderDescriptionsForItemByPrimary($company, $shopCode, $shopLanguageCode, $itemNo)
    {
        $this->db->setQuery(self::QUERY_DESCRIPTIONS);
        $this->db->prepareQuery();
        $params = [
            [':company',$company,\PDO::PARAM_STR],
            [':shop_code',$shopCode,\PDO::PARAM_STR],
            [':shop_language_code',$shopLanguageCode,\PDO::PARAM_STR],
            [':show_in_header',1,\PDO::PARAM_INT],
            [':marketplace_only',0,\PDO::PARAM_INT],
        ];
        $this->db->bindParameters($params);
        $resArr = $this->db->getResultArray();
        $collection = $this->getEmptyCollection();
        foreach ($resArr as $row) {
            $obj = $this->getNullObject();
            $obj->mapFromArray($row);
            $collection->add($obj,true);
        }
        return $collection;
    }

    /**
     * @param $company
     * @param $shopCode
     * @param $shopLanguageCode
     * @param $itemNo
     * @return WebshopItemDescriptionCollection
     */
    public function getAllBodyDescriptionsForItemByPrimary($company, $shopCode, $shopLanguageCode, $itemNo)
    {
        $this->db->setQuery(self::QUERY_DESCRIPTIONS);
        $this->db->prepareQuery();
        $params = [
            [':company',$company,\PDO::PARAM_STR],
            [':shop_code',$shopCode,\PDO::PARAM_STR],
            [':shop_language_code',$shopLanguageCode,\PDO::PARAM_STR],
            [':show_in_header',0,\PDO::PARAM_INT],
            [':marketplace_only',0,\PDO::PARAM_INT],
        ];
        $this->db->bindParameters($params);
        $resArr = $this->db->getResultArray();
        $collection = $this->getEmptyCollection();
        foreach ($resArr as $row) {
            $obj = $this->getNullObject();
            $obj->mapFromArray($row);
            $collection->add($obj,true);
        }
        return $collection;
    }

    /**
     * @param WebshopItemInterface $item
     * @return WebshopItemDescriptionCollection
     */
    public function getAllHeaderDescriptionsForItem(WebshopItemInterface $item)
    {
        $company = $item->getCompany();
        $shopCode = $item->getShopCode();
        $shopLanguageCode = $item->getLanguageCode();
        $itemNo = $item->getItemNo();
        return $this->getAllHeaderDescriptionsForItemByPrimary($company,$shopCode,$shopLanguageCode,$itemNo);
    }

    /**
     * @param WebshopItemInterface $item
     * @return WebshopItemDescriptionCollection
     */
    public function getAllBodyDescriptionsForItem(WebshopItemInterface $item)
    {
        $company = $item->getCompany();
        $shopCode = $item->getShopCode();
        $shopLanguageCode = $item->getLanguageCode();
        $itemNo = $item->getItemNo();
        return $this->getAllBodyDescriptionsForItemByPrimary($company,$shopCode,$shopLanguageCode,$itemNo);
    }

}