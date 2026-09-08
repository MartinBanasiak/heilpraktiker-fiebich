<?php
namespace DynCom\dc\dcShop\Document;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:20
 */
class CreditMemoCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    protected $linesConfig;

    /**
     * @param CreditMemoConfig                                      $config
     * @param CreditMemoLineConfig                                  $linesConfig
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( CreditMemoConfig $config, CreditMemoLineConfig $linesConfig, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->linesConfig               = $linesConfig;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return CreditMemoCollection
     */
    public function getEmptyCollection() {
        return new self($this->config,$this->linesConfig,$this->criteriaValidationService);
    }


    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function add( $instance, $idCheck = FALSE ) {
        return $this->_addCreditMemo($instance, $idCheck);
    }

    /**
     * @param CreditMemoDocument $creditMemo
     * @param bool             $idCheck
     *
     * @return bool
     */
    protected function _addCreditMemo( CreditMemoDocument $creditMemo, $idCheck = FALSE ) {
        return $this->_add($creditMemo, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeCreditMemo($instance,$idCheck);
    }

    /**
     * @param CreditMemoDocument $creditMemo
     * @param bool             $idCheck
     *
     * @return bool
     */
    protected function _removeCreditMemo( CreditMemoDocument $creditMemo, $idCheck = FALSE ) {
        return $this->_remove($creditMemo,$idCheck);
    }

    /**
     * @return CreditMemoDocument
     */
    public function getNullObject() {
        return new CreditMemoDocument($this->config,$this->linesConfig,$this->criteriaValidationService);
    }

}