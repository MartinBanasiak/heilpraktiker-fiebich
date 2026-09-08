<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\common\classes\NAVDateFormulaManagement;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;
use DynCom\dc\dcShop\classes\Shop;

/**
 * Class SubscriptionSequenceStepRepository
 */
class SubscriptionSequenceStepRepository implements Repository{

    use genericRepositoryTrait;

    private $shop;

    /**
     * @param GenericDBQueryWrapperInterface $db
     * @param SubscriptionSequenceStepConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param SubscriptionSequenceStepCollection $collection
     * @param Shop $currShop
     * @param $cacheAll
     */
    public function __construct( GenericDBQueryWrapperInterface $db, SubscriptionSequenceStepConfig $config, CriteriaHelperInterface $criteriaValidationService, SubscriptionSequenceStepCollection $collection, Shop $currShop, $cacheAll=false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->shop                      = $currShop;
        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @return SubscriptionSequenceStep
     */
    public function getNullObject() {
        return new SubscriptionSequenceStep($this->config);
    }

    /**
     * @param SubscriptionHeader $header
     * @return SubscriptionSequenceStepCollection
     */
    public function getAllForHeader(SubscriptionHeader $header) {
        if(!$header->typeIsSequenceItem()) {
            return new SubscriptionSequenceStepCollection($this->config,$this->criteriaValidationService);
        }
        $criteria = [
            [
                ['company','=',$header->company],
                ['subscription_code','=',$header->code]
            ]
        ];
        return $this->findByCriteria($criteria);
    }

    /**
     * @param SubscriptionHeader $header
     * @param \DateTime $referenceDateTime
     * @param NAVDateFormulaManagement $dateFormulaManagement
     * @return SubscriptionSequenceStepCollection
     */
    public function getRemainingStepsFromDateForHeader(SubscriptionHeader $header, \DateTime $referenceDateTime, NAVDateFormulaManagement $dateFormulaManagement) {
        if(!$header->typeIsSequenceItem()) {
            return new SubscriptionSequenceStepCollection($this->config,$this->criteriaValidationService);
        }
        $timeStr = strtotime('midnight',$referenceDateTime->getTimestamp());
        $referenceDateTime = new \DateTime();
        $referenceDateTime->setTimestamp($timeStr);
        $allSteps = $this->getAllForHeader($header);
        if($header->startTypeIsBeginAnew()) return $allSteps;
        $startDate = clone $referenceDateTime;
        $dateFormulaManagement->mutateToClosestWorkDay($startDate);
        $previousDate = clone $startDate;
        $filteredSteps = $allSteps->getEmptyCollection();
        $i = 0;
        $currStep = null;
        $currStepDate = null;
        $previousStep = null;
        for($allSteps->rewind();$allSteps->isCurrValid();$allSteps->next()) {
            if(null !== $currStep && null !== $currStepDate) {
                $previousStep = $currStep;
            }
            $currStep = $allSteps->current();
            if($currStep->fix_date) {
                $currStepDate = $startDate->createFromFormat('Y-m-d', $currStep->fix_date);
                $dateFormulaManagement->mutateToClosestWorkDay($currStepDate);
            } else {
                $currStepDate = clone $previousDate;
                $dateFormulaManagement->evaluateDateFormula($currStep->interval,$currStepDate);
                $dateFormulaManagement->mutateToClosestWorkDay($currStepDate);
                $currStep->setIntervalTransformedToFixedDate(clone $currStepDate);
            }

            if($previousStep) {
                $currStep->setPreviousStep($previousStep);
            }

            if($i === 0) $startDate = $currStepDate;

            $currOrderCreateDate = clone $currStepDate;
            $forerunOrderCreate = $this->shop->forerun_subscr_order_create;
            if(empty($forerunOrderCreate)) {
                $forerunOrderCreate = 'CD';
            }

            $nextOrderCreationDate = clone $currOrderCreateDate;
            $dateFormulaManagement->evaluateDateFormula($forerunOrderCreate,$nextOrderCreationDate);

            if($nextOrderCreationDate->getTimestamp() >= $referenceDateTime->getTimestamp()) {
                $filteredSteps->add($currStep);
            }

            $previousDate = clone $currStepDate;
            ++$i;
        }
        return $filteredSteps;
    }

    /**
     * @param SubscriptionHeader $header
     * @return mixed|SubscriptionSequenceStep
     */
    public function getFirstStepForHeader(SubscriptionHeader $header) {
        $collection = $this->getAllForHeader($header);
        return $collection->getFirst();
    }

    /**
     * @param SubscriptionHeader $header
     * @param \DateTime $alternativeBaseDate
     * @param $baseDateHandling
     * @return bool|mixed
     */
    public function getFirstStep(SubscriptionHeader $header, \DateTime $alternativeBaseDate, $baseDateHandling) {
		
		$baseDate = new \DateTime();
        $baseDate->setTimestamp(strtotime('midnight'));
        $altDate = new \DateTime();
        $altDate->setTimestamp(strtotime('midnight', $alternativeBaseDate->getTimestamp()));
        $alternativeBaseDate = clone $altDate;
        unset($altDate);
        if ($alternativeBaseDate->getTimestamp() !== $baseDate->getTimestamp()) {
            $baseDate = $alternativeBaseDate;
        }
		
		if($header->type === SubscriptionHeader::SUBSCRIPTION_TYPE_ORDER_OPTION) {
			return false;
		}
        	$dateFormulaManagement = new NAVDateFormulaManagement();
		$sequenceStepCollection = $this->getRemainingStepsFromDateForHeader($header,$baseDate,$dateFormulaManagement);
		$firstStep = $sequenceStepCollection->getFirst();

		if($header->startTypeIsBeginAnew()) {
			return $firstStep;
		} elseif($header->startTypeIsJoin()) {
			$firstStep = $sequenceStepCollection->getFirst();
			if(
				($header->allow_late_entry) &&
				($firstStep->previousStep instanceof SubscriptionSequenceStep)) {
				$firstStep = $firstStep->previousStep;
			}
			return $firstStep;
		}
		return false;
	}


}