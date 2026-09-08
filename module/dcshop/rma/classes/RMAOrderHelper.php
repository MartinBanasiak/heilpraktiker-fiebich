<?php
namespace DynCom\dc\dcShop\rma\classes;
use DynCom\dc\common\classes\UnitOfWork;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\common\interfaces\GenericViewInterface;
use DynCom\dc\dcShop\classes\CurrShopConfiguration;
use DynCom\dc\dcShop\classes\TextModule;
use DynCom\dc\dcShop\classes\TextModuleRepository;
use DynCom\dc\dcShop\classes\User;
use DynCom\dc\dcShop\interfaces\TemplatingInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 28.01.2015
 * Time: 13:02
 */
class RMAOrderHelper {

    const SAVE_STATUS_UNSAVED       = 'UNSAVED';
    const SAVE_STATUS_WITHOUT_TOKEN = 'SAVED WITHOUT TOKEN';
    const SAVE_STATUS_WITH_TOKEN    = 'SAVED WITH TOKEN';

    protected $shipmentRepository;
    protected $reasonRepository;
    protected $shopConfiguration;
    protected $templateEngine;
    protected $textModuleRepository;

    protected $saveStatusArr = array();

    protected $errors                = array();
    protected $validatedLineRequests = array();

    /**
     * RMAOrderHelper constructor.
     * @param RetShipmentRepository $shipmentRepository
     * @param ReturnReasonRepository $reasonRepository
     * @param CurrShopConfiguration $shopConfiguration
     * @param TemplatingInterface $templateEngine
     * @param TextModuleRepository $textModuleRepository
     * @param GenericDBQueryWrapperInterface $db
     */
    public function __construct( RetShipmentRepository $shipmentRepository, ReturnReasonRepository $reasonRepository, CurrShopConfiguration $shopConfiguration, TemplatingInterface $templateEngine, TextModuleRepository $textModuleRepository, GenericDBQueryWrapperInterface $db ) {
        $this->shipmentRepository   = $shipmentRepository;
        $this->reasonRepository     = $reasonRepository;
        $this->shopConfiguration    = $shopConfiguration;
        $this->templateEngine       = $templateEngine;
        $this->textModuleRepository = $textModuleRepository;
        $this->db                   = $db;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getShipmentByID( $id ) {
        return $this->shipmentRepository->findByID((int)$id);
    }

    /**
     * @return \DynCom\dc\common\interfaces\GenericCollectionInterface
     */
    public function getAllReturnReasons() {
        $criteria = array(
            array(
                array('company','=',$this->shopConfiguration->getCompany())
            )
        );

        return $this->reasonRepository->findByCriteria($criteria);
    }

    /**
     * @param $code
     * @return \DynCom\dc\common\interfaces\GenericCollectionInterface
     */
    public function getReturnReasonByCode( $code ) {
        $company  = $this->shopConfiguration->getCompany();
        $criteria = array(
            array(
                array('company', '=', $company),
                array('code', '=', (string)$code)
            )
        );
        return $this->reasonRepository->findByCriteria($criteria);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function checkUserRMAPermission( User $user ) {
        $shopType = $this->shopConfiguration->getShopType();
        if ($shopType === 1) {
            return TRUE;
        }
        if ($shopType > 2) {
            return FALSE;
        }
        return $user->hasReturnOrderPermission();
    }

    /**
     * @return bool
     */
    public function checkCurrUserRMAPermission() {
        $user = $this->shopConfiguration->getUser();
        return $this->checkUserRMAPermission($user);
    }

    /**
     * @param User $user
     * @param RetShipmentDocument $shipment
     * @param string $inputEmail
     * @param string $inputPostCode
     * @return bool
     */
    public function checkUserShipmentPermission( User $user, RetShipmentDocument $shipment, $inputEmail = '', $inputPostCode = '' ) {
        if (!(strlen($shipment->no) > 0)) {
            return FALSE;
        }
        $userRMAPermitted = $this->checkUserRMAPermission($user);
        if (!$userRMAPermitted && $this->shopConfiguration->getShopType() == 0) {
            return FALSE;
        }

        $shop        = $this->shopConfiguration->getShop();
        $maxDaysBack = (int)$shop->max_days_shipment_returnable;
        $postingDate = date_create($shipment->posting_date);
        $cutoffDate  = $postingDate;
        date_sub($cutoffDate, date_interval_create_from_date_string((string)$maxDaysBack . ' days'));

        //Check Shipment Company matches Shop
        if (($shipment->company != $this->shopConfiguration->getCompany()) || (empty($shipment->company))) {
            return FALSE;
        }

        if ($this->shopConfiguration->getShopType() == 1) {
            if (strlen($user->customer_no) > 0) {
                //Check Shipment Company matches User
                if ($shipment->company !== $user->company) {
                    return FALSE;
                }
                //Check Shop-Code
                if (($shipment->shop_code !== $this->shopConfiguration->getShopCode()) || (empty($shipment->shop_code))) {
                    return FALSE;
                }
                //No checking for language code - shipments from any language for this shop should be available

                //Check Customer-No
                if (($shipment->sell_to_customer_no !== $user->customer_no) && ($shipment->bill_to_customer_no !== $user->customer_no)) {
                    return FALSE;
                }
                //Check E-Mail
                if (($shipment->user_email !== $user->email) || (empty($shipment->user_email))) {
                    return FALSE;
                }
                //Check Max Days Back
                if (($maxDaysBack > 0) && ($postingDate > $cutoffDate)) {
                    return FALSE;
                }
                return (!(($maxDaysBack > 0) && ($postingDate > $cutoffDate)));
            } else {
                //Check Shipment Company matches Shop
                if ($shipment->company !== $shop->company) {
                    return FALSE;
                }
                //Compare inputEmail
                if (($shipment->user_email !== $inputEmail) || (empty($shipment->user_email))) {
                    return FALSE;
                }
                //Compare postCode
                return (!((($shipment->sell_to_post_code !== $inputPostCode) && ($shipment->bill_to_post_code !== $inputPostCode)) || (empty($inputPostCode))));
            }
        } else {
            //Check Shipment Company matches User
            if ($shipment->company !== $user->company) {
                return FALSE;
            }
            //Check Customer-No
            if (($shipment->sell_to_customer_no !== $user->customer_no) && ($shipment->bill_to_customer_no !== $user->customer_no)) {
                return FALSE;
            }
            //Check Max Days Back
            return (!(($maxDaysBack > 0) && ($postingDate > $cutoffDate)));
        }
    }

    /**
     * @param $shipmentID
     *
     * @return int
     */
    public function getSaveStatus( $shipmentID ) {
        if(array_key_exists($shipmentID,$this->saveStatusArr)) {
            return $this->saveStatusArr[$shipmentID];
        }
        return self::SAVE_STATUS_UNSAVED;
    }

    /**
     * @param RetShipmentDocument $shipment
     * @param $inputLines
     * @return bool
     */
    protected function _validateReturnOrderLineInput( RetShipmentDocument $shipment, $inputLines ) {

        if (!is_array($inputLines)) {
            $this->errors[] = $this->templateEngine->getText('internal_error');
            return FALSE;
        }
        $lineRequests          = array();
        $i                     = 0;
        $givenReturnQuantities = 0;
        $givenReturnReasons    = 0;
        foreach ($inputLines as $lineRequest) {
            $lineID         = filter_var($lineRequest['line_id'], FILTER_SANITIZE_NUMBER_INT);
            $returnQuantity = filter_var($lineRequest['return_quantity'], FILTER_SANITIZE_NUMBER_FLOAT);
            $returnReasonID = filter_var($lineRequest['return_reason_id'], FILTER_SANITIZE_NUMBER_INT);

            if ($returnQuantity > 0) {
                ++$givenReturnQuantities;
            }
            if ($returnReasonID > 0) {
                ++$givenReturnReasons;
            }
            if ($returnQuantity > 0 && $returnReasonID > 0) {
                $lineRequests[$i]['line_id']          = $lineID;
                $lineRequests[$i]['return_quantity']  = $returnQuantity;
                $lineRequests[$i]['return_reason_id'] = $returnReasonID;
                ++$i;
            }
        }

        if ($givenReturnQuantities == 0 || $givenReturnReasons == 0 || $givenReturnReasons != $givenReturnQuantities) {
            $this->errors[] = $this->templateEngine->getText('no_return_quantities_reasons');
            return FALSE;
        }

        $lines = array();
        foreach ($lineRequests as &$lineRequest) {

            $lineRepository = $this->shipmentRepository->getLineRepository();
            $shipmentLine   = $lineRepository->findByID($lineRequest['line_id']);
            if (!((int)$shipmentLine->id > 0)) {
                $devError       = "
                ERROR in file '" . __FILE__ . "', line " . __LINE__ . "
                Description: No Shipment line found for requested id: {$lineRequest['line_id']}
                ";
                $this->errors[] = $this->templateEngine->getText('internal_error') ;
                return FALSE;
            }
            if (($shipmentLine->company != $shipment->company) || ($shipmentLine->document_no != $shipment->no)) {
                $this->errors[] = $this->templateEngine->getText('internal_error') ;
                return FALSE;
            }
            if ($shipmentLine->returnable_quantity < $lineRequest['return_quantity']) {
                $this->errors[] = $this->templateEngine->getText('return_qty_exceeds_returnable');
                return FALSE;
            }

            $reason = $this->reasonRepository->findByID($lineRequest['return_reason_id']);
            if (($reason->company !== $this->shopConfiguration->getCompany()) || !(strlen($reason->code) > 0)) {
                $this->errors[] = $this->templateEngine->getText('internal_error') ;
                return FALSE;
            }
            $lineRequest['return_reason_code'] = $reason->code;
            $lines[]                           = $shipmentLine;
        }
        unset($lineRequest);
        if (!(count($lines) === count($lineRequests))) {
            return FALSE;
        }
        $this->validatedLineRequests[$shipment->id] = $lineRequests;
        return TRUE;
    }

    /**
     * @param RetShipmentDocument $shipment
     */
    protected function _unsetReturnOrder( RetShipmentDocument $shipment ) {
        $lineRepository = $this->shipmentRepository->getLineRepository();
        foreach ($shipment as $line) {
            if($line instanceof RetShipmentLine) {
                $line->unsetReturnOrder();
                $lineRepository->updateSingle($line);
            }
        }
        $shipment->unsetReturnOrder();
        $this->shipmentRepository->updateSingle($shipment);
    }

    /**
     * @param RetShipmentDocument $shipment
     * @param $yourReference
     * @param $inputLines
     * @return bool
     */
    public function saveReturnOrder( RetShipmentDocument $shipment, $yourReference, $inputLines ) {
        if (!$shipment->isReturnOrderSettable()) {
            $this->errors[] = $this->templateEngine->getText('internal_error') ;
            return FALSE;
        }
        if (!$this->_validateReturnOrderLineInput($shipment, $inputLines)) {
            return FALSE;
        }
        if (!is_array($this->validatedLineRequests[$shipment->id])) {
            $this->errors[] = $this->templateEngine->getText('internal_error') ;
            return FALSE;
        }

        $requestLines  = $this->validatedLineRequests[$shipment->id];
        $shipmentLines = $shipment->getLines();

        $token  = '';
        $markAsConfirmed = TRUE;
        if((!$this->shopConfiguration->visitorLoggedIn()) && ($this->getConfirmMailTextModule()->id > 0)) {
            return FALSE;
            $token  = md5(uniqid(mt_rand(), TRUE));
            $markAsConfirmed = FALSE;
        }
        /* OLD LOGIC - ERROR IF NOT LOGGED IN AND NO CONFIRMATION MAIL TEXT MODULE PRESENT
        if (!$this->shopConfiguration->visitorLoggedIn() && ($this->getConfirmMailTextModule()->id > 0)) {
            $token  = md5(uniqid(mt_rand(), TRUE));
            $markAsConfirmed = FALSE;
        } elseif (!$this->shopConfiguration->visitorLoggedIn() && !($this->getConfirmMailTextModule()->id > 0)) {
            $this->errors[] = $this->templateEngine->getText('internal_error') ;
            return FALSE;
        }
        */
        $lineRepository = $this->shipmentRepository->getLineRepository();

        $UoW = new UnitOfWork($this->db);
        $UoW->registerRepository($lineRepository);
        $UoW->registerRepository($this->shipmentRepository);

        foreach ($requestLines as $lineRequest) {
            $lineID           = $lineRequest['line_id'];
            $returnQuantity   = $lineRequest['return_quantity'];
            $returnReasonCode = $lineRequest['return_reason_code'];
            foreach ($shipmentLines as $shipmentLine) {
                if (($shipmentLine->id === $lineID) && ($shipmentLine instanceof RetShipmentLine)) {
                    if (!$shipmentLine->setReturnOrder($markAsConfirmed, $returnQuantity, $returnReasonCode)) {
                        $this->errors[] = $this->templateEngine->getText('internal_error') ;
                        $this->_unsetReturnOrder($shipment);
                        return FALSE;
                    }
                    $UoW->registerDirty($shipmentLine);
                    //if (!$lineRepository->updateSingle($shipmentLine)) {
                    //    $this->errors[] = $this->templateEngine->getText('internal_error') ;
                    //    $this->_unsetReturnOrder($shipment);
                    //    return FALSE;
                    //}
                }
            }
        }
        if(!$this->shipmentRepository->setReturnOrder($shipment, $markAsConfirmed, $this->shopConfiguration->getShopLanguage(), $yourReference, $token)) {
            $UoW->rollback();
            $UoW->clear();
            return FALSE;
        }
        $UoW->registerDirty($shipment);
        $UoW->commit();

        //if (!$this->shipmentRepository->setReturnOrder($shipment, $markAsConfirmed, $this->shopConfiguration->getShopLanguage(), $yourReference, $token)) {
        //    $this->errors[] = $this->templateEngine->getText('internal_error') ;
        //    $this->_unsetReturnOrder($shipment);
        //    return FALSE;
        //}
        if($token !== '') {
            $this->saveStatusArr[$shipment->id] = self::SAVE_STATUS_WITH_TOKEN;
        } else {
            $this->saveStatusArr[$shipment->id] = self::SAVE_STATUS_WITHOUT_TOKEN;
        }
        return TRUE;
    }

    /**
     * @param RetShipmentDocument $shipment
     * @param GenericViewInterface $view
     * @param TextModule $textModule
     * @param $userEMail
     * @return bool
     */
    public function sendConfirmationRequestMail( RetShipmentDocument $shipment, GenericViewInterface $view, TextModule $textModule, $userEMail ) {

        $shop = $this->shopConfiguration->getShop();

        if(empty($textModule->description)) {
            return FALSE;
        }
        if(!filter_var($userEMail, FILTER_VALIDATE_EMAIL)) {
            return FALSE;
        }
        if(!filter_var($shop->email_sender, FILTER_VALIDATE_EMAIL)) {
            return FALSE;
        }
        $subject      = $textModule->description;
        $message      = $view->getContent();
        $from         = $shop->email_sender;
        $to           = $userEMail;
        $from_name    = '';
        $to_name      = '';
        $html_mail    = TRUE;
        $attachment   = $textModule->attachment_1;
        $bcc          = '';
        $attachment_2 = $textModule->attachment_2;

        //Mail-Create in DB
        $dbMailID = mail_create($subject, $message, $from, $to, $from_name, $to_name, $html_mail, $attachment, $bcc, $attachment_2, 0, '');
        if (!($dbMailID > 0)) {
            $this->errors[] = $this->templateEngine->getText('internal_error') ;
            $this->_unsetReturnOrder($shipment);
            return FALSE;
        }

        //Mail sending

        $rootDir = rtrim(dirname(dirname(dirname(dirname(__DIR__)))),'/\\');
        require_once $rootDir. DIRECTORY_SEPARATOR . 'plugins/swiftmailer/lib/swift_required.php';
        // Create the Transport
        //@TODO: Change in Production!!
        $transport = \Swift_SmtpTransport::newInstance('10.0.0.12', 25); //Swift_MailTransport::newInstance();//

        // Create the Mailer using your created Transport
        $mailer = \Swift_Mailer::newInstance($transport);

        //Create Mail itself
        $mail = \Swift_Message::newInstance();
        $mail->setContentType('text/html');
        $mail->setSubject($subject);
        $mail->setFrom($from);
        $mail->setTo($to);
        $mail->setBody($message);
        if (filter_var($bcc, FILTER_VALIDATE_EMAIL)) {
            $mail->setBCC($bcc);
        }

        //Set attachments if exist
        $realAttachment1 = realpath('../..') . DIRECTORY_SEPARATOR . $attachment;
        if (is_file($realAttachment1) && is_readable($realAttachment1)) {
            $mail->attach(\Swift_Attachment::fromPath($realAttachment1));
        }

        $realAttachment2 = realpath('../..') . DIRECTORY_SEPARATOR . $attachment_2;
        if (is_file($realAttachment2) && is_readable($realAttachment2)) {
            $mail->attach(\Swift_Attachment::fromPath($realAttachment2));
        }

        //Send
        $sendResult = $mailer->send($mail);
        if (!$sendResult) {
            $this->errors[] = $this->templateEngine->getText('internal_error') ;
            $this->_unsetReturnOrder($shipment);
            return FALSE;
        }

        //Set mail as sent
        setMailSent($dbMailID);
        return TRUE;
    }


    /**
     * @param $shipmentHeaderID
     * @param $token
     *
     * @return bool
     */
    public function attemptTokenResolution( $shipmentHeaderID, $token ) {
        $shipmentID = filter_var($shipmentHeaderID, FILTER_SANITIZE_NUMBER_INT);
        $userToken  = filter_var($token, FILTER_SANITIZE_STRING);
        if (!($shipmentID > 0)) {
            $this->errors[] = $this->templateEngine->getText('no_shipment_id');
            return FALSE;
        }
        if (!(strlen($userToken) > 0)) {
            $this->errors[] = $this->templateEngine->getText('no_user_token');
            return FALSE;
        }
        $shipment = $this->shipmentRepository->findByID($shipmentID);
        if (!($shipment->id > 0)) {
            $this->errors[] = $this->templateEngine->getText('id_has_no_shipment');
            return FALSE;
        }
        if(!(strlen($shipment->return_order_shop_no) > 0) || !(strlen($shipment->return_order_token) > 0)) {
            $this->errors[] = $this->templateEngine->getText('shipment_is_no_return_order');
            return FALSE;
        }
        if (trim($userToken) != trim($shipment->return_order_token)) {
            $this->errors[] = $this->templateEngine->getText('token_mismatch');
            return FALSE;
        }

        $shopLanguage = $this->shopConfiguration->getShopLanguage();
        foreach($shipment as $line) {
            $line->setReturnOrderInsert(TRUE);
        }
        $shipment->setReturnOrderInsert(TRUE);

        if(!$this->shipmentRepository->updateSingle($shipment)) {
            $this->errors[] = $this->templateEngine->getText('internal_error') ;
            return FALSE;
        }
        return TRUE;
    }

    /**
     * @return RetShipmentRepository
     */
    public function getShipmentRepository() {
        return $this->shipmentRepository;
    }

    /**
     * @return ReturnReasonRepository
     */
    public function getReasonRepository() {
        return $this->reasonRepository;
    }

    /**
     * @return CurrShopConfiguration
     */
    public function getShopConfiguration() {
        return $this->shopConfiguration;
    }

    /**
     * @return TemplatingInterface
     */
    public function getTemplateEngine() {
        return $this->templateEngine;
    }

    /**
     * @return TextModuleRepository
     */
    public function getTextModuleRepository() {
        return $this->textModuleRepository;
    }

    /**
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * @return mixed
     */
    public function getConfirmMailTextModule() {
        $altPrimary = array(
            'company' => $this->shopConfiguration->getCompany(),
            'code' => $this->shopConfiguration->getShopLanguage()->email_rma_confirm_text_module
        );
        $textModule = $this->textModuleRepository->findByAltPrimary($altPrimary);
        return $textModule;
    }

    /**
     * @return mixed
     */
    public function getRetOrderCompleteTextModule() {
        $altPrimary = array(
            'company' => $this->shopConfiguration->getCompany(),
            'code' => $this->shopConfiguration->getShopLanguage()->return_order_complete_text_module
        );
        $textModule = $this->textModuleRepository->findByAltPrimary($altPrimary);
        return $textModule;
    }

}