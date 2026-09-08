<?php
namespace DynCom\dc\dcShop\Document;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;
use DynCom\dc\dcShop\classes\WebshopItemConfig;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:20
 */
class CreditMemoLineCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    protected $docConfig;
    protected $itemConfig;

    /**
     * @param CreditMemoLineConfig                                  $config
     * @param CreditMemoConfig                                      $docConfig
     * @param WebshopItemConfig                                  $itemConfig
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( CreditMemoLineConfig $config, CreditMemoConfig $docConfig, WebshopItemConfig $itemConfig, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  = new \SplObjectStorage();
        $this->config                    = $config;
        $this->docConfig                 = $docConfig;
        $this->itemConfig                = $itemConfig;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return CreditMemoLineCollection
     */
    public function getEmptyCollection() {
        return new self($this->config,$this->docConfig,$this->itemConfig,$this->criteriaValidationService);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function add( $instance, $idCheck = FALSE ) {
        return $this->_addCreditMemoLine($instance, $idCheck);
    }

    /**
     * @param CreditMemoLine $creditMemoLine
     * @param bool         $idCheck
     *
     * @return bool
     */
    protected function _addCreditMemoLine( CreditMemoLine $creditMemoLine, $idCheck = FALSE ) {
        return $this->_add($creditMemoLine, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeCreditMemoLine($instance,$idCheck);
    }

    /**
     * @param CreditMemoLine $creditMemoLine
     * @param bool         $idCheck
     *
     * @return bool
     */
    protected function _removeCreditMemoLine( CreditMemoLine $creditMemoLine, $idCheck = FALSE ) {
        return $this->_remove($creditMemoLine, $idCheck);
    }

    /**
     * @return CreditMemoLine
     */
    public function getNullObject() {
        return new CreditMemoLine($this->config,$this->docConfig,$this->itemConfig,$this->criteriaValidationService);
    }
}