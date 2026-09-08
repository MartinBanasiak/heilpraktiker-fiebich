<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 18.01.2015
 * Time: 23:59
 */

class SiteRepository implements Repository {

    use genericRepositoryTrait;

    /**
     * @param GenericDBQueryWrapperInterface                                                         $db
     * @param SiteConfig                                                     $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param SiteCollection $collection
     * @param                                                                                         $cacheAll
     */
    public function __construct( GenericDBQueryWrapperInterface $db, SiteConfig $config, CriteriaHelperInterface $criteriaValidationService, SiteCollection $collection, $cacheAll=false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
    }

    /**
     * @return mixed
     */
    public function getFromRequest() {
        $site = $this->findByAltPrimary(array('code' => filter_var($_GET['site'],FILTER_SANITIZE_SPECIAL_CHARS)));
        return $site;

    }

    /**
     * @return mixed
     */
    public function getMainSiteIDFromRequest() {
        $inst = $this->getFromRequest();
        return $inst->id;
    }

}