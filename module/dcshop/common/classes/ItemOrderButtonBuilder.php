<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\FormBuilder;
use DynCom\dc\common\classes\TemplateInserterFactory;
use DynCom\dc\common\classes\Templating;
use DynCom\dc\common\classes\Validator;
use DynCom\dc\dcShop\abstracts\DiscountBase;
use DynCom\dc\dcShop\interfaces\ItemAvailabilityProvider;
use DynCom\dc\dcShop\interfaces\ItemPriceDataInterface;
use DynCom\dc\dcShop\interfaces\IVATManager;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;
use DynCom\dc\dcShop\subscriptions\classes\ItemSubscriptionData;
use DynCom\dc\dcShop\subscriptions\classes\SubscriptionHeader;
use DynCom\dc\dcShop\subscriptions\classes\SubscriptionHeaderCollection;
use DynCom\dc\dcShop\subscriptions\classes\SubscriptionItemPriceDataInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 02.07.2015
 * Time: 11:48
 */
class ItemOrderButtonBuilder
{

    protected const HTTP_SCHEMA_ORG_OUT_OF_STOCK = "http://schema.org/OutOfStock";
    protected const HTTP_SCHEMA_ORG_LIMITED_AVAILABILITY = "http://schema.org/LimitedAvailability";
    protected const HTTP_SCHEMA_ORG_IN_STOCK = "http://schema.org/InStock";
    protected const HTTP_SCHEMA_ORG_PRE_ORDER = "http://schema.org/PreOrder";

    /**
     * @var CurrShopConfiguration
     */
    protected $shopConfiguration;

    /**
     * @var WebshopItem
     */
    protected $webshopItem;

    /**
     * @var ItemPriceData
     */
    protected $itemPriceData;

    /**
     * @var ItemAvailabilityProvider
     */
    protected $itemAvailabilityProvider;

    /**
     * @var ItemSubscriptionData
     */
    protected $itemSubscriptionData;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var FormBuilder
     */
    protected $formBuilder;

    /**
     * @var Templating
     */
    protected $templating;

    /**
     * @var TemplateInserterFactory
     */
    protected $templateInserterFactory;

    /**
     * @var IVATManager
     */
    protected $vatManager;

    /**
     * @var \DOMDocument
     */
    protected $doc;

    /**
     * @bool
     */
    protected $displayInventory;

    /**
     * ItemOrderButtonBuilder constructor.
     * @param CurrShopConfiguration $shopConfiguration
     * @param ItemAvailabilityProvider $availabilityProvider
     * @param Validator $validator
     * @param FormBuilder $formBuilder
     * @param Templating $templating
     * @param IVATManager $vatManager
     */
    public function __construct(
        CurrShopConfiguration $shopConfiguration,
        ItemAvailabilityProvider $availabilityProvider,
        Validator $validator,
        FormBuilder $formBuilder,
        Templating $templating,
        IVATManager $vatManager)
    {
        $this->shopConfiguration = $shopConfiguration;
        $this->itemAvailabilityProvider = $availabilityProvider;
        $this->validator = $validator;
        $this->formBuilder = $formBuilder;
        $this->templating = $templating;
        $this->vatManager = $vatManager;
        $DOMImpl = new \DOMImplementation();
        $this->doc = $DOMImpl->createDocument(null, 'html',
            $DOMImpl->createDocumentType("html",
                "-//W3C//DTD XHTML 1.0 Transitional//EN",
                "/dc/common/xhtml11.dtd"));
        $this->doc->formatOutput = true;
    }

    /**
     * @param WebshopItemInterface $item
     * @param ItemPriceData $priceData
     * @param ItemSubscriptionData $subscriptionData
     * @param $displayInventory
     * @return mixed|string
     */
    public function getItemcardButtonHTML(WebshopItemInterface $item, ItemPriceData $priceData,ItemSubscriptionData $subscriptionData,$displayInventory)
    {
        $this->webshopItem = $item;
        $this->itemPriceData = $priceData;
        $this->itemSubscriptionData = $subscriptionData;
        $this->displayInventory = (bool)$displayInventory;
        //$output = $this->getOrderButtonCSSLinkHTML();
        $output = $this->getOrderButtonJSLinkHTML();
        $hasSubscData = $this->itemSubscriptionData->hasData();
        if ($hasSubscData) {
            if ($this->itemSubscriptionData->isTypeOrderOption()) $output .= $this->getCombinedSimpleAndSubscriptionButtonHTML();
            elseif ($this->itemSubscriptionData->isTypeSequenceItem()) $output .= $this->getSubscriptionButtonHTML();
            else $output .= $this->getSimpleButtonHTML();
        } elseif ($this->webshopItem->getCustomizationStatus() === 1) {
            $output .= $this->getCombinedSimpleAndCustomizationButtonHTML();
        } elseif ($this->webshopItem->getCustomizationStatus() === 2) {
            $output .= $this->getCustomizationButtonHTML();
        }else {
            $output .= $this->getSimpleButtonHTML();
        }
        return $output;
    }

    /**
     * @return \DOMElement
     */
    protected function getSimpleButtonDOM()
    {
        $doc = $this->doc;

        $scriptLink = $doc->createElement('script');
        $scriptLink->appendChild(new \DOMAttr('src', '/module/dcshop/common/scripts/itemcard_order_button.js'));
        $scriptLink->appendChild(new \DOMAttr('type', 'text/javascript'));
        $doc->appendChild($scriptLink);

        $wrapper = $innerDIV = $doc->appendChild($doc->createElement('div'));
        $innerDIV->appendChild(new \DOMAttr('class', 'itemcard_order_button_form_std_outer itemcard_order_button_form_outer'));
        $innerDIV->appendChild(new \DOMAttr('id', 'itemcard_order_button_form_std_outer'));

        $actionID = (int)$this->webshopItem->getID();

        $itemLink = create_item_link_from_id_and_slug($this->webshopItem->getID(),$this->webshopItem->item_slug,$_GET['shop_category'] ?? '');
        $formOptions = [
            'method' => 'post',
            'action' => $itemLink . 'queue/?action=shop_add_item_to_basket_card',
            'name' => 'simple_order_button_form'
        ];
        $fb = $this->formBuilder;
        $fb->reset('itemcard_order_button_form_std', $formOptions);
        $fb->addHidden('item_id', $this->webshopItem->getID());
        $fb->addHidden('item_var_code', $this->webshopItem->getVariantCode());

        $formNode = $fb->getMainDOMNode();

        $importedNode = $doc->importNode($formNode, true);
        $setNode = $innerDIV->appendChild($importedNode);
        $vatAndShipTarget = $setNode;
        //if (!$this->shopConfiguration->isShopB2B()) {
            $priceWrapper = $this->appendPriceDataToNode($setNode, $this->itemPriceData, false);
            $vatAndShipTarget = $priceWrapper;
        //}
        $this->appendVATAndShipTextToDOMNode($vatAndShipTarget, $this->shopConfiguration->isShopB2B());
        $active = (bool)$this->itemAvailabilityProvider->isItemOrderable($this->webshopItem);
        if ($this->displayInventory) {
            $this->appendAvailabilityDisplay($setNode);
        }
        $this->appendPricePerGramDisplay($setNode);
        $this->appendOrderButtonToDOMNode($setNode, 'item_qty', $this->templating->getText('add_to_basket'), $active);


        return $wrapper;
    }

    /**
     * @return string
     */
    protected function getSimpleButtonHTML()
    {
        $el = $this->getSimpleButtonDOM();
        $simpleClassAttr = $el->attributes->getNamedItem('class');
        $simpleClassAttr->nodeValue .= ' itemcard_order_button_active';
        return $el->ownerDocument->saveXML($el);
    }

    /**
     * @return string
     */
    protected function getSubscriptionButtonHTML()
    {
        $el = $this->getSubscriptionButtonDOM();
        $subscClassAttr = $el->attributes->getNamedItem('class');
        $subscClassAttr->nodeValue .= ' itemcard_order_button_active';
        return $el->ownerDocument->saveXML($el);
    }

    /**
     * @return string
     */
    protected function getCombinedSimpleAndSubscriptionButtonHTML()
    {
        $el = $this->getCombinedSimpleAndSubscriptionButtonDOM();
        return $el->ownerDocument->saveXML();
    }

