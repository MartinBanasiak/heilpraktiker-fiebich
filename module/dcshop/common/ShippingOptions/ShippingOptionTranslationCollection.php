<?php
namespace DynCom\dc\dcShop\ShippingOptions;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Class ShippingOptionTranslationCollection
 */
class ShippingOptionTranslationCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param ShippingOptionTranslationConfig                                     $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct(ShippingOptionTranslationConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return ShippingOptionTranslationCollection
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
        return $this->_addShippingOptionTranslation($instance, $idCheck);
    }

    /**
     * @param ShippingOptionTranslation $shippingOptionTranslation
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _addShippingOptionTranslation(ShippingOptionTranslation $shippingOptionTranslation, $idCheck = FALSE ) {
        return $this->_add($shippingOptionTranslation, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeShippingOptionTranslation($instance,$idCheck);
    }

    /**
     * @param ShippingOptionTranslation $shippingOptionTranslation
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _removeShippingOptionTranslation(ShippingOptionTranslation $shippingOptionTranslation, $idCheck = FALSE ) {
        return $this->_remove($shippingOptionTranslation,$idCheck);
    }

}