<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 19.01.2015
 * Time: 00:01
 */

class LanguageConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\common\classes\Language';
    protected $tableName      = 'main_language';
    protected $baseTableName  = 'main_language';
    protected $altPrimary     = array('main_site_id','code');
    protected $mappedFields   = array(
        array('name'=>'id','type'=>'INT'),
        array('name'=>'main_site_id','type'=>'INT'),
        array('name'=>'code','type'=>'VARCHAR'),
        array('name' =>'locale_code', 'type' => 'VARCHAR'),
        array('name'=>'name','type'=>'VARCHAR'),
        array('name'=>'site_name','type'=>'VARCHAR'),
        array('name'=>'site_title_name','type'=>'VARCHAR'),
        array('name'=>'main_layout_id','type'=>'INT'),
        array('name'=>'meta_description','type'=>'LONGTEXT'),
        array('name'=>'meta_keywords','type'=>'LONGTEXT'),
        array('name'=>'std_main_navigation_id','type'=>'INT'),
        array('name'=>'company','type'=>'VARCHAR'),
        array('name'=>'shop_code','type'=>'VARCHAR'),
        array('name'=>'shop_language_code','type'=>'VARCHAR'),
        array('name'=>'logout_site_id','type'=>'INT'),
        array('name'=>'logout_language_id','type'=>'INT'),
        array('name'=>'logout_navigation_id','type'=>'INT')
    );

}