<?php
namespace DynCom\dc\dcShop\ShippingOptions;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Class ShippingZoneLineCollection
 */
class ShippingZoneLineCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param ShippingZoneLineConfig                                     $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( ShippingZoneLineConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return ShippingZoneLineCollection
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
        return $this->_addShippingZoneLine($instance, $idCheck);
    }

    /**
     * @param ShippingZoneLine $shippingZoneLine
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _addShippingZoneLine( ShippingZoneLine $shippingZoneLine, $idCheck = FALSE ) {
        return $this->_add($shippingZoneLine, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeShippingZoneLine($instance,$idCheck);
    }

    /**
     * @param ShippingZoneLine $shippingZoneLine
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _removeShippingZoneLine( ShippingZoneLine $shippingZoneLine, $idCheck = FALSE ) {
        return $this->_remove($shippingZoneLine,$idCheck);
    }

}