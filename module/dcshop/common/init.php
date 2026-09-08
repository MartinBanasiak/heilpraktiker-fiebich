<?php

/*
Define autoloader
Add autoloader to queue
Define general functions
*/

/**
 * Define Autoloader
 *
 * @param $objectName
 */
/*
function DcShopAutoloader( $objectName ) {

    $dirArr = array(
        '../../module/myysdeshop/common/abstract/',
        '../../module/dcshop/common/classes/',
        '../../module/dcshop/common/traits/',
        '../../module/dcshop/common/interfaces/',
        '../../module/dcshop/subscriptions/classes/'
    );

    foreach ($dirArr as $dir) {
        if (file_exists(realpath($dir . $objectName . '.php'))) {
            include_once(realpath($dir . $objectName . '.php'));
            return;
        }
    }
}


spl_autoload_register('DcShopAutoloader');
*/
use DynCom\dc\common\classes\URL;
use DynCom\dc\common\interfaces\IOCInterface;
/*
include(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . '/module/ModuleAutoloader.php');
$moduleAutoloader = new ModuleAutoloader();
$moduleAutoloader->register();*/
/**
 * IOCContainer $IOCContainer
 */
if(!isset($IOCContainer) || !($IOCContainer instanceof IOCInterface)) {
    if (isset($GLOBALS['IOC']) && $GLOBALS['IOC'] instanceof IOCInterface) {
        $IOCContainer = $GLOBALS['IOC'];
    } elseif (isset($_SESSION['IOC']) && $_SESSION['IOC'] instanceof IOCInterface) {
        $IOCContainer = $_SESSION['IOC'];
    } else {
        include_once(rtrim(dirname(dirname(dirname(__DIR__))), '/') . '/plugins/dice/dice.php');
        $IOCContainer = new \Dice\Dice();
        include_once(rtrim(dirname(dirname(dirname(__DIR__))), '/') . '/dc/init.php');
    }
}
$startShopInit = microtime(true);



$allRule = clone $IOCContainer->getRule('*');
$allRule->substitutions['DynCom\dc\dcShop\classes\Shop'] = new \Dice\Instance('$CurrShop');
$allRule->substitutions['DynCom\dc\dcShop\interfaces\CustomerInterface'] = new \Dice\Instance('$CurrCustomer');
$allRule->substitutions['DynCom\dc\dcShop\interfaces\ItemAvailabilityProvider'] = new \Dice\Instance('DynCom\dc\dcShop\classes\DefaultItemAvailabilityProvider');
$allRule->substitutions['DynCom\dc\dcShop\interfaces\UserItemPermissionProvider'] = new \Dice\Instance('DynCom\dc\dcShop\classes\GenericUserItemPermissionProvider');
$allRule->substitutions['DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface'] = new \Dice\Instance('DynCom\dc\common\classes\PDOQueryWrapper');
$allRule->substitutions['DynCom\dc\common\interfaces\CriteriaHelperInterface'] = new \Dice\Instance('DynCom\dc\common\classes\SelectionCriteriaHelper');
$allRule->substitutions['DynCom\dc\dcShop\interfaces\ItemInventoryStrategyInterface'] = new \Dice\Instance('$CurrInventoryStrategy');
$allRule->substitutions['DynCom\dc\dcShop\interfaces\TemplatingInterface'] = new \Dice\Instance('DynCom\dc\common\classes\Templating');
$allRule->substitutions['DynCom\dc\dcShop\classes\CurrShopConfiguration'] = new \Dice\Instance('$CurrShopConfig');
$allRule->substitutions[\DynCom\dc\dcShop\interfaces\IVATManager::class] = new \Dice\Instance(\DynCom\dc\dcShop\classes\VATManager::class);
//$allRule->substitutions['DynCom\dc\dcShop\interfaces\IVATManager'] = new \Dice\Instance('$VATManager');
$allRule->substitutions['DynCom\dc\dcShop\interfaces\WebshopItemOrderabilityService'] = new \Dice\Instance('DynCom\dc\dcShop\classes\GenericWebshopItemOrderabilityService');
$allRule->substitutions['DynCom\dc\RuleEngine\ActiveActionItemRuleDiscountRepositoryInterface'] = new \Dice\Instance('$activeRuleDiscountRepository');
$IOCContainer->addRule('*',$allRule);

$shopSetupRepositoryRule = clone $IOCContainer->getRule('ShopSetupRepository');
$shopSetupRepositoryRule->substitutions['DynCom\dc\common\interfaces\ModelDBConfigInterface'] = new \Dice\Instance('DynCom\dc\dcShop\classes\ShopSetupConfig');
$shopSetupRepositoryRule->substitutions['DynCom\dc\common\interfaces\GenericCollectionInterface'] = new \Dice\Instance('DynCom\dc\dcShop\classes\ShopSetupCollection');
$shopSetupRepositoryRule->constructParams = ['cacheAll' => false];
$shopSetupRepositoryRule->shared = true;
$IOCContainer->addRule('ShopSetupRepository',$shopSetupRepositoryRule);
$IOCContainer->addRule('DynCom\dc\dcShop\classes\ShopSetupRepository',$shopSetupRepositoryRule);

