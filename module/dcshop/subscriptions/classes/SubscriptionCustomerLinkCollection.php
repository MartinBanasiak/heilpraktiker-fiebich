<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Class SubscriptionCustomerLinkCollection
 */
class SubscriptionCustomerLinkCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param SubscriptionCustomerLinkConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( SubscriptionCustomerLinkConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return SubscriptionCustomerLinkCollection
     */
    public function getEmptyCollection() {
        return new self($this->config,$this->criteriaValidationService);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function add( $instance, $idCheck = FALSE ) {
        return $this->_addSubscriptionCustomerLink($instance, $idCheck);
    }

    /**
     * @param SubscriptionCustomerLink $subscriptionCustomerLink
     * @param bool $idCheck
     * @return bool
     */
    protected function _addSubscriptionCustomerLink( SubscriptionCustomerLink $subscriptionCustomerLink, $idCheck = FALSE ) {
        return $this->_add($subscriptionCustomerLink, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeSubscriptionCustomerLink($instance,$idCheck);
    }

    /**
     * @param SubscriptionCustomerLink $subscriptionCustomerLink
     * @param bool $idCheck
     * @return bool
     */
    protected function _removeSubscriptionCustomerLink( SubscriptionCustomerLink $subscriptionCustomerLink, $idCheck = FALSE ) {
        return $this->_remove($subscriptionCustomerLink,$idCheck);
    }

}