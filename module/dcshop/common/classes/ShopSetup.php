<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Class ShopSetup
 */
class ShopSetup implements GenericDBModelInterface, Entity
{

    use universallyGettableTrait,
        genericDBModelTrait
    {
        genericDBModelTrait::mapFromArray as parentMapFromArray;
    }
	
	
    protected $id;
    protected $company;
    protected $nas_email_text_module;
	protected $nas_email_recipient;
	protected $nas_email_recipient_2;
	protected $nas_email_sender;
	protected $default_currency_code;
	protected $last_datetime_nav_updated;
	protected $last_datetime_solr_updated;
	protected $default_shipping_class_priority;
	protected $to_delete;


    /**
     * @param ShopSetupConfig $config
     */
    public function __construct( ShopSetupConfig $config ) {
        $this->config = $config;
    }

    /**
     * @return ShopSetup
     */
    public function getNullObject() {
        return new self($this->config);
    }


}