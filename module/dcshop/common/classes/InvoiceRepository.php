<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\dcShop\interfaces\DocumentRepository;
use DynCom\dc\dcShop\interfaces\GenericDocumentInterface;
use DynCom\dc\dcShop\traits\documentRepositoryTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:20
 */
class InvoiceRepository implements DocumentRepository {

    use documentRepositoryTrait;

    /**
     * InvoiceRepository constructor.
     * @param GenericDBQueryWrapperInterface $db
     * @param InvoiceConfig $config
     * @param InvoiceLineRepository $lineRepository
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param InvoiceCollection $collection
     * @param bool $cacheAll
     */
    public function __construct( GenericDBQueryWrapperInterface $db, InvoiceConfig $config, InvoiceLineRepository $lineRepository, CriteriaHelperInterface $criteriaValidationService, InvoiceCollection $collection, $cacheAll = FALSE ) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->lineRepository            = $lineRepository;
        $this->lineConfig                = $this->lineRepository->getConfig();
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->criteriaValidationService = $criteriaValidationService;

        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @param GenericDocumentInterface $instance
     * @return bool
     */
    public function setLinesForDocument( GenericDocumentInterface $instance ) {
        return $this->_setLinesForInvoiceDocument($instance);
    }

    /**
     * @param InvoiceDocument $instance
     * @return bool
     */
    protected function _setLinesForInvoiceDocument( InvoiceDocument $instance ) {
        return $this->_setLinesForDocument($instance);
    }

    /**
     * @return InvoiceDocument
     */
    public function getNullObject() {
        return new InvoiceDocument($this->config,$this->lineConfig,$this->criteriaValidationService);
    }
}