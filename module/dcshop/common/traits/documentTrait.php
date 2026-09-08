<?php
namespace DynCom\dc\dcShop\traits;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\dcShop\interfaces\DocLineModelDBConfigInterface;
use DynCom\dc\dcShop\interfaces\DocumentModelDBConfigInterface;
use DynCom\dc\dcShop\interfaces\GenericDocumentInterface;

/**
 * Trait documentTrait
 */
trait documentTrait {

    use genericDBModelTrait;

    protected $linesConfig;
    protected $linesClassName;
    protected $linesCriteriaFieldMappings;
    protected $criteriaValidationService;
    protected $lines;
    protected $linesSet = FALSE;


    /**
     * @param DocumentModelDBConfigInterface                     $config
     * @param DocLineModelDBConfigInterface                      $lineConfig
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( DocumentModelDBConfigInterface $config, DocLineModelDBConfigInterface $lineConfig, CriteriaHelperInterface $criteriaValidationService ) {
        $this->config = $config;
        $this->lineConfig = $lineConfig;
        $this->criteriaValidationService = $criteriaValidationService;
    }

    /**
     * @return \IteratorIterator
     */
    public function getIterator() {
        return new \IteratorIterator($this->lines);
    }

    /**
     * @return int
     */
    public function count() {
        return count($this->lines);
    }

    /**
     * @return  array
     */
    public function getLinesCriteriaFieldMappings() {
        return $this->config->getLinesCriteriaFieldMappings();
    }

    /**
     * @return GenericCollectionInterface
     */
    protected function _getLines() {
        return $this->lines;
    }

    /**
     * @return GenericCollectionInterface
     */
    public function getLines() {
        return $this->_getLines();
    }

    /**
     * @param GenericCollectionInterface $lineCollection
     *
     * @return bool
     */
    protected function _setLines( GenericCollectionInterface $lineCollection ) {
        $this->lines = $lineCollection;
        if (count($this->lines) > 0) {
            $this->linesSet = TRUE;
            return TRUE;
        }
        return FALSE;
    }

    /**
     * @param GenericCollectionInterface $lineCollection
     *
     * @return bool
     */
    public function setLines( GenericCollectionInterface $lineCollection ) {
        return $this->_setLines($lineCollection);
    }

    /**
     * @return array
     */
    protected function _getLineCriteriaArray() {
        $mappingSchema = $this->getLinesCriteriaFieldMappings();
        if(!$this->criteriaValidationService->validateCriteriaFieldMappings($this->linesConfig,$this->config,$mappingSchema)) {
            return array();
        }
        $criteriaArray = array();
        foreach($mappingSchema as $fieldMapping) {
            $mapToFieldName = $fieldMapping[0];
            $comparisonOperator = $fieldMapping[1];
            if(isset($fieldMapping[2])) {
                $mapFromField = $fieldMapping[2];
                try {
                    $mapFromValue = $this->$mapFromField;
                } catch(\Exception $e) {
                    return array();
                }
                $criteriaArray[0][] = array($mapToFieldName,$comparisonOperator,$mapFromValue);
            } else {
                $criteriaArray[0][] = array($mapToFieldName,$comparisonOperator);
            }
        }
        return $criteriaArray;
    }

    /**
     * @return array
     */
    public function getLineCriteriaArray() {
        return $this->_getLineCriteriaArray();
    }

    /**
     * @return boolean
     */
    public function isLinesSet() {
        return $this->linesSet;
    }

    /**
     * @return  string
     */
    public function getLinesClassName() {
        return $this->linesClassName;
    }

    /**
     * @return GenericDocumentInterface
     */
    public function getNullObject() {
        return new self($this->config,$this->lineConfig,$this->criteriaValidationService);
    }

}