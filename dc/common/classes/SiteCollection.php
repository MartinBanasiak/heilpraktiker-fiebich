<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 18.01.2015
 * Time: 23:45
 */

class SiteCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param SiteConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( SiteConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }


    /**
     * @return SiteCollection
     */
    public function getEmptyCollection() {
        return new self($this->config,$this->criteriaValidationService);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function add( $instance, $idCheck = FALSE ) {
        return $this->_addSite($instance, $idCheck);
    }

    /**
     * @param Site $site
     * @param bool $idCheck
     * @return bool
     */
    protected function _addSite( Site $site, $idCheck = FALSE ) {
        return $this->_add($site, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeSite($instance,$idCheck);
    }

    /**
     * @param Site $site
     * @param bool $idCheck
     * @return bool
     */
    protected function _removeSite( Site $site, $idCheck = FALSE ) {
        return $this->_remove($site,$idCheck);
    }

}