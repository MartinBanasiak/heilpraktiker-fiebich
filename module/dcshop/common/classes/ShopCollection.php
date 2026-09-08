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
class ShopCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param ShopConfig                                         $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( ShopConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return ShopCollection
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
        return $this->_addShop($instance, $idCheck);
    }

    /**
     * @param Shop $shop
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _addShop( Shop $shop, $idCheck = FALSE ) {
        return $this->_add($shop, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeShop($instance,$idCheck);
    }

    /**
     * @param Shop $shop
     * @param bool $idCheck
     * @return bool
     */
    protected function _removeShop( Shop $shop, $idCheck = FALSE ) {
        return $this->_remove($shop,$idCheck);
    }

    /**
     * @return Shop
     */
    public function getNullObject() {
        return new Shop($this->config);
    }

}