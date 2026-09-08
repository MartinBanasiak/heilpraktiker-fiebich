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
 * Time: 13:36
 */
class EachItemQtyGTERuleCondition implements RuleCondition
{
    use campaignConditionTrait;

    private $gteThresholdValue;
    private $multiplyApplicable;
    private $itemBuilder;

    public function __construct(GenericCampaign $campaign, WebshopItemBuilder $itemBuilder)
    {
        $this->elementsToFilterBy = $campaign->getConditionElements();
        $this->gteThresholdValue = (float)$campaign->getCondQtySingleItemGTE();
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
            //
            $checkedItems = [];
            $positionInBasket = 0;
            /**
             * @var $basketEntity BasketEntity
             */
            foreach ($filteredBasketItems as $basketEntity) {
                $identKey = $basketEntity->getIdentifier() . '|' . $basketEntity->getSubIdentifier();
                $fullKey = $basket->getKey($basketEntity);
                $entityQty = 0.0;
                $entities = [];
                //group and add because there could be more than 1 position per item-no & var-code
                if (array_key_exists($identKey, $checkedItems)) {
                    $entities = (array)$checkedItems[$identKey]['entities'];
                    $entityQty = (float)$checkedItems[$identKey]['qty'];
                }
                if (!array_key_exists($fullKey,$entities)) {
                    $entities[$fullKey] = $basketEntity;
                }
                $entityQty += $basketEntity->getQuantity();
                $checkedItems[$identKey] = ['qty' => $entityQty, 'entities' => $entities];
                $positionInBasket++;
            }

            foreach ($checkedItems as $identKey => $arr) {
                $entities = (array)$arr['entities'];
                $entityQty = (float)$arr['qty'];
                $basketEntity = end($entities);
                if ($basketEntity instanceof BasketEntity) {
                    $totalNo++;
                    if ($entityQty >= $threshold) {
                        $metByFactor = 1;
                        $remainder = null;
                        if ($threshold > 0) {
                            $metByFactor = floor($entityQty / $threshold);
                            $remainder = $entityQty % $threshold;
                        }
                        $matchingBasketItems[] += array_values($arr['entities']);
                        $conditionItemMatchingData[$identKey] = ['threshold_met_by_factor' => $metByFactor, 'threshold_modulus' => $remainder, 'entities' => $entities];
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