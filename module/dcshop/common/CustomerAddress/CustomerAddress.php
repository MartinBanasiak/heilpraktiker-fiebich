<?php

namespace DynCom\dc\dcShop\CustomerAddress;

use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Class CustomerAddress
 */
class CustomerAddress implements GenericDBModelInterface, Entity
{

    use genericDBModelTrait, universallyGettableTrait;

    protected $id;
    protected $company;
    protected $customer_no;
    protected $code;
    protected $name;
    protected $name_2;
    protected $address;
    protected $address_2;
    protected $post_code;
    protected $city;
    protected $country;

    /**
     * @param CustomerAddressConfig $config
     */
    public function __construct(CustomerAddressConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return CustomerAddress
     */
    public function getNullObject()
    {
        return new self($this->config);
    }

    /**
     * @return array
     */
    public static function getValidationRules(): array
    {
        static $arr = [
            'id' => 'digit|maxlen:11',
            'company' => 'required|text|minlen:3|maxlen:10',
            'customer_no' => 'required|text|minlen:3|maxlen:10',
            'code' => 'alnum|required|minlen:3|maxlen:10',
            'name' => 'name|required|minlen:4|maxlen:50',
            'name_2' => 'name|minlen:4|maxlen:50',
            'address' => 'text|required|minlen:4|maxlen:50',
            'address_2' => 'text|minlen:4|maxlen:50',
            'post_code' => 'alnum|required|minlen:4|maxlen:10',
            'city' => 'text|required|minlen:4|maxlen:20',
            'country' => 'text|required|minlen:4|maxlen:20',
        ];
        return $arr;
    }

}