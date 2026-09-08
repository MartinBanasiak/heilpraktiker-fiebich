<?php
namespace DynCom\dc\dcShop\traits;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;
use DynCom\dc\dcShop\interfaces\GenericDocumentInterface;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 14.01.2015
 * Time: 19:42
 */
trait documentRepositoryTrait {

    use genericRepositoryTrait {
        deleteByID as genericDeleteByID;
        findByID as genericFindByID;
    }

    protected $lineConfig;
    protected $lineRepository;


    /**
     * documentRepositoryTrait constructor.
     * @param GenericDBQueryWrapperInterface $db
     * @param ModelDBConfigInterface $config
     * @param Repository $lineRepository
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param GenericCollectionInterface $collection
     * @param bool $cacheAll
     */
    public function __construct( GenericDBQueryWrapperInterface $db, ModelDBConfigInterface $config, Repository $lineRepository, CriteriaHelperInterface $criteriaValidationService, GenericCollectionInterface $collection, $cacheAll = FALSE ) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->lineRepository            = $lineRepository;
        $this->lineConfig                = $this->lineRepository->getConfig();
        $this->criteriaValidationService = $collection;

        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @param GenericDocumentInterface $instance
     * @return mixed
     */
    abstract public function setLinesForDocument( GenericDocumentInterface $instance );

    /**
     * @param $id
     * @return mixed
     */
    public function findByID($id) {
        $obj = $this->genericFindByID($id);
        $this->setLinesForDocument($obj);
        return $obj;
    }


    /**
     * @return GenericCollectionInterface
     */
    public function getEmptyCollection() {
        return $this->collection->getEmptyCollection();
    }

    /**
     * @param $instance
     *
     * @return bool
     */
    protected function _setLinesForDocument( GenericDocumentInterface $instance ) {
        //Get array representing selection criteria to query repository
        //in disjunctive normal form
        $criteria = $instance->getLineCriteriaArray();
        if (count($criteria) > 0) {
            //Find lines in repository by criteria
            $lineCollection = $this->lineRepository->findByCriteria($criteria);
            if (count($lineCollection) > 0) {
                //Set those lines in the given instance
                $instance->setLines($lineCollection);
                if ($instance->isLinesSet()) {
                    return $this->_add($instance, TRUE);
                }
                return FALSE;
            }
        }
        return FALSE;
    }

    /**
     * @param      $instance
     * @param bool $idCheck
     *
     * @return bool
     */
    protected function _add( GenericDocumentInterface $instance, $idCheck = FALSE ) {

        if(!$instance->isLinesSet()) {
            return $this->_setLinesForDocument($instance);
        }
        return $this->collection->add($instance, $idCheck);
    }

    /**
     * @return Repository
     */
    public function getLineRepository() {
        return $this->lineRepository;
    }

    /**
     * @param $instance
     *
     * @return bool
     */
    public function updateSingle( GenericDBModelInterface $instance ) {
        if(!($instance instanceof GenericDocumentInterface)) {
            return FALSE;
        }
        $lines = $instance->getLines();
        foreach($lines as $line) {
            $this->lineRepository->updateSingle($line);
        }
        return $this->_updateSingle($instance);
    }

    /**
     * @param GenericDBModelInterface $instance
     * @return bool
     */
    public function deleteByID(GenericDBModelInterface $instance) {
        if(!($instance instanceof GenericDocumentInterface)) {
            return FALSE;
        }
        foreach($instance as $line) {
            $this->lineRepository->deleteByID($line);
        }
        return $this->genericDeleteByID($instance);
    }

}