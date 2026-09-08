<?php
namespace DynCom\dc\dcShop\rma\classes;
use DynCom\dc\common\interfaces\ViewModel;
use DynCom\dc\common\traits\universallyGettableTrait;
use DynCom\dc\dcShop\interfaces\TemplatingInterface;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 22.01.2015
 * Time: 07:30
 */

class RMAShipmentViewModel implements ViewModel {

    use universallyGettableTrait;

    protected $styleFilePath = '/module/dcshop/rma/styles/rma.css';
    protected $templateEngine;

    protected $titleString;
    protected $mainMessageString;
    protected $errors            = array();
    protected $errorString       = '';
    protected $noticeString      = '';

    protected $shipmentLabel;
    protected $shipmentNoLabel;
    protected $nameLabel;
    protected $postingDateLabel;
    protected $yourReferenceLabel;
    protected $backButtonLabel;
    protected $sendButtonLabel;
    protected $addressLabel;
    protected $postCodeLabel;
    protected $cityLabel;
    protected $itemNoLabel;
    protected $descriptionLabel;
    protected $quantityLabel;
    protected $qtyReturnedLabel;
    protected $qtyRemainingLabel;
    protected $returnQuantityLabel;
    protected $returnReasonLabel;
    protected $pleaseChooseText;

    protected $linePrefill;

    protected $inputEMail;
    protected $inputPostCode;
    protected $inputRequest;

    protected $shipment;
    protected $returnReasonCollection;

    protected $linkBack;

    /**
     * @param TemplatingInterface $templateEngine
     * @param RetShipmentDocument $shipment
     * @param ReturnReasonCollection $reasonCollection
     */
    public function __construct( TemplatingInterface $templateEngine, RetShipmentDocument $shipment, ReturnReasonCollection $reasonCollection ) {

        $this->templateEngine         = $templateEngine;
        $this->shipment               = $shipment;
        $this->returnReasonCollection = $reasonCollection;

        /* LABELS */
        $this->addressLabel        = $templateEngine->getText('address');
        $this->backButtonLabel     = $templateEngine->getText('back');
        $this->cityLabel           = $templateEngine->getText('city');
        $this->descriptionLabel    = $templateEngine->getText('description');
        $this->itemNoLabel         = $templateEngine->getText('item_no');
        $this->nameLabel           = $templateEngine->getText('name');
        $this->postCodeLabel       = $templateEngine->getText('post_code');
        $this->postingDateLabel    = $templateEngine->getText('posting_date');
        $this->qtyRemainingLabel   = $templateEngine->getText('qty_remaining');
        $this->qtyReturnedLabel    = $templateEngine->getText('qty_returned');
        $this->quantityLabel       = $templateEngine->getText('quantity');
        $this->returnQuantityLabel = $templateEngine->getText('return_qty');
        $this->returnReasonLabel   = $templateEngine->getText('return_reason');
        $this->sendButtonLabel     = $templateEngine->getText('send');
        $this->shipmentLabel       = $templateEngine->getText('shipment');
        $this->shipmentNoLabel     = $templateEngine->getText('shipment_no');
        $this->yourReferenceLabel  = $templateEngine->getText('your_reference');


        /* TEXTS */
        $this->titleString       = $templateEngine->getText('rma_title');
        $this->mainMessageString = $templateEngine->getText('rma_text_login');

        /* INPUT */
        if (isset($_REQUEST['input_rma_request'])) {
            $this->inputRequest = filter_var($_REQUEST['input_rma_request'], FILTER_SANITIZE_STRING);
        }
        if (isset($_REQUEST['input_rma_email'])) {
            $this->inputEMail = filter_var($_REQUEST['input_rma_email'], FILTER_SANITIZE_STRING);
        }
        if (isset($_REQUEST['input_rma_post_code'])) {
            $this->inputPostCode = filter_var($_REQUEST['input_rma_post_code'], FILTER_SANITIZE_STRING);
        }

        $linePrefill = array();
        if (isset($_REQUEST['input_lines'])) {
            foreach ($_REQUEST['input_lines'] as $lineRequest) {
                $line_id                                   = filter_var($lineRequest['line_id'], FILTER_SANITIZE_NUMBER_INT);
                $linePrefill[$line_id]['return_quantity']  = filter_var($lineRequest['return_quantity'], FILTER_SANITIZE_NUMBER_FLOAT);
                $linePrefill[$line_id]['return_reason_id'] = filter_var($lineRequest['return_reason_id'], FILTER_SANITIZE_NUMBER_INT);
            }
        }
        $this->linePrefill = $linePrefill;

        $this->linkBack = '?shop_category=rma';

        if(!empty($this->inputRequest)) {
            $this->linkBack = '?shop_category=rma&action=search&input_rma_email=' . urlencode($this->inputEMail) .'&input_rma_post_code=' . urlencode($this->inputPostCode) . '&input_rma_request=' . urlencode($this->inputRequest);
        }
    }

