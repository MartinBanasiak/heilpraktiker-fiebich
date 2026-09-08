<?php
namespace DynCom\dc\dcShop\rma\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Class ReturnReasonCollection
 */
class ReturnReasonCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param ReturnReasonConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( ReturnReasonConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return ReturnReasonCollection
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
        return $this->_addReturnReason($instance, $idCheck);
    }

    /**
     * @param ReturnReason $returnReason
     * @param bool $idCheck
     * @return bool
     */
    protected function _addReturnReason( ReturnReason $returnReason, $idCheck = FALSE ) {
        return $this->_add($returnReason, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeReturnReason($instance,$idCheck);
    }

    /**
     * @param ReturnReason $returnReason
     * @param bool $idCheck
     * @return bool
     */
    protected function _removeReturnReason( ReturnReason $returnReason, $idCheck = FALSE ) {
        return $this->_remove($returnReason,$idCheck);
    }

    /**
     * @return ReturnReason
     */
    public function getNullObject() {
        return new ReturnReason($this->config);
    }

}