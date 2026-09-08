<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Class SubscriptionCustomerLink
 */
class SubscriptionCustomerLink implements GenericDBModelInterface, Entity
{

    use genericDBModelTrait, universallyGettableTrait;

      protected $id;
      protected $company;
      protected $subscription_code;
      protected $customer_no;
      protected $webshop_order_email;
      protected $line_no;
      protected $bill_to_name;
      protected $bill_to_name_2;
      protected $bill_to_address;
      protected $bill_to_address_2;
      protected $bill_to_city;
      protected $ship_to_name;
      protected $ship_to_name_2;
      protected $ship_to_address;
      protected $ship_to_address_2;
      protected $ship_to_city;
      protected $currency_code;
      protected $bill_to_post_code;
      protected $bill_to_county;
      protected $bill_to_country_region_code;
      protected $ship_to_post_code;
      protected $ship_to_county;
      protected $ship_to_country_region_code;
      protected $webshop_shop_code;
      protected $webshop_language_code;
      protected $webshop_pay_opt_line_no;
      protected $webshop_ship_opt_line_no;
      protected $customer_pseudo_pay_line_no;
      protected $signup_date;
      protected $cancellation_date;
      protected $next_order_creation_date;
      protected $next_order_w_payment_date;
      protected $no_of_turns_processed;
      protected $user_name;
      protected $subscription_item_no;
      protected $next_sequence_step_line_no;
      protected $subscription_qty_per_turn;
      protected $initial_order_no;
      protected $update_insert;
      protected $to_delete;

    protected $subscriptionHeader;

    /**
     * @param SubscriptionCustomerLinkConfig $config
     */
    public function __construct( SubscriptionCustomerLinkConfig $config ) {
        $this->config = $config;
    }

    /**
     * @return SubscriptionCustomerLink
     */
    public function getNullObject() {
        return new self($this->config);
    }

    /**
     * @param SubscriptionHeader $header
     */
    public function setSubscriptionHeader(SubscriptionHeader $header) {
      if($header->code !== $this->subscription_code) {
        throw new \InvalidArgumentException("SubscriptionHeader must have code '{$this->subscription_code}'.");
      }
      $this->subscriptionHeader = $header;
    }

    public function setOrderNo($orderNo)
    {
        $this->initial_order_no = $orderNo;
    }

}