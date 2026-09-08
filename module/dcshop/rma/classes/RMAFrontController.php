<?php
namespace DynCom\dc\dcShop\rma\classes;
use DynCom\dc\common\classes\BasicControlFlowHandler;
use DynCom\dc\common\classes\GenericView;
use DynCom\dc\common\classes\TemplateInserterFactory;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 29.01.2015
 * Time: 12:25
 */
class RMAFrontController
{

    protected $shopConfiguration;
    protected $inputRequest;
    protected $inputEMail;
    protected $inputPostCode;
    protected $inputHeaderID;
    protected $inputReference;
    protected $inputLines;
    protected $helper;
    protected $retShipmentRepository;
    protected $templateEngine;

    protected $shop;
    protected $maxDaysBack;
    protected $visitor;
    protected $user;
    protected $customer;

    protected $inputReturnShipment;
    protected $returnableShipmentCollection;

    protected $errors = array();

    /**
     * @param RMAOrderHelper $helper
     */
    public function __construct(RMAOrderHelper $helper)
    {

        $this->helper                   =   $helper;
        $this->shopConfiguration        =   $helper->getShopConfiguration();
        $this->retShipmentRepository    =   $helper->getShipmentRepository();
        $this->templateEngine           =   $helper->getTemplateEngine();

        $this->inputRequest     = array_key_exists('input_rma_request',$_REQUEST)     ?   filter_var($_REQUEST['input_rma_request'], FILTER_SANITIZE_STRING)        : '';
        $this->inputEMail       = array_key_exists('input_rma_email',$_REQUEST)       ?   filter_var($_REQUEST['input_rma_email'], FILTER_SANITIZE_STRING)          : '';
        $this->inputPostCode    = array_key_exists('input_rma_post_code',$_REQUEST)   ?   filter_var($_REQUEST['input_rma_post_code'], FILTER_SANITIZE_STRING)      : '';
        $this->inputHeaderID    = array_key_exists('input_header_id',$_REQUEST)       ?   filter_var($_REQUEST['input_header_id'], FILTER_SANITIZE_NUMBER_INT)      : 0;
        $this->inputReference   = array_key_exists('input_your_reference',$_REQUEST)  ?   filter_var($_REQUEST['input_your_reference'], FILTER_SANITIZE_STRING)     : '';
        $this->inputLines       = array_key_exists('input_lines',$_REQUEST)           ?   $_REQUEST['input_lines']                                                  : [];


        $this->shop         = $this->shopConfiguration->getShop();
        $this->maxDaysBack  = (int)$this->shop->max_days_shipment_returnable;
        $this->visitor      = $this->shopConfiguration->getVisitor();
        $this->user         = $this->shopConfiguration->getUser();
        $this->customer     = $this->shopConfiguration->getCustomer();
    }

