<?php
namespace DynCom\dc\dcShop\classes;

use DynCom\dc\dcShop\interfaces\CustomerInterface;
use DynCom\dc\dcShop\interfaces\IVATManager;
use DynCom\dc\dcShop\interfaces\OrderableEntityInterface;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;
use DynCom\dc\RuleEngine\ActiveActionItemRuleDiscountRepository;
use DynCom\dc\RuleEngine\ActiveActionItemRuleDiscountRepositoryInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 28.03.2015
 * Time: 13:56
 */
class AdvancedPriceProvider
{
    /**
     * @var BasicPriceProvider
     */
    protected $basicPriceProvider;

    /**
     * @var Shop
     */
    protected $shop;

    /**
     * @var IVATManager
     */
    protected $VATManager;

    /**
     * @var string
     */
    protected $shopLanguageDefaultCurrencyCode;

    /**
     * @var ActiveActionItemRuleDiscountRepository
     */
    protected $ruleDiscountRepository;

    protected $inGetCrossPrice;

    /*
     * @param                            $shop
     * @param                     $vatManager
     * @param                                 $shopLanguageDefaultCurrencyCode
     */
    /**
     * AdvancedPriceProvider constructor.
     * @param BasicPriceProvider $basicPriceProvider
     * @param Shop $shop
     * @param IVATManager $VATManager
     * @param $shopLanguageDefaultCurrencyCode
     * @param ActiveActionItemRuleDiscountRepositoryInterface $ruleDiscountRepository
     */
    public function __construct(
        BasicPriceProvider $basicPriceProvider,
        Shop $shop,
        IVATManager $VATManager,
        $shopLanguageDefaultCurrencyCode,
        ActiveActionItemRuleDiscountRepositoryInterface $ruleDiscountRepository
    )
    {
        $this->basicPriceProvider = $basicPriceProvider;
        $this->shop = $shop;
        $this->VATManager = $VATManager;
        $this->shopLanguageDefaultCurrencyCode = $shopLanguageDefaultCurrencyCode;
        $this->ruleDiscountRepository = $ruleDiscountRepository;
    }

