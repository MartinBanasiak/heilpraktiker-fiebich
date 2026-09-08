<?php
namespace DynCom\dc\dcShop\ShippingOptions;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;
/**
 * Class ShippingClass
 */
class ShippingClass implements GenericDBModelInterface, Entity
{

    use genericDBModelTrait, universallyGettableTrait;

    protected $id;
    protected $company;
    protected $code;
    protected $description;
    protected $priority;
    protected $to_delete;



    /**
     * @param ShippingClassConfig $config
     */
    public function __construct(ShippingClassConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return ShippingClass
     */
    public function getNullObject()
    {
        return new self($this->config);
    }

}