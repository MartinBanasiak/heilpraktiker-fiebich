<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Class SubscriptionSequenceStepCollection
 */
class SubscriptionSequenceStepCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param SubscriptionSequenceStepConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( SubscriptionSequenceStepConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return SubscriptionSequenceStepCollection
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
        return $this->_addSubscriptionSequenceStep($instance, $idCheck);
    }

    /**
     * @param SubscriptionSequenceStep $subscriptionSequenceStep
     * @param bool $idCheck
     * @return bool
     */
    protected function _addSubscriptionSequenceStep( SubscriptionSequenceStep $subscriptionSequenceStep, $idCheck = FALSE ) {
        return $this->_add($subscriptionSequenceStep, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeSubscriptionSequenceStep($instance,$idCheck);
    }

    /**
     * @param SubscriptionSequenceStep $subscriptionSequenceStep
     * @param bool $idCheck
     * @return bool
     */
    protected function _removeSubscriptionSequenceStep( SubscriptionSequenceStep $subscriptionSequenceStep, $idCheck = FALSE ) {
        return $this->_remove($subscriptionSequenceStep,$idCheck);
    }

}