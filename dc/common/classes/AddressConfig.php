<?php
namespace DynCom\dc\common\classes;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 11.07.2015
 * Time: 00:03
 */
class AddressConfig
{

    protected $modelName = 'Address';
    protected $fieldValidationData = [
        'name' => 'name|required|maxlen:50',
        'name_2' => 'name|maxlen:50',
        'contact' => 'maxlen:50',
        'address' => 'addrstreetplusno|required|maxlen:50',
        'address_2' => 'addrstreetplusno|maxlen:50',
        'address_street' => 'addrstreet|maxlen:50',
        'address_no' => 'addrstreetno|maxlen:10',
        'post_code' => 'required|addrzip|maxlen:20',
        'city' => 'required|addrcity|maxlen:50',
        'country' => 'required|maxlen:10',
        'phone_no' => 'maxlen:80',
        'state' => 'alpha|maxlen:30',
        'surname' => 'name|maxlen:30',
        'lastname' => 'name|maxlen:30',
        'company_name' => 'maxlen:45',
    ];

    /**
     * @return array
     */
    public function getFieldValidationRules() {
        return $this->fieldValidationData;
    }

    /**
     * @param array $rules
     */
    public function setFieldValidationRules(array $rules) {
        $this->fieldValidationData = $rules;
    }

}