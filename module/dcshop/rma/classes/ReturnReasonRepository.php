<?php
namespace DynCom\dc\dcShop\rma\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 14.01.2015
 * Time: 19:12
 */
class ReturnReasonRepository implements Repository {

    use genericRepositoryTrait;

    /**
     * ReturnReasonRepository constructor.
     * @param GenericDBQueryWrapperInterface $db
     * @param ReturnReasonConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param ReturnReasonCollection $collection
     * @param $cacheAll
     */
    public function __construct( GenericDBQueryWrapperInterface $db, ReturnReasonConfig $config, CriteriaHelperInterface $criteriaValidationService, ReturnReasonCollection $collection, $cacheAll=false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @return ReturnReason
     */
    public function getNullObject() {
        return new ReturnReason($this->config);
    }

}