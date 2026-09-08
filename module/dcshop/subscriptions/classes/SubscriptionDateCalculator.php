<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\common\classes\NAVDateFormulaManagement;
use DynCom\dc\dcShop\classes\CurrShopConfiguration;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 20.07.2015
 * Time: 11:11
 */

 
class SubscriptionDateCalculator implements \Psr\Log\LoggerAwareInterface
{

    const BASE_DATE_HANDLING_EXCLUDED = 0;
    const BASE_DATE_HANDLING_INCLUDED = 1;

    protected $shopConfiguration;
    protected $headerRepository;
    protected $sequenceStepRepository;
    protected $NAVDateFormulaManagement;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param CurrShopConfiguration $shopConfiguration
     * @param SubscriptionHeaderRepository $headerRepository
     * @param SubscriptionSequenceStepRepository $sequenceStepRepository
     * @param NAVDateFormulaManagement $NAVDateFormulaManagement
     */
    public function __construct(
        CurrShopConfiguration $shopConfiguration,
        SubscriptionHeaderRepository $headerRepository,
        SubscriptionSequenceStepRepository $sequenceStepRepository,
        NAVDateFormulaManagement $NAVDateFormulaManagement) {
        $this->shopConfiguration = $shopConfiguration;
        $this->headerRepository = $headerRepository;
        $this->sequenceStepRepository = $sequenceStepRepository;
        $this->NAVDateFormulaManagement = $NAVDateFormulaManagement;
    }

