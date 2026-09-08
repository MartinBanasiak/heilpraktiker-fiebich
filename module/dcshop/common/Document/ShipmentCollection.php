<?php
namespace DynCom\dc\dcShop\Document;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:23
 */
class ShipmentCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    protected $linesConfig;

    /**
     * @param ShipmentConfig                                     $config
     * @param ShipmentLineConfig                                 $linesConfig
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( ShipmentConfig $config, ShipmentLineConfig $linesConfig, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  = new \SplObjectStorage();
        $this->config                    = $config;
        $this->linesConfig               = $linesConfig;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return ShipmentCollection
     */
    public function getEmptyCollection() {
        return new self($this->config, $this->linesConfig, $this->criteriaValidationService);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function add( $instance, $idCheck = FALSE ) {
        return $this->_addShipment($instance, $idCheck);
    }

    /**
     * @param ShipmentDocument $shipment
     * @param bool              $idCheck
     *
     * @return bool
     */
    protected function _addShipment( ShipmentDocument $shipment, $idCheck = FALSE ) {
        return $this->_add($shipment, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeShipment($instance, $idCheck);
    }

    /**
     * @param ShipmentDocument $shipment
     * @param bool              $idCheck
     *
     * @return bool
     */
    protected function _removeShipment( ShipmentDocument $shipment, $idCheck = FALSE ) {
        return $this->_remove($shipment, $idCheck);
    }

    /**
     * @return ShipmentDocument
     */
    public function getNullObject() {
        return new ShipmentDocument($this->config, $this->linesConfig, $this->criteriaValidationService);
    }

}