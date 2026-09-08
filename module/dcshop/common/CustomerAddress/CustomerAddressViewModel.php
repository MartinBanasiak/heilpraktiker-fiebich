<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 01.12.2017
 * Time: 06:53
 */

namespace DynCom\dc\dcShop\common\CustomerAddress;


use DynCom\dc\common\classes\SelectionCriteriaHelper;
use DynCom\dc\dcShop\CustomerAddress\CustomerAddress;
use DynCom\dc\dcShop\CustomerAddress\CustomerAddressCollection;
use DynCom\dc\dcShop\CustomerAddress\CustomerAddressConfig;
use DynCom\dc\regionalization\RegionalizedTextProvider;

class CustomerAddressViewModel
{
    protected const TEXT_KEY_NAME_LABEL = 'address_name';
    protected const TEXT_KEY_NAME_2_LABEL = 'address_name_2';
    protected const TEXT_KEY_ADDRESS_LABEL = 'address_address';
    protected const TEXT_KEY_ADDRESS_2_LABEL = 'address_address_2';
    protected const TEXT_KEY_POST_CODE_LABEL = 'address_post_code';
    protected const TEXT_KEY_CITY_LABEL = 'address_city';
    protected const TEXT_KEY_COUNTRY_LABEL = 'address_country';

    public $customerAddress;
    public $customerAddressCollection;
    public $nameLabel;
    public $name2Label;
    public $addressLabel;
    public $address2Label;
    public $postCodeLabel;
    public $cityLabel;
    public $countryLabel;
    public $codeLabel;
    public $action;

    public $fieldInputOptions = [
        'code' => ['required' => false, 'minlength' => 0, 'maxlength' => 10, 'type' => 'text'],
        'name' => ['required' => true, 'minlength' => 4, 'maxlength' => 50, 'type' => 'text'],
        'name_2' => ['required' => false, 'minlength' => 4, 'maxlength' => 50, 'type' => 'text'],
        'address' => ['required' => true, 'minlength' => 4, 'maxlength' => 50, 'type' => 'text'],
        'address_2' => ['required' => false, 'minlength' => 4, 'maxlength' => 50, 'type' => 'text'],
        'post_code' => ['required' => true, 'minlength' => 3, 'maxlength' => 20, 'type' => 'text'],
        'city' => ['required' => true, 'minlength' => 3, 'maxlength' => 50, 'type' => 'text'],
        'country' => ['required' => true, 'minlength' => 3, 'maxlength' => 20, 'type' => 'text'],
    ];


    public function __construct(
        RegionalizedTextProvider $textProvider
    )
    {
        $config = new CustomerAddressConfig();
        $customerAddress = new CustomerAddress($config);
        $customerAddressCollection = new CustomerAddressCollection($config, new SelectionCriteriaHelper());
        $this->customerAddress = $customerAddress;
        $this->customerAddressCollection = $customerAddressCollection;
        $this->setLabels($textProvider);
    }

    protected function setLabels(RegionalizedTextProvider $textProvider)
    {
        $this->nameLabel = $textProvider->getRegionalizedText(self::TEXT_KEY_NAME_LABEL);
        $this->name2Label = $textProvider->getRegionalizedText(self::TEXT_KEY_NAME_2_LABEL);
        $this->addressLabel = $textProvider->getRegionalizedText(self::TEXT_KEY_ADDRESS_LABEL);
        $this->address2Label = $textProvider->getRegionalizedText(self::TEXT_KEY_ADDRESS_2_LABEL);
        $this->postCodeLabel = $textProvider->getRegionalizedText(self::TEXT_KEY_POST_CODE_LABEL);
        $this->cityLabel = $textProvider->getRegionalizedText(self::TEXT_KEY_CITY_LABEL);
        $this->countryLabel = $textProvider->getRegionalizedText(self::TEXT_KEY_COUNTRY_LABEL);
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return $this->customerAddress->$name;
    }

    public function __set($name, $value)
    {
        //Do nothing - CustomerAddress will not be overwritten in viewmodel
    }

    public function __isset($name)
    {
        return isset($this->$name) || isset($this->customerAddress->$name);
    }


}