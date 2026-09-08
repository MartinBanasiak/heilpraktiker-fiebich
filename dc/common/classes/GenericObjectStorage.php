<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\GenericObjectStorageInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/2/2015
 * Time: 10:21 AM
 */

class GenericObjectStorage extends \SplObjectStorage implements GenericObjectStorageInterface {

    public function clear() {
        $tempStorage = clone $this;
        $this->addAll($tempStorage);
        $this->removeAll($tempStorage);
        $tempStorage = null;
    }

}