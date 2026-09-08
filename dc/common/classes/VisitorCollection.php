<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:27
 */
class VisitorCollection implements GenericCollectionInterface
{

    use genericCollectionTrait;

    /**
     * @param VisitorConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( VisitorConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return VisitorCollection
     */
    public function getEmptyCollection() {
        return new self($this->config,$this->criteriaValidationService);
    }

    /**
     * @param mixed $instance
     * @param bool $idCheck
     *
     * @return bool
     */
    public function add($instance, $idCheck = FALSE) {
        return $this->_addVisitor($instance, $idCheck);
    }

    /**
     * @param Visitor $visitor
     * @param bool $idCheck
     * @return bool
     */
    protected function _addVisitor(Visitor $visitor, $idCheck = FALSE) {
        return $this->_add($visitor, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool $idCheck
     *
     * @return bool
     */
    public function remove($instance, $idCheck = FALSE) {
        return $this->_removeVisitor($instance, $idCheck);
    }

    /**
     * @param Visitor $visitor
     * @param bool $idCheck
     * @return bool
     */
    protected function _removeVisitor(Visitor $visitor, $idCheck = FALSE) {
        return $this->_remove($visitor, $idCheck);
    }

}