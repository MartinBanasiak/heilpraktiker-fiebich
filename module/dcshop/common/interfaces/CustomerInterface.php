<?php
namespace DynCom\dc\dcShop\interfaces;
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 12.10.2015
 * Time: 11:42
 */
use DynCom\dc\common\classes\Address;
use DynCom\dc\common\classes\EMail;

/**
 * Class Customer
 */
interface CustomerInterface
{
    /**
     * @return string
     */
    public function getCustomerNo();

    /**
     * @return string
     */
    public function getBillToCustomerNo();

    /**
     * @return string
     */
    public function getCurrencyCode();

    /**
     * @return string
     */
    public function getSalespersonCode();

    /**
     * @return bool
     */
    public function isActive();

    /**
     * @return string
     */
    public function getShopCode();

    /**
     * @return string
     */
    public function getLanguageCode();

    /**
     * @return EMail
     */
    public function getEmail();

    /**
     * @return string
     */
    public function getCustomerPriceGroup();

    /**
     * @return string
     */
    public function getVatBusPostingGroup();

    /**
     * @return string
     */
    public function getInvoiceDiscCode();

    /**
     * @return string
     */
    public function getCustomerDiscGroup();

    /**
     * @return string
     */
    public function getPaymentTerms();

    /**
     * @return string
     */
    public function getSalutation();

    /**
     * @return Address
     */
    public function getAddress();

    /**
     * @return Address
     */
    public function getBillToAddress();
}