    /**
     * @return \DOMNode
     */
    protected function getCombinedSimpleAndSubscriptionButtonDOM()
    {

        $simpleButtonEL = $this->getSimpleButtonDOM();
        //Define Radio Button to prepend to simple order button
        $radioText = $this->templating->getText('itemcard_order_button_radio_single_shipment');
        $radioButtonWrapper = $simpleButtonEL->ownerDocument->createElement('div');
        $radioButtonWrapper->appendChild(new \DOMAttr('id', 'itemcard_order_button_std_radio_wrapper_outer'));
        $radioButtonWrapper->appendChild(new \DOMAttr('class', 'itemcard_order_button_radio_wrapper_outer'));
        $radioButtonWrapper->setIdAttribute('id', true);
        //Append Radio Button as first visible element (before hidden input)
        $wrapper = $simpleButtonEL->firstChild->firstChild->insertBefore($radioButtonWrapper);
        $this->appendRadioButtonToDOMNode($wrapper, 'itemcard_order_type_choice', $radioText);

        $subscriptionButtonDOMNode = $this->getSubscriptionButtonDOM();

        //Wrap both
        $doc = $this->doc;
        $newDocWrapper = $doc->createElement('div');
        $newDocWrapper->appendChild(new \DOMAttr('id', 'itemcard_order_buttons'));
        $newDocWrapper->setIdAttribute('id', false);
        $newDocWrapper = $doc->appendChild($newDocWrapper);
        $simple = $newDocWrapper->appendChild($simpleButtonEL);
        $subsc = $newDocWrapper->appendChild($subscriptionButtonDOMNode);

        //Set active / inactive classes
        $simpleClassAttr = $simple->attributes->getNamedItem('class');
        $simpleClassAttr->nodeValue .= ' itemcard_order_button_active';
        $subscClassAttr = $subsc->attributes->getNamedItem('class');
        $subscClassAttr->nodeValue .= ' itemcard_order_button_inactive';

        //set select disabled
        $select = $subsc->ownerDocument->getElementsByTagName('select')->item(0);
        if ($select) {
            $select->appendChild(new \DOMAttr('disabled', 'disabled'));
            //Make Clickable
            $classAttr = $select->attributes->getNamedItem('class');
            if (null === $classAttr) {
                $select->appendchild(new \DOMAttr('class'));
            }
            $oldNodeValue = $select->attributes->getNamedItem('class')->nodeValue;
            $newNodeValue = $oldNodeValue . ' itemcard_select_clickable';
            $select->attributes->getNamedItem('class')->nodeValue = $newNodeValue;
        }


        return $doc->firstChild;
    }

    /**
     * @return string
     */
    protected function getCustomizationButtonHTML()
    {
        $el = $this->getCustomizationButtonDOM();
        return $el->ownerDocument->saveXML();
    }

    /**
     * @return \DOMNode
     */
    protected function getCustomizationButtonDOM()
    {

        $individualizationButtonDOMNode = $this->getIndividualizationButtonDOM();
        $doc = $this->doc;
        $cust = $doc->appendChild($individualizationButtonDOMNode);

        $custClassAttr = $cust->attributes->getNamedItem('class');
        $custClassAttr->nodeValue .= ' itemcard_order_button_active';

        return $doc->firstChild;
    }

    /**
     * @return string
     */
    protected function getCombinedSimpleAndCustomizationButtonHTML()
    {
        $el = $this->getCombinedSimpleAndCustomizationButtonDOM();
        return $el->ownerDocument->saveXML();
    }

    /**
     * @return \DOMNode
     */
    protected function getCombinedSimpleAndCustomizationButtonDOM()
    {

        $simpleButtonEL = $this->getSimpleButtonDOM();
        //Define Radio Button to prepend to simple order button
        $radioText = $this->templating->getText('order_without_individualization');
        $radioButtonWrapper = $simpleButtonEL->ownerDocument->createElement('div');
        $radioButtonWrapper->appendChild(new \DOMAttr('id', 'itemcard_order_button_std_radio_wrapper_outer'));
        $radioButtonWrapper->appendChild(new \DOMAttr('class', 'itemcard_order_button_radio_wrapper_outer'));
        $radioButtonWrapper->setIdAttribute('id', true);
        //Append Radio Button as first visible element (before hidden input)
        $wrapper = $simpleButtonEL->firstChild->firstChild->insertBefore($radioButtonWrapper);
        $this->appendRadioButtonToDOMNode($wrapper, 'itemcard_order_type_choice', $radioText);

        $individualizationButtonDOMNode = $this->getIndividualizationButtonDOM();

        //Wrap both
        $doc = $this->doc;
        $newDocWrapper = $doc->createElement('div');
        $newDocWrapper->appendChild(new \DOMAttr('id', 'itemcard_order_buttons'));
        $newDocWrapper->setIdAttribute('id', false);
        $newDocWrapper = $doc->appendChild($newDocWrapper);
        $simple = $newDocWrapper->appendChild($simpleButtonEL);
        $cust = $newDocWrapper->appendChild($individualizationButtonDOMNode);

        //Set active / inactive classes
        $simpleClassAttr = $simple->attributes->getNamedItem('class');
        $simpleClassAttr->nodeValue .= ' itemcard_order_button_active';
        $custClassAttr = $cust->attributes->getNamedItem('class');
        $custClassAttr->nodeValue .= ' itemcard_order_button_inactive';

        //set select disabled
        $select = $cust->ownerDocument->getElementsByTagName('select')->item(0);
        if ($select) {
            $select->appendChild(new \DOMAttr('disabled', 'disabled'));
            //Make Clickable
            $classAttr = $select->attributes->getNamedItem('class');
            if (null === $classAttr) {
                $select->appendchild(new \DOMAttr('class'));
            }
            $oldNodeValue = $select->attributes->getNamedItem('class')->nodeValue;
            $newNodeValue = $oldNodeValue . ' itemcard_select_clickable';
            $select->attributes->getNamedItem('class')->nodeValue = $newNodeValue;
        }


        return $doc->firstChild;
    }

    /**
     * @return \DOMNode
     */
    protected function getSubscriptionButtonDOM()
    {
        static $i = 1;
        $doc = $this->doc;
        $subscriptionHeaders = $this->itemSubscriptionData->getSubscriptionHeaders();
        $firstHeader = $subscriptionHeaders->getFirst();
        $classAppend = $firstHeader->typeIsSequenceItem() ? ' itemcard_order_button_sequence_item' : '';

        $cssLink = $doc->createElement('link');
        $cssLink->appendChild(new \DOMAttr('type', 'text/css'));
        $cssLink->appendChild(new \DOMAttr('rel', 'stylesheet'));
        $cssLink->appendChild(new \DOMAttr('href', '/module/dcshop/subscriptions/styles/itemcard_subscription_form.css'));
        $doc->appendChild($cssLink);

        $scriptLink = $doc->createElement('script');
        $scriptLink->appendChild(new \DOMAttr('src', '/module/dcshop/subscriptions/scripts/itemcard_subscription.js'));
        $scriptLink->appendChild(new \DOMAttr('type', 'text/javascript'));
        $doc->appendChild($scriptLink);

        $innerDIV = $doc->appendChild(new \DOMElement('div'));
        $innerDIV->appendChild(new \DOMAttr('class', 'itemcard_order_button_form_subsc_outer itemcard_order_button_form_outer' . $classAppend));
        $innerDIV->appendChild(new \DOMAttr('id', 'itemcard_order_button_form_subsc_outer'));

        $formOptions = [
            'method' => 'post',
            'action' => "/".customizeUrl().'/order/address_select/',
            'name' => 'subscription_order_button'
        ];
        $fb = $this->formBuilder;
        $fb->reset('itemcard_order_button_form_subsc', $formOptions);
        $fb->addHidden('subscription_item_id', $this->webshopItem->getID());
        $fb->addHidden('subscription_item_var_code', $this->webshopItem->getVariantCode());
        $radioWrapper = $fb->addDIV('itemcard_order_button_subsc_radio_wrapper_outer_' . $i, 'itemcard_order_button_radio_wrapper_outer')->getEl();
        $radioWrapper->appendChild(new \DOMText(''));

        $radioText = $this->templating->getText('itemcard_order_button_radio_as_subscription');
        $this->appendRadioButtonToDOMNode($radioWrapper, 'itemcard_order_type_choice', $radioText);

        $node = $fb->getMainDOMNode();
        $imported = $doc->importNode($node, true);
        $imported = $innerDIV->appendChild($imported);

        $firstHeaderCode = $firstHeader->code;
        $firstHeaderIsSeqItem = $firstHeader->typeIsSequenceItem();
        $this->appendSubscriptionHeaderInputsToDOMNode($imported,$subscriptionHeaders);
        //$selectWrapper = $this->appendSubscriptionChoiceWithSavingsToDOMNode($imported);
        $isSequenceItem = $this->itemSubscriptionData->isTypeSequenceItem();
        $hasSteps = false;

        $subscriptionPriceData = $this->itemSubscriptionData->getSubscriptionPriceData($firstHeaderCode);
        $isAvailable = true;
        if ($firstHeader->typeIsSequenceItem()) {
            $payIntDescription = $this->getSubscriptionPayIntervalDescription($firstHeader, $this->templating);
            $subscriptionPriceData->setSubscriptionPayIntervalDescription($payIntDescription);
            $hasSteps = count($this->itemSubscriptionData->getSequenceSteps()) > 0;
        }
        $priceWrapper = $this->appendPriceDataToNode($imported, $subscriptionPriceData, false);
        if ($firstHeader->typeIsSequenceItem() && !$firstHeader->isPayTypeSinglePayment()) {
            $this->appendTotalSubscriptionSequencePriceToDOMNode($priceWrapper, $subscriptionPriceData);
        }

        $this->appendVATAndShipTextToDOMNode($imported, $firstHeaderIsSeqItem);
        $active = (!$isSequenceItem || $hasSteps) && $this->itemAvailabilityProvider->isItemOrderable($this->webshopItem);
        $this->appendOrderButtonToDOMNode($imported, 'subscription_item_qty', $this->templating->getText('Abo bestellen.'), $active);


        ++$i;
        return $innerDIV;
    }

