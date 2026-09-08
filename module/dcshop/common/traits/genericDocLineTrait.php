<?php
namespace DynCom\dc\dcShop\traits;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\dcShop\interfaces\DocLineModelDBConfigInterface;
use DynCom\dc\dcShop\interfaces\DocumentModelDBConfigInterface;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 19.01.2015
 * Time: 03:46
 */
trait genericDocLineTrait {

    use genericDBModelTrait;

    protected $docConfig;
    protected $itemConfig;

    /**
     * genericDocLineTrait constructor.
     * @param DocLineModelDBConfigInterface $config
     * @param DocumentModelDBConfigInterface $docConfig
     * @param ModelDBConfigInterface $itemConfig
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( DocLineModelDBConfigInterface $config, DocumentModelDBConfigInterface $docConfig, ModelDBConfigInterface $itemConfig, CriteriaHelperInterface $criteriaValidationService ) {
        $this->config                    = $config;
        $this->docConfig                 = $docConfig;
        $this->itemConfig                = $itemConfig;
        $this->criteriaValidationService = $criteriaValidationService;
    }

    /**
     * @return array
     */
    public function getDocCriteriaArray() {

        $mappingSchema = $this->config->getDocCriteriaFieldMappings();
        if (!$this->criteriaValidationService->validateCriteriaFieldMappings($this->docConfig, $this->config, $mappingSchema)) {
            return array();
        }
        $criteriaArray = array();
        foreach ($mappingSchema as $fieldMapping) {
            $mapToFieldName     = $fieldMapping[0];
            $comparisonOperator = $fieldMapping[1];
            if (isset($fieldMapping[2])) {
                $mapFromField = $fieldMapping[2];
                try {
                    $mapFromValue = $this->$mapFromField;
                }
                catch (\Exception $e) {
                    return array();
                }
                $criteriaArray[0][] = array($mapToFieldName, $comparisonOperator, $mapFromValue);
            } else {
                $criteriaArray[0][] = array($mapToFieldName, $comparisonOperator);
            }
        }
        return $criteriaArray;
    }

    /**
     * @return array
     */
    public function getDocCriteriaFieldMappings() {
        return $this->config->getDocCriteriaFieldMappings();
    }

    /**
     * @return array
     */
    public function getItemCriteriaArray() {

        if ($this->type !== $this->config->getItemType()) {
            return array();
        }
        $mappingSchema = $this->getItemCriteriaFieldMappings();
        if (!$this->criteriaValidationService->validateCriteriaFieldMappings($this->itemConfig, $this->config, $mappingSchema)) {
            return array();
        }
        $criteriaArray = array();
        foreach ($mappingSchema as $fieldMapping) {
            $mapToFieldName     = $fieldMapping[0];
            $comparisonOperator = $fieldMapping[1];
            if (isset($fieldMapping[2])) {
                $mapFromField = $fieldMapping[2];
                try {
                    $mapFromValue = $this->$mapFromField;
                }
                catch (\Exception $e) {
                    return array();
                }
                $criteriaArray[0][] = array($mapToFieldName, $comparisonOperator, $mapFromValue);
            } else {
                $criteriaArray[0][] = array($mapToFieldName, $comparisonOperator);
            }
        }
        return $criteriaArray;

    }

    /**
     * @return array
     */
    public function getItemCriteriaFieldMappings() {
        return $this->config->getItemCriteriaFieldMappings();
    }

    /**
     * @return genericDocLineTrait
     */
    public function getNullObject() {
        return new self($this->config, $this->docConfig, $this->itemConfig, $this->criteriaValidationService);
    }

    /**
     * @return DocumentModelDBConfigInterface
     */
    public function getDocConfig() {
        return $this->docConfig;
    }

    /**
     * @return \DynCom\dc\common\interfaces\ModelDBConfigInterface
     */
    public function getItemConfig() {
        return $this->itemConfig;
    }

}