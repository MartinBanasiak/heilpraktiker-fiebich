<?php
namespace DynCom\dc\dcShop\campaigns;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\traits\reflectionIDSetter;
use DynCom\dc\dcShop\classes\WebshopItemBuilder;
use DynCom\dc\RuleEngine\AlwaysTrueRuleCondition;
use DynCom\dc\RuleEngine\GenericRule;
use DynCom\dc\RuleEngine\GenericRuleConditionList;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 29.10.2015
 * Time: 10:59
 */
class CampaignRuleFactory
{
    use reflectionIDSetter;

    private $itemBuilder;

    private $db;

    /**
     * @param WebshopItemBuilder $itemBuilder
     * @param PDOQueryWrapper $queryWrapper
     */
    public function __construct(WebshopItemBuilder $itemBuilder, PDOQueryWrapper $queryWrapper)
    {
        $this->itemBuilder = $itemBuilder;
        $this->db = $queryWrapper;
    }

    /**
     * @param GenericCampaign $campaign
     * @return GenericRule
     */
    public function getRuleFromCampaign(GenericCampaign $campaign)
    {
        try {
            $conditionList = new GenericRuleConditionList();
            $condition = $this->getCondition($campaign);
            $conditionList->pushRuleCondition($condition);
            $action = $this->getAction($campaign);
            $campaignName = $campaign->getDescription();
            if(empty($campaignName)) {
                $campaignName = 'Kampagne-' . $campaign->getID();
            }
            $rule = new GenericRule('global',$campaignName,$conditionList,$action);
            $this->setID($rule, $campaign->getID());
            return $rule;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param GenericCampaign $campaign
     * @return NoDiffItemsGTERuleCondition|SingleItemAmntGTERuleCondition|SingleItemQtyGTERuleCondition|SumAmntAllItemsGTERuleCondition|SumBasketAmntGTERuleCondition|SumQtyItemRuleCondition
     */
    private function getCondition(GenericCampaign $campaign)
    {
        if($campaign->getCondSumAmntAllItemsGTE() > 0) {
            $condition = new SumAmntAllItemsGTERuleCondition($campaign,$this->itemBuilder);
        } elseif($campaign->getCondSumAmntSingItemGTE() > 0) {
            $condition = new SingleItemAmntGTERuleCondition($campaign,$this->itemBuilder);
        } elseif($campaign->getCondNoDiffItemsGTE() > 0) {
            $condition = new NoDiffItemsGTERuleCondition($campaign,$this->itemBuilder);
        } elseif($campaign->getCondTotalItemQtyGTE() > 0) {
            $condition = new SumQtyItemRuleCondition($campaign,$this->itemBuilder);
        } elseif($campaign->getCondQtySingleItemGTE() > 0) {
            $condition = new  SingleItemQtyGTERuleCondition($campaign,$this->itemBuilder);
        } elseif($campaign->getCondBasketTotalGTE() > 0) {
            $condition = new SumBasketAmntGTERuleCondition($campaign,$this->itemBuilder);
        } elseif($campaign->getCondSumAmntEachItemGTE() > 0) {
            $condition = new EachItemAmntGTERuleCondition($campaign, $this->itemBuilder);
        } elseif($campaign->getCondQtyEachItemGTE() > 0) {
            $condition = new EachItemQtyGTERuleCondition($campaign,$this->itemBuilder);
        } else {
            $condition = new AlwaysTrueRuleCondition();
        }
        return $condition;
    }

    /**
     * @param GenericCampaign $campaign
     * @return CampaignDiscountAction|CampaignFreeItemAction|CampaignSpecialShippingAction
     */
    private function getAction(GenericCampaign $campaign)
    {
        if($campaign->getDiscountAmnt() > 0 || $campaign->getDiscountPercent() > 0) {
            $action = new CampaignDiscountAction($campaign,$this->itemBuilder);
        } elseif($campaign->getQtyFreeItem() > 0) {
            $action = new CampaignFreeItemAction($campaign,$this->itemBuilder);
        } elseif($campaign->getAlternativeShippingOption()) {
            $action = new CampaignSpecialShippingAction($campaign);
        } else {
            throw new \DomainException('Campaign has no stated action.');
        }
        return $action;
    }
}