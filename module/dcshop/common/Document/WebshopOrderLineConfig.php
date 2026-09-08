<?php
namespace DynCom\dc\dcShop\Document;
use DynCom\dc\common\traits\genericConfigTrait;
use DynCom\dc\dcShop\interfaces\DocLineModelDBConfigInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 19.01.2015
 * Time: 15:17
 */
class WebshopOrderLineConfig implements DocLineModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\Document\WebshopOrderLine';
    protected $tableName      = 'shop_sales_line';
    protected $baseTableName  = 'shop_sales_line';
    protected $docClassName   = 'WebshopOrderDocument';
    protected $altPrimary     = array('company', 'document_no', 'line_no');
    protected $mappedFields   = array(
        array('name' => 'id', 'type' => 'INT'),
        array('name' => 'shop_sales_header_id', 'type' => 'INT'),
        array('name' => 'shop_item_id', 'type' => 'INT'),
        array('name' => 'company', 'type' => 'VARCHAR'),
        array('name' => 'shop_code', 'type' => 'VARCHAR'),
        array('name' => 'language_code', 'type' => 'VARCHAR'),
        array('name' => 'order_no', 'type' => 'VARCHAR'),
        array('name' => 'item_no', 'type' => 'VARCHAR'),
        array('name' => 'variant_code', 'type' => 'VARCHAR'),
        array('name' => 'description', 'type' => 'VARCHAR'),
        array('name' => 'summary', 'type' => 'VARCHAR'),
        array('name' => 'list_price', 'type' => 'DECIMAL'),
        array('name' => 'unit_price', 'type' => 'DECIMAL'),
        array('name' => 'quantity', 'type' => 'DECIMAL'),
        array('name' => 'line_amount', 'type' => 'DECIMAL'),
        array('name' => 'greeting_card_text', 'type' => 'VARCHAR'),
        array('name' => 'package_for_item', 'type' => 'VARCHAR'),
        array('name' => 'package_for_variant_code', 'type' => 'VARCHAR'),
        array('name' => 'customized', 'type' => 'TINYINT'),
        array('name' => 'allow_invoice_disc', 'type' => 'TINYINT'),
        array('name' => 'is_coupon_item', 'type' => 'TINYINT'),
        array('name' => 'salesperson_discount', 'type' => 'DECIMAL'),
        array('name' => 'marketplace_line_id', 'type' => 'VARCHAR'),
        array('name' => 'return_order_insert', 'type' => 'TINYINT'),
        array('name' => 'to_delete', 'type' => 'TINYINT')
    );

    protected $docCriteriaFieldMappings = array(
        array('id', '=', 'shop_sales_header_id'),
    );

    protected $itemType                  = 2;
    protected $itemCriteriaFieldMappings = array(
        array('item_no', '=', 'no')
    );

    /**
     * @return string
     */
    function getDocClassName() {
        return $this->docClassName;
    }

    /**
     * @return array
     */
    function getDocCriteriaFieldMappings() {
        return $this->docCriteriaFieldMappings;
    }

    /**
     * @return int
     */
    public function getItemType() {
        return $this->itemType;
    }

    /**
     * @return array
     */
    function getItemCriteriaFieldMappings() {
        return $this->itemCriteriaFieldMappings;
    }
}