    protected function appendSubscriptionHeaderInputsToDOMNode(\DOMNode $node,SubscriptionHeaderCollection $collection)
    {
        $count = count($collection);
        if (1 === $count) {
            $firstHeader = $collection->getFirst();
            $hiddenInput = new \DOMElement('input');
            $newNode = $node->appendChild($hiddenInput);
            $hiddenInput->appendChild(new \DOMAttr('name','subscription_header_id'));
            $hiddenInput->appendChild(new \DOMAttr('value',$firstHeader->getID()));
            $hiddenInput->setAttribute('type','hidden');
        } else {
            $newNode = $this->appendSubscriptionChoiceWithSavingsToDOMNode($node);
        }
        return $newNode;
    }

    /**
     * @param WebshopItemInterface $item
     * @param ItemPriceData $priceData
     * @param ItemSubscriptionData $subscriptionData
     * @param $iteration
     * @param $displayInventory
     * @return string
     */
    public function getItemlistButtonHTML(WebshopItemInterface $item, ItemPriceData $priceData, ItemSubscriptionData $subscriptionData, $iteration, $displayInventory)
    {
        $this->webshopItem = $item;
        $this->itemPriceData = $priceData;
        $this->itemSubscriptionData = $subscriptionData;
        $this->displayInventory = (bool)$displayInventory;
        $iteration = (int)$iteration;

        $hasSubscData = $this->itemSubscriptionData->hasData();
        if ($this->webshopItem->getCustomizationStatus() !== 0 || ($hasSubscData && ($this->itemSubscriptionData->isTypeOrderOption() || $this->itemSubscriptionData->isTypeSequenceItem()))) {
            //return '<div class="itemlist_content basket_with_price"></div>';
            $el = $this->getItemlistButtonDOMWithoutBasketButton($iteration);
            $html = $el->ownerDocument->saveXML($el);
            return $html;
        }

        $el = $this->getItemlistButtonDOM($iteration);
        $html = $el->ownerDocument->saveXML($el);
        return $html;
    }




    protected function getItemlistButtonDOM($iteration)
    {
        $doc = $this->doc;

        $wrapper = $innerDIV = $doc->appendChild($doc->createElement('div'));
        $innerDIV->appendChild(new \DOMAttr('class', 'itemlist_content basket_with_price'));

        //$priceWrapper = $this->appendPriceDataToNode($innerDIV, $this->itemPriceData, false);
        $active = (bool)$this->itemAvailabilityProvider->isItemOrderable($this->webshopItem);
        if ($this->displayInventory) {
            $this->appendAvailabilityDisplay($innerDIV);
        }
        $this->appendPricePerGramDisplay($innerDIV);
        $buttonWrapper = $this->appendListOrderButtonToDOMNode($innerDIV,$iteration,$active,$this->webshopItem->getMinQty(),$this->webshopItem->getMaxQty(),$this->webshopItem->getQtyStep());

        $this->appendFavoriteSwitchToDomNode($innerDIV,$this->webshopItem->getID(),$this->shopConfiguration->getVisitorID());

        return $wrapper;

    }

    protected function getItemlistButtonDOMWithoutBasketButton($iteration)
    {
        $doc = $this->doc;

        $wrapper = $innerDIV = $doc->appendChild($doc->createElement('div'));
        $innerDIV->appendChild(new \DOMAttr('class', 'itemlist_content basket_with_price'));

        //$priceWrapper = $this->appendPriceDataToNode($innerDIV, $this->itemPriceData, false);
        $active = (bool)$this->itemAvailabilityProvider->isItemOrderable($this->webshopItem);
        if ($this->displayInventory) {
            $this->appendAvailabilityDisplay($innerDIV);
        }
        $this->appendPricePerGramDisplay($innerDIV);
        //$buttonWrapper = $this->appendListOrderButtonToDOMNode($innerDIV,$iteration,$active,$this->webshopItem->getMinQty(),$this->webshopItem->getMaxQty(),$this->webshopItem->getQtyStep());

        $this->appendFavoriteSwitchToDomNode($innerDIV,$this->webshopItem->getID(),$this->shopConfiguration->getVisitorID());

        return $wrapper;

    }

    /**
     * @param \DOMNode $node
     * @param $name
     * @param $text
     */
    protected function appendRadioButtonToDOMNode(\DOMNode $node, $name, $text)
    {
        static $i = 1;
        $radioButtonSpan = $node->appendChild(new \DOMElement('span'));
        $radioButtonSpan->appendChild(new \DOMAttr('class', 'itemcard_order_button_radio_wrapper_inner'));
        $radioTextSpan = $node->appendChild(new \DOMElement('span'));
        $radioTextSpan->appendChild(new \DOMAttr('class', 'itemcard_order_button_radio_text'));
        $radioTextLabel = $radioTextSpan->appendChild(new \DOMElement('label'));
        $radioTextLabel->appendChild(new \DOMAttr('class','form-check-label'));
        $radioTextLabel->appendChild(new \DOMAttr('for', $name . '_' . $i));
        $radioTextLabel->appendChild(new \DOMText($text));
        $radioButton = $radioTextLabel->appendChild(new \DOMElement('input'));
        $radioButton->appendChild(new \DOMAttr('type', 'radio'));
        $radioButton->appendChild(new \DOMAttr('name', $name));
        $radioButton->appendChild(new \DOMAttr('id', $name . '_' . $i));

        if ($i === 1) $radioButton->appendChild(new \DOMAttr('checked', 'true'));
        ++$i;
    }

    /**
     * @return string
     */
    protected function getIndividualizationButtonWithRadioHTML()
    {
        $el = $this->getIndividualizationButtonWithRadioDOM();
        return $el->ownerDocument->saveXML();
    }

    /**
     * @return \DOMNode
     */
    protected function getIndividualizationButtonWithRadioDOM()
    {

        $simpleButtonEL = $this->getSimpleButtonWithoutPriceDOM();
        //Define Radio Button for individualized order
        $radioText = $this->templating->getText('order_individualized');
        $radioButtonWrapper = $simpleButtonEL->ownerDocument->createElement('div');
        $radioButtonWrapper->appendChild(new \DOMAttr('id', 'itemcard_radio_wrapper_2'));
        $radioButtonWrapper->setIdAttribute('id', true);

        //Define Radio Button for std order
        $radioTextStd = $this->templating->getText('order_without_individualization');
        $radioButtonWrapperStd = $simpleButtonEL->ownerDocument->createElement('div');
        $radioButtonWrapperStd->appendChild(new \DOMAttr('id', 'itemcard_radio_wrapper_1'));
        $radioButtonWrapperStd->setIdAttribute('id', true);

        $individualizationButtonDOMNode = $this->getIndividualizationButtonDOM();

        //Wrap both
        $doc = $this->doc;
        $newDocWrapper = $doc->createElement('div');
        $newDocWrapper->appendChild(new \DOMAttr('id', 'itemcard_order_buttons'));
        $newDocWrapper->setIdAttribute('id', false);
        $newDocWrapper = $doc->appendChild($newDocWrapper);

        $wrapperStd = $newDocWrapper->appendChild($radioButtonWrapperStd);

        $this->appendRadioButtonToDOMNode($wrapperStd, 'itemcard_order_type_choice', $radioTextStd);
        $priceWrapperStd = $this->appendPriceDataToNode($wrapperStd, $this->itemPriceData, false);

        $wrapper = $newDocWrapper->appendChild($radioButtonWrapper);

        $this->appendRadioButtonToDOMNode($wrapper, 'itemcard_order_type_choice', $radioText);
        $priceWrapper = $this->appendPriceDataToNode($wrapper, $this->itemPriceData, true);

        $simple = $newDocWrapper->appendChild($simpleButtonEL);
        $indiv = $newDocWrapper->appendChild($individualizationButtonDOMNode);

        //Set active / inactive classes
        $simpleClassAttr = $simple->attributes->getNamedItem('class');
        $simpleClassAttr->nodeValue .= ' itemcard_order_button_active';
        $subscClassAttr = $indiv->attributes->getNamedItem('class');
        $subscClassAttr->nodeValue .= ' itemcard_order_button_inactive';

        //set select disabled
        $select = $indiv->ownerDocument->getElementsByTagName('select')->item(1);
        if ($select) {
            $select->appendChild(new \DOMAttr('disabled', 'disabled'));
            //Make Clickable
            $select->attributes->getNamedItem('class')->nodeValue .= ' itemcard_select_clickable';
        }

        return $doc->firstChild;

    }

