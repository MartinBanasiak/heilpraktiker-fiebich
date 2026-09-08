<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\classes\SelectionCriteriaHelper;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 24.10.2016
 * Time: 11:23
 */
class WebshopItemAttributeService
{

    protected const QUERY_ALL_ITEM_ATTRIBUTES = '
        SELECT
          sal.id AS \'id\',
          :item_shop_code AS \'shop_code\', 
          :item_language_code \'language_code\', 
          :item_no AS \'item_no\', 
          :variant_code AS \'variant_code\',
          sa.code AS \'attribute_code\',
          CASE WHEN (:item_language_code = :shop_default_language_code OR sata.id IS NULL) THEN sa.description ELSE sata.description END AS \'attribute_description\',
          CASE 
            WHEN (sa.data_type = 0 AND sato.id IS NULL) THEN sao.description
            WHEN (sa.data_type = 0 AND sato.id > 0) THEN sato.description
            WHEN sa.data_type = 1 THEN sal.value_integer 
            WHEN sa.data_type = 2 THEN sal.value_decimal 
            WHEN sa.data_type = 3 THEN sal.value_bool 
            WHEN (sa.data_type = 4 AND satl.id IS NULL) THEN sal.value_text
            WHEN (sa.data_type = 4 AND satl.id > 0) THEN satl.description 
          END AS \'attribute_value\', 
          sa.data_type AS \'attribute_value_type\',
          sa.display_type AS \'attribute_display_type\',
          sao.icon AS \'icon_filename\',
          sao.link AS \'option_link\',
          sao.filter AS \'filter_expression\'                      
        FROM
          shop_attribute_link sal
        INNER JOIN
          shop_attribute sa 
          ON (
              sa.company = sal.company
            AND
              sa.code = sal.attribute_code                                 
          )
        LEFT JOIN
          shop_attribute_option sao
          ON (
                  sa.data_type = 0
              AND sao.company = sa.company
              AND sao.attribute_code = sa.code
              AND sao.code = sal.value_option
          )
        LEFT JOIN 
          shop_attribute_translation sata ##for attribute
          ON (
                sata.company = sa.company
            AND sata.attribute_code = sa.code
            AND sata.language_code = :item_language_code
            AND sata.type = 0
          )
        LEFT JOIN
          shop_attribute_translation satl ##for attribute link
          ON (
                   sata.company = sa.company
            AND satl.attribute_code = sa.code
            AND satl.language_code = :item_language_code
            AND satl.attribute_link_no = :item_no
            AND satl.type = 1       
          )
        LEFT JOIN
          shop_attribute_translation sato ##for attribute option
          ON (
                sato.company = sa.company
            AND sato.attribute_code = sa.code
            AND sato.language_code = :item_language_code
            AND sato.attribute_link_no = sao.code
            AND sato.type = 2       
          )
        WHERE
              sal.company = :item_company
          #AND sal.shop_code = :item_shop_code
          AND sal.type = 0
          AND sal.no = :item_no
        ORDER BY 
          sal.sorting ASC        
    ';

    /**
     * @var PDOQueryWrapper
     */
    private $db;
    private $criteriaHelper;
    protected $results = [];


    /**
     * WebshopItemAttributeService constructor.
     * @param PDOQueryWrapper $db
     * @param SelectionCriteriaHelper $criteriaHelper
     */
    public function __construct(PDOQueryWrapper $db, SelectionCriteriaHelper $criteriaHelper)
    {
        $this->db = $db;
        $this->criteriaHelper = $criteriaHelper;
    }

    /**
     * @param $company
     * @param $shopCode
     * @param $shopLanguageCode
     * @param $targetShopDefaultLanguageCode
     * @param $itemNo
     * @param $variantCode
     * @return WebshopItemAttributeCollection|mixed
     * @throws \ErrorException
     */
    public function getAllForItemByPrimary($company, $shopCode, $shopLanguageCode, $targetShopDefaultLanguageCode, $itemNo, $variantCode)
    {
        $key = md5($company . '|' . $shopCode . '|' . $shopLanguageCode . '|' . $targetShopDefaultLanguageCode . '|' . $itemNo . '|' . $variantCode);
        if (array_key_exists($key,$this->results)) {
            return $this->results[$key];
        }

        $this->db->setQuery(self::QUERY_ALL_ITEM_ATTRIBUTES);
        $this->db->prepareQuery();

        $params = [
            [':item_company',$company,\PDO::PARAM_STR],
            [':item_shop_code',$shopCode,\PDO::PARAM_STR],
            [':item_language_code',$shopLanguageCode,\PDO::PARAM_STR],
            [':shop_default_language_code',$targetShopDefaultLanguageCode,\PDO::PARAM_STR],
            [':item_no',$itemNo,\PDO::PARAM_STR],
            [':variant_code',$variantCode,\PDO::PARAM_STR],
        ];

        $this->db->bindParameters($params);
        $this->db->executePreparedStatement();
        $resArr = $this->db->getResultArray();
        $config = new WebshopItemAttributeConfig();
        $collection = new WebshopItemAttributeCollection($config,$this->criteriaHelper);
        foreach ($resArr as $row) {
            $obj = new WebshopItemAttribute($config);
            $obj->mapFromArray($row);
            $collection->add($obj,true);
        }
        $this->results[$key] = $collection;
        return $collection;

    }

    /**
     * @param WebshopItemInterface $item
     * @param Shop $targetShop
     * @return WebshopItemAttributeCollection|mixed
     * @throws \ErrorException
     */
    public function getAllForItem(WebshopItemInterface $item, Shop $targetShop)
    {
        $shopDefaultLanguageCode = $targetShop->default_language_code;
        $company = $item->getCompany();
        $itemShopCode = $item->getShopCode();
        $itemShopLanguageCode = $item->getLanguageCode();
        $itemNo = $item->getItemNo();
        $variantCode = $item->getVariantCode();
        return $this->getAllForItemByPrimary($company,$itemShopCode,$itemShopLanguageCode,$shopDefaultLanguageCode,$itemNo,$variantCode);
    }

}