<?php
namespace DynCom\dc\dcShop\Document;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\dcShop\interfaces\DocumentLineRepository;
use DynCom\dc\dcShop\interfaces\DocumentRepositoryInterface;
use DynCom\dc\dcShop\interfaces\GenericDocLineInterface;
use DynCom\dc\dcShop\interfaces\GenericDocumentInterface;
use DynCom\dc\dcShop\traits\docLineRepositoryTrait;
use DynCom\dc\dcShop\classes\WebshopItemRepository;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:20
 */
class InvoiceLineRepository implements DocumentLineRepository {

    use docLineRepositoryTrait;

    /**
     * InvoiceLineRepository constructor.
     * @param GenericDBQueryWrapperInterface $db
     * @param InvoiceLineConfig $config
     * @param InvoiceConfig $docConfig
     * @param WebshopItemRepository $itemRepository
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param GenericCollectionInterface $collection
     * @param $cacheAll
     */
    public function __construct( GenericDBQueryWrapperInterface $db, InvoiceLineConfig $config, InvoiceConfig $docConfig, WebshopItemRepository $itemRepository, CriteriaHelperInterface $criteriaValidationService, GenericCollectionInterface $collection, $cacheAll=false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->docConfig                 = $docConfig;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->itemRepository            = $itemRepository;
        $this->itemConfig                = $this->itemRepository->getConfig();
        $this->criteriaValidationService = $criteriaValidationService;
        $this->cacheAll                  = $cacheAll;
    }


    /**
     * @return InvoiceLine
     */
    public function getNullObject() {
        return new InvoiceLine($this->config,$this->docConfig,$this->itemConfig,$this->criteriaValidationService);
    }

    /**
     * @param GenericDocLineInterface $invoiceLine
     * @param DocumentRepositoryInterface $invoiceRepository
     * @return GenericDocumentInterface
     */
    public function getDocForDocLine( GenericDocLineInterface $invoiceLine, DocumentRepositoryInterface $invoiceRepository ) {
        return $this->_getInvoiceForInvoiceLine($invoiceLine,$invoiceRepository);
    }

    /**
     * @param InvoiceLine $invoiceLine
     * @param InvoiceRepository $invoiceRepository
     * @return GenericDocumentInterface
     */
    protected function _getInvoiceForInvoiceLine( InvoiceLine $invoiceLine, InvoiceRepository $invoiceRepository ) {
        return $this->_getDocForDocLine($invoiceLine,$invoiceRepository);
    }





}