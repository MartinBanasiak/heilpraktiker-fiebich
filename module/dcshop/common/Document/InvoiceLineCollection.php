<?php
namespace DynCom\dc\dcShop\Document;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;
use DynCom\dc\dcShop\classes\WebshopItemConfig;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:20
 */
class InvoiceLineCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    protected $docConfig;
    protected $itemConfig;

    /**
     * @param InvoiceLineConfig                                  $config
     * @param InvoiceConfig                                      $docConfig
     * @param WebshopItemConfig                                  $itemConfig
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( InvoiceLineConfig $config, InvoiceConfig $docConfig, WebshopItemConfig $itemConfig, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  = new \SplObjectStorage();
        $this->config                    = $config;
        $this->docConfig                 = $docConfig;
        $this->itemConfig                = $itemConfig;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return InvoiceLineCollection
     */
    public function getEmptyCollection() {
        return new self($this->config,$this->docConfig,$this->itemConfig,$this->criteriaValidationService);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function add( $instance, $idCheck = FALSE ) {
        return $this->_addInvoiceLine($instance, $idCheck);
    }

    /**
     * @param InvoiceLine $invoiceLine
     * @param bool         $idCheck
     *
     * @return bool
     */
    protected function _addInvoiceLine( InvoiceLine $invoiceLine, $idCheck = FALSE ) {
        return $this->_add($invoiceLine, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeInvoiceLine($instance,$idCheck);
    }

    /**
     * @param InvoiceLine $invoiceLine
     * @param bool         $idCheck
     *
     * @return bool
     */
    protected function _removeInvoiceLine( InvoiceLine $invoiceLine, $idCheck = FALSE ) {
        return $this->_remove($invoiceLine, $idCheck);
    }

    /**
     * @return InvoiceLine
     */
    public function getNullObject() {
        return new InvoiceLine($this->config,$this->docConfig,$this->itemConfig,$this->criteriaValidationService);
    }
}