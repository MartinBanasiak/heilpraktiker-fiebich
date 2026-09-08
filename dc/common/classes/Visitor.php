<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;
use DynCom\dc\dcShop\classes\Customer;
use DynCom\dc\dcShop\classes\Shop;
use DynCom\dc\dcShop\classes\ShopLanguage;
use DynCom\dc\dcShop\classes\User;

/**
 * Class Visitor
 */
class Visitor implements GenericDBModelInterface, Entity
{

    use genericDBModelTrait, universallyGettableTrait;

    protected $id;
    protected $session_id;
    protected $last_ipv4_anon;
    protected $last_ipv6_anon;
    protected $session_date;
    protected $valid_until;
    protected $frontend_login;
    protected $cookie_only;
    protected $main_user_id;
    protected $shop_salesperson_id;
    protected $admin_login;
    protected $main_admin_user_id;
    protected $currency_code;
    protected $data;
    protected $remember_token;
    protected $nav_login;
    protected $serialized_objects;

    /**
     * @param mixed $serialized_objects
     */
    public function setSerializedObjects($serialized_objects)
    {
        $this->serialized_objects = $serialized_objects;
    }

    /**
     * Visitor constructor.
     * @param VisitorConfig $config
     */
    public function __construct(VisitorConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return Visitor
     */
    public function getNullObject()
    {
        return new self($this->config);
    }

    protected $user;

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        if (!((int)$user->last_visitor_id === (int)$this->id)) {
            throw new \InvalidArgumentException('User\'s last visitor id must match id of visitor.');
        }
        $this->user = $user;
        $this->updateHooks('changed',$this);
    }

    protected $customer;

    /**
     * @param Customer $customer
     */
    public function setCustomer(Customer $customer)
    {
        if (!isset($this->user) || ($this->user->customer_no !== $customer->customer_no)) {
            throw new \InvalidArgumentException('User needs to be set at visitor and user\'s customer no must match customer\'s customer no.');

        }
        $this->customer = $customer;
        $this->updateHooks('changed',$this);
    }

    protected $site;

    /**
     * @param Site $site
     */
    public function setSite(Site $site)
    {
        $this->site = $site;
        $this->updateHooks('changed',$this);
    }

    protected $language;

    /**
     * @param Language $language
     */
    public function setLanguage(Language $language)
    {
        $this->language = $language;
        $this->updateHooks('changed',$this);
    }

    protected $shop;

    /**
     * @param Shop $shop
     */
    public function setShop(Shop $shop)
    {
        $this->shop = $shop;
        $this->updateHooks('changed',$this);
    }

    protected $shopLanguage;

    /**
     * @param ShopLanguage $shopLanguage
     */
    public function setShopLanguage(ShopLanguage $shopLanguage)
    {
        $this->shopLanguage = $shopLanguage;
        $this->updateHooks('changed',$this);
    }
}