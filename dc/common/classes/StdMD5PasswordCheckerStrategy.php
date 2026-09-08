<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\PasswordCheckerInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 13.01.2015
 * Time: 13:56
 */
class StdMD5PasswordCheckerStrategy implements PasswordCheckerInterface {

    /**
     * @param $storedPass
     * @param $inputPass
     *
     * @return bool
     */
    public function isValid( $storedPass, $inputPass ) {
        return $this->_isValid($storedPass, $inputPass);
    }

    /**
     * @param $storedPass
     * @param $inputPass
     *
     * @return bool
     */
    protected function _isValid( $storedPass, $inputPass ) {
        $stored = trim((string)$storedPass);
        $input  = trim((string)$inputPass);
        return (strlen($stored) > 0 && strlen($input) > 0 && $input === md5($stored));
    }

}