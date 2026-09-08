<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;
/**
 * Class ShipmentAddress
 */
class ShipmentAddress implements GenericDBModelInterface, Entity {

    use genericDBModelTrait, universallyGettableTrait;

      protected $id;
  protected $company;
  protected $customer_no;
  protected $code;
  protected $name;
  protected $name_2;
  protected $contact;
  protected $address;
  protected $address_2;
  protected $address_street;
  protected $address_no;
  protected $post_code;
  protected $city;
  protected $country;
  protected $phone_no;
  protected $surname;
  protected $lastname;
  protected $company_name;
  protected $to_delete;

    /**
     * @param CustomerAddressConfig $config
     */
    public function __construct(CustomerAddressConfig $config ) {
        $this->config = $config;
    }

    /**
     * @return ShipmentAddress
     */
    public function getNullObject() {
        return new self($this->config);
    }

}