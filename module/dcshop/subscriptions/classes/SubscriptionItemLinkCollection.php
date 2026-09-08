<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Class SubscriptionItemLinkCollection
 */
class SubscriptionItemLinkCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param SubscriptionItemLinkConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( SubscriptionItemLinkConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return SubscriptionItemLinkCollection
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
        return $this->_addSubscriptionItemLink($instance, $idCheck);
    }

    /**
     * @param SubscriptionItemLink $subscriptionItemLink
     * @param bool $idCheck
     * @return bool
     */
    protected function _addSubscriptionItemLink( SubscriptionItemLink $subscriptionItemLink, $idCheck = FALSE ) {
        return $this->_add($subscriptionItemLink, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeSubscriptionItemLink($instance,$idCheck);
    }

    /**
     * @param SubscriptionItemLink $subscriptionItemLink
     * @param bool $idCheck
     * @return bool
     */
    protected function _removeSubscriptionItemLink( SubscriptionItemLink $subscriptionItemLink, $idCheck = FALSE ) {
        return $this->_remove($subscriptionItemLink,$idCheck);
    }

}