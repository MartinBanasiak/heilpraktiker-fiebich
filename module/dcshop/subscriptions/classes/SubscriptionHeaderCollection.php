<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Class SubscriptionHeaderCollection
 */
class SubscriptionHeaderCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param SubscriptionHeaderConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( SubscriptionHeaderConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return SubscriptionHeaderCollection
     */
    public function getEmptyCollection() {
        return new self($this->config,$this->criteriaValidationService);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     * @return bool
     * @throws \DomainException
     */
    public function add( $instance, $idCheck = FALSE ) {
        return $this->_addSubscriptionHeader($instance, $idCheck);
    }

    /**
     * @param SubscriptionHeader $subscriptionHeader
     * @param bool $idCheck
     * @return bool
     */
    protected function _addSubscriptionHeader( SubscriptionHeader $subscriptionHeader, $idCheck = FALSE ) {
        return $this->_add($subscriptionHeader, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeSubscriptionHeader($instance,$idCheck);
    }

    /**
     * @param SubscriptionHeader $subscriptionHeader
     * @param bool $idCheck
     * @return bool
     */
    protected function _removeSubscriptionHeader( SubscriptionHeader $subscriptionHeader, $idCheck = FALSE ) {
        return $this->_remove($subscriptionHeader,$idCheck);
    }

    /**
     * @return SubscriptionHeader
     */
    public function getFirst() {

        $this->rewind();
        if (0 !== count($this->hashObjectMap)) {
            return $this->hashObjectMap[$this->hashList[0]];
        }
        return $this->getNullObject();
        /**
        $this->elements->rewind();
        return $this->elements->valid() ? $this->elements->current(): $this->getNullObject();
         */
    }

}