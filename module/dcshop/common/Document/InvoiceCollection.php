<?php
namespace DynCom\dc\dcShop\Document;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:20
 */
class InvoiceCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    protected $linesConfig;

    /**
     * @param InvoiceConfig                                      $config
     * @param InvoiceLineConfig                                  $linesConfig
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( InvoiceConfig $config, InvoiceLineConfig $linesConfig, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->linesConfig               = $linesConfig;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return InvoiceCollection
     */
    public function getEmptyCollection() {
        return new self($this->config,$this->linesConfig,$this->criteriaValidationService);
    }


    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function add( $instance, $idCheck = FALSE ) {
        return $this->_addInvoice($instance, $idCheck);
    }

    /**
     * @param InvoiceDocument $invoice
     * @param bool             $idCheck
     *
     * @return bool
     */
    protected function _addInvoice( InvoiceDocument $invoice, $idCheck = FALSE ) {
        return $this->_add($invoice, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeInvoice($instance,$idCheck);
    }

    /**
     * @param InvoiceDocument $invoice
     * @param bool             $idCheck
     *
     * @return bool
     */
    protected function _removeInvoice( InvoiceDocument $invoice, $idCheck = FALSE ) {
        return $this->_remove($invoice,$idCheck);
    }

    /**
     * @return InvoiceDocument
     */
    public function getNullObject() {
        return new InvoiceDocument($this->config,$this->linesConfig,$this->criteriaValidationService);
    }

}