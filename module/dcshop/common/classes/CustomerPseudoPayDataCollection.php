<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Class CustomerPseudoPayDataCollection
 */
class CustomerPseudoPayDataCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * CustomerPseudoPayDataCollection constructor.
     * @param CustomerPseudoPayDataConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( CustomerPseudoPayDataConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return CustomerPseudoPayDataCollection
     */
    public function getEmptyCollection() {
        return new self($this->config,$this->criteriaValidationService);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function add( $instance, $idCheck = FALSE ) {
        return $this->_addCustomerPseudoPayData($instance, $idCheck);
    }

    /**
     * @param CustomerPseudoPayData $customerPseudoPayData
     * @param bool $idCheck
     * @return bool
     */
    protected function _addCustomerPseudoPayData( CustomerPseudoPayData $customerPseudoPayData, $idCheck = FALSE ) {
        return $this->_add($customerPseudoPayData, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeCustomerPseudoPayData($instance,$idCheck);
    }

    /**
     * @param CustomerPseudoPayData $customerPseudoPayData
     * @param bool $idCheck
     * @return bool
     */
    protected function _removeCustomerPseudoPayData( CustomerPseudoPayData $customerPseudoPayData, $idCheck = FALSE ) {
        return $this->_remove($customerPseudoPayData,$idCheck);
    }

}