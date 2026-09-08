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
 * Time: 11:23
 */
class ShipmentRepository implements DocumentRepository {

    use documentRepositoryTrait;

    /**
     * ShipmentRepository constructor.
     * @param GenericDBQueryWrapperInterface $db
     * @param ShipmentConfig $config
     * @param ShipmentLineRepository $lineRepository
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param ShipmentCollection $collection
     * @param bool $cacheAll
     */
    public function __construct( GenericDBQueryWrapperInterface $db, ShipmentConfig $config, ShipmentLineRepository $lineRepository, CriteriaHelperInterface $criteriaValidationService, ShipmentCollection $collection, $cacheAll = FALSE ) {
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
        return $this->_setLinesForShipmentDocument($instance);
    }

    /**
     * @param InvoiceDocument $instance
     * @return bool
     */
    protected function _setLinesForShipmentDocument( InvoiceDocument $instance ) {
        return $this->_setLinesForDocument($instance);
    }

    /**
     * @return ShipmentDocument
     */
    public function getNullObject() {
        return new ShipmentDocument($this->config,$this->linesConfig,$this->criteriaValidationService);
    }
}