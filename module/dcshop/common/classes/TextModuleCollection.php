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
class TextModuleCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param TextModuleConfig                                   $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( TextModuleConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return TextModuleCollection
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
        return $this->_addTextModule($instance, $idCheck);
    }

    /**
     * @param TextModule $textModule
     * @param bool $idCheck
     * @return bool
     */
    protected function _addTextModule( TextModule $textModule, $idCheck = FALSE ) {
        return $this->_add($textModule,$idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeTextModule($instance,$idCheck);
    }

    /**
     * @param TextModule $textModule
     * @param bool $idCheck
     * @return bool
     */
    protected function _removeTextModule( TextModule $textModule, $idCheck = FALSE ) {
        return $this->_remove($textModule,$idCheck);
    }

}