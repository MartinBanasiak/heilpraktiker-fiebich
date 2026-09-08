<?php
namespace DynCom\dc\dcShop\Document;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\traits\universallyGettableTrait;
use DynCom\dc\dcShop\interfaces\GenericDocumentInterface;
use DynCom\dc\dcShop\traits\documentTrait;

/**
 * Class InvoiceDocument
 */
class InvoiceDocument implements GenericDocumentInterface, PrintableDocumentInterface {

    use documentTrait, universallyGettableTrait;

    protected $id;
    protected $company;
    protected $no;
    protected $posting_date;
    protected $order_no;
    protected $webshop_order_no;
    protected $your_reference;
    protected $sell_to_customer_no;
    protected $bill_to_customer_no;
    protected $sell_to_name;
    protected $sell_to_name_2;
    protected $sell_to_address;
    protected $sell_to_address_2;
    protected $sell_to_post_code;
    protected $sell_to_city;
    protected $sell_to_country;
    protected $sell_to_contact;
    protected $bill_to_name;
    protected $bill_to_name_2;
    protected $bill_to_address;
    protected $bill_to_address_2;
    protected $bill_to_post_code;
    protected $bill_to_city;
    protected $bill_to_country;
    protected $bill_to_contact;
    protected $ship_to_name;
    protected $ship_to_name_2;
    protected $ship_to_address;
    protected $ship_to_address_2;
    protected $ship_to_post_code;
    protected $ship_to_city;
    protected $ship_to_country;
    protected $ship_to_contact;
    protected $amount;
    protected $amount_including_vat;


    protected $shipment_method;
    protected $payment_terms;
    protected $request_mail;
    protected $request_shop_code;
    protected $request_language_code;
    protected $send_request;
    protected $to_delete;

    protected $currencyCode;
    protected $documentTypeText;
    protected $customerEmail;

    Public const DOCUMENT_TYPE = "INVOICE";

    /**
     * @param InvoiceConfig                                      $config
     * @param InvoiceLineConfig                                  $linesConfig
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( InvoiceConfig $config, InvoiceLineConfig $linesConfig, CriteriaHelperInterface $criteriaValidationService ) {
        $this->config = $config;
        $this->linesConfig = $linesConfig;
        $this->criteriaValidationService = $criteriaValidationService;
    }

    /**
     * @return InvoiceDocument
     */
    public function getNullObject() {
        return new InvoiceDocument($this->config,$this->linesConfig,$this->criteriaValidationService);
    }

    /**
     * @param mixed $request_mail
     */
    public function setRequestMail($request_mail)
    {
        $this->request_mail = $request_mail;
    }

    /**
     * @param mixed $request_shop_code
     */
    public function setRequestShopCode($request_shop_code)
    {
        $this->request_shop_code = $request_shop_code;
    }

    /**
     * @param mixed $request_language_code
     */
    public function setRequestLanguageCode($request_language_code)
    {
        $this->request_language_code = $request_language_code;
    }

    /**
     * @param mixed $send_request
     */
    public function setSendRequest($send_request)
    {
        $this->send_request = $send_request;
    }


    public function getDocumentNo(){
        return $this->no;
    }

    public function getDocumentDate(){

        return date("d.m.Y", strtotime($this->posting_date));

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


    public function getRelatedDocuments()
    {
        return null;
    }

    public function getDocumentAmount()
    {
        return number_format($this->amount, 2, ',', '.');
    }

    public function showPdfButton()
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * @param mixed $currencyCode
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;
    }

    public function getDocumentName()
    {
        return  $this->sell_to_name_2 . " " . $this->sell_to_name;
    }

    public function getDocumentEmail()
    {
        return $this->customerEmail;
    }
    /**
     * @param mixed $customerEmail
     */
    public function setCustomerEmail($customerEmail)
    {
        $this->customerEmail = $customerEmail;
    }

    public function getCustomerNo()
    {
        return $this->sell_to_customer_no;
    }

    public function hasRelatedDocuments()
    {
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