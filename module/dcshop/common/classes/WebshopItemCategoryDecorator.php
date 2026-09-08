<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\traits\hookableTrait;
use DynCom\dc\dcShop\abstracts\WebshopItemDecorator;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;
use DynCom\dc\dcShop\interfaces\WebshopItemWithCategories;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 16.10.2015
 * Time: 15:40
 */
class WebshopItemCategoryDecorator extends WebshopItemDecorator implements WebshopItemWithCategories
{
    use hookableTrait;

    private $db;
    private $categoryShopCode;
    private $categories;
    private $rootLineNos;

    private static $query = '
          SELECT
            category_line_no
          FROM
            shop_item_has_category
          WHERE
              company = :company
            AND
              shop_code = :itemShopCode
            AND
              language_code = :languageCode
            AND
              (
                  item_no = :itemNo
                OR
                  item_no = :parentItemNo
              )
            AND
              category_shop_code = :categoryShopCode
            AND
              category_language_code = :languageCode
          ORDER BY
            category_line_no ASC
        ';

    private static $parentCategoryQuery = '
      SELECT parent_line_no FROM shop_category WHERE company = :company AND shop_code = :categoryShopCode AND language_code = :categoryLanguageCode AND line_no = :categoryLineNo 
    ';

    /**
     * WebshopItemCategoryDecorator constructor.
     * @param WebshopItemInterface $item
     * @param PDOQueryWrapper $db
     * @param $categoryShopCode
     */
    public function __construct(WebshopItemInterface $item, PDOQueryWrapper $db, $categoryShopCode)
    {
        parent::__construct($item);
        $this->db = $db;
        $this->categoryShopCode = $categoryShopCode;
        $this->initialize();
    }

    private function initialize()
    {
        $company = $this->decoratedEntity->getCompany();
        $itemShopCode = $this->decoratedEntity->getShopCode();
        $categoryShopCode = $this->categoryShopCode;
        $languageCode = $this->decoratedEntity->getLanguageCode();
        $itemNo = $this->decoratedEntity->getItemNo();
        $parentItemNo = $this->decoratedEntity->getParentItemNo();

        $paramArr = [
            [':company',$company,\PDO::PARAM_STR],
            [':itemShopCode',$itemShopCode,\PDO::PARAM_STR],
            [':categoryShopCode',$categoryShopCode,\PDO::PARAM_STR],
            [':languageCode',$languageCode,\PDO::PARAM_STR],
            [':itemNo',$itemNo,\PDO::PARAM_STR],
            [':parentItemNo',$parentItemNo,\PDO::PARAM_STR]
        ];

        $query = self::$query;

        $this->db->setQuery($query)->prepareQuery();
        $this->db->bindParameters($paramArr);
        $this->db->executePreparedStatement();

        $categoryArr = [];
        if($this->db->getNoOfReturnedRows() > 0) {
            $resArr = $this->db->getResultArray();
            foreach($resArr as $row) {
                $rowArr = [
                    'company' => $company,
                    'shop_code' => $categoryShopCode,
                    'language_code' => $languageCode,
                    'line_no' => $row['category_line_no']
                ];
                $key = $company . '|' . $categoryShopCode . '|' . $languageCode . '|' . $row['category_line_no'];
                $categoryArr[$key] = $rowArr;
                $parentLineNos = $this->getCategoryParentLineNos($row['category_line_no']);
                foreach ($parentLineNos as $parentLineNo) {
                    $key = $company . '|' . $categoryShopCode . '|' . $languageCode . '|' . $parentLineNo;
                    $categoryArr[$key] = [
                        'company' => $company,
                        'shop_code' => $categoryShopCode,
                        'language_code' => $languageCode,
                        'line_no' => $parentLineNo,
                    ];
                }
            }
        }
        $this->categories = $categoryArr;
    }

    /**
     * @param $categoryCompany
     * @param $categoryShopCode
     * @param $categoryShopLanguageCode
     * @param $categoryLineNo
     * @return bool
     */
    public function isInCategoryPrimary($categoryCompany, $categoryShopCode, $categoryShopLanguageCode, $categoryLineNo)
    {
        $key = $categoryCompany . '|' . $categoryShopCode . '|' . $categoryShopLanguageCode . '|' . $categoryLineNo;
        return(array_key_exists($key,$this->categories));
    }

    /**
     * @param $categoryLineNo
     * @return bool
     */
    public function isInCategory($categoryLineNo)
    {
        $key = $this->getCompany() . '|' . $this->categoryShopCode. '|' . $this->getLanguageCode(). '|' . $categoryLineNo;
        return(array_key_exists($key,$this->categories));
    }

    /**
     * @return array
     */
    public function getCategoryArr()
    {
        return array_values($this->categories);
    }

    /**
     * @return mixed
     */
    public function getCategoryShopCode()
    {
        return $this->categoryShopCode;
    }

    private function getCategoryParentLineNos($categoryLineNo)
    {
        $parentLineNos = [];
        $currLineNo = (int)$categoryLineNo;
        do {
            $currParentLineNo = 0;
            $params = [
                [':company',$this->getCompany(),\PDO::PARAM_STR],
                [':categoryShopCode',$this->categoryShopCode,\PDO::PARAM_STR],
                [':categoryLanguageCode',$this->getLanguageCode(),\PDO::PARAM_STR],
                [':categoryLineNo',(int)$currLineNo,\PDO::PARAM_INT],
            ];
            $this->db->setQuery(self::$parentCategoryQuery)->prepareQuery();
            $this->db->bindParameters($params);
            $this->db->executePreparedStatement();
            if ($this->db->getNoOfReturnedRows() > 0) {
                $resArr = $this->db->getResultArray();
                $currParentLineNo = isset($resArr[0]['parent_line_no']) ? (int)$resArr[0]['parent_line_no'] : 0;
                if ($currParentLineNo > 0) {
                    $parentLineNos[] = $currParentLineNo;
                    $currLineNo = $currParentLineNo;
                }
            }
        } while ($currParentLineNo > 0);
        return $parentLineNos;
    }
}
