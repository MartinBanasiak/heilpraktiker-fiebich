<?php
/**
 * Created by PhpStorm.
 * User: alsotohy
 * Date: 26.01.2018
 * Time: 13:26
 */

namespace DynCom\dc\dcShop\Document;

use DynCom\dc\dcShop\classes\CountryRepository;
use DynCom\dc\dcShop\classes\Customer;
use DynCom\dc\dcShop\classes\WebshopItemRepository;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;
use DynCom\dc\regionalization\RegionalizedTextProvider;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use DynCom\dc\common\classes\PDOQueryWrapper;

use DynCom\dc\dcShop\classes\WebshopItemVariantService;


class DocumentArchiveContoller
{

    protected $webshopItemRepository;
    protected $webshopOrderRepository;
    protected $invoiceRepository;
    protected $shipmentRepository;
    protected $navOrderRepository;
    protected $creditMemoRepository;


    protected const FIELD_NAME_DATE_RANGE = 'document_daterange';
    protected const FIELD_NAME_DOCUMENT_SEARCH_INPUT = 'document_search_input';
    protected const PARAMETER_NAME_PAGE_NUMBER = 'page_value';

    protected const FIELD_NAME_LAST_SEARCH_DATE_RANGE = 'document_last_search_date_range';
    protected const FIELD_NAME_LAST_DOCUMENT_SEARCH_INPUT = 'document_last_search_input';
    protected const FIELD_NAME_GENERATE_DOCUMENT_PDF = 'generate_document_pdf';
    protected const FIELD_NAME_REQUEST_DOCUMENT_PDF = 'request_document_pdf';
    protected const FIELD_NAME_VIEW_DOCUMENT_CARD_FORM = 'view_document_card';
    protected const FIELD_NAME_DOCUMENT_TYPE_FILTER_VALUE = 'document_type_filter';
    protected const FIELD_NAME_DOCUMENT_TYPE_VALUE = 'document_type';
    protected const FIELD_NAME_DOCUMENT_NO_VALUE = 'document_no';
    protected const FIELD_NAME_REQUEST_MAIL_PDF = 'request_mail_pdf';

    protected const TEMPLATE_NAME_WEBORDERS_ARCHIVE = 'webshop_orders_archive.mustache';
    protected const TEMPLATE_NAME_DOCUMENT_ARCHIVE = 'document_archive.mustache';

    protected $templateDirectory;
    protected $mustacheEngine;
    protected $textProvider;
    protected $logger;

    /**
     * @var WebshopItemVariantService
     */
    private $webshopItemVariantService;
    private $pdo;
    /**
     * @var CountryRepository
     */
    private $countryRepository;


    /**
     * DocumentArchiveContoller constructor.
     * @param PDOQueryWrapper $pdo
     * @param WebshopOrderRepository $webshopOrderRepository
     * @param InvoiceRepository $invoiceRepository
     * @param ShipmentRepository $shipmentRepository
     * @param NavOrderRepository $navOrderRepository
     * @param CreditMemoRepository $creditMemoRepository
     * @param WebshopItemRepository $webshopItemRepository
     * @param WebshopItemVariantService $webshopItemVariantService
     * @param CountryRepository $countryRepository
     * @param LoggerInterface $logger
     * @param RegionalizedTextProvider $textProvider
     * @internal param WebshopItemVariantService $ebshopItemVariantService
     */
    public function __construct(
        PDOQueryWrapper $pdo, WebshopOrderRepository $webshopOrderRepository = null, InvoiceRepository $invoiceRepository = null, ShipmentRepository $shipmentRepository = null, NavOrderRepository $navOrderRepository = null, CreditMemoRepository $creditMemoRepository = null, WebshopItemRepository $webshopItemRepository, WebshopItemVariantService $webshopItemVariantService, CountryRepository $countryRepository, LoggerInterface $logger = null, RegionalizedTextProvider $textProvider = null
    )
    {
        $this->webshopItemRepository = $webshopItemRepository;
        $this->webshopOrderRepository = $webshopOrderRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->navOrderRepository = $navOrderRepository;
        $this->creditMemoRepository = $creditMemoRepository;
        $this->webshopItemVariantService = $webshopItemVariantService;
        $this->pdo = $pdo;


        $this->templateDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'templates';
        $mustacheOptions = [
            'loader' => new \Mustache_Loader_FilesystemLoader($this->templateDirectory),
        ];
        $this->mustacheEngine = new \Mustache_Engine($mustacheOptions);
        $this->textProvider = $textProvider;
        $logger = $logger ?? new NullLogger();
        $this->logger = $logger;
        $this->webshopItemRepository = $webshopItemRepository;
        $this->countryRepository = $countryRepository;
    }

    /**
     * @param ServerRequestInterface $request
     * @param string $company
     * @param Customer $customerNo
     * @return string
     */


