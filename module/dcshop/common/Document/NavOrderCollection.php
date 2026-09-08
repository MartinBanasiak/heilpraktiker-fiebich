<?php
namespace DynCom\dc\dcShop\Document;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:21
 */
class NavOrderCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    protected $linesConfig;

    /**
     * @param NavOrderConfig                                     $config
     * @param NavOrderLineConfig                                 $linesConfig
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( NavOrderConfig $config, NavOrderLineConfig $linesConfig, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  = new \SplObjectStorage();
        $this->config                    = $config;
        $this->linesConfig               = $linesConfig;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return NavOrderCollection
     */
    public function getEmptyCollection() {
        return new self($this->config, $this->linesConfig, $this->criteriaValidationService);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function add( $instance, $idCheck = FALSE ) {
        return $this->_addNavOrder($instance, $idCheck);
    }

    /**
     * @param NavOrderDocument $navOrder
     * @param bool              $idCheck
     *
     * @return bool
     */
    protected function _addNavOrder( NavOrderDocument $navOrder, $idCheck = FALSE ) {
        return $this->_add($navOrder, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeNavOrder($instance, $idCheck);
    }

    /**
     * @param NavOrderDocument $navOrder
     * @param bool              $idCheck
     *
     * @return bool
     */
    protected function _removeNavOrder( NavOrderDocument $navOrder, $idCheck = FALSE ) {
        return $this->_remove($navOrder, $idCheck);
    }

    /**
     * @return NavOrderDocument
     */
    public function getNullObject() {
        return new NavOrderDocument($this->config, $this->linesConfig, $this->criteriaValidationService);
    }

}