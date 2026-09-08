<?php

namespace DynCom\dc\dcShop\Document;


class DocumentArchiveViewModel
{
    protected $textProvider;
    public $documents;
    public $document;
    Public $formURLValue;
    public $documentNoSearchValue;
    Public $dateRangeSearchValue;
    public $listHasValues = false;
    public $emptyListMessage = '';
    public $documentNoHeader;
    public $documentTypeHeader;
    public $documentDateHeader;
    public $documentAmountHeader;
    public $documentNoSearchLabel;
    public $dateRangeLabel;
    public $documentTypelabel;
    public $searchButtonLabel;
    public $resetButtonLabel;
    public $listHasPagination;
    Public $currentPageValue;
    public $numberOfPages;
    public $listHasPaginationBackButton;
    public $previousPageValue;
    public $listHasPaginationNextButton;
    public $nextPageValue;
    public $paginationLabel;
    public $weekDays;
    public $yearMonths;
    public $documentCardInputs;
    public $customer;
    public $showDocumentCard;
    public $dateLabel;
    public $customerNoLabel;
    public $nameLabel;
    public $emailLabel;
    public $referenceLabel;
    public $commentLabel;
    public $invoiceAddressLabel;
    public $shipmentAddressLabel;
    public $leftOrderBoxLabel;
    public $displayItemImage;
    public $displayUnitOfMeasure;
    public $displayLinePrice;


    public $itemNoHeader;
    public $descriptionHeader;
    public $quantityHeader;
    public $unitOfMeasureHeader;
    public $priceHeader;
    public $totalPriceHeader;
    public $variantLabel;
    public $sumLabel;

    public $showDocumentTypeFilter;
    public $documentTypeFilterOptions;

    public $documentTypeFilter;
    public $showRequstPDF;
    public $requestPDFEmailValue;

    public $sendMailLabel;
    public $send;
    public $selectedDocumentType;
    public $selectedDocumentNo;

    public $showNotificationMessage;
    public $notificationMessageValue;

    public $orderLabel;
    public $orderValue;
    public $smallQuantityChargeLabel;
    public $smallQuantityChargeValue;
    public $shippingCostLabel;
    public $shippingCostValue;
    public $paymentCostLabel;
    public $paymentCostValue;
    public $onlineDiscountLabel;
    public $onlineDiscountValue;
    public $onlineDiscountPercent;
    public $invoiceDiscountLabel;
    public $invoiceDiscountValue;
    public $invoiceDiscountPercent;
    public $couponAmountLabel;
    public $couponAmountValue;
    public $displayTypeHeader;
    public $typeDisplayedValue;

    public $pricesIncludingVat;

    public $trackShipmentLabel;

    public $documentArchiveLabel;
    public $orderHistoryLabel;

