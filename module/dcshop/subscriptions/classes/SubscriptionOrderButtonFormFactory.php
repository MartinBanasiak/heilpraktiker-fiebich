<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\common\classes\FormBuilder;
use DynCom\dc\common\classes\NAVDateFormulaManagement;
use DynCom\dc\common\classes\Templating;
use DynCom\dc\dcShop\classes\AdvancedPriceProvider;
use DynCom\dc\dcShop\classes\CurrShopConfiguration;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/25/2015
 * Time: 9:31 AM
 */
class SubscriptionOrderButtonFormFactory
{

    const TC_ORDER_SUBSC_BUTTON = 'subsc_to_order';
    const TC_NO_OF_INTERVALS = 'subsc_no_of_intervals';
    const TC_INTERVAL = 'subsc_interval';

    /**
     * @var SubscriptionHeaderRepository
     */
    protected $headerRepository;
    /**
     * @var SubscriptionItemLinkRepository
     */
    protected $itemLinkRepository;
    /**
     * @var SubscriptionSequenceStepRepository
     */
    protected $sequenceStepRepository;
    /**
     * @var FormBuilder
     */
    protected $formBuilder;

    /**
     * @var Templating
     */
    protected $templating;

    /**
     * @var CurrShopConfiguration
     */
    protected $currConfig;

    /**
     * @var AdvancedPriceProvider
     */
    protected $advancedPriceProvider;
    /**
     * @var NAVDateFormulaManagement
     */
    private $navDateFormulaManagement;

    /**
     * @param SubscriptionHeaderRepository $headerRepository
     * @param SubscriptionItemLinkRepository $itemLinkRepository
     * @param SubscriptionSequenceStepRepository $sequenceStepRepository
     * @param Templating $templating
     * @param CurrShopConfiguration $currConfig
     * @param AdvancedPriceProvider $advancedPriceProvider
     * @param NAVDateFormulaManagement $navDateFormulaManagement
     */
    public function __construct(
            SubscriptionHeaderRepository $headerRepository,
            SubscriptionItemLinkRepository $itemLinkRepository,
            SubscriptionSequenceStepRepository $sequenceStepRepository,
            Templating $templating,
            CurrShopConfiguration $currConfig,
            AdvancedPriceProvider $advancedPriceProvider,
            NAVDateFormulaManagement $navDateFormulaManagement) {
        $this->headerRepository = $headerRepository;
        $this->itemLinkRepository = $itemLinkRepository;
        $this->sequenceStepRepository = $sequenceStepRepository;
        $this->templating = $templating;
        $this->currConfig = $currConfig;
        $this->advancedPriceProvider = $advancedPriceProvider;;
        $this->navDateFormulaManagement = $navDateFormulaManagement;
    }

    /**
     * @param WebshopItemInterface $item
     * @return string
     */
    public function getSubscriptionButtonHTMLForWebshopItem(WebshopItemInterface $item) {
        //check for Sequence Item
        $seqHeader = $this->headerRepository->getOrderableSequenceSubscriptionForWebshopItem($item);
        if($seqHeader->id > 0) {
            $form = $this->getSubscriptionSequenceItemOrderButtonFormForWebshopItem($item,$seqHeader,$this->sequenceStepRepository,$this->navDateFormulaManagement);
            $form->wrapInDIV('seq_subscr','subscription_button_wrapper');
            return $form->getRenderedContent();
        } else {
            //check for Order Option
            $optHeaderCollection = $this->headerRepository->getOrderableSubscriptionOrderOptionsForWebshopItem($item);
            $formHTML = '';
            foreach($optHeaderCollection as $optHeader) {
                if($optHeader->getID() > 0) {

                    $itemLinkCriteria = [
                        [
                            ['company', '=', $item->getCompany()],
                            ['subscription_code', '=', $optHeader->code],
                            ['link_to', '=', 0],
                            ['item_no', '=', $item->getItemNo()],
                            ['variant_code', '=', $item->getVariantCode()]
                        ]
                    ];
                    $result = $this->itemLinkRepository->findByCriteria($itemLinkCriteria);
                    if (!(count($result) === 1)) {
                        $formHTML .= '';
                        continue;
                    }
                    $form = $this->getSubscriptionOrderOptionButtonFormsForWebshopItem($item, $optHeader, $result->getFirst(),$this->sequenceStepRepository,$this->navDateFormulaManagement);
                    $form->wrapInDIV('order_subscr', 'subscription_button_wrapper');
                    $formHTML .= $form->getRenderedContent();
                }
            }
            return $formHTML;
        }
    }