    /**
     * @param mixed $yourReferenceLabel
     */
    public function setYourReferenceLabel( $yourReferenceLabel ) {
        $this->yourReferenceLabel = $yourReferenceLabel;
    }

    /**
     * @param string $template
     */
    public function setTemplate( $template ) {
        $this->template = $template;
    }

    /**
     * @param string $styleFilePath
     */
    public function setStyleFilePath( $styleFilePath ) {
        $this->styleFilePath = $styleFilePath;
    }

    /**
     * @param mixed $templateEngine
     */
    public function setTemplateEngine( $templateEngine ) {
        $this->templateEngine = $templateEngine;
    }

    /**
     * @param mixed $titleString
     */
    public function setTitleString( $titleString ) {
        $this->titleString = $titleString;
    }

    /**
     * @param mixed $mainMessageString
     */
    public function setMainMessageString( $mainMessageString ) {
        $this->mainMessageString = $mainMessageString;
    }

    /**
     * @param mixed $errorString
     */
    public function setErrorString( $errorString ) {
        $this->errorString = $errorString;
    }

    /**
     * @param mixed $noticeString
     */
    public function setNoticeString( $noticeString ) {
        $this->noticeString = $noticeString;
    }

    /**
     * @param mixed $shipmentLabel
     */
    public function setShipmentLabel( $shipmentLabel ) {
        $this->shipmentLabel = $shipmentLabel;
    }

    /**
     * @param mixed $shipmentNoLabel
     */
    public function setShipmentNoLabel( $shipmentNoLabel ) {
        $this->shipmentNoLabel = $shipmentNoLabel;
    }

    /**
     * @param mixed $nameLabel
     */
    public function setNameLabel( $nameLabel ) {
        $this->nameLabel = $nameLabel;
    }

    /**
     * @param mixed $postingDateLabel
     */
    public function setPostingDateLabel( $postingDateLabel ) {
        $this->postingDateLabel = $postingDateLabel;
    }

    /**
     * @param mixed $backButtonLabel
     */
    public function setBackButtonLabel( $backButtonLabel ) {
        $this->backButtonLabel = $backButtonLabel;
    }

    /**
     * @param mixed $sendButtonLabel
     */
    public function setSendButtonLabel( $sendButtonLabel ) {
        $this->sendButtonLabel = $sendButtonLabel;
    }

    /**
     * @param mixed $addressLabel
     */
    public function setAddressLabel( $addressLabel ) {
        $this->addressLabel = $addressLabel;
    }

    /**
     * @param mixed $postCodeLabel
     */
    public function setPostCodeLabel( $postCodeLabel ) {
        $this->postCodeLabel = $postCodeLabel;
    }

    /**
     * @param mixed $cityLabel
     */
    public function setCityLabel( $cityLabel ) {
        $this->cityLabel = $cityLabel;
    }

    /**
     * @param mixed $itemNoLabel
     */
    public function setItemNoLabel( $itemNoLabel ) {
        $this->itemNoLabel = $itemNoLabel;
    }

    /**
     * @param mixed $descriptionLabel
     */
    public function setDescriptionLabel( $descriptionLabel ) {
        $this->descriptionLabel = $descriptionLabel;
    }

    /**
     * @param mixed $quantityLabel
     */
    public function setQuantityLabel( $quantityLabel ) {
        $this->quantityLabel = $quantityLabel;
    }