    /**
     * DocumentArchiveViewModel2 constructor.
     * @param $textProvider
     * @param $documents
     * @param $document
     * @param $formURLValue
     * @param $paginationValues
     * @param $documentNoSearchValue
     * @param $dateRangeSearchValue
     * @param bool $showDocumentTypeFilter
     * @param string $documentFilterSelectedValue
     * @param bool $showRequstPDF
     * @param $selectedDocumentType
     * @param $selectedDocumentNo
     * @param $requestPDFEmailValue
     * @param $notifyMessage
     */
    public function __construct($textProvider, $documents, $document, $formURLValue, $paginationValues, $documentNoSearchValue, $dateRangeSearchValue, $showDocumentTypeFilter = false, $documentFilterSelectedValue = 'all', $showRequstPDF = false, $selectedDocumentType, $selectedDocumentNo, $requestPDFEmailValue, $showNotificationMessage = false, $notificationMessageValue = false, $pricesIncludingVat = false)
    {
        $this->textProvider = $textProvider;
        $this->documents = $documents;
        $this->formURLValue = $formURLValue;
        $this->document = $document;
        $this->pricesIncludingVat = $pricesIncludingVat;

        $this->currentPageValue = $paginationValues['current_page_value'];
        $this->numberOfPages = $paginationValues['number_of_pages'];
        if ($this->numberOfPages > 1) {
            $this->listHasPagination = true;
            if ($paginationValues['next_page_value'] <> null) {
                $this->nextPageValue = $paginationValues['next_page_value'];
                $this->listHasPaginationNextButton = true;
            }
            if ($paginationValues['previous_page_value'] <> null) {
                $this->previousPageValue = $paginationValues['previous_page_value'];
                $this->listHasPaginationBackButton = true;
            }
            $this->paginationLabel = $this->textProvider->getRegionalizedText('pagination_page') . " " . $this->currentPageValue . " " . $this->textProvider->getRegionalizedText('pagination_of') . " " . $this->numberOfPages;

        } else {
            $this->listHasPagination = false;
        }


        $this->documentNoSearchValue = $documentNoSearchValue;
        $this->dateRangeSearchValue = $dateRangeSearchValue;

        if (count($this->documents) > 0) {
            $this->listHasValues = true;
        } else {
            $this->emptyListMessage = $this->textProvider->getRegionalizedText('no_results');
        }

        // show document card
        if ($this->document <> null) {
            $this->showDocumentCard = true;
            $this->setDocumentCardLables();

            $this->displayItemImage = false;
            $this->displayUnitOfMeasure = true;
            $this->displayLinePrice = true;

            if ($this->document->getDocumentType() == \DynCom\dc\dcShop\Document\WebshopOrderDocument::DOCUMENT_TYPE) {

                $this->itemNoHeader = $this->textProvider->getRegionalizedText('item_no');
                $this->displayItemImage = true;
                $this->displayUnitOfMeasure = false;

                if ($this->document->total <> $this->document->subtotal) {

                    $this->orderLabel = $this->textProvider->getRegionalizedText('order_total');
                    $this->orderValue = number_format($this->document->subtotal, 2, ',', '.');
                }

                if ($this->document->small_quantity_charge_amount > 0) {

                    $this->smallQuantityChargeLabel = $this->textProvider->getRegionalizedText('small_quantity_charge');
                    $this->smallQuantityChargeValue = number_format($this->document->small_quantity_charge_amount, 2, ',', '.');
                }


                if ($this->document->shipping_cost > 0) {

                    $this->shippingCostLabel = $this->textProvider->getRegionalizedText('shipping_cost');
                    $this->shippingCostValue = number_format($this->document->shipping_cost, 2, ',', '.');
                }


                if ($this->document->payment_cost > 0) {

                    $this->paymentCostLabel = $this->textProvider->getRegionalizedText('Zahlungsart');
                    $this->paymentCostValue = number_format($this->document->payment_cost, 2, ',', '.');
                }


                if ($this->document->online_discount_amount > 0) {

                    $this->onlineDiscountLabel = $this->textProvider->getRegionalizedText('online_discount');
                    $this->onlineDiscountValue = number_format($this->document->online_discount_amount, 2, ',', '.');
                    $this->onlineDiscountPercent = round($this->document->online_discount);

                }

                if ($this->document->invoice_discount_amount > 0) {

                    $this->invoiceDiscountLabel = $this->textProvider->getRegionalizedText('invoice_discount');
                    $this->invoiceDiscountValue = number_format($this->document->invoice_discount_amount, 2, ',', '.');
                    $this->invoiceDiscountPercent = round($this->document->invoice_discount);
                }

                if ($this->document->coupon_amount > 0) {

                    $this->couponAmountLabel = $this->textProvider->getRegionalizedText('coupon_discount');
                    $this->couponAmountValue = number_format($this->document->coupon_amount, 2, ',', '.');
                }


            } else {
                $this->itemNoHeader = $this->textProvider->getRegionalizedText('number');
                $this->displayTypeHeader = true;
                $this->typeDisplayedValue = $this->textProvider->getRegionalizedText('type');

            }
            if ($this->document->getDocumentType() == \DynCom\dc\dcShop\Document\ShipmentDocument::DOCUMENT_TYPE) {
                $this->displayLinePrice = false;
                $this->trackShipmentLabel =   $this->textProvider->getRegionalizedText('track_your_shipment');
            }
        }
        $this->setLables();

        if ($showDocumentTypeFilter) {
            $this->showDocumentTypeFilter = $showDocumentTypeFilter;
            $this->documentTypeFilter = $documentFilterSelectedValue;
            $this->createDocumentTypeFilter($documentFilterSelectedValue);
        }

        $this->showRequstPDF = $showRequstPDF;
        $this->requestPDFEmailValue = $requestPDFEmailValue;
        $this->selectedDocumentType = $selectedDocumentType;
        $this->selectedDocumentNo = $selectedDocumentNo;

        $this->showNotificationMessage = $showNotificationMessage;
        if ($notificationMessageValue) {
            $this->notificationMessageValue = $this->textProvider->getRegionalizedText('get_email_soon');
        } else {
            $this->notificationMessageValue = $this->textProvider->getRegionalizedText('request_not_delivered');
        }
    }

