<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:24
 */
class ShopRepository implements Repository {

    use genericRepositoryTrait;

    /**
     * @param GenericDBQueryWrapperInterface                     $db
     * @param ModelDBConfigInterface|ShopConfig                $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param \DynCom\dc\common\interfaces\GenericCollectionInterface|ShopCollection        $collection
     * @param                                                    $cacheAll
     */
    public function __construct( GenericDBQueryWrapperInterface $db, ModelDBConfigInterface $config, CriteriaHelperInterface $criteriaValidationService, GenericCollectionInterface $collection, $cacheAll=false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @return Shop
     */
    public function getNullObject() {
        return new Shop($this->config);
    }

}