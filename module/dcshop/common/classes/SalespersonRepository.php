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
 * Date: 15.01.2015
 * Time: 11:25
 */
class SalespersonRepository implements Repository {

    use genericRepositoryTrait;

    /**
     * SalespersonRepository constructor.
     * @param GenericDBQueryWrapperInterface $db
     * @param SalespersonConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param SalespersonCollection $collection
     * @param $cacheAll
     */
    public function __construct( GenericDBQueryWrapperInterface $db, SalespersonConfig $config, CriteriaHelperInterface $criteriaValidationService, SalespersonCollection $collection, $cacheAll=false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @return User
     */
    public function getNullObject() {
        return new User($this->config);
    }

    /**
     * @param Visitor $currVisitor
     * @return mixed
     */
    public function getUserForCurrVisitor(Visitor $currVisitor) {
        return $this->findByID($currVisitor->main_user_id);
    }
}