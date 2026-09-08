<?php
namespace DynCom\dc\dcShop\campaigns;
use DynCom\dc\dcShop\ShippingOptions\ShippingClassRepository;
use DynCom\dc\RuleEngine\GenericRule;
use DynCom\dc\RuleEngine\RuleAction;
use DynCom\dc\RuleEngine\RuleContext;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 29.10.2015
 * Time: 10:15
 */
class CampaignSpecialShippingAction implements RuleAction
{
    use campaignRulePartTrait;

    private $campaignCode;

    public function __construct(GenericCampaign $campaign)
    {
        $this->ruleKey = GenericRule::RULE_TYPE_CAMPAIGN . '|' . $campaign->getID();
        $this->campaignCode = $campaign->getCode();

    }

    public function execute(RuleContext $context)
    {
        $shipOptRepo = $this->getContextVariable($context,'ShippingOptionRepository');
        if($shipOptRepo instanceof ShippingClassRepository) {
            $shipOptRepo->addCampaignCode($this->campaignCode);
        }
    }

}