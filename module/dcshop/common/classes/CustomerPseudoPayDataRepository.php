<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;

/**
 * Class CustomerPseudoPayDataRepository
 */
class CustomerPseudoPayDataRepository implements Repository{

    use genericRepositoryTrait;

    /**
     * CustomerPseudoPayDataRepository constructor.
     * @param PDOQueryWrapper $db
     * @param CustomerPseudoPayDataConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param CustomerPseudoPayDataCollection $collection
     * @param $cacheAll
     */
    public function __construct( PDOQueryWrapper $db, CustomerPseudoPayDataConfig $config, CriteriaHelperInterface $criteriaValidationService, CustomerPseudoPayDataCollection $collection, $cacheAll=false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @return CustomerPseudoPayData
     */
    public function getNullObject() {
        return new CustomerPseudoPayData($this->config);
    }

}