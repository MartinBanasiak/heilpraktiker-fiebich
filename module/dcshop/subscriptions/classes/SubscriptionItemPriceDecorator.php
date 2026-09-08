<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\common\classes\NAVDateFormulaManagement;
use DynCom\dc\dcShop\abstracts\ItemPriceDataDecorator;
use DynCom\dc\dcShop\classes\GenericLineDiscount;
use DynCom\dc\dcShop\classes\ItemPriceData;
use DynCom\dc\dcShop\interfaces\IVATManager;
use DynCom\dc\dcShop\subscriptions\interfaces\SubscriptionItemPriceDataInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/26/2015
 * Time: 4:53 PM
 */
class SubscriptionItemPriceDecorator extends ItemPriceDataDecorator implements SubscriptionItemPriceDataInterface
{

    public const PRICE_SOURCE_SUBSCRIPTION_FIX_PRICE = 'PRICE_SOURCE_SUBSCRIPTION_FIX_PRICE';
    public const PRICE_SOURCE_SUBSCRIPTION_PRICE_PER_BILLING_INTERVAL = 'PRICE_SOURCE_SUBSCRIPTION_PRICE_PER_BILLING_INTERVAL';
    public const DISCOUNT_STATUS_SUBSCRIPTION_DISCOUNT_APPLIED = 'SUBSCRIPTION_DISCOUNT_APPLIED';

    /**
     * @var float
     */
    protected $initialPrice;

    /**
     * @var SubscriptionHeader
     */
    protected $subscriptionHeader;
    /**
     * @var SubscriptionItemLink
     */
    protected $subscriptionItemLink;

    /**
     * @var NAVDateFormulaManagement
     */
    protected $navDateFormulaManagement;
    /**
     * @var string
     */
    protected $priceSource;
    /**
     * @var string
     */
    protected $discountStatus;

    /**
     * @var float
     */
    protected $subscriptionSequenceTotal = 0.00;

    /**
     * @var string
     */
    protected $subscriptionPayIntervalDescription = '';
    /**
     * @var float
     */
    protected $subscriptionSavingsPercent;
    /**
     * @var float
     */
    protected $subscriptionSavingsAmnt;

    protected $allowedPriceSources = [
        ItemPriceData::PRICE_SOURCE_ITEM_PRICE,
        ItemPriceData::PRICE_SOURCE_PRICE_LINES,
        self::PRICE_SOURCE_SUBSCRIPTION_FIX_PRICE,
    ];

    protected $allowedDiscountSources = [
        ItemPriceData::DISCOUNT_STATUS_LINE_DISC_APPLIED,
        self::DISCOUNT_STATUS_SUBSCRIPTION_DISCOUNT_APPLIED
    ];
	
	private $VATManager;
	
    /**
     * @var SubscriptionSequenceStepRepository
     */
    private $seqStepRepository;


    /**
     * @param ItemPriceData $itemPriceData
     * @param SubscriptionHeader $subscriptionHeader
     * @param SubscriptionSequenceStepRepository $seqStepRepository
     * @param NAVDateFormulaManagement $navDateFormulaManagement
     * @param IVATManager $VATManager
     * @param SubscriptionItemLink $subscriptionItemLink
     */
    public function __construct(
            ItemPriceData $itemPriceData,
            SubscriptionHeader $subscriptionHeader,
            SubscriptionSequenceStepRepository $seqStepRepository,
            NAVDateFormulaManagement $navDateFormulaManagement,
			IVATManager $VATManager,
            SubscriptionItemLink $subscriptionItemLink = null)
    {		
        parent::__construct($itemPriceData);
		$this->VATManager = $VATManager;
        $this->initialPrice = $this->itemPriceData->price;
        $this->subscriptionHeader = $subscriptionHeader;
        $this->subscriptionItemLink = $subscriptionItemLink;
        $this->priceSource = $this->itemPriceData->getPriceSource();
        $this->discountStatus = $this->itemPriceData->getDiscountStatus();
        $this->seqStepRepository = $seqStepRepository;
        $this->navDateFormulaManagement = $navDateFormulaManagement;
        $this->processSubscriptionData();
    }