    /**
     * @param WebshopItemInterface $item
     * @param SubscriptionHeader $header
     * @param SubscriptionItemLink $link
     * @param SubscriptionSequenceStepRepository $sequenceStepRepository
     * @param NAVDateFormulaManagement $navDateFormulaManagement
     * @return mixed
     */
    protected function getSubscriptionOrderOptionButtonFormsForWebshopItem(
            WebshopItemInterface $item,
            SubscriptionHeader $header,
            SubscriptionItemLink $link,
            SubscriptionSequenceStepRepository $sequenceStepRepository,
            NAVDateFormulaManagement $navDateFormulaManagement)
    {
        static $orderOptionFormCounter = 1;
        $formID = 'subsc_order_option_button_form_' . $orderOptionFormCounter;
        $fb = new FormBuilder($formID,['method' => 'post','action' => '?shop_category=subscription&action=step1','class' => 'order_button_form']);
        $normalPrice = $this->advancedPriceProvider->getItemCustomerPrice($item, 1, $this->currConfig->getCustomer(), $this->currConfig->getCurrencyCode(),);
        $subscriptionPrice = new SubscriptionItemPriceDecorator($normalPrice, $header, $sequenceStepRepository,$navDateFormulaManagement , $link);

        $minNoOfIntervals = $header->min_no_of_turns > 0 ? $header->min_no_of_turns : 1;
        $maxNoOfIntervals = $header->max_no_of_turns > 0 ? $header->max_no_of_turns : 99;

        $noOfIntervals = filter_input(INPUT_POST,'no_of_intervals',FILTER_SANITIZE_NUMBER_INT);
        $noOfIntervals = $noOfIntervals > 0 ? $noOfIntervals:$minNoOfIntervals;

        $minQty = 1;
        $maxQty = 99;

        $qty = filter_input(INPUT_POST,'subscription_item_qty',FILTER_SANITIZE_NUMBER_INT);
        $qty = ($qty > 0) ? $qty:$minQty;

        $textTop1 = $this->templating->getText('subscription_button_top_text_1');
        $textTop2 = $this->templating->getText('subscription_button_top_text_2');
        $unitText = $item->base_unit_of_measure;
        $intervalDescription = $header->turn_interval_description;
        $paymentTypeLabel = $this->templating->getText('subscription_payment_type_label');
        $paymentTypeDesc = $header->payment_type_description;
        $cancellationTermsLabel = $this->templating->getText('subscription_cancellation_terms_label');
        $cancellationTermsDesc = $header->cancel_period_description;
        $pricePerIntervalLabel = $this->templating->getText('subscription_price_per_interval_label');
        $pricePerIntervalDesc = (string)$subscriptionPrice;
        $priceTotalLabel = $this->templating->getText('subscription_price_total_label');
        $priceTotalDesc = $subscriptionPrice->getSubscriptionPriceTotalDesc($noOfIntervals,$qty);
        $savingsTotalLabel = $this->templating->getText('subscription_savings_total_label');
        $savingsTotalDesc = $subscriptionPrice->getSavingsTotalDesc($noOfIntervals,$qty);
        $pricePerUnitLabel = $this->templating->getText('subscription_price_per_unit_label');
        $pricePerUnitDesc = (string)$subscriptionPrice;
        $savingsPerUnitLabel = $this->templating->getText('subscription_savings_per_unit_label');
        $savingsPerUnitDesc = $subscriptionPrice->getSavingsPerUnitDesc();
        $sendButtonLabel = $this->templating->getText('subscription_send_button');
        $turnDescLabel = $this->templating->getText('turn');
        $turns = $this->templating->getText('turns');
        $qtyLabel = $this->templating->getText('qty_label');
        $durationLabel = $this->templating->getText('duration');


        $fb->addHidden(
            'subscription_header_id', $header->getID());
        $fb->addHidden(
            'subscription_item_id', $item->getID());
        $fb->addHidden(
            'subscription_item_no', $item->getItemNo());
        $fb->addHidden(
            'subscription_item_link_id', $link->getID());
        $fb->addHidden(
            'subscription_init_price', $subscriptionPrice->initialPrice
        );
        $fb->addHidden(
            'subscription_unit_price', $subscriptionPrice->unitPrice
        );
        $fb->addDIV(
            'itemcard_subscription_order_inner_text_top_' . $orderOptionFormCounter,
            'itemcard_subscription_order_inner_text_top');
        $fb->addDIV(
            'itemcard_subscription_order_inner_text_top_1_' . $orderOptionFormCounter,
            'itemcard_subscription_order_inner_text_top_1',
            'itemcard_subscription_order_inner_text_top_' . $orderOptionFormCounter,
            $textTop1);
        $fb->addDIV(
            'itemcard_subscription_order_inner_text_top_2_' . $orderOptionFormCounter,
            'itemcard_subscription_order_inner_text_top_2',
            'itemcard_subscription_order_inner_text_top_' . $orderOptionFormCounter,
            $textTop2);
        $fb->addDIV(
            'itemcard_subscription_order_inner_qty_' . $orderOptionFormCounter,
            'itemcard_subscription_order_inner_qty');
        $fb->addDIV(
            'itemcard_subscription_order_inner_qty_' . $orderOptionFormCounter . '_label',
            'itemcard_subscription_order_inner_qty_label',
            'itemcard_subscription_order_inner_qty_' . $orderOptionFormCounter,
            $qtyLabel);
        $fb->addDIV(
            'itemcard_subscription_order_inner_qty_' . $orderOptionFormCounter . '_fields',
            'itemcard_subscription_order_inner_qty_fields',
            'itemcard_subscription_order_inner_qty_' . $orderOptionFormCounter);
        $fb->addNumberInput(
            'subscription_item_qty',
            'subscription_item_qty_' . $orderOptionFormCounter,
            'input_number',
            'itemcard_subscription_order_inner_qty_' . $orderOptionFormCounter . '_fields',
            $qty,
            null,
            ['min' => $minQty,'max' => $maxQty, 'value' => (string)$minQty]);
        $fb->addDIV(
            'itemcard_subscription_order_inner_unit_text_' . $orderOptionFormCounter,
            'itemcard_subscription_order_inner_unit_text',
            'itemcard_subscription_order_inner_qty_' . $orderOptionFormCounter . '_fields',
            $unitText);
        $fb->addDIV(
            'itemcard_subscription_order_inner_turn_description_' . $orderOptionFormCounter,
            'itemcard_subscription_order_inner_turn_description');
        $fb->addDIV(
            'itemcard_subscription_order_turn_description_text_left_' . $orderOptionFormCounter,
            'itemcard_subscription_order_turn_description_text_left',
            'itemcard_subscription_order_inner_turn_description_' . $orderOptionFormCounter,
            $turnDescLabel);
        $fb->addDIV(
            'itemcard_subscription_order_turn_description_text_right_' . $orderOptionFormCounter,
            'itemcard_subscription_order_turn_description_text_right',
            'itemcard_subscription_order_inner_turn_description_' . $orderOptionFormCounter,
            $intervalDescription);
        $fb->addDIV(
            'itemcard_subscription_order_inner_no_of_turns_select_' . $orderOptionFormCounter,
            'itemcard_subscription_order_inner_no_of_turns_select');
        $fb->addDIV(
            'itemcard_subscription_order_inner_no_of_turns_select_' . $orderOptionFormCounter . '_label',
            'itemcard_subscription_order_inner_no_of_turns_select_label',
            'itemcard_subscription_order_inner_no_of_turns_select_' . $orderOptionFormCounter,
            $durationLabel);
        $fb->addDIV(
            'itemcard_subscription_order_inner_no_of_turns_select_' . $orderOptionFormCounter . '_fields',
            'itemcard_subscription_order_inner_no_of_turns_select_fields',
            'itemcard_subscription_order_inner_no_of_turns_select_' . $orderOptionFormCounter);
        $fb->addNumberInput(
            'no_of_intervals',
            'no_of_intervals_' . $orderOptionFormCounter,
            'input_number',
            'itemcard_subscription_order_inner_no_of_turns_select_' . $orderOptionFormCounter . '_fields',
            $noOfIntervals,
            null,
            ['min' => $minNoOfIntervals,'max' => $maxNoOfIntervals]);
        $fb->addDIV(
            'itemcard_subscription_order_inner_interval_description_' . $orderOptionFormCounter,
            'itemcard_subscription_order_inner_interval_description',
            'itemcard_subscription_order_inner_no_of_turns_select_' . $orderOptionFormCounter . '_fields');
        $fb->addSpan(
            null,
            null,
            'itemcard_subscription_order_inner_interval_description_' . $orderOptionFormCounter,
            '');
        $fb->addSpan(
            null,
            null,
            'itemcard_subscription_order_inner_interval_description_' . $orderOptionFormCounter,
            $turns);
        $fb->addDIV(
            'itemcard_subscription_order_inner_order_button_wrapper_' . $orderOptionFormCounter,
            'itemcard_subscription_order_inner_order_button_wrapper');
        $fb->addField(FormBuilder::ELEMENT_SUBMIT,array('id' => 'itemcard_subscription_order_inner_order_button_' . $orderOptionFormCounter,'class' => 'itemcard_subscription_order_inner_order_button subscription_button order_button','value' => $sendButtonLabel),'itemcard_subscription_order_inner_order_button_wrapper_' . $orderOptionFormCounter);
        $fb->addDIV(
            'itemcard_subscription_order_inner_payment_terms_' . $orderOptionFormCounter,
            'itemcard_subscription_order_inner_payment_terms');
        $fb->addSpan(
            'itemcard_subscription_order_payment_type_text_left_' . $orderOptionFormCounter,
            'itemcard_subscription_order_payment_types_text_left',
            'itemcard_subscription_order_inner_payment_terms_' . $orderOptionFormCounter,
            $paymentTypeLabel);
        $fb->addSpan(
            'itemcard_subscription_order_payment_type_text_right_' . $orderOptionFormCounter,
            'itemcard_subscription_order_payment_type_text_right',
            'itemcard_subscription_order_inner_payment_terms_' . $orderOptionFormCounter,
            $paymentTypeDesc);
        $fb->addDIV(
            'itemcard_subscription_order_inner_cancellation_terms_' . $orderOptionFormCounter,
            'itemcard_subscription_order_inner_cancellation_terms');
        $fb->addSpan(
            'itemcard_subscription_order_cancellation_terms_text_left_' . $orderOptionFormCounter,
            'itemcard_subscription_order_cancellation_terms_text_left',
            'itemcard_subscription_order_inner_cancellation_terms_' . $orderOptionFormCounter,
            $cancellationTermsLabel);
        $fb->addSpan(
            'itemcard_subscription_order_cancellation_terms_text_right_' . $orderOptionFormCounter,
            'itemcard_subscription_order_cancellation_terms_text_right',
            'itemcard_subscription_order_inner_cancellation_terms_' . $orderOptionFormCounter,
            $cancellationTermsDesc);
        $fb->addDIV(
            'itemcard_subscription_order_inner_price_info_' . $orderOptionFormCounter,
            'itemcard_subscription_order_inner_price_info');
        $fb->addDIV(
            'itemcard_subscription_order_price_per_interval_' . $orderOptionFormCounter,
            'itemcard_subscription_order_price_per_interval',
            'itemcard_subscription_order_inner_price_info_' . $orderOptionFormCounter);
        $fb->addSpan(
            'itemcard_subscription_order_price_per_interval_text_left_' . $orderOptionFormCounter,
            'itemcard_subscription_order_price_per_interval_text_left',
            'itemcard_subscription_order_price_per_interval_' . $orderOptionFormCounter,
            $pricePerIntervalLabel);
        $fb->addSpan(
            'itemcard_subscription_order_price_per_interval_text_right_' . $orderOptionFormCounter,
            'itemcard_subscription_order_price_per_interval_text_right',
            'itemcard_subscription_order_price_per_interval_' . $orderOptionFormCounter,
            $pricePerIntervalDesc);
        $fb->addDIV(
            'itemcard_subscription_order_price_total_' . $orderOptionFormCounter,
            'itemcard_subscription_order_price_total',
            'itemcard_subscription_order_inner_price_info_' . $orderOptionFormCounter);
        $fb->addSpan(
            'itemcard_subscription_order_price_total_text_left_' . $orderOptionFormCounter,
            'itemcard_subscription_order_price_total_text_left',
            'itemcard_subscription_order_price_total_' . $orderOptionFormCounter,
            $priceTotalLabel);
        $fb->addSpan(
            'itemcard_subscription_order_price_total_text_right_' . $orderOptionFormCounter,
            'itemcard_subscription_order_price_total_text_right',
            'itemcard_subscription_order_price_total_' . $orderOptionFormCounter,
            $priceTotalDesc);
        $fb->addDIV(
            'itemcard_subscription_order_price_per_unit_' . $orderOptionFormCounter,
            'itemcard_subscription_order_price_per_unit',
            'itemcard_subscription_order_inner_price_info_' . $orderOptionFormCounter);
        $fb->addSpan(
            'itemcard_subscription_order_price_per_unit_text_left_' . $orderOptionFormCounter,
            'itemcard_subscription_order_price_per_unit_text_left',
            'itemcard_subscription_order_price_per_unit_' . $orderOptionFormCounter,
            $pricePerUnitLabel);
        $fb->addSpan(
            'itemcard_subscription_order_price_per_unit_text_right_' . $orderOptionFormCounter,
            'itemcard_subscription_order_price_per_unit_text_right',
            'itemcard_subscription_order_price_per_unit_' . $orderOptionFormCounter,
            $pricePerUnitDesc);
        $fb->addDIV(
            'itemcard_subscription_order_savings_per_unit_' . $orderOptionFormCounter,
            'itemcard_subscription_order_savings_total',
            'itemcard_subscription_order_inner_price_info_' . $orderOptionFormCounter);
        $fb->addSpan(
            'itemcard_subscription_order_savings_per_unit_text_left_' . $orderOptionFormCounter,
            'itemcard_subscription_order_savings_per_unit_text_left',
            'itemcard_subscription_order_savings_per_unit_' . $orderOptionFormCounter,
            $savingsPerUnitLabel);
        $fb->addSpan(
            'itemcard_subscription_order_savings_per_unit_text_right_' . $orderOptionFormCounter,
            'itemcard_subscription_order_savings_per_unit_text_right',
            'itemcard_subscription_order_savings_per_unit_' . $orderOptionFormCounter,
            $savingsPerUnitDesc);
        $fb->addDIV(
            'itemcard_subscription_order_savings_total_' . $orderOptionFormCounter,
            'itemcard_subscription_order_savings_total',
            'itemcard_subscription_order_inner_price_info_' . $orderOptionFormCounter);
        $fb->addSpan(
            'itemcard_subscription_order_savings_total_text_left_' . $orderOptionFormCounter,
            'itemcard_subscription_order_savings_total_text_left',
            'itemcard_subscription_order_savings_total_' . $orderOptionFormCounter,
            $savingsTotalLabel);
        $fb->addSpan(
            'itemcard_subscription_order_savings_total_text_right_' . $orderOptionFormCounter,
            'itemcard_subscription_order_savings_total_text_right',
            'itemcard_subscription_order_savings_total_' . $orderOptionFormCounter,
            $savingsTotalDesc);

        $validation = [
            'subscription_item_qty' => 'digit|minnum:1|maxnum:99|required',
            'no_of_intervals' => "digit|minnum:$minNoOfIntervals|maxnum:$maxNoOfIntervals|required"
        ];

        $fb->setFieldRulesWithRuleString($validation);

        return $fb->getForm();
    }

