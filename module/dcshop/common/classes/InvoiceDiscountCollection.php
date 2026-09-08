<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Class InvoiceDiscountCollection
 */
class InvoiceDiscountCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param InvoiceDiscountConfig                                     $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( InvoiceDiscountConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return InvoiceDiscountCollection
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
        return $this->_addInvoiceDiscount($instance, $idCheck);
    }

    /**
     * @param InvoiceDiscount $invoiceDiscount
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _addInvoiceDiscount( InvoiceDiscount $invoiceDiscount, $idCheck = FALSE ) {
        return $this->_add($invoiceDiscount, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeInvoiceDiscount($instance,$idCheck);
    }

    /**
     * @param InvoiceDiscount $invoiceDiscount
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _removeInvoiceDiscount( InvoiceDiscount $invoiceDiscount, $idCheck = FALSE ) {
        return $this->_remove($invoiceDiscount,$idCheck);
    }

}