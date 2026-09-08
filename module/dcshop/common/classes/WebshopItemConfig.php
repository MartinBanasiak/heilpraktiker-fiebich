<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:25
 */
class WebshopItemConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\WebshopItem';
    protected $tableName      = 'shop_item';
    protected $baseTableName  = 'shop_item';
    protected $altPrimary     = array('company', 'shop_code', 'language_code', 'item_no');
    protected $mappedFields   = array(
        array('name' => 'id', 'type' => 'INT'),
        array('name' => 'company', 'type' => 'VARCHAR'),
        array('name' => 'shop_code', 'type' => 'VARCHAR'),
        array('name' => 'language_code', 'type' => 'VARCHAR'),
        array('name' => 'item_no', 'type' => 'VARCHAR'),
        array('name' => 'description', 'type' => 'VARCHAR'),
        array('name' => 'summary', 'type' => 'VARCHAR'),
        array('name' => 'base_unit_of_measure', 'type' => 'VARCHAR'),
        array('name' => 'unit_of_measure_code', 'type' => 'VARCHAR'),
        array('name' => 'nav_base_unit_code', 'type' => 'VARCHAR'),
        array('name' => 'multiplier', 'type' => 'DECIMAL'),
        array('name' => 'variant_type', 'type' => 'VARCHAR'),
        array('name' => 'active', 'type' => 'TINYINT'),
        array('name' => 'validity_from', 'type' => 'DATE'),
        array('name' => 'validity_to', 'type' => 'DATE'),
        array('name' => 'main_picture_line_no', 'type' => 'INT'),
        array('name' => 'main_category_line_no', 'type' => 'INT'),
        array('name' => 'retail_price', 'type' => 'DECIMAL'),
        array('name' => 'base_price', 'type' => 'DECIMAL'),
        array('name' => 'price_includes_vat', 'type' => 'TINYINT'),
        array('name' => 'inventory', 'type' => 'DECIMAL'),
        array('name' => 'insufficient_inventory_limit', 'type' => 'DECIMAL'),
        array('name' => 'quantity_on_purchase_order', 'type' => 'DECIMAL'),
        array('name' => 'discount_group', 'type' => 'VARCHAR'),
        array('name' => 'allow_invoice_discount', 'type' => 'TINYINT'),
        array('name' => 'search_query', 'type' => 'VARCHAR'),
        array('name' => 'vendor_no', 'type' => 'VARCHAR'),
        array('name' => 'vendor_name', 'type' => 'VARCHAR'),
        array('name' => 'parent_item_no', 'type' => 'VARCHAR'),
        array('name' => 'order_ranking', 'type' => 'DECIMAL'),
        array('name' => 'weight', 'type' => 'DECIMAL'),
        array('name' => 'net_weight', 'type' => 'DECIMAL'),
        array('name' => 'width', 'type' => 'DECIMAL'),
        array('name' => 'height', 'type' => 'DECIMAL'),
        array('name' => 'length', 'type' => 'DECIMAL'),
        array('name' => 'volume', 'type' => 'DECIMAL'),
        array('name' => 'creation_date', 'type' => 'DATE'),
        array('name' => 'meta_keywords', 'type' => 'VARCHAR'),
        array('name' => 'meta_description', 'type' => 'VARCHAR'),
        array('name' => 'site_title', 'type' => 'VARCHAR'),
        array('name' => 'allow_gift_package', 'type' => 'TINYINT'),
        array('name' => 'is_gift_package', 'type' => 'TINYINT'),
        array('name' => 'minimum_order_quantity', 'type' => 'DECIMAL'),
        array('name' => 'quantity_packing_unit', 'type' => 'DECIMAL'),
        array('name' => 'order_per_packing_unit', 'type' => 'TINYINT'),
        array('name' => 'vat_prod_posting_group', 'type' => 'VARCHAR'),
        array('name' => 'is_greeting_card', 'type' => 'TINYINT'),
        array('name' => 'customizable', 'type' => 'TINYINT'),
        array('name' => 'customization_price', 'type' => 'TINYINT'),
        array('name' => 'shipping_class_code', 'type' => 'VARCHAR'),
        array('name' => 'always_available', 'type' => 'TINYINT'),
        array('name' => 'item_slug', 'type' => 'VARCHAR'),
        array('name' => 'to_delete', 'type' => 'TINYINT')
    );

}