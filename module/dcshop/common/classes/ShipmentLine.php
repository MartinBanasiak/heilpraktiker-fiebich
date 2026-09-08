<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\traits\universallyGettableTrait;
use DynCom\dc\dcShop\interfaces\GenericDocLineInterface;
use DynCom\dc\dcShop\traits\genericDocLineTrait;

/**
 * Class ShipmentLine
 */
class ShipmentLine implements GenericDocLineInterface {

    use genericDocLineTrait, universallyGettableTrait;

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
    protected $return_quantity;
    protected $return_reason_code;
    protected $to_delete;

    /**
     * @param ShipmentLineConfig $config
     * @param ShipmentConfig $docConfig
     * @param WebshopItemConfig $itemConfig
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( ShipmentLineConfig $config, ShipmentConfig $docConfig, WebshopItemConfig $itemConfig, CriteriaHelperInterface $criteriaValidationService ) {
        $this->config                    = $config;
        $this->docConfig                 = $docConfig;
        $this->itemConfig                = $itemConfig;
        $this->criteriaValidationService = $criteriaValidationService;
    }

    /**
     * @return ShipmentLine
     */
    public function getNullObject() {
        return new ShipmentLine($this->config,$this->docConfig,$this->itemConfig,$this->criteriaValidationService);
    }

}