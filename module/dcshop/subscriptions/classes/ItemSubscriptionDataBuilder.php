<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\common\classes\NAVDateFormulaManagement;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\dcShop\classes\AdvancedPriceProvider;
use DynCom\dc\dcShop\classes\CurrShopConfiguration;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 02.07.2015
 * Time: 14:32
 */
class ItemSubscriptionDataBuilder
{

    /**
     * @var SubscriptionHeaderRepository
     */
    protected $headerRepository;

    /**
     * @var SubscriptionSequenceStepRepository
     */
    protected $sequenceStepRepository;

    /**
     * @var SubscriptionItemLinkRepository
     */
    protected $itemLinkRepository;

    /**
     * @var CriteriaHelperInterface
     */
    protected $criteriaHelper;

    /**
     * @var AdvancedPriceProvider
     */
    protected $advancedPriceProvider;

    /**
     * @var CurrShopConfiguration
     */
    protected $currShopConfiguration;
    /**
     * @var NAVDateFormulaManagement
     */
    private $navDateFormulaManagement;

    /**
     * @param SubscriptionHeaderRepository $headerRepository
     * @param SubscriptionSequenceStepRepository $sequenceStepRepository
     * @param SubscriptionItemLinkRepository $itemLinkRepository
     * @param CriteriaHelperInterface $criteriaHelper
     * @param AdvancedPriceProvider $advancedPriceProvider
     * @param NAVDateFormulaManagement $navDateFormulaManagement
     * @param CurrShopConfiguration $currShopConfiguration
     */
    public function __construct(
            SubscriptionHeaderRepository $headerRepository,
            SubscriptionSequenceStepRepository $sequenceStepRepository,
            SubscriptionItemLinkRepository $itemLinkRepository,
            CriteriaHelperInterface $criteriaHelper,
            AdvancedPriceProvider $advancedPriceProvider,
            NAVDateFormulaManagement $navDateFormulaManagement,
            CurrShopConfiguration $currShopConfiguration) {
        $this->headerRepository = $headerRepository;
        $this->sequenceStepRepository = $sequenceStepRepository;
        $this->itemLinkRepository = $itemLinkRepository;
        $this->criteriaHelper = $criteriaHelper;
        $this->advancedPriceProvider = $advancedPriceProvider;
        $this->currShopConfiguration = $currShopConfiguration;
        $this->navDateFormulaManagement = $navDateFormulaManagement;
    }

    /**
     * @param WebshopItemInterface $webshopItem
     * @return ItemSubscriptionData
     */
    public function getItemSubscriptionData(WebshopItemInterface $webshopItem) {
        $subscriptionsActive = (bool)(getenv('ACTIVE_MODULE_SUBSCRIPTIONS') ?? '1');
        if (!$subscriptionsActive) {
            return new ItemSubscriptionData(
                $webshopItem,
                new SubscriptionHeaderCollection(
                    new SubscriptionHeaderConfig(),
                    $this->criteriaHelper),
                new SubscriptionSequenceStepCollection(
                    new SubscriptionSequenceStepConfig(),
                    $this->criteriaHelper),
                [],
                []
            );
        }

        $header = $this->headerRepository->getOrderableSequenceSubscriptionForWebshopItem($webshopItem);
        $seqSteps = $this->sequenceStepRepository->getAllForHeader($header);
        if(!($header->getID() > 0)) {
            $headers = $this->headerRepository->getOrderableSubscriptionOrderOptionsForWebshopItem($webshopItem);
        } else {
            $headers = new SubscriptionHeaderCollection($this->headerRepository->getConfig(),$this->criteriaHelper);
            $headers->add($header);
        }
        $itemLinks = [];
        $itemPriceData = [];
        $initItemPriceData = $this->advancedPriceProvider->getItemCustomerPrice($webshopItem, 1, $this->currShopConfiguration->getCustomer(), $this->currShopConfiguration->getCurrencyCode(),false);

        for($headers->rewind();$headers->valid();$headers->next()) {
            $currHeader = $headers->current();
            if ($currHeader->getID() !== null) {
                $itemLink = $this->itemLinkRepository->getItemLinkForOrderOption($currHeader, $webshopItem);
                $itemLinks[$currHeader->code] = $itemLink;
                $currSubscItemPriceData = new SubscriptionItemPriceDecorator(
                    clone $initItemPriceData,
                    $currHeader,
                    $this->sequenceStepRepository,
                    $this->navDateFormulaManagement,
                    $this->advancedPriceProvider->getVATManager(),
                    $itemLink
                );
                $itemPriceData[$currHeader->code] = $currSubscItemPriceData;
            }
        }
        return new ItemSubscriptionData($webshopItem,$headers,$seqSteps,$itemLinks,$itemPriceData);
    }


}