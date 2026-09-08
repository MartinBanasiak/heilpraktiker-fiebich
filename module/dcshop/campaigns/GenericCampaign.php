<?php
namespace DynCom\dc\dcShop\campaigns;
use DynCom\dc\common\classes\SelectionCriteriaHelper;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;
use DynCom\dc\dcShop\classes\ShopLanguage;
use DynCom\dc\dcShop\classes\TextModuleRepository;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 05.10.2015
 * Time: 14:30
 */
class GenericCampaign implements GenericDBModelInterface,Entity
{

    use genericDBModelTrait, universallyGettableTrait;

    const UPLOADDIR_IMAGES                      = '/userdata/dcshop/campaign_images/';

    const CONDITION_TYPE_SUM_AMNT_ITEMS         = 'CONDITION_TYPE_SUM_AMNT_ITEMS';
    const CONDITION_TYPE_AMNT_SINGLE_ITEM       = 'CONDITION_TYPE_AMNT_SINGLE_ITEM';
    const CONDITION_TYPE_NO_DIFF_ITEMS          = 'CONDITION_TYPE_NO_DIFF_ITEMS';
    const CONDITION_TYPE_SUM_QTY_ITEMS          = 'CONDITION_TYPE_SUM_QTY_ITEMS';
    const CONDITION_TYPE_QTY_SINGLE_ITEM        = 'CONDITION_TYPE_QTY_SINGLE_ITEM';
    const CONDITION_TYPE_SUM_AMNT_BASKET        = 'CONDITION_TYPE_SUM_AMNT_BASKET';
    const CONDITION_TYPE_SUM_AMNT_EACH_ITEM     = 'CONDITION_TYPE_SUM_AMNT_EACH_ITEM';
    const CONDITION_TYPE_QTY_EACH_ITEM          = 'CONDITION_TYPE_QTY_EACH_ITEM';

    const ACTION_TYPE_DISCOUNT_AMOUNT           = 'ACTION_TYPE_DISCOUNT_AMOUNT';
    const ACTION_TYPE_DISCOUNT_PERCENT          = 'ACTION_TYPE_DISCOUNT_PERCENT';
    const ACTION_TYPE_FREE_ITEMS                = 'ACTION_TYPE_FREE_ITEMS';
    const ACTION_TYPE_SPECIAL_SHIPPING          = 'ACTION_TYPE_SPECIAL_SHIPPING';

    const DISC_AMNT_TYPE_CURR_COND_ITEM         = 1;
    const DISC_AMNT_TYPE_ALL_COND_ITEMS         = 2;
    const DISC_AMNT_TYPE_ALL_ACTION_ITEMS       = 3;
    const DISC_AMNT_TYPE_BASKET                 = 4;

    const DISC_PERC_TYPE_CURR_COND_ITEM         = 1;
    const DISC_PERC_TYPE_ALL_COND_ITEMS         = 2;
    const DISC_PERC_TYPE_LEAST_EXP_COND_ITEM    = 3;
    const DISC_PERC_TYPE_ALL_ACTION_ITEMS       = 4;
    const DISC_PERC_TYPE_BASKET                 = 5;

    const FREE_ITEM_TYPE_CURR_COND_ITEM         = 1;
    const FREE_ITEM_TYPE_LEAST_EXP_COND_ITEM    = 2;
    const FREE_ITEM_TYPE_ALL_ACT_ITEM           = 3;

    private $headerConfig;

    private $id;
    private $company;
    private $shop_code;
    private $language_code;
    private $all_shops;
    private $all_languages;
    private $code;
    private $description;
    private $active;
    private $active_from_date;
    private $active_to_date;
    private $active_from_time;
    private $active_to_time;
    private $multiply_applicable;
    private $priority;
    private $cond_sum_amnt_all_items_gte;
    private $cond_sum_amnt_sing_item_gte;
    private $cond_sum_amnt_each_item_gte;
    private $cond_no_diff_items_gte;
    private $cond_total_item_qty_gte;
    private $cond_qty_single_item_gte;
    private $cond_qty_each_item_gte;
    private $cond_basket_total_gte;
    private $discount_amnt;
    private $discount_amnt_applies_to;
    private $discount_percent;
    private $discount_percent_applies_to;
    private $discount_applies_to_qty_max;
    private $qty_free_item;
    private $type_free_item;
    private $alternative_shipping_option;
    private $icon_condition_items;
    private $banner_condition_items;
    private $text_condition_items;
    private $icon_action_items;
    private $banner_action_items;
    private $text_action_items;
    private $banner_basket;
    private $text_basket;
    private $to_delete;

