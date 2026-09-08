<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 19.01.2015
 * Time: 12:53
 */

class ShipmentLineCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    protected $docConfig;
    protected $itemConfig;

    /**
     * @param ShipmentLineConfig                                 $config
     * @param ShipmentConfig                                     $docConfig
     * @param WebshopItemConfig                                  $itemConfig
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( ShipmentLineConfig $config, ShipmentConfig $docConfig, WebshopItemConfig $itemConfig, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  = new \SplObjectStorage();
        $this->config                    = $config;
        $this->docConfig                 = $docConfig;
        $this->itemConfig                = $itemConfig;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return ShipmentLineCollection
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
        return $this->_addShipmentLine($instance, $idCheck);
    }

    /**
     * @param ShipmentLine $shipmentLine
     * @param bool         $idCheck
     *
     * @return bool
     */
    protected function _addShipmentLine( ShipmentLine $shipmentLine, $idCheck = FALSE ) {
        return $this->_add($shipmentLine, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeShipmentLine($instance,$idCheck);
    }

    /**
     * @param ShipmentLine $shipmentLine
     * @param bool         $idCheck
     *
     * @return bool
     */
    protected function _removeShipmentLine( ShipmentLine $shipmentLine, $idCheck = FALSE ) {
        return $this->_remove($shipmentLine, $idCheck);
    }

    /**
     * @return ShipmentLine
     */
    public function getNullObject() {
        return new ShipmentLine($this->config,$this->docConfig,$this->itemConfig,$this->criteriaValidationService);
    }
}