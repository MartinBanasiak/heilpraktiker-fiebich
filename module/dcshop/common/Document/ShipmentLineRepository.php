<?php
namespace DynCom\dc\dcShop\Document;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
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
 * Date: 19.01.2015
 * Time: 11:37
 */
class ShipmentLineRepository implements DocumentLineRepository
{

    use docLineRepositoryTrait;

    /**
     * ShipmentLineRepository constructor.
     * @param GenericDBQueryWrapperInterface $db
     * @param ShipmentLineConfig $config
     * @param ShipmentConfig $docConfig
     * @param WebshopItemRepository $itemRepository
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param GenericCollectionInterface $collection
     * @param $cacheAll
     */
    public function __construct( GenericDBQueryWrapperInterface $db, ShipmentLineConfig $config, ShipmentConfig $docConfig, WebshopItemRepository $itemRepository, CriteriaHelperInterface $criteriaValidationService, GenericCollectionInterface $collection, $cacheAll=false) {
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
     * @return ShipmentLine
     */
    public function getNullObject() {
        return new ShipmentLine($this->config,$this->docConfig,$this->itemConfig,$this->criteriaValidationService);
    }

    /**
     * @param GenericDocLineInterface $shipmentLine
     * @param DocumentRepositoryInterface $shipmentRepository
     * @return GenericDocumentInterface
     */
    public function getDocForDocLine( GenericDocLineInterface $shipmentLine, DocumentRepositoryInterface $shipmentRepository ) {
        return $this->_getShipmentForShipmentLine($shipmentLine,$shipmentRepository);
    }

    /**
     * @param ShipmentLine $shipmentLine
     * @param ShipmentRepository $shipmentRepository
     * @return GenericDocumentInterface
     */
    protected function _getShipmentForShipmentLine( ShipmentLine $shipmentLine, ShipmentRepository $shipmentRepository ) {
        return $this->_getDocForDocLine($shipmentLine,$shipmentRepository);
    }

}