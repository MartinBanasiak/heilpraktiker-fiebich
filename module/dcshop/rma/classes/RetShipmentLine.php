<?php
namespace DynCom\dc\dcShop\rma\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\traits\universallyGettableTrait;
use DynCom\dc\dcShop\classes\WebshopItemConfig;
use DynCom\dc\dcShop\interfaces\GenericDocLineInterface;
use DynCom\dc\dcShop\traits\genericDocLineTrait;

/**
 * Class RetShipmentLine
 */
class RetShipmentLine implements GenericDocLineInterface {

    use genericDocLineTrait, universallyGettableTrait;

    const ITEM_TYPE = 2;

    protected $id;
    protected $company;
    protected $document_no;
    protected $line_no;
    protected $type;
    protected $no;
    protected $description;
    protected $description_2;
    protected $unit_of_measure;
    protected $quantity;
    protected $quantity_returned;
    protected $return_order_insert;
    protected $return_order;
    protected $return_quantity = 0;
    protected $return_reason_code;
    protected $to_delete;

    protected $returnable_quantity = 0;

    /**
     * RetShipmentLine constructor.
     * @param RetShipmentLineConfig $config
     * @param RetShipmentConfig $docConfig
     * @param WebshopItemConfig $itemConfig
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( RetShipmentLineConfig $config, RetShipmentConfig $docConfig, WebshopItemConfig $itemConfig, CriteriaHelperInterface $criteriaValidationService ) {
        $this->config                    = $config;
        $this->docConfig                 = $docConfig;
        $this->itemConfig                = $itemConfig;
        $this->criteriaValidationService = $criteriaValidationService;
    }

    /**
     * @param array $arr
     *
     * @return int
     */
    public function mapFromArray( array $arr ) {
        $noOfSuccessfulMappings =  $this->_mapArray($arr);
        if ($this->return_quantity > 0 || $this->quantity_returned > 0) {
            $this->returnable_quantity = $this->quantity - $this->return_quantity - $this->quantity_returned;
        } else {
            $this->return_quantity     = 0;
            $this->returnable_quantity = $this->quantity;
        }
        return $noOfSuccessfulMappings;
    }


    /**
     * @return bool|null
     */
    public function unsetReturnOrder() {
        return $this->_unsetReturnOrder();
    }

    /**
     * @return bool|null
     */
    protected function _unsetReturnOrder() {
        if (
            $this->return_order_insert == TRUE
            ||
            $this->return_order == 1
            ||
            $this->return_quantity > 0
            ||
            !empty($this->return_reason_code)
        ) {
            $this->return_order_insert = 0;
            $this->return_order        = 0;
            $this->return_quantity     = 0;
            $this->return_reason_code  = '';
        }
    }

    /**
     * @param $insert
     * @param $quantity
     * @param $reasonCode
     *
     * @return bool
     */
    protected function _setReturnOrder( $insert, $quantity, $reasonCode ) {
        if(!$this->checkReturnQuantityPossible($quantity)) {
            return FALSE;
        }
        $this->return_order_insert = (bool)$insert;
        $this->return_order        = 1;
        $this->return_quantity     = (float)$quantity;
        $this->return_reason_code  = (string)$reasonCode;
        return TRUE;
    }

    /**
     * @param $insert
     * @param $quantity
     * @param $reasonCode
     *
     * @return bool
     */
    public function setReturnOrder( $insert, $quantity, $reasonCode ) {
        return $this->_setReturnOrder($insert, $quantity, $reasonCode);
    }

    /**
     * @return RetShipmentLine
     */
    public function getNullObject() {
        return new RetShipmentLine($this->config,$this->docConfig,$this->itemConfig,$this->criteriaValidationService);
    }

    /**
     * @param $quantity
     *
     * @return bool
     */
    public function checkReturnQuantityPossible( $quantity ) {
        $qty = (float) $quantity;
        $isPossible = !(($qty > $this->returnable_quantity) || ($this->return_order_insert));
        return $isPossible;
    }


    /**
     * @param bool $insert
     */
    public function setReturnOrderInsert( $insert ) {
        $this->return_order_insert = (bool) $insert;
    }

}