$shopRepositoryRule = clone $IOCContainer->getRule('ShopRepository');
$shopRepositoryRule->substitutions['DynCom\dc\common\interfaces\ModelDBConfigInterface'] = new \Dice\Instance('DynCom\dc\dcShop\classes\ShopConfig');
$shopRepositoryRule->substitutions['DynCom\dc\common\interfaces\GenericCollectionInterface'] = new \Dice\Instance('DynCom\dc\dcShop\classes\ShopCollection');
$shopRepositoryRule->constructParams = ['cacheAll' => false];
$shopRepositoryRule->shared = true;
$IOCContainer->addRule('ShopRepository',$shopRepositoryRule);
$IOCContainer->addRule('DynCom\dc\dcShop\classes\ShopRepository',$shopRepositoryRule);

$shopLanguageRepositoryRule = clone $IOCContainer->getRule('ShopLanguageRepository');
$shopLanguageRepositoryRule->substitutions['DynCom\dc\common\interfaces\ModelDBConfigInterface'] = new \Dice\Instance('DynCom\dc\common\classes\ShopLanguageConfig');
$shopLanguageRepositoryRule->substitutions['DynCom\dc\common\interfaces\GenericCollectionInterface'] = new \Dice\Instance('DynCom\dc\dcShop\classes\ShopLanguageCollection');
$shopLanguageRepositoryRule->constructParams = ['cacheAll' => false];
$shopLanguageRepositoryRule->shared = true;
$IOCContainer->addRule('ShopLanguageRepository',$shopLanguageRepositoryRule);
$IOCContainer->addRule('DynCom\dc\dcShop\classes\ShopLanguageRepository',$shopLanguageRepositoryRule);

$categoryRepositoryRule = clone $IOCContainer->getRule('CategoryRepository');
$categoryRepositoryRule->substitutions['DynCom\dc\common\interfaces\ModelDBConfigInterface'] = new \Dice\Instance('DynCom\dc\dcShop\classes\CategoryConfig');
$categoryRepositoryRule->substitutions['DynCom\dc\common\interfaces\GenericCollectionInterface'] = new \Dice\Instance('DynCom\dc\dcShop\classes\CategoryCollection');
$categoryRepositoryRule->constructParams = ['cacheAll' => false];
$categoryRepositoryRule->shared = true;
$IOCContainer->addRule('CategoryRepository',$categoryRepositoryRule);
$IOCContainer->addRule('DynCom\dc\dcShop\classes\CategoryRepository',$categoryRepositoryRule);

$webshopItemRepositoryRule = clone $IOCContainer->getRule('WebshopItemRepository');
$webshopItemRepositoryRule->substitutions['DynCom\dc\common\interfaces\ModelDBConfigInterface'] = new \Dice\Instance('DynCom\dc\common\classes\WebshopItemConfig');
$webshopItemRepositoryRule->substitutions['DynCom\dc\common\interfaces\GenericCollectionInterface'] = new \Dice\Instance('DynCom\dc\dcShop\classes\WebshopItemCollection');
$webshopItemRepositoryRule->constructParams = ['cacheAll' => false];
$webshopItemRepositoryRule->shared = true;
$IOCContainer->addRule('WebshopItemRepository',$webshopItemRepositoryRule);
$IOCContainer->addRule('DynCom\dc\dcShop\classes\WebshopItemRepository',$webshopItemRepositoryRule);

$webshopItemVariantRepositoryRule = clone $IOCContainer->getRule('WebshopItemVariantRepository');
$webshopItemVariantRepositoryRule->substitutions['DynCom\dc\common\interfaces\ModelDBConfigInterface'] = new \Dice\Instance('DynCom\dc\common\classes\WebshopItemConfig');
$webshopItemVariantRepositoryRule->substitutions['DynCom\dc\common\interfaces\GenericCollectionInterface'] = new \Dice\Instance('DynCom\dc\dcShop\classes\WebshopItemCollection');
$webshopItemVariantRepositoryRule->constructParams = ['cacheAll' => false];
$webshopItemVariantRepositoryRule->shared = true;
$IOCContainer->addRule('WebshopItemVariantRepository',$webshopItemVariantRepositoryRule);
$IOCContainer->addRule('DynCom\dc\dcShop\classes\WebshopItemVariantRepository',$webshopItemVariantRepositoryRule);

$userRepositoryRule = clone $IOCContainer->getRule('UserRepository');
$userRepositoryRule->substitutions['DynCom\dc\common\interfaces\ModelDBConfigInterface'] = new \Dice\Instance('DynCom\dc\common\classes\UserConfig');
$userRepositoryRule->substitutions['DynCom\dc\common\interfaces\GenericCollectionInterface'] = new \Dice\Instance('DynCom\dc\dcShop\classes\UserCollection');
$userRepositoryRule->constructParams = ['cacheAll' => false];
$userRepositoryRule->shared = true;
$IOCContainer->addRule('UserRepository',$userRepositoryRule);
$IOCContainer->addRule('DynCom\dc\dcShop\classes\UserRepository',$userRepositoryRule);

$salespersonRepositoryRule = clone $IOCContainer->getRule('SalespersonRepository');
$salespersonRepositoryRule->substitutions['DynCom\dc\common\interfaces\ModelDBConfigInterface'] = new \Dice\Instance('DynCom\dc\common\classes\SalespersonConfig');
$salespersonRepositoryRule->substitutions['DynCom\dc\common\interfaces\GenericCollectionInterface'] = new \Dice\Instance('DynCom\dc\dcShop\classes\SalespersonCollection');
$salespersonRepositoryRule->constructParams = ['cacheAll' => false];
$salespersonRepositoryRule->shared = true;
$IOCContainer->addRule('SalespersonRepository',$salespersonRepositoryRule);
$IOCContainer->addRule('SalespersonRepository',$salespersonRepositoryRule);

