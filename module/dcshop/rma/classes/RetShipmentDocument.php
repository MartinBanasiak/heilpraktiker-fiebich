<?php
namespace DynCom\dc\dcShop\rma\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\traits\universallyGettableTrait;
use DynCom\dc\dcShop\classes\ShopLanguage;
use DynCom\dc\dcShop\interfaces\GenericDocumentInterface;
use DynCom\dc\dcShop\traits\documentTrait;

/**
 * Class RetShipmentDocument
 */
class RetShipmentDocument implements GenericDocumentInterface {

    use documentTrait, universallyGettableTrait;

    protected $id;
    protected $company;
    protected $no;
    protected $posting_date;
    protected $order_no;
    protected $webshop_order_no;
    protected $shop_code;
    protected $language_code;
    protected $user_email;
    protected $user_id;
    protected $customer_id;
    protected $your_reference;
    protected $sell_to_customer_no;
    protected $bill_to_customer_no;
    protected $sell_to_name;
    protected $sell_to_name_2;
    protected $sell_to_address;
    protected $sell_to_address_2;
    protected $sell_to_post_code;
    protected $sell_to_city;
    protected $sell_to_country;
    protected $sell_to_contact;
    protected $bill_to_name;
    protected $bill_to_name_2;
    protected $bill_to_address;
    protected $bill_to_address_2;
    protected $bill_to_post_code;
    protected $bill_to_city;
    protected $bill_to_country;
    protected $bill_to_contact;
    protected $ship_to_name;
    protected $ship_to_name_2;
    protected $ship_to_address;
    protected $ship_to_address_2;
    protected $ship_to_post_code;
    protected $ship_to_city;
    protected $ship_to_country;
    protected $ship_to_contact;
    protected $shipment_method;
    protected $request_mail;
    protected $request_shop_code;
    protected $request_language_code;
    protected $send_request;
    protected $return_order_insert;
    protected $return_order;
    protected $return_shop_code;
    protected $return_language_code;
    protected $return_order_reference;
    protected $return_order_shop_no;
    protected $return_order_token;
    protected $invoice_no;
    protected $nav_order_no;

    /**
     * @param RetShipmentConfig $config
     * @param RetShipmentLineConfig $linesConfig
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( RetShipmentConfig $config, RetShipmentLineConfig $linesConfig, CriteriaHelperInterface $criteriaValidationService ) {
        $this->config = $config;
        $this->linesConfig = $linesConfig;
        $this->criteriaValidationService = $criteriaValidationService;
    }

    /**
     * @return RetShipmentDocument
     */
    public function getNullObject() {
        return new RetShipmentDocument($this->config,$this->linesConfig,$this->criteriaValidationService);
    }

    /**
     * @return bool|null
     */
    public function unsetReturnOrder() {
        $this->_unsetReturnOrder();
    }

    protected function _unsetReturnOrder() {
        if (
            $this->return_order_insert == TRUE
            ||
            $this->return_order == TRUE
            ||
            !empty($this->return_shop_code)
            ||
            !empty($this->return_language_code)
            ||
            !empty($this->return_order_reference)
            ||
            !empty($this->return_order_token)
            ||
            !empty($this->return_order_shop_no)
        ) {
            $this->return_order_insert    = 0;
            $this->return_order           = 0;
            $this->return_shop_code       = '';
            $this->return_language_code   = '';
            $this->return_order_reference = '';
            $this->return_order_token     = '';
            $this->return_order_shop_no   = 0;
        }
    }

    /**
     * @param $orderNo
     * @param $insert
     * @param ShopLanguage $shopLanguage
     * @param $reference
     * @param string $token
     * @return bool
     * @throws \Exception
     */
    protected function _setReturnOrder( $orderNo, $insert, ShopLanguage $shopLanguage, $reference, $token = '' ) {
        if (!(strlen($orderNo) > 0) || !($orderNo > 0)) {
            throw new \Exception('no orderNo set');
            return FALSE;
        }
        if(!$this->isReturnOrderSettable()) {
            throw new \Exception('Shipment has an unsynchronized return order and can only be set for another when synchronization is done');
            return FALSE;
        }
        $this->return_order_insert    = (bool)$insert;
        $this->return_order           = 1;
        $this->return_shop_code       = (string)$shopLanguage->shop_code;
        $this->return_language_code   = (string)$shopLanguage->code;
        $this->return_order_reference = (string)$reference;
        $this->return_order_token     = (string)$token;
        $this->return_order_shop_no   = (string)$orderNo;
        return TRUE;
    }

    /**
     * @param              $orderNo
     * @param              $insert
     * @param ShopLanguage $shopLanguage
     * @param              $reference
     * @param string       $token
     * @return bool
     */
    public function setReturnOrder( $orderNo, $insert, ShopLanguage $shopLanguage, $reference, $token = '' ) {
        return $this->_setReturnOrder($orderNo, $insert, $shopLanguage, $reference, $token);
    }

    /**
     * @return bool
     */
    public function isReturnOrderSettable() {
        return !((bool)$this->return_order_insert);
    }

    /**
     * @param bool $insert
     */
    public function setReturnOrderInsert( $insert ) {
        $this->return_order_insert = (bool) $insert;
    }


}