    /**
     * @param WebshopItemInterface $item
     * @param $quantity
     * @param CustomerInterface $customer
     * @param $currencyCode
     * @param $forCustomization
     * @return ItemPriceData
     */
    public function getItemCustomerPrice(WebshopItemInterface $item, $quantity, CustomerInterface $customer, $currencyCode = null, $forCustomization = false)
    {

        if ($currencyCode === null) {
            $currencyCode = $this->shopLanguageDefaultCurrencyCode;
        }

        if ($forCustomization) {
            $unitPrice = $item->getCustomizationPrice();
            $price = new ItemPriceData($item->getID(),$item->getCompany(),$item->getItemNo(),$item->getVATProdPostingGroup(),$quantity,$unitPrice,$item->price_includes_vat,false,false,$currencyCode);
            $price->setPriceSourceCustomization();
            $price->setQtySourceUser();
            return $price;
        }


        $billToCustomerNo = (!empty($customer->bill_to_customer_no)) ? $customer->bill_to_customer_no : $customer->customer_no;
        //Get best overall Price
        $currentBestSalesPrice = $this->basicPriceProvider->getBestSalesPriceAllTypes(
            $item,
            $quantity,
            $customer,
            $currencyCode
        );
        //If best overall price is from Price-Line and does not allow line discounts
        //then get best price which allows line discounts
        if (!$currentBestSalesPrice->priceAllowsLineDiscount && ($currentBestSalesPrice->getPriceSource() === ItemPriceData::PRICE_SOURCE_PRICE_LINES)) {
            $currentBestSalesPriceDiscAllowed = $this->basicPriceProvider->getBestSalesPriceLineDiscAllowedAllTypes(
                $item,
                $quantity,
                $customer,
                $currencyCode
            );
            //If the best overall price is from a price line
            //but the best price allowing line discounts is from the item itself
            //meaning there are no price lines for the item which allow line discounts
            //then that price is discarded, because once an applicable price line exists
            //the price from the item itself becomes inapplicable, so we set it to 0
            if ($currentBestSalesPriceDiscAllowed->getPriceSource() === ItemPriceData::PRICE_SOURCE_ITEM_PRICE) {
                $currentBestSalesPriceDiscAllowed->setPrice(0.00);
                $currentBestSalesPriceDiscAllowed->setPriceAllowsInvoiceDiscount(false);
            }
        } elseif ($currentBestSalesPrice->priceAllowsLineDiscount) {
            $currentBestSalesPriceDiscAllowed = &$currentBestSalesPrice;
        }

        //Adjust VAT to Shop-setting (add or subtract VAT if needed)
        $this->VATManager->doVATCalculationOnItemPrice($currentBestSalesPrice);
        $this->VATManager->doVATCalculationOnItemPrice($currentBestSalesPriceDiscAllowed);

        $returnPriceData = $currentBestSalesPrice;

        //Look for discounts applying to the item by item_no
        //If there is a valid (<>0) price allowing line-discounts
        //then apply the discount to it. If the result is smaller than the current best price
        //then set the price-to-be-returned to the new price with discount applied
        $bestLineDiscountPercentItem = $this->basicPriceProvider->getBestLineDiscountAllTypesForItemNo(
            $item,
            $quantity,
            $customer,
            $currencyCode
        );
        if ($bestLineDiscountPercentItem !== 0 && ($currentBestSalesPriceDiscAllowed->price !== 0.00)) {
            $currentBestSalesPriceDiscAllowed->applyDiscount($bestLineDiscountPercentItem, $this->VATManager);
            $currentBestSalesPriceItemDiscountApplied = $currentBestSalesPriceDiscAllowed;
            //$currentBestSalesPriceItemDiscountApplied = $this->basicPriceProvider->getPriceWithDiscountApplied($currentBestSalesPriceDiscAllowed,$bestLineDiscountPercentItem);
            if ($currentBestSalesPriceItemDiscountApplied->price < $currentBestSalesPrice->price) {
                $returnPriceData = $currentBestSalesPriceItemDiscountApplied;
            }
        }

        //Look for discounts applying to the item by the item's discount group
        //If there is a valid (<>0) price allowing line-discounts
        //then apply the discount to it. If the result is smaller than the current best price
        //then set the price-to-be-returned to the new price with discount applied
        $bestLineDiscountPercentItemGroup = $this->basicPriceProvider->getBestLineDiscountAllTypesForItemDiscountGroup(
            $item,
            $quantity,
            $customer,
            $currencyCode
        );
        if ($bestLineDiscountPercentItemGroup !== 0 && ($currentBestSalesPriceDiscAllowed->price !== 0.00)) {
            $currentBestSalesPriceDiscAllowed->applyDiscount($bestLineDiscountPercentItemGroup, $this->VATManager);
            $currentBestSalesPriceItemGroupDiscountApplied = $currentBestSalesPriceDiscAllowed;
            //$currentBestSalesPriceItemGroupDiscountApplied = $this->basicPriceProvider->getPriceWithDiscountApplied($currentBestSalesPriceDiscAllowed,$bestLineDiscountPercentItemGroup);
            if ($currentBestSalesPriceItemGroupDiscountApplied->price < $returnPriceData->price) {
                $returnPriceData = $currentBestSalesPriceItemGroupDiscountApplied;
            }
        }

        //Look for discounts created by the RuleEngine
        $lineDiscountsFromRules = $this->ruleDiscountRepository->getMatchingDiscountsForItem($item);
        $hasRuleDiscounts = count($lineDiscountsFromRules) > 0;
        $priceBeforeRuleDiscounts = $returnPriceData->getUnitPrice();
        foreach ($lineDiscountsFromRules as $lineDiscount) {
            $returnPriceData->applyDiscount($lineDiscount, $this->VATManager);
        }

        //Set Cross-Price
        if (!$hasRuleDiscounts && !$this->inGetCrossPrice) {
            $returnPriceData->setCrossPrice($this->getItemCrossPrice($item, $customer, $currencyCode));
        } else {
            $returnPriceData->setCrossPrice($priceBeforeRuleDiscounts);
        }

        //Set initial Price Source & Qty-Source

        $returnPriceData->setQtySourceUser();
        return $returnPriceData;
    }