$customerRepositoryRule = clone $IOCContainer->getRule('CustomerRepository');
$customerRepositoryRule->substitutions['DynCom\dc\common\interfaces\ModelDBConfigInterface'] = new \Dice\Instance('DynCom\dc\common\classes\CustomerConfig');
$customerRepositoryRule->substitutions['DynCom\dc\common\interfaces\GenericCollectionInterface'] = new \Dice\Instance('DynCom\dc\dcShop\classes\CustomerCollection');
$customerRepositoryRule->constructParams = ['cacheAll' => false];
$customerRepositoryRule->shared = true;
$IOCContainer->addRule('CustomerRepository',$customerRepositoryRule);
$IOCContainer->addRule('DynCom\dc\dcShop\classes\CustomerRepository',$customerRepositoryRule);

$shippingOptionRepositoryRule = clone $IOCContainer->getRule('$ShippingOptionRepository');
$shippingOptionRepositoryRule->substitutions['DynCom\dc\common\interfaces\ModelDBConfigInterface'] = new \Dice\Instance('DynCom\dc\dcShop\ShippingOptions\ShippingOptionConfig');
$shippingOptionRepositoryRule->substitutions['DynCom\dc\common\interfaces\GenericCollectionInterface'] = new \Dice\Instance('DynCom\dc\dcShop\ShippingOptions\ShippingOptionCollection');
$shippingOptionRepositoryRule->substitutions['DynCom\dc\dcShop\ShippingOptions\ShippingZone'] = new \Dice\Instance('DynCom\dc\dcShop\ShippingOptions\ShippingZone');
$shippingOptionRepositoryRule->substitutions['DynCom\dc\dcShop\ShippingOptions\ShippingOptionTranslationRepository'] = new \Dice\Instance('DynCom\dc\dcShop\ShippingOptions\ShippingOptionTranslationRepository');
$shippingOptionRepositoryRule->constructParams = ['cacheAll' => false];
$shippingOptionRepositoryRule->shared = true;
$shippingOptionRepositoryRule->instanceOf = '\DynCom\dc\dcShop\ShippingOptions\ShippingOptionRepository';
$IOCContainer->addRule('$ShippingOptionRepository',$shippingOptionRepositoryRule);

$shippingClassRepositoryRule = clone $IOCContainer->getRule('$ShippingClassRepository');
$shippingClassRepositoryRule->substitutions['DynCom\dc\common\interfaces\ModelDBConfigInterface'] = new \Dice\Instance('DynCom\dc\dcShop\ShippingOptions\ShippingClassConfig');
$shippingClassRepositoryRule->substitutions['DynCom\dc\common\interfaces\GenericCollectionInterface'] = new \Dice\Instance('DynCom\dc\dcShop\ShippingOptions\ShippingClassCollection');
$shippingClassRepositoryRule->constructParams = ['cacheAll' => false];
$shippingClassRepositoryRule->shared = true;
$shippingClassRepositoryRule->instanceOf = '\DynCom\dc\dcShop\ShippingOptions\ShippingClassRepository';
$IOCContainer->addRule('$ShippingClassRepository',$shippingClassRepositoryRule);

$ShippingClassPriorityProviderRuleProviderRule = clone $IOCContainer->getRule('ShippingClassPriorityProvider');
$ShippingClassPriorityProviderRuleProviderRule->substitutions['DynCom\dc\dcShop\ShippingOptions\ShippingClassRepository'] = new \Dice\Instance('$shippingClassRepository');
$IOCContainer->addRule('ShippingClassPriorityProvider',$ShippingClassPriorityProviderRuleProviderRule);
$IOCContainer->addRule('DynCom\dc\dcShop\ShippingOptions\ShippingClassPriorityProvider',$ShippingClassPriorityProviderRuleProviderRule);

$currShopSetupRule = clone $IOCContainer->getRule('ShopSetup');
$currShopSetupFactoryInstance = new \Dice\Instance('DynCom\dc\common\classes\LanguageRepository');
$currShopSetupFactoryInstance->callMethodName = 'getCurrShopSetupFromRequest';
$currShopSetupFactoryInstance->callMethodParams[] = new \Dice\Instance('DynCom\dc\dcShop\classes\ShopSetupRepository');
$currShopSetupFactoryInstance->callMethodParams[] = new \Dice\Instance('DynCom\dc\common\classes\SiteRepository');
$currShopSetupRule->factoryInstance = $currShopSetupFactoryInstance;
$currShopSetupRule->shared = true;
$currShopSetupRule->instanceOf = '\DynCom\dc\dcShop\classes\ShopSetup';
$IOCContainer->addRule('$CurrShopSetup',$currShopSetupRule);

$currShopRule = clone $IOCContainer->getRule('Shop');
$currShopFactoryInstance = new \Dice\Instance('DynCom\dc\common\classes\LanguageRepository');
$currShopFactoryInstance->callMethodName = 'getCurrShopFromRequest';
$currShopFactoryInstance->callMethodParams[] = new \Dice\Instance('DynCom\dc\dcShop\classes\ShopRepository');
$currShopFactoryInstance->callMethodParams[] = new \Dice\Instance('DynCom\dc\common\classes\SiteRepository');
$currShopRule->factoryInstance = $currShopFactoryInstance;
$currShopRule->shared = true;
$currShopRule->instanceOf = '\DynCom\dc\dcShop\classes\Shop';
$IOCContainer->addRule('$CurrShop',$currShopRule);

