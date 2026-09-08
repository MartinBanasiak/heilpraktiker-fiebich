<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

/**
 * Class WebshopItemVariantRepository
 */
class WebshopItemVariantRepository implements Repository{

    use genericRepositoryTrait;

    /**
     * WebshopItemVariantRepository constructor.
     * @param GenericDBQueryWrapperInterface $db
     * @param WebshopItemVariantConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param WebshopItemVariantCollection $collection
     * @param $cacheAll
     */
    public function __construct( GenericDBQueryWrapperInterface $db, WebshopItemVariantConfig $config, CriteriaHelperInterface $criteriaValidationService, WebshopItemVariantCollection $collection, $cacheAll=false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @return WebshopItemVariant
     */
    public function getNullObject() {
        return new WebshopItemVariant($this->config);
    }

    /**
     * @param WebshopItemInterface $item
     * @return \DynCom\dc\common\interfaces\GenericCollectionInterface
     */
    public function getAllForItem(WebshopItemInterface $item) {
        $company = $item->getCompany();
        $itemNo = $item->getItemNo();

        $criteria = [
            [
                ['company','=',$company],
                ['item_no','=',$itemNo]
            ]
        ];
        return $this->findByCriteria($criteria);
    }

    /**
     * @param WebshopItemInterface $item
     * @return mixed
     */
    public function getFirstForItem(WebshopItemInterface $item) {
        $company = $item->getCompany();
        $itemNo = $item->getItemNo();

        $criteria = [
            [
                ['company','=',$company],
                ['item_no','=',$itemNo]
            ]
        ];
        $collection = $this->findByCriteria($criteria,0,1);
        return $collection->getFirst();
    }

}