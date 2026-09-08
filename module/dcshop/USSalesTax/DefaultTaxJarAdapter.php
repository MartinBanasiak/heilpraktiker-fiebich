<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 06.06.2017
 * Time: 16:20
 */

namespace DynCom\dc\dcShop\USSalesTax;


use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\dcShop\abstracts\DiscountBase;
use DynCom\dc\dcShop\classes\AppliedDiscount;
use DynCom\dc\dcShop\classes\BasketEntity;
use DynCom\dc\dcShop\classes\GenericInvoiceDiscount;
use DynCom\dc\dcShop\classes\VATManager;
use DynCom\dc\dcShop\interfaces\UserBasket;
use DynCom\dc\dcShop\USSalesTax\TaxJar\OrderLineItem;
use DynCom\dc\dcShop\USSalesTax\TaxJar\TaxBreakdown;
use DynCom\dc\dcShop\USSalesTax\TaxJar\TaxInformation;
use DynCom\dc\dcShop\USSalesTax\TaxJar\TaxJarRESTAPIConsumer;
use DynCom\dc\dcShop\USSalesTax\TaxJar\TaxLineItem;
use DynCom\dc\dcShop\USSalesTax\TaxJar\Transaction;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Whoops\Exception\ErrorException;

class DefaultTaxJarAdapter
{

    protected const QUERY_GET_TAX_INFO_FOR_BASKET_HEADER =
        '
        SELECT
            order_total,
            shipping,
            tax_source,
            freight_taxable,
            has_nexus,
            taxable_amount,
            combined_tax_rate,
            tax_collectable,
            state_taxable_amount,
            state_tax_rate,
            state_tax_collectable,
            county_taxable_amount,
            county_tax_rate,
            county_tax_collectable,
            city_taxable_amount,
            city_tax_rate,
            city_tax_collectable,
            special_district_taxable_amount,
            special_district_tax_rate,
            special_district_tax_collectable            
        FROM
          shop_order_us_sales_tax
        WHERE
          basket_header_id = :basket_header_id
        ';

    protected const QUERY_GET_TAX_INFO_FOR_ORDER_NO =
        '
        SELECT
            order_total,
            shipping,
            tax_source,
            freight_taxable,
            has_nexus,
            taxable_amount,
            combined_tax_rate,
            tax_collectable,
            state_taxable_amount,
            state_tax_rate,
            state_tax_collectable,
            county_taxable_amount,
            county_tax_rate,
            county_tax_collectable,
            city_taxable_amount,
            city_tax_rate,
            city_tax_collectable,
            special_district_taxable_amount,
            special_district_tax_rate,
            special_district_tax_collectable            
        FROM
          shop_order_us_sales_tax
        WHERE
          webshop_order_no = :webshop_order_no
        ';

    protected const QUERY_GET_TAX_INFO_FOR_BASKET_LINES =
        '
        SELECT
            taxable_amount,
            combined_tax_rate,
            tax_collectable,
            state_taxable_amount,
            state_tax_rate,
            state_tax_collectable,
            county_taxable_amount,
            county_tax_rate,
            county_tax_collectable,
            city_taxable_amount,
            city_tax_rate,
            city_tax_collectable,
            special_district_taxable_amount,
            special_district_tax_rate,
            special_district_tax_collectable
        FROM
          shop_order_us_sales_tax
        WHERE
              basket_header_id = :basket_header_id
          AND line_id = :basket_line_id
        ';

    protected const QUERY_SET_TAX_INFO_FOR_BASKET =
        '
        INSERT INTO
          shop_order_us_sales_tax
        SET
          company = :company,
          shop_code = :shop_code,
          language_code = :language_code,
          visitor_id = :visitor_id,
          basket_header_id = :basket_header_id,
          webshop_order_no = :webshop_order_no,
          line_id = :line_id,
          order_total = :order_total,
          tax_source = :tax_source,
          freight_taxable = :freight_taxable,
          has_nexus = :has_nexus,
          taxable_amount = :taxable_amount,
          combined_tax_rate = :combined_tax_rate,
          tax_collectable = :tax_collectable,
          state_taxable_amount = :state_taxable_amount,
          state_tax_rate = :state_tax_rate,
          state_tax_collectable = :state_tax_collectable,
          county_taxable_amount = :county_taxable_amount,
          county_tax_rate = :county_tax_rate,
          county_tax_collectable = :county_tax_collectable,
          city_taxable_amount = :city_taxable_amount,
          city_tax_rate = :city_tax_rate,
          city_tax_collectable = :city_tax_collectable,
          special_district_taxable_amount = :special_district_taxable_amount,
          special_district_tax_rate = :special_district_tax_rate,
          special_district_tax_collectable = :special_district_tax_collectable,
          transaction_id = :transaction_id,
          transaction_date = :transaction_date,
          transaction_reference_id = :transaction_reference_id
        ';

