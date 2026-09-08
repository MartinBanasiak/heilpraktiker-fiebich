<?php

namespace DynCom\dc\dcShop\CustomerAddress;

use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Class CustomerAddressCollection
 */
class CustomerAddressCollection implements GenericCollectionInterface
{

    use genericCollectionTrait;

    /**
     * @param CustomerAddressConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct(CustomerAddressConfig $config, CriteriaHelperInterface $criteriaValidationService)
    {
        $this->elements = new \SplObjectStorage();
        $this->config = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName = $config->getModelClassName();
    }

    /**
     * @return CustomerAddressCollection
     */
    public function getEmptyCollection()
    {
        return new self($this->config, $this->criteriaValidationService);
    }

    /**
     * @param mixed $instance
     * @param bool $idCheck
     *
     * @return bool
     */
    public function add($instance, $idCheck = FALSE)
    {
        return $this->_addShipmentAddress($instance, $idCheck);
    }

    /**
     * @param CustomerAddress $customerAddress
     * @param bool $idCheck
     *
     * @return bool
     */
    protected function _addShipmentAddress(CustomerAddress $customerAddress, $idCheck = FALSE)
    {
        return $this->_add($customerAddress, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool $idCheck
     *
     * @return bool
     */
    public function remove($instance, $idCheck = FALSE)
    {
        return $this->_removeShipmentAddress($instance, $idCheck);
    }

    /**
     * @param CustomerAddress $customerAddress
     * @param bool $idCheck
     *
     * @return bool
     */
    protected function _removeShipmentAddress(CustomerAddress $customerAddress, $idCheck = FALSE)
    {
        return $this->_remove($customerAddress, $idCheck);
    }

}