    /**
     * @return \DOMNode
     */
    protected function getSimpleButtonWithoutPriceDOM()
    {

        $doc = $this->doc;
        $wrapper = $innerDIV = $doc->appendChild($doc->createElement('div'));
        $innerDIV->appendChild(new \DOMAttr('class', 'itemcard_order_button_form_std_outer itemcard_order_button_form_outer'));
        $innerDIV->appendChild(new \DOMAttr('id', 'itemcard_order_button_form_std_outer'));

        $item_id = $this->webshopItem->getID();
        $itemLink = create_item_link_from_id_and_slug($this->webshopItem->getID(),$this->webshopItem->item_slug,$_GET['shop_category'] ?? '');
        $formOptions = [
            'method' => 'post',
            //'action' => '?action=add_to_basket',
            'action' => $itemLink . 'queue/?action=shop_add_item_to_basket_card&action_id=' . $item_id .'',
            'name' => 'simple_order_button_form'
        ];
        $fb = $this->formBuilder;
        $fb->reset('itemcard_order_button_form_std', $formOptions);
        $fb->addHidden('item_id', $this->webshopItem->getID());
        $fb->addHidden('item_var_code', $this->webshopItem->getVariantCode());

        $formNode = $fb->getMainDOMNode();

        $importedNode = $doc->importNode($formNode, true);
        $setNode = $innerDIV->appendChild($importedNode);
        //$priceWrapper = $this->appendPriceDataToNode($setNode, $this->itemPriceData);

        $this->appendVATAndShipTextToDOMNode($setNode, $this->shopConfiguration->isShopB2B());
        if ($this->displayInventory) {
            $this->appendAvailabilityDisplay($setNode);
        }
        $this->appendPricePerGramDisplay($setNode);

        $this->appendOrderButtonToDOMNode($setNode, 'item_qty', $this->templating->getText('add_to_basket'));


        //$this->appendPledgeTextToDOMNode($setNode);

        return $wrapper;
    }

    /**
     * @return \DOMNode
     */
    protected function getIndividualizationButtonDOM()
    {
        static $i = 1;
        $doc = $this->doc;

        $innerDIV = $doc->appendChild(new \DOMElement('div'));
        $innerDIV->appendChild(new \DOMAttr('class', 'itemcard_order_button_form_subsc_outer itemcard_order_button_form_outer'));
        $innerDIV->appendChild(new \DOMAttr('id', 'itemcard_order_button_form_subsc_outer'));
        $innerDIV->appendChild(new \DOMAttr('data-customizable', true));

        $item_id = $this->webshopItem->getID();

        $itemLink = create_item_link_from_id_and_slug($this->webshopItem->getID(),$this->webshopItem->item_slug,$_GET['shop_category'] ?? '');
        $formOptions = [
            'method' => 'post',
            //'action' => '?action=add_to_basket',
            'action' => $itemLink . 'queue/?action=shop_individualize_item_card',
            'name' => 'indiv_order_button_form',
            'data-customized'=>true
        ];
        $fb = $this->formBuilder;
        $fb->reset('itemcard_order_button_form_subsc', $formOptions);
        $fb->addHidden('customized', true);
        $fb->addHidden('action_id', $this->webshopItem->getID());
        $fb->addHidden('indiv_item_id', $this->webshopItem->getID());
        $fb->addHidden('indiv_item_var_code', $this->webshopItem->getVariantCode());
        if ($this->webshopItem->getCustomizationStatus() !== 2) {
            $radioWrapper = $fb->addDIV('itemcard_order_button_subsc_radio_wrapper_outer_' . $i, 'itemcard_order_button_radio_wrapper_outer')->getEl();
            $radioWrapper->appendChild(new \DOMText(''));
            $radioText = $this->templating->getText('order_individualized');
            $this->appendRadioButtonToDOMNode($radioWrapper, 'itemcard_order_type_choice', $radioText);
        }

        $node = $fb->getMainDOMNode();
        $imported = $doc->importNode($node, true);
        $setNode = $innerDIV->appendChild($imported);
        $priceWrapper = $this->appendPriceDataToNode($setNode, $this->itemPriceData, true);
        $this->appendVATAndShipTextToDOMNode($setNode, $this->shopConfiguration->isShopB2B());

        $active = (bool)$this->itemAvailabilityProvider->isItemOrderable($this->webshopItem);

        if ($this->displayInventory) {
            $this->appendAvailabilityDisplay($setNode);
        }

        $this->appendPricePerGramDisplay($setNode);
        $this->appendOrderButtonToDOMNode($setNode, 'item_qty', $this->templating->getText('individualize'),$active);
        $orderButtonNode = $setNode->lastChild;
        $simpleClassAttr = $orderButtonNode->attributes->getNamedItem('class');
        $simpleClassAttr->nodeValue .= ' itemcard_order_button_inactive';

        return $innerDIV;

    }

