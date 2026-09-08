<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Class CustomerPseudoPayDataConfig
 * @package DynCom\dc\dcShop\classes
 */
class CustomerPseudoPayDataConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\CustomerPseudoPayData';
    protected $tableName      = 'shop_customer_pseudo_pay_data';
    protected $baseTableName  = 'shop_customer_pseudo_pay_data';
    protected $altPrimary     = array();
    protected $mappedFields   = array(
          array('name' => 'id', 'type' => 'int', 'maxlen' => '11'),
  array('name' => 'customer_no', 'type' => 'varchar', 'maxlen' => '30'),
  array('name' => 'line_no', 'type' => 'int', 'maxlen' => '11'),
  array('name' => 'type', 'type' => 'int', 'maxlen' => '3'),
  array('name' => 'card_type', 'type' => 'int', 'maxlen' => '3'),
  array('name' => 'holder_owner_name', 'type' => 'varchar', 'maxlen' => '120'),
  array('name' => 'pseudo_card_no', 'type' => 'varchar', 'maxlen' => '20'),
  array('name' => 'card_expire_date', 'type' => 'date', 'maxlen' => '40'),
  array('name' => 'pseudo_iban', 'type' => 'varchar', 'maxlen' => '40'),
  array('name' => 'bank_name', 'type' => 'varchar', 'maxlen' => '50'),
  array('name' => 'to_delete', 'type' => 'tinyint', 'maxlen' => '1')
    );

}