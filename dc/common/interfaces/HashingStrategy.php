<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 05.01.2016
 * Time: 10:41
 */
interface HashingStrategy
{

    /**
     * @param $plaintext
     * @return mixed
     */
    public function getHash($plaintext);

    /**
     * @param $plaintext
     * @param $hashed
     * @return mixed
     */
    public function checkHash($plaintext, $hashed);

    /**
     * @param $currentHash
     * @return mixed
     */
    public function needsRehash($currentHash);

}