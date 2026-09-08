<?php
namespace DynCom\dc\dcShop\campaigns;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Class GenericCampaignElement
 */
class GenericCampaignElement implements GenericDBModelInterface, Entity
{

    use genericDBModelTrait, universallyGettableTrait;

    const LINK_TYPE_CONDITION = 0;
    const LINK_TYPE_ACTION  = 1;

    const TYPE_ITEM = 0;
    const TYPE_CATEGORY = 1;

    const SET_OPERATOR_INCLUDE = 0;
    const SET_OPERATOR_EXCLUDE = 1;

    protected $id;
    protected $company;
    protected $link_to_header_code;
    protected $link_type;
    protected $type;
    protected $include_exclude;
    protected $item_no;
    protected $item_var_code;
    protected $category_line_no;
    protected $to_delete;

    /**
     * @param GenericCampaignElementConfig $config
     */
    public function __construct(GenericCampaignElementConfig $config)
    {
        $this->config = $config;
    }

    public function getNullObject()
    {
        return new self($this->config);
    }

    public function typeIsItem()
    {
        return (int)$this->type === self::TYPE_ITEM;
    }

    public function typeIsCategory()
    {
        return (int)$this->type === self::TYPE_CATEGORY;
    }

    public function getType()
    {
        return (int)$this->type;
    }

}