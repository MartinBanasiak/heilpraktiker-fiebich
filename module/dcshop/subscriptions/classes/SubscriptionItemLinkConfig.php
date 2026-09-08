<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Class SubscriptionItemLinkConfig
 * @package DynCom\dc\dcShop\subscriptions
 */
class SubscriptionItemLinkConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\subscriptions\classes\SubscriptionItemLink';
    protected $tableName      = 'shop_subscription_item_link';
    protected $baseTableName  = 'shop_subscription_item_link';
    protected $altPrimary     = array('company','subscription_code','link_to','item_no','variant_code','subscr_seq_step_line_no');
    protected $mappedFields   = array(
          array('name' => 'id', 'type' => 'int', 'maxlen' => '11'),
          array('name' => 'company', 'type' => 'varchar', 'maxlen' => '30'),
          array('name' => 'subscription_code', 'type' => 'varchar', 'maxlen' => '30'),
          array('name' => 'link_to', 'type' => 'tinyint', 'maxlen' => '1'),
          array('name' => 'item_no', 'type' => 'varchar', 'maxlen' => '30'),
          array('name' => 'variant_code', 'type' => 'varchar', 'maxlen' => '10'),
          array('name' => 'subscr_seq_step_line_no', 'type' => 'int', 'maxlen' => '11'),
          array('name' => 'description', 'type' => 'varchar', 'maxlen' => '80'),
          array('name' => 'fix_price', 'type' => 'decimal', 'maxlen' => ''),
          array('name' => 'discount_percent', 'type' => 'decimal', 'maxlen' => ''),
          array('name' => 'min_quantity', 'type' => 'decimal', 'maxlen' => ''),
          array('name' => 'max_quantity', 'type' => 'decimal', 'maxlen' => ''),
          array('name' => 'price_includes_vat', 'type' => 'tinyint', 'maxlen' => '1'),
          array('name' => 'quantity', 'type' => 'decimal', 'maxlen' => ''),
          array('name' => 'to_delete', 'type' => 'tinyint', 'maxlen' => '1')
    );

}