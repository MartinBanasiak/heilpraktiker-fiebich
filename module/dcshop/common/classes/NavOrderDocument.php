<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\traits\universallyGettableTrait;
use DynCom\dc\dcShop\interfaces\GenericDocumentInterface;
use DynCom\dc\dcShop\traits\documentTrait;

/**
 * Class NavOrderDocument
 */
class NavOrderDocument implements \IteratorAggregate, \Countable, GenericDocumentInterface
{

    use documentTrait, universallyGettableTrait;

    protected $id;
    protected $company;
    protected $type;
    protected $no;
    protected $order_date;
    protected $promised_delivery_date;
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
    protected $return_receipt_no;
    protected $last_return_receipt_no;
    protected $to_delete;

    /**
     * NavOrderDocument constructor.
     * @param NavOrderConfig $config
     * @param NavOrderLineConfig $linesConfig
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( NavOrderConfig $config, NavOrderLineConfig $linesConfig, CriteriaHelperInterface $criteriaValidationService ) {
        $this->config = $config;
        $this->linesConfig = $linesConfig;
        $this->criteriaValidationService = $criteriaValidationService;
    }

    /**
     * @return NavOrderDocument
     */
    public function getNullObject() {
        return new NavOrderDocument($this->config,$this->linesConfig,$this->criteriaValidationService);
    }

}
