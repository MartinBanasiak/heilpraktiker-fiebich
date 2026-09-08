<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 20.09.2017
 * Time: 16:37
 */

namespace DynCom\dc\dcShop\classes;


use DynCom\dc\dcShop\interfaces\CategoryTreeNodeInterface;

class CategoryTreeBuilder
{
    protected const QUERY_GET_CATEGORY_BY_PRIMARY = '
        SELECT *
        FROM shop_category
        WHERE 
              company = :company 
          AND shop_code = :category_shop_code 
          AND language_code = :language_code 
          AND line_no = :line_no          
    ';
    protected const QUERY_CATEGORY_MAX_LEVEL = '
        SELECT MAX(level) as max_level 
        FROM shop_category 
        WHERE 
              company=:company 
          AND shop_code=:category_shop_code 
          AND language_code=:language_code
    ';
    protected const QUERY_CATEGORY_BY_LEVEL_SORTING = '
        SELECT *
        FROM shop_category
        WHERE 
              level = :level 
          AND company= :company 
          AND shop_code= :category_shop_code 
          AND language_code= :language_code
        ORDER BY 
          parent_line_no ASC, 
          line_no ASC;
    ';

    /**
     * @var \PDO
     */
    protected $db;

    public function __construct(\PDO $shopDB)
    {
        $this->db = $shopDB;
    }

    public function getCategoryTree(string $company, string $categoryShopCode, string $languageCode) : CategoryTreeNodeInterface
    {
        $elementsByLineNo = [];
        $maxLevel = $this->getMaxCategoryLevel($this->db,$company,$categoryShopCode,$languageCode);
        $levelNodesByParent = [];
        $nullCategoryEntity = new Category(new CategoryConfig());
        $root = new CategoryTreeNode(0, $company, $categoryShopCode, $languageCode, 0, '', $nullCategoryEntity);
        $elementsByLineNo[0] = $root;
        for ($i = 0; $i<=$maxLevel; $i++) {
            $currChild = null;
            $stmt = $this->db->prepare(self::QUERY_CATEGORY_BY_LEVEL_SORTING);
            $stmt->bindValue(':company',$company,\PDO::PARAM_STR);
            $stmt->bindValue(':category_shop_code',$categoryShopCode,\PDO::PARAM_STR);
            $stmt->bindValue(':language_code',$languageCode,\PDO::PARAM_STR);
            $stmt->bindValue(':level',$i,\PDO::PARAM_INT);
            $stmt->execute();
            $j = 0;
            /**
             * @var $previousSibling CategoryTreeNode|null
             * @var $leftmostLevelNode CategoryTreeNode|null
             * @var $root CategoryTreeNode|null
             */
            $previousSibling = null;
            $lastParentLineNo = null;
            $currParentLineNo = null;
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $currParentLineNo = (int)$row['parent_line_no'];
                if (!array_key_exists($row['line_no'],$elementsByLineNo)) {
                    $categoryEntity = clone $nullCategoryEntity;
                    $categoryEntity->mapFromArray($row);
                    $currCategory = new CategoryTreeNode((int)$row['id'], (string)$row['company'], (string)$row['shop_code'], (string)$row['language_code'], (int)$row['line_no'], (string)$row['code'], $categoryEntity);
                    $elementsByLineNo[$currCategory->getLineNo()] = $currCategory;
                } else {
                    $currCategory = $elementsByLineNo[$row['line_no']];
                }

                if (array_key_exists($currParentLineNo,$elementsByLineNo)) {
                    $parent = $elementsByLineNo[$currParentLineNo];
                    $parent->addChild($currCategory);
                }

                if (null !== $previousSibling && $lastParentLineNo === $currParentLineNo) {
                    $previousSibling->setRightSibling($currCategory);
                }
                if (!array_key_exists($currParentLineNo,$elementsByLineNo)) {
                    $stmt = $this->db->prepare(self::QUERY_GET_CATEGORY_BY_PRIMARY);
                    $stmt->bindValue(':company',$company,\PDO::PARAM_STR);
                    $stmt->bindValue(':category_shop_code',$categoryShopCode,\PDO::PARAM_STR);
                    $stmt->bindValue(':language_code',$languageCode,\PDO::PARAM_STR);
                    $stmt->bindValue(':line_no',$currParentLineNo);
                    $stmt->execute();
                    $parentRow = $stmt->fetch(\PDO::FETCH_ASSOC);
                    $parentCategoryEntity = clone $nullCategoryEntity;
                    $parentCategoryEntity->mapFromArray($parentRow);
                    $currParentCategory = new CategoryTreeNode((int)$parentRow['id'], (string)$parentRow['company'], (string)$parentRow['shop_code'], (string)$parentRow['language_code'], (int)$parentRow['line_no'], (string)$parentRow['code'], $categoryEntity);
                    $elementsByLineNo[$currParentCategory->getLineNo()] = $currParentCategory;
                }

                $previousSibling = $currCategory;
                $lastParentLineNo = $currParentLineNo;
                $j++;
            }
            /**
             * @var $currChild CategoryTreeNode
             * @var $parent CategoryTreeNode
             */
            /*foreach ($levelNodesByParent as $parentLineNo => $currChild) {
                $parent = $elementsByLineNo[$parentLineNo];
                if ($parent instanceof CategoryTreeNodeInterface && $currChild instanceof CategoryTreeNodeInterface) {
                    $parent->addChild($currChild);
                }
            }*/
        }
        return $root;
    }

    protected function getMaxCategoryLevel(\PDO $pdo, string $company, string $categoryShopCode, string $languageCode) : int
    {
        $stmt = $pdo->prepare(self::QUERY_CATEGORY_MAX_LEVEL);
        $stmt->bindValue(':company',$company,\PDO::PARAM_STR);
        $stmt->bindValue(':category_shop_code',$categoryShopCode,\PDO::PARAM_STR);
        $stmt->bindValue(':language_code',$languageCode,\PDO::PARAM_STR);
        $stmt->execute();
        $val = (int)$stmt->fetchColumn(0);
        return $val;
    }


}