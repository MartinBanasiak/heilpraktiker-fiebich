<?php
namespace DynCom\dc\dcShop\classes;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.07.2015
 * Time: 14:58
 */
class OrderPayData
{

    public $payID;
    public $paymentTransactionID;
    public $bankAccountNo;
    public $bankBranchNo;
    public $bankName;
    public $paymentProcessed;

    public $billPayAccountOwner;
    public $billPayAccountNumber;
    public $billPayAccountIBAN;
    public $billPayBank;
    public $billPayInvoiceRef;
    public $billPayCreditWorthinessCheckResult;
    public $sepaGranted;

    public $sucessful;

}