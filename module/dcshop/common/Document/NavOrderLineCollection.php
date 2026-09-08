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
 * Time: 11:22
 */
class NavOrderLineCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    protected $docConfig;
    protected $itemConfig;

    /**
     * @param NavOrderLineConfig                                 $config
     * @param NavOrderConfig                                     $docConfig
     * @param WebshopItemConfig                                  $itemConfig
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( NavOrderLineConfig $config, NavOrderConfig $docConfig, WebshopItemConfig $itemConfig, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements = new \SplObjectStorage();
        $this->config                    = $config;
        $this->docConfig                 = $docConfig;
        $this->itemConfig                = $itemConfig;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return NavOrderLineCollection
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
        return $this->_addNavOrderLine($instance, $idCheck);
    }

    /**
     * @param NavOrderLine $navOrderLine
     * @param bool         $idCheck
     *
     * @return bool
     */
    protected function _addNavOrderLine( NavOrderLine $navOrderLine, $idCheck = FALSE ) {
        return $this->_add($navOrderLine, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeNavOrderLine($instance,$idCheck);
    }

    /**
     * @param NavOrderLine $navOrderLine
     * @param bool         $idCheck
     *
     * @return bool
     */
    protected function _removeNavOrderLine( NavOrderLine $navOrderLine, $idCheck = FALSE ) {
        return $this->_remove($navOrderLine, $idCheck);
    }

    /**
     * @return NavOrderLine
     */
    public function getNullObject() {
        return new NavOrderLine($this->config,$this->docConfig,$this->itemConfig,$this->criteriaValidationService);
    }
}
