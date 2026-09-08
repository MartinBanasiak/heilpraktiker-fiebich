<?php
namespace DynCom\dc\dcShop\Document;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\traits\universallyGettableTrait;
use DynCom\dc\dcShop\interfaces\GenericDocLineInterface;
use DynCom\dc\dcShop\traits\genericDocLineTrait;
use DynCom\dc\dcShop\classes\WebshopItemConfig;

/**
 * Class InvoiceLine
 */
class InvoiceLine implements GenericDocLineInterface, PrintableDocumentLineInterface {

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
    protected $variant_code;
    protected $to_delete;

    protected $typeText;
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

    public function getLineItemNo()
    {
        return $this->no;
    }
    /**
     * @return mixed
     */
    public function getUnitPrice()
    {
        return ($this->unit_price > 0 ? number_format($this->unit_price, 2, ',', '.'). ' €' : '');
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity > 0 ? round($this->quantity) : '';
    }

    /**
     * @return mixed
     */
    public function getLineAmount()
    {
        return ($this->line_amount > 0 ? number_format($this->line_amount, 2, ',', '.'). ' €' : '');
    }


    public function setTypeText($typeText)
    {
        $this->typeText = $typeText;
    }

}