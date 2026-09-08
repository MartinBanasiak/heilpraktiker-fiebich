<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 28.06.2017
 * Time: 14:04
 */

namespace DynCom\dc\common\interfaces;


use DynCom\dc\common\classes\Address;

interface AddressValidator
{

    public function isAddressValid(string $address): bool;

}