<?php
namespace DynCom\dc\dcShop\rma\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Class RetShipmentCollection
 */
class RetShipmentCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    protected $linesConfig;

    /**
     * @param RetShipmentConfig $config
     * @param RetShipmentLineConfig $linesConfig
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( RetShipmentConfig $config, RetShipmentLineConfig $linesConfig, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  = new \SplObjectStorage();
        $this->config                    = $config;
        $this->linesConfig               = $linesConfig;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return RetShipmentCollection
     */
    public function getEmptyCollection() {
        return new self($this->config, $this->linesConfig, $this->criteriaValidationService);
    }


    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function add( $instance, $idCheck = FALSE ) {
        return $this->_addRetShipment($instance, $idCheck);
    }

    /**
     * @param RetShipmentDocument $retShipment
     * @param bool $idCheck
     * @return bool
     */
    protected function _addRetShipment( RetShipmentDocument $retShipment, $idCheck = FALSE ) {
        return $this->_add($retShipment, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeRetShipment($instance, $idCheck);
    }

    /**
     * @param RetShipmentDocument $retShipment
     * @param bool $idCheck
     * @return bool
     */
    protected function _removeRetShipment( RetShipmentDocument $retShipment, $idCheck = FALSE ) {
        return $this->_remove($retShipment, $idCheck);
    }

    /**
     * @return RetShipmentDocument
     */
    public function getNullObject() {
        return new RetShipmentDocument($this->config, $this->linesConfig, $this->criteriaValidationService);
    }
	
	public function getEntryClassName() {
		return RetShipmentDocument::class;
	}

}