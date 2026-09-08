<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Class CouponHeaderCollection
 */
class CouponHeaderCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param CouponHeaderConfig                                     $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( CouponHeaderConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return CouponHeaderCollection
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
        return $this->_addCouponHeader($instance, $idCheck);
    }

    /**
     * @param CouponHeader $couponHeader
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _addCouponHeader( CouponHeader $couponHeader, $idCheck = FALSE ) {
        return $this->_add($couponHeader, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeCouponHeader($instance,$idCheck);
    }

    /**
     * @param CouponHeader $couponHeader
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _removeCouponHeader( CouponHeader $couponHeader, $idCheck = FALSE ) {
        return $this->_remove($couponHeader,$idCheck);
    }

}