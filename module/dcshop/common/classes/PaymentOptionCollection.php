<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Class PaymentOptionCollection
 */
class PaymentOptionCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param PaymentOptionConfig                                     $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( PaymentOptionConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return PaymentOptionCollection
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
        return $this->_addPaymentOption($instance, $idCheck);
    }

    /**
     * @param PaymentOption $paymentOption
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _addPaymentOption( PaymentOption $paymentOption, $idCheck = FALSE ) {
        return $this->_add($paymentOption, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removePaymentOption($instance,$idCheck);
    }

    /**
     * @param PaymentOption $paymentOption
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _removePaymentOption( PaymentOption $paymentOption, $idCheck = FALSE ) {
        return $this->_remove($paymentOption,$idCheck);
    }

}