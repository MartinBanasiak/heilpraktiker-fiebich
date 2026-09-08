<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\dcShop\interfaces\DocumentLineRepository;
use DynCom\dc\dcShop\interfaces\DocumentRepositoryInterface;
use DynCom\dc\dcShop\interfaces\GenericDocLineInterface;
use DynCom\dc\dcShop\interfaces\GenericDocumentInterface;
use DynCom\dc\dcShop\traits\docLineRepositoryTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 19.01.2015
 * Time: 15:17
 */
class WebshopOrderLineRepository implements DocumentLineRepository {

    use docLineRepositoryTrait;

    /**
     * WebshopOrderLineRepository constructor.
     * @param GenericDBQueryWrapperInterface $db
     * @param WebshopOrderLineConfig $config
     * @param WebshopOrderConfig $docConfig
     * @param WebshopItemRepository $itemRepository
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param GenericCollectionInterface $collection
     * @param $cacheAll
     */
    public function __construct( GenericDBQueryWrapperInterface $db, WebshopOrderLineConfig $config, WebshopOrderConfig $docConfig, WebshopItemRepository $itemRepository, CriteriaHelperInterface $criteriaValidationService, GenericCollectionInterface $collection, $cacheAll=false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->docConfig                 = $docConfig;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->itemRepository            = $itemRepository;
        $this->itemConfig                = $this->itemRepository->getConfig();
        $this->criteriaValidationService = $criteriaValidationService;
        $this->cacheAll                  = (bool)$cacheAll;
    }

    /**
     * @return WebshopOrderLine
     */
    public function getNullObject() {
        return new WebshopOrderLine($this->config, $this->docConfig, $this->itemConfig, $this->criteriaValidationService);
    }

    /**
     * @param GenericDocLineInterface $webshopOrderLine
     * @param DocumentRepositoryInterface $webshopOrderRepository
     * @return GenericDocumentInterface
     */
    public function getDocForDocLine( GenericDocLineInterface $webshopOrderLine, DocumentRepositoryInterface $webshopOrderRepository ) {
        return $this->_getWebshopOrderForWebshopOrderLine($webshopOrderLine, $webshopOrderRepository);
    }

    /**
     * @param WebshopOrderLine $webshopOrderLine
     * @param WebshopOrderRepository $webshopOrderRepository
     * @return GenericDocumentInterface
     */
    protected function _getWebshopOrderForWebshopOrderLine( WebshopOrderLine $webshopOrderLine, WebshopOrderRepository $webshopOrderRepository ) {
        return $this->_getDocForDocLine($webshopOrderLine, $webshopOrderRepository);
    }

}