    /**
     * @param WebshopItemInterface $item
     * @param CustomerInterface $customer
     * @param $currencyCode
     * @return ItemPriceData
     */
    public function getBestCampaignPrice(WebshopItemInterface $item, CustomerInterface $customer, $currencyCode)
    {

        $bestCampaignSalesPriceAllTypes = $this->basicPriceProvider->getBestSalesPriceForCampaignAllTypes(
            $item,
            $currencyCode
        );
        $bestCampaignSalesPriceLineDiscountAllowed = $this->basicPriceProvider->getBestSalesPriceLineDiscAllowedForCampaign(
            $item,
            $currencyCode
        );

        //Adjust VAT to Shop-setting (add or subtract VAT if needed)
        $this->VATManager->doVATCalculationOnItemPrice($bestCampaignSalesPriceAllTypes);
        $this->VATManager->doVATCalculationOnItemPrice($bestCampaignSalesPriceLineDiscountAllowed);

        $returnPriceData = $bestCampaignSalesPriceAllTypes;

        //Look for discounts applying to the item by item_no
        //If there is a valid (<>0) price allowing line-discounts
        //then apply the discount to it. If the result is smaller than the current best price
        //then set the price-to-be-returned to the new price with discount applied
        $bestLineDiscountPercentItem = $this->basicPriceProvider->getBestLineDiscountAllTypesForItemNo(
            $item,
            1,
            $customer,
            $currencyCode
        );
        if ($bestLineDiscountPercentItem !== 0 && ($bestCampaignSalesPriceLineDiscountAllowed->price !== 0.00)) {
            $bestCampaignSalesPriceLineDiscountAllowed->applyDiscount($bestLineDiscountPercentItem, $this->VATManager);
            $currentBestCampaignPriceItemDiscountApplied = $bestCampaignSalesPriceLineDiscountAllowed;
            //$currentBestCampaignPriceItemDiscountApplied = $this->basicPriceProvider->getPriceWithDiscountApplied($bestCampaignSalesPriceLineDiscountAllowed,$bestLineDiscountPercentItem);
            if ($currentBestCampaignPriceItemDiscountApplied->price < $bestCampaignSalesPriceAllTypes->price) {
                $returnPriceData = $currentBestCampaignPriceItemDiscountApplied;
            }
        }

        //Look for discounts applying to the item by the item's discount group
        //If there is a valid (<>0) price allowing line-discounts
        //then apply the discount to it. If the result is smaller than the current best price
        //then set the price-to-be-returned to the new price with discount applied
        $bestLineDiscountPercentItemGroup = $this->basicPriceProvider->getBestLineDiscountAllTypesForItemDiscountGroup(
            $item,
            1,
            $customer,
            $currencyCode
        );
        if ($bestLineDiscountPercentItemGroup !== 0 && ($bestCampaignSalesPriceLineDiscountAllowed->price !== 0.00)) {
            $bestCampaignSalesPriceLineDiscountAllowed->applyDiscount(
                $bestLineDiscountPercentItemGroup,
                $this->VATManager
            );
            $currentBestCampaignPriceItemGroupDiscountApplied = $bestCampaignSalesPriceLineDiscountAllowed;
            //$currentBestCampaignPriceItemGroupDiscountApplied = $this->basicPriceProvider->getPriceWithDiscountApplied($bestCampaignSalesPriceLineDiscountAllowed,$bestLineDiscountPercentItemGroup);
            if ($currentBestCampaignPriceItemGroupDiscountApplied->price < $returnPriceData->price) {
                $returnPriceData = $currentBestCampaignPriceItemGroupDiscountApplied;
            }
        }


        //Set Cross-Price
        if (!$this->inGetCrossPrice) {
            $returnPriceData->setCrossPrice($this->getItemCrossPrice($item, $customer, $currencyCode));
        }
        $returnPriceData->setQtySourceUser();
        return $returnPriceData;

    }

    /**
     * @param WebshopItemInterface $item
     * @param CustomerInterface $customer
     * @param $currencyCode
     * @return GraduatedItemPriceData
     */
    public function getGraduatedPrices(WebshopItemInterface $item, CustomerInterface $customer, $currencyCode)
    {
        //TODO: test
        $graduatedPrices = new GraduatedItemPriceData();
        $minQtys = $this->basicPriceProvider->getGraduatedPricesMinQtys($item, $customer, $currencyCode);
        foreach ($minQtys as $minQtyRow) {
            $minQty = (float)$minQtyRow['minimum_quantity'];
            if ($minQty <= 0) {
                $minQty = $item->getMinQty();
            }
            $currPrice = $this->getItemCustomerPrice($item, $minQty, $customer, $currencyCode,false);
            $graduatedPrices->addPrice($minQty, $currPrice);
        }
        return $graduatedPrices;
    }


