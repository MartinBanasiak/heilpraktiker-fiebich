<?php
namespace DynCom\dc\dcShop\campaigns;
use DynCom\dc\dcShop\classes\BasketEntity;
use DynCom\dc\dcShop\classes\WebshopItemBuilder;
use DynCom\dc\dcShop\interfaces\UserBasket;
use DynCom\dc\RuleEngine\GenericRule;
use DynCom\dc\RuleEngine\GenericRuleContextVariable;
use DynCom\dc\RuleEngine\RuleCondition;
use DynCom\dc\RuleEngine\RuleContext;
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 28.10.2015
 * Time: 13:42
 */
class SumQtyItemRuleCondition implements RuleCondition
{
    use campaignConditionTrait;

    private $gteThresholdValue;
    private $itemBuilder;

    public function __construct(GenericCampaign $campaign,WebshopItemBuilder $itemBuilder)
    {
        $this->elementsToFilterBy = $campaign->getConditionElements();
        $this->gteThresholdValue = (float)$campaign->getCondTotalItemQtyGTE();
        $this->ruleKey = GenericRule::RULE_TYPE_CAMPAIGN . '|' . $campaign->getID();
        $this->itemBuilder = $itemBuilder;
    }

    public function evaluatesTrue(RuleContext $context)
    {
        $this->extractedVariableArray = [];
        $basket = $context->getVariable('UserBasket')->getValue();
        if ($basket instanceof UserBasket) {
            $filteredBasketItems = $this->filterBasketByCampaignElementCollection($basket,$this->elementsToFilterBy,$this->itemBuilder);
            $sumQtys = $this->sumQtys($filteredBasketItems);
            if(count($filteredBasketItems) > 0 && $sumQtys >= $this->gteThresholdValue) {
                $thresholdMetByFactor = 1;
                if($this->gteThresholdValue > 0) {
                    $thresholdMetByFactor = floor($sumQtys / $this->gteThresholdValue);
                }
                $conditionItemMatchingData = [];
                $conditionItemMatchingData['all'] = ['threshold_met_by_factor' => $thresholdMetByFactor];
                $this->extractedVariableArray[] = new GenericRuleContextVariable($this->ruleKey . '.ConditionItems',$filteredBasketItems);
                $this->extractedVariableArray[] = new GenericRuleContextVariable($this->ruleKey . '.CurrConditionItem',current(end($filteredBasketItems)));
                $this->extractedVariableArray[] = new GenericRuleContextVariable($this->ruleKey . '.ConditionItemMatchingData',$conditionItemMatchingData);
                return true;
            }
        }
        return false;
    }

    public function getHash()
    {
        // TODO: Implement getHash() method.
    }

    public function getNodeData()
    {
        // TODO: Implement getNodeData() method.
    }


}