    /**
     * @param SubscriptionCustomerLink $subscriptionCustomerLink
     * @param \DateTime $alternativeBaseDate
     * @param $baseDateHandling
     * @return \DateTime|boolean
     * @throws \InvalidArgumentException
     * @throws \ErrorException
     */
    public function getCustomerSubscriptionNextPayOrderCreateDate(
        SubscriptionCustomerLink $subscriptionCustomerLink,
        \DateTime $alternativeBaseDate,
        $baseDateHandling)
    {

        //Log
        $logMsg = 'getting Customer Subscription Next Pay Order Create Date for CustLink with Subscription Code {subscription_code} and CustomerNo {customer_no}';
        $logContext = ['subscription_code' => $subscriptionCustomerLink->subscription_code, 'customer_no' => $subscriptionCustomerLink->customer_no];
        if($this->logger instanceof \Psr\Log\LoggerInterface) {
            $this->logger->log(\Psr\Log\LogLevel::INFO,$logMsg,$logContext);
        }

        $company = $subscriptionCustomerLink->company;
        $subscriptionCode = $subscriptionCustomerLink->subscription_code;

        $headerCriteria = [
            [
                ['company', '=', $company],
                ['code', '=', $subscriptionCode]
            ]
        ];
        $headerCollection = $this->headerRepository->findByCriteria($headerCriteria);
        if (count($headerCollection) !== 1) {
            throw new \InvalidArgumentException(
                'Parameter \'subscriptionCustomerLink\' must contain a valid subscription code.'
            );
        }

        $subscriptionHeader = $headerCollection->getFirst();

        if (($baseDateHandling !== self::BASE_DATE_HANDLING_EXCLUDED) && ($baseDateHandling !== self::BASE_DATE_HANDLING_INCLUDED)) {
            throw new \InvalidArgumentException(
                'Parameter \'baseDateHandling\' must be either SubscriptionDateCalculator::BASE_DATE_HANDLING_EXCLUDED or SubscriptionDateCalculator::BASE_DATE_HANDLING_INCLUDED.'
            );
        }

        $baseDate = new \DateTime();
        $baseDate->setTimestamp(strtotime('midnight'));
        $altBaseDate = new \DateTime();
        $altBaseDate->setTimestamp(strtotime('midnight', $alternativeBaseDate->getTimestamp()));
        $alternativeBaseDate = clone $altBaseDate;
        unset($altBaseDate);
        if ($alternativeBaseDate->getTimestamp() !== $baseDate->getTimestamp()) {
            $baseDate = $alternativeBaseDate;
        }

        $paymentType = $subscriptionHeader->payment_type;


        switch ($paymentType) {
            case SubscriptionHeader::SUBSCRIPTION_PAY_TYPE_ONCE:
                //Log
                $logMsg = 'leaving get-CustomerSubscriptionNextPayOrderCreateDate for CustLink on line ' . __LINE__ . ' with Subscription Code {subscription_code} and CustomerNo {customer_no} with return value FALSE. Reason: Pay-Type is pay_once';
                $logContext = ['subscription_code' => $subscriptionCustomerLink->subscription_code, 'customer_no' => $subscriptionCustomerLink->customer_no];
                if($this->logger instanceof \Psr\Log\LoggerInterface) {
                    $this->logger->log(\Psr\Log\LogLevel::ERROR,$logMsg,$logContext);
                }
                return false;
                break;
            case SubscriptionHeader::SUBSCRIPTION_PAY_TYPE_PER_TURN:
                $payOrderCreateDate = $this->getCustomerSubscriptionNextShipOrderCreateDate($subscriptionCustomerLink, $alternativeBaseDate, $baseDateHandling);
                //Log
                $logMsg = 'leaving get-CustomerSubscriptionNextPayOrderCreateDate for CustLink on line ' . __LINE__ . ' with Subscription Code {subscription_code} and CustomerNo {customer_no} with return value {payOrderCreateDate}. Reason: Pay-Type is per_turn';
                $logContext = ['subscription_code' => $subscriptionCustomerLink->subscription_code, 'customer_no' => $subscriptionCustomerLink->customer_no,'payOrderCreateDate' => $payOrderCreateDate->format('Y-m-dTH:i:s')];
                if($this->logger instanceof \Psr\Log\LoggerInterface) {
                    $this->logger->log(\Psr\Log\LogLevel::INFO,$logMsg,$logContext);
                }
                return $payOrderCreateDate;
                break;
            case SubscriptionHeader::SUBSCRIPTION_PAY_TYPE_MONTHLY:
                $payIntervalFormula = '<CD+1M>';
                break;
            case SubscriptionHeader::SUBSCRIPTION_PAY_TYPE_QUARTERLY:
                $payIntervalFormula = '<CD+3M>';
                break;
            case SubscriptionHeader::SUBSCRIPTION_PAY_TYPE_SEMI_ANNUALLY:
                $payIntervalFormula = '<CD+6M>';
                break;
            case SubscriptionHeader::SUBSCRIPTION_PAY_TYPE_ANNUALLY:
                $payIntervalFormula = '<CD+12M>';
                break;
            default:
                throw new \LogicException('SubscriptionHeader has an invalid payment type');
        }

        $subscriptionType = $subscriptionHeader->type;
        $signupDateStr = $subscriptionCustomerLink->signup_date;
        $signupDate = \DateTime::createFromFormat('Y-m-d H:i:s', $signupDateStr);
        $startDate = clone $signupDate;
        if ($subscriptionType === SubscriptionHeader::SUBSCRIPTION_TYPE_ORDER_OPTION) {
            $this->NAVDateFormulaManagement->evaluateDateFormula($subscriptionHeader->start_date_formula, $startDate);
        } elseif ($subscriptionType === SubscriptionHeader::SUBSCRIPTION_TYPE_SEQUENCE_ITEM) {

            $sequenceStepCriteria = [
                [
                    ['company', '=', $company],
                    ['subscription_code', '=', $subscriptionCode]
                ]
            ];
            $sequenceStepCollection = $this->sequenceStepRepository->findByCriteria($sequenceStepCriteria);
            $firstStep = $sequenceStepCollection->getFirst();
            if (!(count($sequenceStepCollection) > 0)) {
                //Log
                $logMsg = 'leaving get-CustomerSubscriptionNextPayOrderCreateDate for CustLink on line ' . __LINE__ . ' with Subscription Code {subscription_code} and CustomerNo {customer_no} with return value FALSE. Reason: Type is Sequence-Item but no sequence steps are present';
                $logContext = ['subscription_code' => $subscriptionCustomerLink->subscription_code, 'customer_no' => $subscriptionCustomerLink->customer_no];
                if($this->logger instanceof \Psr\Log\LoggerInterface) {
                    $this->logger->log(\Psr\Log\LogLevel::ERROR,$logMsg,$logContext);
                }
                return false;
            }

            if (($subscriptionHeader->start_type === SubscriptionHeader::SUBSCRIPTION_START_TYPE_ANEW)) {
                $interval = $firstStep->interval;
                $this->NAVDateFormulaManagement->evaluateDateFormula($interval, $startDate);
            } elseif ($subscriptionHeader->start_type === SubscriptionHeader::SUBSCRIPTION_START_TYPE_JOIN) {
                $fixDate = $firstStep->fix_date;
                $startDate = \DateTime::createFromFormat('Y-m-d H:i:s', $fixDate);
            }
        }

        $shop = $this->shopConfiguration->getShop();
        $forerunPaymentCapture = $shop->forerun_payment_capture;
        $intermediateCalculationDate = clone $startDate;
        $this->NAVDateFormulaManagement->evaluateDateFormula($forerunPaymentCapture,$intermediateCalculationDate);
        $this->NAVDateFormulaManagement->mutateToClosestWorkDay($intermediateCalculationDate);

        while(
            (($intermediateCalculationDate->getTimestamp() < $baseDate->getTimestamp()) && ($baseDateHandling === SubscriptionDateCalculator::BASE_DATE_HANDLING_INCLUDED)) ||
            (($intermediateCalculationDate->getTimestamp() <= $baseDate->getTimestamp()) && ($baseDateHandling === SubscriptionDateCalculator::BASE_DATE_HANDLING_EXCLUDED))
        ) {
            $this->NAVDateFormulaManagement->evaluateDateFormula($payIntervalFormula,$intermediateCalculationDate);
            $this->NAVDateFormulaManagement->evaluateDateFormula($forerunPaymentCapture,$intermediateCalculationDate);
            $this->NAVDateFormulaManagement->mutateToClosestWorkDay($intermediateCalculationDate);
        }

        $payOrderCreateDate = $intermediateCalculationDate;
        //Log
        $logMsg = 'leaving get-CustomerSubscriptionNextPayOrderCreateDate for CustLink on line ' . __LINE__ . ' with Subscription Code {subscription_code} and CustomerNo {customer_no} with return value {payOrderCreateDate}. Reason: Done';
        $logContext = ['subscription_code' => $subscriptionCustomerLink->subscription_code, 'customer_no' => $subscriptionCustomerLink->customer_no,'payOrderCreateDate' => $payOrderCreateDate->format('Y-m-dTH:i:s')];
        if($this->logger instanceof \Psr\Log\LoggerInterface) {
            $this->logger->log(\Psr\Log\LogLevel::INFO,$logMsg,$logContext);
        }
        return $payOrderCreateDate;
    }