    /**
     * @param \DOMNode $node
     * @param $name
     * @param $text
     * @param bool $active
     * @param float $min
     * @param float $max
     * @param float $step
     * @return \DOMNode
     */
    protected function appendOrderButtonToDOMNode(\DOMNode $node, $name, $text, $active = true, $min = null, $max = null, $step = null)
    {
        static $i = 0;

        $min = $min ? (float)$min : (float)$this->webshopItem->getMinQty();
        $max = $max ? (float)$max : (float)$this->webshopItem->getMaxQty();
        $step = $step ? (float)$step : (float)$this->webshopItem->getQtyStep();
        $active = (bool)$active;

        $displaySwitchClassString = $active ? ' display_switch_block_itemcard_order_button_active active_call_to_action_wrapper active_order_button_wrapper' : 'display_switch_bock_itemcard_order_button_inactive inactive_call_to_action_wrapper inactive_order_button_wrapper';
        $submitButtonActivitySwitchClassString = $active ? ' itemcard_order_submit_button_active' : ' itemcard_order_submit_button_inactive';

        $wrapperOuterDIV = $node->appendChild(new \DOMElement('div'));
        $wrapperOuterDIV->appendChild(new \DOMAttr('class', 'itemcard_order_button_input_wrapper_outer' . ' ' . $name . '_input_wrapper_outer' . $displaySwitchClassString));


        $wrapperInnerDIV = $wrapperOuterDIV->appendChild(new \DOMElement('div'));
        $wrapperInnerDIV->appendChild(new \DOMAttr('class', 'itemcard_order_button_input_wrapper_inner' . ' ' . $name . '_input_wrapper_inner'));

        $buttonNode = $wrapperInnerDIV->appendChild(new \DOMElement('input'));
        $buttonNode->appendChild(new \DOMAttr('type', 'number'));
        $buttonNode->appendChild(new \DOMAttr('min', $min));
        $buttonNode->appendChild(new \DOMAttr('max', $max));
        $buttonNode->appendChild(new \DOMAttr('step', $step));
        $buttonNode->appendChild(new \DOMAttr('name', $name));
        $buttonNode->appendChild(new \DOMAttr('id', $name));
        $buttonNode->appendChild(new \DOMAttr('value', $min));
        if (!$active) {
            $buttonNode->appendChild(new \DOMAttr('disabled', 'disabled'));
        }

        $spinnerWrapper = $wrapperInnerDIV->appendChild(new \DOMElement('div'));
        $spinnerWrapper->appendChild(new \DOMAttr('class', 'spinner_wrapper'));
        $spinnerUp = $spinnerWrapper->appendChild(new \DOMElement('div'));
        $spinnerUp->appendChild(new \DOMAttr('class', 'spinner_up'));
        $spinnerUpIcon = $spinnerUp->appendChild(new \DOMElement('i'));
        $spinnerUpIcon->appendChild(new \DOMAttr('class', 'fa fa-caret-up'));

        if (!$active) {
            $spinnerUp->appendChild(new \DOMAttr('data-disabled','disabled'));
        }

        $spinnerDown = $spinnerWrapper->appendChild(new \DOMElement('div'));
        $spinnerDown->appendChild(new \DOMAttr('class', 'spinner_down'));
        $spinnerDownIcon = $spinnerDown->appendChild(new \DOMElement('i'));
        $spinnerDownIcon->appendChild(new \DOMAttr('class', 'fa fa-caret-down'));

        if (!$active) {
            $spinnerDown->appendChild(new \DOMAttr('data-disabled','disabled'));
        }

        $wrapperText = $wrapperOuterDIV->appendChild(new \DOMElement('div'));
        $wrapperText->appendChild(new \DOMAttr('class', 'itemcard_order_button_wrapper_text' . ' ' . $name . '_wrapper_text'));

        $wrappingButton = $wrapperText->appendChild(new \DOMElement('button'));
        $wrappingButton->appendChild(new \DOMAttr('type', 'submit'));
        $wrappingButton->appendChild(new \DOMAttr('class', 'itemcard_order_submit_button button'));
        $wrappingButton->appendChild(new \DOMAttr('class', 'itemcard_order_submit_button' . $submitButtonActivitySwitchClassString));
        if (!$active) {
            $wrappingButton->appendChild(new \DOMAttr('disabled', 'disabled'));
        }

        $wrapperTextSpan = $wrappingButton->appendChild(new \DOMElement('span'));
        $wrapperTextSpan->appendChild(new \DOMText($text));

        $wrapperTextI = $wrappingButton->appendChild(new \DOMElement('i'));
        $wrapperTextI->appendChild(new \DOMAttr('class', 'fa fa-shopping-cart'));

        ++$i;
        return $wrapperOuterDIV;
    }

    /**
     * @param \DOMNode $node
     * @param bool $active
     * @param float $min
     * @param float $max
     * @param float $step
     * @return \DOMNode
     */
    protected function appendListOrderButtonToDOMNode(\DOMNode $node, $iteration, $active = true, $min = null, $max = null, $step = null)
    {
        static $i = 0;

        $min = $min ? (float)$min : (float)$this->webshopItem->getMinQty();
        $max = $max ? (float)$max : (float)$this->webshopItem->getMaxQty();
        $step = $step ? (float)$step : (float)$this->webshopItem->getQtyStep();
        $iteration = (int)$iteration;
        $active = (bool)$active;

        $wrapperActivitySwitchClassString = $active ? ' itemlist_qty_wrapper_active' : ' itemlist_qty_wrapper_inactive';
        $qtyInputActivitySwitchClassString = $active ? ' itemlist_qty_input_active' : ' itemlist_qty_input_inactive';

        $displaySwitchClassString = 'display_switch_block_itemlist_order_button_active active_call_to_action_wrapper active_order_button_wrapper';
        $submitButtonActivitySwitchClassString = $active ? ' itemcard_order_submit_button_active' : ' itemcard_order_submit_button_inactive';


        $wrapperOuterDIV = $node->appendChild(new \DOMElement('div'));
        $wrapperOuterDIV->appendChild(new \DOMAttr('class', 'basket_button' . $wrapperActivitySwitchClassString));


        $wrapperInnerDIV = $wrapperOuterDIV->appendChild(new \DOMElement('div'));
        $wrapperInnerDIV->appendChild(new \DOMAttr('class', 'quantity' . $qtyInputActivitySwitchClassString));

        $itemIDInputNode = $wrapperInnerDIV->appendChild(new \DOMElement('input'));
        $itemIDInputNode->appendChild(new \DOMAttr('type','hidden'));
        $itemIDInputNode->appendChild(new \DOMAttr('name','input_item_id_' . $iteration));
        $itemIDInputNode->appendChild(new \DOMAttr('class','input_item_id'));
        $itemIDInputNode->appendChild(new \DOMAttr('id','input_item_id_' . $iteration));
        $itemIDInputNode->appendChild(new \DOMAttr('value',$this->webshopItem->getID()));

        $itemVarCodeInputNode = $wrapperInnerDIV->appendChild(new \DOMElement('input'));
        $itemVarCodeInputNode->appendChild(new \DOMAttr('type','hidden'));
        $itemVarCodeInputNode->appendChild(new \DOMAttr('name','input_variant_code_' . $iteration));
        $itemVarCodeInputNode->appendChild(new \DOMAttr('class','input_variant_code'));
        $itemVarCodeInputNode->appendChild(new \DOMAttr('id','input_variant_code_' . $iteration));
        $itemVarCodeInputNode->appendChild(new \DOMAttr('value',$this->webshopItem->getVariantCode()));

        $buttonNode = $wrapperInnerDIV->appendChild(new \DOMElement('input'));
        $buttonNode->appendChild(new \DOMAttr('type', 'number'));
        $buttonNode->appendChild(new \DOMAttr('min', $min));
        $buttonNode->appendChild(new \DOMAttr('max', $max));
        $buttonNode->appendChild(new \DOMAttr('step', $step));
        $buttonNode->appendChild(new \DOMAttr('name', 'input_item_quantity_' . $iteration));
        $buttonNode->appendChild(new \DOMAttr('id', 'input_item_quantity_' . $iteration));
        $buttonNode->appendChild(new \DOMAttr('class', 'input_item_quantity_value'));
        $value = 0;
        if ($this->shopConfiguration->isShopB2C()) {
            $value = 1;
        }
        $buttonNode->appendChild(new \DOMAttr('value',$value));
        if (!$active) {
            $buttonNode->appendChild(new \DOMAttr('disabled','disabled'));
        }

        $spinnerWrapper = $wrapperInnerDIV->appendChild(new \DOMElement('div'));
        $spinnerWrapper->appendChild(new \DOMAttr('class', 'spinner_wrapper'));
        $spinnerUp = $spinnerWrapper->appendChild(new \DOMElement('div'));
        $spinnerUp->appendChild(new \DOMAttr('class', 'spinner_up'));
        $spinnerUpIcon = $spinnerUp->appendChild(new \DOMElement('i'));
        $spinnerUpIcon->appendChild(new \DOMAttr('class', 'fa fa-caret-up'));

        if (!$active) {
            $spinnerUp->appendChild(new \DOMAttr('data-disabled','disabled'));
        }


        $spinnerDown = $spinnerWrapper->appendChild(new \DOMElement('div'));
        $spinnerDown->appendChild(new \DOMAttr('class', 'spinner_down'));
        $spinnerDownIcon = $spinnerDown->appendChild(new \DOMElement('i'));
        $spinnerDownIcon->appendChild(new \DOMAttr('class', 'fa fa-caret-down'));

        if (!$active) {
            $spinnerDown->appendChild(new \DOMAttr('data-disabled','disabled'));
        }


        $basketButtonLinkNode = $wrapperOuterDIV->appendChild(new \DOMElement('a'));
        $basketButtonLinkNode->appendChild(new \DOMAttr('class','itemlist_order_button_link'));
        if (!$active) {
            $basketButtonLinkNode->appendChild(new \DOMAttr('data-disabled', 'disabled'));
        }
        $basketButtonLinkNode->appendChild(new \DOMAttr('href','#'));


        //$scriptNode = $wrapperOuterDIV->appendChild(new \DOMElement('script'));
        //$text = '$(\'.itemlist_order_button_link\').on(\'click\',function(e) { e.preventDefault(); e.stopPropagation(); var itemLink = $(this).find(".itemlist2").attr("data-itemlink"); formAction = itemlink + "&shop_category=queue&action=shop_add_item_to_basket_card"; $(this).closest(\'form\').attr("action",formAction); $(this).closest(\'form\').submit(); });';
        //$scriptNode->appendChild(new \DOMText($text));

        $buttonINode = $basketButtonLinkNode->appendChild(new \DOMElement('i'));
        $buttonINode->appendChild(new \DOMAttr('class','fa fa-cart-plus'));
        $buttonINode->appendChild(new \DOMAttr('aria-hidden','true'));

        return $wrapperOuterDIV;
    }