    protected function processSubscriptionData()
    {
        $savingsPercent = 0.00;
        $savingsCurr = 0.00;
        $origPrice = $this->itemPriceData->getPrice();
        $origCrossPrice = $this->itemPriceData->getCrossPrice();
        $vatIncluded = $this->subscriptionItemLink->fix_price > 0 ? (bool)$this->subscriptionItemLink->price_includes_vat : (bool)$this->itemPriceData->priceIncludesVAT;



        if ($this->subscriptionHeader->typeIsSequenceItem()) {
            $this->itemPriceData->setPrice($this->subscriptionHeader->price_per_billing_interval);
            $this->setPriceSourceSubscriptionPricePerBillingInterval();
            $this->itemPriceData->setDiscountAmount(0);
            $this->itemPriceData->setDiscountStatusNotDiscounted();
            $savingsPercent = round((abs(1 - ($this->itemPriceData->price - $this->subscriptionHeader->price_per_billing_interval)) * 100), 2);
            $savingsCurr = $this->itemPriceData->price - $this->subscriptionHeader->price_per_billing_interval;
            $this->setSubscriptionSavingsPercent($savingsPercent);
            $this->setSubscriptionSavingsAmount($savingsCurr);
            $this->setPriceSourceID($this->subscriptionHeader->getID());
            $this->subscriptionSequenceTotal = $this->getRemainingStepsFromDateForHeaderTotal($this->subscriptionHeader,new \DateTime());
			$vatCode = $this->itemPriceData->getVATCode();		
			if(!empty($vatCode)) {
				$this->VATManager->doVATCalculationOnItemPrice($this->itemPriceData);
			}
            return;
        }
        $subscriptionFixPrice = $this->subscriptionItemLink->fix_price;
        $subscriptionDiscountPercent = $this->subscriptionItemLink->discount_percent;
        if ($subscriptionFixPrice > 0) {
            $this->itemPriceData->setVATAdjustmentStatusUnadjusted();
            $savingsCurr = $this->itemPriceData->price - $subscriptionFixPrice;
            $savingsPercent =  round((100 / ($this->itemPriceData->price / $savingsCurr)),2);
            $finalPrice = $subscriptionFixPrice;
            $this->itemPriceData->setPrice($finalPrice);
            $this->setPriceSourceSubscriptionFixPrice();
            $this->itemPriceData->setDiscountAmount(0);
            $this->itemPriceData->setDiscountStatusNotDiscounted();
            $this->setSubscriptionSavingsAmount($savingsCurr);
            $this->setSubscriptionSavingsPercent($savingsPercent);
            $this->itemPriceData->setPriceSourceID($this->subscriptionItemLink->getID());
        } elseif ($subscriptionDiscountPercent > 0) {
            $savingsPercent = $subscriptionDiscountPercent;
            $finalPrice = round($this->itemPriceData->price - ($this->itemPriceData->price * ($subscriptionDiscountPercent / 100)), 2);
            $savingsCurr = $this->itemPriceData->price - $finalPrice;
            //$this->itemPriceData->removeAllAppliedDiscounts($this->VATManager);
            //@TODO: TEST!!!
            $discObj = new GenericLineDiscount(GenericLineDiscount::DISCOUNT_SOURCE_TYPE_SUBSCRIPTION,$this->subscriptionItemLink->getID(),$savingsPercent);
            $this->itemPriceData->applyDiscount($discObj,$this->VATManager);

            $this->itemPriceData->setCrossPrice($origPrice);

            //$this->itemPriceData->setDiscountAmount($savingsCurr);
           // $this->itemPriceData->setPrice($finalPrice);
            $this->setSubscriptionSavingsAmount($savingsCurr);
            $this->setSubscriptionSavingsPercent($savingsPercent);
            $this->setDiscountStatusSubscriptionDiscountApplied();
            $this->itemPriceData->setDiscountSourceID($this->subscriptionItemLink->getID());
        }
		$this->itemPriceData->setPriceIncludesVAT($vatIncluded);
		$vatCode = $this->itemPriceData->getVATCode();		
		if(!empty($vatCode)) {
			$this->VATManager->doVATCalculationOnItemPrice($this->itemPriceData);
		}
    }


    public function setPriceSourceSubscriptionFixPrice()
    {
        $this->itemPriceData->setPriceSourceSubscriptionFixPrice();
        $this->priceSource = static::PRICE_SOURCE_SUBSCRIPTION_FIX_PRICE;
    }

