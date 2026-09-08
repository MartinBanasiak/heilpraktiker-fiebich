<?php
namespace DynCom\dc\dcShop\campaigns;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;

/**
 * Class GenericCampaignHeaderRepository
 */
class GenericCampaignHeaderRepository implements Repository{

    use genericRepositoryTrait;

    /**
     * @param PDOQueryWrapper                                        $db
     * @param GenericCampaignHeaderConfig            $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param GenericCampaignHeaderCollection    $collection
     * @param                                                    $cacheAll
     */
    public function __construct( PDOQueryWrapper $db, GenericCampaignHeaderConfig $config, CriteriaHelperInterface $criteriaValidationService, GenericCampaignHeaderCollection $collection, $cacheAll = false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @return GenericCampaign
     */
    public function getNullObject() {
        $coll = new GenericCampaignElementCollection(new GenericCampaignElementConfig(),$this->criteriaValidationService);
        return new GenericCampaign($this->config,$coll,$coll);
    }

}