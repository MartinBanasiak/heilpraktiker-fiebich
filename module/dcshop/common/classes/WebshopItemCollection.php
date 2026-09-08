<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:26
 */
class WebshopItemCollection implements GenericCollectionInterface {

    use genericCollectionTrait {
        __construct as __traitConstruct;
    }

    /**
     * @param WebshopItemConfig                                  $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( WebshopItemConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->__traitConstruct($config,$criteriaValidationService);
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = 'WebshopItemInterface';
    }

    /**
     * @return WebshopItemCollection
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
        return $this->_addWebshopItem($instance, $idCheck);
    }

    /**
     * @param WebshopItemInterface $webshopItem
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _addWebshopItem( WebshopItemInterface $webshopItem, $idCheck = FALSE ) {
        return $this->_add($webshopItem, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeWebshopItem($instance,$idCheck);
    }

    /**
     * @param WebshopItemInterface $webshopItem
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _removeWebshopItem( WebshopItemInterface $webshopItem, $idCheck = FALSE ) {
        return $this->_remove($webshopItem,$idCheck);
    }

    /**
     * @return WebshopItem
     */
    public function getNullObject() {
        return new WebshopItem($this->config);
    }


}