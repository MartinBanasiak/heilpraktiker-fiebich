<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/2/2015
 * Time: 10:22 AM
 */

interface GenericObjectStorageInterface extends \Countable, \Iterator, \ArrayAccess
{
    /**
     * @param $object
     * @param null $data
     * @return mixed
     */
    public function attach($object, $data = null);

    /**
     * @param $object
     * @return mixed
     */
    public function detach($object);

    /**
     * @return mixed
     */
    public function clear();
}