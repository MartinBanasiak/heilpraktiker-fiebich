<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\dcShop\abstracts\WebshopItemDecorator;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;
use DynCom\dc\dcShop\interfaces\WebshopItemWithImages;
use DynCom\dc\dcShop\traits\genericDecoratorTrait;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 20.10.2015
 * Time: 01:47
 */
class WebshopItemDescriptionsDecorator extends WebshopItemDecorator implements WebshopItemWithImages
{
    use genericDecoratorTrait;

    /**
     * @var PDOQueryWrapper
     */
    private $db;
    /**
     * @var WebshopItemInterface
     */
    private $parentItem;
    private $imageData;
    private $mainImageData;

    private $defaultAllImagesQuery = '
          SELECT
            DISTINCT shop_item_file.*
          FROM
            shop_item_file
		  LEFT JOIN
		    shop_item
		    ON
		      shop_item.item_no = shop_item_file.item_no		 
		  WHERE
		        shop_item_file.type = 0
		    AND shop_item_file.company = :company
			AND shop_item_file.shop_code = :shop_code
			AND shop_item_file.filename <> \'\'
			AND (
			      shop_item_file.variant_code = :variant_code
			  OR  shop_item_file.variant_code=\'\'
			)
			AND (			  
			       (shop_item.item_no = :item_no
			    OR shop_item.item_no = :parent_item_no)			        
			  	AND (
			  	      shop_item_file.language_code = :language_code
			  	  OR  shop_item_file.all_language_codes = TRUE
			    )			  		  
			)
			ORDER BY
			  (`line_no` = :item_main_picture_line_no),
			  (`line_no` = :parent_item_main_picture_line_no),
			  line_no ASC
    ';

    private $defaultMainImageQuery = '
        SELECT
          id,
          item_no,
          type,
          line_no,
          description,
          filename,
          all_language_codes
		FROM
		  shop_item_file
		WHERE
		    type = 0
		  AND (
                (
                      item_no = :item_no
                  AND line_no = :item_main_picture_line_no
                )
		    OR  (
		          item_no = :parent_item_no
		      AND line_no = :parent_item_main_picture_line_no
		    )
		  )
		  AND company = :company
		  AND filename <> \'\'
		  ORDER BY line_no ASC
		LIMIT 1
    ';

