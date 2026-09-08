<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\traits\arrayMappableTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 10.07.2015
 * Time: 14:32
 */
class Address
{
    use arrayMappableTrait;

    protected $config;

    /**
     * Address constructor.
     */
    public function __construct() {
        $this->config = new AddressConfig();
    }

    public $salutation;
    public $name;
    public $name_2;
    public $contact;
    public $address;
    public $address_extra;
    public $address_street;
    public $address_no;
    public $post_code;
    public $city;
    public $country;
    public $state;
    public $phone_no;
    public $surname;
    public $lastname;
    public $company_name;


    /**
     * @param array $rules
     */
    public function setFieldValidationRules(array $rules) {
        $this->config = clone $this->config;
        $this->config->setFieldValidationRules($rules);
    }

    /**
     * @return array
     */
    public function getFieldValidationRules() {
        return $this->config->getFieldValidationRules();
    }

    /**
     * @param array $statusArr
     * @return bool
     */
    public function isValid(array $validationRules = null, array &$statusArr = []) {
        $validationRules = $validationRules ?? $this->config->getFieldValidationRules();
        $validator = new NewValidator($validationRules, $this->getAllFieldsAsArray());
        return $validator->isValid($statusArr);
    }

    /**
     * @param array $arr
     * @return Address
     */
    public static function fromArray(array $arr) {
        $newAddr = new Address();
        $newAddr->mapFromArray($arr);
        $statusArr = [];
        if(!$newAddr->isValid($statusArr)) {
            throw new \InvalidArgumentException('Data for Address is not valid: Field status: ' . print_r($statusArr,1));
        }
        return $newAddr;
    }

    /**
     * @return AddressConfig
     */
    public function getConfig(): AddressConfig
    {
        return $this->config;
    }

    public function withSalutation(string $salutation): Address
    {
        $arr = $this->getAllFieldsAsArray();
        $arr['salutation'] = $salutation;
        $newAddr = Address::fromArray($arr);
        return $newAddr;
    }


    public function withContact(string $contact): Address
    {
        $arr = $this->getAllFieldsAsArray();
        $arr['contact'] = $contact;
        $newAddr = Address::fromArray($arr);
        return $newAddr;
    }

    public function withAddressStreet(string $addressStreet): Address
    {
        $arr = $this->getAllFieldsAsArray();
        $arr['address_street'] = $addressStreet;
        $newAddr = Address::fromArray($arr);
        return $newAddr;
    }

    public function withAddressNo(string $addressNo): Address
    {
        $arr = $this->getAllFieldsAsArray();
        $arr['address_no'] = $addressNo;
        $newAddr = Address::fromArray($arr);
        return $newAddr;
    }

    public function withAddressExtra(string $addressExtra): Address
    {
        $arr = $this->getAllFieldsAsArray();
        $arr['address_extra'] = $addressExtra;
        $newAddr = Address::fromArray($arr);
        return $newAddr;
    }

    public function withCity(string $city): Address
    {
        $arr = $this->getAllFieldsAsArray();
        $arr['city'] = $city;
        $newAddr = Address::fromArray($arr);
        return $newAddr;
    }

    public function withPostCode(string $postCode): Address
    {
        $arr = $this->getAllFieldsAsArray();
        $arr['post_code'] = $postCode;
        $newAddr = Address::fromArray($arr);
        return $newAddr;
    }

    public function withCountry(string $country): Address
    {
        $arr = $this->getAllFieldsAsArray();
        $arr['country'] = $country;
        $newAddr = Address::fromArray($arr);
        return $newAddr;
    }

    public function withState(string $state): Address
    {
        $arr = $this->getAllFieldsAsArray();
        $arr['state'] = $state;
        $newAddr = Address::fromArray($arr);
        return $newAddr;
    }

    public function withPhoneNo(string $phoneNo): Address
    {
        $arr = $this->getAllFieldsAsArray();
        $arr['address_no'] = $phoneNo;
        $newAddr = Address::fromArray($arr);
        return $newAddr;
    }

    public function withSurname(string $surname): Address
    {
        $arr = $this->getAllFieldsAsArray();
        $arr['surname'] = $surname;
        $newAddr = Address::fromArray($arr);
        return $newAddr;
    }

    public function withLastName(string $lastName): Address
    {
        $arr = $this->getAllFieldsAsArray();
        $arr['lastname'] = $lastName;
        $newAddr = Address::fromArray($arr);
        return $newAddr;
    }

    public function withCompanyName(string $companyName): Address
    {
        $arr = $this->getAllFieldsAsArray();
        $arr['company_name'] = $companyName;
        $newAddr = Address::fromArray($arr);
        return $newAddr;
    }

    /**
     * @return string|null
     */
    public function getSalutation(): ?string
    {
        return $this->salutation;
    }

    /**
     * @return string|null
     */
    public function getContact(): ?string
    {
        return $this->contact;
    }

    /**
     * @return string|null
     */
    public function getAddressExtra(): ?string
    {
        return $this->address_extra;
    }

    /**
     * @return string|null
     */
    public function getAddressStreet(): ?string
    {
        return $this->address_street;
    }

    /**
     * @return string|null
     */
    public function getAddressNo(): ?string
    {
        return $this->address_no;
    }

    /**
     * @return string|null
     */
    public function getPostCode(): ?string
    {
        return $this->post_code;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @return string|null
     */
    public function getPhoneNo(): ?string
    {
        return $this->phone_no;
    }

    /**
     * @return string|null
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @return string|null
     */
    public function getCompanyName(): ?string
    {
        return $this->company_name;
    }



}
