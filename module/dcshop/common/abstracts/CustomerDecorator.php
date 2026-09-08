<?php
namespace DynCom\dc\dcShop\abstracts;
use DynCom\dc\dcShop\classes\Customer;
use DynCom\dc\dcShop\interfaces\CustomerInterface;
use DynCom\dc\dcShop\traits\genericDecoratorTrait;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 12.10.2015
 * Time: 11:38
 */
abstract class CustomerDecorator implements CustomerInterface
{

    /**
     * @var Customer
     */
    protected $decoratedEntity;

    use genericDecoratorTrait {
        __construct as traitConstruct;
    }

    /**
     * @return string
     */
    protected function getDecoratedEntityClassName() {
        return 'DynCom\dc\dcShop\interfaces\CustomerInterface';
    }

    /**
     * CustomerDecorator constructor.
     * @param CustomerInterface $customer
     */
    public function __construct(CustomerInterface $customer) {
        $this->traitConstruct($customer);
    }

    /**
     * @return mixed
     */
    public function getCustomerNo()
    {
        return $this->decoratedEntity->getCustomerNo();
    }

    /**
     * @return mixed
     */
    public function getBillToCustomerNo()
    {
        return $this->decoratedEntity->getBillToCustomerNo();
    }

    /**
     * @return mixed
     */
    public function getCurrencyCode()
    {
        return $this->decoratedEntity->getCurrencyCode();
    }

    /**
     * @return mixed
     */
    public function getSalespersonCode()
    {
        return $this->decoratedEntity->getSalespersonCode();
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->decoratedEntity->isActive();
    }

    /**
     * @return mixed
     */
    public function getShopCode()
    {
        return $this->decoratedEntity->getShopCode();
    }

    /**
     * @return mixed
     */
    public function getLanguageCode()
    {
        return $this->decoratedEntity->getLanguageCode();
    }

    /**
     * @return \DynCom\dc\common\classes\EMail
     */
    public function getEmail()
    {
        return $this->decoratedEntity->getEmail();
    }

    /**
     * @return mixed
     */
    public function getCustomerPriceGroup()
    {
        return $this->decoratedEntity->getCustomerPriceGroup();
    }

    /**
     * @return mixed
     */
    public function getVatBusPostingGroup()
    {
        return $this->decoratedEntity->getVatBusPostingGroup();
    }

    /**
     * @return mixed
     */
    public function getInvoiceDiscCode()
    {
        return $this->decoratedEntity->getInvoiceDiscCode();
    }

    /**
     * @return mixed
     */
    public function getCustomerDiscGroup()
    {
        return $this->decoratedEntity->getCustomerDiscGroup();
    }

    /**
     * @return mixed
     */
    public function getPaymentTerms()
    {
        return $this->decoratedEntity->getPaymentTerms();
    }

    /**
     * @return mixed
     */
    public function getSalutation()
    {
        return $this->decoratedEntity->getSalutation();
    }

    /**
     * @return \DynCom\dc\common\classes\Address
     */
    public function getAddress()
    {
        return $this->decoratedEntity->getAddress();
    }

    /**
     * @return \DynCom\dc\common\classes\Address
     */
    public function getBillToAddress()
    {
        return $this->decoratedEntity->getBillToAddress();
    }


}