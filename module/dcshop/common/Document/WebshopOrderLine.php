<?php
namespace DynCom\dc\dcShop\Document;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\traits\universallyGettableTrait;
use DynCom\dc\dcShop\interfaces\GenericDocLineInterface;
use DynCom\dc\dcShop\traits\genericDocLineTrait;
use DynCom\dc\dcShop\classes\WebshopItemConfig;

/**
 * Class WebshopOrderLine
 */
class WebshopOrderLine implements GenericDocLineInterface, PrintableDocumentLineInterface
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
    protected $greeting_card_text;
    protected $package_for_item;
    protected $package_for_variant_code;
    protected $customized;
    protected $allow_invoice_disc;
    protected $is_coupon_item;
    protected $salesperson_discount;
    protected $marketplace_line_id;
    protected $update_insert;
    protected $to_delete;

    protected $webshopItemImagePath;

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

    public function getLineItemNo()
    {
        return  $this->item_no;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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

    public function setWebshopItemImagePath($imagePath)
    {
        $this->webshopItemImagePath = $imagePath;
    }

    public function getWebshopItemImagePath()
    {
        return $this->webshopItemImagePath;
    }

}