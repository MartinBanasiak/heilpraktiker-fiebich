<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\ViewModel;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/16/2015
 * Time: 11:45 PM
 */
class GenericViewModel implements ViewModel
{
    protected $data = [];

    /**
     * @param $name
     * @param $value
     */
    public function setData($name, $value) {
        $this->data[strip_tags($name)] = $value;
    }

    /**
     * @param $name
     */
    public function unsetData($name) {
        if(isset($this->data[$name])) {
            unset($this->data[$name]);
        }
    }

    /**
     * @return array
     */
    public function getData() {
        return $this->data;
    }

}