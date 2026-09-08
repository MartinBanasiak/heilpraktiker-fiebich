<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 19.01.2015
 * Time: 14:08
 */

class CategoryConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\Category';
    protected $tableName      = 'shop_category';
    protected $baseTableName  = 'shop_category';
    protected $altPrimary     = array('company','shop_code','language_code','line_no');
    protected $mappedFields   = array(
        array('name' => 'id','type' => 'INT'),
        array('name' => 'company','type' => 'VARCHAR'),
        array('name' => 'shop_code','type' => 'VARCHAR'),
        array('name' => 'language_code','type' => 'VARCHAR'),
        array('name' => 'line_no','type' => 'INT'),
        array('name' => 'parent_line_no','type' => 'INT'),
        array('name' => 'sorting','type' => 'INT'),
        array('name' => 'level','type' => 'TINYINT'),
        array('name' => 'name','type' => 'VARCHAR'),
        array('name' => 'root_line_no','type' => 'INT'),
        array('name' => 'code','type' => 'VARCHAR'),
        array('name' => 'show_random_items','type' => 'TINYINT'),
        array('name' => 'no_of_random_items','type' => 'INT'),
        array('name' => 'show_campain_items','type' => 'TINYINT'),
        array('name' => 'no_of_campain_items','type' => 'INT'),
        array('name' => 'show_all_items','type' => 'TINYINT'),
        array('name' => 'user_sorting','type' => 'TINYINT'),
        array('name' => 'sort_items','type' => 'TINYINT'),
        array('name' => 'show_sub_categorys','type' => 'TINYINT'),
        array('name' => 'category_picture','type' => 'VARCHAR'),
        array('name' => 'category_icon','type' => 'VARCHAR'),
        array('name' => 'category_description','type' => 'LONGTEXT'),
        array('name' => 'category_description_2','type' => 'LONGTEXT'),
        array('name' => 'category_description_excerpt','type' => 'LONGTEXT'),
        array('name' => 'active','type' => 'TINYINT'),
        array('name' => 'validity_from','type' => 'DATE'),
        array('name' => 'validity_to','type' => 'DATE'),
        array('name' => 'promotion_active','type' => 'TINYINT'),
        array('name' => 'promotion_validity_from','type' => 'DATE'),
        array('name' => 'promotion_validity_to','type' => 'DATE'),
        array('name' => 'promotion_label','type' => 'TINYINT'),
        array('name' => 'promotion_description','type' => 'LONGTEXT'),
        array('name' => 'search_query','type' => 'VARCHAR'),
        array('name' => 'meta_keywords','type' => 'VARCHAR'),
        array('name' => 'meta_description','type' => 'VARCHAR'),
        array('name' => 'site_titel','type' => 'VARCHAR'),
        array('name' => 'filter_active','type' => 'TINYINT'),
        array('name' => 'max_no_of_filter','type' => 'INT'),
        array('name' => 'to_delete','type' => 'TINYINT')
    );

}
