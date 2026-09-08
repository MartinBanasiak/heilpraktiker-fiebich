<?php
namespace DynCom\dc\dcShop\ShippingOptions;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;
/**
 * Class ShippingOption
 */
class ShippingOption implements GenericDBModelInterface, Entity
{

    use genericDBModelTrait, universallyGettableTrait;

    protected $id;
    protected $company;
    protected $shipping_group_code;
    protected $line_no;
    protected $shipping_agent_code;
    protected $shipping_agent_service_code;
    protected $weight_from;
    protected $weight_to;
    protected $amount_from;
    protected $amount_to;
    protected $valid_from;
    protected $valid_to;
    protected $shipping_cost;
    protected $exemption;
    protected $description;
    protected $logo;
    protected $content;
    protected $sorting;
    protected $shipping_class_code;
    protected $shipping_zone_code;
    protected $to_delete;



    /**
     * @param ShippingOptionConfig $config
     */
    public function __construct(ShippingOptionConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return ShippingOption
     */
    public function getNullObject()
    {
        return new self($this->config);
    }

}