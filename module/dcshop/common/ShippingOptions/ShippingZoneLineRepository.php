<?php
namespace DynCom\dc\dcShop\ShippingOptions;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;
/**
 * Class ShippingZoneLineRepository
 */
class ShippingZoneLineRepository implements Repository {

    use genericRepositoryTrait;

    /**
     * ShippingZoneLineRepository constructor.
     * @param PDOQueryWrapper $db
     * @param ShippingZoneLineConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param ShippingZoneLineCollection $collection
     * @param $cacheAll
     */
    public function __construct( PDOQueryWrapper $db, ShippingZoneLineConfig $config, CriteriaHelperInterface $criteriaValidationService, ShippingZoneLineCollection $collection, $cacheAll=false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @return ShippingZoneLine
     */
    public function getNullObject() {
        return new ShippingZoneLine($this->config);
    }

}