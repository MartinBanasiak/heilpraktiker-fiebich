<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 24.03.2015
 * Time: 10:09
 */

class SalesPriceConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\SalesPrice';
    protected $tableName        = 'shop_sales_price';
    protected $baseTableName    = 'shop_sales_price';
    protected $altPrimary       = array('company', 'type', 'item_no', 'sales_type', 'sales_code', 'starting_date', 'currency_code', 'variant_code', 'unit_of_measure_code', 'minimum_quantity');
    protected $mappedFields     = array(
        array('name' => 'id', 'type' => 'INT'),
        array('name' => 'company', 'type' => 'VARCHAR'),
        array('name' => 'type', 'type' => 'TINYINT'),
        array('name' => 'item_no', 'type' => 'VARCHAR'),
        array('name' => 'variant_code', 'type' => 'VARCHAR'),
        array('name' => 'currency_code', 'type' => 'VARCHAR'),
        array('name' => 'discount_group', 'type' => 'VARCHAR'),
        array('name' => 'sales_type', 'type' => 'TINYINT'),
        array('name' => 'sales_code', 'type' => 'VARCHAR'),
        array('name' => 'unit_price', 'type' => 'DECIMAL'),
        array('name' => 'line_discount', 'type' => 'DECIMAL'),
        array('name' => 'minimum_quantity', 'type' => 'DECIMAL'),
        array('name' => 'unit_of_measure_code', 'type' => 'VARCHAR'),
        array('name' => 'allow_line_disc', 'type' => 'TINYINT'),
        array('name' => 'allow_invoice_disc', 'type' => 'TINYINT'),
        array('name' => 'starting_date', 'type' => 'DATE'),
        array('name' => 'ending_date', 'type' => 'DATE'),
        array('name' => 'price_includes_vat', 'type' => 'TINYINT'),
        array('name' => 'to_delete')
    );

}