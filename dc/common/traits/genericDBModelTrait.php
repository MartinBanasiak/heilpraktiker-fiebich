<?php
namespace DynCom\dc\common\traits;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 19.01.2015
 * Time: 00:17
 */

trait genericDBModelTrait {

    use arrayMappableTrait;

    protected $config;

    /**
     * @param ModelDBConfigInterface $config
     */
    public function __construct( ModelDBConfigInterface $config ) {
        $this->config = $config;#TEST2
    }

    /**
     * @return Entity
     */
    public function getNullObject() {
        return new static($this->config);
    }

    /**
     * @return int|null
     */
    public function getID() {
        return $this->id;
    }

    /**
     * @return \DynCom\dc\common\interfaces\ModelDBConfigInterface
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * @return array
     */
    public function getAltPrimaryFieldNames() {
        return $this->config->getAltPrimary();
    }

    /**
     * @return array
     */
    public function getAltPrimaryFieldValues() {
        $arr = [];
        $names = $this->getAltPrimaryFieldNames();
        foreach($names as $name) {
            $arr[$name] = $this->$name;
        }
        return $arr;
    }

}