    public function setOrdersRelatedDocuments($webshopOrders, $defaultCurrencyCode)
    {
        foreach ($webshopOrders as $webshopOrder) {
            $relatedDocuments = array();

            $currencyCode = $webshopOrder->currency_code;
            if ($currencyCode == '') {
                $webshopOrder->setCurrencyCode($defaultCurrencyCode);
                $currencyCode = $defaultCurrencyCode;
            }
            $webshopOrder->setDocumentTypeText($this->textProvider->getRegionalizedText(\DynCom\dc\dcShop\Document\WebshopOrderDocument::DOCUMENT_TYPE));




           // $orderContacts = $this->invoiceRepository->getInvoiceByWebshopOrderNo($webshopOrder->company, $webshopOrder->customer_no, $webshopOrder->order_no);

            $orderContacts = $this->navOrderRepository->getNavOrderByWebshopOrderNo($webshopOrder->company, $webshopOrder->customer_no, $webshopOrder->order_no);
            foreach ($orderContacts as $orderContact) {

                $orderContact->setCurrencyCode($currencyCode);
                $orderContact->setDocumentTypeText($this->textProvider->getRegionalizedText(\DynCom\dc\dcShop\Document\NavOrderDocument::DOCUMENT_TYPE));
                $relatedDocuments[count($relatedDocuments)] = $orderContact;
            }


            $orderInvoices = $this->invoiceRepository->getInvoiceByWebshopOrderNo($webshopOrder->company, $webshopOrder->customer_no, $webshopOrder->order_no);
            foreach ($orderInvoices as $invoice) {

                $invoice->setCurrencyCode($currencyCode);
                $invoice->setDocumentTypeText($this->textProvider->getRegionalizedText(\DynCom\dc\dcShop\Document\InvoiceDocument::DOCUMENT_TYPE));
                $relatedDocuments[count($relatedDocuments)] = $invoice;
            }


            $orderShipments = $this->shipmentRepository->getShipmentByWebshopOrderNo($webshopOrder->company, $webshopOrder->customer_no, $webshopOrder->order_no);
            // $relatedDocuments[count($relatedDocuments)] = $orderShipments;

            foreach ($orderShipments as $shipment) {
                $shipment->setCurrencyCode($currencyCode);
                $shipment->setDocumentTypeText($this->textProvider->getRegionalizedText(\DynCom\dc\dcShop\Document\ShipmentDocument::DOCUMENT_TYPE));
                $relatedDocuments[count($relatedDocuments)] = $shipment;
            }

            $orderCrMemos = $this->creditMemoRepository->getCreditMemoByWebshopOrderNo($webshopOrder->company, $webshopOrder->customer_no, $webshopOrder->order_no);

            //$relatedDocuments[count($relatedDocuments)] = $orderCrMemos;

            foreach ($orderCrMemos as $crMemo) {
                $crMemo->setCurrencyCode($currencyCode);
                $crMemo->setDocumentTypeText($this->textProvider->getRegionalizedText(\DynCom\dc\dcShop\Document\CreditMemoDocument::DOCUMENT_TYPE));
                $relatedDocuments[count($relatedDocuments)] = $crMemo;
            }

            $webshopOrder->setRelatedDocuments($relatedDocuments);

        }

        return $webshopOrders;
    }

    private function flagDocumentForPdfSending($company, $customerNo, $documentType, $documentNo, $shopCode, $shopLanguageCode, $userEmail)
    {
        $updated = false;
        if ((strpos($documentType, \DynCom\dc\dcShop\Document\InvoiceDocument::DOCUMENT_TYPE) !== false)) {
            /* @var $invoice InvoiceDocument */
            $invoice = $this->invoiceRepository->getInvoiceByNo($company, $customerNo, $documentNo);
            $invoice = $invoice->getFirst();
            if ($shopCode <> '') {
                $invoice->setRequestShopCode($shopCode);
            }
            if ($shopLanguageCode <> '') {
                $invoice->setRequestLanguageCode($shopLanguageCode);
            }
            if ($userEmail <> '') {
                $invoice->setRequestMail($userEmail);
            }
            $invoice->setSendRequest(1);
            $updated = $this->invoiceRepository->updateSingle($invoice);

        } elseif ((strpos($documentType, \DynCom\dc\dcShop\Document\ShipmentDocument::DOCUMENT_TYPE) !== false)) {

            /* @var $shipment ShipmentDocument */
            $shipment = $this->shipmentRepository->getShipmentByNo($company, $customerNo, $documentNo);
            $shipment = $shipment->getFirst();
            if ($shopCode <> '') {
                $shipment->setRequestShopCode($shopCode);
            }
            if ($shopLanguageCode <> '') {
                $shipment->setRequestLanguageCode($shopLanguageCode);
            }
            if ($userEmail <> '') {
                $shipment->setRequestMail($userEmail);
            }
            $shipment->setSendRequest(1);
            $updated = $this->shipmentRepository->updateSingle($shipment);

        } elseif ((strpos($documentType, \DynCom\dc\dcShop\Document\CreditMemoDocument::DOCUMENT_TYPE) !== false)) {

            /* @var $crdMemo CreditMemoDocument */
            $crdMemo = $this->creditMemoRepository->getCreditMemoByNo($company, $customerNo, $documentNo);
            $crdMemo = $crdMemo->getFirst();
            if ($shopCode <> '') {
                $crdMemo->setRequestShopCode($shopCode);
            }
            if ($shopLanguageCode <> '') {
                $crdMemo->setRequestLanguageCode($shopLanguageCode);
            }
            if ($userEmail <> '') {
                $crdMemo->setRequestMail($userEmail);
            }
            $crdMemo->setSendRequest(1);
            $updated = $this->creditMemoRepository->updateSingle($crdMemo);
        }

        return $updated;

    }


