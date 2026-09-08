<?php
namespace DynCom\dc\dcShop\rma\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\dcShop\classes\WebshopItemRepository;
use DynCom\dc\dcShop\interfaces\DocumentLineRepository;
use DynCom\dc\dcShop\interfaces\DocumentRepositoryInterface;
use DynCom\dc\dcShop\interfaces\GenericDocLineInterface;
use DynCom\dc\dcShop\interfaces\GenericDocumentInterface;
use DynCom\dc\dcShop\traits\docLineRepositoryTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 14.01.2015
 * Time: 15:15
 */
class RetShipmentLineRepository implements DocumentLineRepository {

    use docLineRepositoryTrait;

    /**
     * RetShipmentLineRepository constructor.
     * @param GenericDBQueryWrapperInterface $db
     * @param RetShipmentLineConfig $config
     * @param RetShipmentConfig $docConfig
     * @param WebshopItemRepository $itemRepository
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param RetShipmentLineCollection $collection
     * @param $cacheAll
     */
    public function __construct( GenericDBQueryWrapperInterface $db, RetShipmentLineConfig $config, RetShipmentConfig $docConfig, WebshopItemRepository $itemRepository, CriteriaHelperInterface $criteriaValidationService, RetShipmentLineCollection $collection, $cacheAll ) {
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
     * @return RetShipmentLine
     */
    public function getNullObject() {
        return new RetShipmentLine($this->config, $this->docConfig, $this->itemConfig, $this->criteriaValidationService);
    }

    /**
     * @param GenericDocLineInterface $retShipmentLine
     * @param DocumentRepositoryInterface $retShipmentRepository
     * @return GenericDocumentInterface
     */
    public function getDocForDocLine( GenericDocLineInterface $retShipmentLine, DocumentRepositoryInterface $retShipmentRepository ) {
        return $this->_getRetShipmentForRetShipmentLine($retShipmentLine, $retShipmentRepository);
    }

    /**
     * @param RetShipmentLine $retShipmentLine
     * @param RetShipmentRepository $retShipmentRepository
     * @return GenericDocumentInterface
     */
    protected function _getRetShipmentForRetShipmentLine( RetShipmentLine $retShipmentLine, RetShipmentRepository $retShipmentRepository ) {
        return $this->_getDocForDocLine($retShipmentLine, $retShipmentRepository);
    }

    /**
     * @param RetShipmentLine $line
     * @param $insert
     * @param $quantity
     * @param $reasonCode
     * @return bool
     */
    public function setReturnOrder( RetShipmentLine $line, $insert, $quantity, $reasonCode ) {
        if($line->setReturnOrder($insert,$quantity,$reasonCode)) {
            return $this->updateSingle($line);
        }
        return FALSE;
    }
	
	public function getObjectClass() {
		return RetShipmentLine::class;
	}
	
}