    /**
     * @param WebshopItemInterface $item
     * @param SubscriptionHeader $header
     * @param SubscriptionSequenceStepRepository $sequenceStepRepository
     * @param NAVDateFormulaManagement $navDateFormulaManagement
     * @return mixed
     */
    protected function getSubscriptionSequenceItemOrderButtonFormForWebshopItem(WebshopItemInterface $item, SubscriptionHeader $header, SubscriptionSequenceStepRepository $sequenceStepRepository, NAVDateFormulaManagement $navDateFormulaManagement) {
        $formID = 'subsc_order_option_button_form';
        $fb = new FormBuilder($formID,['method' => 'post','action' => '?shop_category=subscription&action=step1','class' => 'order_button_form']);
        $normalPrice = $this->advancedPriceProvider->getItemCustomerPrice($item, 1, $this->currConfig->getCustomer(), $this->currConfig->getCurrencyCode(),);
        $subscriptionPrice = new SubscriptionItemPriceDecorator($normalPrice, $header, $sequenceStepRepository,$navDateFormulaManagement);

        $textTop1 = $this->templating->getText('subscription_button_top_text_1');
        $textTop2 = $this->templating->getText('subscription_button_top_text_2');
        $qty = filter_input(INPUT_POST,'subscription_item_qty',FILTER_SANITIZE_NUMBER_INT);
        $qty = ($qty > 0) ? $qty:1;
        $unitText = $item->base_unit_of_measure;
        $paymentTypeLabel = $this->templating->getText('subscription_payment_type_label');
        $paymentTypeDesc = $header->payment_type_description;
        $cancellationTermsLabel = $this->templating->getText('subscription_cancellation_terms_label');
        $cancellationTermsDesc = $header->cancel_period_description;
        $pricePerIntervalLabel = $this->templating->getText('subscription_price_per_interval_label');
        $pricePerIntervalDesc = (string)$subscriptionPrice;
        $priceTotalLabel = $this->templating->getText('subscription_price_total_label');
        $priceTotalDesc = $subscriptionPrice->getSubscriptionPriceTotalDesc($header->no_of_sequence_steps,$qty);

        $minQty = 1;
        $maxQty = 99;
        $sendButtonLabel = $this->templating->getText('subscription_send_button');


        $fb->addHidden(
            'subscription_header_id', $header->getID());
        $fb->addHidden(
            'subscription_item_id', $item->getID());
        $fb->addHidden(
            'subscription_item_no', $item->getItemNo());
        $fb->addHidden(
            'subscription_init_price', $subscriptionPrice->initialPrice
        );
        $fb->addHidden(
            'subscription_unit_price', $subscriptionPrice->unitPrice
        );
        $fb->addDIV(
            'itemcard_subscription_order_inner_text_top',
            'itemcard_subscription_order_inner_text_top');
        $fb->addDIV(
            'itemcard_subscription_order_inner_text_top_1',
            'itemcard_subscription_order_inner_text_top_1',
            'itemcard_subscription_order_inner_text_top',
            $textTop1);
        $fb->addDIV(
            'itemcard_subscription_order_inner_text_top_2',
            'itemcard_subscription_order_inner_text_top_2',
            'itemcard_subscription_order_inner_text_top',
            $textTop2);
        $fb->addDIV(
            'itemcard_subscription_order_inner_qty',
            'itemcard_subscription_order_inner_qty');
        $fb->addNumberInput(
            'subscription_item_qty',
            'subscription_item_qty',
            'input_number',
            'itemcard_subscription_order_inner_qty',
            $qty,
            null,
            ['min' => $minQty,'max' => $maxQty]);
        $fb->addDIV(
            'itemcard_subscription_order_inner_unit_text',
            'itemcard_subscription_order_inner_unit_text',
            'itemcard_subscription_order_inner_qty',
            $unitText);
        $fb->addDIV(
            'itemcard_subscription_order_inner_order_button_wrapper',
            'itemcard_subscription_order_inner_order_button_wrapper');
        $fb->addField(FormBuilder::ELEMENT_SUBMIT,array('id' => 'itemcard_subscription_order_inner_order_button','class' => 'itemcard_subscription_order_inner_order_button subscription_button order_button','value' => $sendButtonLabel),'itemcard_subscription_order_inner_order_button_wrapper');
        $fb->addDIV(
            'itemcard_subscription_order_inner_payment_terms',
            'itemcard_subscription_order_inner_payment_terms');
        $fb->addSpan(
            'itemcard_subscription_order_payment_type_text_left',
            'itemcard_subscription_order_payment_types_text_left',
            'itemcard_subscription_order_inner_payment_terms',
            $paymentTypeLabel);
        $fb->addSpan(
            'itemcard_subscription_order_payment_type_text_right',
            'itemcard_subscription_order_payment_type_text_right',
            'itemcard_subscription_order_inner_payment_terms',
            $paymentTypeDesc);
        $fb->addDIV(
            'itemcard_subscription_order_inner_cancellation_terms',
            'itemcard_subscription_order_inner_cancellation_terms');
        $fb->addSpan(
            'itemcard_subscription_order_cancellation_terms_text_left',
            'itemcard_subscription_order_cancellation_terms_text_left',
            'itemcard_subscription_order_inner_cancellation_terms',
            $cancellationTermsLabel);
        $fb->addSpan(
            'itemcard_subscription_order_cancellation_terms_text_right',
            'itemcard_subscription_order_cancellation_terms_text_right',
            'itemcard_subscription_order_inner_cancellation_terms',
            $cancellationTermsDesc);
        $fb->addDIV(
            'itemcard_subscription_order_inner_price_info',
            'itemcard_subscription_order_inner_price_info');
        $fb->addDIV(
            'itemcard_subscription_order_price_per_interval',
            'itemcard_subscription_order_price_per_interval',
            'itemcard_subscription_order_inner_price_info');
        $fb->addSpan(
            'itemcard_subscription_order_price_per_interval_text_left',
            'itemcard_subscription_order_price_per_interval_text_left',
            'itemcard_subscription_order_price_per_interval',
            $pricePerIntervalLabel);
        $fb->addSpan(
            'itemcard_subscription_order_price_per_interval_text_right',
            'itemcard_subscription_order_price_per_interval_text_right',
            'itemcard_subscription_order_price_per_interval',
            $pricePerIntervalDesc);
        $fb->addDIV(
            'itemcard_subscription_order_price_total',
            'itemcard_subscription_order_price_total',
            'itemcard_subscription_order_inner_price_info');
        $fb->addSpan(
            'itemcard_subscription_order_price_total_text_left',
            'itemcard_subscription_order_price_total_text_left',
            'itemcard_subscription_order_price_total',
            $priceTotalLabel);
        $fb->addSpan(
            'itemcard_subscription_order_price_total_text_right',
            'itemcard_subscription_order_price_total_text_right',
            'itemcard_subscription_order_price_total',
            $priceTotalDesc);


        $validation = [
            'subscription_item_qty' => 'digit|minnum:1|maxnum:99|required'
        ];

        $fb->setFieldRulesWithRuleString($validation);

        return $fb->getForm();
    }

}