    /**
     * @param ServerRequestInterface $request
     * @param string $company
     * @param Customer $customer
     * @param string $shopCode
     * @param string $shopLanguageCode
     * @param string $defaultCurrencyCode
     * @param string $userEmail
     * @param string $itemImagesPath
     * @param $formURL
     * @param int $maxRowsNumbers
     * @return string
     * @internal param Customer $customerNo
     */
    public function showWebshopOrderHistorySearchForm(ServerRequestInterface $request, string $company, Customer $customer, string $shopCode, string $shopLanguageCode, $currencyCode = '€', string $userEmail = '', string $itemImagesPath = '', $formURL, $maxRowsNumbers = 10, bool $pricesIncludingVat): string
    {
        $dateRange = '';
        $queryParams = $request->getQueryParams();
        $bodyParams = $request->getParsedBody();
        $dateRange = $bodyParams[self::FIELD_NAME_DATE_RANGE] ?? $queryParams[self::FIELD_NAME_DATE_RANGE] ?? null;
        $documentSearchValue = $bodyParams[self::FIELD_NAME_DOCUMENT_SEARCH_INPUT] ?? $queryParams[self::FIELD_NAME_DOCUMENT_SEARCH_INPUT] ?? null;
        $page = $bodyParams[self::PARAMETER_NAME_PAGE_NUMBER] ?? $queryParams[self::PARAMETER_NAME_PAGE_NUMBER] ?? null;

        $requestDocumentPDF = $bodyParams[self::FIELD_NAME_REQUEST_DOCUMENT_PDF] ?? $queryParams[self::FIELD_NAME_REQUEST_DOCUMENT_PDF] ?? null;
        $generateDocumentPDF = $bodyParams[self::FIELD_NAME_GENERATE_DOCUMENT_PDF] ?? $queryParams[self::FIELD_NAME_GENERATE_DOCUMENT_PDF] ?? null;

        $viewDocumentCardForm = $bodyParams[self::FIELD_NAME_VIEW_DOCUMENT_CARD_FORM] ?? $queryParams[self::FIELD_NAME_VIEW_DOCUMENT_CARD_FORM] ?? null;
        $viewCardFormDocumentNO = $bodyParams[self::FIELD_NAME_DOCUMENT_NO_VALUE] ?? $queryParams[self::FIELD_NAME_DOCUMENT_NO_VALUE] ?? null;
        $viewCardFormDocumentType = $bodyParams[self::FIELD_NAME_DOCUMENT_TYPE_VALUE] ?? $queryParams[self::FIELD_NAME_DOCUMENT_TYPE_VALUE] ?? null;

        $customerNo = $customer->getCustomerNo();
        $sendMailResult = false;
        if ($generateDocumentPDF == true) {
            $dateRange = $bodyParams[self::FIELD_NAME_LAST_SEARCH_DATE_RANGE] ?? $queryParams[self::FIELD_NAME_LAST_SEARCH_DATE_RANGE] ?? null;
            $documentSearchValue = $bodyParams[self::FIELD_NAME_LAST_DOCUMENT_SEARCH_INPUT] ?? $queryParams[self::FIELD_NAME_LAST_DOCUMENT_SEARCH_INPUT] ?? null;
            $sendMailTo = $bodyParams[self::FIELD_NAME_REQUEST_MAIL_PDF] ?? $queryParams[self::FIELD_NAME_REQUEST_MAIL_PDF] ?? null;
            $sendMailResult = $this->flagDocumentForPdfSending($company, $customerNo, $viewCardFormDocumentType, $viewCardFormDocumentNO, $shopCode, $shopLanguageCode, $sendMailTo);
        }

        $document = null;

        if ($viewDocumentCardForm == true) {
            $document = $this->getDocument($company, $customer, $shopCode, $shopLanguageCode, $viewCardFormDocumentType, $viewCardFormDocumentNO, $currencyCode, $itemImagesPath);
        }

        if ($page == '') {
            $page = 1;
        } else {
            $dateRange = $bodyParams[self::FIELD_NAME_LAST_SEARCH_DATE_RANGE] ?? $queryParams[self::FIELD_NAME_LAST_SEARCH_DATE_RANGE] ?? null;
            $documentSearchValue = $bodyParams[self::FIELD_NAME_LAST_DOCUMENT_SEARCH_INPUT] ?? $queryParams[self::FIELD_NAME_LAST_DOCUMENT_SEARCH_INPUT] ?? null;
        }

        $dateFrom = '';
        $dateTo = '';

        if ($dateRange <> '') {
            $dateArray = explode("-", $dateRange);
            $dateFrom = date('Y-m-d', strtotime($dateArray[0]));
            $dateTo = date('Y-m-d', strtotime($dateArray[1]));
        }


        $from = ($page - 1) * $maxRowsNumbers;
        $reach = $maxRowsNumbers;

        $allRowsCount = 0;

        $orderBy = " order_no DESC ";

        if ($dateFrom <> '' && $dateTo <> '' && $documentSearchValue <> '') {
            // get orders with document number

            $webshopOrdersWithoutLimit = $this->webshopOrderRepository->getOrderByDateAndOrderNoAndReference($company, $shopCode, $shopLanguageCode, $customerNo, $dateFrom, $dateTo, $documentSearchValue);
            $allRowsCount = count($webshopOrdersWithoutLimit->getAllAsArray());

            $webshopOrders = $this->webshopOrderRepository->getOrderByDateAndOrderNoAndReference($company, $shopCode, $shopLanguageCode, $customerNo, $dateFrom, $dateTo, $documentSearchValue, $from, $reach, $orderBy);



            // get orders which have nav Contacts with that document number and date

            $navContracts = $this->navOrderRepository->getNavOrderByNoAndDateAndReference($company, $customerNo, $dateFrom, $dateTo, $documentSearchValue);

            foreach ($navContracts as $navContract) {
                $order = $this->webshopOrderRepository->getOrderByOrderNo($company, $shopCode, $shopLanguageCode, $customerNo, $navContract->webshop_order_no);
                if ($order->getFirst()->id <> null) {
                    $webshopOrders->add($order->getFirst(), true);
                }
            }


            // get orders which have invoices with that document number and date
            $invoices = $this->invoiceRepository->getInvoiceByNoAndDateAndReference($company, $customerNo, $dateFrom, $dateTo, $documentSearchValue);

            foreach ($invoices as $invoice) {
                $order = $this->webshopOrderRepository->getOrderByOrderNo($company, $shopCode, $shopLanguageCode, $customerNo, $invoice->webshop_order_no);
                if ($order->getFirst()->id <> null) {
                    $webshopOrders->add($order->getFirst(), true);
                }
            }

            // get orders which have shipments with that document number and date

            $shipmets = $this->shipmentRepository->getShipmentByNoAndDateAndReference($company, $customerNo, $dateFrom, $dateTo, $documentSearchValue);

            foreach ($shipmets as $shipmet) {
                $order = $this->webshopOrderRepository->getOrderByOrderNo($company, $shopCode, $shopLanguageCode, $customerNo, $shipmet->webshop_order_no);
                if ($order->getFirst()->id <> null) {
                    $webshopOrders->add($order->getFirst(), true);
                }
            }

            // get orders which have credit memos with that document number and date
            $crMemos = $this->creditMemoRepository->getCreditMemoByNoAndDateAndReference($company, $customerNo, $dateFrom, $dateTo, $documentSearchValue);

            foreach ($crMemos as $crMemo) {
                $order = $this->webshopOrderRepository->getOrderByOrderNo($company, $shopCode, $shopLanguageCode, $customerNo, $crMemo->webshop_order_no);
                if ($order->getFirst()->id <> null) {
                    $webshopOrders->add($order->getFirst(), true);
                }
            }


        } elseif ($dateFrom <> '' && $dateTo <> '' && $documentSearchValue == '') {

            $webshopOrdersWithoutLimit = $this->webshopOrderRepository->getOrderByDate($company, $shopCode, $shopLanguageCode, $customerNo, $dateFrom, $dateTo);
            $allRowsCount = count($webshopOrdersWithoutLimit->getAllAsArray());
            $webshopOrders = $this->webshopOrderRepository->getOrderByDate($company, $shopCode, $shopLanguageCode, $customerNo, $dateFrom, $dateTo, $from, $reach, $orderBy);

        } elseif ($documentSearchValue <> '') {


            $webshopOrdersWithoutLimit = $this->webshopOrderRepository->getOrderByOrderNoAndReference($company, $shopCode, $shopLanguageCode, $customerNo, $documentSearchValue);
            $allRowsCount = count($webshopOrdersWithoutLimit->getAllAsArray());

            $webshopOrders = $this->webshopOrderRepository->getOrderByOrderNoAndReference($company, $shopCode, $shopLanguageCode, $customerNo, $documentSearchValue, $from, $reach, $orderBy);


            // get orders which have nav Contacts with that document number and date

            $navContracts = $this->navOrderRepository->getNavOrderByNoAndReference($company, $customerNo, $dateFrom, $dateTo, $documentSearchValue);

            foreach ($navContracts as $navContract) {
                $order = $this->webshopOrderRepository->getOrderByOrderNo($company, $shopCode, $shopLanguageCode, $customerNo, $navContract->webshop_order_no);
                if ($order->getFirst()->id <> null) {
                    $webshopOrders->add($order->getFirst(), true);
                }
            }

            // get orders which have invoices with that document number
            $invoices = $this->invoiceRepository->getInvoiceByNoAndReference($company, $customerNo, $documentSearchValue);

            foreach ($invoices as $invoice) {
                $order = $this->webshopOrderRepository->getOrderByOrderNo($company, $shopCode, $shopLanguageCode, $customerNo, $invoice->webshop_order_no);
                if ($order->getFirst()->id <> null) {
                    $webshopOrders->add($order->getFirst(), true);
                }
            }

            // get orders which have shipments with that document number

            $shipmets = $this->shipmentRepository->getShipmentByNoAndReference($company, $customerNo, $documentSearchValue);

            foreach ($shipmets as $shipmet) {
                $order = $this->webshopOrderRepository->getOrderByOrderNo($company, $shopCode, $shopLanguageCode, $customerNo, $shipmet->webshop_order_no);
                if ($order->getFirst()->id <> null) {
                    $webshopOrders->add($order->getFirst(), true);
                }
            }

            // get orders which have credit memos with that document number
            $crMemos = $this->creditMemoRepository->getCreditMemoByNoAndReference($company, $shopCode, $shopLanguageCode, $customerNo, $documentSearchValue);

            foreach ($crMemos as $crMemo) {
                $order = $this->webshopOrderRepository->getOrderByOrderNo($company, $shopCode, $shopLanguageCode, $customerNo, $crMemo->webshop_order_no);
                if ($order->getFirst()->id <> null) {
                    $webshopOrders->add($order->getFirst(), true);
                }
            }

        } else {
            $webshopOrdersWithoutLimit = $this->webshopOrderRepository->getOrders($company, $shopCode, $shopLanguageCode, $customerNo);
            $allRowsCount = count($webshopOrdersWithoutLimit->getAllAsArray());
            $webshopOrders = $this->webshopOrderRepository->getOrders($company, $shopCode, $shopLanguageCode, $customerNo, $from, $reach, $orderBy);

        }

        $webshopOrders = $this->setOrdersRelatedDocuments($webshopOrders, $currencyCode);

        $webshopOrders = $webshopOrders->getAllAsArray();


        $documentArchiveService = new DocumentArchiveService();
        $paginationValues = $documentArchiveService->getPaginationValues($page, $allRowsCount, $maxRowsNumbers);


        $documentArchiveViewModel = new DocumentArchiveViewModel($this->textProvider, $webshopOrders, $document, $formURL, $paginationValues, $documentSearchValue, $dateRange, false, '', $requestDocumentPDF, $viewCardFormDocumentType, $viewCardFormDocumentNO, $userEmail, $generateDocumentPDF, $sendMailResult, $pricesIncludingVat);
        $string = $this->mustacheEngine->render(self::TEMPLATE_NAME_WEBORDERS_ARCHIVE, $documentArchiveViewModel);

        return $string;

    }


