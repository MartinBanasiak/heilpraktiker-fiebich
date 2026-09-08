<?php
namespace DynCom\dc\dcShop\campaigns;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

class GenericCampaignHeaderConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\GenericCampaign';
    protected $tableName      = 'shop_campaign_header';
    protected $baseTableName  = 'shop_campaign_header';
    protected $altPrimary     = ['code', 'company'];
    protected $mappedFields   = [
          ['name' => 'id', 'type' => 'int', 'maxlen' => '11'],
		  ['name' => 'company', 'type' => 'varchar', 'maxlen' => '30'],
		  ['name' => 'shop_code', 'type' => 'varchar', 'maxlen' => '20'],
		  ['name' => 'language_code', 'type' => 'varchar', 'maxlen' => '20'],
		  ['name' => 'all_shops', 'type' => 'tinyint', 'maxlen' => '1'],
		  ['name' => 'all_languages', 'type' => 'tinyint', 'maxlen' => '1'],
		  ['name' => 'code', 'type' => 'varchar', 'maxlen' => '20'],
		  ['name' => 'description', 'type' => 'varchar', 'maxlen' => '80'],
		  ['name' => 'active', 'type' => 'tinyint', 'maxlen' => '1'],
		  ['name' => 'active_from_date', 'type' => 'date', 'maxlen' => '1'],
		  ['name' => 'active_to_date', 'type' => 'date','maxlen' => '1'],
		  ['name' => 'active_from_time', 'type' => 'date', 'maxlen' => '1'],
		  ['name' => 'active_to_time', 'type' => 'date','maxlen' => '1'],
		  ['name' => 'multiply_applicable','type' => 'tinyint','maxlen' => '1'],
		  ['name' => 'priority', 'type' => 'int', 'maxlen' => '2'],
		  ['name' => 'cond_sum_amnt_all_items_gte', 'type' => 'decimal', 'maxlen' => ''],
		  ['name' => 'cond_sum_amnt_sing_item_gte', 'type' => 'decimal', 'maxlen' => ''],
		  ['name' => 'cond_sum_amnt_each_item_gte', 'type' => 'decimal', 'maxlen' => ''],
		  ['name' => 'cond_no_diff_items_gte', 'type' => 'int', 'maxlen' => '3'],
		  ['name' => 'cond_total_item_qty_gte', 'type' => 'decimal', 'maxlen' => ''],
		  ['name' => 'cond_qty_single_item_gte', 'type' => 'decimal', 'maxlen' => ''],
		  ['name' => 'cond_qty_each_item_gte', 'type' => 'decimal', 'maxlen' => ''],
		  ['name' => 'cond_basket_total_gte', 'type' => 'decimal', 'maxlen' => ''],
		  ['name' => 'discount_amnt', 'type' => 'decimal', 'maxlen' => ''],
		  ['name' => 'discount_amnt_applies_to', 'type' => 'int', 'maxlen' => '3'],
		  ['name' => 'discount_percent', 'type' => 'decimal', 'maxlen' => ''],
		  ['name' => 'discount_percent_applies_to', 'type' => 'int', 'maxlen' => '3'],
		  ['name' => 'discount_applied_to_qty_max', 'type' => 'int', 'maxlen' => '3'],
		  ['name' => 'qty_free_item', 'type' => 'decimal', 'maxlen' => ''],
		  ['name' => 'type_free_item', 'type' => 'int', 'maxlen' => '3'],
		  ['name' => 'alternative_shipping_option', 'type' => 'tinyint', 'maxlen' => '1'],
		  ['name' => 'icon_condition_items', 'type' => 'varchar', 'maxlen' => '80'],
		  ['name' => 'banner_condition_items', 'type' => 'varchar', 'maxlen' => '80'],
		  ['name' => 'text_condition_items', 'type' => 'varchar', 'maxlen' => '30'],
		  ['name' => 'icon_action_items', 'type' => 'varchar', 'maxlen' => '80'],
		  ['name' => 'banner_action_items', 'type' => 'varchar', 'maxlen' => '80'],
		  ['name' => 'text_action_items', 'type' => 'varchar', 'maxlen' => '30'],
		  ['name' => 'banner_basket', 'type' => 'varchar', 'maxlen' => '80'],
		  ['name' => 'text_basket', 'type' => 'varchar', 'maxlen' => '30'],
		  ['name' => 'to_delete', 'type' => 'tinyint', 'maxlen' => '1']
    ];

}