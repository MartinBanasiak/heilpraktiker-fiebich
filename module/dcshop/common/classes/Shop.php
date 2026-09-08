<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Class Shop
 */
class Shop implements GenericDBModelInterface, Entity
{

    use universallyGettableTrait,
        genericDBModelTrait
    {
        genericDBModelTrait::mapFromArrayAndConfig as parentMapFromArrayAndConfig;
    }
	
	const VARIANT_TYPE_DUMMY_PARENT = 0;
	const VARIANT_TYPE_ORDERABLE_PARENT = 1;
	const VARIANT_TYPE_NAV_VARIANTS = 2;


    protected $id;
    protected $company;
    protected $code;
    protected $description;
    protected $shop_typ;
    protected $login_type;
    protected $default_language_code;
    protected $use_items_from_shop_code;
    protected $use_categorys_from_shop_code;
    protected $variant_typ;
    protected $use_customer_from_shop_code;
    protected $email_sender;
    protected $email_order_mail_1_copy;
    protected $computop_merchant_id;
    protected $computop_password;
    protected $show_vendor_filter;
    protected $show_invoice_discount;
    protected $small_quantity_charge;
    protected $small_quantity_charge_limit;
    protected $online_discount;
    protected $retail_price_typ;
    protected $cust_price_group_retail_price;
    protected $cust_disc_group_retail_price;
    protected $base_price_typ;
    protected $cust_price_group_base_price;
    protected $cust_disc_group_base_price;
    protected $campain_no;
    protected $prices_including_vat;
    protected $vat_bus_posting_group;
    protected $select_req_delivery_date;
    protected $addition_req_delivery_date;
    protected $cross_price_typ;
    protected $show_filters;
    protected $show_comparison;
    protected $show_filter_on_card;
    protected $attribute_frige_shipping;
    protected $attribute_bio_food;
    protected $g_ftp_url;
    protected $g_ftp_user;
    protected $g_ftp_passwd;
    protected $g_channel_title;
    protected $g_channel_link;
    protected $g_channel_desc;
    protected $default_copernica_segment;
    protected $max_days_shipment_returnable;
    protected $to_delete;
    protected $attribute_brand;
    protected $extended_search;
    protected $forerun_subscr_order_create;
    protected $forerun_payment_capture;
    protected $item_source;
    protected $category_source;
    protected $customer_source;
    protected $order_options_display;
    protected $order_options_sorting;
    protected $inventory_display;
    protected $vat_identifier_1;
    protected $vat_identifier_2;
    protected $vat_identifier_3;
    protected $paydirekt_api_key;
    protected $copernica_db_id;
    protected $copernica_access_token;
    protected $item_availability;
    protected $us_sales_tax_enabled;
    protected $shipping_group_code;

    /**
     * @return mixed
     */
    public function getShippingGroupCode()
    {
        return $this->shipping_group_code;
    }

    /**
     * @return integer
     */
    public function getInventoryDisplay()
    {
        return $this->inventory_display;
    }

    /**
     * @param ShopConfig $config
     */
    public function __construct( ShopConfig $config ) {
        $this->config = $config;
    }

    /**
     * @return Shop
     */
    public function getNullObject() {
        return new self($this->config);
    }

    /**
     * @return bool
     */
    public function isB2B() {
        return ((int)$this->shop_typ === 0);
    }

    /**
     * @return bool
     */
    public function isB2C() {
        return ((int)$this->shop_typ === 1);
    }

    /**
     * @return bool
     */
    public function isSalespersonShop() {
        return ((int)$this->shop_typ === 2);
    }

    /**
     * @return bool
     */
    public function isCatalog() {
        return ((int)$this->shop_typ === 3);
    }

    /**
     * @param array $arr
     */
    public function mapFromArray(array $arr) {
        $this->parentMapFromArrayAndConfig($arr,$this->config);
        $this->item_source = (null === $this->use_items_from_shop_code || '' === $this->use_items_from_shop_code) ? $this->code : $this->use_items_from_shop_code;
        $this->category_source = (null === $this->use_categorys_from_shop_code || '' === $this->use_categorys_from_shop_code) ? $this->code : $this->use_categorys_from_shop_code;
        $this->customer_source = (null === $this->customer_source || '' === $this->use_customer_from_shop_code) ? $this->code : $this->customer_source;
    }

    /**
     * @return mixed
     */
    public function getUseItemsFromShopCode() {
        return $this->item_source ?? $this->code;
    }

    /**
     * @return mixed
     */
    public function getUseCategoriesFromShopCode() {
        return $this->category_source ?? $this->code;
    }

    /**
     * @return mixed
     */
    public function getUseCustomersFromShopCode() {
        return $this->customer_source ?? $this->code;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return bool
     */
    public function isVariantTypeDummyParent()
	{
		//0
		return (int)$this->variant_typ === self::VARIANT_TYPE_DUMMY_PARENT;
	}

    /**
     * @return bool
     */
    public function isVariantTypeOrderableParent()
	{
		//1
		return (int)$this->variant_typ === self::VARIANT_TYPE_ORDERABLE_PARENT;
	}

    /**
     * @return bool
     */
    public function isVariantTypeNavVariants()
	{
		//2
		return (int)$this->variant_typ === self::VARIANT_TYPE_NAV_VARIANTS;
	}

    /**
     * @return int
     */
    public function getVariantType()
	{
		return (int)$this->variant_typ;
	}
    /**
     * @return integer
     */
    public function getOrderOptionsDisplay()
    {
        return $this->order_options_display;
    }

    /**
     * @return integer
     */
    public function getOrderOptionsSorting()
    {
        return $this->order_options_sorting;
    }

    /**
     * @return string
     */
    public function getVatIdentifier1()
    {
        return $this->vat_identifier_1;
    }

    /**
     * @return string
     */
    public function getVatIdentifier2()
    {
        return $this->vat_identifier_2;
    }

    /**
     * @return string
     */
    public function getVatIdentifier3()
    {
        return $this->vat_identifier_3;
    }

    /**
     * @return mixed
     */
    public function getPaydirektApiKey()
    {
        return $this->paydirekt_api_key;
    }

    /**
     * @return mixed
     */
    public function getCopernicaDbId()
    {
        return $this->copernica_db_id;
    }

    /**
     * @return mixed
     */
    public function getCopernicaAccessToken()
    {
        return $this->copernica_access_token;
    }

    /**
     * @return mixed
     */
    public function getItemAvailability()
    {
        return $this->item_availability;
    }

    /**
     * @return bool
     */
    public function isUSSalesTaxEnabled()
    {
        return (bool)$this->us_sales_tax_enabled;
    }

}