    public function setPriceSourceSubscriptionPricePerBillingInterval()
    {
        $this->itemPriceData->setPriceSourceSubscriptionPricePerBillingInterval();
        $this->priceSource = static::PRICE_SOURCE_SUBSCRIPTION_PRICE_PER_BILLING_INTERVAL;
    }

    public function setDiscountStatusSubscriptionDiscountApplied()
    {
        $this->discountStatus = static::DISCOUNT_STATUS_SUBSCRIPTION_DISCOUNT_APPLIED;
    }

    /**
     * @param $amnt
     */
    public function setSubscriptionSavingsAmount($amnt) {
        $this->subscriptionSavingsAmnt = (float)$amnt;
    }

    /**
     * @param $percent
     */
    public function setSubscriptionSavingsPercent($percent) {
        $this->subscriptionSavingsPercent = (float)$percent;
    }

    /**
     * @return string
     */
    public function getSavingsPerUnitDesc() {

        $amntStr = $this->_formatPrice($this->subscriptionSavingsAmnt);

        if($this->itemPriceData->currencyCode === '' || $this->itemPriceData->currencyCode === 'EUR' || is_null($this->itemPriceData->currencyCode)) {
            return $amntStr . ' / ' . round($this->subscriptionSavingsPercent,2) . '%';
        } else {
            return $this->itemPriceData->currencyCode . ' ' . $amntStr . ' / ' . round($this->subscriptionSavingsPercent,2) . '%';
        }
    }

    /**
     * @param int $noOfTurns
     * @param int $itemQty
     * @return string
     */
    public function getSavingsTotalDesc($noOfTurns = 1, $itemQty = 1) {
        $multiplier = (int)$noOfTurns * (int)$itemQty;
        $totalWithoutSubscription = $this->initialPrice * $multiplier;
        $totalWithSubscription = $this->itemPriceData->price * $multiplier;
        $totalSavingsAmnt = $totalWithoutSubscription - $totalWithSubscription;
        $savingsPercent =  round((100 / ($totalWithoutSubscription / $totalSavingsAmnt)),2);

        $priceStr = $this->_formatPrice($totalSavingsAmnt);

        if($this->itemPriceData->currencyCode === '' || $this->itemPriceData->currencyCode === 'EUR' || is_null($this->itemPriceData->currencyCode)) {
            return $priceStr  . ' / ' . round($savingsPercent,2) . '%';
        } else {
            return $priceStr  . ' / ' . round($savingsPercent,2) . '%';
        }
    }

