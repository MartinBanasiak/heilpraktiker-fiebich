<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Class InvoiceDiscount
 */
class InvoiceDiscount implements GenericDBModelInterface, Entity
{

    use genericDBModelTrait, universallyGettableTrait;

    protected $id;
    protected $company;
    protected $invoice_discount_code;
    protected $currency_code;
    protected $minimum_amount;
    protected $discount;
    protected $service_charge;
    protected $to_delete;

    /**
     * InvoiceDiscount constructor.
     * @param InvoiceDiscountConfig $config
     */
    public function __construct( InvoiceDiscountConfig $config ) {
        $this->config = $config;
    }

    /**
     * @return InvoiceDiscount
     */
    public function getNullObject() {
        return new self($this->config);
    }

}