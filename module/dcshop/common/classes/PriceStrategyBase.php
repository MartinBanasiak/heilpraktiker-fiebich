<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\dcShop\interfaces\IVATManager;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 24.03.2015
 * Time: 17:24
 */
abstract class PriceStrategyBase {

    protected $db;
    protected $shop;
    protected $vatManager;
    protected $variant_code;
    protected $shopLanguageDefaultCurrencyCode;


    /**
     * @param GenericDBQueryWrapperInterface $db
     * @param Shop $shop
     * @param IVATManager $vatManager
     * @param                                 $shopLanguageDefaultCurrencyCode
     */
    public function __construct( GenericDBQueryWrapperInterface $db, Shop $shop, IVATManager $vatManager, $shopLanguageDefaultCurrencyCode ) {
        $this->db                              = $db;
        $this->shop                            = $shop;
        $this->vatManager                      = $vatManager;
        $this->shopLanguageDefaultCurrencyCode = $shopLanguageDefaultCurrencyCode;
    }

    /**
     * @param WebshopItem $item
     * @param              $variantCode
     * @param              $quantity
     * @param |Customer $customer
     * @param              $currencyCode
     * @return \ItemPriceData
     */
    protected function _getBestSalesPriceAllTypes( WebshopItem $item, $variantCode, $quantity, Customer $customer, $currencyCode ) {

        $currencyConditions = $this->_getCurrencyQuerySnippet($currencyCode);
        $curDate            = new date('Y-m-d');
        $query              = "SELECT id,(CASE WHEN unit_of_measure_code = '" . $item->unit_of_measure_code . "' THEN unit_price
						   WHEN unit_of_measure_code = '' OR unit_of_measure_code = '" . $item->nav_base_unit_code . "' THEN unit_price * " . $item->multiplier . "
						   ELSE 9999999
					  END) AS 'unit_price',allow_line_disc,allow_invoice_disc,price_includes_vat
			  FROM shop_sales_price
			  WHERE type = 0
			  	AND item_no = '" . $item->item_no . "'
			  	AND ((minimum_quantity <= " . $quantity . " AND unit_of_measure_code = '" . $item->unit_of_measure_code . "')
					OR(minimum_quantity <= (" . $quantity . "*" . $item['multiplier'] . ") AND (unit_of_measure_code = '" . $item->nav_base_unit_code . "' OR unit_of_measure_code = '')))
			  	AND ((sales_type = 2)
			  		OR ((sales_type = 1) AND (sales_code='" . $customer->customer_price_group . "'))
			  		OR ((sales_type = 1) AND (sales_code='" . $this->shop->cust_price_group_base_price . "'))
			  		OR ((sales_type = 0) AND (sales_code = '" . $customer->customer_no . "'))
			  		OR ((sales_type = 3) AND (sales_code = '" . $this->shop->campaign_no . "')))
			  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= '" . $curDate . "')
			  		OR (`shop_sales_price`.`starting_date` <= '" . $curDate . "') AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= '" . $curDate . "') AND (`shop_sales_price`.`ending_date` >= '" . $curDate . "'))
			  	" . $currencyConditions . "
			  	AND (variant_code = '" . $variantCode . "' OR variant_code = '')
			  	AND company = '" . $this->shop->company . "'
			  ORDER BY unit_price ASC
			  LIMIT 1";
        $this->db->setQuery($query);
        $this->db->doQuery();
        $resultCount = $this->db->getNoOfReturnedRows();
        $salesPrice  = array();
        if ($resultCount === 1) {
            $resultArray = $this->db->getResultArray();
            $salesPrice  = $resultArray[0];
            $priceData = new ItemPriceData($item->getID(), $this->shop->company, $item->item_no, $item->vat_prod_posting_group, $quantity, $salesPrice['unit_price'], $salesPrice['price_includes_vat'], $salesPrice['allow_line_disc'], $salesPrice['allow_invoice_disc'], $currencyCode);
            $priceData->setPriceSourcePriceLine();
            $priceData->setPriceSourceID($salesPrice['id']);
        } else {
            $salesPrice['unit_price']         = $item->base_price;
            $salesPrice['price_includes_vat'] = (bool)$this->shop->prices_including_vat;
            $salesPrice['allow_line_disc']    = TRUE;
            $salesPrice['allow_invoice_disc'] = ($item["allow_invoice_discount"] == 1);
            $priceData = new ItemPriceData($item->getID(), $this->shop->company, $item->item_no, $item->vat_prod_posting_group, $quantity, $salesPrice['unit_price'], $salesPrice['price_includes_vat'], $salesPrice['allow_line_disc'], $salesPrice['allow_invoice_disc'], $currencyCode);
            $priceData->setPriceSourceItemPrice();
            $priceData->setPriceSourceID($item->id);
        }
        return $priceData;
    }

    /**
     * @param WebshopItem $item
     * @param              $variantCode
     * @param              $quantity
     * @param |Customer $customer
     * @param              $currencyCode
     * @return \ItemPriceData
     */
    protected function _getBestSalesPriceLineDiscAllowedAllTypes( WebshopItem $item, $variantCode, $quantity, Customer $customer, $currencyCode ) {

        $currencyConditions = $this->_getCurrencyQuerySnippet($currencyCode);
        $curDate            = date('Y-m-d');

        //@TODO:Why not cust_price_group_base_price?
        $query = "SELECT (CASE WHEN unit_of_measure_code = '" . $item->unit_of_measure_code . "' THEN unit_price
						   WHEN unit_of_measure_code = '' OR unit_of_measure_code = '" . $item->nav_base_unit_code . "' THEN unit_price * " . $item->multiplier . "
						   ELSE 9999999
					  END) AS 'unit_price',allow_line_disc,allow_invoice_disc,price_includes_vat
			  FROM shop_sales_price
			  WHERE type = 0
			    AND allow_line_disc = 1
			  	AND item_no = '" . $item->item_no . "'
			  	AND ((minimum_quantity <= " . $quantity . " AND unit_of_measure_code = '" . $item->unit_of_measure_code . "')
					OR(minimum_quantity <= (" . $quantity . "*" . $item->multiplier . ") AND (unit_of_measure_code = '" . $item->nav_base_unit_code . "' OR unit_of_measure_code = '')))
			  	AND ((sales_type = 2)
			  		OR ((sales_type = 1) AND (sales_code='" . $customer->customer_price_group . "'))
			  		OR ((sales_type = 0) AND (sales_code = '" . $customer->customer_no . "'))
			  		OR ((sales_type = 3) AND (sales_code = '" . $this->shop->campain_no . "')))
			  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= '" . $curDate . "')
			  		OR (`shop_sales_price`.`starting_date` <= '" . $curDate . "') AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= '" . $curDate . "') AND (`shop_sales_price`.`ending_date` >= '" . $curDate . "'))
			  	" . $currencyConditions . "
			  	AND (variant_code = '" . $variantCode . "' OR variant_code = '')
			  	AND company = '" . $this->shop->company . "'
			  ORDER BY unit_price ASC
			  LIMIT 1";
        $this->db->setQuery($query);
        $this->db->doQuery();
        $resultCount = $this->db->getNoOfReturnedRows();
        $salesPrice  = array();
        if ($resultCount === 1) {
            $resultArray = $this->db->getResultArray();
            $salesPrice  = $resultArray[0];
            $priceData = new ItemPriceData($item->getID(), $this->shop->company, $item->item_no, $item->vat_prod_posting_group, $quantity, $salesPrice['unit_price'], $salesPrice['price_includes_vat'], $salesPrice['allow_line_disc'], $salesPrice['allow_invoice_disc'], $currencyCode);
            $priceData->setPriceSourcePriceLine();
            $priceData->setPriceSourceID($salesPrice['id']);
        } else {
            $salesPrice['unit_price']         = $item->base_price;
            $salesPrice['price_includes_vat'] = (bool)$this->shop->prices_including_vat;
            $salesPrice['allow_line_disc']    = TRUE;
            $salesPrice['allow_invoice_disc'] = ($item["allow_invoice_discount"] == 1);
            $priceData = new ItemPriceData($item->getID(), $this->shop->company, $item->item_no, $item->vat_prod_posting_group, $quantity, $salesPrice['unit_price'], $salesPrice['price_includes_vat'], $salesPrice['allow_line_disc'], $salesPrice['allow_invoice_disc'], $currencyCode);
            $priceData->setPriceSourceItemPrice();
            $priceData->setPriceSourceID($item->getID());
        }
        return $priceData;
    }

    /**
     * @param WebshopItem $item
     * @param $variantCode
     * @param $quantity
     * @param Customer $customer
     * @param $currencyCode
     * @return float
     */
    protected function _getBestLineDiscountAllTypesForItemNo(WebshopItem $item, $variantCode, $quantity, Customer $customer, $currencyCode ) {
        $query = "SELECT line_discount
			  FROM shop_sales_price
			  WHERE type = 1
			  	AND item_no = '" . $item->item_no . "'
			  	AND ((minimum_quantity <= " . $quantity . " AND unit_of_measure_code = '" . $item->unit_of_measure_code . "')
					OR(minimum_quantity <= (" . $quantity . "*" . $item->multiplier . ") AND (unit_of_measure_code = '" . $item->nav_base_unit_code . "' OR unit_of_measure_code = '')))
			  	AND ((sales_type = 2)
			  		OR ((sales_type = 1) AND (sales_code='" . $customer->customer_disc_group . "'))
			  		OR ((sales_type = 1) AND (sales_code='" . $this->shop->cust_disc_group_base_price . "'))
			  		OR ((sales_type = 0) AND (sales_code = '" . $customer->customer_no . "'))
			  		OR ((sales_type = 3) AND (sales_code = '" . $this->shop->campain_no . "')))
			  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
			  	AND (variant_code = '" . $variantCode . "' OR variant_code = '')
			  	AND (currency_code = '" . $currencyCode . "' OR currency_code = '')
			  	AND company = '" . $this->shop->company . "'
			  ORDER BY line_discount DESC
			  LIMIT 1";
        $this->db->setQuery($query);
        $this->db->doQuery();
        $resultCount  = $this->db->getNoOfReturnedRows();
        $lineDiscount = 0.00;
        if ($resultCount === 1) {
            $resultArray  = $this->db->getResultArray();
            $lineDiscount = (float)$resultArray[0][0];
        }
        return $lineDiscount;
    }

    /**
     * @param WebshopItem $item
     * @param $variantCode
     * @param $quantity
     * @param Customer $customer
     * @param $currencyCode
     * @return float
     */
    protected function _getBestLineDiscountAllTypesForItemDiscountGroup(WebshopItem $item, $variantCode, $quantity, Customer $customer, $currencyCode ) {

        $currencyConditions = $this->_getCurrencyQuerySnippet($currencyCode);
        $curDate            = new date('Y-m-d');

        $query = "SELECT line_discount
			  FROM shop_sales_price
			  WHERE type = 1
			  	AND discount_group = '" . $item->discount_group . "'
				AND item_no =''
			  	AND ((minimum_quantity <= " . $quantity . " AND unit_of_measure_code = '" . $item->unit_of_measure_code . "')
					OR(minimum_quantity <= (" . $quantity . "*" . $item->multiplier . ") AND (unit_of_measure_code = '" . $item->nav_base_unit_code . "' OR unit_of_measure_code = '')))
			  	AND ((sales_type = 2)
			  		OR ((sales_type = 1) AND (sales_code='" . $customer->customer_disc_group . "'))
			  		OR ((sales_type = 1) AND (sales_code='" . $this->shop->cust_disc_group_base_price . "'))
			  		OR ((sales_type = 0) AND (sales_code = '" . $customer->customer_no . "'))
			  		OR ((sales_type = 3) AND (sales_code = '" . $this->shop->campain_no . "')))
			  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= '" . $curDate . "')
			  		OR (`shop_sales_price`.`starting_date` <= '" . $curDate . "') AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= '" . $curDate . "') AND (`shop_sales_price`.`ending_date` >= '" . $curDate . "'))
			  	AND (variant_code = '" . $variantCode . "' OR variant_code = '')
			  	" . $currencyConditions . "
			  	AND company = '" . $this->shop->company . "'
			  ORDER BY line_discount DESC
			  LIMIT 1";
        $this->db->setQuery($query);
        $this->db->doQuery();
        $resultCount  = $this->db->getNoOfReturnedRows();
        $lineDiscount = 0.00;
        if ($resultCount === 1) {
            $resultArray  = $this->db->getResultArray();
            $lineDiscount = (float)$resultArray[0][0];
        }
        return $lineDiscount;
    }

    /**
     * @param WebshopItem $item
     * @param $variantCode
     * @param $quantity
     * @param Customer $customer
     * @param $currencyCode
     * @return ItemPriceData
     */
    protected function _getBestSalesPriceForCampaign(WebshopItem $item, $variantCode, $quantity, Customer $customer, $currencyCode ) {

        $currencyConditions = $this->_getCurrencyQuerySnippet($currencyCode);
        $curDate            = new date('Y-m-d');

        $query = "SELECT (CASE WHEN unit_of_measure_code = '" . $item->unit_of_measure_code . "' THEN unit_price
			   			WHEN unit_of_measure_code = '' OR unit_of_measure_code = '" . $item->nav_base_unit_code . "' THEN unit_price * " . $item->multiplier . "
			  			ELSE 9999999
		  	  		  END) AS 'unit_price',allow_line_disc,allow_invoice_disc,price_includes_vat
			  FROM shop_sales_price
			  WHERE type = 0
        AND item_no = '" . $item->item_no . "'
        AND ((minimum_quantity <= 1 AND unit_of_measure_code = '" . $item->unit_of_measure_code . "')
					OR(minimum_quantity <= (1*" . $item->multiplier . ") AND (unit_of_measure_code = '" . $item->nav_base_unit_code . "' OR unit_of_measure_code = '')))
			  	AND (sales_type = 3 AND sales_code = '" . $this->shop->campain_no . "')
			  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
            OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= '" . $curDate . "')
                OR (`shop_sales_price`.`starting_date` <= '" . $curDate . "') AND isnull(`shop_sales_price`.`ending_date`))
            OR (`shop_sales_price`.`starting_date` <= '" . $curDate . "') AND (`shop_sales_price`.`ending_date` >= '" . $curDate . "'))
			  	" . $currencyConditions . "
                AND (variant_code = '" . $variantCode . "' OR variant_code = '')
                AND company = '" . $this->shop->company . "'
        AND (unit_of_measure_code = '" . $item->unit_of_measure_code . "' OR unit_of_measure_code = '')
			  ORDER BY unit_price ASC
			  LIMIT 1";
        $this->db->setQuery($query);
        $this->db->doQuery();
        $resultCount = $this->db->getNoOfReturnedRows();
        $salesPrice  = array();
        if ($resultCount === 1) {
            $resultArray = $this->db->getResultArray();
            $salesPrice  = $resultArray[0];
        }
        $priceData = new ItemPriceData($item->getID(), $this->shop->company, $item->item_no, $item->vat_prod_posting_group, $quantity, $salesPrice['unit_price'], $salesPrice['price_includes_vat'], $salesPrice['allow_line_disc'], $salesPrice['allow_invoice_disc'], $currencyCode);
        return $priceData;
    }

    /**
     * @param WebshopItem $item
     * @param $variantCode
     * @param $quantity
     * @param Customer $customer
     * @param $currencyCode
     * @return ItemPriceData
     */
    protected function _getBestSalesPriceLineDiscAllowedForCampaign(WebshopItem $item, $variantCode, $quantity, Customer $customer, $currencyCode ) {

        $currencyConditions = $this->_getCurrencyQuerySnippet($currencyCode);
        $curDate            = new date('Y-m-d');

        $query = "SELECT (CASE WHEN unit_of_measure_code = '" . $item->unit_of_measure_code . "' THEN unit_price
			   			WHEN unit_of_measure_code = '' OR unit_of_measure_code = '" . $item->nav_base_unit_code . "' THEN unit_price * " . $item->multiplier . "
			  			ELSE 9999999
		  	  		  END) AS 'unit_price',allow_line_disc,allow_invoice_disc,price_includes_vat
			  FROM shop_sales_price
			  WHERE type = 0
        AND item_no = '" . $item->item_no . "'
        AND allow_line_disc = 1
        AND ((minimum_quantity <= 1 AND unit_of_measure_code = '" . $item->unit_of_measure_code . "')
					OR(minimum_quantity <= (1*" . $item->multiplier . ") AND (unit_of_measure_code = '" . $item->nav_base_unit_code . "' OR unit_of_measure_code = '')))
			  	AND (sales_type = 3 AND sales_code = '" . $this->shop->campain_no . "')
			  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
            OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= '" . $curDate . "')
                OR (`shop_sales_price`.`starting_date` <= '" . $curDate . "') AND isnull(`shop_sales_price`.`ending_date`))
            OR (`shop_sales_price`.`starting_date` <= '" . $curDate . "') AND (`shop_sales_price`.`ending_date` >= '" . $curDate . "'))
			  	" . $currencyConditions . "
                AND (variant_code = '" . $variantCode . "' OR variant_code = '')
                AND company = '" . $this->shop->company . "'
        AND (unit_of_measure_code = '" . $item->unit_of_measure_code . "' OR unit_of_measure_code = '')
			  ORDER BY unit_price ASC
			  LIMIT 1";
        $this->db->setQuery($query);
        $this->db->doQuery();
        $resultCount = $this->db->getNoOfReturnedRows();
        $salesPrice  = array();
        if ($resultCount === 1) {
            $resultArray = $this->db->getResultArray();
            $salesPrice  = $resultArray[0];
        }
        $priceData = new ItemPriceData($item->getID(), $this->shop->company, $item->item_no, $item->vat_prod_posting_group, $quantity, $salesPrice['unit_price'], $salesPrice['price_includes_vat'], $salesPrice['allow_line_disc'], $salesPrice['allow_invoice_disc'], $currencyCode);
        return $priceData;
    }

    /**
     * @param $currCode
     * @return string
     */
    protected function _getCurrencyQuerySnippet($currCode ) {
        if ($currCode === $this->shopLanguageDefaultCurrencyCode) {
            $currencyQuerySnippet = " AND (currency_code = '" . $currCode . "' OR currency_code = '') ";
        } else {
            $currencyQuerySnippet = " AND currency_code = '" . $currCode . "' ";
        }
        return $currencyQuerySnippet;
    }

    /**
     * @param ItemPriceData $priceData
     * @param $discountPercent
     * @return ItemPriceData
     */
    protected function _getPriceWithDiscountApplied(ItemPriceData $priceData, $discountPercent ) {
        $newPriceData = clone $priceData;
        $oldPrice = (float)$priceData->price;
        $discountPercent = (float)$discountPercent;
        if(round($discountPercent,2) === 100.00) {
            $newPrice = 0;
        } else {
            $newPrice = ($oldPrice / 100 * (100 - $discountPercent));
        }
        if((float)$newPrice !== (float)$oldPrice) {
            $newPriceData->setPrice($newPrice);
            $newPriceData->setDiscountAmount(abs($newPrice - $oldPrice));
            $newPriceData->setDiscountStatusLineDiscountApplied();
        }
        return $newPriceData;
    }

    /**
     * @param WebshopItem $item
     * @param $variantCode
     * @param $quantity
     * @param Customer $customer
     * @param $currencyCode
     * @return ItemPriceData
     */
    public function getItemCustomerPrice(WebshopItem $item, $variantCode, $quantity, Customer $customer, $currencyCode ) {

        //Get best overall Price
        $this->currentBestSalesPrice = $this->_getBestSalesPriceAllTypes($item,$variantCode,$quantity,$customer,$currencyCode);

        //If best overall price is from Price-Line and does not allow line discounts
        //then get best price which allows line discounts
        if(($this->currentBestSalesPrice->getPriceSource() === ItemPriceData::PRICE_SOURCE_PRICE_LINES) && !$this->currentBestSalesPrice->priceAllowsLineDiscount) {
            $this->currentBestSalesPriceDiscAllowed = $this->_getBestSalesPriceLineDiscAllowedAllTypes($item,$variantCode,$quantity,$currencyCode,$currencyCode);
            //If the best overall price is from a price line
            //but the best price allowing line discounts is from the item itself
            //meaning there are no price lines for the item which allow line discounts
            //then that price is discarded, because once an applicable price line exists
            //the price from the item itself becomes inapplicable, so we set it to 0
            if($this->currentBestSalesPriceDiscAllowed->getPriceSource() === ItemPriceData::PRICE_SOURCE_ITEM_PRICE) {
                $this->currentBestSalesPriceDiscAllowed->setPrice(0.00);
                $this->currentBestSalesPriceDiscAllowed->setPriceAllowsInvoiceDiscount(FALSE);
            }
        } elseif($this->currentBestSalesPrice->priceAllowsLineDiscount) {
            $this->currentBestSalesPriceDiscAllowed = &$this->currentBestSalesPrice;
        }

        //Adjust VAT to Shop-setting (add or subtract VAT if needed)
        $this->VATManager->doVATCalculationOnItemPrice($this->currentBestSalesPrice);
        $this->VATManager->doVATCalculationOnItemPrice($this->currentBestSalesPriceDiscAllowed);

        $returnPriceData = $this->currentBestSalesPrice;

        //Look for discounts applying to the item by item_no
        //If there is a valid (<>0) price allowing line-discounts
        //then apply the discount to it. If the result is smaller than the current best price
        //then set the price-to-be-returned to the new price with discount applied
        $bestLineDiscountPercentItem = $this->_getBestLineDiscountAllTypesForItemNo($item,$variantCode,$quantity,$currencyCode,$currencyCode);
        if($bestLineDiscountPercentItem > 0 && ($this->currentBestSalesPriceDiscAllowed->price <> 0.00)) {
            $this->currentBestSalesPriceItemDiscountApplied = $this->_getPriceWithDiscountApplied($this->currentBestSalesPriceDiscAllowed,$bestLineDiscountPercentItem);
            if($this->currentBestSalesPriceItemDiscountApplied->price < $this->currentBestSalesPrice->price) {
                $returnPriceData = $this->currentBestSalesPriceItemDiscountApplied;
            }
        }

        //Look for discounts applying to the item by the item's discount group
        //If there is a valid (<>0) price allowing line-discounts
        //then apply the discount to it. If the result is smaller than the current best price
        //then set the price-to-be-returned to the new price with discount applied
        $bestLineDiscountPercentItemGroup = $this->_getBestLineDiscountAllTypesForItemDiscountGroup($item,$variantCode,$quantity,$customer,$currencyCode);
        if($bestLineDiscountPercentItemGroup > 0 && ($this->currentBestSalesPriceDiscAllowed->price <> 0.00)) {
            $this->currentBestSalesPriceItemGroupDiscountApplied = $this->_getPriceWithDiscountApplied($this->currentBestSalesPriceDiscAllowed,$bestLineDiscountPercentItemGroup);
            if($this->currentBestSalesPriceItemGroupDiscountApplied->price < $returnPriceData->price) {
                $returnPriceData = $this->currentBestSalesPriceItemGroupDiscountApplied;
            }
        }

        return $returnPriceData;
    }

    /**
     * @param WebshopItem $item
     * @param $variantCode
     * @param Customer $customer
     * @param $currencyCode
     * @return mixed
     */
    public function getItemCrossPrice(WebshopItem $item, $variantCode, Customer $customer, $currencyCode ) {

        switch($this->shop-cross_price_typ) {
            case 1:
                return $item->retail_price;
                break;
            case 2:
                return $item->retail_price;

        }

    }

}