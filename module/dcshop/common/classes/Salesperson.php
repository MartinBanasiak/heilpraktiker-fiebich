<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Class Salesperson
 */
class Salesperson extends User implements GenericDBModelInterface, Entity {

    use genericDBModelTrait, universallyGettableTrait;

	protected $language_code;
	protected $salesperson_code;
	protected $phone_no;
	protected $max_discount;
	protected $user;

    /**
     * @param SalespersonConfig|UserConfig $config
     */
    public function __construct( SalespersonConfig $config ) {
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
        return true;
    }

    /**
     * @param $customerNo
     */
    public function setCustomerNo($customerNo)
	{
		$this->customer_no = (string)$customerNo;
	}

}