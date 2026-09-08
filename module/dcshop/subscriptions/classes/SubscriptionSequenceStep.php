<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Class SubscriptionSequenceStep
 */
class SubscriptionSequenceStep implements GenericDBModelInterface, Entity
{

    use genericDBModelTrait, universallyGettableTrait;

      protected $id;
      protected $company;
      protected $subscription_code;
      protected $line_no;
      protected $fix_date;
      protected $interval;
      protected $no_of_items;
      protected $to_delete;
    protected $previousStep;
    protected $intervalTransformedToFixedDate;

    /**
     * @param SubscriptionSequenceStepConfig $config
     */
    public function __construct( SubscriptionSequenceStepConfig $config ) {
        $this->config = $config;
    }

    /**
     * @return SubscriptionSequenceStep
     */
    public function getNullObject() {
        return new self($this->config);
    }

    /**
     * @param SubscriptionSequenceStep $previousStep
     */
    public function setPreviousStep(SubscriptionSequenceStep $previousStep)
    {
        $this->previousStep = $previousStep;
    }

    /**
     * @param \DateTime $intervalTransformedToFixedDate
     */
    public function setIntervalTransformedToFixedDate(\DateTime $intervalTransformedToFixedDate)
    {
        $this->intervalTransformedToFixedDate = $intervalTransformedToFixedDate;
    }



}