    protected const QUERY_UPDATE_TAX_HEADER_INFO_FOR_ORDER =
        '
        UPDATE shop_order_us_sales_tax
        SET 
          webshop_order_no = :webshop_order_no,
          basket_header_id = NULL 
        WHERE basket_header_id = :basket_header_id
        ';
    protected const QUERY_UPDATE_TAX_LINE_INFO_FOR_ORDER =
        '
        UPDATE shop_order_us_sales_tax
        SET
          line_id = :sales_line_id
        WHERE
              basket_header_id = :basket_header_id
          AND line_id = :basket_line_id
        ';

    protected const QUERY_GET_TAX_INFO_FOR_WEBSHOP_ORDER_NO =
        '
        SELECT
        *
        FROM
        shop_order_us_sales_tax
        WHERE
            company = :company
        AND shop_code = :shop_code
        AND webshop_order_no = :webshop_order_no
        ';

    protected const QUERY_GET_TAX_INFO_FOR_BASKET_ID =
        '
        SELECT
                *
        FROM
        shop_order_us_sales_tax
        WHERE
        basket_header_id = :basket_header_id
        ';

    protected const QUERY_SET_TRANSACTION_DATA_FOR_WEBSHOP_ORDER_NO =
        '
        UPDATE shop_order_us_sales_tax
        SET
          transaction_id = :transaction_id,
          transaction_date = :transaction_date,
          transaction_reference_id = :transaction_reference_id
        WHERE
              company = :company
          AND shop_code = :shop_code
          AND webshop_order_no = :webshop_order_no
        ';

    protected const QUERY_DELETE_TAX_INFO_FOR_BASKET =
        '
        DELETE FROM shop_order_us_sales_tax WHERE basket_header_id = :basket_header_id
        ';

    /**
     * @var TaxJarRESTAPIConsumer
     */
    protected $taxJarAPIConsumer;

    /**
     * @var VATManager
     */
    protected $vatManager;

    /**
     * @var PDOQueryWrapper
     */
    protected $shopPDO;

    /**
     * @var LoggerInterface
     */
    protected $log;

    /**
     * DefaultTaxJarAdapter constructor.
     * @param TaxJarRESTAPIConsumer $taxJarAPIConsumer
     * @param VATManager $vatManager
     */
    public function __construct(
        TaxJarRESTAPIConsumer $taxJarAPIConsumer,
        VATManager $vatManager,
        PDOQueryWrapper $shopPDO,
        ?LoggerInterface $logger = null
    ) {
        $this->taxJarAPIConsumer = $taxJarAPIConsumer;
        $this->vatManager = $vatManager;
        $this->shopPDO = $shopPDO;
        if (null === $logger) {
            $logger = new NullLogger();
        }
        $this->log = $logger;
    }