    /**
     * @param SubscriptionCustomerLink $subscriptionCustomerLink
     * @param \DateTime $alternativeBaseDate
     * @param $baseDateHandling
     * @return bool
     */
    public function getCustomerSubscriptionNextShipOrderCreateDate(SubscriptionCustomerLink $subscriptionCustomerLink,
                                                                   \DateTime $alternativeBaseDate,
                                                                   $baseDateHandling) {

        $company = $subscriptionCustomerLink->company;
        $subscriptionCode = $subscriptionCustomerLink->subscription_code;

        $headerCriteria = [
            [
                ['company', '=', $company],
                ['code', '=', $subscriptionCode]
            ]
        ];
        $headerCollection = $this->headerRepository->findByCriteria($headerCriteria);
        if (count($headerCollection) !== 1) {
            throw new \InvalidArgumentException(
                'Parameter \'subscriptionCustomerLink\' must contain a valid subscription code.'
            );
        }

        $subscriptionHeader = $headerCollection->getFirst();

        if (($baseDateHandling !== self::BASE_DATE_HANDLING_EXCLUDED) && ($baseDateHandling !== self::BASE_DATE_HANDLING_INCLUDED)) {
            throw new \InvalidArgumentException(
                'Parameter \'baseDateHandling\' must be either SubscriptionDateCalculator::BASE_DATE_HANDLING_EXCLUDED or SubscriptionDateCalculator::BASE_DATE_HANDLING_INCLUDED.'
            );
        }

        $baseDate = new \DateTime();
        $baseDate->setTimestamp(strtotime('midnight'));
        $altBaseDate = new \DateTime();
        $altBaseDate->setTimestamp(strtotime('midnight', $alternativeBaseDate->getTimestamp()));
        $alternativeBaseDate = clone $altBaseDate;
        unset($altBaseDate);
        if ($alternativeBaseDate->getTimestamp() !== $baseDate->getTimestamp()) {
            $baseDate = $alternativeBaseDate;
        }

        $nextPlannedShippingDate = $this->getCustomerSubscriptionNextPlannedDeliveryDate($subscriptionCustomerLink,$alternativeBaseDate,$baseDateHandling);

        if($nextPlannedShippingDate === false) {
            //Log
            $logMsg = 'leaving getCustomerSubscriptionNextShipOrderCreateDate for CustLink on line ' . __LINE__ . ' with Subscription Code {subscription_code} and CustomerNo {customer_no} with return value FALSE. Reason: No next planned shipping date';
            $logContext = ['subscription_code' => $subscriptionCustomerLink->subscription_code, 'customer_no' => $subscriptionCustomerLink->customer_no];
            if($this->logger instanceof \Psr\Log\LoggerInterface) {
                $this->logger->log(\Psr\Log\LogLevel::ERROR,$logMsg,$logContext);
            }
            return false;
        }

        $shop = $this->shopConfiguration->getShop();
        $forerunOrderCreate = $shop->forerun_subscr_order_create;
        if(empty($forerunOrderCreate)) {
            $forerunOrderCreate = 'CD';
        }

        $nextOrderCreationDate = clone $nextPlannedShippingDate;
        $this->NAVDateFormulaManagement->evaluateDateFormula($forerunOrderCreate,$nextOrderCreationDate);
        if(
            (($nextOrderCreationDate->getTimestamp() < $baseDate->getTimestamp()) && ($baseDateHandling === self::BASE_DATE_HANDLING_INCLUDED)) ||
            (($nextOrderCreationDate->getTimestamp() <= $baseDate->getTimestamp()) && ($baseDateHandling === self::BASE_DATE_HANDLING_EXCLUDED))
        ) {
            $nextOrderCreationDate = $this->getCustomerSubscriptionNextPlannedDeliveryDate($subscriptionCustomerLink,$nextOrderCreationDate,self::BASE_DATE_HANDLING_EXCLUDED);
            if($nextOrderCreationDate === false) {
                //Log
                $logMsg = 'leaving getCustomerSubscriptionNextShipOrderCreateDate for CustLink on line ' . __LINE__ . ' with Subscription Code {subscription_code} and CustomerNo {customer_no} with return value FALSE. Reason: Next order creation date initially in the past and no future date could be gotten.';
                $logContext = ['subscription_code' => $subscriptionCustomerLink->subscription_code, 'customer_no' => $subscriptionCustomerLink->customer_no];
                if($this->logger instanceof \Psr\Log\LoggerInterface) {
                    $this->logger->log(\Psr\Log\LogLevel::ERROR,$logMsg,$logContext);
                }
                return false;
            }
            $this->NAVDateFormulaManagement->evaluateDateFormula($forerunOrderCreate,$nextOrderCreationDate);
        }

        $this->NAVDateFormulaManagement->mutateToClosestWorkDay($nextOrderCreationDate);
//Log
        $logMsg = 'leaving getCustomerSubscriptionNextShipOrderCreateDate for CustLink on line ' . __LINE__ . ' with Subscription Code {subscription_code} and CustomerNo {customer_no} with return value {nextOrderCreationDate}. Reason: Done.';
        $logContext = ['subscription_code' => $subscriptionCustomerLink->subscription_code, 'customer_no' => $subscriptionCustomerLink->customer_no,'nextOrderCreationDate' => $nextOrderCreationDate->format('Y-m-dTH:i:s')];
        if($this->logger instanceof \Psr\Log\LoggerInterface) {
            $this->logger->log(\Psr\Log\LogLevel::INFO,$logMsg,$logContext);
        }
        return $nextOrderCreationDate;
    }

