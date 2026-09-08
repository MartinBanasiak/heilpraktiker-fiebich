<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Class CountryCollection
 */
class CountryCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param CountryConfig                                     $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( CountryConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return CountryCollection
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
        return $this->_addCountry($instance, $idCheck);
    }

    /**
     * @param Country $country
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _addCountry( Country $country, $idCheck = FALSE ) {
        return $this->_add($country, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeCountry($instance,$idCheck);
    }

    /**
     * @param Country $country
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _removeCountry( Country $country, $idCheck = FALSE ) {
        return $this->_remove($country,$idCheck);
    }

}