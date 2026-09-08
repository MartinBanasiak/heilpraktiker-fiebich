<?php
namespace DynCom\dc\dcShop\ShippingOptions;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Class ShippingOptionCollection
 */
class ShippingOptionCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param ShippingOptionConfig                                     $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( ShippingOptionConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return ShippingOptionCollection
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
        return $this->_addShippingOption($instance, $idCheck);
    }

    /**
     * @param ShippingOption $shippingOption
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _addShippingOption( ShippingOption $shippingOption, $idCheck = FALSE ) {
        return $this->_add($shippingOption, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeShippingOption($instance,$idCheck);
    }

    /**
     * @param ShippingOption $shippingOption
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _removeShippingOption( ShippingOption $shippingOption, $idCheck = FALSE ) {
        return $this->_remove($shippingOption,$idCheck);
    }

}