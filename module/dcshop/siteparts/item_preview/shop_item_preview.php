<?
use DynCom\dc\dcShop\classes\WebshopItemBuilder;

function shop_item_preview_show($sitepart_id ) {
    $rootDir = rtrim(dirname(dirname(dirname(dirname(__DIR__)))),'/\\');
    require_once $rootDir . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'shop_functions.inc.php';
    require_once $rootDir . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'category_functions.inc.php';

    $query  = "SELECT * FROM main_shop_item_preview WHERE id = '" . $sitepart_id . "' LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {

        $item_preview_sitepart = @mysqli_fetch_array($result);

        $item_no_string = $item_preview_sitepart['item_no_string'];
        if (strpos($item_no_string, ';') !== FALSE) {
            $item_nos = explode(';', $item_no_string);
        } elseif (strpos($item_no_string, ',') !== FALSE) {
            $item_nos = explode(',', $item_no_string);
        } elseif (strpos($item_no_string, ' ') !== FALSE) {
            $item_nos = explode(' ', $item_no_string);
        } elseif (strlen($item_no_string) > 0) {
            $item_nos = array($item_no_string);
        }
        $has_item_nos = (count($item_nos) > 0);


        $category_string = $item_preview_sitepart['category_code_string'];
        if (strpos($category_string, ';') !== FALSE) {
            $category_codes = explode(';', $category_string);
        } elseif (strpos($category_string, ',') !== FALSE) {
            $category_codes = explode(',', $category_string);
        } elseif (strpos($category_string, ' ') !== FALSE) {
            $category_codes = explode(' ', $category_string);
        } elseif (strlen($category_string) > 0) {
            $category_codes = array($category_string);
        }
        $has_category = (count($category_codes) > 0);

        $shopquery = "SELECT * FROM shop_shop WHERE company = '" . $GLOBALS['language']['company'] . "' AND code = '" . $GLOBALS['language']['shop_code'] . "'";

        $shopresult = @mysqli_query($GLOBALS['mysql_con'], $shopquery);
        if (@mysqli_num_rows($shopresult) == 1) {
            $shop                                 = mysqli_fetch_assoc($shopresult);
            $shop['use_items_from_shop_code']     = (empty($shop['use_items_from_shop_code']) ? $shop['code'] : $shop['use_items_from_shop_code']);
            $shop['use_categorys_from_shop_code'] = (empty($shop['use_categorys_from_shop_code']) ? $shop['code'] : $shop['use_categorys_from_shop_code']);
            if ($has_item_nos) {
                $item_no_snippet = 'UNION SELECT svai.* FROM shop_view_active_item svai WHERE (
				svai.company = \'' . $GLOBALS['language']['company'] . '\'
				AND
				svai.shop_code = \'' . $shop['use_items_from_shop_code'] . '\'
				AND
				svai.language_code = \'' . $GLOBALS['shop_language']['code'] . '\'
				AND
				svai.item_no IN (';
                $i               = 0;
                foreach ($item_nos as $no) {
                    $no = trim($no);
                    if ($i > 0) {
                        $item_no_snippet .= ',';
                    }
                    $item_no_snippet .= '\'' . $no . '\'';
                    ++$i;
                }
                $item_no_snippet .= ') )';
            } else {
                $item_no_snippet = '';
            }

            if ($has_category) {
                $category_codes_query_snippet = ' IN (';
                $i                            = 0;

                if ($item_preview_sitepart["include_sub_categories"]) {
                    if (!isset($GLOBALS['curr_category_tree']) || !($GLOBALS['curr_category_tree'] instanceof \DynCom\dc\dcShop\classes\CategoryTreeNode)) {
                        /** @var \DynCom\dc\dcShop\classes\CategoryTreeBuilder $treeBuilder */
                        $treeBuilder = new \DynCom\dc\dcShop\classes\CategoryTreeBuilder(get_main_db_pdo_from_env_single_instance());
                        /** @var \DynCom\dc\dcShop\classes\CategoryTreeNode $currCategoryTree */
                        $currCategoryTree = $treeBuilder->getCategoryTree($GLOBALS['language']['company'], $shop['use_categorys_from_shop_code'], $GLOBALS['language']['shop_language_code']);
                    } else {
                        $currCategoryTree = $GLOBALS['curr_category_tree'];
                    }
                }

                foreach ($category_codes as $code) {
                    $code = trim($code);
                    if ($i > 0) {
                        $category_codes_query_snippet .= ',';
                    }
                    $category_codes_query_snippet .= '\'' . $code . '\'';

                    if ($item_preview_sitepart["include_sub_categories"]) {
                        $subCategories = null;
                        $visitorFunction = function(\DynCom\dc\dcShop\interfaces\CategoryTreeNodeInterface $node) use(&$subCategories,$code)
                        {
                            if ($node->getCode() === $code)
                            {
                                $subCategories = $node->getChildren();
                            }
                        };
                        $visitor = new \DynCom\dc\dcShop\classes\TreeNodeVisitor($visitorFunction);
                        $visitor->visitByLevel($currCategoryTree);

                        if (null !== $subCategories) {
                            foreach ($subCategories as $subCategory) {
                                $code = trim($code);
                                if ($i > 0) {
                                    $category_codes_query_snippet .= ',';
                                }
                                $category_codes_query_snippet .= '\'' . $subCategory->getCode() . '\'';
                                ++$i;
                            }
                        }
                    }
                    ++$i;
                }
                $category_codes_query_snippet .= ') ';
            } else {
                $category_codes_query_snippet = '!= \'\'';
            }

            switch ($shop['shop_typ']) {
                case 0:
                    $shop_type_snippet = 'b2b';
                    break;
                case 1:
                    $shop_type_snippet = 'b2c';
                    break;
                case 2:
                    $shop_type_snippet = 'salesperson';
                    break;
                case 3:
                    $shop_type_snippet = 'catalog';
                    break;
            }
            $showItemListPath = $rootDir . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . $shop_type_snippet . DIRECTORY_SEPARATOR . 'show_item_list.inc.php';
            require_once $showItemListPath;
            if (empty($shop['use_categorys_from_shop_code'])) {
                $shop['use_categorys_from_shop_code'] = $shop['code'];
            }
            if ($has_category) {
                $line_no_query       = "
					SELECT 
						CONCAT(' IN (',GROUP_CONCAT(line_no SEPARATOR ','),')') 
					FROM 
						`shop_category` 
					WHERE 
						`shop_code` = '" . $shop['use_categorys_from_shop_code'] . "' 
					  AND 
						`language_code` = '" . $GLOBALS['language']['shop_language_code'] . "' 
					  AND 
						`code` $category_codes_query_snippet
					  AND
					    company = '" . $GLOBALS['language']['company'] . "'				 
				";

                $line_no_result      = @mysqli_query($GLOBALS['mysql_con'], $line_no_query);
                $line_no_snippet_pre = @mysqli_result($line_no_result, 0);
                $line_no_snippet     = ((strlen($line_no_snippet_pre) > 0) ? ' AND sihc.category_line_no ' . $line_no_snippet_pre . ' ' : '');
            } else {
                if ($has_item_nos) {
                    $line_no_snippet = " AND sihc.category_line_no IS NULL";
                } else {
                    $line_no_snippet = "";
                }
            }

            $limit_no         = (int)$item_preview_sitepart['no_of_items'];
            $limit_no_snippet = (($limit_no > 0) ? ' LIMIT ' . $limit_no . ' ' : ' LIMIT 12 ');

            $item_query  = "
			SELECT DISTINCT svai.*
			FROM 
				shop_view_active_item svai,
				shop_item_has_category sihc 
			WHERE (
					sihc.item_no = svai.item_no
				  AND
					svai.company = sihc.company
				  AND
					svai.shop_code = sihc.shop_code
				  AND
					svai.language_code = sihc.language_code
				  AND
					sihc.shop_code = '" . $shop['use_items_from_shop_code'] . "'
				  AND
					sihc.language_code = '" . $GLOBALS['language']['shop_language_code'] . "'
				  AND
					sihc.category_shop_code = '" . $shop['use_categorys_from_shop_code'] . "'
				  AND
					sihc.category_language_code = '" . $GLOBALS['language']['shop_language_code'] . "'
				  AND
					sihc.company = '" . $GLOBALS['language']['company'] . "'
				  $line_no_snippet
				) $item_no_snippet 
			ORDER BY RAND()
			$limit_no_snippet
			";
            $item_result = null;
            //MB --- OOP ---
            if(isset($GLOBALS['IOC'])) {
                $IOCContainer = $GLOBALS['IOC'];
                if ($IOCContainer instanceof \Dice\Dice) {
                    $itemBuilder = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemBuilder');
                    if ($itemBuilder instanceof WebshopItemBuilder) {
                        //$objArr = $itemBuilder->getAllWebshopItemsDecoratedForItemListAsArray($itemBuilder->buildWebshopItemsFromItemQuery($item_query));
                        $item_result = [];
                        foreach ($itemBuilder->buildWebshopItemsFromItemQuery($item_query) as $itemObjectPre) {
                            $itemObj = $itemBuilder->getAllWebshopItemsDecoratedForItemListAsArray([$itemObjectPre])[0];
                            $item = [];
                            $item['id'] = $itemObj->getID();
                            $item['company'] = $itemObj->getCompany();
                            $item['shop_code'] = $itemObj->getShopCode();
                            $item['language_code'] = $itemObj->getLanguageCode();
                            $item['item_no'] = $itemObj->getItemNo();
                            $item['var_code'] = $itemObj->getVariantCode();
                            $item['unit_price'] = $itemObj->getUnitPrice();
                            $item['customer_price'] = $itemObj->getUnitPrice();
                            $item['cross_price'] = $itemObj->getCrossPrice();
                            $item['image_data'] = $itemObj->getImageData();
                            $item['main_image_data'] = $itemObj->getMainImageData();
                            $item['description'] = $itemObj->getDescription();
                            $item['variant_typ'] = $itemObj->getVariantType();
                            $item['variant_type'] = $itemObj->getVariantType();
                            $item['summary'] = $itemObj->getSummary();
                            $item['parent_item_no'] = $itemObj->getParentItemNo();
                            $item['availability'] = $itemObj->isAvailable();
                            $item['inventory'] = $itemObj->getInventory();
                            $item['item_slug'] = $itemObj->getItemSlug();
                            $item_result[] = $item;
                        }
                        $numRows = count($item_result);
                    }
                }
            }

            if(empty($item_result)) {
                $item_result = @mysqli_query($GLOBALS['mysql_con'], $item_query);
                $numRows = @mysqli_num_rows($item_result);
                if($numRows > 0) {
                    $item_result = @mysqli_fetch_all($item_result, MYSQLI_ASSOC);
                }
            }
            if ($numRows > 0) {
                echo "<div class='shop_item_preview'>";
                switch ($shop['shop_typ']) {
                    case 0:
                        $list_no = 10;
                        $columns = 4;
                        break;
                    case 1:
                        $list_no = 11;
                        $columns = 4;
                        break;
                    case 2:
                        $list_no = 10;
                        $columns = 4;
                        break;
                    case 3:
                        $list_no = 10;
                        $columns = 4;
                        break;
                    default:
                        $list_no = 10;
                        $columns = 4;
                        break;
                }
                show_item_list($item_result, $list_no, FALSE, $columns);
                echo "</div>";
            }
        }
    }
}

function shop_item_preview_edit() {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_item_preview.inc.php';
}
/*
if((int) $sitepart['main_sitepart_header_id'] > 0 ){
	shop_main_show($sitepart['main_sitepart_header_id']);
}
require_once('edit_shop_main.inc.php');*/