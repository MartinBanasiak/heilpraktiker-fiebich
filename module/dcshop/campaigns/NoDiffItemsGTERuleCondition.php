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
 * Time: 13:11
 */
class NoDiffItemsGTERuleCondition implements RuleCondition
{
    use campaignConditionTrait;

    private $gteThresholdValue;
    private $itemBuilder;

    public function __construct(GenericCampaign $campaign,WebshopItemBuilder $itemBuilder)
    {
        $this->elementsToFilterBy = $campaign->getConditionElements();
        $this->gteThresholdValue = (float)$campaign->getCondSumAmntSingItemGTE();
        $this->ruleKey = GenericRule::RULE_TYPE_CAMPAIGN . '|' . $campaign->getID();
        $this->itemBuilder = $itemBuilder;
    }

    public function evaluatesTrue(RuleContext $context)
    {
        $this->extractedVariableArray = [];
        $basket = $context->getVariable('UserBasket')->getValue();
        if ($basket instanceof UserBasket) {
            $filteredBasketItems = $this->filterBasketByCampaignElementCollection($basket,$this->elementsToFilterBy,$this->itemBuilder);
            $identKeys = [];
            foreach ($filteredBasketItems as $entity) {
                if ($entity instanceof BasketEntity) {
                    $identKey = $entity->getIdentifier() . '|' . $entity->getSubidentifier();
                    if (!array_key_exists($identKey,$identKeys)) {
                        $identKeys[$identKey] = 1;
                    }
                }
            }
            $noOfDifferentPositions = count($identKeys);
            $conditionItemMatchingData = [];
            if($noOfDifferentPositions >= $this->gteThresholdValue) {
                $metByFactor = 1;
                if($this->gteThresholdValue > 0) {
                    $metByFactor = floor($noOfDifferentPositions / $this->gteThresholdValue);
                }
                $conditionItemMatchingData['all'] = ['threshold_met_by_factor' => $metByFactor];
                $this->extractedVariableArray[] = new GenericRuleContextVariable($this->ruleKey . '.ConditionItemMatchingData',$conditionItemMatchingData);
                $this->extractedVariableArray[] = new GenericRuleContextVariable($this->ruleKey . '.ConditionItems',$filteredBasketItems);
                $this->extractedVariableArray[] = new GenericRuleContextVariable($this->ruleKey . '.CurrConditionItem',end($filteredBasketItems));
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