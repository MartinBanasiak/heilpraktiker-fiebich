<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Class CustomerTemplate
 */
class CustomerTemplate implements GenericDBModelInterface, Entity {

    use genericDBModelTrait, universallyGettableTrait;

    protected $id;
    protected $company;
	protected $code;
	protected $description;
	protected $territory_code;
	protected $global_dimension_1_code;
	protected $global_dimension_2_code;
	protected $customer_posting_group;
	protected $currency_code;
	protected $customer_price_group;
	protected $payment_terms_code;
	protected $shipment_method_code;
	protected $invoice_disc_code;
	protected $customer_disc_group;
	protected $country_region_code;
	protected $payment_method_code;
	protected $prices_including_vat;
	protected $gen_bus_posting_group;
	protected $vat_bus_posting_group;
	protected $contact_type;
	protected $allow_line_disc;
	protected $to_delete;

    /**
     * @param CustomerTemplateConfig $config
     */
    public function __construct( CustomerTemplateConfig $config ) {
        $this->config = $config;
    }

    /**
     * @return CustomerTemplate
     */
    public function getNullObject() {
        return new self($this->config);
    }

}