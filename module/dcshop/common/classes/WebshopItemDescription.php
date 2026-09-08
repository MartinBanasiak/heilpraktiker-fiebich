<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Class WebshopItemDescription
 * @package DynCom\dc\dcShop\classes
 */
class WebshopItemDescription implements GenericDBModelInterface, Entity
{

    use genericDBModelTrait, universallyGettableTrait;

    protected $id;
    protected $company;
    protected $shop_code;
    protected $language_code;
    protected $item_no;
    protected $line_no;
    protected $description;
    protected $content;
    protected $all_language_codes;
    protected $show_in_header;
    protected $marketplcae_only;
    protected $to_delete;

    /**
     * @param WebshopItemDescriptionConfig $config
     */
    public function __construct(WebshopItemDescriptionConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return WebshopItemDescription
     */
    public function getNullObject()
    {
        return new self($this->config);
    }


}