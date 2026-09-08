<?php
namespace DynCom\dc\dcShop\classes;
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

    protected $modelClassName = 'DynCom\dc\dcShop\classes\WebshopOrderLine';
    protected $tableName      = 'shop_sales_line';
    protected $baseTableName  = 'shop_sales_line';
    protected $docClassName   = 'WebshopOrderDocument';
    protected $altPrimary     = array('company', 'document_no', 'line_no');
    protected $mappedFields   = array(
        array('name' => 'id', 'type' => 'INT'),
        array('name' => 'company', 'type' => 'VARCHAR'),
        array('name' => 'document_no', 'type' => 'VARCHAR'),
        array('name' => 'line_no', 'type' => 'INT'),
        array('name' => 'type', 'type' => 'TINYINT'),
        array('name' => 'no', 'type' => 'VARCHAR'),
        array('name' => 'description', 'type' => 'VARCHAR'),
        array('name' => 'description_2', 'type' => 'VARCHAR'),
        array('name' => 'unit_of_measure', 'type' => 'VARCHAR'),
        array('name' => 'quantity', 'type' => 'DECIMAL'),
        array('name' => 'quantity_returned', 'type' => 'DECIMAL'),
        array('name' => 'return_order_insert', 'type' => 'TINYINT'),
        array('name' => 'return_order', 'type' => 'TINYINT'),
        array('name' => 'return_quantity', 'type' => 'DECIMAL'),
        array('name' => 'return_reason_code', 'type' => 'VARCHAR'),
        array('name' => 'to_delete', 'type' => 'TINYINT')
    );

    protected $docCriteriaFieldMappings = array(
        array('company', '=', 'company'),
        array('no', '=', 'document_no')
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