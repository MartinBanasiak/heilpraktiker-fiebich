<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 19.01.2015
 * Time: 14:08
 */

class WebshopOrderCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    protected $linesConfig;

    /**
     * @param WebshopOrderConfig                                     $config
     * @param WebshopOrderLineConfig                                 $linesConfig
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( WebshopOrderConfig $config, WebshopOrderLineConfig $linesConfig, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  = new \SplObjectStorage();
        $this->config                    = $config;
        $this->linesConfig               = $linesConfig;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return WebshopOrderCollection
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
        return $this->_addWebshopOrder($instance, $idCheck);
    }

    /**
     * @param WebshopOrderDocument $webshopOrder
     * @param bool              $idCheck
     *
     * @return bool
     */
    protected function _addWebshopOrder( WebshopOrderDocument $webshopOrder, $idCheck = FALSE ) {
        return $this->_add($webshopOrder, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeWebshopOrder($instance, $idCheck);
    }

    /**
     * @param WebshopOrderDocument $webshopOrder
     * @param bool              $idCheck
     *
     * @return bool
     */
    protected function _removeWebshopOrder( WebshopOrderDocument $webshopOrder, $idCheck = FALSE ) {
        return $this->_remove($webshopOrder, $idCheck);
    }

    /**
     * @return WebshopOrderDocument
     */
    public function getNullObject() {
        return new WebshopOrderDocument($this->config, $this->linesConfig, $this->criteriaValidationService);
    }

}