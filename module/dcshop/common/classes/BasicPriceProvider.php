<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\dcShop\interfaces\CustomerInterface;
use DynCom\dc\dcShop\interfaces\IVATManager;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 28.03.2015
 * Time: 13:56
 */
class BasicPriceProvider
{

    protected const QUERY_BEST_SALES_PRICE_ALL_TYPES =
        "SELECT id,(CASE WHEN unit_of_measure_code = :item_unit_of_measure_code THEN unit_price
						   WHEN unit_of_measure_code = '' OR unit_of_measure_code = :item_nav_base_unit_code THEN (`unit_price` * :multiplier)
						   ELSE 9999999
					  END) AS 'unit_price',allow_line_disc,allow_invoice_disc,price_includes_vat,vat_bus_posting_group
			  FROM shop_sales_price
			  WHERE type = 0
			  	AND (0 = :check_item_no OR item_no = :item_no)
			  	AND (0 = :check_line_discount_allowed OR allow_line_disc = 1)
			  	AND ((minimum_quantity <= :item_quantity AND unit_of_measure_code = :item_unit_of_measure_code)
					OR(minimum_quantity <= (:item_quantity * :multiplier) AND (unit_of_measure_code = :item_nav_base_unit_code OR unit_of_measure_code = '')))
			  	AND ((sales_type = 2)
			  		OR ((sales_type = 1) AND (sales_code= :customer_price_group))
			  		OR ((sales_type = 1) AND (sales_code= :shop_customer_price_group_base_price))
			  		OR ((sales_type = 0) AND (sales_code = :customer_no))
			  		OR ((sales_type = 3) AND (sales_code = :shop_campaign_no)))
			  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= :reference_date)
			  		OR (`shop_sales_price`.`starting_date` <= :reference_date) AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= :reference_date) AND (`shop_sales_price`.`ending_date` >= :reference_date))
			  	AND (
						(:currency_code = :shop_language_default_currency_code AND `currency_code` = '')
					OR 	currency_code = :currency_code
				)
			  	AND (variant_code = :item_variant_code OR variant_code = '')
			  	AND company = :shop_company
			  ORDER BY variant_code DESC, unit_price ASC
			  LIMIT 1";

    protected const QUERY_BEST_LINE_DISC_ALL_TYPES =
         "SELECT id,line_discount
			  FROM shop_sales_price
			  WHERE type = 1
			  	AND (0 = :check_item_no OR item_no = :item_no)
			  	AND (0 = :check_item_discount_group OR (discount_group = :item_discount_group AND discount_group != ''))
			  	AND ((minimum_quantity <= :item_quantity AND unit_of_measure_code = :item_unit_of_measure_code)
					OR(minimum_quantity <= (:item_quantity * :multiplier) AND (unit_of_measure_code = :item_nav_base_unit_code OR unit_of_measure_code = '')))
			  	AND ((sales_type = 2)
			  		OR ((sales_type = 1) AND (sales_code = :customer_discount_group))
			  		OR ((sales_type = 1) AND (sales_code = :shop_customer_discount_group_base_price))
			  		OR ((sales_type = 0) AND (sales_code = :customer_no))
			  		OR ((sales_type = 3) AND (sales_code = :shop_campaign_no)))
			  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= :reference_date)
			  		OR (`shop_sales_price`.`starting_date` <= :reference_date) AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= :reference_date) AND (`shop_sales_price`.`ending_date` >= :reference_date))
			  	AND (variant_code = :item_variant_code OR variant_code = '')
			  	AND (					
						(:currency_code = :shop_language_default_currency_code AND `currency_code` = '')
					OR 	currency_code = :currency_code
				)
			  	AND company = :shop_company
			  	AND line_discount > 0
			  ORDER BY variant_code DESC, line_discount DESC
			  LIMIT 1";



    protected $db;
    protected $shop;
    protected $vatManager;
    protected $shopLanguageDefaultCurrencyCode;

    /**
     * @param GenericDBQueryWrapperInterface $db
     * @param Shop $shop
     * @param IVATManager $vatManager
     * @param                                 $shopLanguageDefaultCurrencyCode
     */
    public function __construct(GenericDBQueryWrapperInterface $db, Shop $shop, IVATManager $vatManager, $shopLanguageDefaultCurrencyCode)
    {
        $this->db = $db;
        $this->shop = $shop;
        $this->vatManager = $vatManager;
        $this->shopLanguageDefaultCurrencyCode = $shopLanguageDefaultCurrencyCode;
    }

    /**
     * @param WebshopItemInterface $item
     * @param $quantity
     * @param CustomerInterface $customer
     * @param $currencyCode
     * @return ItemPriceData
     */
    public function getBestSalesPriceAllTypes(WebshopItemInterface $item, $quantity, CustomerInterface $customer, $currencyCode)
    {

        if (empty($item->multiplier)) {
            $multiplier = 1;
        } else {
            $multiplier = (float)$item->multiplier;
        }
        $priceDataCurrencyCode = $currencyCode ?: 'EUR';
        $customerNo = $this->getCustomerNoForPricing($customer);
        $curDate = date('Y-m-d');

        $this->db->setQuery(self::QUERY_BEST_SALES_PRICE_ALL_TYPES);

        $this->db->prepareQuery();
        $checkItemNo = 1;
        $checkLineDiscountAllowed = 0;

        $parameters = [
            [':item_unit_of_measure_code', (string)$item->unit_of_measure_code, \PDO::PARAM_STR],
            [':item_nav_base_unit_code', (string)$item->nav_base_unit_code, \PDO::PARAM_STR],
            [':multiplier', (string)$multiplier, \PDO::PARAM_STR], //PDO knows no float/decimal type
            [':check_item_no', $checkItemNo, \PDO::PARAM_INT],
            [':item_no', (string)$item->getItemNo(), \PDO::PARAM_STR],
            [':check_line_discount_allowed', (int)$checkLineDiscountAllowed, \PDO::PARAM_INT],
            [':item_quantity', (string)$quantity, \PDO::PARAM_STR], //PDO knows no float/decimal type
            [':customer_price_group', (string)$customer->customer_price_group, \PDO::PARAM_STR],
            [':shop_customer_price_group_base_price', (string)$this->shop->cust_price_group_base_price, \PDO::PARAM_STR],
            [':customer_no', (string)$customerNo, \PDO::PARAM_STR],
            [':shop_campaign_no', (string)$this->shop->campaign_no, \PDO::PARAM_STR],
            [':reference_date', (string)$curDate, \PDO::PARAM_STR], //SQL dates are always given as strings in queries - no extra data type
            [':currency_code', (string)$currencyCode, \PDO::PARAM_STR],
            [':shop_language_default_currency_code', (string)$this->shopLanguageDefaultCurrencyCode, \PDO::PARAM_STR],
            [':item_variant_code', (string)$item->getVariantCode(), \PDO::PARAM_STR],
            [':shop_company', (string)$this->shop->getCompany(), \PDO::PARAM_STR],
        ];

        $this->db->bindParameters($parameters);
        $query = $this->db->getQuery();
        $this->db->executePreparedStatement();
        $isInErrorState = $this->db->isErrorState();
        $error = $this->db->getErrorMessage();
        $resultArray = $this->db->getResultArray();
        $resultCount = count($resultArray);
        $salesPrice = array();
        if ($resultCount === 1) {
            $salesPrice = $resultArray[0];
            $vatProdPostingGroup = $item->getVATProdPostingGroup();
            $priceData = new ItemPriceData($item->getID(), $this->shop->company, $item->item_no, $vatProdPostingGroup, $quantity, $salesPrice['unit_price'], $salesPrice['price_includes_vat'], $salesPrice['allow_line_disc'], $salesPrice['allow_invoice_disc'], $priceDataCurrencyCode, $salesPrice['vat_bus_posting_group']);
            $priceData->setPriceSourcePriceLine();
            $priceData->setPriceSourceID($salesPrice['id']);
        } else {
            $salesPrice['unit_price'] = $item->base_price;
            $salesPrice['price_includes_vat'] = (bool)$this->shop->prices_including_vat;
            $salesPrice['allow_line_disc'] = TRUE;
            $salesPrice['allow_invoice_disc'] = ($item->allow_invoice_discount === 1);
            $vatProdPostingGroup = $item->getVATProdPostingGroup();
            $priceData = new ItemPriceData($item->getID(), $this->shop->company, $item->item_no, $vatProdPostingGroup, $quantity, $salesPrice['unit_price'], $salesPrice['price_includes_vat'], $salesPrice['allow_line_disc'], $salesPrice['allow_invoice_disc'], $priceDataCurrencyCode);
            $priceData->setPriceSourceItemPrice();
            $priceData->setPriceSourceID($item->id);
        }
        return $priceData;
    }


    /**
     * @param WebshopItemInterface $item
     * @param $quantity
     * @param CustomerInterface $customer
     * @param $currencyCode
     * @return ItemPriceData
     */
    public function getBestSalesPriceLineDiscAllowedAllTypes(WebshopItemInterface $item, $quantity, CustomerInterface $customer, $currencyCode)
    {

        $priceDataCurrencyCode = $currencyCode ?: 'EUR';
        $currencyConditions = $this->getCurrencyQuerySnippet($currencyCode);
        $curDate = date('Y-m-d');

        if (empty($item->multiplier) || (float)$item->multiplier <= 0) {
            $multiplier = 1;
        } else {
            $multiplier = $item->multiplier;
        }
        $customerNo = $this->getCustomerNoForPricing($customer);
        $curDate = date('Y-m-d');

        $this->db->setQuery(self::QUERY_BEST_SALES_PRICE_ALL_TYPES);
        $this->db->prepareQuery();
        $checkItemNo = 0;
        $checkLineDiscountAllowed = 1;

        $parameters = [
            [':item_unit_of_measure_code', (string)$item->unit_of_measure_code, \PDO::PARAM_STR],
            [':item_nav_base_unit_code', (string)$item->nav_base_unit_code, \PDO::PARAM_STR],
            [':multiplier', (string)$multiplier, \PDO::PARAM_STR], //PDO knows no float/decimal type
            [':check_item_no', $checkItemNo, \PDO::PARAM_INT],
            [':item_no', (string)$item->getItemNo(), \PDO::PARAM_STR],
            [':check_line_discount_allowed', (int)$checkLineDiscountAllowed, \PDO::PARAM_INT],
            [':item_quantity', (string)$quantity, \PDO::PARAM_STR], //PDO knows no float/decimal type
            [':customer_price_group', (string)$customer->customer_price_group, \PDO::PARAM_STR],
            [':shop_customer_price_group_base_price', (string)$this->shop->cust_price_group_base_price, \PDO::PARAM_STR],
            [':customer_no', (string)$customerNo, \PDO::PARAM_STR],
            [':shop_campaign_no', (string)$this->shop->campaign_no, \PDO::PARAM_STR],
            [':reference_date', (string)$curDate, \PDO::PARAM_STR], //SQL dates are always given as strings in queries - no extra data type
            [':currency_code', (string)$currencyCode, \PDO::PARAM_STR],
            [':shop_language_default_currency_code', (string)$this->shopLanguageDefaultCurrencyCode, \PDO::PARAM_STR],
            [':item_variant_code', (string)$item->getVariantCode(), \PDO::PARAM_STR],
            [':shop_company', (string)$this->shop->getCompany(), \PDO::PARAM_STR],
        ];

        $this->db->bindParameters($parameters);
        $this->db->executePreparedStatement();
        $resultArray = $this->db->getResultArray();
        $resultCount = count($resultArray);
        $salesPrice = array();
        if ($resultCount === 1) {
            $salesPrice = $resultArray[0];
            $priceData = new ItemPriceData($item->getID(), $this->shop->company, $item->item_no, $item->vat_prod_posting_group, $quantity, $salesPrice['unit_price'], $salesPrice['price_includes_vat'], $salesPrice['allow_line_disc'], $salesPrice['allow_invoice_disc'], $priceDataCurrencyCode, $salesPrice['vat_bus_posting_group']);
            $priceData->setPriceSourcePriceLine();
            $priceData->setPriceSourceID($salesPrice['id']);
        } else {
            $salesPrice['unit_price'] = $item->base_price;
            $salesPrice['price_includes_vat'] = (bool)$this->shop->prices_including_vat;
            $salesPrice['allow_line_disc'] = TRUE;
            $salesPrice['allow_invoice_disc'] = ($item->allow_invoice_discount == 1);
            $priceData = new ItemPriceData($item->getID(), $this->shop->company, $item->item_no, $item->vat_prod_posting_group, $quantity, $salesPrice['unit_price'], $salesPrice['price_includes_vat'], $salesPrice['allow_line_disc'], $salesPrice['allow_invoice_disc'], $priceDataCurrencyCode);
            $priceData->setPriceSourceItemPrice();
            $priceData->setPriceSourceID($item->id);
        }
        return $priceData;
    }

    /**
     * @param WebshopItemInterface $item
     * @param $quantity
     * @param CustomerInterface $customer
     * @param $currencyCode
     * @return GenericLineDiscount|int
     */
    public function getBestLineDiscountAllTypesForItemNo(WebshopItemInterface $item, $quantity, CustomerInterface $customer, $currencyCode)
    {

        if (empty($item->multiplier) || (float)$item->multiplier <= 0) {
            $multiplier = 1;
        } else {
            $multiplier = $item->multiplier;
        }
        $priceDataCurrencyCode = $currencyCode ?: 'EUR';
        $customerNo = $this->getCustomerNoForPricing($customer);
        $curDate = date('Y-m-d');

        $this->db->setQuery(self::QUERY_BEST_LINE_DISC_ALL_TYPES);
        $this->db->prepareQuery();

        $checkItemNo = 1;
        $itemNo = $item->getItemNo();
        $checkItemDiscountGroup = 0;
        $itemDiscountGroup = $item->getDiscountGroup();


        $parameters = [
            [':check_item_no', (int)$checkItemNo, \PDO::PARAM_INT],
            [':item_no', (string)$itemNo, \PDO::PARAM_STR],
            [':check_item_discount_group', (int)$checkItemDiscountGroup, \PDO::PARAM_INT],
            [':item_discount_group', (string)$itemDiscountGroup, \PDO::PARAM_STR],
            [':item_nav_base_unit_code', (string)$item->nav_base_unit_code, \PDO::PARAM_STR],
            [':item_quantity', (string)$quantity, \PDO::PARAM_STR], //PDO knows no float/decimal type
            [':item_unit_of_measure_code',(string)$item->unit_of_measure_code, \PDO::PARAM_STR],
            [':multiplier', (string)$multiplier, \PDO::PARAM_STR], //PDO knows no float/decimal type
            [':customer_discount_group', (string)$customer->customer_disc_group, \PDO::PARAM_STR],
            [':shop_customer_discount_group_base_price', (string)$this->shop->cust_disc_group_base_price, \PDO::PARAM_STR],
            [':customer_no', (string)$customerNo, \PDO::PARAM_STR],
            [':shop_campaign_no', (string)$this->shop->campaign_no, \PDO::PARAM_STR],
            [':reference_date', (string)$curDate, \PDO::PARAM_STR], //SQL dates are always given as strings in queries - no extra data type
            [':item_variant_code', (string)$item->getVariantCode(), \PDO::PARAM_STR],
            [':currency_code', (string)$currencyCode, \PDO::PARAM_STR],
            [':shop_language_default_currency_code', (string)$this->shopLanguageDefaultCurrencyCode, \PDO::PARAM_STR],
            [':shop_company', (string)$this->shop->getCompany(), \PDO::PARAM_STR],
        ];

        $this->db->bindParameters($parameters);
        $this->db->executePreparedStatement();
        $error = $this->db->getErrorMessage();
        $resultArray = $this->db->getResultArray();
        $resultCount = count($resultArray);
        if ($resultCount === 1) {
            $discountID = $resultArray[0][0];
            $lineDiscount = (float)$resultArray[0][1];
            $discObj = new GenericLineDiscount(GenericLineDiscount::DISCOUNT_SOURCE_TYPE_LINE_DISCOUNT, $discountID, $lineDiscount);
            return $discObj;
        }
        return 0;
    }

    /**
     * @param WebshopItemInterface $item
     * @param $quantity
     * @param CustomerInterface $customer
     * @param $currencyCode
     * @return GenericLineDiscount|int
     */
    public function getBestLineDiscountAllTypesForItemDiscountGroup(WebshopItemInterface $item, $quantity, CustomerInterface $customer, $currencyCode)
    {

        $currencyConditions = $this->getCurrencyQuerySnippet($currencyCode);
        $curDate = date('Y-m-d');

        if (empty($item->multiplier) || (float)$item->multiplier <= 0) {
            $multiplier = 1;
        } else {
            $multiplier = $item->multiplier;
        }
        $customerNo = $this->getCustomerNoForPricing($customer);
        $curDate = date('Y-m-d');

        $this->db->setQuery(self::QUERY_BEST_LINE_DISC_ALL_TYPES);
        $this->db->prepareQuery();

        $checkItemNo = 0;
        $itemNo = $item->getItemNo();
        $checkItemDiscountGroup = 1;
        $itemDiscountGroup = $item->getDiscountGroup();



        $parameters = [
            [':check_item_no', (int)$checkItemNo, \PDO::PARAM_INT],
            [':item_no', (string)$itemNo, \PDO::PARAM_STR],
            [':check_item_discount_group', (int)$checkItemDiscountGroup, \PDO::PARAM_INT],
            [':item_discount_group', (string)$itemDiscountGroup, \PDO::PARAM_STR],
            [':item_nav_base_unit_code', (string)$item->nav_base_unit_code, \PDO::PARAM_STR],
            [':item_quantity', (string)$quantity, \PDO::PARAM_STR], //PDO knows no float/decimal type
            [':item_unit_of_measure_code',(string)$item->unit_of_measure_code, \PDO::PARAM_STR],
            [':multiplier', (string)$multiplier, \PDO::PARAM_STR], //PDO knows no float/decimal type
            [':customer_discount_group', (string)$customer->customer_disc_group, \PDO::PARAM_STR],
            [':shop_customer_discount_group_base_price', (string)$this->shop->cust_disc_group_base_price, \PDO::PARAM_STR],
            [':customer_no', (string)$customerNo, \PDO::PARAM_STR],
            [':shop_campaign_no', (string)$this->shop->campaign_no, \PDO::PARAM_STR],
            [':reference_date', (string)$curDate, \PDO::PARAM_STR], //SQL dates are always given as strings in queries - no extra data type
            [':item_variant_code', (string)$item->getVariantCode(), \PDO::PARAM_STR],
            [':currency_code', (string)$currencyCode, \PDO::PARAM_STR],
            [':shop_language_default_currency_code', (string)$this->shopLanguageDefaultCurrencyCode, \PDO::PARAM_STR],
            [':shop_company', (string)$this->shop->getCompany(), \PDO::PARAM_STR],
        ];

        $this->db->bindParameters($parameters);
        $this->db->executePreparedStatement();
        $resultArray = $this->db->getResultArray();
        $resultCount = count($resultArray);
        if ($resultCount === 1) {
            $discountID = $resultArray[0][0];
            $lineDiscount = (float)$resultArray[0][1];
            $discObj = new GenericLineDiscount(GenericLineDiscount::DISCOUNT_SOURCE_TYPE_LINE_DISCOUNT, $discountID, $lineDiscount);
            return $discObj;
        }
        return 0;
    }

    /**
     * @param WebshopItemInterface $item
     * @param $currencyCode
     * @return ItemPriceData
     */
    public function getBestSalesPriceForCampaignAllTypes(WebshopItemInterface $item, $currencyCode)
    {

        $priceDataCurrencyCode = $currencyCode ?: 'EUR';
        $currencyConditions = $this->getCurrencyQuerySnippet($currencyCode);
        $curDate = date('Y-m-d');


        /** @noinspection PhpUndefinedFieldInspection */
        $query = <<<SQL
            SELECT
              id,
              (CASE WHEN unit_of_measure_code = '{$item->unit_of_measure_code}'
                THEN unit_price
               WHEN unit_of_measure_code = '' OR unit_of_measure_code = '{$item->nav_base_unit_code}'
                 THEN unit_price * {$item->multiplier}
               ELSE 9999999
               END) AS 'unit_price',
              allow_line_disc,
              allow_invoice_disc,
              price_includes_vat,
              vat_bus_posting_group
            FROM shop_sales_price
            WHERE type = 0
                  AND item_no = '{$item->item_no}'
                  AND ((minimum_quantity <= 1 AND unit_of_measure_code = '{$item->unit_of_measure_code}')
                       OR (minimum_quantity <= (1 * {$item->multiplier}) AND
                           (unit_of_measure_code = '{$item->nav_base_unit_code}' OR unit_of_measure_code = '')))
                  AND (sales_type = 3 AND sales_code = '{$this->shop->campain_no}')
                  AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
                       OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= '{$curDate}')
                           OR (`shop_sales_price`.`starting_date` <= '{$curDate}') AND isnull(`shop_sales_price`.`ending_date`))
                       OR (`shop_sales_price`.`starting_date` <= '{$curDate}') AND
                          (`shop_sales_price`.`ending_date` >= '{$curDate}'))
            $currencyConditions
            AND (variant_code = '{$item->getVariantCode()}' OR variant_code = '')
            AND company = '{$this->shop->company}'
            AND (unit_of_measure_code = '{$item->unit_of_measure_code}' OR unit_of_measure_code = '')
            ORDER BY unit_price ASC
            LIMIT 1
