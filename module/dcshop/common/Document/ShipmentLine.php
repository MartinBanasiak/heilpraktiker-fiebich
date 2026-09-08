<?php
namespace DynCom\dc\dcShop\Document;

use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\traits\universallyGettableTrait;
use DynCom\dc\dcShop\interfaces\GenericDocLineInterface;
use DynCom\dc\dcShop\traits\genericDocLineTrait;
use DynCom\dc\dcShop\classes\WebshopItemConfig;

/**
 * Class ShipmentLine
 */
class ShipmentLine implements GenericDocLineInterface, PrintableDocumentLineInterface
{

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
    protected $variant_code;
    protected $to_delete;

    /**
     * @param ShipmentLineConfig $config
     * @param ShipmentConfig $docConfig
     * @param WebshopItemConfig $itemConfig
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct(ShipmentLineConfig $config, ShipmentConfig $docConfig, WebshopItemConfig $itemConfig, CriteriaHelperInterface $criteriaValidationService)
    {
        $this->config = $config;
        $this->docConfig = $docConfig;
        $this->itemConfig = $itemConfig;
        $this->criteriaValidationService = $criteriaValidationService;
    }

    /**
     * @return ShipmentLine
     */
    public function getNullObject()
    {
        return new ShipmentLine($this->config, $this->docConfig, $this->itemConfig, $this->criteriaValidationService);
    }

    public function getLineItemNo()
    {
        return $this->no;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity > 0 ? round($this->quantity) : '';
    }

    public function setTypeText($typeText)
    {
        $this->typeText = $typeText;
    }

}