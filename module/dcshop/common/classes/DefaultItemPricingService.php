<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\dcShop\interfaces\CustomerInterface;
use DynCom\dc\dcShop\interfaces\ItemPricingService;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;
use DynCom\dc\RuleEngine\ActiveActionItemRuleDiscountRepositoryInterface;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 18.10.2016
 * Time: 10:25
 */
class DefaultItemPricingService implements ItemPricingService
{

    /**
     * @var ShopRepository
     */
    private $shopRepository;
    /**
     * @var WebshopItemRepository
     */
    private $itemRepository;
    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    private $shopByKey = [];

    private $itemByKey = [];

    private $customerByKey = [];

    private $currencyCodeByShopLanguageCustomer = [];

    private $currencyCodeByShopLanguage = [];

    private $providerByPrimary = [];

    private $vatManagerByKey = [];

    /**
     * @var ShopLanguageRepository
     */
    private $shopLanguageRepository;
    /**
     * @var WebshopItemVariantRepository
     */
    private $variantRepository;
    /**
     * @var ActiveActionItemRuleDiscountRepositoryInterface
     */
    private $ruleDiscountRepository;
    /**
     * @var PDOQueryWrapper
     */
    private $pdo;


    /**
     * DefaultItemPricingService constructor.
     * @param ShopRepository $shopRepository
     * @param WebshopItemRepository $itemRepository
     * @param CustomerRepository $customerRepository
     * @param ShopLanguageRepository $shopLanguageRepository
     * @param WebshopItemVariantRepository $variantRepository
     * @param ActiveActionItemRuleDiscountRepositoryInterface $ruleDiscountRepository
     * @param PDOQueryWrapper $pdo
     */
    public function __construct(
        ShopRepository $shopRepository,
        WebshopItemRepository $itemRepository,
        CustomerRepository $customerRepository,
        ShopLanguageRepository $shopLanguageRepository,
        WebshopItemVariantRepository $variantRepository,
        ActiveActionItemRuleDiscountRepositoryInterface $ruleDiscountRepository,
        PDOQueryWrapper $pdo
    )
    {
        $this->shopRepository = $shopRepository;
        $this->itemRepository = $itemRepository;
        $this->customerRepository = $customerRepository;
        $this->shopLanguageRepository = $shopLanguageRepository;
        $this->variantRepository = $variantRepository;
        $this->ruleDiscountRepository = $ruleDiscountRepository;
        $this->pdo = $pdo;
    }

    /**
     * @param $company
     * @param $targetShopCode
     * @param $itemShopCode
     * @param $customerShopCode
     * @param $shopLanguageCode
     * @param $customerNo
     * @param $itemNo
     * @param $variantCode
     * @param $quantity
     * @return ItemPriceData
     */
    public function getItemCustomerPriceByPrimary( $company, $targetShopCode, $itemShopCode, $customerShopCode, $shopLanguageCode, $customerNo, $itemNo, $variantCode, $quantity )
    {

        /**
         * @var $provider AdvancedPriceProvider
         * @var $item WebshopItemInterface
         * @var $customer CustomerInterface
         */
        $provider = $this->getPriceProvider($company, $targetShopCode, $shopLanguageCode, $customerNo, $customerShopCode);
        $customer = $this->getCustomer($company,$shopLanguageCode,$customerShopCode,$customerNo);
        $currencyCode = $this->getCurrencyCode($company,$targetShopCode,$shopLanguageCode,$customerShopCode,$customerNo);
        $item = $this->getItem($company, $itemShopCode, $shopLanguageCode, $itemNo, $variantCode);

        $priceData = $provider->getItemCustomerPrice($item, $quantity, $customer, $currencyCode,false);
        return $priceData;

    }

    /**
     * @param $company
     * @param $targetShopCode
     * @param $itemShopCode
     * @param $customerShopCode
     * @param $shopLanguageCode
     * @param $customerNo
     * @param $itemNo
     * @param $variantCode
     * @return GraduatedItemPriceData
     */
    public function getItemGraduatedCustomerPricesByPrimary($company, $targetShopCode, $itemShopCode, $customerShopCode, $shopLanguageCode, $customerNo, $itemNo, $variantCode)
    {
        /**
         * @var $provider AdvancedPriceProvider
         * @var $item WebshopItemInterface
         * @var $customer CustomerInterface
         */
        $provider = $this->getPriceProvider($company, $targetShopCode, $shopLanguageCode, $customerNo, $customerShopCode);
        $customer = $this->getCustomer($company,$shopLanguageCode,$customerShopCode,$customerNo);
        $currencyCode = $this->getCurrencyCode($company,$targetShopCode,$shopLanguageCode,$customerShopCode,$customerNo);
        $item = $this->getItem($company, $itemShopCode, $shopLanguageCode, $itemNo, $variantCode);

        return $provider->getGraduatedPrices($item, $customer, $currencyCode);
    }

