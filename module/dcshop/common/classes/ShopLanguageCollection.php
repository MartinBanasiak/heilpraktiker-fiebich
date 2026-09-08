<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:24
 */
class ShopLanguageCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param ShopLanguageConfig                                 $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( ShopLanguageConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return ShopLanguageCollection
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
        return $this->_addShopLanguage($instance, $idCheck);
    }

    /**
     * @param ShopLanguage $shopLanguage * @param bool      $idCheck
     *
     * @param bool          $idCheck
     *
     * @return bool
     */
    protected function _addShopLanguage( ShopLanguage $shopLanguage, $idCheck = FALSE ) {
        return $this->_add($shopLanguage,$idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeShopLanguage($instance,$idCheck);
    }

    /**
     * @param ShopLanguage $shopLanguage
     * @param bool          $idCheck
     *
     * @return bool
     */
    protected function _removeShopLanguage( ShopLanguage $shopLanguage, $idCheck = FALSE ) {
        return $this->_remove($shopLanguage,$idCheck);
    }

}