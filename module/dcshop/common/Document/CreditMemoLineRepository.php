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
class CreditMemoLineRepository implements DocumentLineRepository {

    use docLineRepositoryTrait;

    /**
     * InvoiceLineRepository constructor.
     * @param GenericDBQueryWrapperInterface $db
     * @param CreditMemoLineConfig $config
     * @param CreditMemoConfig $docConfig
     * @param WebshopItemRepository $itemRepository
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param GenericCollectionInterface $collection
     * @param $cacheAll
     */
    public function __construct( GenericDBQueryWrapperInterface $db, CreditMemoLineConfig $config, CreditMemoConfig $docConfig, WebshopItemRepository $itemRepository, CriteriaHelperInterface $criteriaValidationService, GenericCollectionInterface $collection, $cacheAll=false) {
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
     * @return CreditMemoLine
     */
    public function getNullObject() {
        return new CreditMemoLine($this->config,$this->docConfig,$this->itemConfig,$this->criteriaValidationService);
    }

    /**
     * @param GenericDocLineInterface $creditMemoLine
     * @param DocumentRepositoryInterface $creditMemoRepository
     * @return GenericDocumentInterface
     */
    public function getDocForDocLine( GenericDocLineInterface $creditMemoLine, DocumentRepositoryInterface $creditMemoRepository ) {
        return $this->_getcreditMemoForcreditMemoLine($creditMemoLine,$creditMemoRepository);
    }

    /**
     * @param CreditMemoLine $creditMemoLine
     * @param CreditMemoRepository $creditMemoRepository
     * @return GenericDocumentInterface
     */
    protected function _getcreditMemoForcreditMemoLine( CreditMemoLine $creditMemoLine, CreditMemoRepository $creditMemoRepository ) {
        return $this->_getDocForDocLine($creditMemoLine,$creditMemoRepository);
    }





}