    public function getEstimatedTaxesForBasket(
        UserBasket $basket,
        Address $fromAddress,
        Address $toAddress,
        float $shippingCost
    ): ?TaxInformation
    {
        $this->log->info(
            'Getting Tax estimate for basket ID [' . $basket->getID() . '], from address [' . serialize(
                $fromAddress
            ) . '], to address [' . serialize($toAddress) . '], shipping cost [' . $shippingCost . '].'
        );
        $start = microtime(true);

        $lineItems = [];

        $appliedInvoiceDiscounts = $basket->getAppliedInvoiceDiscounts();
        $basketTotalNet = $basket->getBasketTotalNet();
        $appliedInvoiceDiscountAmount = 0.00;
        /**
         * @var $discount AppliedDiscount
         */
        foreach ($appliedInvoiceDiscounts as $discount) {
            $type = $discount->getType();
            if ($discount->getType() === DiscountBase::DISCOUNT_TYPE_INVOICE_DISCOUNT && !($discount->getSourceType(
                    ) === DiscountBase::DISCOUNT_SOURCE_TYPE_COUPON && $discount->getDiscountEvaluationType(
                    ) === GenericInvoiceDiscount::EVALUATION_TYPE_PAYMENT)
            ) {
                $appliedInvoiceDiscountAmount += $discount->getDiscountedAmount();
            }
        }
        $divisor = $basket->getTotalNoOfPos() ?: 1;
        $invoiceDiscountPerLine = $appliedInvoiceDiscountAmount / $divisor;

        /**
         * @var $basketItem BasketEntity
         */
        foreach ($basket as $basketItem) {
            $lineDiscountAmnt = 0.00;
            $lineDiscounts = $basketItem->getAppliedLineDiscounts();
            /**
             * @var $lineDiscount AppliedDiscount
             */
            foreach ($lineDiscounts as $lineDiscount) {
                $lineDiscountAmnt += $lineDiscount->getDiscountedAmount();
            }

            $unitPriceNet = $basketItem->getUnitPriceNet($this->vatManager);
            $unitPriceWOLineDiscounts = $unitPriceNet + abs($lineDiscountAmnt);

            $identifier = $basketItem->getIdentifier();
            if ($basketItem->getSubIdentifier()) {
                $identifier .= '|' . $basketItem->getSubIdentifier();
            }


            $lineItem = new OrderLineItem(
                $basket->getKey($basketItem),
                (int)$basketItem->getQuantity(),
                $identifier,
                null,
                $unitPriceWOLineDiscounts,
                $lineDiscountAmnt,
                null
            );
            $lineItems[] = $lineItem;
            $lineItemsJSON = \GuzzleHttp\json_encode($lineItems);
        }
        $this->log->info("Submitting with order total [$basketTotalNet], shipping cost [$shippingCost] and line items [$lineItemsJSON]");
        try {
            $taxInfo = $this->taxJarAPIConsumer->estimateTaxes(
                $fromAddress,
                null,
                $toAddress,
                $basketTotalNet,
                $shippingCost,
                $lineItems
            );
        } catch (\Throwable $t) {
            $this->log->error(
                'Tax estimation unsuccessful. Error code[' . $t->getCode() . '], message [' . $t->getMessage() . '].'
            );
            return null;
        }
        $duration = microtime(true) - $start;
        $this->log->info(
            'Successfully got estimate for basket ID [' . $basket->getID() . '] in [' . $duration . ']s: [' . serialize(
                $taxInfo
            ) . '].'
        );
        return $taxInfo;
    }

    public function createTransactionFromOrder(
        array $salesHeader,
        float $salesTaxAmount,
        array $salesLines,
        array $salesLineTaxAmounts,
        Address $fromAddress
    ): ?Transaction
    {
        $this->log->info(
            'Creating tax transaction for sales header id [' . $salesHeader['id'] . '] with lines [' . print_r($salesLines,1) . '], tax amount [' . $salesTaxAmount . '] and line amounts [' . serialize(
                $salesLineTaxAmounts
            ) . '].'
        );
        $start = microtime(true);

        $toAddressState = $this->getUSStateCodeFromZIP($salesHeader['ship_to_post_code']);

        $toAddress = new Address(
            $salesHeader['ship_to_country'],
            filter_var($salesHeader['ship_to_post_code'],FILTER_SANITIZE_NUMBER_INT),
            $toAddressState,
            $salesHeader['ship_to_city'],
            $salesHeader['ship_to_address']
        );
        $lineItems = [];
        foreach ($salesLines as $salesLine) {
            $taxAmount = (float)$salesLineTaxAmounts[$salesLine['id']];
            $lineItem = new OrderLineItem(
                (int)$salesLine['id'],
                (int)$salesLine['quantity'],
                $salesLine['description'],
                null,
                (float)$salesLine['unit_price'],
                0.00,
                $taxAmount
            );
            $lineItems[] = $lineItem;
        }
        $orderDate = $salesHeader['order_date'];
        $dt = \DateTimeImmutable::createFromFormat('Y-m-d', $salesHeader['order_date']);
        if (!$dt && !$orderDate) {
            $dt = new \DateTimeImmutable();
        }
        $transaction = new Transaction(
            (string)$salesHeader['id'],
            null,
            $dt,
            null,
            $fromAddress,
            $toAddress,
            round((float)$salesHeader['total'] - $salesTaxAmount,3),
            (float)$salesHeader['shipping_cost'],
            $salesTaxAmount,
            $lineItems
        );
        try {
            $transaction = $this->taxJarAPIConsumer->createTransaction(
                $transaction,
                TaxJarRESTAPIConsumer::TRANSACTION_TYPE_ORDER
            );
        } catch (\Throwable $t) {
            $this->log->error(
                'Could not create transaction. Error code [' . $t->getCode() . '], message [' . $t->getMessage() . '].'
            );
        }
        $duration = microtime(true) - $start;
        $this->log->info(
            'Successfully created tax transaction for sales header id [' . $salesHeader['id'] . '] in [' . $duration . ']s: [' . serialize(
                $transaction
            ) . ']'
        );
        return $transaction;
    }

