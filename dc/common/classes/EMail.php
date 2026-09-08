<?php
namespace DynCom\dc\common\classes;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.07.2015
 * Time: 12:46
 */
class EMail
{

    protected $email;

    /**
     * EMail constructor.
     * @param $email
     */
    public function __construct($email) {
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(
              "A valid eMail must be passed to the constructor."
            );
        }
        $this->email = $email;
    }

    public function getAddress() {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getDomain() {
        return explode('@',$this->email)[1];
    }

}