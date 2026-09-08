<?php

namespace DynCom\dc\dcShop\CustomerAddress;

use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;

/**
 * Class ShipmentAddressRepository
 */
class CustomerAddressRepository implements Repository
{

    use genericRepositoryTrait;

    /**
     * ShipmentAddressRepository constructor.
     * @param PDOQueryWrapper $db
     * @param CustomerAddressConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param CustomerAddressCollection $collection
     * @param $cacheAll
     */
    public function __construct(PDOQueryWrapper $db, CustomerAddressConfig $config, CriteriaHelperInterface $criteriaValidationService, CustomerAddressCollection $collection, $cacheAll = false)
    {
        $this->db = $db;
        $this->config = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection = $collection;
        $this->collectionEntryClassName = $this->collection->getEntryClassName();
        $this->cacheAll = $cacheAll;
    }

    /**
     * @return CustomerAddress
     */
    public function getNullObject() :CustomerAddress
    {
        return new CustomerAddress($this->config);
    }

    /**
     * @param string $company
     * @param string $customerNo
     * @return CustomerAddressCollection
     * @throws \Exception
     */
    public function getAllCustomerAddresses(string $company, string $customerNo): CustomerAddressCollection
    {
        $emptyCollection = new CustomerAddressCollection($this->config, $this->criteriaValidationService);
        $criteria = [
            [
                ['company', '=', $company],
                ['customer_no', '=', $customerNo]
            ]
        ];
        $collection = $this->findByCriteria($criteria);
        if ($collection instanceof CustomerAddressCollection) {
            return $collection;
        }
        return $emptyCollection;
    }

}