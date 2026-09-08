<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Class SubscriptionHeader
 */
class SubscriptionHeader implements GenericDBModelInterface, Entity
{
    use genericDBModelTrait, universallyGettableTrait;

    const SUBSCRIPTION_TYPE_ORDER_OPTION = 0;
    const SUBSCRIPTION_TYPE_SEQUENCE_ITEM = 1;

    const SUBSCRIPTION_START_TYPE_ANEW = 0;
    const SUBSCRIPTION_START_TYPE_JOIN = 1;

    const SUBSCRIPTION_PAY_TYPE_ONCE = 0;
    const SUBSCRIPTION_PAY_TYPE_PER_TURN = 1;
    const SUBSCRIPTION_PAY_TYPE_MONTHLY = 2;
    const SUBSCRIPTION_PAY_TYPE_QUARTERLY = 3;
    const SUBSCRIPTION_PAY_TYPE_SEMI_ANNUALLY = 4;
    const SUBSCRIPTION_PAY_TYPE_ANNUALLY = 5;



    protected $id;
    protected $company;
    protected $code;
    protected $type;
    protected $description;
    protected $item_no_subscription_item;
    protected $payment_type;
    protected $payment_type_description;
    protected $start_type;
    protected $start_date_formula;
    protected $turn_interval_formula;
    protected $turn_interval_description;
    protected $min_no_of_turns;
    protected $max_no_of_turns;
    protected $subscription_duration;
    protected $no_of_sequence_steps;
    protected $no_of_items;
    protected $no_of_customers;
    protected $active;
    protected $valid_from;
    protected $valid_to;
    protected $cancel_period_date_formula;
    protected $cancel_period_description;
    protected $price_per_billing_interval;
    protected $orderable;
    protected $orderable_from;
    protected $orderable_to;
    protected $shop_code;
    protected $language_code;
    protected $all_shops;
    protected $all_languages;
    protected $first_order_email_text;
    protected $shipment_email_text;
    protected $last_ship_email_text;
    protected $shipping_option_line_no;
    protected $ref_pay_date_from_start_date;
    protected $allow_late_entry;
    protected $to_delete;

    /**
     * @param SubscriptionHeaderConfig $config
     */
    public function __construct(SubscriptionHeaderConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return SubscriptionHeader
     */
    public function getNullObject()
    {
        return new self($this->config);
    }

    /**
     * @return bool
     */
    public function typeIsOrderOption()
    {
        return ((int)$this->type === self::SUBSCRIPTION_TYPE_ORDER_OPTION);
    }

    /**
     * @return bool
     */
    public function typeIsSequenceItem()
    {
        return ((int)$this->type === self::SUBSCRIPTION_TYPE_SEQUENCE_ITEM);
    }

    /**
     * @return bool
     */
    public function startTypeIsBeginAnew() {
        return ((int)$this->start_type === self::SUBSCRIPTION_START_TYPE_ANEW);
    }

    /**
     * @return bool
     */
    public function startTypeIsJoin() {
        return ((int)$this->start_type === self::SUBSCRIPTION_START_TYPE_JOIN);
    }

    /**
     * @return bool
     */
    public function isPayTypeSinglePayment() {
        return ((int)$this->payment_type) === self::SUBSCRIPTION_PAY_TYPE_ONCE;
    }

    /**
     * @return bool
     */
    public function isPayTypePerTurn() {
        return ((int)$this->payment_type) === self::SUBSCRIPTION_PAY_TYPE_PER_TURN;
    }

    /**
     * @return bool|string
     */
    public function getPayTypeDateFormulaString() {
        switch((int)$this->payment_type) {
            case self::SUBSCRIPTION_PAY_TYPE_ONCE :
                return false;
                break;
            case self::SUBSCRIPTION_PAY_TYPE_PER_TURN :
                return false;
            break;
            case self::SUBSCRIPTION_PAY_TYPE_MONTHLY :
                return '+1M';
                break;
            case self::SUBSCRIPTION_PAY_TYPE_QUARTERLY :
                return '+3M';
                break;
            case self::SUBSCRIPTION_PAY_TYPE_SEMI_ANNUALLY :
                return '+6M';
                break;
            case self::SUBSCRIPTION_PAY_TYPE_ANNUALLY :
                return '+12M';
                break;
            default :
                return false;
                break;
        }
    }

}