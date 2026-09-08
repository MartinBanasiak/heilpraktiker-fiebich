<?php
namespace DynCom\dc\dcShop\classes;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 26.01.2015
 * Time: 16:03
 */
use DynCom\dc\common\classes\URL;
use DynCom\dc\common\classes\Visitor;
use DynCom\dc\dcShop\interfaces\CustomerInterface;

/**
 * Class CurrShopConfiguration
 */
class CurrShopConfiguration {

    /**
     * @var Shop
     */
    protected $shop;
    /**
     * @var ShopLanguage
     */
    protected $shopLanguage;
    /**
     * @var Visitor
     */
    protected $visitor;
    /**
     * @var User
     */
    protected $user;
    /**
     * @var Customer
     */
    protected $customer;
    /**
     * @var string
     */
    protected $baseShopUrl;
    /**
     * @var ShopSetup
     */
    private $shopSetup;

    /**
     * @param ShopSetup $shopSetup
     * @param Shop $shop
     * @param ShopLanguage $shopLanguage
     * @param Visitor $visitor
     * @param User $user
     * @param CustomerInterface $customer
     * @param URL $baseURL
     */
    public function __construct(ShopSetup $shopSetup, Shop $shop, ShopLanguage $shopLanguage, Visitor $visitor, User $user, CustomerInterface $customer, URL $baseURL) {
        $this->shop         = $shop;
        $this->shopLanguage = $shopLanguage;
        $this->visitor      = $visitor;
        $this->user         = $user;
        $this->customer     = $customer;
        $this->baseShopUrl  = $baseURL;
        $this->shopSetup = $shopSetup;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        $shopSet = isset($this->shop);
        $shopID = 0;
        if($shopSet) {
            $shopID = $this->shop->getID();
        }
        $isSet = ($shopSet && ($shopID > 0));
        return $isSet;
    }

    /**
     * @return Shop
     */
    public function getShop() {
        return $this->shop;
    }

    /**
     * @return ShopLanguage
     */
    public function getShopLanguage() {
        return $this->shopLanguage;
    }

    /**
     * @return Visitor
     */
    public function getVisitor() {
        return $this->visitor;
    }

    /**
     * @return int|null
     */
    public function getVisitorID() {
        return $this->visitor->getID();
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @return int|null
     */
    public function getUserID() {
        return $this->user->getID();
    }

    /**
     * @return Customer
     */
    public function getCustomer() {
        return $this->customer;
    }

    /**
     * @return bool
     */
    public function visitorLoggedIn() {
        return (bool)$this->visitor->frontend_login;
    }

    /**
     * @return bool
     */
    public function isUserSet() {
        return ($this->user->id > 0);
    }

    /**
     * @return bool
     */
    public function isCustomerSet() {
        return ($this->customer->id > 0);
    }

    /**
     * @return string
     */
    public function getCustomerNo() {
        return ($this->customer->customer_no ?: '');
    }

    /**
     * @return array
     */
    public function getShopLangPrimaryArr() {
        if(!isset($this->shopLanguage->company) || !isset($this->shopLanguage->shop_code) || !isset($this->shopLanguage->code)) {
            return [];
        }
        $primaryArr = [];
        $primaryArr[] = $this->shopLanguage->company;
        $primaryArr[] = $this->shopLanguage->shop_code;
        $primaryArr[] = $this->shopLanguage->code;
        return $primaryArr;
    }

    public function getCompany() {
        return $this->shop->company;
    }

    public function getShopCode() {
        return $this->shop->code;
    }

    public function getShopLanguageCode() {
        return $this->shopLanguage->code;
    }

    public function getShopType() {
        return $this->shop->shop_typ;
    }

    /**
     * @return URL
     */
    public function getBaseShopUrl() {
        return $this->baseShopUrl;
    }

    /**
     * @return mixed
     */
    public function getCurrencyCode() {
        $custCurrCode = $this->customer->currency_code;
        $langCurrCode = empty($this->shopLanguage->default_currency_code) ? '' : $this->shopLanguage->default_currency_code;
        return (empty($custCurrCode)) ? $langCurrCode : $custCurrCode;
    }

    /**
     * @return int
     */
    public function getVariantType() {
        return (int)$this->shop->getVariantType();
    }

    /**
     * @return bool
     */
    public function isShopB2B() {
        return $this->shop->isB2B();
    }

    /**
     * @return bool
     */
    public function isShopB2C() {
        return $this->shop->isB2c();
    }

    /**
     * @return bool
     */
    public function isShopSalespersonShop() {
        return $this->shop->isSalespersonShop();
    }

    /**
     * @return bool
     */
    public function isShopCatalog() {
        return $this->shop->isCatalog();
    }

    /**
     * @return bool
     */
    public function isUserLoggedIn() {
        return (bool)$this->visitor->frontend_login;
    }

    public function getUseItemsFromShopCode() {
        return $this->shop->use_items_from_shop_code ?? $this->shop->code;
    }

    public function getUseCategoriesFromShopCode() {
        return $this->shop->use_categorys_from_shop_code ?? $this->shop->code;
    }

    /**
     * @return mixed|null
     */
    public function getUseCustomersFromShopCode() {
        return $this->shop->use_customers_from_shop_code;
    }

    /**
     * @return int
     */
    public function getNoOfItemsPerPage() {
        //@TODO: Config zu Objekt parsen und in Shop Übernehmen
        return (isset($GLOBALS['shop_setup']['num_items_per_page'])) ? (int)$GLOBALS['shop_setup']['num_items_per_page'] : 0;
    }

    public function getShopVATBusPostingGroup() {
        return $this->shop->vat_bus_posting_group;
    }

    /**
     * @return mixed
     */
    public function getUserCompany() {
        return $this->user->getCompany();
    }


    /**
     * @return bool
     */
    public function isVariantTypeDummyParent()
	{
		//0
		return $this->getVariantType() === Shop::VARIANT_TYPE_DUMMY_PARENT;
	}

    /**
     * @return bool
     */
    public function isVariantTypeOrderableParent()
	{
		//1
		return $this->getVariantType() === Shop::VARIANT_TYPE_ORDERABLE_PARENT;
	}

    /**
     * @return bool
     */
    public function isVariantTypeNavVariants()
	{
		//2
		return $this->getVariantType() === Shop::VARIANT_TYPE_NAV_VARIANTS;
	}

    /**
     * @return integer
     */
    public function getOrderOptionsDisplay()
    {
        return $this->shop->getOrderOptionsDisplay();
    }

    /**
     * @return integer
     */
    public function getOrderOptionsSorting()
    {
        return $this->shop->getOrderOptionsSorting();
    }

    /**
     * @return string
     */
    public function getVatIdentifier1()
    {
        return $this->shop->getVatIdentifier1();
    }

    /**
     * @return string
     */
    public function getVatIdentifier2()
    {
        return $this->shop->getVatIdentifier2();
    }

    /**
     * @return string
     */
    public function getVatIdentifier3()
    {
        return $this->shop->getVatIdentifier3();
    }

    public function getDefaultShippingClassPriority()
    {
        return $this->shopSetup->default_shipping_class_priority;
    }

}