<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:24
 */
class ShopConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\Shop';
    protected $tableName      = 'shop_shop';
    protected $baseTableName  = 'shop_shop';
    protected $altPrimary     = array('company', 'code');
    protected $mappedFields   = array(
        array('name'=>'id','type'=>'INT'),
        array('name'=>'company','type'=>'VARCHAR'),
        array('name'=>'code','type'=>'VARCHAR'),
        array('name'=>'description','type'=>'VARCHAR'),
        array('name'=>'shop_typ','type'=>'TINYINT'),
        array('name'=>'login_type','type'=>'TINYINT'),
        array('name'=>'default_language_code','type'=>'VARCHAR'),
        array('name'=>'use_items_from_shop_code','type'=>'VARCHAR'),
        array('name'=>'use_categorys_from_shop_code','type'=>'VARCHAR'),
        array('name'=>'variant_typ','type'=>'TINYINT'),
        array('name'=>'use_customer_from_shop_code','type'=>'VARCHAR'),
        array('name'=>'email_sender','type'=>'VARCHAR'),
        array('name'=>'email_order_mail_1_copy','type'=>'VARCHAR'),
        array('name'=>'computop_merchant_id','type'=>'VARCHAR'),
        array('name'=>'computop_password','type'=>'VARCHAR'),
        array('name'=>'show_vendor_filter','type'=>'TINYINT'),
        array('name'=>'show_invoice_discount','type'=>'TINYINT'),
        array('name'=>'small_quantity_charge','type'=>'DECIMAL'),
        array('name'=>'small_quantity_charge_limit','type'=>'DECIMAL'),
        array('name'=>'online_discount','type'=>'DECIMAL'),
        array('name'=>'retail_price_typ','type'=>'TINYINT'),
        array('name'=>'cust_price_group_retail_price','type'=>'VARCHAR'),
        array('name'=>'cust_disc_group_retail_price','type'=>'VARCHAR'),
        array('name'=>'base_price_typ','type'=>'TINYINT'),
        array('name'=>'cust_price_group_base_price','type'=>'VARCHAR'),
        array('name'=>'cust_disc_group_base_price','type'=>'VARCHAR'),
        array('name'=>'campain_no','type'=>'VARCHAR'),
        array('name'=>'prices_including_vat','type'=>'TINYINT'),
        array('name'=>'vat_bus_posting_group','type'=>'VARCHAR'),
        array('name'=>'select_req_delivery_date','type'=>'TINYINT'),
        array('name'=>'addition_req_delivery_date','type'=>'INT'),
        array('name'=>'cross_price_typ','type'=>'TINYINT'),
        array('name'=>'show_filters','type'=>'TINYINT'),
        array('name'=>'show_comparison','type'=>'TINYINT'),
        array('name'=>'show_filter_on_card','type'=>'TINYINT'),
        array('name'=>'attribute_frige_shipping','type'=>'VARCHAR'),
        array('name'=>'attribute_bio_food','type'=>'VARCHAR'),
        array('name'=>'g_ftp_url','type'=>'VARCHAR'),
        array('name'=>'g_ftp_user','type'=>'VARCHAR'),
        array('name'=>'g_ftp_passwd','type'=>'VARCHAR'),
        array('name'=>'g_channel_title','type'=>'VARCHAR'),
        array('name'=>'g_channel_link','type'=>'VARCHAR'),
        array('name'=>'g_channel_desc','type'=>'VARCHAR'),
        array('name'=>'default_copernica_segment','type'=>'VARCHAR'),
        array('name'=>'max_days_shipment_returnable','type'=>'INT'),
        array('name'=>'to_delete','type'=>'TINYINT'),
        array('name'=>'attribute_brand','type'=>'VARCHAR'),
        array('name'=>'extended_search','type'=>'TINYINT'),
        array('name'=>'forerun_subscr_order_create','type'=>'VARCHAR'),
        array('name'=>'forerun_payment_capture','type'=>'VARCHAR'),
        array('name'=>'order_options_display','type'=>'TINYINT'),
        array('name'=>'order_options_sorting','type'=>'TINYINT'),
        array('name'=>'paydirekt_api_key','type'=>'TINYINT'),
        array('name'=>'copernica_db_id','type'=>'TINYINT'),
        array('name'=>'copernica_access_token','type'=>'TINYINT'),
        array('name'=>'vat_identifier_1','type'=>'TINYINT'),
        array('name'=>'vat_identifier_2','type'=>'TINYINT'),
        array('name'=>'vat_identifier_3','type'=>'TINYINT'),
        array('name'=>'inventory_display','type'=>'TINYINT'),
        array('name'=>'item_availability','type'=>'TINYINT'),
        array('name'=>'us_sales_tax_enabled','type'=>'TINYINT'),
        array('name'=>'shipping_group_code','type'=>'VARCHAR'),
    );

}