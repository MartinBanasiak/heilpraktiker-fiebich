<?php
namespace DynCom\dc\dcShop\classes;
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;
/**
 * Class ShopLanguage
 */
class ShopLanguage implements GenericDBModelInterface, Entity {

    use genericDBModelTrait, universallyGettableTrait;

    protected $id;
    protected $company;
    protected $shop_code;
    protected $code;
    protected $description;
    protected $default_currency_code;
    protected $default_country_code;
    protected $default_payment_option_line_no;
    protected $email_order_1_text_module;
    protected $email_order_2_text_module;
    protected $email_order_3_text_module;
    protected $email_order_4_text_module;
    protected $email_login_text_module;
    protected $email_password_text_module;
    protected $email_invoice_copy_text_module;
    protected $email_shipment_copy_text_module;
    protected $email_return_order_text_module;
    protected $email_rma_confirm_text_module;
    protected $email_availability_notify;
    protected $login_welcome_text_module;
    protected $shopping_basket_text_module;
    protected $empty_basket_text_module;
    protected $order_queue_text_module;
    protected $order_step_1_text_module;
    protected $order_step_2_text_module;
    protected $order_step_3_text_module;
    protected $order_complete_text_module;
    protected $return_order_complete_text_module;
    protected $payment_error_text_module;
    protected $checkout_confirmation_text_module;
    protected $newsletter_registration_text_module;
    protected $recommend_mail_text_module;
    protected $text_search_results;
    protected $item_placeholder_image;
    protected $billpay_agb_text_module;
    protected $digital_coupon_active;
    protected $coupon_header_digital_coupon;
    protected $digital_coupon_background_1;
    protected $digital_coupon_background_2;
    protected $digital_coupon_background_3;
    protected $digital_coupon_background_4;
    protected $digital_coupon_background_5;
    protected $digital_coupon_amount_1;
    protected $digital_coupon_amount_2;
    protected $digital_coupon_amount_3;
    protected $digital_coupon_amount_4;
    protected $digital_coupon_amount_5;
    protected $minimum_coupon_amount;
    protected $max_coupon_amount;
    protected $gl_account_coupons;
    protected $text_email_digital_coupon;
    protected $dc_category_line_no;
    protected $to_delete;

    /**
     * @param ShopLanguageConfig $config
     */
    public function __construct( ShopLanguageConfig $config ) {
        $this->config = $config;
    }

    /**
     * @return ShopLanguage
     */
    public function getNullObject() {
        return new self($this->config);
    }

}