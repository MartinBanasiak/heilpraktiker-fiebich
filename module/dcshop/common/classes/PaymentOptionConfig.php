<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Class PaymentOptionConfig
 * @package DynCom\dc\dcShop\classes
 */
class PaymentOptionConfig implements ModelDBConfigInterface
{

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\PaymentOption';
    protected $tableName = 'shop_payment_option';
    protected $baseTableName = 'shop_payment_option';
    protected $altPrimary = array();
    protected $mappedFields = array(
        array('name' => 'id', 'type' => 'int', 'maxlen' => '11'),
        array('name' => 'company', 'type' => 'varchar', 'maxlen' => '30'),
        array('name' => 'shop_code', 'type' => 'varchar', 'maxlen' => '10'),
        array('name' => 'language_code', 'type' => 'varchar', 'maxlen' => '10'),
        array('name' => 'line_no', 'type' => 'int', 'maxlen' => '11'),
        array('name' => 'description', 'type' => 'varchar', 'maxlen' => '50'),
        array('name' => 'country_code', 'type' => 'varchar', 'maxlen' => '10'),
        array('name' => 'checkout', 'type' => 'tinyint', 'maxlen' => '1'),
        array('name' => 'checkout_state', 'type' => 'tinyint', 'maxlen' => '1'),
        array('name' => 'payment_cost', 'type' => 'decimal', 'maxlen' => ''),
        array('name' => 'credit_check_required', 'type' => 'tinyint', 'maxlen' => '1'),
        array('name' => 'dc_active', 'type' => 'tinyint', 'maxlen' => '1'),
        array('name' => 'active', 'type' => 'tinyint', 'maxlen' => '1'),
        array('name' => 'to_delete', 'type' => 'tinyint', 'maxlen' => '1'),
        array('name' => 'recurrent_payment_active', 'type' => 'tinyint', 'maxlen' => '1'),
        array('name' => 'logo', 'type' => 'varchar', 'maxlen' => '100'),
        array('name' => 'content', 'type' => 'varchar', 'maxlen' => '250'),
        array('name' => 'billpay_agb_text_module', 'type' => 'varchar', 'maxlen' => '250'),
        array('name' => 'text_module_order_conf', 'type' => 'varchar', 'maxlen' => '80'),
    );

}