$currShopLanguageRule = clone $IOCContainer->getRule('DynCom\dc\dcShop\classes\ShopLanguage');
$currShopLanguageFactoryInstance = new \Dice\Instance('DynCom\dc\common\classes\LanguageRepository');
$currShopLanguageFactoryInstance->callMethodName = 'getCurrShopLanguageFromRequest';
$currShopLanguageFactoryInstance->callMethodParams[] = new \Dice\Instance('DynCom\dc\dcShop\classes\ShopLanguageRepository');
$currShopLanguageFactoryInstance->callMethodParams[] = new \Dice\Instance('DynCom\dc\common\classes\SiteRepository');
$currShopLanguageRule->factoryInstance = $currShopLanguageFactoryInstance;
$currShopLanguageRule->shared = true;
$currShopLanguageRule->instanceOf = '\DynCom\dc\dcShop\classes\ShopLanguage';
$IOCContainer->addRule('$CurrShopLanguage',$currShopLanguageRule);

$currCategoryRule = clone $IOCContainer->getRule('Category');
$currCategoryFactoryInstance = new \Dice\Instance('DynCom\dc\dcShop\classes\CategoryRepository');
$currCategoryFactoryInstance->callMethodName = 'getFromRequest';
$currCategoryFactoryInstance->callMethodParams[] = new \Dice\Instance('$CurrShopLanguage');
$currCategoryRule->factoryInstance = $currCategoryFactoryInstance;
$currCategoryRule->shared = true;
$currCategoryRule->instanceOf = '\DynCom\dc\dcShop\classes\Category';
$IOCContainer->addRule('$CurrCategory',$currCategoryRule);

$currUserRule = clone $IOCContainer->getRule('User');
if (isset($GLOBALS['shop']) && is_array($GLOBALS['shop']) && array_key_exists('shop_typ',$GLOBALS['shop']) && (int)$GLOBALS['shop']['shop_typ'] === 2) {
	$currUserFactoryInstance = new \Dice\Instance('DynCom\dc\dcShop\classes\SalespersonRepository');
} else {
	$currUserFactoryInstance = new \Dice\Instance('DynCom\dc\dcShop\classes\UserRepository');
}

$currUserFactoryInstance->callMethodName = 'getUserForCurrVisitor';
$currUserFactoryInstance->callMethodParams[] = new \Dice\Instance('$CurrVisitor');
$currUserRule->factoryInstance = $currUserFactoryInstance;
$currUserRule->shared = true;
$currUserRule->instanceOf = '\DynCom\dc\dcShop\classes\User';
$IOCContainer->addRule('$CurrUser',$currUserRule);

$currCustomerRule = clone $IOCContainer->getRule('Customer');
$currCustomerFactoryInstance = new \Dice\Instance('DynCom\dc\dcShop\classes\CustomerRepository');
$currCustomerFactoryInstance->callMethodName = 'getCustomerForCurrUserAndLangCode';
$currCustomerFactoryInstance->callMethodParams[] = new \Dice\Instance('$CurrUser');
$currCustLangCodeParamInstance = new \Dice\Instance('$CurrShopLanguage');
$currCustLangCodeParamInstance->getFieldValueForName = 'code';
$currCustomerFactoryInstance->callMethodParams[] = $currCustLangCodeParamInstance;
$currCustomerRule->factoryInstance = $currCustomerFactoryInstance;
$currCustomerRule->shared = true;
$currCustomerRule->instanceOf = '\DynCom\dc\dcShop\classes\Customer';
$IOCContainer->addRule('$CurrCustomer',$currCustomerRule);


$vatMgrCompanyInst = new \Dice\Instance('$CurrShop');
$vatMgrCompanyInst->getFieldValueForName = 'company';
$vatMgrVBPSInst = new \Dice\Instance('$CurrShop');
$vatMgrVBPSInst->getFieldValueForName = 'vat_bus_posting_group';
$vatMgrPIVInst = new \Dice\Instance('$CurrShop');
$vatMgrPIVInst->getFieldValueForName = 'prices_including_vat';
$vatMgrShopCodeInst = new \Dice\Instance('$CurrShop');
$vatMgrShopCodeInst->getFieldValueForName = 'code';
$vatMgrLangCodeInst = new \Dice\Instance('$CurrShopLanguage');
$vatMgrLangCodeInst->getFieldValueForName = 'code';

$vatMgrFactoryInstance = new \Dice\Instance('\DynCom\dc\dcShop\classes\VATManagerFactory');
$vatMgrFactoryInstance->callMethodName = 'getVATManager';
$vatMgrFactoryInstance->callMethodParams = [
    'shopDB' => new \Dice\Instance('\DynCom\dc\common\classes\PDOQueryWrapper'),
    'company' => $vatMgrCompanyInst,
    'shopCode' => $vatMgrShopCodeInst,
    'languageCode' => $vatMgrLangCodeInst,
    'VATBusPostingGroup' => $vatMgrVBPSInst,
    'shipToCountry' => $_SESSION['visitor_country_shipping'] ?? null,
    'customerVatID' => $_SESSION['visitor_vatid'] ?? '',
    'shopPricesIncludeVAT' => $vatMgrPIVInst
];

$vatMgrRule = clone $IOCContainer->getRule('DynCom\dc\dcShop\classes\VATManager');
$vatMgrRule->shared = true;
$vatMgrRule->factoryInstance = $vatMgrFactoryInstance;
$vatMgrRule->instanceOf = \DynCom\dc\dcShop\classes\VATManager::class;
$IOCContainer->addRule('DynCom\dc\dcShop\classes\VATManager',$vatMgrRule);
$IOCContainer->addRule(\DynCom\dc\dcShop\classes\VATManager::class,$vatMgrRule);
$IOCContainer->addRule('$VATManager',$vatMgrRule);

