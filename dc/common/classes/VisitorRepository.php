<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;
use DynCom\dc\dcShop\classes\UserRepository;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:27
 */
class VisitorRepository implements Repository
{

    use genericRepositoryTrait;

    /**
     * VisitorRepository constructor.
     * @param GenericDBQueryWrapperInterface $db
     * @param VisitorConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param VisitorCollection $collection
     * @param $cacheAll
     */
    public function __construct( GenericDBQueryWrapperInterface $db, VisitorConfig $config, CriteriaHelperInterface $criteriaValidationService, VisitorCollection $collection, $cacheAll=false) {
        $this->db           = $db;
        $this->config       = $config;
        $this->collection   = $collection;
        $this->collectionEntryClassName    = $this->collection->getEntryClassName();
        $this->tableName    = $this->config->getTableName();
        $this->altPrimary   = $this->config->getAltPrimary();
        $this->mappedFields = $this->config->getMappedFields();
    }

    /**
     * @param $sessionID
     * @return \DynCom\dc\common\interfaces\Entity
     */
    public function findBySessionID( $sessionID ) {
        $visitor = $this->getNullObject();

        if($this->cacheAll) {
            $this->_cacheAll();
        }

        //Try from collection
        foreach($this->collection as $instance) {
            if(isset($instance->session_id) && ($instance->session_id == $sessionID)) {
                return $instance;
            }
        }
        //Else try from db
        $query = "SELECT * FROM  `" . $this->tableName . "` WHERE `session_id` = :sessionID";
        if ($this->db->setQuery($query) && $this->db->prepareQuery() && $this->db->bindParameters(array(array(':sessionID', $sessionID, \PDO::PARAM_STR))) && $this->db->executePreparedStatement() && ($resArr = $this->db->getResultArray()) && (count($resArr) == 1)) {
            $visitor->mapFromArray($resArr[0]);
            return $visitor;
        }

        return $visitor;
    }

    /**
     * @return Visitor
     */
    public function getFromSession() {
        return $this->findBySessionID(session_id());
    }

    /**
     * @param UserRepository $userRepository
     * @return mixed
     */
    public function getUserFromSession(UserRepository $userRepository) {
        $currVisitor = $this->getFromSession();
        return $userRepository->findByID($currVisitor->main_user_id);
    }

}