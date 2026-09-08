<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 14.01.2015
 * Time: 19:14
 */
class CustomerCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param CustomerConfig                                     $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( CustomerConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return CustomerCollection
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
        return $this->_addCustomer($instance, $idCheck);
    }

    /**
     * @param Customer $customer
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _addCustomer( Customer $customer, $idCheck = FALSE ) {
        return $this->_add($customer, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeCustomer($instance,$idCheck);
    }

    /**
     * @param Customer $customer
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _removeCustomer( Customer $customer, $idCheck = FALSE ) {
        return $this->_remove($customer,$idCheck);
    }

    /**
     * @return Customer
     */
    public function getNullObject() {
        return new Customer($this->config);
    }

}