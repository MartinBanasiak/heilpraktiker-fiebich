<?php
$root = dirname(__DIR__);
$startInit = microtime(true);
if(!isset($GLOBALS['IOC'])) {
    include(rtrim($root,'/') . '/plugins/dice/dice.php');
    $IOCContainer = new \Dice\Dice();
}

$DBWrapperRule = new \Dice\Rule();
$DBWrapperRule->shared = true;
$DBWrapperRule->constructParams = [$GLOBALS["myservername"], 3306, $GLOBALS["mydb"], $GLOBALS["mylogin"], $GLOBALS["mypass"]];
$IOCContainer->addRule('PDOQueryWrapper',$DBWrapperRule);
$IOCContainer->addRule('DynCom\dc\common\classes\PDOQueryWrapper',$DBWrapperRule);



$templatingRule = new \Dice\Rule();
$templatingRule->shared = true;
$templatingRule->constructParams = ['locTextConstants' => $GLOBALS['tc']];
//$IOCContainer->addRule('Templating',$templatingRule);
$IOCContainer->addRule('DynCom\dc\common\classes\Templating',$templatingRule);



/*$selectionCriteriaHelperRule = new \Dice\Rule();
$selectionCriteriaHelperRule->shared = true;
$IOCContainer->addRule('SelectionCriteriaHelper',$selectionCriteriaHelperRule);*/

$globalLogger = get_logger('global');
$loggerFactoryFunc = function() {
    return get_logger('global');
};

$allRule = new \Dice\Rule();
$allRule->substitutions['DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface'] = new \Dice\Instance('DynCom\dc\common\classes\PDOQueryWrapper');
$allRule->substitutions['DynCom\dc\common\interfaces\CriteriaHelperInterface'] = new \Dice\Instance('DynCom\dc\common\classes\SelectionCriteriaHelper');
$allRule->substitutions['DynCom\dc\common\interfaces\TemplatingInterface'] = new \Dice\Instance('DynCom\dc\common\classes\Templating');
$allRule->substitutions[\DynCom\dc\regionalization\RegionalizedTextProvider::class] = new \Dice\Instance('$CurrTextProvider');
$allRule->substitutions[\Psr\Log\LoggerInterface::class] = new \Dice\Instance($loggerFactoryFunc);
$IOCContainer->addRule('*',$allRule);

$visitorRepositoryRule = clone $IOCContainer->getRule('VisitorRepository');
$visitorRepositoryRule->substitutions['DynCom\dc\common\interfaces\ModelDBConfigInterface'] = new \Dice\Instance('DynCom\dc\common\classes\PDOQueryWrapper');
$visitorRepositoryRule->substitutions['DynCom\dc\common\interfaces\GenericCollectionInterface'] = new \Dice\Instance('VisitorCollection');
$visitorRepositoryRule->constructParams = ['cacheAll' => false];
$visitorRepositoryRule->shared = true;
$IOCContainer->addRule('VisitorRepository',$visitorRepositoryRule);
$IOCContainer->addRule('DynCom\dc\common\classes\VisitorRepository',$visitorRepositoryRule);





$siteRepositoryRule = clone $IOCContainer->getRule('SiteRepository');
$siteRepositoryRule->substitutions['DynCom\dc\common\interfaces\ModelDBConfigInterface'] = new \Dice\Instance('DynCom\dc\common\classes\SiteConfig');
$siteRepositoryRule->substitutions['DynCom\dc\common\interfaces\GenericCollectionInterface'] = new \Dice\Instance('DynCom\dc\common\classes\SiteCollection');
$siteRepositoryRule->constructParams = ['cacheAll' => false];
$siteRepositoryRule->shared = true;
$IOCContainer->addRule('SiteRepository',$siteRepositoryRule);
$IOCContainer->addRule('DynCom\dc\common\classes\SiteRepository',$siteRepositoryRule);


$languageRepositoryRule = clone $IOCContainer->getRule('LanguageRepository');
$languageRepositoryRule->substitutions['DynCom\dc\common\interfaces\ModelDBConfigInterface'] = new \Dice\Instance('DynCom\dc\common\classes\LanguageConfig');
$languageRepositoryRule->substitutions['DynCom\dc\common\interfaces\GenericCollectionInterface'] = new \Dice\Instance('DynCom\dc\common\classes\LanguageCollection');
$languageRepositoryRule->constructParams = ['cacheAll' => false];
$languageRepositoryRule->shared = true;
$IOCContainer->addRule('LanguageRepository',$languageRepositoryRule);
$IOCContainer->addRule('DynCom\dc\common\classes\LanguageRepository',$languageRepositoryRule);


