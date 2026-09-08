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
 * Time: 11:21
 */
class NavOrderRepository  implements DocumentRepository {

    use documentRepositoryTrait;

    /**
     * NavOrderRepository constructor.
     * @param GenericDBQueryWrapperInterface $db
     * @param NavOrderConfig $config
     * @param NavOrderLineRepository $lineRepository
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param NavOrderCollection $collection
     * @param bool $cacheAll
     */
    public function __construct( GenericDBQueryWrapperInterface $db, NavOrderConfig $config, NavOrderLineRepository $lineRepository, CriteriaHelperInterface $criteriaValidationService, NavOrderCollection $collection, $cacheAll = FALSE ) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->lineRepository            = $lineRepository;
        $this->linesConfig               = $this->lineRepository->getConfig();
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
        return $this->_setLinesForNavOrderDocument($instance);
    }

    /**
     * @param InvoiceDocument $instance
     * @return bool
     */
    protected function _setLinesForNavOrderDocument( InvoiceDocument $instance ) {
        return $this->_setLinesForDocument($instance);
    }

    /**
     * @return NavOrderDocument
     */
    public function getNullObject() {
        return new NavOrderDocument($this->config,$this->linesConfig,$this->criteriaValidationService);
    }
}