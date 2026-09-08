<?php
namespace DynCom\dc\dcShop\rma\classes;
use DynCom\dc\common\interfaces\ViewModel;
use DynCom\dc\common\traits\universallyGettableTrait;
use DynCom\dc\dcShop\interfaces\TemplatingInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 22.01.2015
 * Time: 09:40
 */

class RMASearchViewModel implements ViewModel {

    use universallyGettableTrait;

    protected $styleFilePath = '/module/dcshop/rma/styles/rma.css';

    protected $templateEngine;
    protected $hasLogin;

    protected $searchFieldLabel;
    protected $emailLabel;
    protected $submitLabel;
    protected $shipmentNoLabel;
    protected $postingDateLabel;
    protected $yourReferenceLabel;
    protected $addressLabel;
    protected $postCodeLabel;
    protected $cityLabel;
    protected $showShipmentLabel;
    protected $searchPrefill;
    protected $scriptletPath = '/module/dcshop/rma/scripts/RMASearchScriptlet.js';

    protected $titleString;
    protected $mainMessageString = '';
    protected $errors            = array();
    protected $errorString       = '';
    protected $noticeString      = '';
    protected $retShipmentCollection;

    protected $inputRequest = '';
    protected $inputEMail = '';
    protected $inputPostCode = '';

    /**
     * @param TemplatingInterface $templateEngine
     * @param RetShipmentCollection $retShipmentCollection
     * @param bool $hasLogin
     */
    public function __construct( TemplatingInterface $templateEngine, RetShipmentCollection $retShipmentCollection, $hasLogin = FALSE ) {

        /* Parameters */
        $this->templateEngine        = $templateEngine;
        $this->retShipmentCollection = $retShipmentCollection;
        $this->hasLogin              = $hasLogin;

        /* Input */
        if(isset($_REQUEST['input_rma_request'])) {
            $this->inputRequest = filter_var($_REQUEST['input_rma_request'], FILTER_SANITIZE_STRING);
        }
        if(isset($_REQUEST['input_rma_email'])) {
            $this->inputEMail = filter_var($_REQUEST['input_rma_email'], FILTER_SANITIZE_STRING);
        }
        if(isset($_REQUEST['input_rma_post_code'])) {
            $this->inputPostCode = filter_var($_REQUEST['input_rma_post_code'], FILTER_SANITIZE_STRING);
        }

        /* Labels */
        $this->addressLabel       = $this->templateEngine->getText('address');
        $this->cityLabel          = $this->templateEngine->getText('city');
        $this->submitLabel        = $this->templateEngine->getText('search');
        $this->postCodeLabel      = $this->templateEngine->getText('post_code');
        $this->postingDateLabel   = $this->templateEngine->getText('posting_date');
        $this->searchFieldLabel   = $this->templateEngine->getText('search_term');
        $this->shipmentNoLabel    = $this->templateEngine->getText('shipment_no');
        $this->yourReferenceLabel = $this->templateEngine->getText('your_reference');
        $this->emailLabel         = $this->templateEngine->getText('email');
        $this->showShipmentLabel  = $this->templateEngine->getText('show_shipment');

        /* Texts*/
        $this->titleString = $this->templateEngine->getText('rma_title');
        $this->mainMessageString = $this->templateEngine->getText('rma_text_login');
        $this->searchPrefill = $this->templateEngine->getText('rma_search_field_text');
    }


    /**
     * @param string $searchFieldLabel
     */
    public function setSearchFieldLabel( $searchFieldLabel ) {
        $this->searchFieldLabel = $searchFieldLabel;
    }


    /**
     * @param string $submitLabel
     */
    public function setSubmitLabel( $submitLabel ) {
        $this->submitLabel = $submitLabel;
    }


    /**
     * @param string $shipmentNoLabel
     */
    public function setShipmentNoLabel( $shipmentNoLabel ) {
        $this->shipmentNoLabel = $shipmentNoLabel;
    }

    /**
     * @param string $postingDateLabel
     */
    public function setPostingDateLabel( $postingDateLabel ) {
        $this->postingDateLabel = $postingDateLabel;
    }

    /**
     * @param string $yourReferenceLabel
     */
    public function setYourReferenceLabel( $yourReferenceLabel ) {
        $this->yourReferenceLabel = $yourReferenceLabel;
    }

    /**
     * @param string $addressLabel
     */
    public function setAddressLabel( $addressLabel ) {
        $this->addressLabel = $addressLabel;
    }

