<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;
/**
 * Class CouponLineRepository
 */
class CouponLineRepository implements Repository {

    use genericRepositoryTrait;

    /**
     * CouponLineRepository constructor.
     * @param PDOQueryWrapper $db
     * @param CouponLineConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param CouponLineCollection $collection
     * @param $cacheAll
     */
    public function __construct( PDOQueryWrapper $db, CouponLineConfig $config, CriteriaHelperInterface $criteriaValidationService, CouponLineCollection $collection, $cacheAll=false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @return CouponLine
     */
    public function getNullObject() {
        return new CouponLine($this->config);
    }

}