    /**
     * @param WebshopItemInterface $item
     * @param CustomerInterface $customer
     * @param $currencyCode
     * @return ItemPriceData|float
     */
    public function getItemCrossPrice(WebshopItemInterface $item, CustomerInterface $customer, $currencyCode)
    {
        $this->inGetCrossPrice = true;
        $crossPrice = 0.00;
        switch ($this->shop->cross_price_typ) {
            case 1:
                $crossPrice = ($item->retail_price > $item->base_price) ? $item->retail_price : 0.00;
                break;
            case 2:
                $crossPrice = ($item->retail_price > $this->getItemCustomerPrice(
                        $item, 1, $customer, $currencyCode,false
                    )) ? $item->retail_price : 0.00;
                break;
            case 3:
                $crossPrice = ($item->retail_price > $this->getBestCampaignPrice(
                        $item,
                        $customer,
                        $currencyCode
                    )) ? $item->retail_price : 0.00;
                break;
            case 4:
                $crossPrice = ($item->base_price > $this->getItemCustomerPrice(
                        $item, 1, $customer, $currencyCode,false
                    )) ? $item->base_price : 0.00;
                break;
            case 5:
                $crossPrice = ($item->base_price > $this->getBestCampaignPrice(
                        $item,
                        $customer,
                        $currencyCode
                    )) ? $item->base_price : 0.00;
                break;
            case 6:
                $crossPrice = ($this->getItemCustomerPrice($item, 1, $customer, $currencyCode,false) > $this->getBestCampaignPrice(
                        $item,
                        $customer,
                        $currencyCode
                    )) ? $this->getItemCustomerPrice($item, 1, $customer, $currencyCode,false) : 0.00;
                break;
            default:
                $crossPrice = 0.00;
                break;
        }
        $this->inGetCrossPrice = false;
        return $crossPrice;
    }

    /**
     * @param WebshopItemInterface $item
     * @param CustomerInterface $customer
     * @param $currencyCode
     * @param $priceGroupCode
     * @param $discountGroupCode
     * @return ItemPriceData
     */
    public function getCustomerPriceFromPriceGroup(
        WebshopItemInterface $item,
        CustomerInterface $customer,
        $currencyCode,
        $priceGroupCode,
        $discountGroupCode
    )
    {
        $priceDataCurrencyCode = $currencyCode ?: 'EUR';
        $currencyConditions = $this->basicPriceProvider->getCurrencyQuerySnippet($currencyCode);
        $curDate = date('Y-m-d');

        if (empty($item->multiplier) || (float)$item->multiplier <= 0) {
            $multiplier = 1;
        } else {
            $multiplier = $item->multiplier;
        }
        if ($priceGroupCode != '') {

            $currentBestSalesPrice = $this->basicPriceProvider->getPriceFromPriceGroup(
                $item,
                $currencyCode,
                $priceGroupCode
            );
            if (($currentBestSalesPrice->getPriceSource() === ItemPriceData::PRICE_SOURCE_PRICE_LINES) && !$currentBestSalesPrice->priceAllowsLineDiscount
            ) {
                $currentBestSalesPriceDiscAllowed = $this->basicPriceProvider->getPriceFromPriceGroupAllowsLineDisc(
                    $item,
                    $currencyCode,
                    $priceGroupCode
                );
                //If the best overall price is from a price line
                //but the best price allowing line discounts is from the item itself
                //meaning there are no price lines for the item which allow line discounts
                //then that price is discarded, because once an applicable price line exists
                //the price from the item itself becomes inapplicable, so we set it to 0
                if ($currentBestSalesPriceDiscAllowed->getPriceSource() === ItemPriceData::PRICE_SOURCE_ITEM_PRICE) {
                    $currentBestSalesPriceDiscAllowed->setPrice(0.00);
                    $currentBestSalesPriceDiscAllowed->setPriceAllowsInvoiceDiscount(false);
                }
            } elseif ($currentBestSalesPrice->priceAllowsLineDiscount) {
                $currentBestSalesPriceDiscAllowed = &$currentBestSalesPrice;
            }

            //Adjust VAT to Shop-setting (add or subtract VAT if needed)
            $this->VATManager->doVATCalculationOnItemPrice($currentBestSalesPrice);
            $this->VATManager->doVATCalculationOnItemPrice($currentBestSalesPriceDiscAllowed);
            $returnPriceData = $currentBestSalesPrice;

            if ($discountGroupCode !== '') {
                $bestLineDiscount = $this->basicPriceProvider->getBestLineDiscountForItemNoWithDiscountGroup(
                    $item,
                    $currencyConditions,
                    $discountGroupCode
                );
                //Look for discounts applying to the item by item_no
                //If there is a valid (<>0) price allowing line-discounts
                //then apply the discount to it. If the result is smaller than the current best price
                //then set the price-to-be-returned to the new price with discount applied
                $bestLineDiscountPercentItem = $this->basicPriceProvider->getBestLineDiscountForItemNoWithDiscountGroup(
                    $item,
                    $currencyConditions,
                    $discountGroupCode
                );
                if ($bestLineDiscountPercentItem !== 0 && ($currentBestSalesPriceDiscAllowed->price !== 0.00)) {
                    $currentBestSalesPriceDiscAllowed->applyDiscount($bestLineDiscountPercentItem, $this->VATManager);
                    $currentBestSalesPriceItemDiscountApplied = $currentBestSalesPriceDiscAllowed;
                    //$currentBestSalesPriceItemDiscountApplied = $this->basicPriceProvider->getPriceWithDiscountApplied($currentBestSalesPriceDiscAllowed, $bestLineDiscountPercentItem);
                    if ($currentBestSalesPriceItemDiscountApplied->price < $currentBestSalesPrice->price) {
                        $returnPriceData = $currentBestSalesPriceItemDiscountApplied;
                    }
                }

                //Look for discounts applying to the item by the item's discount group
                //If there is a valid (<>0) price allowing line-discounts
                //then apply the discount to it. If the result is smaller than the current best price
                //then set the price-to-be-returned to the new price with discount applied
                $bestLineDiscountPercentItemGroup = $this->basicPriceProvider->getBestLineDiscountForItemDiscGroupWithDiscountGroup(
                    $item,
                    $currencyConditions,
                    $discountGroupCode
                );
                if ($bestLineDiscountPercentItemGroup !== 0 && ($currentBestSalesPriceDiscAllowed->price !== 0.00)) {
                    $currentBestSalesPriceDiscAllowed->applyDiscount(
                        $bestLineDiscountPercentItemGroup,
                        $this->VATManager
                    );
                    $currentBestSalesPriceItemGroupDiscountApplied = $currentBestSalesPriceDiscAllowed;
                    //$currentBestSalesPriceItemGroupDiscountApplied = $this->basicPriceProvider->getPriceWithDiscountApplied($currentBestSalesPriceDiscAllowed, $bestLineDiscountPercentItemGroup);
                    if ($currentBestSalesPriceItemGroupDiscountApplied->price < $returnPriceData->price) {
                        $returnPriceData = $currentBestSalesPriceItemGroupDiscountApplied;
                    }
                }

            }
            $returnPriceData->setQtySourceUser();
            return $returnPriceData;
        }
        throw new \InvalidArgumentException('Parameter \'priceGroupCode\' must not be empty.');
    }