    /**
     * @param string $postCodeLabel
     */
    public function setPostCodeLabel( $postCodeLabel ) {
        $this->postCodeLabel = $postCodeLabel;
    }

    /**
     * @param string $cityLabel
     */
    public function setCityLabel( $cityLabel ) {
        $this->cityLabel = $cityLabel;
    }

    /**
     * @param string $titleString
     */
    public function setTitleString( $titleString ) {
        $this->titleString = $titleString;
    }

    /**
     * @param string $mainMessageString
     */
    public function setMainMessageString( $mainMessageString ) {
        $this->mainMessageString = $mainMessageString;
    }


    /**
     * @param array $errors
     */
    public function setErrors( array $errors ) {
        $this->errors = array_unique($errors);
        $errorHTML = '';
        foreach($this->errors as $error) {
            $errorHTML .= $this->templateEngine->prepareHTMLSnippet('ERRORBOX', array(array('title' => 'INNERHTML', 'value' => $error)),'CLEANUP_ALL');
        }
        $this->setErrorString($errorHTML);
    }

    /**
     * @param string $errorString
     */
    public function setErrorString( $errorString ) {
        $this->errorString = $errorString;
    }

    /**
     * @param string $noticeString
     */
    public function setNoticeString( $noticeString ) {
        $this->noticeString = $noticeString;
    }

    /**
     * @param string $inputRequest
     */
    public function setInputRequest( $inputRequest ) {
        $this->inputRequest = $inputRequest;
    }

    /**
     * @param  $retShipmentCollection
     */
    public function setRetShipmentCollection( $retShipmentCollection ) {
        $this->retShipmentCollection = $retShipmentCollection;
    }

    /**
     * @param string $inputEMail
     */
    public function setInputEMail( $inputEMail ) {
        $this->inputEMail = $inputEMail;
    }

    /**
     * @param string $inputPostCode
     */
    public function setInputPostCode( $inputPostCode ) {
        $this->inputPostCode = $inputPostCode;
    }

    /**
     * @param string $emailLabel
     */
    public function setEmailLabel( $emailLabel ) {
        $this->emailLabel = $emailLabel;
    }

    /**
     * @param string $scriptletPath
     */
    public function setScriptletPath( $scriptletPath ) {
        $this->scriptletPath = $scriptletPath;
    }

    /**
     * @param TemplatingInterface $templateEngine
     */
    public function setTemplateEngine( $templateEngine ) {
        $this->templateEngine = $templateEngine;
    }

    /**
     * @param bool $hasLogin
     */
    public function setHasLogin( $hasLogin ) {
        $this->hasLogin = (bool) $hasLogin;
    }

    /**
     * @param string $template
     */
    public function setTemplate( $template ) {
        $this->template = $template;
    }

    /**
     * @param mixed $showShipmentLabel
     */
    public function setShowShipmentLabel( $showShipmentLabel ) {
        $this->showShipmentLabel = $showShipmentLabel;
    }

    /**
     * @param string $styleFilePath
     */
    public function setStyleFilePath( $styleFilePath ) {
        $this->styleFilePath = $styleFilePath;
    }

    /**
     * @return array
     */
    public function getData() {
        $arr = [
            'styleFilePath' => $this->styleFilePath,
            'hasLogin' => $this->hasLogin,
            'searchFieldLabel' => $this->searchFieldLabel,
            'emailLabel' => $this->emailLabel,
            'submitLabel' => $this->submitLabel,
            'shipmentNoLabel' => $this->shipmentNoLabel,
            'postingDateLabel' => $this->postingDateLabel,
            'yourReferenceLabel' => $this->yourReferenceLabel,
            'addressLabel' => $this->addressLabel,
            'postCodeLabel' => $this->postCodeLabel,
            'cityLabel' => $this->cityLabel,
            'showShipmentLabel' => $this->showShipmentLabel,
            'searchPrefill' => $this->searchPrefill,
            'scriptletPath' => $this->scriptletPath,
            'titleString' => $this->titleString,
            'mainMessageString' => $this->mainMessageString,
            'errors' => $this->errors,
            'errorString' => $this->errorString,
            'noticeString' => $this->noticeString,
            'retShipmentCollection' => $this->retShipmentCollection,
            'inputRequest' => $this->inputRequest,
            'inputEMail' => $this->inputEMail,
            'inputPostCode' => $this->inputPostCode
        ];
        return $arr;
    }

}