<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\traits\universallyGettableTrait;
use DynCom\dc\dcShop\traits\genericDocLineTrait;

/**
 * Class NavOrderLine
 */
class NavOrderLine {

    use genericDocLineTrait, universallyGettableTrait;

    protected $id;
    protected $company;
    protected $document_type;
    protected $document_no;
    protected $line_no;
    protected $type;
    protected $no;
    protected $description;
    protected $description_2;
    protected $unit_of_measure;
    protected $quantity;
    protected $unit_price;
    protected $line_amount;
    protected $return_receipt_no;
    protected $return_reason_code;
    protected $to_delete;

    /**
     * @param NavOrderLineConfig $config
     * @param NavOrderConfig $docConfig
     * @param WebshopItemConfig $itemConfig
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( NavOrderLineConfig $config, NavOrderConfig $docConfig, WebshopItemConfig $itemConfig, CriteriaHelperInterface $criteriaValidationService ) {
        $this->config                    = $config;
        $this->docConfig                 = $docConfig;
        $this->itemConfig                = $itemConfig;
        $this->criteriaValidationService = $criteriaValidationService;
    }

    /**
     * @return NavOrderLine
     */
    public function getNullObject() {
        return new NavOrderLine($this->config,$this->docConfig,$this->itemConfig,$this->criteriaValidationService);
    }
}

