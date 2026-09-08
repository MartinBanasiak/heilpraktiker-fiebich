<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 18.01.2015
 * Time: 23:43
 */

class Site implements GenericDBModelInterface, Entity {

    use genericDBModelTrait, universallyGettableTrait;

    protected $id;
    protected $code;
    protected $name;
    protected $std_main_language_id;
    protected $login_required;
    protected $login_type;
    protected $google_analytics_id;
    protected $use_session_id;
    protected $create_xml_sitemap;
    protected $site_url;
    protected $is_standard_site;
    protected $use_ssl;

    /**
     * @param SiteConfig $config
     */
    public function __construct( SiteConfig $config ) {
        $this->config = $config;
    }

    /**
     * @return Site
     */
    public function getNullObject() {
        return new self($this->config);
    }


}