    protected function appendFavoriteSwitchToDomNode(\DOMNode $node,$itemID, $visitorID)
    {
        $itemID = (int)$itemID;
        $visitorID = (int)$visitorID;
        $isItemVisitorFavorite = is_item_visitor_favorite($itemID,$visitorID);
        if ($this->shopConfiguration->isShopB2C()) {
            $text = $isItemVisitorFavorite ? $this->templating->getText('favorit_delete') : $this->templating->getText('favorit_add');
        } else {
            $text = $isItemVisitorFavorite ? $this->templating->getText('favorit_delete_b2b') : $this->templating->getText('favorit_add_b2b');
        }
        $actualURLPartsQuery=dropAllActionItemsOnUrlQuery();
        $href = '?';
        if ($actualURLPartsQuery !== '') {
            $href .= $actualURLPartsQuery . '&';
        }
        $href .= $isItemVisitorFavorite ? 'action=shop_remove_item_from_favorites&action_id=' . $itemID : 'action=shop_add_item_to_favorites&action_id=' . $itemID ;
        $iNodeClass = $isItemVisitorFavorite ? 'fa fa-star' : 'fa fa-star-o';

        $wrapper = $node->appendChild(new \DOMElement('div'));
        $wrapper->appendChild(new \DOMAttr('class','itemlist_content favorites'));

        $textNode = $wrapper->appendChild(new \DOMElement('div'));
        $textNode->appendChild(new \DOMAttr('class','itemlist_content_label'));
        $textNode->appendChild(new \DOMText($this->templating->getText('favorites')));

        $aNode = $wrapper->appendChild(new \DOMElement('a'));
        //$aNode->appendChild(new \DOMAttr('title',$text));
        $aNode->appendChild(new \DOMAttr('class','favorite-button'));
        $aNode->appendChild(new \DOMAttr('href',$href));
        $aNode->appendChild(new \DOMText($text));

        $iNode = $aNode->appendChild(new \DOMElement('i'));
        $iNode->appendChild(new \DOMAttr('class',$iNodeClass));
        $iNode->appendChild(new \DOMAttr('aria-hidden','true'));

        return $wrapper;
    }


    /**
     * @param \DOMNode $node
     * @param ItemPriceDataInterface $sourcePriceData
     * @param bool $is_customize
     * @return \DOMNode
     */
    protected function appendPriceDataToNode(\DOMNode $node, ItemPriceDataInterface $sourcePriceData, $is_customize = false)
    {
        static $i = 0;
        //$displayNoneclassestring = ($i > 0) ? ' display_none' : '';
        $displayNoneClassString = '';
        $crossPriceString = '';
        if (!$is_customize) {
            $crossPriceString = ((float)$sourcePriceData->crossPrice > 0) ? $sourcePriceData->getFormattedCrossPrice() : '';
        }
        $priceWrapperOuter = $node->appendChild(new \DOMElement('div'));

        if (!$this->webshopItem->getCustomizationStatus()) {
            $priceWrapperOuter->appendChild(new \DOMAttr('class', 'itemcard_order_button_price_wrapper_outer' . $displayNoneClassString));
        }


        $discounts = $this->itemPriceData->getAppliedLineDiscounts();
        if ($crossPriceString !== '') {
            $crossPriceWrapper = $priceWrapperOuter->appendChild(new \DOMElement('div'));
            $crossPriceWrapper->appendChild(new \DOMAttr('class', 'itemcard_order_button_cross_price_wrapper cross_price'));
            $crossPriceWrapper->appendChild(new \DOMText($crossPriceString));
        }
        if (!empty($discounts)) {
            $discountInfoWrapper = $priceWrapperOuter->appendChild(new \DOMElement('div'));
            $discountInfoWrapper->appendChild(new \DOMAttr('class', 'item_order_button_discount_info_wrapper'));
            foreach ($discounts as $discount) {
                if ($discount instanceof AppliedDiscount && $discount->getSourceType() === DiscountBase::DISCOUNT_SOURCE_TYPE_RULE) {
                    $discountNotification = '';
                    $valueType = $discount->getDiscountValueType();
                    $value = $discount->getDiscountValue();
                    $valueStr = $value;
                    if ($valueType === DiscountBase::DISCOUNT_VALUE_TYPE_AMOUNT) {
                        $valueStr .= '€';
                    } else {
                        $valueStr .= '%';
                    }
                    $discountNotification = $valueStr . ' ' . $this->templating->getText('discount') . ' (' . $discount->getNotification() . ') ';
                    $discountInfoElement = $discountInfoWrapper->appendChild(new \DOMElement('div'));
                    $discountInfoElement->appendChild(new \DOMAttr('class', 'item_order_button_discount_info_element'));
                    $discountInfoElement->appendChild(new \DOMText($discountNotification));
                }
            }
        }

        $priceWrapperInner = $priceWrapperOuter->appendChild(new \DOMElement('div'));

        if ($this->webshopItem->getCustomizationStatus()) {
            $priceWrapperInner->appendChild(new \DOMAttr('class', 'itemcard_individualisation_cust_price_wrapper base_price'));
        } else {
            $priceWrapperInner->appendChild(new \DOMAttr('class', 'itemcard_order_button_cust_price_wrapper base_price'));
        }

        $priceStr = $sourcePriceData->getFormattedCustomerPrice();

        if ($is_customize === true) {
            $priceStr = $this->webshopItem->getCustomizationPrice();
            $priceStr = number_format((float)$priceStr, 2, ',', '.') . ' €';
        }

        $priceWrapperInner = $this->appendPriceItemProp($priceWrapperInner, $sourcePriceData);

        $priceWrapperInner->appendChild(new \DOMText($priceStr));
        ++$i;
        return $priceWrapperOuter;
    }

    /**
     * @param \DOMNode $node
     * @param SubscriptionItemPriceDataInterface $subscriptionPriceData
     * @return \DOMElement
     */
    protected function appendTotalSubscriptionSequencePriceToDOMNode(\DOMNode $node, SubscriptionItemPriceDataInterface $subscriptionPriceData)
    {
        $payIntervalDescription = $subscriptionPriceData->getSubscriptionPayIntervalDescription();
        $priceStr = $subscriptionPriceData->getFormattedSubscriptionSequenceTotal();
        if ($payIntervalDescription !== '') {
            $payIntervalWrapper = $node->appendChild(new \DOMElement('div'));
            $payIntervalWrapper->appendChild(new \DOMAttr('class', 'subscription_pay_interval_wrapper'));
            $payIntervalInner = $payIntervalWrapper->appendChild(new \DOMElement('span'));
            $payIntervalInner->appendChild(new \DOMText(' / ' . $payIntervalDescription));
        }
        $wrapperInner = $node->appendChild(new \DOMElement('div'));
        $wrapperInner->appendChild(new \DOMAttr('class', 'subscription_sequence_total_wrapper'));
        $wrapperInner = $this->appendSubscriptionPriceItemProp($wrapperInner, $subscriptionPriceData);
        $wrapperInner->appendChild(new \DOMText('(' . $priceStr . ' Total)'));
        return $wrapperInner;
    }

    /**
     * @param \DOMNode $node
     * @param bool $includeShipping
     */
    protected function appendVATAndShipTextToDOMNode(\DOMNode $node, $includeShipping)
    {
        static $i = 0;
        $prodPostingGroup = $this->webshopItem->getVATProdPostingGroup();
        $vatPercent = $this->vatManager->getVATPercentForProdPostingGroup($prodPostingGroup);
        $prefix = 'item_vat_and_shipping_notice_';
        $shipPart = ((bool)$includeShipping) ? 'incl_ship' : 'plus_ship';
        $suffix = '_no_vat';
        if ($this->shopConfiguration->isShopB2C() && ($vatPercent > 0)) {
            switch ($this->webshopItem->getVATProdPostingGroup()) {
                case $this->shopConfiguration->getVatIdentifier1():
                    $suffix = '_vat1';
                    break;
                case $this->shopConfiguration->getVatIdentifier2():
                    $suffix = '_vat2';
                    break;
                case $this->shopConfiguration->getVatIdentifier3():
                    $suffix = '_vat3';
                    break;
                default:
                    $suffix = '_vat1';
                    break;
            }
        }
        $tc = $prefix . $shipPart . $suffix;
        $text = $this->templating->getText($tc);
        //$text = $GLOBALS["tc"][$tc];
        if (!$text) {
            $text = '';
        }
        $dummyDoc = new \DOMDocument('1.0', 'UTF-8');
        $dummyDoc->loadXML($text);
        $textEL = $dummyDoc->documentElement;
        $wrapperNode = $node->appendChild(new \DOMElement('div'));
        $wrapperNode->appendChild(new \DOMAttr('class', 'itemcard_order_button_vat_ship_notice_wrapper'));

        $imported = $node->ownerDocument->importNode($textEL, true);
        $wrapperNode->appendChild($imported);
        ++$i;
    }

