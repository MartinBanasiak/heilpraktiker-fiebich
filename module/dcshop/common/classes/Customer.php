<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\Address;
use DynCom\dc\common\classes\EMail;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;
use DynCom\dc\dcShop\interfaces\CustomerInterface;

/**
 * Class Customer
 */
class Customer implements GenericDBModelInterface, Entity, CustomerInterface
{

    use genericDBModelTrait, universallyGettableTrait;

    protected $id;
    protected $company;
    protected $shop_code;
    protected $language_code;
    protected $customer_no;
    protected $name;
    protected $name_2;
    protected $address;
    protected $address_2;
    protected $address_street;
    protected $address_no;
    protected $post_code;
    protected $city;
    protected $country;
    protected $phone_no;
    protected $fax_no;
    protected $email;
    protected $homepage;
    protected $salesperson_code;
    protected $active;
    protected $currency_code;
    protected $bill_to_customer_no;
    protected $bill_to_name;
    protected $bill_to_name_2;
    protected $bill_to_address;
    protected $bill_to_address_2;
    protected $bill_to_post_code;
    protected $bill_to_city;
    protected $bill_to_country;
    protected $customer_price_group;
    protected $vat_bus_posting_group;
    protected $invoice_disc_code;
    protected $customer_disc_group;
    protected $payment_terms;
    protected $surname;
    protected $lastname;
    protected $company_name;
    protected $salutation;
    protected $to_delete;

    /**
     * Customer constructor.
     * @param CustomerConfig $config
     */
    public function __construct( CustomerConfig $config )
    {
        $this->config = $config;
    }

    /**
     * @return Customer
     */
    public function getNullObject()
    {
        return new self($this->config);
    }

    /**
     * @return mixed
     */
    public function getCustomerNo()
    {
        return $this->customer_no;
    }

    /**
     * @return mixed
     */
    public function getBillToCustomerNo()
    {
        return $this->bill_to_customer_no;
    }

    /**
     * @return mixed
     */
    public function getCurrencyCode()
    {
        return $this->currency_code;
    }

    /**
     * @return mixed
     */
    public function getSalespersonCode()
    {
        return $this->salesperson_code;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return (bool)$this->active;
    }

    /**
     * @return mixed
     */
    public function getShopCode()
    {
        return $this->shop_code;
    }

    /**
     * @return mixed
     */
    public function getLanguageCode()
    {
        return $this->language_code;
    }

    /**
     * @return EMail
     */
    public function getEmail()
    {
        return new EMail($this->email);
    }

    /**
     * @return mixed
     */
    public function getCustomerPriceGroup()
    {
        return $this->customer_price_group;
    }

    /**
     * @return mixed
     */
    public function getVatBusPostingGroup()
    {
        return $this->vat_bus_posting_group;
    }

    /**
     * @return mixed
     */
    public function getInvoiceDiscCode()
    {
        return $this->invoice_disc_code;
    }

    /**
     * @return mixed
     */
    public function getCustomerDiscGroup()
    {
        return $this->customer_disc_group;
    }

    /**
     * @return mixed
     */
    public function getPaymentTerms()
    {
        return $this->payment_terms;
    }

    /**
     * @return mixed
     */
    public function getSalutation()
    {
        return $this->salutation;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        $arr = [
            'name' => $this->name,
            'name_2' => $this->name_2,
            'address' => $this->address,
            'address_2' => $this->address_2,
            'address_street' => $this->address_street,
            'address_no' => $this->address_no,
            'surname' => $this->surname,
            'lastname' => $this->lastname,
            'post_code' => $this->post_code,
            'country' => $this->country,
            'company_name' => $this->company_name
        ];
        return Address::fromArray($arr);
    }

    /**
     * @return Address
     */
    public function getBillToAddress()
    {
        $arr = [
            'name' => $this->bill_to_name,
            'name_2' => $this->bill_to_name_2,
            'address' => $this->bill_to_address,
            'address_2' => $this->bill_to_address_2,
            'post_code' => $this->bill_to_post_code,
            'country' => $this->bill_to_country,
            'company_name' => $this->company_name
        ];
        return Address::fromArray($arr);
    }

    protected $fieldValidationData = [
        'name'              =>  'name|required|maxlen:50',
        'name_2'            =>  'name|maxlen:50',
        'address'           =>  'addrstreetplusno|required|maxlen:50',
        'address_2'         =>  'addrstreetplusno|maxlen:50',
        'address_street'    =>  'addrstreet|maxlen:50',
        'address_no'        =>  'addrstreetno|maxlen:10',
        'post_code'         =>  'required|addrzip|maxlen:20',
        'city'              =>  'required|addrcity|maxlen:50',
        'country'           =>  'required|maxlen:10',
        'bill_to_name'      =>  'name|required|maxlen:50',
        'bill_to_name_2'    =>  'name|maxlen:50',
        'bill_to_address'   =>  'addrstreetplusno|required|maxlen:50',
        'bill_to_address_2' =>  'addrstreetplusno|maxlen:50',
        'bill_to_post_code' =>  'required|addrzip|maxlen:20',
        'bill_to_city'      =>  'required|addrcity|maxlen:50',
        'bill_to_country'   =>  'required|maxlen:10',
        'phone_no'          =>  'maxlen:80',
        'surname'           =>  'name|maxlen:30',
        'lastname'          =>  'name|maxlen:30',
        'company_name'      =>  'maxlen:45'
    ];

    /**
     * @return array
     */
    public function getFieldValidationData()
    {
        return $this->fieldValidationData;
    }

}
