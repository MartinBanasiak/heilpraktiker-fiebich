<?php
namespace DynCom\dc\dcShop\Document;
use DynCom\dc\common\traits\genericConfigTrait;
use DynCom\dc\dcShop\interfaces\DocumentModelDBConfigInterface;
use DynCom\dc\dcShop\traits\documentConfigTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:21
 */
class NavOrderConfig implements DocumentModelDBConfigInterface {

    use documentConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\Document\NavOrderDocument';
    protected $tableName      = 'shop_nav_sales_header';
    protected $baseTableName  = 'shop_nav_sales_header';
    protected $altPrimary     = array('company', 'type', 'no');
    protected $mappedFields   = array(
        array('name' => 'id','type' => 'INT'),
        array('name' => 'company','type' => 'VARCHAR'),
        array('name' => 'type','type' => 'TINYINT'),
        array('name' => 'no','type' => 'VARCHAR'),
        array('name' => 'order_date','type' => 'DATE'),
        array('name' => 'promised_delivery_date','type' => 'DATE'),
        array('name' => 'webshop_order_no','type' => 'INT'),
        array('name' => 'your_reference','type' => 'VARCHAR'),
        array('name' => 'sell_to_customer_no','type' => 'VARCHAR'),
        array('name' => 'bill_to_customer_no','type' => 'VARCHAR'),
        array('name' => 'sell_to_name','type' => 'VARCHAR'),
        array('name' => 'sell_to_name_2','type' => 'VARCHAR'),
        array('name' => 'sell_to_address','type' => 'VARCHAR'),
        array('name' => 'sell_to_address_2','type' => 'VARCHAR'),
        array('name' => 'sell_to_post_code','type' => 'VARCHAR'),
        array('name' => 'sell_to_city','type' => 'VARCHAR'),
        array('name' => 'sell_to_country','type' => 'VARCHAR'),
        array('name' => 'sell_to_contact','type' => 'VARCHAR'),
        array('name' => 'bill_to_name','type' => 'VARCHAR'),
        array('name' => 'bill_to_name_2','type' => 'VARCHAR'),
        array('name' => 'bill_to_address','type' => 'VARCHAR'),
        array('name' => 'bill_to_address_2','type' => 'VARCHAR'),
        array('name' => 'bill_to_post_code','type' => 'VARCHAR'),
        array('name' => 'bill_to_city','type' => 'VARCHAR'),
        array('name' => 'bill_to_country','type' => 'VARCHAR'),
        array('name' => 'bill_to_contact','type' => 'VARCHAR'),
        array('name' => 'ship_to_name','type' => 'VARCHAR'),
        array('name' => 'ship_to_name_2','type' => 'VARCHAR'),
        array('name' => 'ship_to_address','type' => 'VARCHAR'),
        array('name' => 'ship_to_address_2','type' => 'VARCHAR'),
        array('name' => 'ship_to_post_code','type' => 'VARCHAR'),
        array('name' => 'ship_to_city','type' => 'VARCHAR'),
        array('name' => 'ship_to_country','type' => 'VARCHAR'),
        array('name' => 'ship_to_contact','type' => 'VARCHAR'),
        array('name' => 'amount','type' => 'DECIMAL'),
        array('name' => 'amount_including_vat','type' => 'DECIMAL'),
        array('name' => 'shipment_method','type' => 'VARCHAR'),
        array('name' => 'payment_terms','type' => 'VARCHAR'),
        array('name' => 'return_receipt_no','type' => 'VARCHAR'),
        array('name' => 'last_return_receipt_no','type' => 'VARCHAR'),
        array('name' => 'to_delete','type' => 'TINYINT')
    );

    protected $linesClassName             = 'NavOrderDocument';
    protected $linesCriteriaFieldMappings = array(
        array('company', '=', 'company'),
        array('document_no', '=', 'no')
    );

}