$basicPriceProviderRule = clone $IOCContainer->getRule('BasicPriceProvider');
$basicPriceProviderRule->substitutions['DynCom\dc\dcShop\classes\Shop'] = new \Dice\Instance('$CurrShop');
$basicPriceProviderRule->substitutions[\DynCom\dc\dcShop\interfaces\IVATManager::class] = new \Dice\Instance('$VATManager');
$basicPriceProviderDCCInst = new \Dice\Instance('$CurrShopLanguage');
$basicPriceProviderDCCInst->getFieldValueForName = 'default_currency_code';
$basicPriceProviderRule->constructParams = [$basicPriceProviderDCCInst];
$basicPriceProviderRule->shared = true;
$IOCContainer->addRule('BasicPriceProvider',$basicPriceProviderRule);
$IOCContainer->addRule('DynCom\dc\dcShop\classes\BasicPriceProvider',$basicPriceProviderRule);


$activeRuleDiscountRepository = clone $IOCContainer->getRule('$activeRuleDiscountRepository');
$activeRuleDiscountRepository->shared = true;
$activeRuleDiscountRepository->substitutions['DynCom\dc\dcShop\classes\Shop'] = new \Dice\Instance('$CurrShop');
$activeRuleDiscountRepository->instanceOf = 'DynCom\dc\RuleEngine\ActiveActionItemRuleDiscountRepository';
$IOCContainer->addRule('$activeRuleDiscountRepository',$activeRuleDiscountRepository);

$advancedPriceProviderRule = clone $IOCContainer->getRule('AdvancedPriceProvider');
$advancedPriceProviderRule->substitutions['DynCom\dc\dcShop\classes\Shop'] = new \Dice\Instance('$CurrShop');
$advancedPriceProviderRule->substitutions[\DynCom\dc\dcShop\interfaces\IVATManager::class] = new \Dice\Instance('$VATManager');
$advancedPriceProviderRule->substitutions['DynCom\dc\RuleEngine\ActiveActionItemRuleDiscountRepository'] = new \Dice\Instance('$activeRuleDiscountRepository');
$advancedPriceProviderDCCInst = new \Dice\Instance('$CurrShopLanguage');
$advancedPriceProviderDCCInst->getFieldValueForName = 'default_currency_code';
$advancedPriceProviderRule->constructParams = [$advancedPriceProviderDCCInst];
$advancedPriceProviderRule->shared = true;
$IOCContainer->addRule('AdvancedPriceProvider',$advancedPriceProviderRule);
$IOCContainer->addRule('DynCom\dc\dcShop\classes\AdvancedPriceProvider',$advancedPriceProviderRule);
$IOCContainer->addRule(\DynCom\dc\dcShop\classes\AdvancedPriceProvider::class,$advancedPriceProviderRule);

$countryRepositoryRule = clone $IOCContainer->getRule('CountryRepository');
$countryRepositoryRule->substitutions['DynCom\dc\common\interfaces\ModelDBConfigInterface'] = new \Dice\Instance('DynCom\dc\dcShop\classes\ShopConfig');
$countryRepositoryRule->substitutions['DynCom\dc\common\interfaces\GenericCollectionInterface'] = new \Dice\Instance('DynCom\dc\dcShop\classes\ShopCollection');
$countryRepositoryRule->constructParams = ['cacheAll' => false];
$countryRepositoryRule->shared = true;
$IOCContainer->addRule('CountryRepository',$countryRepositoryRule);
$IOCContainer->addRule('DynCom\dc\dcShop\classes\CountryRepository',$countryRepositoryRule);

$itemRecommRule = clone $IOCContainer->getRule('ItemRecommendationRepository');
$itemRecommRule->substitutions['DynCom\dc\common\interfaces\ModelDBConfigInterface'] = new \Dice\Instance('DynCom\dc\dcShop\classes\ItemRecommendationConfig');
$itemRecommRule->substitutions['DynCom\dc\common\interfaces\GenericCollectionInterface'] = new \Dice\Instance('DynCom\dc\dcShop\classes\ItemRecommendationCollection');
$itemRecommRule->constructParams = ['cacheAll' => false];
$itemRecommRule->shared = true;
$IOCContainer->addRule('ItemRecommendationRepository',$itemRecommRule);
$IOCContainer->addRule('DynCom\dc\dcShop\classes\ItemRecommendationRepository',$itemRecommRule);


$inventoryStrategyRule = clone $IOCContainer->getRule('$CurrInventoryStrategy');
$inventoryStrategyFactoryObj = new \Dice\Instance('DynCom\dc\dcShop\classes\InventoryStrategyFactory');
$inventoryStrategyFactoryObj->callMethodName = 'getInventoryStrategy';
$inventoryStrategyFactoryObj->callMethodParams[] = new \Dice\Instance('$CurrShop');
$inventoryStrategyRule->factoryInstance = $inventoryStrategyFactoryObj;
$inventoryStrategyRule->shared = true;
$inventoryStrategyRule->instanceOf = '\DynCom\dc\dcShop\interfaces\ItemInventoryStrategyInterface';
$IOCContainer->addRule('$CurrInventoryStrategy',$inventoryStrategyRule);


$couponLineRepoRule = clone $IOCContainer->getRule(\DynCom\dc\dcShop\classes\CouponLineRepository::class);
$couponLineRepoRule->constructParams = ['cacheAll' => false];
$couponLineRepoRule->shared = true;
$IOCContainer->addRule(\DynCom\dc\dcShop\classes\CouponLineRepository::class,$couponLineRepoRule);

