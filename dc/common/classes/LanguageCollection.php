<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 19.01.2015
 * Time: 00:01
 */

class LanguageCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param LanguageConfig                                     $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( LanguageConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }


    /**
     * @return LanguageCollection
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
        return $this->_addLanguage($instance, $idCheck);
    }

    /**
     * @param Language $language
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _addLanguage( Language $language, $idCheck = FALSE ) {
        return $this->_add($language, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeLanguage($instance,$idCheck);
    }

    /**
     * @param Language $language
     * @param bool                $idCheck
     *
     * @return bool
     */
    protected function _removeLanguage( Language $language, $idCheck = FALSE ) {
        return $this->_remove($language,$idCheck);
    }

}