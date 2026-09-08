<?php

namespace DynCom\dc\dcShop\classes;

use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\classes\Visitor;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:25
 */
class UserRepository implements Repository
{

    use genericRepositoryTrait;

    /**
     * UserRepository constructor.
     * @param PDOQueryWrapper $db
     * @param UserConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param UserCollection $collection
     * @param $cacheAll
     */
    public function __construct(PDOQueryWrapper $db, UserConfig $config, CriteriaHelperInterface $criteriaValidationService, UserCollection $collection, $cacheAll = false)
    {
        $this->db = $db;
        $this->config = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection = $collection;
        $this->collectionEntryClassName = $this->collection->getEntryClassName();
        $this->cacheAll = $cacheAll;
    }

    /**
     * @return User
     */
    public function getNullObject() : User
    {
        return new User($this->config);
    }

    /**
     * @param Visitor $currVisitor
     * @return User
     */
    public function getUserForCurrVisitor(Visitor $currVisitor) : User
    {
        $found = $this->findByID($currVisitor->main_user_id);
        return $found;
    }
}