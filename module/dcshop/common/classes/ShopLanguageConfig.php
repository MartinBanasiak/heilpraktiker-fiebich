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
class ShopLanguageConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\ShopLanguage';
    protected $tableName      = 'shop_language';
    protected $baseTableName  = 'shop_language';
    protected $altPrimary     = array('company', 'shop_code', 'code');
    protected $mappedFields   = array(
        array('name'=>'id','type'=>'INT'),
        array('name'=>'company','type'=>'VARCHAR'),
        array('name'=>'shop_code','type'=>'VARCHAR'),
        array('name'=>'code','type'=>'VARCHAR'),
        array('name'=>'description','type'=>'VARCHAR'),
        array('name'=>'default_currency_code','type'=>'VARCHAR'),
        array('name'=>'default_country_code','type'=>'VARCHAR'),
        array('name'=>'default_payment_option_line_no','type'=>'INT'),
        array('name'=>'email_order_1_text_module','type'=>'VARCHAR'),
        array('name'=>'email_order_2_text_module','type'=>'VARCHAR'),
        array('name'=>'email_order_3_text_module','type'=>'VARCHAR'),
        array('name'=>'email_order_4_text_module','type'=>'VARCHAR'),
        array('name'=>'email_login_text_module','type'=>'VARCHAR'),
        array('name'=>'email_password_text_module','type'=>'VARCHAR'),
        array('name'=>'email_invoice_copy_text_module','type'=>'VARCHAR'),
        array('name'=>'email_shipment_copy_text_module','type'=>'VARCHAR'),
        array('name'=>'email_return_order_text_module','type'=>'VARCHAR'),
        array('name'=>'email_rma_confirm_text_module','type'=>'VARCHAR'),
        array('name'=>'email_availability_notify','type'=>'VARCHAR'),
        array('name'=>'login_welcome_text_module','type'=>'VARCHAR'),
        array('name'=>'shopping_basket_text_module','type'=>'VARCHAR'),
        array('name'=>'empty_basket_text_module','type'=>'VARCHAR'),
        array('name'=>'order_queue_text_module','type'=>'VARCHAR'),
        array('name'=>'order_step_1_text_module','type'=>'VARCHAR'),
        array('name'=>'order_step_2_text_module','type'=>'VARCHAR'),
        array('name'=>'order_step_3_text_module','type'=>'VARCHAR'),
        array('name'=>'order_complete_text_module','type'=>'VARCHAR'),
        array('name'=>'return_order_complete_text_module','type'=>'VARCHAR'),
        array('name'=>'payment_error_text_module','type'=>'VARCHAR'),
        array('name'=>'checkout_confirmation_text_module','type'=>'VARCHAR'),
        array('name'=>'newsletter_registration_text_module','type'=>'VARCHAR'),
        array('name'=>'recommend_mail_text_module','type'=>'VARCHAR'),
        array('name'=>'text_search_results','type'=>'VARCHAR'),
        array('name'=>'item_placeholder_image','type'=>'VARCHAR'),
        array('name'=>'billpay_agb_text_module','type'=>'VARCHAR'),
        array('name'=>'digital_coupon_active','type'=>'TINYINT'),
        array('name'=>'coupon_header_digital_coupon','type'=>'VARCHAR'),
        array('name'=>'digital_coupon_background_1','type'=>'VARCHAR'),
        array('name'=>'digital_coupon_background_2','type'=>'VARCHAR'),
        array('name'=>'digital_coupon_background_3','type'=>'VARCHAR'),
        array('name'=>'digital_coupon_background_4','type'=>'VARCHAR'),
        array('name'=>'digital_coupon_background_5','type'=>'VARCHAR'),
        array('name'=>'digital_coupon_amount_1','type'=>'DECIMAL'),
        array('name'=>'digital_coupon_amount_2','type'=>'DECIMAL'),
        array('name'=>'digital_coupon_amount_3','type'=>'DECIMAL'),
        array('name'=>'digital_coupon_amount_4','type'=>'DECIMAL'),
        array('name'=>'digital_coupon_amount_5','type'=>'DECIMAL'),
        array('name'=>'minimum_coupon_amount','type'=>'DECIMAL'),
        array('name'=>'max_coupon_amount','type'=>'DECIMAL'),
        array('name'=>'gl_account_coupons','type'=>'VARCHAR'),
        array('name'=>'text_email_digital_coupon','type'=>'VARCHAR'),
        array('name'=>'dc_category_line_no','type'=>'INT'),
        array('name'=>'to_delete','type'=>'TINYINT')
    );

}