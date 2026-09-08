<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 24.03.2015
 * Time: 10:23
 */

class SalesPriceCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param SalesPriceConfig                               $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( SalesPriceConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return SalesPriceCollection
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
        return $this->_addSalesPrice($instance, $idCheck);
    }

    /**
     * @param SalesPrice $shop
     * @param bool $idCheck
     * @return bool
     */
    protected function _addSalesPrice( SalesPrice $shop, $idCheck = FALSE ) {
        return $this->_add($shop, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeSalesPrice($instance,$idCheck);
    }

    /**
     * @param SalesPrice $shop
     * @param bool $idCheck
     * @return bool
     */
    protected function _removeSalesPrice( SalesPrice $shop, $idCheck = FALSE ) {
        return $this->_remove($shop,$idCheck);
    }

    /**
     * @return SalesPrice
     */
    public function getNullObject() {
        return new SalesPrice($this->config);
    }

}