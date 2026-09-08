<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:25
 */
class SalespersonCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param SalespersonConfig                                         $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( SalespersonConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return SalespersonCollection
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
        return $this->_addSalesperson($instance, $idCheck);
    }

    /**
     * @param Salesperson $salesperson
     * @param bool $idCheck
     * @return bool
     */
    protected function _addSalesperson( Salesperson $salesperson, $idCheck = FALSE ) {
        return $this->_add($salesperson,$idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeSalesperson($instance,$idCheck);
    }

    /**
     * @param Salesperson $salesperson
     * @param bool $idCheck
     * @return bool
     */
    protected function _removeSalesperson( Salesperson $salesperson, $idCheck = FALSE ) {
        return $this->_remove($salesperson,$idCheck);
    }

    /**
     * @return Salesperson
     */
    public function getNullObject() {
        return new Salesperson($this->config);
    }

}