    private $conditionElements;
    private $actionElements;

    public function __construct(
        GenericCampaignHeaderConfig $config,
        GenericCampaignElementCollection $conditionElements,
        GenericCampaignElementCollection $actionElements
    )
    {
        $this->headerConfig = $config;
        $this->conditionElements = $conditionElements;
        $this->actionElements = $actionElements;
    }

    public function getNullObject()
    {
        $el = new GenericCampaignElementCollection(new GenericCampaignElementConfig(), new SelectionCriteriaHelper());
        return new self($this->headerConfig, $el, $el);
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getShopCode()
    {
        return $this->shop_code;
    }

    /**
     * @return string
     */
    public function getShopLanguageCode()
    {
        return $this->language_code;
    }


    public function isActive()
    {
        return (bool)$this->active;
    }

    public function isCurrentlyActive()
    {
        if (!$this->active) {
            return false;
        }
        $currDT = new \DateTime();
        return $this->isActiveAtDateTime($currDT);
    }

    public function isActiveAtDateTime(\DateTime $dateTime)
    {
        if (!$this->active) {
            return false;
        }
        $startDate = null;
        $endDate = null;
        $currDT = new \DateTime();
        $currDateStr = $currDT->format('Y-m-d');

        if (!empty($this->active_from_date)) {
            $startDate = new \DateTime();
            $startDate = $startDate->createFromFormat('Y-m-d', $this->active_from_date);
            if ($dateTime->getTimestamp() < $startDate->getTimestamp()) {
                return false;
            }
        }
        if (!empty($this->active_to_date)) {
            $endDate = new \DateTime();
            $endDate = $endDate->createFromFormat('Y-m-d', $this->active_to_date);
            if ($dateTime->getTimestamp() > $endDate->getTimestamp()) {
                return false;
            }
        }
        if (!empty($this->active_from_time) && !($this->active_from_time === '00:00:00')) {
            $startDateTimeString = $currDateStr . ' ' . $this->active_from_time;
            $startDateTime = new \DateTime();
            $startDateTime = $startDateTime->createFromFormat('Y-m-d H:i:s', $startDateTimeString);
            if ($dateTime->format('H:i:s') < $startDateTime->format('H:i:s')) {
                return false;
            }
        }
        if (!empty($this->active_to_time) && !($this->active_to_time === '00:00:00')) {
            $endDateTimeString = $currDateStr . ' ' . $this->active_to_time;
            $endDateTime = new \DateTime();
            $endDateTime = $endDateTime->createFromFormat('Y-m-d H:i:s', $endDateTimeString);
            if ($dateTime->format('H:i:s') > $endDateTime->format('H:i:s')) {
                return false;
            }
        }
        return true;
    }


    public function isValidForAllShops()
    {
        return (bool)$this->all_shops;
    }

    public function isValidForAllShopLanguages()
    {
        return (bool)$this->all_languages;
    }

    public function isValidForShopLanguage(ShopLanguage $shopLanguage)
    {
        $valid = (
            ($this->isValidForAllShops() && $this->isValidForAllShopLanguages()) ||
            (
                ($this->getShopCode() === $shopLanguage->shop_code) &&
                ($this->getShopLanguageCode() === $shopLanguage->code)
            ));
        return $valid;
    }

    public function getActiveFromDate()
    {
        $dt = \DateTime::createFromFormat('Y-m-d',$this->active_from_date);
        return $dt;
    }

    /**
     * @return bool|\DateTime
     */
    public function getActiveToDate()
    {
        $dt = \DateTime::createFromFormat('Y-m-d',$this->active_to_date);
        return $dt;
    }

    /**
     * @return bool|\DateTime
     */
    public function getActiveFromTime()
    {
        if(!empty($this->active_from_time) && $this->active_from_time !== '00:00:00') {
            $time = \DateTime::createFromFormat('H:i:s', $this->active_from_time);
            return $time;
        } else {
            $time = new \DateTime();
            $time->setTimestamp(strtotime('today'));
            return $time;
        }
    }

    /**
     * @return bool|\DateTime
     */
    public function getActiveToTime()
    {
        if(!empty($this->active_to_time) && $this->active_to_time !== '00:00:00') {
            $time = \DateTime::createFromFormat('H:i:s', $this->active_to_time);
            return $time;
        } else {
            $time = new \DateTime();
            $time->setTimestamp(strtotime('tomorrow'));
            return $time;
        }
    }

    /**
     * @return bool
     */
    public function isMultiplyApplicable()
    {
        return (bool)$this->multiply_applicable;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return (int)$this->priority;
    }

    /**
     * @return GenericCampaignElementCollection
     */
    public function getActionElements()
    {
        return $this->actionElements;
    }

    /**
     * @return GenericCampaignElementCollection
     */
    public function getConditionElements()
    {
        return $this->conditionElements;
    }

    /**
     * @return bool
     */
    public function getToDelete()
    {
        return (bool)$this->to_delete;
    }

    /**
     * @return string
     */
    public function getTextBasket()
    {
        return $this->text_basket;
    }

    /**
     * @return string
     */
    public function getBannerBasket()
    {
        return $this->banner_basket;
    }

    /**
     * @return string
     */
    public function getTextActionItems()
    {
        return $this->text_action_items;
    }

    /**
     * @return string
     */
    public function getBannerActionItems()
    {
        return $this->banner_action_items;
    }

    /**
     * @return string
     */
    public function getIconActionItems()
    {
        return $this->icon_action_items;
    }

    /**
     * @return string
     */
    public function getTextConditionItems()
    {
        return $this->text_condition_items;
    }

    /**
     * @return string
     */
    public function getBannerConditionItems()
    {
        return $this->banner_condition_items;
    }

    /**
     * @return string
     */
    public function getIconConditionItems()
    {
        return $this->icon_condition_items;
    }

    /**
     * @return bool
     */
    public function getAlternativeShippingOption()
    {
        return (bool)$this->alternative_shipping_option;
    }

    /**
     * @return float
     */
    public function getCondSumAmntAllItemsGTE()
    {
        return (float)$this->cond_sum_amnt_all_items_gte;
    }

    public function getCondSumAmntEachItemGTE()
    {
        return (float)$this->cond_sum_amnt_each_item_gte;
    }

    /**
     * @return float
     */
    public function getCondSumAmntSingItemGTE()
    {
        return (float)$this->cond_sum_amnt_sing_item_gte;
    }

    /**
     * @return float
     */
    public function getCondNoDiffItemsGTE()
    {
        return (float)$this->cond_no_diff_items_gte;
    }

    /**
     * @return float
     */
    public function getCondTotalItemQtyGTE()
    {
        return (float)$this->cond_total_item_qty_gte;
    }

    /**
     * @return float
     */
    public function getCondQtySingleItemGTE()
    {
        return (float)$this->cond_qty_single_item_gte;
    }

    public function getCondQtyEachItemGTE()
    {
        return (float)$this->cond_qty_each_item_gte;
    }

    /**
     * @return float
     */
    public function getCondBasketTotalGTE()
    {
        return (float)$this->cond_basket_total_gte;
    }

    /**
     * @return float
     */
    public function getDiscountAmnt()
    {
        return (float)$this->discount_amnt;
    }

    /**
     * @return string
     */
    public function getDiscountAmntAppliesTo()
    {
        return $this->discount_amnt_applies_to;
    }

    /**
     * @return float
     */
    public function getDiscountPercent()
    {
        return (float)$this->discount_percent;
    }

    /**
     * @return string
     */
    public function getDiscountPercentAppliesTo()
    {
        return $this->discount_percent_applies_to;
    }

    /**
     * @return float
     */
    public function getQtyFreeItem()
    {
        return (float)$this->qty_free_item;
    }

    /**
     * @return string
     */
    public function getTypeFreeItem()
    {
        return $this->type_free_item;
    }

    /**
     * @return string
     */
    public function getConditionType()
    {
        if($this->cond_sum_amnt_all_items_gte > 0) {
            return self::CONDITION_TYPE_SUM_AMNT_ITEMS;
        } elseif($this->cond_sum_amnt_sing_item_gte > 0) {
            return self::CONDITION_TYPE_AMNT_SINGLE_ITEM;
        } elseif($this->cond_no_diff_items_gte > 0) {
            return self::CONDITION_TYPE_NO_DIFF_ITEMS;
        } elseif($this->cond_total_item_qty_gte > 0) {
            return self::CONDITION_TYPE_SUM_QTY_ITEMS;
        } elseif($this->cond_qty_single_item_gte > 0) {
            return self::CONDITION_TYPE_QTY_SINGLE_ITEM;
        } elseif($this->cond_basket_total_gte > 0) {
            return self::CONDITION_TYPE_SUM_AMNT_BASKET;
        } elseif($this->cond_sum_amnt_each_item_gte > 0) {
            return self::CONDITION_TYPE_SUM_AMNT_EACH_ITEM;
        } elseif($this->cond_qty_each_item_gte > 0) {
            return self::CONDITION_TYPE_QTY_EACH_ITEM;
        }
        throw new \DomainException('Condition type is not set.');
    }

    public function getConditionThresholdValue()
    {
        if($this->cond_sum_amnt_all_items_gte > 0) {
            return $this->cond_sum_amnt_all_items_gte;
        } elseif($this->cond_sum_amnt_sing_item_gte > 0) {
            return $this->cond_sum_amnt_sing_item_gte;
        } elseif($this->cond_no_diff_items_gte > 0) {
            return $this->cond_no_diff_items_gte;
        } elseif($this->cond_total_item_qty_gte > 0) {
            return $this->cond_total_item_qty_gte;
        } elseif($this->cond_qty_single_item_gte > 0) {
            return $this->cond_qty_single_item_gte;
        } elseif($this->cond_basket_total_gte > 0) {
            return $this->cond_basket_total_gte;
        } elseif($this->cond_sum_amnt_each_item_gte > 0) {
            return $this->cond_sum_amnt_each_item_gte;
        } elseif($this->cond_qty_each_item_gte) {
            return $this->cond_qty_each_item_gte;
        } else {
            return 0;
        }
    }

    /**
     * @return string
     */
    public function getActionType()
    {
        if($this->discount_amnt > 0) {
            return self::ACTION_TYPE_DISCOUNT_AMOUNT;
        } elseif($this->discount_percent > 0) {
            return self::ACTION_TYPE_DISCOUNT_PERCENT;
        } elseif($this->qty_free_item > 0) {
            return self::ACTION_TYPE_FREE_ITEMS;
        } elseif($this->alternative_shipping_option) {
            return self::ACTION_TYPE_SPECIAL_SHIPPING;
        }
        throw new \DomainException('Action type is not set.');
    }

    public function getDiscountApplicationMaxQty()
    {
        return $this->discount_applies_to_qty_max;
    }

    public function getIconPathConditionItems()
    {
        if(empty($this->icon_condition_items)) {
            return '';
        } else {
            return self::UPLOADDIR_IMAGES . $this->icon_condition_items;
        }
    }

    public function getBannerPathConditionItems()
    {
        if(empty($this->banner_condition_items)) {
            return '';
        } else {
            return self::UPLOADDIR_IMAGES . $this->banner_condition_items;
        }
    }

    public function getIconPathActionItems()
    {
        if(empty($this->icon_action_items)) {
            return '';
        } else {
            return self::UPLOADDIR_IMAGES . $this->icon_action_items;
        }
    }

    public function getBannerPathActionItems()
    {
        if(empty($this->banner_action_items)) {
            return '';
        } else {
            return self::UPLOADDIR_IMAGES . $this->banner_action_items;
        }
    }

    public function getTextModuleConditionItems( TextModuleRepository $textModuleRepository )
    {
        if (empty($this->text_condition_items)) {
            return $textModuleRepository->getNullObject();
        } else {
            return $textModuleRepository->findByAltPrimary(['company' => $this->getCompany(),'code' => $this->getTextConditionItems()]);
        }
    }

    public function getTextModuleActionItems( TextModuleRepository $textModuleRepository )
    {
        if (empty($this->text_action_items)) {
            return $textModuleRepository->getNullObject();
        } else {
            return $textModuleRepository->findByAltPrimary(['company' => $this->getCompany(),'code' => $this->getTextActionItems()]);
        }
    }

}