$couponHeaderRepoRule = clone $IOCContainer->getRule(\DynCom\dc\dcShop\classes\CouponHeaderRepository::class);
$couponHeaderRepoRule->constructParams = ['cacheAll' => false];
$couponHeaderRepoRule->shared = true;
$IOCContainer->addRule(\DynCom\dc\dcShop\classes\CouponHeaderRepository::class,$couponHeaderRepoRule);

$shippingZoneLineRepoRule = clone $IOCContainer->getRule(\DynCom\dc\dcShop\ShippingOptions\ShippingZoneLine::class);
$shippingZoneLineRepoRule->constructParams = ['cacheAll' => false];
$shippingZoneLineRepoRule->shared = true;
$IOCContainer->addRule(\DynCom\dc\dcShop\ShippingOptions\ShippingZoneLineRepository::class,$shippingZoneLineRepoRule);

$shippingZoneRepoRule = clone $IOCContainer->getRule(\DynCom\dc\dcShop\ShippingOptions\ShippingZone::class);
$shippingZoneRepoRule->constructParams = ['cacheAll' => false];
$shippingZoneRepoRule->shared = true;
$IOCContainer->addRule(\DynCom\dc\dcShop\ShippingOptions\ShippingZoneRepository::class,$shippingZoneRepoRule);

$shippingOptionTranslationRepoRule = clone $IOCContainer->getRule(\DynCom\dc\dcShop\ShippingOptions\ShippingOptionTranslationRepository::class);
$shippingOptionTranslationRepoRule->constructParams = ['cacheAll' => false];
$shippingOptionTranslationRepoRule->shared = true;
$IOCContainer->addRule(\DynCom\dc\dcShop\ShippingOptions\ShippingOptionTranslationRepository::class,$shippingOptionTranslationRepoRule);

/* ---AUSGELAGERT--- */
/*
$subscHeaderRepoRule = clone $IOCContainer->getRule('SubscriptionHeaderRepository');
$subscHeaderRepoRule->substitutions['DynCom\dc\common\interfaces\ModelDBConfigInterface'] = new \Dice\Instance('SubscriptionHeaderConfig');
$subscHeaderRepoRule->substitutions['DynCom\dc\common\interfaces\GenericCollectionInterface'] = new \Dice\Instance('SubscriptionHeaderCollection');
$subscHeaderRepoRule->constructParams = ['cacheAll' => false]
$subscHeaderRepoRule->shared = true;
$IOCContainer->addRule('SubscriptionHeaderRepository',$subscHeaderRepoRule);

$subscSeqStepRepoRule = clone $IOCContainer->getRule('SubscriptionSequenceStepRepository');
$subscSeqStepRepoRule->substitutions['DynCom\dc\common\interfaces\ModelDBConfigInterface'] = new \Dice\Instance('SubscriptionSequenceStepConfig');
$subscSeqStepRepoRule->substitutions['DynCom\dc\common\interfaces\GenericCollectionInterface'] = new \Dice\Instance('SubscriptionSequenceStepCollection');
$subscSeqStepRepoRule->constructParams = ['cacheAll' => false]
$subscSeqStepRepoRule->shared = true;
$IOCContainer->addRule('SubscriptionSequenceStepRepository',$subscSeqStepRepoRule);

$subscItemLinkRule = clone $IOCContainer->getRule('SubscriptionItemLinkRepository');
$subscItemLinkRule->substitutions['DynCom\dc\common\interfaces\ModelDBConfigInterface'] = new \Dice\Instance('SubscriptionItemLinkConfig');
$subscItemLinkRule->substitutions['DynCom\dc\common\interfaces\GenericCollectionInterface'] = new \Dice\Instance('SubscriptionItemLinkCollection');
$subscItemLinkRule->constructParams = ['cacheAll' => false]
$subscItemLinkRule->shared = true;
$IOCContainer->addRule('SubscriptionItemLinkRepository',$subscItemLinkRule);
*/
/* +++AUSGELAGERT+++ */


$GLOBALS['site']['site_url'] = (strtoupper(substr(php_uname('s'), 0, 3)) === 'WIN') ? 'localhost' : $GLOBALS['site']['site_url'];
$siteDomain = str_replace(['http://','https://'],'',$GLOBALS['site']['site_url']);
$shopBaseURLRule = clone $IOCContainer->getRule('URL');
$shopBaseURLRule->shared = true;
$shopBaseURLRule->constructParams = [
    'protocol' => URL::PROTOCOL_HTTP,
    'user' => null,
    'password' => null,
    'domain' => $siteDomain,
    'port' => null,
    'path' => '/' . customizeUrl() . '/',
    'queryParams' => null,
    'anchor' => null
];

$shopBaseURLRule->shared = true;
$shopBaseURLRule->instanceOf = '\DynCom\dc\common\classes\URL';
$IOCContainer->addRule('$ShopBaseURL',$shopBaseURLRule);

