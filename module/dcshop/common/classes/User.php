<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Class User
 */
class User implements GenericDBModelInterface, Entity {

    use genericDBModelTrait, universallyGettableTrait;

    protected $id;
    protected $company;
    protected $shop_code;
    protected $customer_no;
    protected $shop_shipment_address_id;
    protected $name;
    protected $email;
    protected $login;
    protected $password;
    protected $main_user;
    protected $right_user_management;
    protected $right_order_history;
    protected $right_order;
    protected $right_return_order;
    protected $last_visitor_id;
    protected $to_delete;

    /**
     * @param UserConfig $config
     */
    public function __construct( UserConfig $config ) {
        $this->config = $config;
    }

    /**
     * @return User
     */
    public function getNullObject() {
        return new self($this->config);
    }

    /**
     * @return bool
     */
    public function hasReturnOrderPermission() {
        $this->right_return_order = ($this->right_order && $this->right_order_history);
        return $this->right_return_order;
    }

    /**
     * @return bool
     */
    public function isMainUser()
    {
        return (bool)$this->main_user;
    }

    /**
     * @return bool
     */
    public function hasRightUserManagement()
    {
        return (bool)$this->right_user_management;
    }

    /**
     * @return bool
     */
    public function hasRightOrder()
    {
        return (bool)$this->right_order;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

}