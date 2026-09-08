<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Class CustomerTemplateConfig
 * @package DynCom\dc\dcShop\classes
 */
class CustomerTemplateConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\CustomerTemplate';
    protected $tableName      = 'shop_country';
    protected $baseTableName  = 'shop_country';
    protected $altPrimary     = array();
    protected $mappedFields   = array(
          array('name' => 'id', 						'type' => 'int', 		'maxlen' => '11'),
          array('name' => 'company', 					'type' => 'varchar', 	'maxlen' => '30'),
          array('name' => 'code', 						'type' => 'varchar', 	'maxlen' => '10'),
		  array('name' => 'description', 				'type' => 'varchar', 	'maxlen' => '50'),
		  array('name' => 'territory_code', 			'type' => 'varchar', 	'maxlen' => '10'),
		  array('name' => 'global_dimension_1_code', 	'type' => 'varchar', 	'maxlen' => '20'),
		  array('name' => 'global_dimension_2_code', 	'type' => 'varchar', 	'maxlen' => '20'),
		  array('name' => 'customer_posting_group', 	'type' => 'varchar', 	'maxlen' => '20'),
		  array('name' => 'currency_code', 				'type' => 'varchar', 	'maxlen' => '10'),
		  array('name' => 'customer_price_group', 		'type' => 'varchar', 	'maxlen' => '10'),
		  array('name' => 'payment_terms_code', 		'type' => 'varchar', 	'maxlen' => '10'),
		  array('name' => 'shipment_method_code', 		'type' => 'varchar', 	'maxlen' => '10'),
		  array('name' => 'invoice_disc_code', 			'type' => 'varchar', 	'maxlen' => '20'),
		  array('name' => 'customer_disc_group', 		'type' => 'varchar', 	'maxlen' => '20'),
		  array('name' => 'country_region_code', 		'type' => 'varchar', 	'maxlen' => '10'),
		  array('name' => 'payment_method_code', 		'type' => 'varchar', 	'maxlen' => '10'),
		  array('name' => 'prices_including_vat', 		'type' => 'tinyint', 	'maxlen' => '1'),
		  array('name' => 'gen_bus_posting_group', 		'type' => 'varchar', 	'maxlen' => '20'),
		  array('name' => 'vat_bus_posting_group', 		'type' => 'varchar', 	'maxlen' => '20'),
		  array('name' => 'contact_type', 				'type' => 'int', 		'maxlen' => '11'),
		  array('name' => 'allow_line_disc', 			'type' => 'tinyint', 	'maxlen' => '1'),
          array('name' => 'to_delete', 					'type' => 'tinyint', 	'maxlen' => '1')
    );

}