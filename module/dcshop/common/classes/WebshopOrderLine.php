<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\traits\universallyGettableTrait;
use DynCom\dc\dcShop\interfaces\GenericDocLineInterface;
use DynCom\dc\dcShop\traits\genericDocLineTrait;

/**
 * Class WebshopOrderLine
 */
class WebshopOrderLine implements GenericDocLineInterface
{

    use genericDocLineTrait, universallyGettableTrait;

    protected $id;
    protected $shop_sales_header_id;
    protected $shop_item_id;
    protected $company;
    protected $shop_code;
    protected $language_code;
    protected $order_no;
    protected $item_no;
    protected $variant_code;
    protected $description;
    protected $summary;
    protected $list_price;
    protected $unit_price;
    protected $quantity;
    protected $line_amount;
    protected $allow_invoice_disc;
    protected $is_coupon_item;
    protected $salesperson_discount;
    protected $update_insert;
    protected $to_delete;

    /**
     * @param WebshopOrderLineConfig $config
     * @param WebshopOrderConfig $docConfig
     * @param WebshopItemConfig $itemConfig
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( WebshopOrderLineConfig $config, WebshopOrderConfig $docConfig, WebshopItemConfig $itemConfig, CriteriaHelperInterface $criteriaValidationService ) {
        $this->config                    = $config;
        $this->docConfig                 = $docConfig;
        $this->itemConfig                = $itemConfig;
        $this->criteriaValidationService = $criteriaValidationService;
    }

    /**
     * @return WebshopOrderLine
     */
    public function getNullObject() {
        return new WebshopOrderLine($this->config,$this->docConfig,$this->itemConfig,$this->criteriaValidationService);
    }

}