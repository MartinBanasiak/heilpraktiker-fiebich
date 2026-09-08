<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\traits\universallyGettableTrait;
use DynCom\dc\dcShop\interfaces\GenericDocLineInterface;
use DynCom\dc\dcShop\traits\genericDocLineTrait;

/**
 * Class InvoiceLine
 */
class InvoiceLine implements GenericDocLineInterface {

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
    protected $unit_price;
    protected $line_amount;
    protected $to_delete;

    /**
     * @param InvoiceLineConfig $config
     * @param InvoiceConfig $docConfig
     * @param WebshopItemConfig $itemConfig
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( InvoiceLineConfig $config, InvoiceConfig $docConfig, WebshopItemConfig $itemConfig, CriteriaHelperInterface $criteriaValidationService ) {
        $this->config                    = $config;
        $this->docConfig                 = $docConfig;
        $this->itemConfig                = $itemConfig;
        $this->criteriaValidationService = $criteriaValidationService;
    }

    /**
     * @return InvoiceLine
     */
    public function getNullObject() {
        return new InvoiceLine($this->config,$this->docConfig,$this->itemConfig,$this->criteriaValidationService);
    }

}