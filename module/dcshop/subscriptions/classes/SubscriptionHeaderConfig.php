<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Class SubscriptionHeaderConfig
 * @package DynCom\dc\dcShop\subscriptions
 */
class SubscriptionHeaderConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\subscriptions\classes\SubscriptionHeader';
    protected $tableName      = 'shop_subscription_header';
    protected $baseTableName  = 'shop_subscription_header';
    protected $altPrimary     = array('company','code');
    protected $mappedFields   = array(
          array('name' => 'id', 'type' => 'int', 'maxlen' => '11'),
          array('name' => 'company', 'type' => 'varchar', 'maxlen' => '30'),
          array('name' => 'code', 'type' => 'varchar', 'maxlen' => '30'),
          array('name' => 'type', 'type' => 'int', 'maxlen' => '1'),
          array('name' => 'description', 'type' => 'varchar', 'maxlen' => '80'),
          array('name' => 'item_no_subscription_item', 'type' => 'varchar', 'maxlen' => '30'),
          array('name' => 'payment_type', 'type' => 'int', 'maxlen' => '2'),
          array('name' => 'payment_type_description', 'type' => 'varchar', 'maxlen' => '50'),
          array('name' => 'start_type', 'type' => 'int', 'maxlen' => '1'),
          array('name' => 'start_date_formula', 'type' => 'varchar', 'maxlen' => '45'),
          array('name' => 'turn_interval_formula', 'type' => 'varchar', 'maxlen' => '45'),
          array('name' => 'turn_interval_description', 'type' => 'varchar', 'maxlen' => '50'),
          array('name' => 'subscription_duration', 'type' => 'varchar', 'maxlen' => '45'),
          array('name' => 'no_of_sequence_steps', 'type' => 'int', 'maxlen' => '3'),
          array('name' => 'no_of_items', 'type' => 'int', 'maxlen' => '11'),
          array('name' => 'no_of_customers', 'type' => 'int', 'maxlen' => '11'),
          array('name' => 'active', 'type' => 'tinyint', 'maxlen' => '1'),
          array('name' => 'valid_from', 'type' => 'date', 'maxlen' => '45'),
          array('name' => 'valid_to', 'type' => 'date', 'maxlen' => '45'),
          array('name' => 'cancel_period_date_formula', 'type' => 'varchar', 'maxlen' => '45'),
          array('name' => 'cancel_period_description', 'type' => 'varchar', 'maxlen' => '50'),
          array('name' => 'price_per_billing_interval', 'type' => 'decimal', 'maxlen' => ''),
          array('name' => 'orderable', 'type' => 'tinyint', 'maxlen' => '1'),
          array('name' => 'orderable_from', 'type' => 'date', 'maxlen' => '30'),
          array('name' => 'shop_code', 'type' => 'varchar', 'maxlen' => '30'),
          array('name' => 'language_code', 'type' => 'varchar', 'maxlen' => '10'),
          array('name' => 'all_shops', 'type' => 'tinyint', 'maxlen' => '1'),
          array('name' => 'all_languages', 'type' => 'tinyint', 'maxlen' => '1'),
          array('name' => 'first_order_email_text', 'type' => 'varchar', 'maxlen' => '50'),
          array('name' => 'shipment_email_text', 'type' => 'varchar', 'maxlen' => '50'),
          array('name' => 'last_ship_email_text', 'type' => 'varchar', 'maxlen' => '50'),
          array('name' => 'shipping_option_line_no', 'type' => 'int', 'maxlen' => '11'),
          array('name' => 'ref_pay_date_from_start_date', 'type' => 'varchar', 'maxlen' => '45'),
	  array('name' => 'allow_late_entry', 'type' => 'tinyint', 'maxlen' => '1'),
          array('name' => 'to_delete', 'type' => 'tinyint', 'maxlen' => '1')
    );

}