    /**
     * @param WebshopItemInterface $item
     * @param CustomerInterface $customer
     * @param $countryCode
     * @param $currencyCode
     * @return ItemPriceData
     */
    public function getCustomerPriceFromCountry(
        WebshopItemInterface $item,
        CustomerInterface $customer,
        $countryCode,
        $currencyCode
    )
    {
        $groups = $this->basicPriceProvider->getPriceAndDiscountGroupsForCountryCode($countryCode);
        $basePrice = $this->getCustomerPriceFromPriceGroup(
            $item,
            $customer,
            $currencyCode,
            $groups['price_groups']['base_price'],
            $groups['discount_groups']['base_price']
        );
        $retailPrice = $this->getCustomerPriceFromPriceGroup(
            $item,
            $customer,
            $currencyCode,
            $groups['price_groups']['retail_price'],
            $groups['discount_groups']['retail_price']
        );
        $returnPrice = $basePrice;
        if ($retailPrice->getCustomerPrice() > $basePrice->getCustomerPrice()) {
            $returnPrice->setCrossPrice($retailPrice->getCustomerPrice());
        }
        $returnPrice->setQtySourceUser();
        return $returnPrice;
    }

    /**
     * @return IVATManager
     */
    public function getVATManager()
    {
        return $this->VATManager;
    }

    /**
     * Provides a \Closure over an Item, Customer and Currency-Code to
     * recalculate the (naive) unit price from somewhere else. The returned
     * \Closure then receives only the new quantity to return the new unit price.
     * @param WebshopItemInterface $item
     * @param $quantity
     * @param CustomerInterface $customer
     * @param null $currencyCode
     * @return \Closure
     */
    public function getPriceUpdateClosure(
        WebshopItemInterface $item,
        $quantity,
        CustomerInterface $customer,
        $currencyCode = null,
        $forCustomization = false
    )
    {
        $func = function ($quantity) use (&$item, &$customer, $currencyCode, $forCustomization) {
            if ($item instanceof OrderableEntityInterface || $item instanceof BasketEntity) {
                $forCustomization = !empty($item->getCustomizationHash());
            }
            $custPrice = $this->getItemCustomerPrice($item, $quantity, $customer, $currencyCode,$forCustomization)->getUnitPrice();
        };
        return $func;
    }

}