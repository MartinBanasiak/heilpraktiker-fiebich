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
class ShopLanguageRepository  implements Repository {

    use genericRepositoryTrait;

    /**
     * @param GenericDBQueryWrapperInterface                      $db
     * @param ModelDBConfigInterface|ShopLanguageConfig         $config
     * @param CriteriaHelperInterface  $criteriaValidationService
     * @param GenericCollectionInterface|ShopLanguageCollection $collection
     * @param                                                     $cacheAll
     */
    public function __construct( GenericDBQueryWrapperInterface $db, ShopLanguageConfig $config, CriteriaHelperInterface $criteriaValidationService, ShopLanguageCollection $collection, $cacheAll=false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @return ShopLanguage
     */
    public function getNullObject() {
        return new ShopLanguage($this->config);
    }

}