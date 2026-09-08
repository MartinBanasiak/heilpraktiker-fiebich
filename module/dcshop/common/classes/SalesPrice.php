<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 24.03.2015
 * Time: 10:19
 */

class SalesPrice implements GenericDBModelInterface, Entity {

    use genericDBModelTrait, universallyGettableTrait;

    protected $id;
    protected $company;
    protected $type;
    protected $item_no;
    protected $variant_code;
    protected $currency_code;
    protected $discount_group;
    protected $sales_type;
    protected $sales_code;
    protected $unit_price;
    protected $line_discount;
    protected $minimum_quantity;
    protected $unit_of_measure_code;
    protected $allow_line_disc;
    protected $allow_invoice_disc;
    protected $starting_date;
    protected $ending_date;
    protected $price_includes_vat;
    protected $to_delete;

    /**
     * SalesPrice constructor.
     * @param SalesPriceConfig $config
     */
    public function __construct( SalesPriceConfig $config ) {
        $this->config = $config;
    }

    /**
     * @return SalesPrice
     */
    public function getNullObject() {
        return new self($this->config);
    }

}