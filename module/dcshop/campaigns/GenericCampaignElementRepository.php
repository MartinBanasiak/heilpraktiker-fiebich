<?php
namespace DynCom\dc\dcShop\campaigns;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;

/**
 * Class GenericCampaignElementRepository
 */
class GenericCampaignElementRepository implements Repository{

    use genericRepositoryTrait;

    /**
     * @param PDOQueryWrapper                                        $db
     * @param GenericCampaignElementConfig            $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param GenericCampaignElementCollection    $collection
     * @param                                                    $cacheAll
     */
    public function __construct( PDOQueryWrapper $db, GenericCampaignElementConfig $config, CriteriaHelperInterface $criteriaValidationService, GenericCampaignElementCollection $collection, $cacheAll = false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @return GenericCampaignElement
     */
    public function getNullObject() {
        return new GenericCampaignElement($this->config);
    }

}