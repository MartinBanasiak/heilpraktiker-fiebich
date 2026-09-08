<?php
namespace DynCom\dc\dcShop\ShippingOptions;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;
use DynCom\dc\dcShop\interfaces\UserBasket;

/**
 * Class ShippingOptionTranslationRepository
 */
class ShippingOptionTranslationRepository implements Repository{

    use genericRepositoryTrait;

    /**
     * ShippingOptionTranslationRepository constructor.
     * @param PDOQueryWrapper $db
     * @param ShippingOptionTranslationConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param ShippingOptionTranslationCollection $collection
     * @param $cacheAll
     */
    public function __construct(PDOQueryWrapper $db, ShippingOptionTranslationConfig $config, CriteriaHelperInterface $criteriaValidationService, ShippingOptionTranslationCollection $collection, $cacheAll=false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @return ShippingOptionTranslation
     *
     */
    public function getNullObject() {
        return new ShippingOptionTranslation($this->config);
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