    public function showDocumentHistorySearchForm(ServerRequestInterface $request, string $company, Customer $customer, string $shopCode, string $shopLanguageCode, $currencyCode = "€", string $itemImagesPath = '', $formURL, $maxRowsNumbers = 10, bool $pricesIncludingVat): string
    {

        $dateRange = '';
        $queryParams = $request->getQueryParams();
        $bodyParams = $request->getParsedBody();
        $dateRange = $bodyParams[self::FIELD_NAME_DATE_RANGE] ?? $queryParams[self::FIELD_NAME_DATE_RANGE] ?? null;
        $documentSearchValue = $bodyParams[self::FIELD_NAME_DOCUMENT_SEARCH_INPUT] ?? $queryParams[self::FIELD_NAME_DOCUMENT_SEARCH_INPUT] ?? null;
        $page = $bodyParams[self::PARAMETER_NAME_PAGE_NUMBER] ?? $queryParams[self::PARAMETER_NAME_PAGE_NUMBER] ?? null;

        $requestDocumentPDF = $bodyParams[self::FIELD_NAME_REQUEST_DOCUMENT_PDF] ?? $queryParams[self::FIELD_NAME_REQUEST_DOCUMENT_PDF] ?? null;
        $generateDocumentPDF = $bodyParams[self::FIELD_NAME_GENERATE_DOCUMENT_PDF] ?? $queryParams[self::FIELD_NAME_GENERATE_DOCUMENT_PDF] ?? null;

        $viewDocumentCardForm = $bodyParams[self::FIELD_NAME_VIEW_DOCUMENT_CARD_FORM] ?? $queryParams[self::FIELD_NAME_VIEW_DOCUMENT_CARD_FORM] ?? null;
        $viewCardFormDocumentNO = $bodyParams[self::FIELD_NAME_DOCUMENT_NO_VALUE] ?? $queryParams[self::FIELD_NAME_DOCUMENT_NO_VALUE] ?? null;
        $viewCardFormDocumentType = $bodyParams[self::FIELD_NAME_DOCUMENT_TYPE_VALUE] ?? $queryParams[self::FIELD_NAME_DOCUMENT_TYPE_VALUE] ?? null;
        $documentTypeFilterValue = $bodyParams[self::FIELD_NAME_DOCUMENT_TYPE_FILTER_VALUE] ?? $queryParams[self::FIELD_NAME_DOCUMENT_TYPE_FILTER_VALUE] ?? null;

        $customerNo = $customer->getCustomerNo();
        $customerEmail = $customer->email;
        $sendMailResult = false;
        if ($generateDocumentPDF == true) {
            $dateRange = $bodyParams[self::FIELD_NAME_LAST_SEARCH_DATE_RANGE] ?? $queryParams[self::FIELD_NAME_LAST_SEARCH_DATE_RANGE] ?? null;
            $documentSearchValue = $bodyParams[self::FIELD_NAME_LAST_DOCUMENT_SEARCH_INPUT] ?? $queryParams[self::FIELD_NAME_LAST_DOCUMENT_SEARCH_INPUT] ?? null;
            $sendMailTo = $bodyParams[self::FIELD_NAME_REQUEST_MAIL_PDF] ?? $queryParams[self::FIELD_NAME_REQUEST_MAIL_PDF] ?? null;
            $sendMailResult = $this->flagDocumentForPdfSending($company, $customerNo, $viewCardFormDocumentType, $viewCardFormDocumentNO, $shopCode, $shopLanguageCode, $sendMailTo);
        }

        $document = null;
        if ($viewDocumentCardForm == true) {
            $document = $this->getDocument($company, $customer, $shopCode, $shopLanguageCode, $viewCardFormDocumentType, $viewCardFormDocumentNO, $currencyCode, $itemImagesPath);
        }


        if ($page == '') {
            $page = 1;
        } else {
            $dateRange = $bodyParams[self::FIELD_NAME_LAST_SEARCH_DATE_RANGE] ?? $queryParams[self::FIELD_NAME_LAST_SEARCH_DATE_RANGE] ?? null;
            $documentSearchValue = $bodyParams[self::FIELD_NAME_LAST_DOCUMENT_SEARCH_INPUT] ?? $queryParams[self::FIELD_NAME_LAST_DOCUMENT_SEARCH_INPUT] ?? null;
            $documentTypeFilterValue = $bodyParams[self::FIELD_NAME_DOCUMENT_TYPE_FILTER_VALUE] ?? $queryParams[self::FIELD_NAME_DOCUMENT_TYPE_FILTER_VALUE] ?? null;
        }

        $dateFrom = '';
        $dateTo = '';

        if ($dateRange <> '') {
            $dateArray = explode("-", $dateRange);
            $dateFrom = date('Y-m-d', strtotime($dateArray[0]));
            $dateTo = date('Y-m-d', strtotime($dateArray[1]));
        }


        $showAllDocuments = true;
        $showInvoices = false;
        $showShipemts = false;
        $showCrMemos = false;
        $showNavContacts = false;


        if ($documentTypeFilterValue == "all") {
        } elseif ($documentTypeFilterValue == "invoices") {
            $showInvoices = true;
            $showAllDocuments = false;
        } elseif ($documentTypeFilterValue == "shipments") {
            $showShipemts = true;
            $showAllDocuments = false;

        } elseif ($documentTypeFilterValue == "crmemos") {
            $showCrMemos = true;
            $showAllDocuments = false;
        } elseif ($documentTypeFilterValue == "nav_contracts") {
            $showNavContacts = true;
            $showAllDocuments = false;
        }
        if ($documentTypeFilterValue == null) {
            $documentTypeFilterValue = 'all';
        }

        if ($dateFrom <> '' && $dateTo <> '' && $documentSearchValue <> '') {

            if ($showAllDocuments || $showInvoices) {
                $invoices = $this->invoiceRepository->getInvoiceByNoAndDateAndReference($company, $customerNo, $dateFrom, $dateTo, $documentSearchValue);
            }
            if ($showAllDocuments || $showShipemts) {
                $shipments = $this->shipmentRepository->getShipmentByNoAndDateAndReference($company, $customerNo, $dateFrom, $dateTo, $documentSearchValue);
            }
            if ($showAllDocuments || $showCrMemos) {
                $crMemos = $this->creditMemoRepository->getCreditMemoByNoAndDateAndReference($company, $customerNo, $dateFrom, $dateTo, $documentSearchValue);
            }
            if ($showAllDocuments || $showNavContacts) {
                $navOrders = $this->navOrderRepository->getNavOrderByNoAndDateAndReference($company, $customerNo, $dateFrom, $dateTo, $documentSearchValue);
            }


        } elseif ($dateFrom <> '' && $dateTo <> '' && $documentSearchValue == '') {

            if ($showAllDocuments || $showInvoices) {
                $invoices = $this->invoiceRepository->getInvoiceByDate($company, $customerNo, $dateFrom, $dateTo);
            }
            if ($showAllDocuments || $showShipemts) {
                $shipments = $this->shipmentRepository->getShipmentByDate($company, $customerNo, $dateFrom, $dateTo);
            }
            if ($showAllDocuments || $showCrMemos) {
                $crMemos = $this->creditMemoRepository->getCreditMemoByDate($company, $customerNo, $dateFrom, $dateTo);
            }
            if ($showAllDocuments || $showNavContacts) {
                $navOrders = $this->navOrderRepository->getNavOrderByDate($company, $customerNo, $dateFrom, $dateTo);
            }


        } elseif ($documentSearchValue <> '') {

            $invoices = $this->invoiceRepository->getInvoiceByNoAndReference($company, $customerNo, $documentSearchValue);
            $shipments = $this->shipmentRepository->getShipmentByNoAndReference($company, $customerNo, $documentSearchValue);
            $crMemos = $this->creditMemoRepository->getCreditMemoByNoAndReference($company, $customerNo, $documentSearchValue);
            $navOrders = $this->navOrderRepository->getNavOrderByNoAndReference($company, $customerNo, $documentSearchValue);

            if ($showAllDocuments || $showInvoices) {
                $invoices = $this->invoiceRepository->getInvoiceByNoAndReference($company, $customerNo, $documentSearchValue);
            }
            if ($showAllDocuments || $showShipemts) {
                $shipments = $this->shipmentRepository->getShipmentByNoAndReference($company, $customerNo, $documentSearchValue);
            }
            if ($showAllDocuments || $showCrMemos) {
                $crMemos = $this->creditMemoRepository->getCreditMemoByNoAndReference($company, $customerNo, $documentSearchValue);
            }
            if ($showAllDocuments || $showNavContacts) {
                $navOrders = $this->navOrderRepository->getNavOrderByNoAndReference($company, $customerNo, $documentSearchValue);
            }
        } else {

            if ($showAllDocuments || $showInvoices) {
                $invoices = $this->invoiceRepository->getInvoices($company, $customerNo);
            }
            if ($showAllDocuments || $showShipemts) {
                $shipments = $this->shipmentRepository->getShipments($company, $customerNo);
            }
            if ($showAllDocuments || $showCrMemos) {
                $crMemos = $this->creditMemoRepository->getCreditMemos($company, $customerNo);
            }
            if ($showAllDocuments || $showNavContacts) {
                $navOrders = $this->navOrderRepository->getNavOrders($company, $customerNo);
            }

        }

        $documents = array();

        foreach ($navOrders as $navOrder) {
            $navOrder->setCurrencyCode($currencyCode);
            $navOrder->setDocumentTypeText($this->textProvider->getRegionalizedText(\DynCom\dc\dcShop\Document\NavOrderDocument::DOCUMENT_TYPE));
            array_push($documents, $navOrder);
        }

        foreach ($invoices as $invoice) {
            $invoice->setCurrencyCode($currencyCode);
            $invoice->setDocumentTypeText($this->textProvider->getRegionalizedText(\DynCom\dc\dcShop\Document\InvoiceDocument::DOCUMENT_TYPE));
            array_push($documents, $invoice);

        }
        foreach ($shipments as $shipment) {
            $shipment->setCurrencyCode($currencyCode);
            $shipment->setDocumentTypeText($this->textProvider->getRegionalizedText(\DynCom\dc\dcShop\Document\ShipmentDocument::DOCUMENT_TYPE));
            array_push($documents, $shipment);

        }
        foreach ($crMemos as $crMemo) {
            $crMemo->setCurrencyCode($currencyCode);
            $crMemo->setDocumentTypeText($this->textProvider->getRegionalizedText(\DynCom\dc\dcShop\Document\CreditMemoDocument::DOCUMENT_TYPE));
            array_push($documents, $crMemo);

        }

        $allRowsCount = count($documents);

        $from = ($page - 1) * $maxRowsNumbers;
        $reach = $maxRowsNumbers;

        $reach = $from + $reach;
        if ($reach > $allRowsCount) {
            $reach = $allRowsCount;
        }

        $documentArchiveService = new DocumentArchiveService();
        $paginationValues = $documentArchiveService->getPaginationValues($page, $allRowsCount, $maxRowsNumbers);
        $documents = $documentArchiveService->sortArrayWithObjectsWithDates($documents, 'DESC');
        $documents = array_slice($documents, $from, $reach);

        $documentArchiveViewModel = new DocumentArchiveViewModel($this->textProvider, $documents, $document, $formURL, $paginationValues, $documentSearchValue, $dateRange, true, $documentTypeFilterValue, $requestDocumentPDF, $viewCardFormDocumentType, $viewCardFormDocumentNO, $customerEmail, $generateDocumentPDF, $sendMailResult, $pricesIncludingVat);
        $string = $this->mustacheEngine->render(self::TEMPLATE_NAME_DOCUMENT_ARCHIVE, $documentArchiveViewModel);

        return $string;

    }


