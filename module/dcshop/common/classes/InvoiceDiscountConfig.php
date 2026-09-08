<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Class InvoiceDiscountConfig
 * @package DynCom\dc\dcShop\classes
 */
class InvoiceDiscountConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\InvoiceDiscount';
    protected $tableName      = 'shop_invoice_discount';
    protected $baseTableName  = 'shop_invoice_discount';
    protected $altPrimary     = array();
    protected $mappedFields   = array(
          array('name' => 'id', 'type' => 'int', 'maxlen' => '11'),
  array('name' => 'company', 'type' => 'varchar', 'maxlen' => '30'),
  array('name' => 'invoice_discount_code', 'type' => 'varchar', 'maxlen' => '10'),
  array('name' => 'currency_code', 'type' => 'varchar', 'maxlen' => '20'),
  array('name' => 'minimum_amount', 'type' => 'decimal', 'maxlen' => ''),
  array('name' => 'discount', 'type' => 'decimal', 'maxlen' => ''),
  array('name' => 'service_charge', 'type' => 'decimal', 'maxlen' => ''),
  array('name' => 'to_delete', 'type' => 'tinyint', 'maxlen' => '1')
    );

}