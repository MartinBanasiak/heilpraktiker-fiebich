<?php
namespace DynCom\dc\dcShop\rma;
if(!isset($IOCContainer)) {
    $IOCContainer = unserialize($_SESSION['IOC']);
}

$retShipLineRepoRule = clone $IOCContainer->getRule('RetShipmentLineRepository');
$retShipLineRepoRule->shared = true;
$retShipLineRepoRule->constructParams = ['cacheAll' => false];
$IOCContainer->addRule('RetShipmentLineRepository',$retShipLineRepoRule);

$retShipRepoRule = clone $IOCContainer->getRule('RetShipmentLineRepository');
$retShipRepoRule->shared = true;
$retShipRepoRule->constructParams = ['cacheAll' => false];
$IOCContainer->addRule('RetShipmentRepository',$retShipLineRepoRule);

$retReasonRepoRule = clone $IOCContainer->getRule('ReturnReasonRepository');
$retReasonRepoRule->shared = true;
$retShipLineRepoRule->constructParams = ['cacheAll' => false];
$IOCContainer->addRule('ReturnReasonRepository',$retReasonRepoRule);

$rmaOrderHelperRule = clone $IOCContainer->getRule('*');
$rmaOrderHelperRule->substitutions['CurrShopConfiguration'] = new \Dice\Instance('$CurrShopConfig');
$IOCContainer->addRule('RMAOrderHelper',$rmaOrderHelperRule);
$GLOBALS['IOC'] = $IOCContainer;