<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Class Country
 */
class Country implements GenericDBModelInterface, Entity {

    use genericDBModelTrait, universallyGettableTrait;

      protected $id;
  protected $company;
  protected $shop_code;
  protected $language_code;
  protected $country_code;
  protected $description;
  protected $invoice_to;
  protected $ship_to;
  protected $to_delete;

    /**
     * @param CountryConfig $config
     */
    public function __construct( CountryConfig $config ) {
        $this->config = $config;
    }

    /**
     * @return Country
     */
    public function getNullObject() {
        return new self($this->config);
    }

}