$textModuleRepositoryRule = clone $IOCContainer->getRule('TextModuleRepository');
$textModuleRepositoryRule->substitutions['DynCom\dc\common\interfaces\ModelDBConfigInterface'] = new \Dice\Instance('DynCom\dc\common\classes\TextModuleConfig');
$textModuleRepositoryRule->substitutions['DynCom\dc\common\interfaces\GenericCollectionInterface'] = new \Dice\Instance('DynCom\dc\common\classes\TextModuleCollection');
$textModuleRepositoryRule->constructParams = ['cacheAll' => false];
$textModuleRepositoryRule->shared = true;
$IOCContainer->addRule('TextModuleRepository',$textModuleRepositoryRule);
$IOCContainer->addRule('DynCom\dc\common\classes\TextModuleRepository',$textModuleRepositoryRule);


$currSiteRule = clone $IOCContainer->getRule('Site');
$currSiteFactoryInstance = new \Dice\Instance('DynCom\dc\common\classes\SiteRepository');
$currSiteFactoryInstance->callMethodName = 'getFromRequest';
$currSiteRule->factoryInstance = $currSiteFactoryInstance;
$currSiteRule->shared = true;
$currSiteRule->instanceOf = \DynCom\dc\common\classes\Site::class;
$IOCContainer->addRule('$CurrSite',$currSiteRule);


$currLanguageRule = clone $IOCContainer->getRule('Language');
$currLanguageFactoryInstance = new \Dice\Instance('DynCom\dc\common\classes\LanguageRepository');
$currLanguageFactoryInstance->callMethodName = 'getFromRequest';
$currLanguageFactoryInstance->callMethodParams[] = new \Dice\Instance('DynCom\dc\common\classes\SiteRepository');
$currLanguageRule->factoryInstance = $currLanguageFactoryInstance;
$currLanguageRule->shared = true;
$currLanguageRule->instanceOf = \DynCom\dc\common\classes\Language::class;
$IOCContainer->addRule('$CurrLanguage',$currLanguageRule);


$currVisitorRule = clone $IOCContainer->getRule('Visitor');
$currVisitorFactoryInstance = new \Dice\Instance('DynCom\dc\common\classes\VisitorRepository');
$currVisitorFactoryInstance->callMethodName = 'getFromSession';
$currVisitorRule->factoryInstance = $currVisitorFactoryInstance;
$currVisitorRule->shared = true;
$currVisitorRule->instanceOf = \DynCom\dc\common\classes\Visitor::class;
$IOCContainer->addRule('$CurrVisitor',$currVisitorRule);


$validatorRule = clone $IOCContainer->getRule('Validator');
$validatorRule->constructParams = [[]];
$validatorRule->shared = false;
$IOCContainer->addRule('Validator',$validatorRule);
$IOCContainer->addRule('DynCom\dc\common\classes\Validator',$validatorRule);


$formBuilderRule = clone $IOCContainer->getRule('FormBuilder');
$formBuilderRule->constructParams = ['dummyID'];
$formBuilderRule->shared = false;
$IOCContainer->addRule('FormBuilder',$formBuilderRule);
$IOCContainer->addRule('DynCom\dc\common\classes\FormBuilder',$formBuilderRule);

//echo "<br>DURATION INIT: " . (microtime(true) - $startInit);
/*$_SESSION['IOC'] = serialize($IOCContainer);*/

$regionalizedTextProviderRule = clone $IOCContainer->getRule(\DynCom\dc\regionalization\RegionalizedTextProvider::class);
$regionalizedTextProviderRule->instanceOf = \DynCom\dc\regionalization\PHPFileRegionalizedTextProvider::class;
$regionalizedTextProviderFactoryInstance = new \Dice\Instance('$CurrLanguage');
$regionalizedTextProviderFactoryInstance->getFieldValueForName = 'locale_code';
$regionalizedTextProviderRule->constructParams = [$regionalizedTextProviderFactoryInstance];
$IOCContainer->addRule('$CurrTextProvider',$regionalizedTextProviderRule);


//Mustache
include(rtrim($root,'/') . '/vendor/mustache/mustache/src/Mustache/Autoloader.php');
$mustacheAutoloader = new Mustache_Autoloader();
$mustacheAutoloader->register();


//Framework-functions
include(rtrim($root,'/') . '/dc/common/framework_functions.php');


$GLOBALS['IOC'] = $IOCContainer;