    /**
     * @return bool
     */
    public function handleRequest()
    {
        //Data-Workflow Separation
        //Define conditions and actions (as booleans and anonymous functions providing booleans)
        $conditionIsActionSearch        =   (!array_key_exists('action',$_GET) || $_GET['action'] === 'show' || $_GET['action'] === 'search');
        $conditionIsActionViewShipment  =   ($_GET['action'] === 'view-shipment');
        $conditionIsActionSave          =   ($_GET['action'] === 'save');
        $conditionIsActionConfirmToken  =   ($_GET['action'] === 'confirm');
        $conditionIsHeaderIDSet         =   ($this->inputHeaderID > 0);
        $conditionDoFindHeader          =   (($conditionIsActionViewShipment || $conditionIsActionSave) && $conditionIsHeaderIDSet);
        $conditionIsHeaderFound         =   function() use($conditionDoFindHeader) {
            $this->inputReturnShipment = $this->retShipmentRepository->findByID($this->inputHeaderID);
            return ($conditionDoFindHeader && isset($this->inputReturnShipment) && $this->inputReturnShipment->id > 0);
        };
        $conditionDoUserPermissionCheck =   function() use($conditionDoFindHeader,&$conditionIsHeaderFound) {
            if(!$conditionDoFindHeader) return false;
            if(is_callable($conditionIsHeaderFound)) $conditionIsHeaderFound = $conditionIsHeaderFound();
            return ((bool)$conditionIsHeaderFound);
        };
        $conditionUserHasPermission     =   function() use(&$conditionDoUserPermissionCheck) {
            if(is_callable($conditionDoUserPermissionCheck)) $conditionDoUserPermissionCheck = $conditionDoUserPermissionCheck();
            if(!$conditionDoUserPermissionCheck) return false;
            return $this->helper->checkUserShipmentPermission($this->user, $this->inputReturnShipment, $this->inputEMail, $this->inputPostCode);
        };
        $conditionDoSaveOrderAction     =   function() use($conditionIsActionSave,&$conditionUserHasPermission) {
            if(!$conditionIsActionSave) return false;
            if(is_callable($conditionUserHasPermission)) $conditionUserHasPermission = $conditionUserHasPermission();
            return($conditionUserHasPermission);
        };
        $conditionIsReturnOrderSaved    =   function() use(&$conditionDoSaveOrderAction) {
            if(is_callable($conditionDoSaveOrderAction))$conditionDoSaveOrderAction = $conditionDoSaveOrderAction();
            return ($conditionDoSaveOrderAction && $this->helper->saveReturnOrder($this->inputReturnShipment, $this->inputReference, $this->inputLines));
        };
        $conditionSendConfirmationMail  =   function() {
            $saveStatus = ($this->helper->getSaveStatus($this->inputReturnShipment->id));
            $tokenConfirmRequired = ($saveStatus === RMAOrderHelper::SAVE_STATUS_WITH_TOKEN);
            return $tokenConfirmRequired;
        };
        $conditionIsTokenConfirmed      =   function() {
            $shipmentID = filter_var($_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT);
            $token      = filter_var($_REQUEST['token'], FILTER_SANITIZE_STRING);
            return $this->helper->attemptTokenResolution($shipmentID, $token);
        };

        $actionDoSearch                 =   function() {$this->_showSearchPage();};
        $actionViewShipment             =   function() {$this->_showShipmentPage($this->inputReturnShipment);};
        $actionSaveShipmentSuccess      =   function() {			
            $this->_showSuccessPage($this->inputReturnShipment->return_order_shop_no);
        };
        $actionSaveShipmentFail         =   function() {
            $this->errors = $this->helper->getErrors();
            $this->_showShipmentPage($this->inputReturnShipment);
        };
        $actionRequestConfirmation      =   function() {
            $this->_requestTokenConfirmation($this->inputReturnShipment);
        };
        $actionViewShipmentNoRecordSet  =   function() {
            $this->errors[] = $this->templateEngine->getText('record_not_set');
            $this->_showSearchPage();
        };
        $actionViewShipmentNoPermission =   function() {
            $this->errors[] = $this->templateEngine->getText('record_not_permitted');
            $this->_showSearchPage();
        };
        $actionTokenConfirmFail         =   function() {
            $this->errors = $this->helper->getErrors();
            $this->_showSearchPage();
        };
        $actionTokenConfirmSuccess      =   function() {
            $shipmentID = filter_var($_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT);
            $shipment   = $this->retShipmentRepository->findByID($shipmentID);
            $this->_showSuccessPage($shipment->return_order_shop_no);
        };
        //Data-Workflow separation
        //Define Workflow
        //Every Sub-Array has form: [0] => condition [1] => ifConditionTrue ([2] => ifConditionFalse)
        $controlFlow = [
            [$conditionIsActionSearch,$actionDoSearch],
            [$conditionIsActionViewShipment,[$conditionIsHeaderFound,[$conditionUserHasPermission,$actionViewShipment,$actionViewShipmentNoPermission],$actionViewShipmentNoRecordSet]],
            [$conditionIsActionSave,[$conditionIsReturnOrderSaved,[$conditionSendConfirmationMail,$actionRequestConfirmation,$actionSaveShipmentSuccess],$actionSaveShipmentFail]],
            [$conditionIsActionConfirmToken,[$conditionIsTokenConfirmed,$actionTokenConfirmSuccess,$actionTokenConfirmFail]]
        ];

        //Evaluate Workflow
        $controlFlowHandler = new BasicControlFlowHandler();
        $controlFlowHandler->evaluateControlFlowArray($controlFlow);

    }

