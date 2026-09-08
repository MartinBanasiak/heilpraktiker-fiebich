<?php
namespace DynCom\dc\dcShop\Document;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;
use DynCom\dc\dcShop\classes\WebshopItemConfig;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 19.01.2015
 * Time: 15:17
 */
class WebshopOrderLineCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    protected $docConfig;
    protected $itemConfig;

    /**
     * @param WebshopOrderLineConfig                             $config
     * @param WebshopOrderConfig                                 $docConfig
     * @param WebshopItemConfig                                  $itemConfig
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( WebshopOrderLineConfig $config, WebshopOrderConfig $docConfig, WebshopItemConfig $itemConfig, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  = new \SplObjectStorage();
        $this->config                    = $config;
        $this->docConfig                 = $docConfig;
        $this->itemConfig                = $itemConfig;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return WebshopOrderLineCollection
     */
    public function getEmptyCollection() {
        return new self($this->config, $this->docConfig, $this->itemConfig, $this->criteriaValidationService);
    }


    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function add( $instance, $idCheck = FALSE ) {
        return $this->_addWebshopOrderLine($instance, $idCheck);
    }

    /**
     * @param WebshopOrderLine $webshopOrderLine
     * @param bool              $idCheck
     *
     * @return bool
     */
    protected function _addWebshopOrderLine( WebshopOrderLine $webshopOrderLine, $idCheck = FALSE ) {
        return $this->_add($webshopOrderLine, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeWebshopOrderLine($instance, $idCheck);
    }

    /**
     * @param WebshopOrderLine $webshopOrderLine
     * @param bool              $idCheck
     *
     * @return bool
     */
    protected function _removeWebshopOrderLine( WebshopOrderLine $webshopOrderLine, $idCheck = FALSE ) {
        return $this->_remove($webshopOrderLine, $idCheck);
    }

    /**
     * @return WebshopOrderLine
     */
    public function getNullObject() {
        return new WebshopOrderLine($this->config, $this->docConfig, $this->itemConfig, $this->criteriaValidationService);
    }

}