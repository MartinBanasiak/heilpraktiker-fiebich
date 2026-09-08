<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;

/**
 * Class ShipmentAddressRepository
 */
class ShipmentAddressRepository implements Repository{

    use genericRepositoryTrait;

    /**
     * ShipmentAddressRepository constructor.
     * @param PDOQueryWrapper $db
     * @param CustomerAddressConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param ShipmentAddressCollection $collection
     * @param $cacheAll
     */
    public function __construct(PDOQueryWrapper $db, CustomerAddressConfig $config, CriteriaHelperInterface $criteriaValidationService, ShipmentAddressCollection $collection, $cacheAll=false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @return ShipmentAddress
     */
    public function getNullObject() {
        return new ShipmentAddress($this->config);
    }

}