<?php
namespace DynCom\dc\dcShop\ShippingOptions;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;
/**
 * Class ShippingOptionTranslation
 */
class ShippingOptionTranslation implements GenericDBModelInterface, Entity
{

    use genericDBModelTrait, universallyGettableTrait;

    protected $id;
    protected $company;
    protected $code;
    protected $description;
    protected $priority;
    protected $to_delete;



    /**
     * @param ShippingOptionTranslationConfig $config
     */
    public function __construct(ShippingOptionTranslationConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return ShippingOptionTranslation
     */
    public function getNullObject()
    {
        return new self($this->config);
    }

}