    public function storeTaxInformationForBasket(
        TaxInformation $taxInformation,
        UserBasket $basket,
        PDOQueryWrapper $pdo
    ): void {
        $pdo->startTransaction();
        try {
            $company = $basket->getCompany();
            $shopCode = $basket->getShopCode();
            $languageCode = $basket->getShopLanguageCode();
            $visitorID = $basket->getVisitorID();
            $basketHeaderID = $basket->getID();
            $taxBreakdown = $taxInformation->getBreakdown();
            $shippingCost = $taxInformation->getShipping();
            $orderTotal = $taxInformation->getOrderTotalAmount();
            $lines = $taxBreakdown->getLineItems();


            $commonParams = [
                [':company', $company, \PDO::PARAM_STR],
                [':shop_code', $shopCode, \PDO::PARAM_STR],
                [':language_code', $languageCode, \PDO::PARAM_STR],
                [':visitor_id', $visitorID, \PDO::PARAM_INT],
                [':basket_header_id', $basketHeaderID, \PDO::PARAM_INT],
                [':webshop_order_no', null, \PDO::PARAM_STR],
                [':transaction_id', null, \PDO::PARAM_NULL],
                [':transaction_date', null, \PDO::PARAM_NULL],
                [':transaction_reference_id', null, \PDO::PARAM_NULL],
            ];

            $headerParamArray = [
                [':line_id', null, \PDO::PARAM_STR],
                [':order_total', $orderTotal, \PDO::PARAM_STR],
                [':tax_source', $taxInformation->getTaxSource(), \PDO::PARAM_STR],
                [':freight_taxable', $taxInformation->isFreightTaxable(), \PDO::PARAM_INT],
                [':has_nexus', $taxInformation->hasNexus(), \PDO::PARAM_INT],
                [':taxable_amount', $taxBreakdown->getTaxableAmount(), \PDO::PARAM_STR],
                [':combined_tax_rate', $taxBreakdown->getCombinedTaxRate(), \PDO::PARAM_STR],
                [':tax_collectable', $taxBreakdown->getTaxCollectable(), \PDO::PARAM_STR],
                [':state_taxable_amount', $taxBreakdown->getStateTaxableAmount(), \PDO::PARAM_STR],
                [':state_tax_rate', $taxBreakdown->getStateTaxRate(), \PDO::PARAM_STR],
                [':state_tax_collectable', $taxBreakdown->getStateTaxCollectable(), \PDO::PARAM_STR],
                [':county_taxable_amount', $taxBreakdown->getCountyTaxableAmount(), \PDO::PARAM_STR],
                [':county_tax_rate', $taxBreakdown->getCountyTaxRate(), \PDO::PARAM_STR],
                [':county_tax_collectable', $taxBreakdown->getCountyTaxCollectable(), \PDO::PARAM_STR],
                [':city_taxable_amount', $taxBreakdown->getCityTaxableAmount(), \PDO::PARAM_STR],
                [':city_tax_rate', $taxBreakdown->getCityTaxRate(), \PDO::PARAM_STR],
                [':city_tax_collectable', $taxBreakdown->getCityTaxCollectable(), \PDO::PARAM_STR],
                [':special_district_taxable_amount', $taxBreakdown->getSpecialDistrictTaxableAmount(), \PDO::PARAM_STR],
                [':special_district_tax_rate', $taxBreakdown->getSpecialTaxRate(), \PDO::PARAM_STR],
                [':special_district_tax_collectable', $taxBreakdown->getSpecialDistrictTaxCollectable(
                ), \PDO::PARAM_STR],
            ];

            $headerParamArray = array_merge($commonParams, $headerParamArray);
            $pdo->setQuery(self::QUERY_SET_TAX_INFO_FOR_BASKET)->prepareQuery();
            $pdo->bindParameters($headerParamArray);
            if (!$pdo->executePreparedStatement()) {
                $error = $pdo->getErrorMessage();
                throw new \ErrorException($error);
            }


            /**
             * @var $taxLineItem TaxLineItem
             */
            foreach ($lines as $taxLineItem) {
                $lineID = $taxLineItem->getId();
                $basketEntity = $basket->hasItemForKey($lineID) ? $basket->getItemByKey($lineID) : null;

                $lineParamArray = [
                    [':line_id', $lineID, \PDO::PARAM_STR],
                    [':order_total', null, \PDO::PARAM_STR],
                    [':taxable_amount', $taxLineItem->getTaxableAmount(), \PDO::PARAM_STR],
                    [':combined_tax_rate', $taxLineItem->getCombinedTaxRate(), \PDO::PARAM_STR],
                    [':tax_collectable', $taxLineItem->getTaxCollectable(), \PDO::PARAM_STR],
                    [':state_taxable_amount', $taxLineItem->getStateTaxableAmount(), \PDO::PARAM_STR],
                    [':state_tax_rate', $taxLineItem->getStateSalesTaxRate(), \PDO::PARAM_STR],
                    [':state_tax_collectable', $taxLineItem->getStateAmount(), \PDO::PARAM_STR],
                    [':county_taxable_amount', $taxLineItem->getCountyTaxableAmount(), \PDO::PARAM_STR],
                    [':county_tax_rate', $taxLineItem->getCountyTaxRate(), \PDO::PARAM_STR],
                    [':county_tax_collectable', $taxLineItem->getCountyAmount(), \PDO::PARAM_STR],
                    [':city_taxable_amount', $taxLineItem->getCityTaxableAmount(), \PDO::PARAM_STR],
                    [':city_tax_rate', $taxLineItem->getCityTaxRate(), \PDO::PARAM_STR],
                    [':city_tax_collectable', $taxLineItem->getCityAmount(), \PDO::PARAM_STR],
                    [':special_district_taxable_amount', $taxLineItem->getSpecialDistrictTaxableAmount(
                    ), \PDO::PARAM_STR],
                    [':special_district_tax_rate', $taxLineItem->getSpecialTaxRate(), \PDO::PARAM_STR],
                    [':special_district_tax_collectable', $taxLineItem->getSpecialDistrictAmount(), \PDO::PARAM_STR],
                    [':tax_source', null, \PDO::PARAM_STR],
                    [':freight_taxable', null, \PDO::PARAM_INT],
                    [':has_nexus', null, \PDO::PARAM_INT],
                ];
                $lineParamArray = array_merge($commonParams, $lineParamArray);
                $pdo->setQuery(self::QUERY_SET_TAX_INFO_FOR_BASKET)->prepareQuery();
                $pdo->bindParameters($lineParamArray);
                if (!$pdo->executePreparedStatement()) {
                    $error = $pdo->getErrorMessage();
                    throw new \ErrorException($error);
                }
            }
            $pdo->commitTransaction();
        } catch (\Throwable $t) {
            $pdo->rollbackTransaction();
            throw $t;
        }
    }

