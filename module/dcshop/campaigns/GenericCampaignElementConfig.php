<?php
namespace DynCom\dc\dcShop\campaigns;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

class GenericCampaignElementConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\GenericCampaignElement';
    protected $tableName      = 'shop_campaign_element';
    protected $baseTableName  = 'shop_campaign_element';
    protected $altPrimary     = ['link_to_header_code', 'link_type', 'type', 'company', 'item_no', 'item_var_code', 'category_line_no'];
    protected $mappedFields   = [
          ['name' => 'id', 'type' => 'int', 'maxlen' => '11'],
		  ['name' => 'company', 'type' => 'varchar', 'maxlen' => '30'],
		  ['name' => 'link_to_header_code', 'type' => 'varchar', 'maxlen' => '20'],
		  ['name' => 'link_type', 'type' => 'int', 'maxlen' => '3'],
		  ['name' => 'type', 'type' => 'int', 'maxlen' => '3'],
		  ['name' => 'include_exclude', 'type' => 'int', 'maxlen' => '3'],
		  ['name' => 'item_no', 'type' => 'varchar', 'maxlen' => '30'],
		  ['name' => 'item_var_code', 'type' => 'varchar', 'maxlen' => '10'],
		  ['name' => 'category_line_no', 'type' => 'int', 'maxlen' => '11'],
		  ['name' => 'to_delete', 'type' => 'tinyint', 'maxlen' => '1']
    ];

}