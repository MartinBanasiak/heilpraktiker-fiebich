<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\SelectionCriteriaHelper;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 19.01.2015
 * Time: 14:08
 */
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;

/**
 * Class CategoryRepository
 */
class CategoryRepository implements Repository{

    use genericRepositoryTrait;

    const QUERY_ITEM_CATEGORY = '
          SELECT
            sc.*
          FROM
            shop_item_has_category sihc
          LEFT JOIN 
            shop_category sc
            ON (
                  sc.company = sihc.company
              AND sc.shop_code = sihc.category_shop_code
              AND sc.language_code = sihc.category_language_code
              AND sc.line_no = sihc.category_line_no
            )
            
          WHERE
              sihc.company = :company
            AND
              sihc.shop_code = :itemShopCode
            AND
              sihc.language_code = :languageCode
            AND
              (
                  sihc.item_no = :itemNo
                OR
                  sihc.item_no = :parentItemNo
              )
            AND
              sihc.category_shop_code = :categoryShopCode
            AND
              sihc.category_language_code = :languageCode
          ORDER BY
            sihc.category_line_no ASC
        ';

    protected const QUERY_CHILDREN = '
      SELECT * FROM 
        shop_category 
      WHERE 
          company = :company 
        AND 
          shop_code = :shop_code 
        AND 
          language_code = :language_code 
        AND 
          (:parent_line_no = 0 OR parent_line_no = :parent_line_no)
        AND 
          (active = 1 OR active = :only_active)
        ORDER BY sorting ASC
    ';

    /**
     * CategoryRepository constructor.
     * @param GenericDBQueryWrapperInterface $db
     * @param CategoryConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param CategoryCollection $collection
     * @param $cacheAll
     */
    public function __construct( GenericDBQueryWrapperInterface $db, CategoryConfig $config, CriteriaHelperInterface $criteriaValidationService, CategoryCollection $collection, $cacheAll=false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @return Category
     */
    public function getNullObject() {
        return new Category($this->config);
    }

    /**
     * @param ShopLanguage $currShopLanguage
     * @return Category
     */
    public function getFromRequest(ShopLanguage $currShopLanguage) {
        $nullObj = $this->getNullObject();
        $isShop = (isset($_GET['level_1']) && $_GET['level_1'] === 'shop');
        if(!$isShop) {return $nullObj;}
        $company = $currShopLanguage->company;
        $shop_code = $currShopLanguage->shop_code;
        $language_code = $currShopLanguage->code;
        $code = '';
        $codeIsset = false;
        for($i = 5; $i > 0; --$i) {
            if(isset($_GET['slevel_' . $i])) {
                $filtered = filter_var($_GET['slevel_' . $i],FILTER_SANITIZE_SPECIAL_CHARS);
                if($filtered !== $_GET['slevel_' . $i]) {return $nullObj;}
                $code = $_GET['slevel_' . $i];
                $codeIsset = true;
                break;
            }
        }
        if(!$codeIsset){return $nullObj;}
        $criteriaArray = array(
          array(
              array('company','=',$company),
              array('shop_code','=',$shop_code),
              array('language_code','=',$language_code),
              array('code','=',$code)
          )
        );

        $categoryCollection = $this->_findByCriteria($criteriaArray);
        if(count($categoryCollection) !== 1) {
            return $nullObj;
        }

        $it = $categoryCollection->getIterator();
        $it->rewind();
        $category = $it->current();
        return $category;

    }

    public function getAllChildrenSorted($parentLineNo,CurrShopConfiguration $currShopConfiguration,$onlyActive = false)
    {
        $coll = $this->collection->getEmptyCollection();
        $params = [
            [':company',$currShopConfiguration->getCompany(),\PDO::PARAM_STR],
            [':shop_code',$currShopConfiguration->getShopCode(),\PDO::PARAM_STR],
            [':language_code',$currShopConfiguration->getShopLanguageCode(),\PDO::PARAM_STR],
            [':parent_line_no',(int)$parentLineNo,\PDO::PARAM_INT],
            [':only_active',(int)$onlyActive,\PDO::PARAM_INT],
        ];


        $this->db->setQuery(self::QUERY_CHILDREN)
            ->prepareQuery();

        $category = new Category(new CategoryConfig());
        $this->db->bindParameters($params);
        if ($this->db->executePreparedStatement()) {
            $resArr = $this->db->getResultArray();
            foreach ($resArr as $row) {
                $newCategory = clone $category;
                $newCategory->mapFromArray($row);
                $coll->add($newCategory,true);
            }
        }
        return $coll;
    }


    public function getAllDescendents($parentLineNo, CurrShopConfiguration $currShopConfiguration)
    {
        //@TODO: Write!
    }

    public function getAllAncestors($subCategoryLineNo, CurrShopConfiguration $currShopConfiguration)
    {

    }

}