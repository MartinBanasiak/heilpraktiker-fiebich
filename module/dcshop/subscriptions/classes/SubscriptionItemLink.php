<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Class SubscriptionItemLink
 */
class SubscriptionItemLink implements GenericDBModelInterface, Entity
{

    use genericDBModelTrait, universallyGettableTrait;

      protected $id;
      protected $company;
      protected $subscription_code;
      protected $link_to;
      protected $item_no;
      protected $variant_code;
      protected $subscr_seq_step_line_no;
      protected $description;
      protected $fix_price;
      protected $discount_percent;
      protected $min_quantity;
      protected $max_quantity;
      protected $quantity;
      protected $price_includes_vat;
      protected $to_delete;

    /**
     * @param SubscriptionItemLinkConfig $config
     */
    public function __construct( SubscriptionItemLinkConfig $config ) {
        $this->config = $config;
    }

    /**
     * @return SubscriptionItemLink
     */
    public function getNullObject() {
        return new self($this->config);
    }

}