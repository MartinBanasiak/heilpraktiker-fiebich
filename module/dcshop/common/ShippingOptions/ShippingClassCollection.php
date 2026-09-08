<?php
namespace DynCom\dc\dcShop\ShippingOptions;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Class ShippingClassCollection
 */
class ShippingClassCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param ShippingClassConfig                                     $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct(ShippingClassConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return ShippingClassCollection
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
        return $this->_addShippingClass($instance, $idCheck);
    }

    /**
     * @param ShippingClass $shippingClass
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _addShippingClass(ShippingClass $shippingClass, $idCheck = FALSE ) {
        return $this->_add($shippingClass, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeShippingClass($instance,$idCheck);
    }

    /**
     * @param ShippingClass $shippingClass
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _removeShippingClass(ShippingClass $shippingClass, $idCheck = FALSE ) {
        return $this->_remove($shippingClass,$idCheck);
    }

}