    /**
     * @param $company
     * @param $targetShopCode
     * @param $itemShopCode
     * @param $customerShopCode
     * @param $shopLanguageCode
     * @param $customerNo
     * @param $itemNo
     * @param $variantCode
     * @param $quantity
     * @return float|ItemPriceData
     */
    public function getItemDiscountedCustomerPriceByPrimary($company, $targetShopCode, $itemShopCode, $customerShopCode, $shopLanguageCode, $customerNo, $itemNo, $variantCode, $quantity)
    {
        /**
         * @var $provider AdvancedPriceProvider
         * @var $item WebshopItemInterface
         * @var $customer CustomerInterface
         */
        $provider = $this->getPriceProvider($company, $targetShopCode, $shopLanguageCode, $customerNo, $customerShopCode);
        $customer = $this->getCustomer($company,$shopLanguageCode,$customerShopCode,$customerNo);
        $currencyCode = $this->getCurrencyCode($company,$targetShopCode,$shopLanguageCode,$customerShopCode,$customerNo);
        $item = $this->getItem($company, $itemShopCode, $shopLanguageCode, $itemNo, $variantCode);

        return $provider->getItemCrossPrice($item,$customer,$currencyCode);
    }

    /**
     * @param $company
     * @param $targetShopCode
     * @param $languageCode
     * @param $customerShopCode
     * @param $customerNo
     * @return mixed|null
     */
    public function getCurrencyCode($company, $targetShopCode, $languageCode, $customerShopCode, $customerNo)
    {
        $delim = '|';
        $keyCustomer = $company . $delim . $targetShopCode . $delim . $languageCode . $delim . $customerShopCode . $delim . $customerNo;
        $keyLanguage = $company . $delim . $targetShopCode . $delim . $languageCode;
        if (array_key_exists($keyCustomer,$this->currencyCodeByShopLanguageCustomer)) {
            return $this->currencyCodeByShopLanguageCustomer[$keyCustomer];
        }


        $customerCurrencyCode = null;
        if ($customerNo !== '') {
            $customer = $this->getCustomer($company, $languageCode, $customerShopCode, $customerNo);
            $customerCurrencyCode = $customer->currency_code;
        }

        if (array_key_exists($keyLanguage,$this->currencyCodeByShopLanguage)) {
            $languageCurrencyCode = $this->currencyCodeByShopLanguage[$keyLanguage];
        } else {
            $shopLanguagePrimary = ['company' => $company, 'shop_code' => $targetShopCode, 'code' => $languageCode];
            $shopLanguage = $this->shopLanguageRepository->findByAltPrimary($shopLanguagePrimary);
            $languageCurrencyCode = $shopLanguage->default_currency_code;
            $this->currencyCodeByShopLanguage[$keyLanguage] = $languageCurrencyCode;
        }

        if ($customerCurrencyCode !== null) {
            $currencyCode = $customerCurrencyCode;
        } else {
            $currencyCode = $languageCurrencyCode;
        }
        $this->currencyCodeByShopLanguageCustomer[$keyCustomer] = $currencyCode;
        return $currencyCode;

    }

    /**
     * @param $company
     * @param $itemShopCode
     * @param $languageCode
     * @param $itemNo
     * @param $variantCode
     * @return WebshopItemInterface
     */
    private function getItem($company, $itemShopCode, $languageCode, $itemNo, $variantCode)
    {
        $delim = '|';
        $itemKey = $company . $delim . $itemShopCode . $delim . $languageCode . $delim . $itemNo . $delim . $variantCode;

        if (array_key_exists(
                $itemKey,
                $this->itemByKey
            ) && $this->itemByKey[$itemKey] instanceof WebshopItemInterface
        ) {
            $item = $this->itemByKey[$itemKey];
        } else {
            $itemPrimary = ['company' => $company, 'shop_code' => $itemShopCode, 'language_code' => $languageCode, 'item_no' => $itemNo];
            $item = $this->itemRepository->findByAltPrimary($itemPrimary);
            if (!empty($variantCode)) {
                $variantItemPrimary = ['company' => $company, 'item_no' => $itemNo, 'code' => $variantCode];
                $variant = $this->variantRepository->findByAltPrimary($variantItemPrimary);
                $item = new WebshopItemNAVVariantDecorator($item, $variant);
            }
        }
        return $item;
    }