    private function setLables()
    {

        $this->documentArchiveLabel = $this->textProvider->getRegionalizedText('document_archive');
        $this->orderHistoryLabel = $this->textProvider->getRegionalizedText('order_history');

        $this->documentNoHeader = $this->textProvider->getRegionalizedText('document_number');
        $this->documentTypeHeader = $this->textProvider->getRegionalizedText('document_type');
        $this->documentDateHeader = $this->textProvider->getRegionalizedText('document_date');
        $this->documentAmountHeader = $this->textProvider->getRegionalizedText('document_total');


        $this->documentNoSearchLabel = $this->textProvider->getRegionalizedText('document_number');
        $this->dateRangeLabel = $this->textProvider->getRegionalizedText('document_date');
        $this->documentTypelabel = $this->textProvider->getRegionalizedText('document_type');

        $this->searchButtonLabel = $this->textProvider->getRegionalizedText('search');
        $this->resetButtonLabel = $this->textProvider->getRegionalizedText('reset');

        $days = "'" .
            $this->textProvider->getRegionalizedText('sunDay') . "',"
            . "'" . $this->textProvider->getRegionalizedText('monDay') . "',"
            . "'" . $this->textProvider->getRegionalizedText('tuesDay') . "',"
            . "'" . $this->textProvider->getRegionalizedText('wednsDay') . "',"
            . "'" . $this->textProvider->getRegionalizedText('thursDay') . "',"
            . "'" . $this->textProvider->getRegionalizedText('FriDay') . "',"
            . "'" . $this->textProvider->getRegionalizedText('SaturDay') . "'";

        $this->weekDays = $days;

        $months = "'" .
            $this->textProvider->getRegionalizedText('january') . "',"
            . "'" . $this->textProvider->getRegionalizedText('february') . "',"
            . "'" . $this->textProvider->getRegionalizedText('march') . "',"
            . "'" . $this->textProvider->getRegionalizedText('april') . "',"
            . "'" . $this->textProvider->getRegionalizedText('may') . "',"
            . "'" . $this->textProvider->getRegionalizedText('june') . "',"
            . "'" . $this->textProvider->getRegionalizedText('july') . "',"
            . "'" . $this->textProvider->getRegionalizedText('august') . "',"
            . "'" . $this->textProvider->getRegionalizedText('september') . "',"
            . "'" . $this->textProvider->getRegionalizedText('october') . "',"
            . "'" . $this->textProvider->getRegionalizedText('november') . "',"
            . "'" . $this->textProvider->getRegionalizedText('december') . "'";

        $this->yearMonths = $months;

        $this->backButtonLable = $this->textProvider->getRegionalizedText('back');
        $this->nextButtonLable = $this->textProvider->getRegionalizedText('next');
        $this->emailLabel = $this->textProvider->getRegionalizedText('email');
        $this->sendMailLabel = $this->textProvider->getRegionalizedText('send_to');
        $this->send = $this->textProvider->getRegionalizedText('send');

    }


