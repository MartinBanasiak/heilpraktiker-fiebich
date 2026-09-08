<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;
/**
 * Class CustomerPseudoPayData
 */
class CustomerPseudoPayData implements GenericDBModelInterface, Entity {

    use genericDBModelTrait, universallyGettableTrait;

      protected $id;
      protected $customer_no;
      protected $line_no;
      protected $type;
      protected $card_type;
      protected $holder_owner_name;
      protected $pseudo_card_no;
      protected $card_expire_date;
      protected $bic;
      protected $pseudo_iban;
      protected $bank_name;
      protected $to_delete;

    /**
     * @param |CustomerPseudoPayDataConfig $config
     */
    public function __construct( CustomerPseudoPayDataConfig $config ) {
        $this->config = $config;
    }

    /**
     * @return CustomerPseudoPayData
     */
    public function getNullObject() {
        return new self($this->config);
    }

}