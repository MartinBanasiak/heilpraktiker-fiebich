<?php
namespace DynCom\dc\dcShop\Document;

use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\traits\universallyGettableTrait;
use DynCom\dc\dcShop\interfaces\GenericDocumentInterface;
use DynCom\dc\dcShop\traits\documentTrait;

/**
 * Class WebshopOrderDocument
 */
class WebshopOrderDocument implements GenericDocumentInterface, PrintableDocumentInterface
{

    use documentTrait, universallyGettableTrait;

    protected $id;
    protected $company;
    protected $shop_code;
    protected $language_code;
    protected $order_no;
    protected $shop_customer_id;
    protected $shop_user_id;
    protected $customer_no;
    protected $user_name;
    protected $user_email;
    protected $user_phone_no;
    protected $user_salutation;
    protected $salesperson_code;
    protected $order_date;
    protected $requested_delivery_date;
    protected $ship_to_name;
    protected $ship_to_name_2;
    protected $ship_to_address;
    protected $ship_to_address_2;
    protected $ship_to_city;
    protected $ship_to_post_code;
    protected $ship_to_country;
    protected $ship_to_contact;
    protected $ship_to_telephone_no;
    protected $bill_to_customer_no;
    protected $bill_to_name;
    protected $bill_to_name_2;
    protected $bill_to_address;
    protected $bill_to_address_2;
    protected $bill_to_post_code;
    protected $bill_to_city;
    protected $bill_to_country;
    protected $your_reference;
    protected $your_comment;
    protected $subtotal;
    protected $online_discount;
    protected $online_discount_amount;
    protected $invoice_discount;
    protected $invoice_discount_amount;
    protected $small_quantity_charge_amount;
    protected $total;
    protected $shipping_option_line_no;
    protected $shipping_cost;
    protected $payment_option_line_no;
    protected $payment_cost;
    protected $payment_transaction_id;
    protected $pay_id;
    protected $bank_account_no;
    protected $bank_branch_no;
    protected $bank_name;
    protected $currency_code;
    protected $drop_shipment;
    protected $shipping_advice;
    protected $process_payment;
    protected $payment_processed;
    protected $coupon_amount;
    protected $value_coupon;
    protected $coupon_code;
    protected $shipping_coupon;
    protected $dc_order;
    protected $newsletter_registration;
    protected $bp_acc_owner;
    protected $bp_acc_nr;
    protected $bp_acc_iban;
    protected $bp_bank;
    protected $bp_invoice_ref;
    protected $bon_check;
    protected $sepa;
    protected $birthday;
    protected $sur_name;
    protected $last_name;
    protected $salutation_title;
    protected $to_nl_transfer;
    protected $order_error;
    protected $update_notify;
    protected $successful;
    protected $subscription_code;
    protected $subscription_cust_line_no;
    protected $update_insert;
    protected $marketplace_order;
    protected $marketplace_order_status;
    protected $marketplace_sales_channel;
    protected $marketplace_shipped;
    protected $vat_id;
    protected $payment_reference;
    protected $to_delete;

    protected $relatedDocuments;

    protected $documentTypeText;

    Public const DOCUMENT_TYPE = "ORDER";


    /**
     * WebshopOrderDocument constructor.
     * @param WebshopOrderConfig $config
     * @param WebshopOrderLineConfig $linesConfig
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct(WebshopOrderConfig $config, WebshopOrderLineConfig $linesConfig, CriteriaHelperInterface $criteriaValidationService)
    {
        $this->config = $config;
        $this->linesConfig = $linesConfig;
        $this->criteriaValidationService = $criteriaValidationService;
    }

    /**
     * @return WebshopOrderDocument
     */
    public function getNullObject()
    {
        return new WebshopOrderDocument($this->config, $this->linesConfig, $this->criteriaValidationService);
    }

    /**
     * @param mixed $relatedDocuments
     */
    public function setRelatedDocuments($relatedDocuments)
    {
        $this->relatedDocuments = $relatedDocuments;
    }

    public function getRelatedDocuments()
    {
        return $this->relatedDocuments;
    }

    public function getDocumentNo()
    {

        return $this->order_no;

    }

    public function getDocumentDate()
    {
        return date("d.m.Y", strtotime($this->order_date));
    }

    /**
     * @param mixed $documentTypeText
     */
    public function setDocumentTypeText($documentTypeText)
    {
        $this->documentTypeText = $documentTypeText;
    }
    public function getDocumentTypeText()
    {
        return $this->documentTypeText;
    }
    public function getDocumentType()
    {
        return Self::DOCUMENT_TYPE;
    }

    public function getDocumentAmount()
    {
        return number_format($this->total, 2, ',', '.');
    }

    public function showPdfButton()
    {
        return false;
    }

    /**
     * @return mixed
     */
    public function getCurrencyCode()
    {
        return $this->currency_code;
    }

    /**
     * @param mixed $currency_code
     */
    public function setCurrencyCode($currency_code)
    {
        $this->currency_code = $currency_code;
    }

    public function getDocumentName()
    {
        return $this->user_name;
    }

    public function getDocumentEmail()
    {
        return $this->user_email;
    }

    public function getCustomerNo()
    {
        return $this->customer_no;
    }

    public function hasRelatedDocuments()
    {
        $relatedDocuments = $this->getRelatedDocuments();
        if ($relatedDocuments <> null) {
            return true;
        }
        return false;

    }

    /**
     * @param mixed $bill_to_country
     */
    public function setBillToCountry($bill_to_country)
    {
        $this->bill_to_country = $bill_to_country;
    }

    /**
     * @param mixed $ship_to_country
     */
    public function setShipToCountry($ship_to_country)
    {
        $this->ship_to_country = $ship_to_country;
    }


}