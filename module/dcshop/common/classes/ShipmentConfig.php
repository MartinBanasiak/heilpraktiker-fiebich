<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\dcShop\interfaces\DocLineModelDBConfigInterface;
use DynCom\dc\dcShop\interfaces\DocumentModelDBConfigInterface;
use DynCom\dc\dcShop\traits\documentConfigTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:22
 */
class ShipmentConfig implements DocumentModelDBConfigInterface {

    use documentConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\ShipmentDocument';
    protected $tableName      = 'shop_sales_shipment_header';
    protected $baseTableName  = 'shop_sales_shipment_header';
    protected $altPrimary     = array('company', 'no');
    protected $mappedFields   = array(
        array('name' => 'id', 'type' => 'INT'),
        array('name' => 'company', 'type' => 'VARCHAR'),
        array('name' => 'no', 'type' => 'VARCHAR'),
        array('name' => 'posting_date', 'type' => 'DATE'),
        array('name' => 'order_no', 'type' => 'VARCHAR'),
        array('name' => 'webshop_order_no', 'type' => 'INT'),
        array('name' => 'your_reference', 'type' => 'VARCHAR'),
        array('name' => 'sell_to_customer_no', 'type' => 'VARCHAR'),
        array('name' => 'bill_to_customer_no', 'type' => 'VARCHAR'),
        array('name' => 'sell_to_name', 'type' => 'VARCHAR'),
        array('name' => 'sell_to_name_2', 'type' => 'VARCHAR'),
        array('name' => 'sell_to_address', 'type' => 'VARCHAR'),
        array('name' => 'sell_to_address_2', 'type' => 'VARCHAR'),
        array('name' => 'sell_to_post_code', 'type' => 'VARCHAR'),
        array('name' => 'sell_to_city', 'type' => 'VARCHAR'),
        array('name' => 'sell_to_country', 'type' => 'VARCHAR'),
        array('name' => 'sell_to_contact', 'type' => 'VARCHAR'),
        array('name' => 'bill_to_name', 'type' => 'VARCHAR'),
        array('name' => 'bill_to_name_2', 'type' => 'VARCHAR'),
        array('name' => 'bill_to_address', 'type' => 'VARCHAR'),
        array('name' => 'bill_to_address_2', 'type' => 'VARCHAR'),
        array('name' => 'bill_to_post_code', 'type' => 'VARCHAR'),
        array('name' => 'bill_to_city', 'type' => 'VARCHAR'),
        array('name' => 'bill_to_country', 'type' => 'VARCHAR'),
        array('name' => 'bill_to_contact', 'type' => 'VARCHAR'),
        array('name' => 'ship_to_name', 'type' => 'VARCHAR'),
        array('name' => 'ship_to_name_2', 'type' => 'VARCHAR'),
        array('name' => 'ship_to_address', 'type' => 'VARCHAR'),
        array('name' => 'ship_to_address_2', 'type' => 'VARCHAR'),
        array('name' => 'ship_to_post_code', 'type' => 'VARCHAR'),
        array('name' => 'ship_to_city', 'type' => 'VARCHAR'),
        array('name' => 'ship_to_country', 'type' => 'VARCHAR'),
        array('name' => 'ship_to_contact', 'type' => 'VARCHAR'),
        array('name' => 'shipment_method', 'type' => 'VARCHAR'),
        array('name' => 'request_mail', 'type' => 'VARCHAR'),
        array('name' => 'request_shop_code', 'type' => 'VARCHAR'),
        array('name' => 'request_language_code', 'type' => 'VARCHAR'),
        array('name' => 'send_request', 'type' => 'TINYINT'),
        array('name' => 'return_order_insert', 'type' => 'TINYINT'),
        array('name' => 'return_order', 'type' => 'TINYINT'),
        array('name' => 'return_shop_code', 'type' => 'VARCHAR'),
        array('name' => 'return_language_code', 'type' => 'VARCHAR'),
        array('name' => 'return_order_reference', 'type' => 'VARCHAR'),
        array('name' => 'return_order_shop_no', 'type' => 'VARCHAR'),
        array('name' => 'return_order_token', 'type' => 'VARCHAR'),
        array('name' => 'to_delete', 'type' => 'TINYINT')
    );

    protected $linesClassName             = 'ShipmentLine';
    protected $linesCriteriaFieldMappings = array(
        array('company', '=', 'company'),
        array('document_no', '=', 'no')
    );

}