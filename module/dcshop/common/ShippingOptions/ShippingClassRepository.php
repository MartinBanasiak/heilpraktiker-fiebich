<?php
namespace DynCom\dc\dcShop\ShippingOptions;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;
use DynCom\dc\dcShop\interfaces\UserBasket;

/**
 * Class ShippingClassRepository
 */
class ShippingClassRepository implements Repository{

    use genericRepositoryTrait;

    /**
     * ShippingClassRepository constructor.
     * @param PDOQueryWrapper $db
     * @param ShippingClassConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param ShippingClassCollection $collection
     * @param $cacheAll
     */
    public function __construct(PDOQueryWrapper $db, ShippingClassConfig $config, CriteriaHelperInterface $criteriaValidationService, ShippingClassCollection $collection, $cacheAll=false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @return ShippingClass
     *
     */
    public function getNullObject() {
        return new ShippingClass($this->config);
    }

    /**
     * @param $primaryArr
     * @return bool
     */
    public function hasEntityByPrimary($primaryArr)
    {
        foreach ($this->collection as $instance) {
            $instanceAltPrimary = array();
            foreach ($primaryArr as $key => $value) {
                $instanceAltPrimary[$key] = $instance->$key;
            }
            if ($instanceAltPrimary === $primaryArr) {
                return true;
            }
        }
        return false;
    }

}