    /**
     * @param \DOMNode $node
     * @return \DOMNode|void
     */
    protected function appendSubscriptionChoiceWithSavingsToDOMNode(\DOMNode $node)
    {
        $subscData = $this->itemSubscriptionData;
        $subscHeaders = $subscData->getSubscriptionHeaders();
        $wrapper = $node->appendChild(new \DOMElement('div'));
        $multipleHeaders = count($subscHeaders) > 1;
        $elType = $multipleHeaders ? 'select' : 'div';
        $subElType = $multipleHeaders ? 'option' : 'span';
        $selectBodyClass = $multipleHeaders ? ' select_body' : '';
        $wrapper->appendChild(new \DOMAttr('class', 'itemcard_order_subsc_select_wrapper' . $selectBodyClass));
        $selectOrDiv = $wrapper->appendChild(new \DOMElement($elType));
        if ($multipleHeaders) $selectOrDiv->appendChild(new \DOMAttr('name', 'subscription_header_id'));
        $selectOrDiv->appendChild(new \DOMAttr('id', 'itemcard_order_button_subsc_select'));
        $savingsString = '';
        $savingsStringPrefix = $this->templating->getText('itemcard_order_button_subscription_savings');
        $firstSavingsString = $savingsStringPrefix;
        $firstPriceString = '';
        $i = 0;
        for ($subscHeaders->rewind(); $subscHeaders->isCurrValid(); $subscHeaders->next()) {
            $header = $subscHeaders->current();
            $description = $header->description;
            $perUnitSavingsStr = $this->getSubscriptionSavingsStr($header);
            $priceString = $this->getUnitPriceString($header);
            $option = $selectOrDiv->appendChild(new \DOMElement($subElType));
            $option->appendChild(new \DOMText($description));
            if ($multipleHeaders) $option->appendChild(new \DOMAttr('value', $header->getID()));

            $itemLink = $subscData->getItemLink($header->code);

            $minQty = ($itemLink->min_quantity > 0) ? $itemLink->min_quantity : $this->webshopItem->getMinQty();
            $maxQty = ($itemLink->max_quantity > 0) ? $itemLink->max_quantity : $this->webshopItem->getMaxQty();
            $step = $this->webshopItem->getQtyStep();
            if ($i === 0) {
                $firstSavingsString .= $perUnitSavingsStr;
                $firstPriceString = $priceString;
            }
            $savingsString = $savingsStringPrefix . $perUnitSavingsStr;
            $option->appendChild(new \DOMAttr('data-price', $priceString));
            $option->appendChild(new \DOMAttr('data-savings', $savingsString));
            $option->appendChild(new \DOMAttr('data-minQty', $minQty));
            $option->appendChild(new \DOMAttr('data-maxQty', $maxQty));
            $option->appendChild(new \DOMAttr('data-qtyStep', $step));

            ++$i;
        }

        if (!$subscHeaders->getFirst()->typeIsSequenceItem()) {
            $savingsTextWrapper = $node->appendChild(new \DOMElement('div'));
            $savingsTextWrapper->appendChild(new \DOMAttr('class', 'itemcard_order_button_subscription_savings_wrapper'));
            $savingsTextDiv = $savingsTextWrapper->appendChild(new \DOMElement('div'));
            $savingsTextDiv->appendChild(new \DOMAttr('class', 'itemcard_order_button_subscription_savings_text'));
            $savingsTextDiv->appendChild(new \DOMText($firstSavingsString));
        }

        $dummyClickable = $wrapper->appendChild(new \DOMElement('div'));
        $dummyClickable->appendChild(new \DOMAttr('id', 'itemcard_order_button_select_dummy_clickable'));
        $dummyClickable->appendChild(new \DOMText(''));

        return $wrapper;
    }

    /**
     * @param SubscriptionHeader $header
     * @return string
     */
    protected function getSubscriptionSavingsStr(SubscriptionHeader $header)
    {
        $priceData = $this->itemSubscriptionData->getSubscriptionPriceData($header->code);
        return $priceData->getSavingsPerUnitDesc();
    }

    /**
     * @param SubscriptionHeader $header
     * @return string
     */
    protected function getUnitPriceString(SubscriptionHeader $header)
    {
        $priceData = $this->itemSubscriptionData->getSubscriptionPriceData($header->code);
        return (string)$priceData;
    }

    /**
     * @param SubscriptionHeader $header
     * @param Templating $templating
     * @return bool|mixed|null|string
     */
    protected function getSubscriptionPayIntervalDescription(SubscriptionHeader $header, Templating $templating)
    {
        if (!$header->typeIsSequenceItem()) {
            return '';
        }
        return $templating->getText('subscription_pay_interval_' . $header->payment_type);
    }

    /**
     * @return \DOMElement
     */
    protected function getOrderButtonCSSLinkDOM()
    {
        $doc = $this->doc;
        $cssLink = $doc->createElement('link');
        $cssLink->appendChild(new \DOMAttr('type', 'text/css'));
        $cssLink->appendChild(new \DOMAttr('rel', 'stylesheet'));
        $cssLink->appendChild(new \DOMAttr('href', '/layout/frontend/' . $GLOBALS['site']['code'] . '/css/itemcard_order_button.css'));
        $cssLink = $doc->appendChild($cssLink);
        return $cssLink;
    }

    /**
     * @return \DOMElement
     */
    protected function getOrderButtonJSLinkDOM()
    {
        $doc = $this->doc;

        $scriptLink = $doc->createElement('script');
        $scriptLink->appendChild(new \DOMAttr('src', '/module/dcshop/common/scripts/itemcard_order_button.js'));
        $scriptLink->appendChild(new \DOMAttr('type', 'text/javascript'));
        $scriptLink = $doc->appendChild($scriptLink);
        return $scriptLink;
    }

    /**
     * @return mixed
     */
    protected function getOrderButtonCSSLinkHTML()
    {
        $DOM = $this->getOrderButtonCSSLinkDOM();
        return $DOM->ownerDocument->saveXML($DOM);
    }

    /**
     * @return mixed
     */
    protected function getOrderButtonJSLinkHTML()
    {
        $DOM = $this->getOrderButtonJSLinkDOM();
        return $DOM->ownerDocument->saveXML($DOM);
    }

    /**
     * @param \DOMNode $node
     */
    protected function appendPricePerGramDisplay(\DOMNode $node)
    {
        $wrapperNode = $node->appendChild(new \DOMElement('div'));
        $wrapperNode->appendChild(new \DOMAttr('class', 'orderbox_price_per_gram'));
        $wrapperNode->appendChild(new \DOMAttr('style', 'display: none;'));
        $itemWeight = $this->webshopItem->getNetWeight();
        $itemPrice = $this->itemPriceData->getCustomerPrice();

        if ($itemWeight > 0) {
            $itemGramPrice = $itemPrice / $itemWeight * 100;
            $itemGramStr = $this->templating->getText('price_per_gram');
            $itemGramStr .= format_amount($itemGramPrice, TRUE, FALSE);

            $dummyDoc = new \DOMDocument('1.0', 'UTF-8');
            $dummyDoc->loadHTML($itemGramStr);
            $textEL = $dummyDoc->documentElement->textContent;

            $imported = $node->ownerDocument->createTextNode($textEL);
            $wrapperNode->appendChild($imported);
        }
    }

