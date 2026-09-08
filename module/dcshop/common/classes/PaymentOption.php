<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;
/**
 * Class PaymentOption
 */
class PaymentOption implements GenericDBModelInterface, Entity {

    use genericDBModelTrait, universallyGettableTrait;

    protected $id;
    protected $company;
    protected $shop_code;
    protected $language_code;
    protected $line_no;
    protected $description;
    protected $country_code;
    protected $checkout;
    protected $checkout_state;
    protected $payment_cost;
    protected $credit_check_required;
    protected $dc_active;
    protected $active;
    protected $to_delete;
    protected $recurrent_payment_active;
    protected $logo;
    protected $content;
    protected $text_module_order_conf;
    protected $billpay_agb_text_module;

    /**
     * @param PaymentOptionConfig $config
     */
    public function __construct( PaymentOptionConfig $config ) {
        $this->config = $config;
    }

    /**
     * @return PaymentOption
     */
    public function getNullObject() {
        return new self($this->config);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return mixed
     */
    public function getShopCode()
    {
        return $this->shop_code;
    }

    /**
     * @return mixed
     */
    public function getLanguageCode()
    {
        return $this->language_code;
    }

    /**
     * @return mixed
     */
    public function getLineNo()
    {
        return $this->line_no;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getCountryCode()
    {
        return $this->country_code;
    }

    /**
     * @return mixed
     */
    public function getCheckout()
    {
        return $this->checkout;
    }

    /**
     * @return mixed
     */
    public function getCheckoutState()
    {
        return $this->checkout_state;
    }

    /**
     * @return mixed
     */
    public function getPaymentCost()
    {
        return $this->payment_cost;
    }

    /**
     * @return mixed
     */
    public function getCreditCheckRequired()
    {
        return $this->credit_check_required;
    }

    /**
     * @return mixed
     */
    public function getDcActive()
    {
        return $this->dc_active;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @return mixed
     */
    public function getToDelete()
    {
        return $this->to_delete;
    }

    /**
     * @return mixed
     */
    public function getRecurrentPaymentActive()
    {
        return $this->recurrent_payment_active;
    }

    /**
     * @return mixed
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return mixed
     */
    public function getTextModuleOrderConf()
    {
        return $this->text_module_order_conf;
    }

    /**
     * @return mixed
     */
    public function getBillpayAgbTextModule()
    {
        return $this->billpay_agb_text_module;
    }



}