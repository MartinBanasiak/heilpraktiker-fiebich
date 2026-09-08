<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:25
 */
class TextModuleRepository implements Repository {

    use genericRepositoryTrait;

    /**
     * TextModuleRepository constructor.
     * @param GenericDBQueryWrapperInterface $db
     * @param TextModuleConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param TextModuleCollection $collection
     * @param $cacheAll
     */
    public function __construct( GenericDBQueryWrapperInterface $db, TextModuleConfig $config, CriteriaHelperInterface $criteriaValidationService, TextModuleCollection $collection, $cacheAll=false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @return TextModule
     */
    public function getNullObject() {
        return new TextModule($this->config);
    }

}