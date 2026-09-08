<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Class CouponHeaderConfig
 * @package DynCom\dc\dcShop\classes
 */
class CouponHeaderConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\CouponHeader';
    protected $tableName      = 'shop_coupon_header';
    protected $baseTableName  = 'shop_coupon_header';
    protected $altPrimary     = array('company', 'code');
    protected $mappedFields   = array(
          array('name' => 'id', 'type' => 'int', 'maxlen' => '11'),
          array('name' => 'company', 'type' => 'varchar', 'maxlen' => '30'),
          array('name' => 'shop_code', 'type' => 'varchar', 'maxlen' => '10'),
          array('name' => 'language_code', 'type' => 'varchar', 'maxlen' => '10'),
          array('name' => 'code', 'type' => 'varchar', 'maxlen' => '20'),
          array('name' => 'description', 'type' => 'varchar', 'maxlen' => '50'),
          array('name' => 'coupon_type', 'type' => 'tinyint', 'maxlen' => '1'),
          array('name' => 'value_type', 'type' => 'tinyint', 'maxlen' => '1'),
          array('name' => 'amount', 'type' => 'decimal', 'maxlen' => ''),
          array('name' => 'percentage', 'type' => 'decimal', 'maxlen' => ''),
          array('name' => 'item_no', 'type' => 'varchar', 'maxlen' => '20'),
          array('name' => 'valid_from', 'type' => 'date', 'maxlen' => ''),
          array('name' => 'value_coupon', 'type' => 'tinyint', 'maxlen' => '1'),
          array('name' => 'category_coupon', 'type' => 'tinyint', 'maxlen' => '1'),
          array('name' => 'category_line_no', 'type' => 'int', 'maxlen' => '11'),
          array('name' => 'to_delete', 'type' => 'tinyint', 'maxlen' => '1')
    );

    protected $linesClassName             = 'CouponLine';
    protected $linesCriteriaFieldMappings = array(
        array('company', '=', 'company'),
        array('shop_code', '=', 'shop_code'),
        array('language_code','=','language_code'),
        array('code','=','code')
    );

    public function getLinesClassName()
    {
        return $this->linesClassName;
    }

    public function getLineCriteriaFieldMappings()
    {
        return $this->linesCriteriaFieldMappings;
    }

}