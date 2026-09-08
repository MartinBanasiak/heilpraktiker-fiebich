<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

/**
 * Class SubscriptionItemLinkRepository
 */
class SubscriptionItemLinkRepository implements Repository{

    use genericRepositoryTrait;

    /**
     * @param GenericDBQueryWrapperInterface $db
     * @param SubscriptionItemLinkConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param SubscriptionItemLinkCollection $collection
     * @param $cacheAll
     */
    public function __construct( GenericDBQueryWrapperInterface $db, SubscriptionItemLinkConfig $config, CriteriaHelperInterface $criteriaValidationService, SubscriptionItemLinkCollection $collection, $cacheAll=false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @return SubscriptionItemLink
     */
    public function getNullObject() {
        return new SubscriptionItemLink($this->config);
    }

    /**
     * @param SubscriptionHeader $header
     * @return SubscriptionItemLinkCollection
     */
    public function getAllForOrderOptionHeader(SubscriptionHeader $header) {
        $criteria = [
            [
                ['company','=',$header->company],
                ['subscription_code','=',$header->code],
                ['link-to','=', 0]
            ]
        ];
        return $this->findByCriteria($criteria);
    }

    /**
     * @param SubscriptionSequenceStep $sequenceStep
     * @return SubscriptionItemLinkCollection
     */
    public function getAllForSequenceStep(SubscriptionSequenceStep $sequenceStep) {
            $criteria = [
                [
                    ['company','=',$sequenceStep->company],
                    ['subscription_code','=',$sequenceStep->subscription_code],
                    ['link-to','=', 1],
                    ['subscr_seq_step_line_no','=',$sequenceStep->line_no]
                ]
            ];
            return $this->findByCriteria($criteria);
    }


    /**
     * @param SubscriptionHeader $header
     * @param WebshopItemInterface $item
     * @return SubscriptionItemLink
     */
    public function getItemLinkForOrderOption(SubscriptionHeader $header, WebshopItemInterface $item) {
        $altPrimaryArr = [
            'company' => $header->company,
            'subscription_code' => $header->code,
            'link_to' => 0,
            'item_no' => $item->getItemNo(),
            'variant_code' => $item->getVariantCode(),
            'subscr_seq_step_line_no' => 0
        ];
        return $this->findByAltPrimary($altPrimaryArr);
    }

}