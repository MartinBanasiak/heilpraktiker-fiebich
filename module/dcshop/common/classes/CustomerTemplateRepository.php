<?php
namespace DynCom\dc\dcShop\classes;

use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;

/**
 * Class CustomerTemplateRepository
 */
class CustomerTemplateRepository implements Repository
{

    use genericRepositoryTrait;

    /**
     * CustomerTemplateRepository constructor.
     * @param PDOQueryWrapper $db
     * @param CustomerTemplateConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param CustomerTemplateCollection $collection
     * @param $cacheAll
     */
    public function __construct(PDOQueryWrapper $db, CustomerTemplateConfig $config, CriteriaHelperInterface $criteriaValidationService, CustomerTemplateCollection $collection, $cacheAll = false)
    {
        $this->db = $db;
        $this->config = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection = $collection;
        $this->collectionEntryClassName = $this->collection->getEntryClassName();
        $this->cacheAll = $cacheAll;
    }

    /**
     * @return CustomerTemplate
     */
    public function getNullObject()
    {
        return new CustomerTemplate($this->config);
    }
}