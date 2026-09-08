<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Class CouponLineCollection
 */
class CouponLineCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param CouponLineConfig                                     $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( CouponLineConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return CouponLineCollection
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
        return $this->_addCouponLine($instance, $idCheck);
    }

    /**
     * @param CouponLine $couponLine
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _addCouponLine( CouponLine $couponLine, $idCheck = FALSE ) {
        return $this->_add($couponLine, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeCouponLine($instance,$idCheck);
    }

    /**
     * @param CouponLine $couponLine
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _removeCouponLine( CouponLine $couponLine, $idCheck = FALSE ) {
        return $this->_remove($couponLine,$idCheck);
    }

}