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
 * Date: 16.11.2015
 * Time: 13:34
 */
class EachItemAmntGTERuleCondition implements RuleCondition
{
    use campaignConditionTrait;

    private $gteThresholdValue;
    private $multiplyApplicable;
    private $itemBuilder;

    public function __construct(GenericCampaign $campaign, WebshopItemBuilder $itemBuilder)
    {
        $this->elementsToFilterBy = $campaign->getConditionElements();
        $this->gteThresholdValue = (float)$campaign->getCondSumAmntSingItemGTE();
        $this->ruleKey = GenericRule::RULE_TYPE_CAMPAIGN . '|' . $campaign->getID();
        $this->multiplyApplicable = $campaign->isMultiplyApplicable();
        $this->itemBuilder = $itemBuilder;
    }

    public function evaluatesTrue(RuleContext $context)
    {
        $this->extractedVariableArray = [];
        $basket = $context->getVariable('UserBasket')->getValue();
        if ($basket instanceof UserBasket) {
            //Filtering excludes items whose quantity source isn't the user
            $filteredBasketItems = $this->filterBasketByCampaignElementCollection($basket, $this->elementsToFilterBy, $this->itemBuilder);
            $conditionItemMatchingData = [];
            $conditionItemMatchingData['all'] = ['threshold_met_by_factor' => 0];
            $matchingBasketItems = [];
            $totalNo = 0;
            $threshold = $this->gteThresholdValue;
            $noOfItemsGteThreshold = 0;
            $lastMatchedEntity = null;
            $lastMatchedEntities = null;
            //
            $checkedItems = [];
            $positionInBasket = 0;
            /**
             * @var $basketEntity BasketEntity
             */
            foreach ($filteredBasketItems as $basketEntity) {
                $identKey = $basketEntity->getIdentifier() . '|' . $basketEntity->getSubIdentifier();
                $fullKey = $basket->getKey($basketEntity);
                $entityLineAmnt = 0.0;
                $entities = [];
                //group and add because there could be more than 1 position per item-no & var-code
                if (array_key_exists($identKey, $checkedItems)) {
                    $entities = (array)$checkedItems[$identKey]['entities'];
                    $entityLineAmnt = (float)$checkedItems[$identKey]['line_amnt'];
                }
                if (!array_key_exists($fullKey,$entities)) {
                    $entities[$fullKey] = $basketEntity;
                }
                $entityLineAmnt += $basketEntity->getLineAmount();
                $checkedItems[$identKey] = ['line_amnt' => $entityLineAmnt, 'entities' => $entities];
                $positionInBasket++;
            }

            foreach ($checkedItems as $identKey => $arr) {
                $entities = (array)$arr['entities'];
                $entityLineAmnt = (float)$arr['line_amnt'];
                $basketEntity = end($entities);
                if ($basketEntity instanceof BasketEntity) {
                    $totalNo++;
                    if ($entityLineAmnt >= $this->gteThresholdValue) {
                        $noOfItemsGteThreshold++;
                        $metByFactor = 1;
                        if ($this->gteThresholdValue > 0) {
                            $metByFactor = floor($entityLineAmnt / $this->gteThresholdValue);
                        }
                        $matchingBasketItems[] += array_values($arr['entities']);
                        $conditionItemMatchingData[$identKey] = ['threshold_met_by_factor' => $metByFactor, 'entities' => $entities];
                        $conditionItemMatchingData['all']['threshold_met_by_factor'] += $metByFactor;
                        $lastMatchedEntity = $basketEntity;
                        $lastMatchedEntities = $entities;
                        $noOfItemsGteThreshold++;
                    }
                }
            }
            if ($noOfItemsGteThreshold === $totalNo) {
                $this->extractedVariableArray[] = new GenericRuleContextVariable($this->ruleKey . '.ConditionItemMatchingData', $conditionItemMatchingData);
                $this->extractedVariableArray[] = new GenericRuleContextVariable($this->ruleKey . '.ConditionItems', $matchingBasketItems);
                $this->extractedVariableArray[] = new GenericRuleContextVariable($this->ruleKey . '.CurrConditionItem', $lastMatchedEntities);
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