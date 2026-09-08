<?php
namespace DynCom\dc\dcShop\rma\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;
use DynCom\dc\dcShop\classes\WebshopItemConfig;

/**
 * Class RetShipmentCollection
 */
class RetShipmentLineCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    protected $docConfig;
    protected $itemConfig;

    /**
     * @param RetShipmentLineConfig $config
     * @param RetShipmentConfig $docConfig
     * @param WebshopItemConfig $itemConfig
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( RetShipmentLineConfig $config, RetShipmentConfig $docConfig, WebshopItemConfig $itemConfig, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  = new \SplObjectStorage();
        $this->config                    = $config;
        $this->docConfig                 = $docConfig;
        $this->itemConfig                = $itemConfig;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return RetShipmentLineCollection
     */
    public function getEmptyCollection() {
        return new self($this->config, $this->docConfig, $this->itemConfig, $this->criteriaValidationService);
    }


    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function add( $instance, $idCheck = FALSE ) {
        return $this->_addRetShipmentLine($instance, $idCheck);
    }

    /**
     * @param RetShipmentLine $retShipmentLine
     * @param bool $idCheck
     * @return bool
     */
    protected function _addRetShipmentLine( RetShipmentLine $retShipmentLine, $idCheck = FALSE ) {
        return $this->_add($retShipmentLine, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeRetShipmentLine($instance, $idCheck);
    }

    /**
     * @param RetShipmentLine $retShipmentLine
     * @param bool $idCheck
     * @return bool
     */
    protected function _removeRetShipmentLine( RetShipmentLine $retShipmentLine, $idCheck = FALSE ) {
        return $this->_remove($retShipmentLine, $idCheck);
    }

    /**
     * @return RetShipmentLine
     */
    public function getNullObject() {
        return new RetShipmentLine($this->config, $this->docConfig, $this->itemConfig, $this->criteriaValidationService);
    }
	
	public function getEntryClassName() {
		return RetShipmentLine::class;
	}

}