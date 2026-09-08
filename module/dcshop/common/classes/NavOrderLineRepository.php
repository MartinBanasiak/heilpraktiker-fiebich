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
 * Date: 15.01.2015
 * Time: 11:22
 */
class NavOrderLineRepository implements DocumentLineRepository {

    use docLineRepositoryTrait;

    /**
     * NavOrderLineRepository constructor.
     * @param GenericDBQueryWrapperInterface $db
     * @param NavOrderLineConfig $config
     * @param NavOrderConfig $docConfig
     * @param WebshopItemRepository $itemRepository
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param GenericCollectionInterface $collection
     * @param $cacheAll
     */
    public function __construct( GenericDBQueryWrapperInterface $db, NavOrderLineConfig $config, NavOrderConfig $docConfig, WebshopItemRepository $itemRepository, CriteriaHelperInterface $criteriaValidationService, GenericCollectionInterface $collection, $cacheAll=false) {
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
     * @return NavOrderLine
     */
    public function getNullObject() {
        return new NavOrderLine($this->config,$this->docConfig,$this->itemConfig,$this->criteriaValidationService);
    }

    /**
     * @param GenericDocLineInterface $navOrderLine
     * @param DocumentRepositoryInterface $navOrderRepository
     * @return GenericDocumentInterface
     */
    public function getDocForDocLine( GenericDocLineInterface $navOrderLine, DocumentRepositoryInterface $navOrderRepository ) {
        return $this->_getNavOrderForNavOrderLine($navOrderLine,$navOrderRepository);
    }

    /**
     * @param NavOrderLine $navOrderLine
     * @param NavOrderRepository $navOrderRepository
     * @return mixed
     */
    protected function _getNavOrderForNavOrderLine( NavOrderLine $navOrderLine, NavOrderRepository $navOrderRepository ) {
        return $this->_getDocForDocLine($navOrderLine,$navOrderRepository);
    }

}