    /**
     * @param SubscriptionCustomerLink $subscriptionCustomerLink
     * @param \DateTime $alternativeBaseDate
     * @param $baseDateHandling
     * @return \DateTime|bool
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \ErrorException
     */
    public function getCustomerSubscriptionNextPlannedDeliveryDate(SubscriptionCustomerLink $subscriptionCustomerLink,\DateTime $alternativeBaseDate,$baseDateHandling) {

        $today = new \DateTime();
        $today->setTimestamp(strtotime('midnight'));

        $company = $subscriptionCustomerLink->company;
        $subscriptionCode = $subscriptionCustomerLink->subscription_code;

        $headerCriteria = [
            [
                ['company', '=', $company],
                ['code', '=', $subscriptionCode]
            ]
        ];
        $headerCollection = $this->headerRepository->findByCriteria($headerCriteria);
        if (count($headerCollection) !== 1) {
            throw new \InvalidArgumentException(
                'Parameter \'subscriptionCustomerLink\' must contain a valid subscription code.'
            );
        }

        $subscriptionHeader = $headerCollection->getFirst();
        $headerType = $subscriptionHeader->type;
        $headerPayType = $subscriptionHeader->payment_type;

        $headerValidFrom = $subscriptionHeader->valid_from;
        if($headerValidFrom === '' || $headerValidFrom === '0000-00-00 00:00:00' || $headerValidFrom === '0000-00-00' || is_null($headerValidFrom )) {
            $headerValidFrom = new \DateTime();
            $headerValidFrom->setTimestamp(strtotime('0000-00-00 00:00'));
        } else {
            $headerValidFrom = \DateTime::createFromFormat('Y-m-d H:i:s',$headerValidFrom);
        }
        $headerValidTo = $subscriptionHeader->valid_to;
        if($headerValidTo === '' || $headerValidTo === '0000-00-00 00:00:00' || $headerValidTo === '0000-00-00' || is_null($headerValidTo )) {

            $headerValidTo = new \DateTime();
            $headerValidTo->setTimestamp(strtotime('2020-12-31 23:59:59'));
        } else {

            $headerValidTo = \DateTime::createFromFormat('Y-m-d H:i:s',$headerValidTo);
        }
        if (($baseDateHandling !== self::BASE_DATE_HANDLING_EXCLUDED) && ($baseDateHandling !== self::BASE_DATE_HANDLING_INCLUDED)) {
            throw new \InvalidArgumentException(
                'Parameter \'baseDateHandling\' must be either SubscriptionDateCalculator::BASE_DATE_HANDLING_EXCLUDED or SubscriptionDateCalculator::BASE_DATE_HANDLING_INCLUDED.'
            );
        }


        $baseDate = new \DateTime();
        $baseDate->setTimestamp(strtotime('midnight'));
        $altDate = new \DateTime();
        $altDate->setTimestamp(strtotime('midnight', $alternativeBaseDate->getTimestamp()));
        $alternativeBaseDate = clone $altDate;
        unset($altDate);
        if ($alternativeBaseDate->getTimestamp() !== $baseDate->getTimestamp()) {
            $baseDate = $alternativeBaseDate;
        }

        $headerNotActive = ($subscriptionHeader->active == false);
        $headerNotYetValid = (($baseDate->getTimestamp() < $headerValidFrom->getTimestamp()) && ($baseDateHandling === self::BASE_DATE_HANDLING_INCLUDED))
            ||  (($baseDate->getTimestamp() <= $headerValidFrom->getTimestamp()) && ($baseDateHandling === self::BASE_DATE_HANDLING_EXCLUDED));
        $headerNoLongerValid = (($baseDate->getTimestamp() > $headerValidTo->getTimestamp()) && ($baseDateHandling === self::BASE_DATE_HANDLING_INCLUDED))
            ||  (($baseDate->getTimestamp() >= $headerValidTo->getTimestamp()) && ($baseDateHandling === self::BASE_DATE_HANDLING_EXCLUDED));

        if(
            $headerNotActive || $headerNotYetValid || $headerNoLongerValid
        ) {
            //Log
            $logMsg = 'leaving getCustomerSubscriptionNextPlannedDeliveryDate for CustLink on line ' . __LINE__ . ' with Subscription Code {subscription_code} and CustomerNo {customer_no} with return value FALSE. Reason: Header not valid.';
            $logContext = ['subscription_code' => $subscriptionCustomerLink->subscription_code, 'customer_no' => $subscriptionCustomerLink->customer_no];
            if($this->logger instanceof \Psr\Log\LoggerInterface) {
                $this->logger->log(\Psr\Log\LogLevel::ERROR,$logMsg,$logContext);
            }
            return false;
        }


        $startDateFormula = $subscriptionHeader->start_date_formula;
        $turnIntervalFormula = $subscriptionHeader->turn_interval_formula;
        $signupDateStr = $subscriptionCustomerLink->signup_date;


        //If there is no signup day, set it to today in order to be able to collect and display information
        //About when the next shipment *would* be if the customer signed up today.
        if($signupDateStr === '' || $signupDateStr === '0000-00-00 00:00:00' || $signupDateStr === '0000-00-00') {
            $signupDate = $today;
        } else {
            $signupDate = new \DateTime();
            $signupDate->setTimestamp(strtotime($signupDateStr));
        }


        switch($headerType) {
            case SubscriptionHeader::SUBSCRIPTION_TYPE_ORDER_OPTION:

                $currRefTurn = 1;
                $currRefDateStr = $subscriptionCustomerLink->signup_date;
                if($currRefDateStr === '' || $currRefDateStr === '0000-00-00 00:00:00' || $currRefDateStr === '0000-00-00') {
                    $currRefDate = new \DateTime();
                } else {
                    $currRefDate = \DateTime::createFromFormat('Y-m-d',$currRefDateStr);
                }

                $firstPlannedDeliveryDate = clone $currRefDate;
                $this->NAVDateFormulaManagement->evaluateDateFormula($startDateFormula,$firstPlannedDeliveryDate);
                $firstPlannedDeliveryDate = new \DateTime(strtotime('midnight',$firstPlannedDeliveryDate));

                $nextTurnDateFormula = ($firstPlannedDeliveryDate->getTimestamp() >= $baseDate->getTimestamp()) ? $startDateFormula : $turnIntervalFormula;

                $intermediateCalculationDate = clone $firstPlannedDeliveryDate;
                $this->NAVDateFormulaManagement->evaluateDateFormula($nextTurnDateFormula,$intermediateCalculationDate);
                $this->NAVDateFormulaManagement->mutateToClosestWorkDay($intermediateCalculationDate);

                while(
                    (($intermediateCalculationDate->getTimestamp() < $baseDate->getTimestamp()) && ($baseDateHandling === SubscriptionDateCalculator::BASE_DATE_HANDLING_INCLUDED)) ||
                    (($intermediateCalculationDate->getTimestamp() <= $baseDate->getTimestamp()) && ($baseDateHandling === SubscriptionDateCalculator::BASE_DATE_HANDLING_EXCLUDED))
                ) {
                    $this->NAVDateFormulaManagement->evaluateDateFormula($nextTurnDateFormula,$intermediateCalculationDate);
                    $this->NAVDateFormulaManagement->mutateToClosestWorkDay($intermediateCalculationDate);
                    ++$currRefTurn;
                }

                break;
            case SubscriptionHeader::SUBSCRIPTION_TYPE_SEQUENCE_ITEM:

                $sequenceStepCollection = $this->sequenceStepRepository->getRemainingStepsFromDateForHeader($subscriptionHeader,$baseDate,$this->NAVDateFormulaManagement);
                $firstStep = $sequenceStepCollection->getFirst();

                $intermediateCalculationDate = clone $signupDate;
                if($subscriptionHeader->startTypeIsBeginAnew()) {
                    $this->NAVDateFormulaManagement->evaluateDateFormula($firstStep->interval,$intermediateCalculationDate);
                } elseif($subscriptionHeader->startTypeIsJoin()) {
                    $firstStep = $sequenceStepCollection->getFirst();
                    if(
                        ($subscriptionHeader->allow_late_entry) &&
                        !($subscriptionCustomerLink->no_of_turns_processed > 0) &&
                        ($firstStep->previousStep instanceof SubscriptionSequenceStep)) {
                        $firstStep = $firstStep->previousStep;
                    }
                    if(isset($firstStep->fix_date) && $firstStep->fix_date !== '' && $firstStep->fix_date !== '0000-00-00') {
                        $intermediateCalculationDate = \DateTime::createFromFormat('Y-m-d',$firstStep->fix_date);
                        $this->NAVDateFormulaManagement->mutateToClosestWorkDay($intermediateCalculationDate);
                    } else {
                        $intermediateCalculationDate = clone $firstStep->intervalTransformedToFixedDate;
                        $this->NAVDateFormulaManagement->mutateToClosestWorkDay($intermediateCalculationDate);
                    }
                }

                //If date is in the past - set for closest workday.
                if($intermediateCalculationDate->getTimestamp() < $baseDate->getTimestamp()) {
                    $intermediateCalculationDate = new \DateTime();
                    $intermediateCalculationDate->setTimestamp(strtotime('midnight'));
                    $this->NAVDateFormulaManagement->mutateToClosestWorkDay($intermediateCalculationDate);
                }
                break;
            default:
                throw new \InvalidArgumentException(
                    'Subscription for CustomerLink must have a valid type (order-option or sequence-step).'
                );
                break;
        }


        if(isset($subscriptionCustomerLink->cancellation_date) && $subscriptionCustomerLink->cancellation_date !== '' && $subscriptionCustomerLink->cancellation_date !== '0000-00-00 00:00:00' && $subscriptionCustomerLink->cancellation_date !== '0000-00-00' && $subscriptionCustomerLink->cancellation_date !== null && $subscriptionCustomerLink->cancellation_date !== 'NULL') {
            $cancellationDate = \DateTime::createFromFormat('Y-m-d',$subscriptionCustomerLink->cancellation_date);
            $cutoffDate = clone $cancellationDate;
            $this->NAVDateFormulaManagement->evaluateDateFormula($subscriptionHeader->cancel_period_date_formula,$cutoffDate);
            if($intermediateCalculationDate->getTimestamp() > $cutoffDate->getTimestamp()) {
                //Log
                $logMsg = 'leaving getCustomerSubscriptionNextPlannedDeliveryDate for CustLink on line ' . __LINE__ . ' with Subscription Code {subscription_code} and CustomerNo {customer_no} with return value FALSE. Reason: Subsc cancelled.';
                $logContext = ['subscription_code' => $subscriptionCustomerLink->subscription_code, 'customer_no' => $subscriptionCustomerLink->customer_no];
                if($this->logger instanceof \Psr\Log\LoggerInterface) {
                    $this->logger->log(\Psr\Log\LogLevel::INFO,$logMsg,$logContext);
                }
                return false;
            }
        }
        //Log
        $logMsg = 'leaving getCustomerSubscriptionNextPlannedDeliveryDate for CustLink on line ' . __LINE__ . ' with Subscription Code {subscription_code} and CustomerNo {customer_no} with return value {intermediateCalculationDate}. Reason: Done.';
        $logContext = ['subscription_code' => $subscriptionCustomerLink->subscription_code, 'customer_no' => $subscriptionCustomerLink->customer_no,'intermediateCalculationDate' => $intermediateCalculationDate->format('Y-m-dTH:i:s')];
        if($this->logger instanceof \Psr\Log\LoggerInterface) {
            $this->logger->log(\Psr\Log\LogLevel::INFO,$logMsg,$logContext);
        }
        return $intermediateCalculationDate;

    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function setLogger(\Psr\Log\LoggerInterface $logger) {
        $this->logger = $logger;
    }


}