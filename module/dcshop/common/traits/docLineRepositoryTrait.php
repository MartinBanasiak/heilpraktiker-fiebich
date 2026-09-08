<?php
namespace DynCom\dc\dcShop\traits;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\common\traits\genericRepositoryTrait;
use DynCom\dc\dcShop\classes\ShopLanguage;
use DynCom\dc\dcShop\classes\WebshopItem;
use DynCom\dc\dcShop\classes\WebshopItemRepository;
use DynCom\dc\dcShop\interfaces\DocLineModelDBConfigInterface;
use DynCom\dc\dcShop\interfaces\DocumentModelDBConfigInterface;
use DynCom\dc\dcShop\interfaces\DocumentRepository;
use DynCom\dc\dcShop\interfaces\GenericDocLineInterface;
use DynCom\dc\dcShop\interfaces\GenericDocumentInterface;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 14.01.2015
 * Time: 19:42
 */
trait docLineRepositoryTrait {

    use genericRepositoryTrait;

    protected $docConfig;
    protected $itemConfig;

    protected $itemRepository;


    /**
     * docLineRepositoryTrait constructor.
     * @param GenericDBQueryWrapperInterface $db
     * @param DocLineModelDBConfigInterface $config
     * @param DocumentModelDBConfigInterface $docConfig
     * @param WebshopItemRepository $itemRepository
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param GenericCollectionInterface $collection
     * @param $cacheAll
     */
    public function __construct( GenericDBQueryWrapperInterface $db, DocLineModelDBConfigInterface $config, DocumentModelDBConfigInterface $docConfig, WebshopItemRepository $itemRepository, CriteriaHelperInterface $criteriaValidationService, GenericCollectionInterface $collection, $cacheAll=false) {
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
     * @param GenericDocLineInterface $instance
     * @param DocumentRepository $docRepository
     * @return GenericDocumentInterface
     * @throws \Exception
     */
    protected function _getDocForDocLine( GenericDocLineInterface $instance, DocumentRepository $docRepository ) {

        $criteriaArray = $instance->getDocCriteriaArray();

        if(!$this->criteriaValidationService->validateCriteria($docRepository->getConfig(),$criteriaArray)) {
            return $docRepository->getNullObject();
        }

        $documentCollection = $docRepository->findByCriteria($criteriaArray);

        if(count($documentCollection) < 1) {
            throw new \Exception('No document could be gotten from the repository.');
            return $docRepository->getNullObject();
        } elseif(count($documentCollection) > 1) {
            throw new \Exception('More than one document was found. Check criteria.');
            return $docRepository->getNullObject();
        }

        return $documentCollection[0];

    }

    /**
     * @param GenericDocLineInterface $instance
     * @param ShopLanguage $shopLanguage
     * @return WebshopItem
     */
    public function getItemForDocLine( GenericDocLineInterface $instance, ShopLanguage $shopLanguage ) {
        return $this->_getItemForDocLine($instance,$shopLanguage);
    }

    /**
     * @param GenericDocLineInterface $instance
     * @param ShopLanguage $shopLanguage
     * @return WebshopItem
     * @throws \Exception
     */
    protected function _getItemForDocLine( GenericDocLineInterface $instance, ShopLanguage $shopLanguage ) {

        $criteriaArray = $instance->getItemCriteriaArray();
        if(!(count($criteriaArray) > 0)) {
            return $this->itemRepository->getNullObject();
        }

        $criteriaArray[] = array('shop_code','=',$shopLanguage->shop_code);
        $criteriaArray[] = array('language_code','=',$shopLanguage->code);


        if(!$this->criteriaValidationService->validateCriteria($this->itemConfig,$criteriaArray)) {
            return $this->itemRepository->getNullObject();
        }

        $itemCollection = $this->itemRepository->findByCriteria($criteriaArray);

        if(count($itemCollection) < 1) {
            throw new \Exception('No item could be gotten from the repository.');
            return $this->itemRepository->getNullObject();
        } elseif(count($itemCollection) > 1) {
            throw new \Exception('More than one item was found. Check criteria.');
            return $this->itemRepository->getNullObject();
        }

        return $itemCollection[0];

    }

}