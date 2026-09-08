<?php
namespace DynCom\dc\dcShop\rma\classes;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 22.01.2015
 * Time: 12:26
 */
use DynCom\dc\dcShop\classes\CurrShopConfiguration;
use DynCom\dc\dcShop\interfaces\TemplatingInterface;

/**
 * Class RMASearchController
 */
class RMASearchController {

    protected $model;
    protected $retShipmentRepository;
    protected $currShopConfig;
    protected $templateEngine;
    protected $maxDaysBack = 0;
    protected $inputRequest;
    protected $inputEMail;
    protected $inputPostCode;

    /**
     * @param RMASearchViewModel $viewModel
     * @param RetShipmentRepository $repository
     * @param TemplatingInterface $templateEngine
     * @param |CurrShopConfiguration $currShopConfig
     */
    public function __construct(RMASearchViewModel $viewModel, RetShipmentRepository $repository, TemplatingInterface $templateEngine, CurrShopConfiguration $currShopConfig ) {
        $this->model                 = $viewModel;
        $this->retShipmentRepository = $repository;
        $this->currShopConfig        = $currShopConfig;
        $this->templateEngine        = $templateEngine;

        $shop = $this->currShopConfig->getShop();
        $customer = $this->currShopConfig->getCustomer();
        $this->maxDaysBack           = (($shop->max_days_shipment_returnable > 0) ? $shop->max_days_shipment_returnable : 0);

        if(isset($_REQUEST['input_rma_request'])) {
            $this->inputRequest = filter_var($_REQUEST['input_rma_request'], FILTER_SANITIZE_STRING);
        }
        if (isset($_REQUEST['input_rma_email'])) {
            $this->inputEMail    = filter_var($_REQUEST['input_rma_email'], FILTER_SANITIZE_STRING);
        }
        if (isset($_REQUEST['input_rma_post_code'])) {
            $this->inputPostCode = filter_var($_REQUEST['input_rma_post_code'], FILTER_SANITIZE_STRING);
        }

        if($shop->shop_typ == 0 || $shop->shop_typ == 2 || ($shop->shop_typ == 1 && !empty($customer->customer_no))) {
            $this->model->setHasLogin(TRUE);
            $this->model->setMainMessageString($this->templateEngine->getText('rma_text_login'));
        } elseif($shop->shop_typ == 1 && empty($shop->customer_no)) {
            $this->model->setHasLogin(FALSE);
            $this->model->setMainMessageString($this->templateEngine->getText('rma_text_no_login'));
        }
    }

    public function show() {
        $customer = $this->currShopConfig->getCustomer();
        $shipmentCollection = $this->retShipmentRepository->getAllForCustomer($customer,$this->maxDaysBack);
        $this->model->setRetShipmentCollection($shipmentCollection);
    }

    public function search() {

        $shop = $this->currShopConfig->getShop();
        $shopLanguage = $this->currShopConfig->getShopLanguage();
        $customer = $this->currShopConfig->getCustomer();
        $shipmentCollection = $this->retShipmentRepository->getEmptyCollection();

        $errorArray = $this->_getSearchErrorArray();
        if (count($errorArray) > 0) {
            $this->model->setErrors($errorArray);
            $this->model->setRetShipmentCollection($shipmentCollection);
        } else {
            if ($shop->shop_typ == 0 || $shop->shop_typ == 2) {
                $shipmentCollection = $this->retShipmentRepository->searchB2B($customer, $this->inputRequest, $this->maxDaysBack);
            } elseif ($shop->shop_typ == 1 && !empty($customer->customer_no)) {
                $shipmentCollection = $this->retShipmentRepository->searchB2CWithLogin($customer, $this->inputRequest, $this->maxDaysBack);
            } elseif ($shop->shop_typ == 1 && empty($customer->customer_no)) {
                $shipmentCollection = $this->retShipmentRepository->searchB2CWithoutLogin($this->inputRequest, $this->inputEMail, $this->inputPostCode, $shop->code, $shopLanguage->code, $this->maxDaysBack);
            }
            if (!(count($shipmentCollection) > 0)) {
                $this->model->setNoticeString($this->templateEngine->getText('no_results'));
            }
            $this->model->setRetShipmentCollection($shipmentCollection);
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function _getSearchErrorArray() {

        $shop = $this->currShopConfig->getShop();
        $user = $this->currShopConfig->getUser();
        $customer = $this->currShopConfig->getCustomer();
        //$IOC = $_SESSION['IOC'];
        //$customer = $IOC->create('$CurrCustomer');
        $errors = array();

        if (empty($this->inputRequest)) {
            $errors[] = $this->templateEngine->getText('error_no_request');
        }

        if(($this->currShopConfig->getShopType() == 0) && !($user->hasReturnOrderPermission())) {;
            $errors[] = $this->templateEngine->getText('no_rma_permission_for_user');
        }

        if (($shop->shop_typ == 0 || $shop->shop_typ == 2) && (empty($customer->customer_no))) {
            throw new \Exception('RMASearchController was called outside of B2B, B2C or Salesperson - this should not have happened.');
        }

        if ($shop->shop_typ == 1 && empty($customer->customer_no)) {
            if (empty($this->inputEMail) || !filter_var($this->inputEMail, FILTER_VALIDATE_EMAIL)) {
                $errors[] = $this->templateEngine->getText('error_no_valid_email');
            }
            if (empty($this->inputPostCode)) {
                $errors[] = $this->templateEngine->getText('error_no_post_code');
            }
        }
        return $errors;
    }

}