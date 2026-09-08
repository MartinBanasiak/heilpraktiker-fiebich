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
 * Time: 10:12
 */
class SingleItemQtyGTERuleCondition implements RuleCondition
{
    use campaignConditionTrait;

    private $gteThresholdValue;
    private $multiplyApplicable;
    private $itemBuilder;
    private $campaignCode;

    public function __construct(GenericCampaign $campaign, WebshopItemBuilder $itemBuilder)
    {
        $this->campaignCode = $campaign->getCode();
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
            $filteredBasketItems = $this->filterBasketByCampaignElementCollection($basket, $this->elementsToFilterBy, $this->itemBuilder);
            $conditionItemMatchingData = [];
            $conditionItemMatchingData['all'] = ['threshold_met_by_factor' => 0];
            $matchingBasketItems = [];
            $atLeastOneTrue = false;
            $lastMatchedEntity = null;
            $lastMatchedEntities = null;

            $identKeyData = [];
            foreach ($filteredBasketItems as $basketEntity) {
                if ($basketEntity instanceof BasketEntity) {
                    $identKey = $basketEntity->getIdentifier() . '|' . $basketEntity->getSubIdentifier();
                    $entityQty = $basketEntity->getQuantity();
                    $entities = [$basketEntity];
                    if (array_key_exists($identKey, $identKeyData)) {
                        $entityQty += $identKeyData[$identKey]['qty'];
                        $entities += array_values($identKeyData[$identKey]['entities']);
                    }
                    $identKeyData[$identKey] = ['qty' => $entityQty, 'entities' => $entities];
                }
            }
            foreach ($identKeyData as $identKey => $data) {
                $entityQty = $data['qty'];
                $entities = $data['entities'];
                if ($entityQty >= $this->gteThresholdValue) {
                    $atLeastOneTrue = true;
                    $metByFactor = 1;
                    $remainder = null;
                    if ($this->gteThresholdValue > 0) {
                        $metByFactor = floor($entityQty / $this->gteThresholdValue);
                        $remainder = $entityQty % $this->gteThresholdValue;;
                    }
                    $matchingBasketItems += $entities;
                    $conditionItemMatchingData[$identKey] = ['threshold_met_by_factor' => $metByFactor,'threshold_modulus' => $remainder];
                    $conditionItemMatchingData['all']['threshold_met_by_factor'] += $metByFactor;
                    $lastMatchedEntity = $basketEntity;
                    $lastMatchedEntities = $entities;
                    if (!$this->multiplyApplicable) {
                        break;
                    }
                }
            }
            if ($atLeastOneTrue) {
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