<?php
namespace DynCom\dc\dcShop\Document;

use DynCom\dc\dcShop\interfaces\DocumentModelDBConfigInterface;
use DynCom\dc\dcShop\traits\documentConfigTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 19.01.2015
 * Time: 14:08
 */
class WebshopOrderConfig implements DocumentModelDBConfigInterface
{

    use documentConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\Document\WebshopOrderDocument';
    protected $tableName = 'shop_sales_header';
    protected $baseTableName = 'shop_sales_header';
    protected $altPrimary = array('company', 'no');
    protected $mappedFields = array(
        array('name' => 'id', 'type' => 'INT'),
        array('name' => 'company', 'type' => 'VARCHAR'),
        array('name' => 'shop_code', 'type' => 'VARCHAR'),
        array('name' => 'language_code', 'type' => 'VARCHAR'),
        array('name' => 'order_no', 'type' => 'VARCHAR'),
        array('name' => 'shop_customer_id', 'type' => 'INT'),
        array('name' => 'shop_user_id', 'type' => 'INT'),
        array('name' => 'customer_no', 'type' => 'VARCHAR'),
        array('name' => 'user_name', 'type' => 'VARCHAR'),
        array('name' => 'user_email', 'type' => 'VARCHAR'),
        array('name' => 'user_phone_no', 'type' => 'VARCHAR'),
        array('name' => 'user_salutation', 'type' => 'VARCHAR'),
        array('name' => 'salesperson_code', 'type' => 'VARCHAR'),
        array('name' => 'order_date', 'type' => 'DATE'),
        array('name' => 'requested_delivery_date', 'type' => 'DATE'),
        array('name' => 'ship_to_name', 'type' => 'VARCHAR'),
        array('name' => 'ship_to_name_2', 'type' => 'VARCHAR'),
        array('name' => 'ship_to_address', 'type' => 'VARCHAR'),
        array('name' => 'ship_to_address_2', 'type' => 'VARCHAR'),
        array('name' => 'ship_to_post_code', 'type' => 'VARCHAR'),
        array('name' => 'ship_to_city', 'type' => 'VARCHAR'),
        array('name' => 'ship_to_country', 'type' => 'VARCHAR'),
        array('name' => 'ship_to_contact', 'type' => 'VARCHAR'),
        array('name' => 'ship_to_telephone_no', 'type' => 'VARCHAR'),
        array('name' => 'bill_to_customer_no', 'type' => 'VARCHAR'),
        array('name' => 'bill_to_name', 'type' => 'VARCHAR'),
        array('name' => 'bill_to_name_2', 'type' => 'VARCHAR'),
        array('name' => 'bill_to_address', 'type' => 'VARCHAR'),
        array('name' => 'bill_to_address_2', 'type' => 'VARCHAR'),
        array('name' => 'bill_to_post_code', 'type' => 'VARCHAR'),
        array('name' => 'bill_to_city', 'type' => 'VARCHAR'),
        array('name' => 'bill_to_country', 'type' => 'VARCHAR'),
        array('name' => 'your_reference', 'type' => 'VARCHAR'),
        array('name' => 'your_comment', 'type' => 'VARCHAR'),
        array('name' => 'subtotal', 'type' => 'DECIMAL'),
        array('name' => 'online_discount', 'type' => 'DECIMAL'),
        array('name' => 'online_discount_amount', 'type' => 'DECIMAL'),
        array('name' => 'invoice_discount', 'type' => 'DECIMAL'),
        array('name' => 'invoice_discount_amount', 'type' => 'DECIMAL'),
        array('name' => 'small_quantity_charge_amount', 'type' => 'DECIMAL'),
        array('name' => 'total', 'type' => 'DECIMAL'),
        array('name' => 'shipping_option_line_no', 'type' => 'INT'),
        array('name' => 'shipping_cost', 'type' => 'DECIMAL'),
        array('name' => 'payment_option_line_no', 'type' => 'INT'),
        array('name' => 'payment_cost', 'type' => 'DECIMAL'),
        array('name' => 'payment_transaction_id', 'type' => 'VARCHAR'),
        array('name' => 'pay_id', 'type' => 'VARCHAR'),
        array('name' => 'bank_account_no', 'type' => 'VARCHAR'),
        array('name' => 'bank_branch_no', 'type' => 'VARCHAR'),
        array('name' => 'bank_name', 'type' => 'VARCHAR'),
        array('name' => 'currency_code', 'type' => 'VARCHAR'),
        array('name' => 'drop_shipment', 'type' => 'TINYINT'),
        array('name' => 'shipping_advice', 'type' => 'TINYINT'),
        array('name' => 'process_payment', 'type' => 'TINYINT'),
        array('name' => 'payment_processed', 'type' => 'DATETIME'),
        array('name' => 'coupon_amount', 'type' => 'DECIMAL'),
        array('name' => 'value_coupon', 'type' => 'TINYINT'),
        array('name' => 'coupon_code', 'type' => 'VARCHAR'),
        array('name' => 'coupon_header_code', 'type' => 'VARCHAR'),
        array('name' => 'shipping_coupon', 'type' => 'VARCHAR'),
        array('name' => 'dc_order', 'type' => 'TINYINT'),
        array('name' => 'newsletter_registration', 'type' => 'TINYINT'),
        array('name' => 'bp_acc_owner', 'type' => 'VARCHAR'),
        array('name' => 'bp_acc_nr', 'type' => 'VARCHAR'),
        array('name' => 'bp_acc_iban', 'type' => 'VARCHAR'),
        array('name' => 'bp_bank', 'type' => 'VARCHAR'),
        array('name' => 'bp_invoice_ref', 'type' => 'VARCHAR'),
        array('name' => 'bon_check', 'type' => 'TINYINT'),
        array('name' => 'sepa', 'type' => 'TINYINT'),
        array('name'=>'birthday','type'=>'DATE'),
        array('name' => 'sur_name', 'type' => 'VARCHAR'),
        array('name' => 'last_name', 'type' => 'VARCHAR'),
        array('name' => 'salutation_title', 'type' => 'VARCHAR'),
        array('name' => 'to_nl_transfer', 'type' => 'TINYINT'),
        array('name' => 'order_error', 'type' => 'TINYINT'),
        array('name' => 'update_notify', 'type' => 'TINYINT'),
        array('name' => 'successful', 'type' => 'TINYINT'),
        array('name' => 'subscription_code', 'type' => 'VARCHAR'),
        array('name' => 'subscription_cust_line_no', 'type' => 'INT'),
        array('name' => 'update_insert', 'type' => 'TINYINT'),
        array('name' => 'marketplace_order', 'type' => 'VARCHAR'),
        array('name' => 'marketplace_order_status', 'type' => 'VARCHAR'),
        array('name' => 'marketplace_sales_channel', 'type' => 'VARCHAR'),
        array('name' => 'marketplace_shipped', 'type' => 'TINYINT'),
        array('name' => 'vat_id', 'type' => 'VARCHAR'),
        array('name' => 'payment_reference', 'type' => 'VARCHAR'),
        array('name' => 'to_delete', 'type' => 'TINYINT'),
    );

    protected $linesClassName = 'WebshopOrderLine';
    protected $linesCriteriaFieldMappings = array(
        array('shop_sales_header_id', '=', 'id')
    );

}