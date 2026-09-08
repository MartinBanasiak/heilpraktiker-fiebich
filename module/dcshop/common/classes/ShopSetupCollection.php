<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:24
 */
class ShopSetupCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param ShopSetupConfig                                         $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( ShopSetupConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return ShopSetupCollection
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
        return $this->_addShopSetup($instance, $idCheck);
    }

    /**
     * @param ShopSetup $ShopSetup
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _addShopSetup( ShopSetup $ShopSetup, $idCheck = FALSE ) {
        return $this->_add($ShopSetup, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeShopSetup($instance,$idCheck);
    }

    /**
     * @param ShopSetup $ShopSetup
     * @param bool $idCheck
     * @return bool
     */
    protected function _removeShopSetup( ShopSetup $ShopSetup, $idCheck = FALSE ) {
        return $this->_remove($ShopSetup,$idCheck);
    }

    /**
     * @return ShopSetup
     */
    public function getNullObject() {
        return new ShopSetup($this->config);
    }

}