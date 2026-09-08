<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\traits\universallyGettableTrait;
use DynCom\dc\dcShop\interfaces\GenericDocumentInterface;
use DynCom\dc\dcShop\traits\documentTrait;

/**
 * Class InvoiceDocument
 */
class InvoiceDocument implements GenericDocumentInterface {

    use documentTrait, universallyGettableTrait;

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

}