    public function updateStoredTaxInfoForOrder(
        string $webshopOrderNo,
        int $basketHeaderID,
        array $basketLineKeySalesLineIDMap
    ): void {
        $this->log->info('Updating stored tax info for header id [' . $basketHeaderID . '] to webshop order [' . $webshopOrderNo . '] with basket->salesline map [' . serialize($basketLineKeySalesLineIDMap) . '].');

        if (!$this->shopPDO->startTransaction()) {
            $this->log->error('Shop PDO error [' . $this->shopPDO->getErrorMessage() . '].');
            throw new \ErrorException($this->shopPDO->getErrorMessage());
        }

        foreach ($basketLineKeySalesLineIDMap as $basketLineKey => $salesLineID) {
            $lineParamArray = [
                [':basket_header_id', $basketHeaderID, \PDO::PARAM_INT],
                [':basket_line_id', $basketLineKey, \PDO::PARAM_STR],
                [':sales_line_id', $salesLineID, \PDO::PARAM_STR],
            ];
            $this->shopPDO->setQuery(self::QUERY_UPDATE_TAX_LINE_INFO_FOR_ORDER)->prepareQuery();
            $this->shopPDO->bindParameters($lineParamArray);
            $this->shopPDO->executePreparedStatement();
            if (!$this->shopPDO->executePreparedStatement() || $this->shopPDO->isErrorState()) {
                $this->log->error('Shop PDO error [' . $this->shopPDO->getErrorMessage() . '].');
                throw new \ErrorException($this->shopPDO->getErrorMessage());
            }
        }

        $headerParamArray = [
            [':webshop_order_no', $webshopOrderNo, \PDO::PARAM_STR],
            [':basket_header_id', $basketHeaderID, \PDO::PARAM_INT],
        ];
        $this->shopPDO->setQuery(self::QUERY_UPDATE_TAX_HEADER_INFO_FOR_ORDER)->prepareQuery();
        $this->shopPDO->bindParameters($headerParamArray);
        if (!$this->shopPDO->executePreparedStatement() || $this->shopPDO->isErrorState()) {
            $this->log->error('Shop PDO error [' . $this->shopPDO->getErrorMessage() . '].');
            throw new \ErrorException($this->shopPDO->getErrorMessage());
        }

        if (!$this->shopPDO->commitTransaction() || $this->shopPDO->isErrorState()) {
            $this->log->error('Shop PDO error [' . $this->shopPDO->getErrorMessage() . '].');
            throw new \ErrorException($this->shopPDO->getErrorMessage());
        }
        $this->log->info('Done updating stored tax info.');
    }

