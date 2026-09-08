<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;

/**
 * Class PageRepository
 * @package DynCom\dc\common\classes
 */
class PageRepository implements Repository
{

    use genericRepositoryTrait;

    /**
     * @param GenericDBQueryWrapperInterface $db
     * @param PageConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param PageCollection $collection
     * @param                                                     $cacheAll
     */
    public function __construct(GenericDBQueryWrapperInterface $db, PageConfig $config, CriteriaHelperInterface $criteriaValidationService, PageCollection $collection, $cacheAll=false)
    {
        $this->db = $db;
        $this->config = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection = $collection;
        $this->collectionEntryClassName = $this->collection->getEntryClassName();
        $this->cacheAll = $cacheAll;
    }

    /**
     * @return Page
     */
    public function getNullObject()
    {
        return new Page($this->config);
    }

}