    private $fallback1MainImageQuery = '
            SELECT
              shop_item_file.id,
              shop_item_file.item_no,
              shop_item_file.type,
              shop_item_file.line_no,
              shop_item_file.description,
              shop_item_file.filename,
              shop_item_file.all_language_codes
			FROM
			  shop_item_file
			LEFT JOIN
			  shop_item
			  ON (
			        shop_item.item_no = shop_item_file.item_no
			    AND shop_item.company = shop_item_file.company
			    AND shop_item.shop_code = shop_item_file.shop_code
			    AND shop_item.language_code = :language_code
			  )
			LEFT JOIN
			  shop_item AS parent_shop_item
			  ON (
			        parent_shop_item.item_no = shop_item_file.item_no
			    AND shop_item.company = shop_item_file.company
			    AND shop_item.shop_code = shop_item_file.shop_code
			    AND shop_item.language_code = :parent_language_code
			  )
			WHERE
			      shop_item_file.company = :company
			  AND shop_item_file.shop_code = :shop_code
			  AND (
			        shop_item_file.language_code = :language_code
			    OR  shop_item_file.all_language_codes = 1
			  )
			  AND shop_item_file.item_no = :item_no
			  AND (
			        shop_item_file.variant_code = :variant_code OR shop_item_file.variant_code=\'\')
			    AND shop_item_file.type = 0
				AND shop_item_file.filename <> \'\'
				AND (
				      shop_item_file.line_no = shop_item.main_picture_line_no
				  OR  shop_item_file.line_no=parent_shop_item.main_picture_line_no
				)
			ORDER BY
			  (shop_item_file.item_no = :item_no),
			  (shop_item_file.item_no = :parent_item_no),
			  line_no ASC
			LIMIT 1
    ';

    /**
     * WebshopItemDescriptionsDecorator constructor.
     * @param WebshopItemInterface $item
     * @param WebshopItemVariantService $variantService
     * @param PDOQueryWrapper $queryWrapper
     */
    public function __construct(WebshopItemInterface $item, WebshopItemVariantService $variantService, PDOQueryWrapper $queryWrapper)
    {
        parent::__construct($item);
        $this->db = $queryWrapper;
        $this->parentItem = $variantService->getParentItem($item);
    }

    /**
     * @return array|mixed
     */
    public function getImageData()
    {
        if(is_array($this->imageData)) {
            return $this->imageData;
        }
        $resArr = $this->getAllImagesQueryResult();
        if(count($resArr) > 0) {
            $this->imageData = $resArr;
        } else {
            $resArr = $this->getAllImagesQueryResultParentItem();
            if(count($resArr) > 0) {
                $this->imageData = $resArr;
            }
        }

        return $resArr;
    }

    /**
     * @return array
     */
    public function getMainImageData()
    {
        if(is_array($this->mainImageData)) {
            return $this->mainImageData;
        }
        $resArr = $this->getDefaultMainImgQueryResult();
        if(count($resArr) > 0) {
            $this->mainImageData = $resArr[0];
            return $this->mainImageData;
        }
        $resArr = $this->getFallback1MainImageQueryResult();
        if(count($resArr) > 0) {
            $this->mainImageData = $resArr[0];
            return $this->mainImageData;
        }


        // TODO: Implement fallbacks
    }

    /**
     * @return mixed
     */
    private function getDefaultMainImgQueryResult() {
        $paramArr = [
            [':item_no',$this->getItemNo(),\PDO::PARAM_STR],
            [':item_main_picture_line_no',$this->decoratedEntity->main_picture_line_no,\PDO::PARAM_INT],
            [':parent_item_no',$this->parentItem->getItemNo(),\PDO::PARAM_STR],
            [':parent_item_main_picture_line_no',$this->parentItem->main_picture_line_no,\PDO::PARAM_INT],
            [':company',$this->getCompany(),\PDO::PARAM_STR]
        ];

        $this->db->setQuery($this->defaultMainImageQuery);
        $this->db->prepareQuery();
        $this->db->bindParameters($paramArr);
        $this->db->executePreparedStatement();
        $resArr = $this->db->getResultArray();
        return $resArr;
    }

    /**
     * @return mixed
     */
    private function getAllImagesQueryResult()
    {
        $paramArr = [
            [':item_no',$this->getItemNo(),\PDO::PARAM_STR],
            [':company',$this->getCompany(),\PDO::PARAM_STR],
            [':shop_code',$this->getShopCode(),\PDO::PARAM_STR],
            [':language_code',$this->getLanguageCode(),\PDO::PARAM_STR],
            [':variant_code',$this->getVariantCode(),\PDO::PARAM_STR],
            [':item_main_picture_line_no',$this->decoratedEntity->main_picture_line_no,\PDO::PARAM_INT],
            [':parent_item_main_picture_line_no',$this->parentItem->main_picture_line_no,\PDO::PARAM_INT]
        ];
        $this->db->setQuery($this->defaultAllImagesQuery);
        $this->db->prepareQuery();
        $this->db->bindParameters($paramArr);
        $this->db->executePreparedStatement();
        $resArr = $this->db->getResultArray();
        return $resArr;
    }

    /**
     * @return mixed
     */
    private function getAllImagesQueryResultParentItem()
    {
        $paramArr = [
            [':item_no',$this->parentItem->getItemNo(),\PDO::PARAM_STR],
            [':company',$this->getCompany(),\PDO::PARAM_STR],
            [':shop_code',$this->getShopCode(),\PDO::PARAM_STR],
            [':language_code',$this->getLanguageCode(),\PDO::PARAM_STR],
            [':variant_code',$this->getVariantCode(),\PDO::PARAM_STR]
        ];
        $this->db->setQuery($this->defaultAllImagesQuery);
        $this->db->prepareQuery();
        $this->db->bindParameters($paramArr);
        $this->db->executePreparedStatement();
        $resArr = $this->db->getResultArray();
        return $resArr;
    }

    /**
     * @return mixed
     */
    private function getFallback1MainImageQueryResult()
    {
        $paramArr = [
            [':language_code',$this->getLanguageCode(),\PDO::PARAM_STR],
            [':parent_language_code',$this->parentItem->getLanguageCode(),\PDO::PARAM_STR],
            [':item_no',$this->getItemNo(),\PDO::PARAM_STR],
            [':parent_item_no',$this->parentItem->getItemNo(),\PDO::PARAM_STR],
            [':company',$this->getCompany(),\PDO::PARAM_STR],
            [':shop_code',$this->getShopCode(),\PDO::PARAM_STR],
            [':variant_code',$this->getVariantCode(),\PDO::PARAM_STR]
        ];
        $this->db->setQuery($this->defaultAllImagesQuery);
        $this->db->prepareQuery();
        $this->db->bindParameters($paramArr);
        $this->db->executePreparedStatement();
        $resArr = $this->db->getResultArray();
        return $resArr;
    }
}