    public function getTaxBreakdownByBasketHeaderID(int $basketHeaderID): ?TaxBreakdown
    {
        $paramArr = [
            [':basket_header_id', $basketHeaderID, \PDO::PARAM_INT],
        ];
        $this->shopPDO->setQuery(self::QUERY_GET_TAX_INFO_FOR_BASKET_HEADER)->prepareQuery();
        $this->shopPDO->bindParameters($paramArr);
        $this->shopPDO->executePreparedStatement();
        $result = $this->shopPDO->getResultArray();

        if (count($result) > 0) {


            $headerRow = [];
            $lines = [];
            foreach ($result as $row) {
                if (!($row['line_id'])) {
                    $headerRow = $row;
                } else {
                    $line = new TaxLineItem(
                        (string)$row['line_id'],
                        (float)$row['taxable_amount'],
                        (float)$row['tax_collectable'],
                        (float)$row['combined_tax_rate'],
                        (float)$row['state_taxable_amount'],
                        (float)$row['state_tax_rate'],
                        (float)$row['state_tax_collectable'],
                        (float)$row['county_taxable_amount'],
                        (float)$row['county_tax_rate'],
                        (float)$row['county_tax_collectable'],
                        (float)$row['city_taxable_amount'],
                        (float)$row['city_tax_rate'],
                        (float)$row['city_tax_collectable'],
                        (float)$row['special_district_taxable_amount'],
                        (float)$row['special_district_tax_rate'],
                        (float)$row['special_district_tax_collectable']
                    );
                    $lines[] = $line;
                }
            }
            $breakdown = new TaxBreakdown(
                (float)$headerRow['taxable_amount'],
                (float)$headerRow['tax_collectable'],
                (float)$headerRow['combined_tax_rate'],
                (float)$headerRow['state_taxable_amount'],
                (float)$headerRow['state_tax_rate'],
                (float)$headerRow['state_tax_collectable'],
                (float)$row['county_taxable_amount'],
                (float)$row['county_tax_rate'],
                (float)$row['county_tax_collectable'],
                (float)$headerRow['city_taxable_amount'],
                (float)$headerRow['city_tax_rate'],
                (float)$headerRow['city_tax_collectable'],
                (float)$headerRow['special_district_taxable_amount'],
                (float)$headerRow['special_district_tax_rate'],
                (float)$headerRow['special_district_tax_collectable'],
                $lines
            );
            return $breakdown;
        }
        return null;
    }