$currCustomerWithPermissionGroupsRule = clone $IOCContainer->getRule('$CurrCustomerWithPermissionGroupCodes');
$currCustomerWithPermissionGroupsRule->shared = true;
$currCustomerWithPermissionGroupsRuleFactory = new \Dice\Instance('DynCom\dc\dcShop\classes\CustomerRepository');
$currCustomerWithPermissionGroupsRuleFactory->callMethodName = 'getCustomerWithPermissionGroupsForCurrUserAndLangCode';
$currCustomerWithPermissionGroupsRuleFactory->callMethodParams[] = new \Dice\Instance('$CurrUser');
$currCustomerWithPermissionGroupsRuleFactory->callMethodParams[] = $currCustLangCodeParamInstance;
$currCustomerWithPermissionGroupsRule->factoryInstance = $currCustomerWithPermissionGroupsRuleFactory;
$currCustomerWithPermissionGroupsRule->shared = true;
$currCustomerWithPermissionGroupsRule->instanceOf = 'DynCom\dc\dcShop\classes\CustomerPermissionGroupDecorator';
$IOCContainer->addRule('$CurrCustomerWithPermissionGroupCodes',$currCustomerWithPermissionGroupsRule);

$currShopConfigRule = clone $IOCContainer->getRule('CurrShopConfiguration');
$currShopConfigRule->shared = true;
$currShopConfigRule->substitutions['DynCom\dc\dcShop\classes\ShopSetup'] = new \Dice\Instance('$CurrShopSetup');
$currShopConfigRule->substitutions['DynCom\dc\dcShop\classes\Shop'] = new \Dice\Instance('$CurrShop');
$currShopConfigRule->substitutions['DynCom\dc\dcShop\classes\ShopLanguage'] = new \Dice\Instance('$CurrShopLanguage');
$currShopConfigRule->substitutions['DynCom\dc\common\classes\Visitor'] = new \Dice\Instance('$CurrVisitor');
$currShopConfigRule->substitutions['DynCom\dc\dcShop\classes\User'] = new \Dice\Instance('$CurrUser');
$currShopConfigRule->substitutions['DynCom\dc\dcShop\classes\Customer'] = new \Dice\Instance('$CurrCustomerWithPermissionGroupCodes');
$currShopConfigRule->substitutions['DynCom\dc\common\classes\URL'] = new \Dice\Instance('$ShopBaseURL');
$currShopConfigRule->instanceOf = 'DynCom\dc\dcShop\classes\CurrShopConfiguration';
$IOCContainer->addRule('$CurrShopConfig',$currShopConfigRule);

$webshopItemVariantServiceRule = clone $IOCContainer->getRule('WebshopItemVariantService');
$webshopItemVariantServiceRule->shared = true;
$webshopItemVariantServiceRule->substitutions['CurrShopConfiguration'] = new \Dice\Instance('$CurrShopConfig');
$webshopItemVariantServiceRule->instanceOf = '\DynCom\dc\dcShop\classes\WebshopItemVariantService';
$IOCContainer->addRule('WebshopItemVariantService',$webshopItemVariantServiceRule);
$IOCContainer->addRule('DynCom\dc\dcShop\classes\WebshopItemVariantService',$webshopItemVariantServiceRule);

$webshopItemBuilderRule = clone $IOCContainer->getRule('WebshopItemBuilder');
$webshopItemBuilderRule->shared = true;
$webshopItemBuilderRule->substitutions['DynCom\dc\common\classes\Visitor'] = new \Dice\Instance('$CurrVisitor');
$webshopItemBuilderRule->substitutions['DynCom\dc\dcShop\classes\Customer'] = new \Dice\Instance('$CurrCustomer');
$webshopItemBuilderRule->substitutions['DynCom\dc\dcShop\classes\Shop'] = new \Dice\Instance('$CurrShop');
$webshopItemBuilderRule->substitutions['DynCom\dc\dcShop\classes\ShopLanguage'] = new \Dice\Instance('$CurrShopLanguage');
$webshopItemBuilderRule->substitutions['DynCom\dc\common\classes\Visitor'] = new \Dice\Instance('$CurrVisitor');
$webshopItemBuilderRule->constructParams = ['currencyCode' => $basicPriceProviderDCCInst];
$IOCContainer->addRule('WebshopItemBuilder',$webshopItemBuilderRule);
$IOCContainer->addRule('DynCom\dc\dcShop\classes\WebshopItemBuilder',$webshopItemBuilderRule);

$basketRepoRule = clone $IOCContainer->getRule('UserBasketRepository');
$basketRepoRule->substitutions['DynCom\dc\dcShop\interfaces\WebshopItemOrderabilityService'] = new \Dice\Instance('DynCom\dc\dcShop\classes\GenericWebshopItemOrderabilityService');
$basketRepoRule->substitutions[\DynCom\dc\dcShop\classes\CouponLine::class] = new \Dice\Instance(\DynCom\dc\dcShop\classes\CouponLine::class);
$IOCContainer->addRule('UserBasketRepository',$basketRepoRule);
$IOCContainer->addRule('DynCom\dc\dcShop\classes\UserBasketRepository',$basketRepoRule);



$currUserBasketRule = clone $IOCContainer->getRule('$CurrUserBasket');
$currUserBasketRule->shared = true;
$currUserBasketRule->substitutions['DynCom\dc\common\classes\Visitor'] = new \Dice\Instance('$CurrVisitor');
$currUserBasketRule->substitutions['DynCom\dc\dcShop\classes\User'] = new \Dice\Instance('$CurrUser');
$currUserBasketRule->substitutions['mainBasket'] = true;
$currUserBasketRule->instanceOf = '\DynCom\dc\dcShop\classes\GenericUserBasket';
$currUserBasketFactory = new \Dice\Instance('DynCom\dc\dcShop\classes\UserBasketRepository');
$currUserBasketFactory->callMethodName = 'getCurrUserMainBasket';
$currUserBasketFactory->callMethodParams[] = new \Dice\Instance('$CurrShopConfig');
$currUserBasketRule->factoryInstance = $currUserBasketFactory;
$IOCContainer->addRule('$CurrUserBasket',$currUserBasketRule);