    /**
     * @param \DOMNode $node
     */
    protected function appendAvailabilityDisplay(\DOMNode $node)
    {
        $wrapperNode = $node->appendChild(new \DOMElement('div'));
        $wrapperNode->appendChild(new \DOMAttr('class', 'orderbox_inventory'));
        $availabilityCode = $this->itemAvailabilityProvider->getItemAvailability($this->webshopItem);

        $text = '<span>';

        switch ($this->shopConfiguration->getShop()->getInventoryDisplay()) {
            case 1:
                $inventoryDisplay = 'traffic';
                $currentInventory = $this->itemAvailabilityProvider->getInventory($this->webshopItem);
                if (($currentInventory > $this->webshopItem->getLowInventoryLimit())) {
                    $currentInventory = $this->webshopItem->getLowInventoryLimit();
                }
                $text = $this->templating->getText($availabilityCode) . $text;
                $this->templating->replacePlaceholder($text,'CURR_INVENTORY',$currentInventory);
                break;
            case 2:
                $inventoryDisplay = 'number';
                $currentInventory = $this->itemAvailabilityProvider->getInventory($this->webshopItem);
                if (($currentInventory > $this->webshopItem->getLowInventoryLimit())) {
                    $currentInventory = $this->webshopItem->getLowInventoryLimit();
                }
                $text .= $this->templating->getText($availabilityCode . '_' . $inventoryDisplay);
                $this->templating->replacePlaceholder($text,'CURR_INVENTORY',$currentInventory);
                break;
            case 0:
            default:
                $inventoryDisplay = 'checkmark';
            $currentInventory = $this->itemAvailabilityProvider->getInventory($this->webshopItem);
                if (($currentInventory > $this->webshopItem->getLowInventoryLimit())) {
                    $currentInventory = $this->webshopItem->getLowInventoryLimit();
                }
                $text = $this->templating->getText($availabilityCode) . $text;
                $this->templating->replacePlaceholder($text,'CURR_INVENTORY',$currentInventory);
                break;
        }

        $text .= '</span>';


        if (!$text) {
            $text = '';
        }

        $inventoryWrapper = $wrapperNode->appendChild(new \DOMElement('div'));
        $inventoryWrapper->appendChild(new \DOMAttr('class', 'inventory ' . $availabilityCode . ' ' . $inventoryDisplay));

        $inventoryWrapper = $this->appendAvailabilityItemProp($wrapperNode,$availabilityCode);

        $dummyDoc = new \DOMDocument('1.0', 'UTF-8');
        $dummyDoc->loadHTML('<?xml encoding="utf-8" ?>' . $text);
        $textEL = $dummyDoc->documentElement;

        $imported = $node->ownerDocument->importNode($textEL, true);
        $inventoryWrapper->appendChild($imported);

        $text = '<span>';
        $text .= $this->templating->getText($availabilityCode . '_shipping');
        $text .= '</span>';

        if (!$text) {
            $text = '';
        }

        $shippingWrapper = $wrapperNode->appendChild(new \DOMElement('div'));
        $shippingWrapper->appendChild(new \DOMAttr('class', 'inventory'));

        $dummyDoc = new \DOMDocument('1.0', 'UTF-8');
        $dummyDoc->loadHTML('<?xml encoding="utf-8" ?>' . $text);
        $textEL = $dummyDoc->documentElement;
        //$textEL = substr($dummyDoc->saveHTML(), 12, -15);

        $imported = $node->ownerDocument->importNode($textEL, true);
        $shippingWrapper->appendChild($imported);

    }

    protected function appendPriceItemProp(\DOMNode $node, ItemPriceDataInterface $sourcePriceData)
    {
        $currencyMeta = $node->appendChild(new \DOMElement('span'));
        $currencyMeta->appendChild(new \DOMAttr('itemprop','priceCurrency'));
        $currencyMeta->appendChild(new \DOMAttr('content',$sourcePriceData->__get('currencyCode')));

        $priceWithItemprop = $node->appendChild(new \DOMElement('span'));
        $priceWithItemprop->appendChild(new \DOMAttr('itemprop','price'));
        $priceWithItemprop->appendChild(new \DOMAttr('content',$sourcePriceData->getCustomerPrice()));

        $priceWithItemprop = $node->appendChild(new \DOMElement('span'));
        $priceWithItemprop->appendChild(new \DOMAttr('itemprop','lowprice'));
        $priceWithItemprop->appendChild(new \DOMAttr('content',$sourcePriceData->getCustomerPrice()));

        return $node;
    }

    protected function appendSubscriptionPriceItemProp(\DOMNode $node, SubscriptionItemPriceDataInterface $subscriptionPriceData)
    {
        $currencyMeta = $node->appendChild(new \DOMElement('span'));
        $currencyMeta->appendChild(new \DOMAttr('itemprop','priceCurrency'));
        $currencyMeta->appendChild(new \DOMAttr('content',$subscriptionPriceData->__get('currencyCode')));

        $subscriptionPriceWithItemprop = $node->appendChild(new \DOMElement('span'));
        $subscriptionPriceWithItemprop->appendChild(new \DOMAttr('itemprop','price'));
        $subscriptionPriceWithItemprop->appendChild(new \DOMAttr('content',$subscriptionPriceData->__get('subscriptionSequenceTotal')));

        return $node;
    }

    protected function appendAvailabilityItemProp(\DOMNode $node, $availabilityCode)
    {
        $availabilityMeta = $node->appendChild(new \DOMElement('span'));
        $availabilityMeta->appendChild(new \DOMAttr('itemprop','availability'));

        switch ($availabilityCode) {
            case DefaultItemAvailabilityProvider::INVENTORY_NOT_AVAILABLE:
                $content = self::HTTP_SCHEMA_ORG_OUT_OF_STOCK;
                break;
            case DefaultItemAvailabilityProvider::INVENTORY_AVAILABLE_NOT_ORDERABLE:
                $content = self::HTTP_SCHEMA_ORG_OUT_OF_STOCK;
                break;
            case DefaultItemAvailabilityProvider::INVENTORY_LOW_AVAILABILITY_NOT_ORDERABLE:
                $content = self::HTTP_SCHEMA_ORG_OUT_OF_STOCK;
                break;
            case DefaultItemAvailabilityProvider::INVENTORY_LOW_AVAILABILITY:
                $content = self::HTTP_SCHEMA_ORG_LIMITED_AVAILABILITY;
                break;
            case DefaultItemAvailabilityProvider::INVENTORY_AVAILABLE:
                $content = self::HTTP_SCHEMA_ORG_IN_STOCK;
                break;
            case DefaultItemAvailabilityProvider::INVENTORY_NOT_AVAILABLE_ORDERABLE:
                $content = self::HTTP_SCHEMA_ORG_PRE_ORDER;
                break;
        }

        $availabilityMeta->appendChild(new \DOMAttr('content',$content));

        return $node;
    }

    public function getGraduatedPricesTableHTML(GraduatedItemPriceData $priceData,$itemBaseUnitOfMeasure,$localeCode,$limitAtLines = 0)
    {
        $localeCode = $localeCode ?? 'de-DE';
        $numberFormatter = \NumberFormatter::create($localeCode,\NumberFormatter::CURRENCY);
        $doc = $this->doc;

        $wrapper = $doc->createElement('div');
        $wrapper->appendChild(new \DOMAttr('class', 'graduated_price_table'));
        $priceArray = $priceData->toArray();
        $i = 0;
        foreach ($priceArray as $price) {
            if ($limitAtLines > 0 && $i >= $limitAtLines) {
                $priceLineWrapper = $doc->createElement('div');
                $priceLineWrapper->appendChild(new \DOMAttr('class','graduated_price_line'));
                $descriptionSpan = $doc->createElement('span');
                $descriptionSpan->appendChild(new \DOMAttr('class','graduated_price_desc'));
                $descriptionSpan->appendChild(new \DOMText('...'));
                $priceLineWrapper->appendChild($descriptionSpan);
                $wrapper->appendChild($priceLineWrapper);
                break;
            }
            $minQty = $price['minimum_quantity'];
            $pricePerUnit = $price['your_price_per_unit'];
            $currencyCode = $price['currency_code'] ?? 'EUR';
            $formattedPricePerUnit = $numberFormatter->formatCurrency($pricePerUnit,$currencyCode);
            $priceLineWrapper = $doc->createElement('div');
            $priceLineWrapper->appendChild(new \DOMAttr('class','graduated_price_line'));
            $descriptionSpan = $doc->createElement('span');
            $descriptionSpan->appendChild(new \DOMAttr('class','graduated_price_desc'));
            $descriptionSpan->appendChild(new \DOMText($this->templating->getText('your_price_from') . ' ' . $minQty . ' ' . $itemBaseUnitOfMeasure));
            $priceLineWrapper->appendChild($descriptionSpan);
            $priceSpan = $doc->createElement('span');
            $priceSpan->appendChild(new \DOMAttr('class','graduated_price_value'));
            $priceSpan->appendChild(new \DOMText($formattedPricePerUnit));
            $priceLineWrapper->appendChild($priceSpan);
            $wrapper->appendChild($priceLineWrapper);
            $i++;
        }
        $doc->appendChild($wrapper);
        $string = $wrapper->ownerDocument->saveXML($wrapper);
        return $string;
    }

}