    public function createTransactionAndUpdateTaxDataFromDB(
        array $salesHeader,
        array $salesLines,
        Address $fromAddress
    ): Transaction {
        $this->log->info('Creating transaction and updating tax data from db for sales header id [' . $salesHeader['id'] . '] with lines [' . print_r($salesLines,1) . '].');
        $start = microtime(true);
        $company = $salesHeader['company'];
        $shopCode = $salesHeader['shop_code'];
        $webshopOrderNo = $salesHeader['order_no'];

        $paramArr = [
            [':company', $company, \PDO::PARAM_STR],
            [':shop_code', $shopCode, \PDO::PARAM_STR],
            [':webshop_order_no', $webshopOrderNo, \PDO::PARAM_STR],
        ];

        $this->shopPDO->setQuery(self::QUERY_GET_TAX_INFO_FOR_WEBSHOP_ORDER_NO)->prepareQuery();
        $this->shopPDO->bindParameters($paramArr);
        $this->shopPDO->executePreparedStatement();
        $result = $this->shopPDO->getResultArray();

        $orderTaxAmount = 0;
        $taxByLine = [];
        $headerRow = [];
        $lines = [];
        $this->log->info('Tax info from db is [' . print_r($result,1) . '].');
        foreach ($result as $row) {
            if (!($row['line_id'])) {
                $headerRow = $row;
                $orderTaxAmount = (float)$row['tax_collectable'];
            } else {
                $line = new TaxLineItem(
                    (string)$row['line_id'],
                    (float)$row['taxable_amount'],
                    (float)$row['tax_collectable'],
                    (float)$row['combined_tax_rate'],
                    (float)$row['state_taxable_amount'],
                    (float)$row['state_tax_rate'],
                    (float)$row['state_tax_collectable'],
                    (float)$row['county_taxable_amount'],
                    (float)$row['county_tax_rate'],
                    (float)$row['county_tax_collectable'],
                    (float)$row['city_taxable_amount'],
                    (float)$row['city_tax_rate'],
                    (float)$row['city_tax_collectable'],
                    (float)$row['special_district_taxable_amount'],
                    (float)$row['special_district_tax_rate'],
                    (float)$row['special_district_tax_collectable']
                );
                $lines[] = $line;
                $taxByLine[(int)($row['line_id'])] = (float)$row['tax_collectable'];
            }
        }
        $breakdown = new TaxBreakdown(
            (float)$headerRow['taxable_amount'],
            (float)$headerRow['tax_collectable'],
            (float)$headerRow['combined_tax_rate'],
            (float)$headerRow['state_taxable_amount'],
            (float)$headerRow['state_tax_rate'],
            (float)$headerRow['state_tax_collectable'],
            (float)$row['county_taxable_amount'],
            (float)$row['county_tax_rate'],
            (float)$row['county_tax_collectable'],
            (float)$headerRow['city_taxable_amount'],
            (float)$headerRow['city_tax_rate'],
            (float)$headerRow['city_tax_collectable'],
            (float)$headerRow['special_district_taxable_amount'],
            (float)$headerRow['special_district_tax_rate'],
            (float)$headerRow['special_district_tax_collectable'],
            $lines
        );

        $transaction = $this->createTransactionFromOrder(
            $salesHeader,
            $orderTaxAmount,
            $salesLines,
            $taxByLine,
            $fromAddress
        );
        $this->setTransactionInfoForOrderTaxInfo(
            $company,
            $shopCode,
            $webshopOrderNo,
            $transaction->getTransactionID(),
            $transaction->getTransactionDate(),
            $transaction->getTransactionReferenceID()
        );
        $duration = microtime(true) - $start;
        $this->log->info('Done creating and updating tax info for sales header [' . $salesHeader['id'] . '] in [' . $duration . ']s.');
        return $transaction;
    }

    public function setTransactionInfoForOrderTaxInfo(
        string $company,
        string $shopCode,
        string $webshopOrderNo,
        string $transactionID,
        \DateTimeImmutable $transactionDate,
        ?string $transactionReferenceID = null
    ): void {
        $paramArr = [
            [':company', $company, \PDO::PARAM_STR],
            [':shop_code', $shopCode, \PDO::PARAM_STR],
            [':webshop_order_no', $webshopOrderNo, \PDO::PARAM_STR],
            [':transaction_id', $transactionID, \PDO::PARAM_STR],
            [':transaction_date',$transactionDate->format('Y-m-d H:i:s'),\PDO::PARAM_STR],
            [':transaction_reference_id', $transactionReferenceID, \PDO::PARAM_INT],
        ];
        $this->shopPDO->setQuery(self::QUERY_SET_TRANSACTION_DATA_FOR_WEBSHOP_ORDER_NO)->prepareQuery();
        $this->shopPDO->bindParameters($paramArr);
        $this->shopPDO->executePreparedStatement();
    }

