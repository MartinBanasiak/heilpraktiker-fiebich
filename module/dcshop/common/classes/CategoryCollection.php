<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Class CategoryCollection
 */
class CategoryCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param CategoryConfig                                     $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( CategoryConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return CategoryCollection
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
        return $this->_addCategory($instance, $idCheck);
    }

    /**
     * @param Category $category
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _addCategory( Category $category, $idCheck = FALSE ) {
        return $this->_add($category, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeCategory($instance,$idCheck);
    }

    /**
     * @param Category $category
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _removeCategory( Category $category, $idCheck = FALSE ) {
        return $this->_remove($category,$idCheck);
    }

}