<?php
namespace DynCom\dc\dcShop\ShippingOptions;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Class ShippingZoneCollection
 */
class ShippingZoneCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param ShippingZoneConfig                                     $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( ShippingZoneConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return ShippingZoneCollection
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
        return $this->_addShippingZone($instance, $idCheck);
    }

    /**
     * @param ShippingZone $shippingZone
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _addShippingZone( ShippingZone $shippingZone, $idCheck = FALSE ) {
        return $this->_add($shippingZone, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeShippingZone($instance,$idCheck);
    }

    /**
     * @param ShippingZone $shippingZone
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _removeShippingZone( ShippingZone $shippingZone, $idCheck = FALSE ) {
        return $this->_remove($shippingZone,$idCheck);
    }

}