    public function deleteTaxInfoForBasketHeaderID(int $basketHeaderID): void
    {
        $this->shopPDO->setQuery(self::QUERY_DELETE_TAX_INFO_FOR_BASKET)->prepareQuery();
        $paramArr = [
            [':basket_header_id', $basketHeaderID, \PDO::PARAM_INT],
        ];
        $this->shopPDO->bindParameters($paramArr);
        $this->shopPDO->executePreparedStatement();
    }

    public function getUSStateCodeFromZIP(string $zip): string
    {
        $start = microtime(true);
        $this->log->info('Getting US State Code for zip [' . $zip . '].');
        $endpoint = 'http://api.zippopotam.us/us/' . (int)$zip;
        $client = new Client();
        $zip = filter_var($zip,FILTER_SANITIZE_NUMBER_INT);

        $stateCode = '';

        try {
            $response = $client->get($endpoint);
            $responseCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();

            if (200 <= $responseCode && 300 > $responseCode) {
                $arr = \GuzzleHttp\json_decode($responseBody, true);
                $stateCode = $arr['places'][0]['state abbreviation'];
            }
        } catch (\Throwable $t) {
            $this->log->error(
                'Could not get State Code for zip [' . $zip . ']. Error code [' . $t->getCode(
                ) . '], message [' . $t->getMessage() . '].'
            );
            return $stateCode;
        }
        $duration = microtime(true) - $start;
        $this->log->info(
            'State code for zip [' . $zip . '] is [' . $stateCode . ']. Execution took [' . $duration . '] s.'
        );
        return $stateCode;
    }

    public function getTaxBreakdownByWebshopOrderNo(string $webshopOrderNo): ?TaxBreakdown
    {
        $paramArr = [
            [':webshop_order_no', $webshopOrderNo, \PDO::PARAM_STR],
        ];
        $this->shopPDO->setQuery(self::QUERY_GET_TAX_INFO_FOR_ORDER_NO)->prepareQuery();
        $this->shopPDO->bindParameters($paramArr);
        $this->shopPDO->executePreparedStatement();
        $result = $this->shopPDO->getResultArray();

        if (count($result) > 0) {


            $headerRow = [];
            $lines = [];
            foreach ($result as $row) {
                if (!($row['line_id'])) {
                    $headerRow = $row;
                } else {
                    $line = new TaxLineItem(
                        (string)$row['line_id'],
                        (float)$row['taxable_amount'],
                        (float)$row['tax_collectable'],
                        (float)$row['combined_tax_rate'],
                        (float)$row['state_taxable_amount'],
                        (float)$row['state_tax_rate'],
                        (float)$row['state_tax_collectable'],
                        (float)$row['county_taxable_amount'],
                        (float)$row['county_tax_rate'],
                        (float)$row['county_tax_collectable'],
                        (float)$row['city_taxable_amount'],
                        (float)$row['city_tax_rate'],
                        (float)$row['city_tax_collectable'],
                        (float)$row['special_district_taxable_amount'],
                        (float)$row['special_district_tax_rate'],
                        (float)$row['special_district_tax_collectable']
                    );
                    $lines[] = $line;
                }
            }
            $breakdown = new TaxBreakdown(
                (float)$headerRow['taxable_amount'],
                (float)$headerRow['tax_collectable'],
                (float)$headerRow['combined_tax_rate'],
                (float)$headerRow['state_taxable_amount'],
                (float)$headerRow['state_tax_rate'],
                (float)$headerRow['state_tax_collectable'],
                (float)$row['county_taxable_amount'],
                (float)$row['county_tax_rate'],
                (float)$row['county_tax_collectable'],
                (float)$headerRow['city_taxable_amount'],
                (float)$headerRow['city_tax_rate'],
                (float)$headerRow['city_tax_collectable'],
                (float)$headerRow['special_district_taxable_amount'],
                (float)$headerRow['special_district_tax_rate'],
                (float)$headerRow['special_district_tax_collectable'],
                $lines
            );
            return $breakdown;
        }
        return null;
    }

}