    private function setDocumentCardLables()
    {
        $this->dateLabel = $this->textProvider->getRegionalizedText('document_date');
        $this->customerNoLabel = $this->textProvider->getRegionalizedText('customer_no');
        $this->nameLabel = $this->textProvider->getRegionalizedText('name');
        $this->emailLabel = $this->textProvider->getRegionalizedText('email');
        $this->referenceLabel = $this->textProvider->getRegionalizedText('your_reference');
        $this->commentLabel = $this->textProvider->getRegionalizedText('your_comment');
        $this->invoiceAddressLabel = $this->textProvider->getRegionalizedText('invoice_address');
        $this->shipmentAddressLabel = $this->textProvider->getRegionalizedText('shipping_address');

        $this->descriptionHeader = $this->textProvider->getRegionalizedText('description');
        $this->quantityHeader = $this->textProvider->getRegionalizedText('quantity');
        $this->unitOfMeasureHeader = $this->textProvider->getRegionalizedText('unit');
        $this->priceHeader = $this->textProvider->getRegionalizedText('your_price');
        $this->totalPriceHeader = $this->textProvider->getRegionalizedText('line_amount');

        $this->variantLabel = $this->textProvider->getRegionalizedText('var_code');

        $vatMessage = '';

        if($this->pricesIncludingVat)
        {
            $vatMessage = $this->textProvider->getRegionalizedText('vat_message') . "<br/>";
        }

        $this->leftOrderBoxLabel =  $vatMessage
            . $this->textProvider->getRegionalizedText('shipment_message') . "<br/>";

        $this->sumLabel = $this->textProvider->getRegionalizedText('total_amount');

    }

    public function createDocumentTypeFilter($selectedValue = 'all')
    {
        $documentTypes = array();

        if ($selectedValue == 'all') {
            $documentTypeOption = array(
                'option_name' => $this->textProvider->getRegionalizedText('all'),
                'option_value' => 'all',
                'selected_value' => 'selected',
            );
        } else {
            $documentTypeOption = array(
                'option_name' => $this->textProvider->getRegionalizedText('all'),
                'option_value' => 'all',
                'selected_value' => '',
            );
        }

        array_push($documentTypes, $documentTypeOption);


        $documentTypeOption = array(
            'option_name' => $this->textProvider->getRegionalizedText('contacts'),
            'option_value' => 'nav_contracts',
            'selected_value' => 'selected',
        );
        if ($selectedValue == 'nav_contracts') {
            $documentTypeOption = array(
                'option_name' => $this->textProvider->getRegionalizedText('contacts'),
                'option_value' => 'nav_contracts',
                'selected_value' => 'selected',
            );
        } else {

            $documentTypeOption = array(
                'option_name' => $this->textProvider->getRegionalizedText('contacts'),
                'option_value' => 'nav_contracts',
                'selected_value' => '',
            );
        }

        array_push($documentTypes, $documentTypeOption);

        if ($selectedValue == 'invoices') {
            $documentTypeOption = array(
                'option_name' => $this->textProvider->getRegionalizedText('sales_invoice_history'),
                'option_value' => 'invoices',
                'selected_value' => 'selected',
            );
        } else {

            $documentTypeOption = array(
                'option_name' => $this->textProvider->getRegionalizedText('sales_invoice_history'),
                'option_value' => 'invoices',
                'selected_value' => '',
            );
        }


        array_push($documentTypes, $documentTypeOption);

        if ($selectedValue == 'shipments') {
            $documentTypeOption = array(
                'option_name' => $this->textProvider->getRegionalizedText('sales_shipment_history'),
                'option_value' => 'shipments',
                'selected_value' => 'selected',
            );
        } else {

            $documentTypeOption = array(
                'option_name' => $this->textProvider->getRegionalizedText('sales_shipment_history'),
                'option_value' => 'shipments',
                'selected_value' => '',
            );
        }

        array_push($documentTypes, $documentTypeOption);

        if ($selectedValue == 'crmemos') {
            $documentTypeOption = array(
                'option_name' => $this->textProvider->getRegionalizedText('cr_memo_history'),
                'option_value' => 'crmemos',
                'selected_value' => 'selected',
            );
        } else {

            $documentTypeOption = array(
                'option_name' => $this->textProvider->getRegionalizedText('cr_memo_history'),
                'option_value' => 'crmemos',
                'selected_value' => '',
            );
        }


        array_push($documentTypes, $documentTypeOption);


        $this->documentTypeFilterOptions = $documentTypes;
    }

}