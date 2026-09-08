<?php
namespace DynCom\dc\dcShop\subscriptions;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 28.08.2015
 * Time: 10:25
 */
if(!isset($IOCContainer)) {
    $IOCContainer = unserialize($_SESSION['IOC']);
}


$subscCustLinkRepoRule = $IOCContainer->getRule('SubscriptionCustomerLinkRepository');
$subscCustLinkRepoRule->substitutions['ModelDBConfigInterface'] = new \Dice\Instance('ShopLanguageConfig');
$subscCustLinkRepoRule->substitutions['DynCom\dc\common\interfaces\GenericCollectionInterface'] = new \Dice\Instance('ShopLanguageCollection');
$subscCustLinkRepoRule->shared = true;
$subscCustLinkRepoRule->constructParams = ['cacheAll' => false];
$IOCContainer->addRule('SubscriptionCustomerLinkRepository',$subscCustLinkRepoRule);


$subscHeaderRepositoryRule = $IOCContainer->getRule('SubscriptionHeaderRepository');
$subscHeaderRepositoryRule->substitutions['ModelDBConfigInterface'] = new \Dice\Instance('ShopLanguageConfig');
$subscHeaderRepositoryRule->substitutions['DynCom\dc\common\interfaces\GenericCollectionInterface'] = new \Dice\Instance('ShopLanguageCollection');
$subscHeaderRepositoryRule->shared = true;
$subscHeaderRepositoryRule->constructParams = ['cacheAll' => false];
$IOCContainer->addRule('SubscriptionHeaderRepository',$subscHeaderRepositoryRule);

$subscItemLinkRepositoryRule = $IOCContainer->getRule('SubscriptionItemLinkRepository');
$subscItemLinkRepositoryRule->substitutions['ModelDBConfigInterface'] = new \Dice\Instance('ShopLanguageConfig');
$subscItemLinkRepositoryRule->substitutions['DynCom\dc\common\interfaces\GenericCollectionInterface'] = new \Dice\Instance('ShopLanguageCollection');
$subscItemLinkRepositoryRule->shared = true;
$subscItemLinkRepositoryRule->constructParams = ['cacheAll' => false];
$IOCContainer->addRule('SubscriptionItemLinkRepository',$subscItemLinkRepositoryRule);

$subscriptionRepositoryRule = $IOCContainer->getRule('SubscriptionRepository');
$subscriptionRepositoryRule->substitutions['ModelDBConfigInterface'] = new \Dice\Instance('ShopLanguageConfig');
$subscriptionRepositoryRule->substitutions['DynCom\dc\common\interfaces\GenericCollectionInterface'] = new \Dice\Instance('ShopLanguageCollection');
$subscriptionRepositoryRule->shared = true;
$IOCContainer->addRule('SubscriptionRepository',$subscriptionRepositoryRule);

$subscrSeqStepRepoRule = $IOCContainer->getRule('SubscriptionSequenceStepRepository');
$subscrSeqStepRepoRule->substitutions['ModelDBConfigInterface'] = new \Dice\Instance('ShopLanguageConfig');
$subscrSeqStepRepoRule->substitutions['DynCom\dc\common\interfaces\GenericCollectionInterface'] = new \Dice\Instance('ShopLanguageCollection');
$subscrSeqStepRepoRule->substitutions['Shop'] = new \Dice\Instance('$CurrShop');
$subscrSeqStepRepoRule->shared = true;
$subscrSeqStepRepoRule->constructParams = ['cacheAll' => false];
$IOCContainer->addRule('SubscriptionSequenceStepRepository',$subscrSeqStepRepoRule);


/*$_SESSION['IOC'] = $IOCContainer;*/
$GLOBALS['IOC'] = $IOCContainer;