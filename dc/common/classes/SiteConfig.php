<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 18.01.2015
 * Time: 23:40
 */

class SiteConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\common\classes\Site';
    protected $tableName      = 'main_site';
    protected $altPrimary     = array('code');
    protected $mappedFields   = array(
        array('name'=>'id','type'=>'INT'),
        array('name'=>'code','type'=>'VARCHAR'),
        array('name'=>'name','type'=>'VARCHAR'),
        array('name'=>'std_main_language_id','type'=>'INT'),
        array('name'=>'login_required','type'=>'TINYINT'),
        array('name'=>'login_type','type'=>'TINYINT'),
        array('name'=>'google_analytics_id','type'=>'VARCHAR'),
        array('name'=>'use_session_id','type'=>'TINYINT'),
        array('name'=>'create_xml_sitemap','type'=>'TINYINT'),
        array('name'=>'site_url','type'=>'VARCHAR'),
        array('name'=>'is_standard_site','type'=>'TINYINT'),
        array('name'=>'use_ssl','type'=>'TINYINT'),
    );

}