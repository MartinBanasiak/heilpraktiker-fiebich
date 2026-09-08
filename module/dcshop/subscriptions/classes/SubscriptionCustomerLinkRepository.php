<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\common\classes\EMail;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;
use DynCom\dc\dcShop\classes\Customer;

/**
 * Class SubscriptionCustomerLinkRepository
 */
class SubscriptionCustomerLinkRepository implements Repository
{

    use genericRepositoryTrait;

    /**
     * @param PDOQueryWrapper $db
     * @param SubscriptionCustomerLinkConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param SubscriptionCustomerLinkCollection $collection
     * @param $cacheAll
     */
    public function __construct( PDOQueryWrapper $db, SubscriptionCustomerLinkConfig $config, CriteriaHelperInterface $criteriaValidationService, SubscriptionCustomerLinkCollection $collection, $cacheAll=false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @return SubscriptionCustomerLink
     */
    public function getNullObject() {
        return new SubscriptionCustomerLink($this->config);
    }

    /**
     * @param SubscriptionHeader $header
     * @param Customer $customer
     * @param Email $email
     * @return int
     */
    public function getNextLineNoForSubscriptionAndCustomer(SubscriptionHeader $header, Customer $customer, EMail $email) {
        $company = $header->company;
        $subscriptionCode = $header->code;
        $customerNo = $customer->customer_no;

        $newLineNo = 10000;

        $querySuccess =
            $this->db->select('MAX(line_no)')
                        ->from($this->config->getTableName())
                        ->where('company','=',$company)
                        ->andWhere('subscription_code','=',$subscriptionCode)
                        ->andWhere('customer_no','=',$customerNo)
                        ->andWhere('webshop_order_email','=',$email->getAddress())
            ->setConstructedQuery()->doQuery();
        if($querySuccess && ($this->db->getNoOfReturnedRows() === 1)) {
            $newLineNo = (int)$this->db->getResultArray()[0][0];
            $newLineNo += 10000;
        }
        return $newLineNo;
    }

    /**
     * @param Customer $customer
     * @return SubscriptionCustomerLinkCollection
     */
    public function getAllActiveForCustomer(Customer $customer) {
        $company = $customer->company;
        $customerNo = $customer->customer_no;

        $collection = new SubscriptionCustomerLinkCollection($this->config,$this->criteriaValidationService);

        $querySuccess =
            $this->db->select('*')
                    ->from($this->config->getTableName())
                    ->where('company','=',$company)
                    ->andWhere('customer_no','=',$customerNo)
                    ->enterParentheses('and')
                        ->where('cancellation_date','!=',0)
                        ->andWhere('cancellation_date','IS NOT NULL')
                        ->andWhere('cancellation_date','!=','0000-00-00')
                        ->andWhere('cancellation_date','!=','0000-00-00 00:00:00')
                    ->leaveParentheses()
            ->setConstructedQuery()->doQuery();

        if($querySuccess && $this->db->getNoOfReturnedRows() > 0) {
            $arr = $this->db->getResultArray();
            foreach($arr as $link) {
                $newLink = $this->getNullObject();
                $newLink->mapFromArray($link);
                $collection->add($newLink);
            }
        }
        return $collection;
    }

}