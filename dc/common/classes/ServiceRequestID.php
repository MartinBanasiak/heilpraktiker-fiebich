<?php
namespace DynCom\dc\common\classes;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 17.08.2015
 * Time: 11:08
 */
class ServiceRequestID
{

    protected $id;

    /**
     * ServiceRequestID constructor.
     * @param string $prefix
     */
    public function __construct($prefix = '') {
        $this->id = uniqid($prefix,true);
    }

    /**
     * @return string
     */
    public function getID() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id;
    }
    
    public function __clone()
    {
        throw new \BadMethodCallException('Cloning a ServiceRequestID is forbidden');
    }

}