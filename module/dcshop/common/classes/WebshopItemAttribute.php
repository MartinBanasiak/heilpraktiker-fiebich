<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 24.10.2016
 * Time: 12:21
 */
class WebshopItemAttribute implements Entity,GenericDBModelInterface
{
    use genericDBModelTrait, universallyGettableTrait;

    public const VALUE_TYPE_OPTION = 0;
    public const VALUE_TYPE_INTEGER = 1;
    public const VALUE_TYPE_DECIMAL = 2;
    public const VALUE_TYPE_BOOLEAN = 3;
    public const VALUE_TYPE_TEXT = 4;

    public const DISPLAY_TYPE_LIST = 0;
    public const DISPLAY_TYPE_SCROLL_LIST = 1;
    public const DISPLAY_TYPE_DROPDOWN = 2;
    public const DISPLAY_TYPE_SLIDER = 3;
    public const DISLPAY_TYPE_CHECKBOX = 4;
    public const DISPLAY_TYPE_ICONS = 5;

    //Id is composed of attribute-id and attribute-link-id
    protected $id;
    protected $company;
    protected $shop_code;
    protected $language_code;
    protected $item_no;
    protected $variant_code;
    protected $attribute_code;
    protected $attribute_description;
    protected $attribute_value;
    protected $attribute_value_type;
    protected $attribute_display_type;
    protected $icon_filename;
    protected $filter_expression;
    protected $option_link;

    /**
     * WebshopItemAttribute constructor.
     * @param WebshopItemAttributeConfig $attributeConfig
     */
    public function __construct(WebshopItemAttributeConfig $attributeConfig)
    {
        $this->config = $attributeConfig;
    }

    /**
     * @return WebshopItemAttribute
     */
    public function getNullObject() {
        return new self($this->config);
    }

}