    /**
     * @param $company
     * @param $targetShopCode
     * @param $languageCode
     * @param $customerNo
     * @param $customerShopCode
     * @return AdvancedPriceProvider $provider
     */
    public function getPriceProvider($company, $targetShopCode, $languageCode, $customerNo, $customerShopCode)
    {
        $delim = '|';
        $shopKey = $company . $delim . $targetShopCode;
        if (array_key_exists($shopKey, $this->shopByKey) && $this->shopByKey[$shopKey] instanceof Shop) {
            $shop = $this->shopByKey[$shopKey];
        } else {
            $shop = $this->shopRepository->findByAltPrimary(['company' => $company, 'code' => $targetShopCode]);
            $this->shopByKey[$shopKey] = $shop;
        }

        $customerKey = $company . $delim . $targetShopCode . $delim . $languageCode . $delim . $customerShopCode . $delim . $customerNo;
        if (array_key_exists(
                $customerKey,
                $this->customerByKey
            ) && $this->customerByKey[$customerKey] instanceof CustomerInterface
        ) {
            $customer = $this->customerByKey[$customerKey];
        } else {
            $customerPrimary = ['company' => $company, 'shop_code' => $customerShopCode, 'language_code' => $languageCode, 'customer_no' => $customerNo];
            $customer = $this->customerRepository->findByAltPrimary($customerPrimary);
            $this->customerByKey[$customerKey] = $customer;
        }

        $currencyCode = $this->getCurrencyCode($company, $targetShopCode, $languageCode, $customerShopCode, $customerNo);

        $providerKey = $company . $delim . $targetShopCode . $delim . $currencyCode;
        $vatManager = $this->getVatManager($company,$targetShopCode);
        if (array_key_exists(
                $providerKey,
                $this->providerByPrimary
            ) && $this->providerByPrimary[$providerKey] instanceof AdvancedPriceProvider
        ) {
            $provider = $this->providerByPrimary[$providerKey];
        } else {
            $basicPriceProvider = new BasicPriceProvider($this->pdo, $shop, $vatManager, $currencyCode);
            $provider = new AdvancedPriceProvider($basicPriceProvider, $shop, $vatManager, $currencyCode, $this->ruleDiscountRepository);
            $this->providerByPrimary[$providerKey] = $provider;
        }
        return $provider;
    }

    /**
     * @param $company
     * @param $languageCode
     * @param $customerShopCode
     * @param $customerNo
     * @return mixed
     */
    protected function getCustomer($company, $languageCode, $customerShopCode, $customerNo)
    {
        $delim = '|';
        $customerKey = $company . $delim . $customerShopCode . $delim . $languageCode . $delim . $customerNo;
        if (array_key_exists(
                $customerKey,
                $this->customerByKey
            ) && $this->customerByKey[$customerKey] instanceof CustomerInterface
        ) {
            $customer = $this->customerByKey[$customerKey];
            return $customer;
        }
        $customerPimary = ['company' => $company, 'shop_code' => $customerShopCode, 'language_code' => $languageCode, 'customer_no' => $customerNo];
        $customer = $this->customerRepository->findByAltPrimary($customerPimary);
        $this->customerByKey[$customerKey] = $customer;
        return $customer;

    }

    /**
     * @param $company
     * @param $targetShopCode
     * @return VATManager|mixed
     */
    protected function getVatManager($company, $targetShopCode)
    {
        $delim = '|';
        $shopKey = $company . $delim . $targetShopCode;
        if (array_key_exists($shopKey,$this->vatManagerByKey)) {
            return $this->vatManagerByKey[$shopKey];
        }

        if (array_key_exists($shopKey, $this->shopByKey) && $this->shopByKey[$shopKey] instanceof Shop) {
            $shop = $this->shopByKey[$shopKey];
        } else {
            $shop = $this->shopRepository->findByAltPrimary(['company' => $company, 'code' => $targetShopCode]);
            $this->shopByKey[$shopKey] = $shop;
        }
        $vatManager = new VATManager($this->pdo,$company,$shop->vat_bus_posting_group,$shop->prices_including_vat);
        $this->vatManagerByKey[$shopKey] = $vatManager;
        return $vatManager;
    }

}