    protected function _showSearchPage()
    {

        $retShipmentCollection = $this->retShipmentRepository->getAllForCustomer($this->customer, $this->maxDaysBack);
        $viewModel = new RMASearchViewModel($this->templateEngine, $retShipmentCollection, (bool)$this->visitor->frontend_login);
        $template = new RMASearchPHTMLTemplate();
        $subController = new RMASearchController($viewModel, $this->retShipmentRepository, $this->templateEngine, $this->shopConfiguration);

        $viewModel->setErrors($this->errors);

        if (!array_key_exists('action',$_GET) || $_GET['action'] === 'show') {
            $subController->show();
        } elseif ($_GET['action'] === 'search' || (strlen($this->inputRequest) > 0)) {
            $subController->search();
        }

        $view = new GenericView($template, $viewModel);
        $view->render(new TemplateInserterFactory());

        $view->echoContent();

    }

    /**
     * @param RetShipmentDocument $shipment
     */
    protected function _showShipmentPage(RetShipmentDocument $shipment)
    {
        $retReasonCollection    = $this->helper->getAllReturnReasons();
        $viewModel              = new RMAShipmentViewModel($this->templateEngine, $shipment, $retReasonCollection);
        $template               = new RMAShipmentViewPHTMLTemplate();
        $viewModel->setErrors($this->errors);

        $view = new GenericView($template,$viewModel);
        $view->render(new TemplateInserterFactory());
        $view->echoContent();
    }

    /**
     * @param RetShipmentDocument $shipment
     */
    protected function _requestTokenConfirmation(RetShipmentDocument $shipment)
    {

        $tokenLinkBase = $this->shopConfiguration->getBaseShopUrl();

        $textModule = $this->helper->getConfirmMailTextModule();
        $mainText = $textModule->content;

        $name = $shipment->sell_to_name;
        $shipment_no = $shipment->no;
        $return_order_no = $shipment->return_order_shop_no;
        if ($tokenLinkBase != '' && strlen($shipment->return_order_token) > 0) {
            $link = $tokenLinkBase . '?shop_category=rma&action=confirm&id=' . $shipment->id . '&token=' . $shipment->return_order_token;
        } else {
            $link = $tokenLinkBase;
        }

        //Access lines via IteratorAggregate-interface
        $linesHTML = "<ul>";
        foreach ($shipment as $line) {
            if ($line->return_quantity > 0) {
                $linesHTML .= "<li><strong>{$this->templateEngine->getText('item_no')}:</strong> {$line->no} - <strong>{$this->templateEngine->getText('return_quantity')}:</strong> {$line->quantity}</li>";
            }
        }
        $linesHTML .= "</ul>";

        //Replace
        $mainText = str_replace('%customer_name%', $name, $mainText);
        $mainText = str_replace('%shipment_no%', $shipment_no, $mainText);
        $mainText = str_replace('%return_order_no%', $return_order_no, $mainText);
        $mainText = str_replace('%confirm_link%', $link, $mainText);
        $mainText = str_replace('%return_lines%', $linesHTML, $mainText);

        $viewModel = new RMAConfirmationMailViewModel($mainText);
        $template = new RMAConfirmationMailPHTMLTemplate();
        $view = new GenericView($template,$viewModel);
        $view->render(new TemplateInserterFactory());

        if ($this->shopConfiguration->visitorLoggedIn()) {
            $userEmail = $this->shopConfiguration->getUser()->email;
        } else {
            $userEmail = $this->inputEMail;
        }


        if (!$this->helper->sendConfirmationRequestMail($shipment, $view, $textModule, $userEmail)) {
            $this->errors = $this->helper->getErrors();
            $this->_showShipmentPage($shipment);
        }
        $this->_showConfirmationRequestPage();

    }

    protected function _showConfirmationRequestPage()
    {
        $titleString = $this->templateEngine->getText('rma_title');
        $mainMessageString = $this->templateEngine->getText('rma_confirmation_mail_sent');
        $viewModel = new RMAGenericViewModel($titleString, $mainMessageString);
        $template = new RMAGenericViewPHTMLTemplate();
        $view = new GenericView($template,$viewModel);
        $view->render(new TemplateInserterFactory());
        $view->echoContent();
    }

    /**
     * @param $retOrderNo
     */
    protected function _showSuccessPage($retOrderNo)
    {
        $titleString = $this->templateEngine->getText('rma_title');
        $mainMessageString = $this->helper->getRetOrderCompleteTextModule()->content;
        $this->templateEngine->replacePlaceholder($mainMessageString, 'order_no', $retOrderNo);
        $viewModel = new RMAGenericViewModel($titleString, $mainMessageString);
        $template = new RMAGenericViewPHTMLTemplate();
        $view = new GenericView($template,$viewModel);
        $view->render(new TemplateInserterFactory());
        $view->echoContent();
    }

}


