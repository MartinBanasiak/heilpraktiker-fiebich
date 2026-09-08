<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Class WebshopItemVariantCollection
 */
class WebshopItemVariantCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param WebshopItemVariantConfig                                     $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( WebshopItemVariantConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return WebshopItemVariantCollection
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
        return $this->_addWebshopItemVariant($instance, $idCheck);
    }

    /**
     * @param WebshopItemVariant $webshopItemVariant
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _addWebshopItemVariant( WebshopItemVariant $webshopItemVariant, $idCheck = FALSE ) {
        return $this->_add($webshopItemVariant, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeWebshopItemVariant($instance,$idCheck);
    }

    /**
     * @param WebshopItemVariant $webshopItemVariant
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _removeWebshopItemVariant( WebshopItemVariant $webshopItemVariant, $idCheck = FALSE ) {
        return $this->_remove($webshopItemVariant,$idCheck);
    }

}