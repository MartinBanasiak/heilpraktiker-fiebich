<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 19.01.2015
 * Time: 00:01
 */

class Language implements GenericDBModelInterface, Entity {

    use genericDBModelTrait, universallyGettableTrait;

    protected $id;
    protected $main_site_id;
    protected $code;
    protected $locale_code;
    protected $name;
    protected $site_name;
    protected $site_title_name;
    protected $main_layout_id;
    protected $meta_description;
    protected $meta_keywords;
    protected $std_main_navigation_id;
    protected $company;
    protected $shop_code;
    protected $shop_language_code;
    protected $logout_site_id;
    protected $logout_language_id;
    protected $logout_navigation_id;

    /**
     * @param LanguageConfig $config
     */
    public function __construct( LanguageConfig $config ) {
        $this->config = $config;
    }

    /**
     * @return Language
     */
    public function getNullObject() {
        return new self($this->config);
    }

    /**
     * @return array
     */
    public function getLinkedShopPrimary()
    {
        $primary = [];
        if ($this->company && $this->shop_code) {
            $primary['company'] = $this->company;
            $primary['code'] = $this->shop_code;
        }
        return $primary;
    }

    /**
     * @return array
     */
    public function getLinkedShopLanguagePrimary()
    {
        $primary = [];
        if ($this->company && $this->shop_code && $this->shop_language_code) {
            $primary['company'] = $this->company;
            $primary['shop_code'] = $this->shop_code;
            $primary['code'] = $this->shop_language_code;
        }
        return $primary;
    }

}