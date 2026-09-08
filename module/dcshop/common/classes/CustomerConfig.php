<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 14.01.2015
 * Time: 19:14
 */
class CustomerConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\Customer';
    protected $tableName      = 'shop_customer';
    protected $baseTableName  = 'shop_customer';
    protected $altPrimary     = array('company', 'shop_code', 'language_code', 'customer_no');
    protected $mappedFields   = array(
        array('name' => 'id', 'type' => 'INT'),
        array('name' => 'company', 'type' => 'VARCHAR'),
        array('name' => 'shop_code', 'type' => 'VARCHAR'),
        array('name' => 'language_code', 'type' => 'VARCHAR'),
        array('name' => 'customer_no', 'type' => 'VARCHAR'),
        array('name' => 'name', 'type' => 'VARCHAR'),
        array('name' => 'name_2', 'type' => 'VARCHAR'),
        array('name' => 'address', 'type' => 'VARCHAR'),
        array('name' => 'address_2', 'type' => 'VARCHAR'),
        array('name' => 'address_street', 'type' => 'VARCHAR'),
        array('name' => 'address_no', 'type' => 'VARCHAR'),
        array('name' => 'post_code', 'type' => 'VARCHAR'),
        array('name' => 'city', 'type' => 'VARCHAR'),
        array('name' => 'country', 'type' => 'VARCHAR'),
        array('name' => 'phone_no', 'type' => 'VARCHAR'),
        array('name' => 'fax_no', 'type' => 'VARCHAR'),
        array('name' => 'email', 'type' => 'VARCHAR'),
        array('name' => 'homepage', 'type' => 'VARCHAR'),
        array('name' => 'salesperson_code', 'type' => 'VARCHAR'),
        array('name' => 'active', 'type' => 'TINYINT'),
        array('name' => 'currency_code', 'type' => 'VARCHAR'),
        array('name' => 'bill_to_customer_no', 'type' => 'VARCHAR'),
        array('name' => 'bill_to_name', 'type' => 'VARCHAR'),
        array('name' => 'bill_to_name_2', 'type' => 'VARCHAR'),
        array('name' => 'bill_to_address', 'type' => 'VARCHAR'),
        array('name' => 'bill_to_address_2', 'type' => 'VARCHAR'),
        array('name' => 'bill_to_post_code', 'type' => 'VARCHAR'),
        array('name' => 'bill_to_city', 'type' => 'VARCHAR'),
        array('name' => 'bill_to_country', 'type' => 'VARCHAR'),
        array('name' => 'customer_price_group', 'type' => 'VARCHAR'),
        array('name' => 'vat_bus_posting_group', 'type' => 'VARCHAR'),
        array('name' => 'invoice_disc_code', 'type' => 'VARCHAR'),
        array('name' => 'customer_disc_group', 'type' => 'VARCHAR'),
        array('name' => 'payment_terms', 'type' => 'VARCHAR'),
        array('name' => 'surname', 'type' => 'VARCHAR'),
        array('name' => 'lastname', 'type' => 'VARCHAR'),
        array('name' => 'company_name', 'type' => 'VARCHAR'),
        array('name' => 'salutation', 'type' => 'VARCHAR'),
        array('name' => 'to_delete', 'type' => 'TINYINT')
    );

}