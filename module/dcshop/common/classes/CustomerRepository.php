<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 14.01.2015
 * Time: 19:14
 */
class CustomerRepository implements Repository {

    use genericRepositoryTrait;

    /**
     * CustomerRepository constructor.
     * @param PDOQueryWrapper $db
     * @param CustomerConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param CustomerCollection $collection
     * @param $cacheAll
     */
    public function __construct( PDOQueryWrapper $db, CustomerConfig $config, CriteriaHelperInterface $criteriaValidationService, CustomerCollection $collection, $cacheAll=false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @return Customer
     */
    public function getNullObject() {
        return new Customer($this->config);
    }

    /**
     * @param User $currUser
     * @param $langCode
     * @return Customer
     */
    public function getCustomerForCurrUserAndLangCode(User $currUser, $langCode) {
		if ($currUser instanceof Salesperson) {
			$criteria = [
				[
				    ['company','=',$currUser->getCompany()],
				    ['customer_no','=',$currUser->customer_no],
				    ['salesperson_code','=',$currUser->salesperson_code],
				],
			];
			$collection = $this->findByCriteria($criteria);		
			$count = count($collection);
			if ($count === 1) {
				$customer = $collection->getFirst();
				return $customer;
			} else {
				return new Customer($this->config);
			}
		} else {
			$altPrimary = [
				'company' => $currUser->company,
				'shop_code' => $currUser->shop_code,
				'language_code' => $langCode,
				'customer_no' => $currUser->customer_no,
			];
			$el = $this->findByAltPrimary($altPrimary);
		}
        return $el;
    }

    /**
     * @param User $currUser
     * @param $langCode
     * @return CustomerPermissionGroupDecorator
     */
    public function getCustomerWithPermissionGroupsForCurrUserAndLangCode(User $currUser, $langCode) {
        $customer = $this->getCustomerForCurrUserAndLangCode($currUser,$langCode);
        $withPermissionGroups = new CustomerPermissionGroupDecorator($customer,$this->db);
        return $withPermissionGroups;
    }

}