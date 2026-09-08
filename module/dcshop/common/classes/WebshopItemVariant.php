<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Class WebshopItemVariant
 */
class WebshopItemVariant implements GenericDBModelInterface, Entity {

    use genericDBModelTrait, universallyGettableTrait;

      protected $id;
	  protected $company;
	  protected $item_no;
	  protected $code;
	  protected $description;
	  protected $description_2;
	  protected $inventory;
	  protected $to_delete;

    /**
     * @param WebshopItemVariantConfig $config
     */
    public function __construct( WebshopItemVariantConfig $config ) {
        $this->config = $config;
    }

    /**
     * @return WebshopItemVariant
     */
    public function getNullObject() {
        return new self($this->config);
    }

    /**
     * @return string
     */
    public function getCode() {
        return (string)$this->code;
    }

    /**
     * @return float
     */
    public function getinventory() {
        return (float)$this->inventory;
    }

}