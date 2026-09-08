<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Class SubscriptionSequenceStepConfig
 * @package DynCom\dc\dcShop\subscriptions
 */
class SubscriptionSequenceStepConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\subscriptions\classes\SubscriptionSequenceStep';
    protected $tableName      = 'shop_subscription_sequence_step';
    protected $baseTableName  = 'shop_subscription_sequence_step';
    protected $altPrimary     = array();
    protected $mappedFields   = array(
          array('name' => 'id', 'type' => 'int', 'maxlen' => '11'),
  array('name' => 'company', 'type' => 'varchar', 'maxlen' => '30'),
  array('name' => 'subscription_code', 'type' => 'varchar', 'maxlen' => '30'),
  array('name' => 'line_no', 'type' => 'int', 'maxlen' => '11'),
  array('name' => 'fix_date', 'type' => 'date', 'maxlen' => '45'),
  array('name' => 'no_of_items', 'type' => 'int', 'maxlen' => '11'),
  array('name' => 'to_delete', 'type' => 'varchar', 'maxlen' => '45')
    );

}