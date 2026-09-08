<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Class SubscriptionCustomerLinkConfig
 * @package DynCom\dc\dcShop\subscriptions
 */
class SubscriptionCustomerLinkConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\subscriptions\classes\SubscriptionCustomerLink';
    protected $tableName      = 'shop_subscr_customer_link';
    protected $baseTableName  = 'shop_subscr_customer_link';
    protected $altPrimary     = array();
    protected $mappedFields   = array(
          array('name' => 'id', 'type' => 'int', 'maxlen' => '11'),
  array('name' => 'company', 'type' => 'varchar', 'maxlen' => '30'),
  array('name' => 'subscription_code', 'type' => 'varchar', 'maxlen' => '30'),
  array('name' => 'customer_no', 'type' => 'varchar', 'maxlen' => '30'),
  array('name' => 'webshop_order_email', 'type' => 'varchar', 'maxlen' => '100'),
  array('name' => 'line_no', 'type' => 'int', 'maxlen' => '11'),
  array('name' => 'bill_to_name', 'type' => 'varchar', 'maxlen' => '50'),
  array('name' => 'bill_to_name_2', 'type' => 'varchar', 'maxlen' => '50'),
  array('name' => 'bill_to_address', 'type' => 'varchar', 'maxlen' => '50'),
  array('name' => 'bill_to_address_2', 'type' => 'varchar', 'maxlen' => '50'),
  array('name' => 'bill_to_city', 'type' => 'varchar', 'maxlen' => '30'),
  array('name' => 'ship_to_name', 'type' => 'varchar', 'maxlen' => '50'),
  array('name' => 'ship_to_name_2', 'type' => 'varchar', 'maxlen' => '50'),
  array('name' => 'ship_to_address', 'type' => 'varchar', 'maxlen' => '50'),
  array('name' => 'ship_to_address_2', 'type' => 'varchar', 'maxlen' => '50'),
  array('name' => 'ship_to_city', 'type' => 'varchar', 'maxlen' => '30'),
  array('name' => 'currency_code', 'type' => 'varchar', 'maxlen' => '10'),
  array('name' => 'bill_to_post_code', 'type' => 'varchar', 'maxlen' => '20'),
  array('name' => 'bill_to_county', 'type' => 'varchar', 'maxlen' => '30'),
  array('name' => 'bill_to_country_region_code', 'type' => 'varchar', 'maxlen' => '10'),
  array('name' => 'ship_to_post_code', 'type' => 'varchar', 'maxlen' => '20'),
  array('name' => 'ship_to_county', 'type' => 'varchar', 'maxlen' => '30'),
  array('name' => 'ship_to_country_region_code', 'type' => 'varchar', 'maxlen' => '10'),
  array('name' => 'webshop_shop_code', 'type' => 'varchar', 'maxlen' => '10'),
  array('name' => 'webshop_language_code', 'type' => 'varchar', 'maxlen' => '10'),
  array('name' => 'webshop_pay_opt_line_no', 'type' => 'int', 'maxlen' => '11'),
  array('name' => 'webshop_ship_opt_line_no', 'type' => 'int', 'maxlen' => '11'),
  array('name' => 'customer_pseudo_pay_line_no', 'type' => 'int', 'maxlen' => '11'),
  array('name' => 'signup_date', 'type' => 'datetime'),
  array('name' => 'cancellation_date', 'type' => 'datetime'),
  array('name' => 'next_order_creation_date', 'type' => 'datetime'),
  array('name' => 'next_order_w_payment_date', 'type' => 'datetime'),
  array('name' => 'no_of_turns_processed', 'type' => 'int', 'maxlen' => '11'),
  array('name' => 'user_name', 'type' => 'varchar', 'maxlen' => '50'),
  array('name' => 'subscription_item_no', 'type' => 'varchar', 'maxlen' => '30'),
  array('name' => 'next_sequence_step_line_no', 'type' => 'int', 'maxlen' => '11'),
  array('name' => 'subscription_qty_per_turn', 'type' => 'decimal', 'maxlen' => ''),
  array('name' => 'initial_order_no', 'type' => 'int', 'maxlen' => '11'),
  array('name' => 'update_insert', 'type' => 'tinyint', 'maxlen' => '1'),
  array('name' => 'to_delete', 'type' => 'tinyint', 'maxlen' => '1')
    );

}