    /**
     * @param int $noOfTurns
     * @param int $itemQty
     * @return mixed
     */
    public function getSubscriptionPriceTotalDesc($noOfTurns = 1, $itemQty = 1) {
        $total = ((int)$noOfTurns * (int)$itemQty) * $this->itemPriceData->price;
        return $this->_formatPrice($total);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->{$name})) return $this->{$name};
        return $this->itemPriceData->{$name};
    }

    /**
     * @return mixed
     */
    public function getFormattedCustomerPrice() {
        return $this->_formatPrice($this->itemPriceData->price);
    }

    /**
     * @return mixed
     */
    public function getFormattedCrossPrice() {
        return $this->_formatPrice($this->itemPriceData->crossPrice);
    }

    /**
     * @return mixed
     */
    public function getFormattedSubscriptionSequenceTotal() {
        return $this->_formatPrice($this->subscriptionSequenceTotal);
    }

    /**
     * @param $desc
     */
    public function setSubscriptionPayIntervalDescription($desc) {
        $this->subscriptionPayIntervalDescription = strip_tags($desc);
    }

    /**
     * @return string
     */
    public function getSubscriptionPayIntervalDescription() {
        return $this->subscriptionPayIntervalDescription;
    }

    /**
     * @return SubscriptionHeader
     */
    public function getSubscriptionHeader() {
        return $this->subscriptionHeader;
    }

    /**
     * @return SubscriptionItemLink
     */
    public function getSubscriptionItemLink() {
        return $this->subscriptionItemLink;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->itemPriceData->setPrice($price);
    }

    /**
     * @return mixed
     */
    public function getCustomerPrice() {
        return $this->itemPriceData->getCustomerPrice();
    }

    /**
     * @return float
     */
    public function getCrossPrice() {
        if($this->getCustomerPrice() < $this->initialPrice) {
            return $this->initialPrice;
        } else {
            return $this->itemPriceData->getCrossPrice();
        }
    }

    /**
     * @return mixed
     */
    public function getItemVATProdPostingGroup() {
        return $this->itemPriceData->getItemVATProdPostingGroup();
    }


    /**
     * @param SubscriptionHeader $header
     * @param \DateTime $referenceDateTime
     * @return float
     */
    protected function getRemainingStepsFromDateForHeaderTotal(SubscriptionHeader $header, \DateTime $referenceDateTime) {
        if(!$header->typeIsSequenceItem()) {
            //echo "<!-- SUBSC SEQ TOTAL Premature return 1 -->";
            return 0.00;
        }

        $dateFormulaManagement = $this->navDateFormulaManagement;

        $timeStr = strtotime('midnight',$referenceDateTime->getTimestamp());

        $referenceDateTime = new \DateTime();
        $referenceDateTime->setTimestamp($timeStr);

        $pricePerBillingInterval = (float)$header->price_per_billing_interval;

        if($header->isPayTypeSinglePayment()) {
            //echo "<!-- SUBSC SEQ TOTAL Return price per billing interval -->";
            return $pricePerBillingInterval;
        }
        $remainingSteps = $this->seqStepRepository->getRemainingStepsFromDateForHeader($header,$referenceDateTime,$dateFormulaManagement);
        $firstStep = $remainingSteps->getFirst();
        $addOneToCount = false;





        /*echo "<!-- REMAINING STEPS: ";
        var_dump($remainingSteps);
        echo " -->";*/
        $noOfTurns = count($remainingSteps);
        if($header->isPayTypePerTurn()) {
            return (float)((int)$noOfTurns * $pricePerBillingInterval);
        }

        $previousDate = clone $referenceDateTime;
        $lastDate = clone $referenceDateTime;
        $finalIteration = count($remainingSteps) - 1;
        $firstDate = clone $referenceDateTime;
        if($header->startTypeIsJoin()) {
            $firstDate = \DateTime::createFromFormat('Y-m-d',$firstStep->fix_date);
            //echo "<!-- SETTING firstDate to " . $firstDate->format('d.m.Y') . " -->";
        } else {
            //echo "<!-- START TYPE != JOIN -->";
        }
        $i = 0;
        for($remainingSteps->rewind();$remainingSteps->isCurrValid();$remainingSteps->next()) {
            $currStep = $remainingSteps->current();
            $currDate = ($i === 0) ? new \DateTime() : $previousDate;
            $dateFormulaManagement->evaluateDateFormula($currStep->interval,$currDate);
            $dateFormulaManagement->mutateToClosestWorkDay($currDate);
            //if($i === 0) $firstDate = $firstDate ? $firstDate : $currDate;
            if($i === $finalIteration) $lastDate = $currDate;
            $previousDate = clone $currDate;
            ++$i;
        }

        $payIntervalDateFormula = $header->getPayTypeDateFormulaString();
        if(!$payIntervalDateFormula) {
            //echo "<!-- SUBSC SEQ TOTAL return no valid pay interval date formula -->";
            return 0.00;
        }

        //echo "<!-- PAY INTERVAL DATE FORMULA: $payIntervalDateFormula; -->";
        $noOfBillingIntervals = 1;
        $startNextBillingInterval = clone $referenceDateTime;
        $dateFormulaManagement->evaluateDateFormula($payIntervalDateFormula,$startNextBillingInterval);
        $startNextBillingIntervalReadable = $startNextBillingInterval->format('d.m.Y');
        $lastDateReadable = $lastDate->format('d.m.Y');
        //echo "<!-- START NEXT BILLING INTERVAL: $startNextBillingIntervalReadable - LAST DATE READABLE: $lastDateReadable -->";
        while($startNextBillingInterval->getTimestamp() <= $lastDate->getTimestamp()) {
            $startNextBillingIntervalReadable = $startNextBillingInterval->format('d.m.Y');
            //echo "<!-- START NEXT BILLING INTERVAL: $startNextBillingIntervalReadable -->";
            ++$noOfBillingIntervals;
            $dateFormulaManagement->evaluateDateFormula($payIntervalDateFormula,$startNextBillingInterval);
        }

        //echo "<!-- SUBSC SEQ TOTAL Returning noOfBillingIntervals ($noOfBillingIntervals) * pricePerBillingInterval ($pricePerBillingInterval) -->";
        return (float)($noOfBillingIntervals * $pricePerBillingInterval);
    }

    /**
     * @param GenericLineDiscount $discount
     * @param IVATManager $VATManager
     */
    public function applyDiscount(GenericLineDiscount $discount, IVATManager $VATManager)
    {
        $this->itemPriceData->applyDiscount($discount,$VATManager);
    }

    /**
     * @param GenericLineDiscount $discount
     * @param IVATManager $VATManager
     */
    public function removeDiscount(GenericLineDiscount $discount, IVATManager $VATManager)
    {
        $this->itemPriceData->removeDiscount($discount,$VATManager);
    }


    /**
     * @param IVATManager $VATManager
     */
    public function removeAllAppliedDiscounts(IVATManager $VATManager)
    {
        $this->itemPriceData->removeAllAppliedDiscounts($VATManager);
    }


    public function setPriceSourceRule()
    {
        $this->itemPriceData->setPriceSourceRule();
        $this->priceSource = ItemPriceData::PRICE_SOURCE_RULE;
    }

    /**
     * @return mixed
     */
    public function getPriceSourceType()
    {
        return $this->getPriceSource();
    }

    /**
     * @return mixed
     */
    public function getAppliedLineDiscounts()
    {
        return $this->itemPriceData->getAppliedLineDiscounts();
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->itemPriceData->getPrice();
    }

    /**
     * @return mixed
     */
    public function getUnitPrice()
    {
        return $this->itemPriceData->getUnitPrice();
    }

    /**
     * @return mixed
     */
    public function getQtySource()
    {
        return $this->itemPriceData->getQtySource();
    }

    public function setPriceSourceCoupon()
    {
        $this->itemPriceData->setPriceSourceCoupon();
    }

    public function setQtySourceUser()
    {
        $this->itemPriceData->setQtySourceUser();
    }

    public function setQtySourceSubscription()
    {
        $this->itemPriceData->setQtySourceSubscription();
    }

    public function setQtySourceCoupon()
    {
        $this->itemPriceData->setQtySourceCoupon();
    }

    public function setQtySourceRule()
    {
        $this->itemPriceData->setQtySourceRule();
    }

    /**
     * @return mixed
     */
    public function getQtySourceID()
    {
        return $this->itemPriceData->getQtySourceID();
    }

    /**
     * @param $qtySourceID
     */
    public function setQtySourceID($qtySourceID)
    {
        $this->itemPriceData->setQtySourceID($qtySourceID);
    }

    /**
     * @param $sourceType
     */
    public function setQtySourceType($sourceType)
    {
        $this->itemPriceData->setQtySourceType($sourceType);
    }

    /**
     * @param $sourceType
     */
    public function setPriceSourceType($sourceType)
    {
        throw new \DomainException('SubscriptionItemPriceDecorator is its own price source.');
    }

    /**
     * @return mixed
     */
    public function getQtySourceType()
    {
        return $this->itemPriceData->getQtySourceType();
    }

    /**
     * @param $sourceType
     * @return bool
     */
    public static function isAllowedPriceSourceType($sourceType)
    {
        return false;
    }

    /**
     * @param $sourceType
     * @return mixed
     */
    public static function isAllowedDiscountSourceType($sourceType)
    {
        return ItemPriceData::isAllowedDiscountSourceType($sourceType);
    }

    /**
     * @param GenericLineDiscount $discount
     * @return mixed
     */
    public function isDiscountApplied(GenericLineDiscount $discount)
    {
        return $this->itemPriceData->isDiscountApplied($discount);
    }

    /**
     * @param $sourceType
     * @return mixed
     */
    public static function isAllowedQtySourceType($sourceType)
    {
        return ItemPriceData::isAllowedQtySourceType($sourceType);
    }

    /**
     * @param $newQty
     */
    public function updateQuantityAndLineAmountWithoutUnitPrice($newQty)
    {
        $this->itemPriceData->updateQuantityAndLineAmountWithoutUnitPrice($newQty);
    }

    public function getCurrencyCode()
    {
        return $this->itemPriceData->getCurrencyCode();
    }


}