$currCustomerWithPermissionGroupsRule = clone $IOCContainer->getRule('$CurrCustomerWithPermissionGroupCodes');
$currCustomerWithPermissionGroupsRule->shared = true;
$currCustomerWithPermissionGroupsRuleFactory = new \Dice\Instance('DynCom\dc\dcShop\classes\CustomerRepository');
$currCustomerWithPermissionGroupsRuleFactory->callMethodName = 'getCustomerWithPermissionGroupsForCurrUserAndLangCode';
$currCustomerWithPermissionGroupsRuleFactory->callMethodParams[] = new \Dice\Instance('$CurrUser');
$currCustomerWithPermissionGroupsRuleFactory->callMethodParams[] = $currCustLangCodeParamInstance;
$currCustomerWithPermissionGroupsRule->factoryInstance = $currCustomerWithPermissionGroupsRuleFactory;
$currCustomerWithPermissionGroupsRule->shared = true;
$currCustomerWithPermissionGroupsRule->instanceOf = '\DynCom\dc\dcShop\classes\CustomerPermissionGroupDecorator';
$IOCContainer->addRule('$CurrCustomerWithPermissionGroupCodes',$currCustomerWithPermissionGroupsRule);

$userBasketPersistenceHandlerRule = clone $IOCContainer->getRule('$UserBasketPersistenceHandler');
$userBasketPersistenceHandlerRule->shared = true;
$userBasketPersistenceHandlerRule->substitutions['DynCom\dc\dcShop\classes\UserBasketRepository'] = new \Dice\Instance('DynCom\dc\dcShop\classes\UserBasketRepository');
$userBasketPersistenceHandlerRule->substitutions['DynCom\dc\dcShop\interfaces\UserBasket'] = new \Dice\Instance('$CurrUserBasket');
$userBasketPersistenceHandlerRule->instanceOf = '\DynCom\dc\dcShop\classes\UserBasketPersistenceHandler';
$IOCContainer->addRule('$UserBasketPersistenceHandler',$userBasketPersistenceHandlerRule);

$ruleContextRule = clone $IOCContainer->getRule('$CurrRuleContext');
$ruleContextRule->shared = true;
$ruleContextRule->instanceOf = '\DynCom\dc\RuleEngine\GenericRuleContext';
$IOCContainer->addRule('$CurrRuleContext',$ruleContextRule);



$basketListenerRule = clone $IOCContainer->getRule('UserBasketListener');
$basketListenerRule->shared = true;
$basketListenerRule->substitutions['DynCom\dc\dcShop\interfaces\UserBasket'] = new \Dice\Instance('$CurrUserBasket');
$basketListenerRule->substitutions['DynCom\dc\dcShop\classes\CurrShopConfiguration'] = new \Dice\Instance('$CurrShopConfig');
$basketListenerRule->substitutions['DynCom\dc\dcShop\interfaces\WebshopItemOrderabilityService'] = new \Dice\Instance('DynCom\dc\dcShop\classes\GenericWebshopItemOrderabilityService');
//$basketListenerRule->substitutions['CustomizationService'] = new \Dice\Instance('CustomizationService');
$IOCContainer->addRule('UserBasketListener',$basketListenerRule);
$IOCContainer->addRule('DynCom\dc\dcShop\classes\UserBasketListener',$basketListenerRule);

$currCategoryTreeRule = clone $IOCContainer->getRule('$CurrCategoryTree');
$currShopCompanyParamInstance = new \Dice\Instance('$CurrShop');
$currShopCompanyParamInstance->getFieldValueForName = 'company';
$currShopCategorySourceParamInstance = new \Dice\Instance('$CurrShop');
$currShopCategorySourceParamInstance->callMethodName= 'getUseCategoriesFromShopCode';
$currShopLanguageCodeParamInstance = new \Dice\Instance('$CurrShopLanguage');
$currShopLanguageCodeParamInstance->getFieldValueForName= 'code';
$currCategoryTreeFactoryInstance = new \Dice\Instance('DynCom\dc\dcShop\classes\CategoryTreeBuilder');
$currCategoryTreeFactoryInstance->callMethodName = 'getCategoryTree';
$currCategoryTreeFactoryInstance->callMethodParams[] = $currShopCompanyParamInstance;
$currCategoryTreeFactoryInstance->callMethodParams[] = $currShopCategorySourceParamInstance;
$currCategoryTreeFactoryInstance->callMethodParams[] = $currShopLanguageCodeParamInstance;
$currCategoryTreeRule->factoryInstance = $currCategoryTreeFactoryInstance;
$currCategoryTreeRule->shared = true;
$currCategoryTreeRule->instanceOf = '\DynCom\dc\dcShop\classes\CategoryTreeNode';
$IOCContainer->addRule('$CurrCategoryTree',$currCategoryTreeRule);


$subsc_init_path = rtrim(dirname(dirname(dirname(__DIR__))),'/') . '/module/dcshop/subscriptions/init.php';
include_once($subsc_init_path);

//$a = new CurrShopConfiguration($shop,$sl,$v,)

/*$_SESSION['IOC'] = $IOCContainer;*/

//echo "<br>Duration shop init: " . (microtime(true) - $startShopInit);

//$startInst = microtime(true);
//$currShopTest = $IOCContainer->create('$CurrShop');
//echo "<br>Time to instantiate CurrShop: " . (microtime(true) - $startInst);
//var_dump($currShopTest);
$GLOBALS['IOC'] = $IOCContainer;