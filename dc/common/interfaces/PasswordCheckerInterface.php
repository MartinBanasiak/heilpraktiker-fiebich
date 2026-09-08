<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 13.01.2015
 * Time: 14:00
 */
interface PasswordCheckerInterface {

    /**
     * @param $storedPass
     * @param $inputPass
     * @return mixed
     */
    public function isValid($storedPass, $inputPass );
}