    /**
     * @param mixed $qtyReturnedLabel
     */
    public function setQtyReturnedLabel( $qtyReturnedLabel ) {
        $this->qtyReturnedLabel = $qtyReturnedLabel;
    }

    /**
     * @param mixed $qtyRemainingLabel
     */
    public function setQtyRemainingLabel( $qtyRemainingLabel ) {
        $this->qtyRemainingLabel = $qtyRemainingLabel;
    }

    /**
     * @param mixed $returnQuantityLabel
     */
    public function setReturnQuantityLabel( $returnQuantityLabel ) {
        $this->returnQuantityLabel = $returnQuantityLabel;
    }

    /**
     * @param mixed $returnReasonLabel
     */
    public function setReturnReasonLabel( $returnReasonLabel ) {
        $this->returnReasonLabel = $returnReasonLabel;
    }

    /**
     * @param mixed $pleaseChooseText
     */
    public function setPleaseChooseText( $pleaseChooseText ) {
        $this->pleaseChooseText = $pleaseChooseText;
    }

    /**
     * @param mixed $linePrefill
     */
    public function setLinePrefill( $linePrefill ) {
        $this->linePrefill = $linePrefill;
    }

    /**
     * @param mixed $inputEMail
     */
    public function setInputEMail( $inputEMail ) {
        $this->inputEMail = $inputEMail;
    }

    /**
     * @param mixed $inputPostCode
     */
    public function setInputPostCode( $inputPostCode ) {
        $this->inputPostCode = $inputPostCode;
    }

    /**
     * @param mixed $inputRequest
     */
    public function setInputRequest( $inputRequest ) {
        $this->inputRequest = $inputRequest;
    }

    /**
     * @param mixed $shipment
     */
    public function setShipment( $shipment ) {
        $this->shipment = $shipment;
    }

    /**
     * @param mixed $returnReasonCollection
     */
    public function setReturnReasonCollection( $returnReasonCollection ) {
        $this->returnReasonCollection = $returnReasonCollection;
    }

    /**
     * @param array $errors
     */
    public function setErrors( array $errors ) {
        $this->errors = array_unique($errors);
        $errorHTML = '';
        foreach($this->errors as $error) {
            $errorHTML .= $this->templateEngine->prepareHTMLSnippet('ERRORBOX', array(array('title' => 'INNERHTML', 'value' => $error)));
        }
        $this->setErrorString($errorHTML);
    }

    /**
     * @return array
     */
    public function getData() {
        $arr = [
            'titleString' =>$this->titleString,
            'mainMessageString' =>$this->mainMessageString,
            'errorString' =>$this->errorString,
            'noticeString' =>$this->noticeString,
            'shipmentLabel' =>$this->shipmentLabel,
            'shipmentNoLabel' =>$this->shipmentNoLabel,
            'shipment' =>$this->shipment,
            'nameLabel' =>$this->nameLabel,
            'postingDateLabel' =>$this->postingDateLabel,
            'addressLabel' =>$this->addressLabel,
            'postCodeLabel' =>$this->postCodeLabel,
            'cityLabel' =>$this->cityLabel,
            'inputEMail' =>$this->inputEMail,
            'inputPostCode' =>$this->inputPostCode,
            'inputRequest' =>$this->inputRequest,
            'itemNoLabel' =>$this->itemNoLabel,
            'descriptionLabel' =>$this->descriptionLabel,
            'quantityLabel' =>$this->quantityLabel,
            'qtyReturnedLabel' =>$this->qtyReturnedLabel,
            'qtyRemainingLabel' =>$this->qtyRemainingLabel,
            'returnQuantityLabel' =>$this->returnQuantityLabel,
            'returnReasonLabel' =>$this->returnReasonLabel,
            'pleaseChooseText' =>$this->pleaseChooseText,
            'returnReasonCollection' =>$this->returnReasonCollection,
            'linePrefill' =>$this->linePrefill,
            'yourReferenceLabel' =>$this->yourReferenceLabel,
            'linkBack' =>$this->linkBack,
            'backButtonLabel' =>$this->backButtonLabel,
            'sendButtonLabel' =>$this->sendButtonLabel,
            'styleFilePath' =>$this->styleFilePath
        ];
        return $arr;
    }

}