SQL;

        $this->db->setQuery($query);
        $this->db->doQuery();
        $resultCount = $this->db->getNoOfReturnedRows();
        $salesPrice = array();
        if ($resultCount === 1) {
            $resultArray = $this->db->getResultArray();
            $salesPrice = $resultArray[0];
            $priceData = new ItemPriceData($item->getID(), $this->shop->company, $item->item_no, $item->vat_prod_posting_group, $item->getMinQty(), $salesPrice['unit_price'], $salesPrice['price_includes_vat'], $salesPrice['allow_line_disc'], $salesPrice['allow_invoice_disc'], $priceDataCurrencyCode, $salesPrice['vat_bus_posting_group']);
            $priceData->setPriceSourcePriceLine();
            $priceData->setPriceSourceID($salesPrice['id']);
        } else {
            $salesPrice['unit_price'] = $item->base_price;
            $salesPrice['price_includes_vat'] = (bool)$this->shop->prices_including_vat;
            $salesPrice['allow_line_disc'] = TRUE;
            $salesPrice['allow_invoice_disc'] = ($item->allow_invoice_discount === 1);
            $priceData = new ItemPriceData($item->getID(), $this->shop->company, $item->item_no, $item->vat_prod_posting_group, $item->getMinQty(), $salesPrice['unit_price'], $salesPrice['price_includes_vat'], $salesPrice['allow_line_disc'], $salesPrice['allow_invoice_disc'], $priceDataCurrencyCode);
            $priceData->setPriceSourceItemPrice();
            $priceData->setPriceSourceID($item->id);
        }
        return $priceData;
    }

    /**
     * @param WebshopItemInterface $item
     * @param $currencyCode
     * @return ItemPriceData
     */
    public function getBestSalesPriceLineDiscAllowedForCampaign(WebshopItemInterface $item, $currencyCode)
    {

        $priceDataCurrencyCode = $currencyCode ?: 'EUR';
        $currencyConditions = $this->getCurrencyQuerySnippet($currencyCode);
        $curDate = date('Y-m-d');

        $query = "SELECT id,(CASE WHEN unit_of_measure_code = '" . $item->unit_of_measure_code . "' THEN unit_price
			   			WHEN unit_of_measure_code = '' OR unit_of_measure_code = '" . $item->nav_base_unit_code . "' THEN unit_price * " . $item->multiplier . "
			  			ELSE 9999999
		  	  		  END) AS 'unit_price',allow_line_disc,allow_invoice_disc,price_includes_vat, vat_bus_posting_group
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
                AND (variant_code = '" . $item->getVariantCode() . "' OR variant_code = '')
                AND company = '" . $this->shop->company . "'
        AND (unit_of_measure_code = '" . $item->unit_of_measure_code . "' OR unit_of_measure_code = '')
			  ORDER BY variant_code DESC, unit_price ASC
			  LIMIT 1";

        $this->db->setQuery($query);
        $this->db->doQuery();
        $resultCount = $this->db->getNoOfReturnedRows();
        $salesPrice = array();
        if ($resultCount === 1) {
            $resultArray = $this->db->getResultArray();
            $salesPrice = $resultArray[0];
            $priceData = new ItemPriceData($item->getID(), $this->shop->company, $item->item_no, $item->vat_prod_posting_group, $item->getMinQty(), $salesPrice['unit_price'], $salesPrice['price_includes_vat'], $salesPrice['allow_line_disc'], $salesPrice['allow_invoice_disc'], $priceDataCurrencyCode, $salesPrice['vat_bus_posting_group']);
            $priceData->setPriceSourcePriceLine();
            $priceData->setPriceSourceID($salesPrice['id']);
        } else {
            $salesPrice['unit_price'] = $item->base_price;
            $salesPrice['price_includes_vat'] = (bool)$this->shop->prices_including_vat;
            $salesPrice['allow_line_disc'] = TRUE;
            $salesPrice['allow_invoice_disc'] = ($item->allow_invoice_discount === 1);
            $priceData = new ItemPriceData($item->getID(), $this->shop->company, $item->item_no, $item->vat_prod_posting_group, $item->getMinQty(), $salesPrice['unit_price'], $salesPrice['price_includes_vat'], $salesPrice['allow_line_disc'], $salesPrice['allow_invoice_disc'], $priceDataCurrencyCode);
            $priceData->setPriceSourceItemPrice();
            $priceData->setPriceSourceID($item->id);
        }
        return $priceData;
    }

    /**
     * @param $currCode
     * @return string
     */
    public function getCurrencyQuerySnippet($currCode)
    {
        $code = '
        AND 
        (            
            (:currency_code = :shop_language_default_currency_code AND `currency_code` :currency_code)
            OR
            currency_code = \'\'
        )
        ';
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
    public function getPriceWithDiscountApplied(ItemPriceData $priceData, $discountPercent)
    {
        $newPriceData = clone $priceData;
        $oldPrice = (float)$priceData->price;
        $discountPercent = (float)$discountPercent;
        if (round($discountPercent, 2) === 100.00) {
            $newPrice = 0;
        } else {
            $newPrice = ($oldPrice / 100 * (100 - $discountPercent));
        }
        if ((float)$newPrice !== (float)$oldPrice) {
            $newPriceData->setPrice($newPrice);
            $newPriceData->setDiscountAmount(abs($newPrice - $oldPrice));
            $newPriceData->setDiscountStatusLineDiscountApplied();
        }
        return $newPriceData;
    }

    /**
     * @param CustomerInterface $customer
     * @return mixed
     */
    public function getCustomerNoForPricing(CustomerInterface $customer)
    {
        return ($customer->bill_to_customer_no) ?: $customer->customer_no;
    }

    /**
     * @param WebshopItemInterface $item
     * @param $currencyCode
     * @param $priceGroupCode
     * @return ItemPriceData
     */
    public function getPriceFromPriceGroup(WebshopItemInterface $item, $currencyCode, $priceGroupCode) {
        if(empty($item->multiplier)) {
            $multiplier = 1;
        } else {
            $multiplier = $item->multiplier;
        }
        $priceDataCurrencyCode = $currencyCode?:'EUR';
        $currencyConditions = $this->getCurrencyQuerySnippet($currencyCode);
        $quantity = 1;
        $curDate            = date('Y-m-d');


        $query              = "   SELECT (CASE WHEN unit_of_measure_code = '" . $item->unit_of_measure_code . "' THEN unit_price
                                   WHEN unit_of_measure_code = '' OR unit_of_measure_code = '" . $item->nav_base_unit_code . "' THEN unit_price * " . $item->multiplier . "
                                   ELSE 9999999 END) AS 'unit_price',allow_line_disc,allow_invoice_disc,price_includes_vat, vat_bus_posting_group
                                  FROM shop_sales_price
                                  WHERE type = 0
                                    AND item_no = '" . $item->item_no . "'
                                    AND ((minimum_quantity <= 1 AND unit_of_measure_code = '" . $item->unit_of_measure_code . "')
                                        OR(minimum_quantity <= (1*" . $multiplier . ") AND (unit_of_measure_code = '" . $item->nav_base_unit_code . "' OR unit_of_measure_code = '')))
                                    AND (sales_type = 1 AND sales_code='" . $priceGroupCode . "')
                                   AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
                                    OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= '" . $curDate . "')
                                    OR (`shop_sales_price`.`starting_date` <= '" . $curDate . "') AND isnull(`shop_sales_price`.`ending_date`))
                                    OR (`shop_sales_price`.`starting_date` <= '" . $curDate . "') AND (`shop_sales_price`.`ending_date` >= '" . $curDate . "'))
                                    " . $currencyConditions . "
                                    AND company = '" . $this->shop->company . "'
                                    AND (unit_of_measure_code = '" . $item->unit_of_measure_code . "' OR unit_of_measure_code = '')
                                  ORDER BY variant_code DESC, unit_price ASC
                                  LIMIT 1";

        $this->db->setQuery($query);
        $this->db->doQuery();
        $resultCount = $this->db->getNoOfReturnedRows();
        $salesPrice  = array();
        if ($resultCount === 1) {
            $resultArray = $this->db->getResultArray();
            $salesPrice  = $resultArray[0];
            $priceData = new ItemPriceData($item->getID(), $this->shop->company, $item->item_no, $item->vat_prod_posting_group, $quantity, $salesPrice['unit_price'], $salesPrice['price_includes_vat'], $salesPrice['allow_line_disc'], $salesPrice['allow_invoice_disc'], $priceDataCurrencyCode, $salesPrice['vat_bus_posting_group']);
            $priceData->setPriceSourcePriceLine();
            $priceData->setPriceSourceID($salesPrice['id']);
        } else {
            $salesPrice['unit_price']         = $item->base_price;
            $salesPrice['price_includes_vat'] = (bool)$this->shop->prices_including_vat;
            $salesPrice['allow_line_disc']    = TRUE;
            $salesPrice['allow_invoice_disc'] = ($item->allow_invoice_discount === 1);
            $priceData = new ItemPriceData($item->getID(), $this->shop->company, $item->item_no, $item->vat_prod_posting_group, $quantity, $salesPrice['unit_price'], $salesPrice['price_includes_vat'], $salesPrice['allow_line_disc'], $salesPrice['allow_invoice_disc'], $priceDataCurrencyCode);
            $priceData->setPriceSourceItemPrice();
            $priceData->setPriceSourceID($item->id);
        }
        return $priceData;
    }

    /**
     * @param WebshopItemInterface $item
     * @param $currencyCode
     * @param $priceGroupCode
     * @return ItemPriceData
     */
    public function getPriceFromPriceGroupAllowsLineDisc(WebshopItemInterface $item, $currencyCode, $priceGroupCode) {
        if(empty($item->multiplier)) {
            $multiplier = 1;
        } else {
            $multiplier = $item->multiplier;
        }
        $priceDataCurrencyCode = $currencyCode?:'EUR';
        $currencyConditions = $this->getCurrencyQuerySnippet($currencyCode);
        $quantity = 1;
        $curDate            = date('Y-m-d');


        $query              = "   SELECT (CASE WHEN unit_of_measure_code = '" . $item->unit_of_measure_code . "' THEN unit_price
                                   WHEN unit_of_measure_code = '' OR unit_of_measure_code = '" . $item->nav_base_unit_code . "' THEN unit_price * " . $item->multiplier . "
                                   ELSE 9999999 END) AS 'unit_price',allow_line_disc,allow_invoice_disc,price_includes_vat, vat_bus_posting_group
                                  FROM shop_sales_price
                                  WHERE type = 0
                                    AND allow_line_disc = 1
                                    AND item_no = '" . $item->item_no . "'
                                    AND ((minimum_quantity <= 1 AND unit_of_measure_code = '" . $item->unit_of_measure_code . "')
                                        OR(minimum_quantity <= (1*" . $multiplier . ") AND (unit_of_measure_code = '" . $item->nav_base_unit_code . "' OR unit_of_measure_code = '')))
                                    AND (sales_type = 1 AND sales_code='" . $priceGroupCode . "')
                                   AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
                                    OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= '" . $curDate . "')
                                    OR (`shop_sales_price`.`starting_date` <= '" . $curDate . "') AND isnull(`shop_sales_price`.`ending_date`))
                                    OR (`shop_sales_price`.`starting_date` <= '" . $curDate . "') AND (`shop_sales_price`.`ending_date` >= '" . $curDate . "'))
                                    " . $currencyConditions . "
                                    AND company = '" . $this->shop->company . "'
                                    AND (unit_of_measure_code = '" . $item->unit_of_measure_code . "' OR unit_of_measure_code = '')
                                  ORDER BY variant_code DESC, unit_price ASC
                                  LIMIT 1";

        $this->db->setQuery($query);
        $this->db->doQuery();
        $resultCount = $this->db->getNoOfReturnedRows();
        $salesPrice  = array();
        if ($resultCount === 1) {
            $resultArray = $this->db->getResultArray();
            $salesPrice  = $resultArray[0];
            $priceData = new ItemPriceData($item->getID(), $this->shop->company, $item->item_no, $item->vat_prod_posting_group, $quantity, $salesPrice['unit_price'], $salesPrice['price_includes_vat'], $salesPrice['allow_line_disc'], $salesPrice['allow_invoice_disc'], $priceDataCurrencyCode, $salesPrice['vat_bus_posting_group']);
            $priceData->setPriceSourcePriceLine();
            $priceData->setPriceSourceID($salesPrice['id']);
        } else {
            $salesPrice['unit_price']         = $item->base_price;
            $salesPrice['price_includes_vat'] = (bool)$this->shop->prices_including_vat;
            $salesPrice['allow_line_disc']    = TRUE;
            $salesPrice['allow_invoice_disc'] = ($item->allow_invoice_discount === 1);
            $priceData = new ItemPriceData($item->getID(), $this->shop->company, $item->item_no, $item->vat_prod_posting_group, $quantity, $salesPrice['unit_price'], $salesPrice['price_includes_vat'], $salesPrice['allow_line_disc'], $salesPrice['allow_invoice_disc'], $priceDataCurrencyCode);
            $priceData->setPriceSourceItemPrice();
            $priceData->setPriceSourceID($item->id);
        }
        return $priceData;
    }

    /**
     * @param WebshopItemInterface $item
     * @param $currencyCode
     * @param $discountGroupCode
     * @return float
     */
    public function getBestLineDiscountForItemNoWithDiscountGroup(WebshopitemInterface $item, $currencyCode, $discountGroupCode) {
        if(empty($item->multiplier) || (float)$item->multiplier <= 0) {
            $multiplier = 1;
        } else {
            $multiplier = $item->multiplier;
        }
        $quantity = 1;

        $query = "SELECT line_discount
			  FROM shop_sales_price
			  WHERE type = 1
			  	AND item_no = '" . $item->item_no . "'
			  	AND ((minimum_quantity <= " . $quantity . " AND unit_of_measure_code = '" . $item->unit_of_measure_code . "')
					OR(minimum_quantity <= (" . $quantity . "*" . $multiplier . ") AND (unit_of_measure_code = '" . $item->nav_base_unit_code . "' OR unit_of_measure_code = '')))
			  	AND ((sales_type = 1) AND (sales_code='" . $discountGroupCode . "'))
			  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
			  	AND (variant_code = '" . $item->getVariantCode() . "' OR variant_code = '')
			  	AND (currency_code = '" . $currencyCode . "' OR currency_code = '')
			  	AND company = '" . $this->shop->company . "'
			  	AND (unit_of_measure_code = '" . $item->unit_of_measure_code . "' OR unit_of_measure_code = '')
			  ORDER BY variant_code DESC, line_discount DESC
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
     * @param WebshopItemInterface $item
     * @param $currencyCode
     * @param $discountGroupCode
     * @return GenericLineDiscount
     */
    public function getBestLineDiscountForItemDiscGroupWithDiscountGroup(WebshopItemInterface $item, $currencyCode, $discountGroupCode) {
        if(empty($item->multiplier) || (float)$item->multiplier <= 0) {
            $multiplier = 1;
        } else {
            $multiplier = $item->multiplier;
        }
        $quantity = 1;

        $query = "SELECT line_discount
			  FROM shop_sales_price
			  WHERE type = 1
			    AND discount_group = '" . $item->discount_group . "'
			  	AND item_no = ''
			  	AND ((minimum_quantity <= " . $quantity . " AND unit_of_measure_code = '" . $item->unit_of_measure_code . "')
					OR(minimum_quantity <= (" . $quantity . "*" . $multiplier . ") AND (unit_of_measure_code = '" . $item->nav_base_unit_code . "' OR unit_of_measure_code = '')))
			  	AND ((sales_type = 1) AND (sales_code='" . $discountGroupCode . "'))
			  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
			  	AND (variant_code = '" . $item->getVariantCode() . "' OR variant_code = '')
			  	AND (currency_code = '" . $currencyCode . "' OR currency_code = '')
			  	AND company = '" . $this->shop->company . "'
			  	AND (unit_of_measure_code = '" . $item->unit_of_measure_code . "' OR unit_of_measure_code = '')
			  ORDER BY variant_code DESC, line_discount DESC
			  LIMIT 1";

        $this->db->setQuery($query);
        $this->db->doQuery();
        $resultCount  = $this->db->getNoOfReturnedRows();
		$discountID = 0;
        $lineDiscount = 0.00;
        if ($resultCount === 1) {
            $resultArray = $this->db->getResultArray();
            $discountID = $resultArray[0][0];
            $lineDiscount = (float)$resultArray[0][1];
        }
        $discObj = new GenericLineDiscount(GenericLineDiscount::DISCOUNT_SOURCE_TYPE_LINE_DISCOUNT, $discountID, $lineDiscount);
        return $discObj;
    }

    /**
     * @param $countryCode
     * @return array
     */
    public function getPriceAndDiscountGroupsForCountryCode($countryCode) {
        // Get Retail and Baseprice from sales_price
        $query  = "SELECT *
			  FROM shop_country
			  WHERE country_code = '" . $countryCode . "'
			  	AND company = '" . $this->shop->company . "'
			  	AND shop_code = '" . $this->shop->code . "'
			  	AND to_delete = 0
			  LIMIT 1";
        $this->db->setQuery($query);
        $this->db->doQuery();
        $resultCount  = $this->db->getNoOfReturnedRows();
        $priceGroups = [];
        if($resultCount === 1) {
            $resultArray = $this->db->getResultArray();
            $priceGroups['price_groups']['base_price'] = $resultArray[0]['pricegroup_baseprice'];
            $priceGroups['price_groups']['retail_price'] = $resultArray[0]['pricegroup_retailprice'];
            $priceGroups['disc_groups']['base_price'] = $resultArray[0]['discgroup_baseprice'];
            $priceGroups['disc_groups']['retail_price'] = $resultArray[0]['discgroup_retailprice'];
        }
        return $priceGroups;
    }

    /**
     * @param WebshopItemInterface $item
     * @param CustomerInterface $customer
     * @param $currencyCode
     * @return array
     */
    public function getGraduatedPricesMinQtys(WebshopItemInterface $item, CustomerInterface $customer, $currencyCode)
    {
        $query = <<<SQL
          SELECT minimum_quantity
		  FROM shop_sales_price
		  WHERE (
		    (
		      (
		            item_no = '{$item->getItemNo()}'
                AND (
                      (sales_type = 2)
                  OR  (
                        (sales_type = 1)
                    AND (sales_code = '{$customer->getCustomerPriceGroup()}')
                  )
                  OR  (
                        (sales_type = 0)
                    AND (sales_code = '{$customer->getCustomerNo()}')
                  )
                )
              )
              OR (
                    item_no = ''
                AND sales_type = 2
                AND discount_group = '{$item->getDiscountGroup()}'
              )
			)
            AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
			  	AND (variant_code = '{$item->getVariantCode()}' OR variant_code = '')
			  	AND (currency_code = '{$currencyCode}' OR currency_code = '')
			  	AND company = '{$this->shop->company}'
	    )
SQL;

        $this->db->setQuery($query);
        $this->db->doQuery();
        if ($this->db->getNoOfReturnedRows() > 0) {
            $resArr = $this->db->getResultArray();
            return $resArr;
        }
        return [['minimum_quantity'  => $item->getMinQty()]];
    }

}