    private function getDocument($company, $customer, $shopCode, $shopLanguageCode, $documentType, $documentNo, $currencyCode, $itemImagesPath)
    {
        $customerNo = $customer->getCustomerNo();
        $customerEmail = $customer->getEmail();

        if ((strpos($documentType, \DynCom\dc\dcShop\Document\WebshopOrderDocument::DOCUMENT_TYPE) !== false)) {

            /* @var $order WebshopOrderDocument */
            $order = $this->webshopOrderRepository->getOrderByOrderNo($company, $shopCode, $shopLanguageCode, $customerNo, $documentNo);
            $order = $order->getFirst();
            $orderLines = $order->getLines();
            foreach ($orderLines as $orderLine) {
                $itemImage = $this->getWebshopItemImage($order->company, $order->shop_code, $order->language_code, $orderLine->item_no);

                $imagePath = $itemImagesPath . $itemImage["filename"];


                if ($itemImage["filename"] == '' || !file_exists((rtrim(dirname(dirname(dirname(dirname(__DIR__)))), '/')) . $imagePath)) {
                    $imagePath = $itemImagesPath . "noimage.jpg";
                }

                $orderLine->setWebshopItemImagePath($imagePath);

            }

            $document = $order;
            $document->setDocumentTypeText($this->textProvider->getRegionalizedText(\DynCom\dc\dcShop\Document\WebshopOrderDocument::DOCUMENT_TYPE));


            /*  @var $orderLines  WebshopOrderLineCollection */

        } elseif ((strpos($documentType, \DynCom\dc\dcShop\Document\InvoiceDocument::DOCUMENT_TYPE) !== false)) {
            /* @var $invoice InvoiceDocument */
            $invoice = $this->invoiceRepository->getInvoiceByNo($company, $customerNo, $documentNo);
            $document = $invoice->getFirst();
            $document->setCustomerEmail($customerEmail);
            $document->setDocumentTypeText($this->textProvider->getRegionalizedText(\DynCom\dc\dcShop\Document\InvoiceDocument::DOCUMENT_TYPE));

        } elseif ((strpos($documentType, \DynCom\dc\dcShop\Document\ShipmentDocument::DOCUMENT_TYPE) !== false)) {

            /* @var $shipment ShipmentDocument */
            $shipment = $this->shipmentRepository->getShipmentByNo($company, $customerNo, $documentNo);
            $document = $shipment->getFirst();
            $document->setCustomerEmail($customerEmail);
            $document->setDocumentTypeText($this->textProvider->getRegionalizedText(\DynCom\dc\dcShop\Document\ShipmentDocument::DOCUMENT_TYPE));

        } elseif ((strpos($documentType, \DynCom\dc\dcShop\Document\CreditMemoDocument::DOCUMENT_TYPE) !== false)) {

            /* @var $crdMemo CreditMemoDocument */
            $crdMemo = $this->creditMemoRepository->getCreditMemoByNo($company, $customerNo, $documentNo);
            $document = $crdMemo->getFirst();
            $document->setCustomerEmail($customerEmail);
            $document->setDocumentTypeText($this->textProvider->getRegionalizedText(\DynCom\dc\dcShop\Document\CreditMemoDocument::DOCUMENT_TYPE));

        } elseif ((strpos($documentType, \DynCom\dc\dcShop\Document\NavOrderDocument::DOCUMENT_TYPE) !== false)) {

            /* @var $navOrder NavOrderDocument */
            $navOrder = $this->navOrderRepository->getNavOrderByNo($company, $customerNo, $documentNo);
            $document = $navOrder->getFirst();
            $document->setCustomerEmail($customerEmail);
            $document->setDocumentTypeText($this->textProvider->getRegionalizedText(\DynCom\dc\dcShop\Document\NavOrderDocument::DOCUMENT_TYPE));
        }
        $document->setCurrencyCode($currencyCode);

        if ((strpos($documentType, \DynCom\dc\dcShop\Document\WebshopOrderDocument::DOCUMENT_TYPE) === false)) {
            $documentLines = $document->getLines();
            foreach ($documentLines as $documentLine) {
                if ($documentLine->type == 0) {
                    $documentLine->setTypeText('');
                } elseif ($documentLine->type == 1) {
                    $documentLine->setTypeText($this->textProvider->getRegionalizedText('g_l_account'));
                } elseif ($documentLine->type == 2) {
                    $documentLine->setTypeText($this->textProvider->getRegionalizedText('item'));
                } elseif ($documentLine->type == 3) {
                    $documentLine->setTypeText($this->textProvider->getRegionalizedText('resource'));
                } elseif ($documentLine->type == 4) {
                    $documentLine->setTypeText($this->textProvider->getRegionalizedText('fixed_asset'));
                } elseif ($documentLine->type == 5) {
                    $documentLine->setTypeText($this->textProvider->getRegionalizedText('charge_item'));
                }
            }
        }


        $shipmentCountry = $this->countryRepository->getCountyByCode($company, $shopCode, $shopLanguageCode, $document->ship_to_country);
        $shipmentCountry = $shipmentCountry->getFirst();

        $billCountry = $this->countryRepository->getCountyByCode($company, $shopCode, $shopLanguageCode, $document->bill_to_country);
        $billCountry = $billCountry->getFirst();

        $document->setShipToCountry($shipmentCountry->description);
        $document->setBillToCountry($billCountry->description);

        return $document;

    }

    private function getWebshopItemImage($company, $shopCode, $languageCode, $itemNo)
    {

        $criteria = [
            [
                ['company', '=', $company],
                ['shop_code', '=', $shopCode],
                ['language_code', '=', $languageCode],
                ['item_no', '=', $itemNo],
            ],

        ];
        $webshopItem = $this->webshopItemRepository->getWebshopItemByCriteria($criteria);
        $webshopItem = $webshopItem->getFirst();

        /** @var WebshopItemInterface $webshopItem */
        $webshopItemImage = new \DynCom\dc\dcShop\classes\WebshopItemImagesDecorator($webshopItem, $this->webshopItemVariantService, $this->pdo);
        $imageData = $webshopItemImage->getImageData();

        return $imageData[0];

    }


}




