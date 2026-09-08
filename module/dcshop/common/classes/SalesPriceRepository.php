<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\classes\Visitor;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 24.03.2015
 * Time: 10:29
 */

class SalesPriceRepository implements Repository {

    use genericRepositoryTrait;

    /**
     * SalesPriceRepository constructor.
     * @param GenericDBQueryWrapperInterface $db
     * @param SalesPriceConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param SalesPriceCollection $collection
     * @param $cacheAll
     */
    public function __construct( GenericDBQueryWrapperInterface $db, SalesPriceConfig $config, CriteriaHelperInterface $criteriaValidationService, SalesPriceCollection $collection, $cacheAll=false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @return SalesPrice
     */
    public function getNullObject() {
        return new SalesPrice($this->config);
    }

}