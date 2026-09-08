<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use Psr\Log\LoggerInterface;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 15.01.2015
 * Time: 21:43
 */
class SelectionCriteriaValidationService implements CriteriaHelperInterface {

    /**
     * @param   array                  $allowedComparisons
     * @param   ModelDBConfigInterface $modelConfig
     * @param   array                  $criteriaArray
     *
     * @return  bool
     */

    protected $allowedComparisonOperators = array('IS NULL','IS NOT NULL','=','!=','<','>','>=','<=','IN','LIKE');

    /**
     * @var LoggerInterface|null
     */
    protected $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        if (null === $logger) {
            $logger = get_logger('criteria_helper');
        }
        $this->logger = $logger;
    }

    /**
     * @param \DynCom\dc\common\interfaces\ModelDBConfigInterface $modelConfig
     * @param array                   $criteriaArray
     *
     * @return bool
     */
    public function validateCriteria( ModelDBConfigInterface $modelConfig, array $criteriaArray, array &$errors = [] ) {
        return $this->_validateCriteria($modelConfig, $criteriaArray, $errors);
    }

    /**
     * @param \DynCom\dc\common\interfaces\ModelDBConfigInterface $modelConfig
     * @param array                   $criteriaArray
     *
     * @return bool
     */
    protected function _validateCriteria(ModelDBConfigInterface $modelConfig, array $criteriaArray, array &$errors) {
        $fields = $modelConfig->getMappedFields();
        $entityName = $modelConfig->getModelClassName();
        $fieldNames = array();
        foreach ($fields as $fieldArr) {
            $fieldNames[] = $fieldArr['name'];
        }
        $depth = (int)array_depth($criteriaArray);
        if (3 !== $depth) {
            $errors[] = 'Criteria-array does not have depth 3, has [' . $depth . ']: ' . print_r($criteriaArray,1);
            return FALSE;
        }


        $disjunctIndex = -1;
        $hasError = false;
        foreach ($criteriaArray as $disjunct) {
            $disjunctIndex++;
            $conjunctIndex = -1;
            foreach ($disjunct as $conjunct) {
                $conjunctIndex++;
                $conjunctIsArray = \is_array($conjunct);
                if (!$conjunctIsArray) {
                    $errors[] = 'Conjunct with index [' . $conjunctIndex . '] in disjunct with index [' . $disjunctIndex . '] is not an array.';
                    $hasError = true;
                    continue;
                }
                $conjunctCount = \count($conjunct);
                $conjunctHasNullCondition = array_key_exists(1, $conjunct) && ('IS NOT NULL' === $conjunct[1] || 'IS NULL' === $conjunct[1]);
                if (($conjunctHasNullCondition && 2 !== $conjunctCount) || (!$conjunctHasNullCondition && 3 !== $conjunctCount)) {
                    $errors[] = 'Conjunct with index [' . $conjunctIndex . '] in disjunct with index [' . $disjunctIndex . '] must have exactly three entries, unless the operator is \'IS NULL\' or \'IS NOT NULL\', then it must have 2 entries.';
                    $hasError = true;
                    continue;
                }
                $conjunctHasInCondition = array_key_exists(1,$conjunct) && 'IN' === $conjunct[1];
                if ($conjunctHasInCondition && !\is_array($conjunct[2])) {
                    $hasError = true;
                    $errors[] = 'Conjunct with index [' . $conjunctIndex . '] in disjunct with index [' . $disjunctIndex . '] has \'IN\' Operator, but last value is not an array.';

                }
                if (!\in_array($conjunct[0],$fieldNames,true)) {
                    $hasError = true;
                    $errors[] = 'Conjunct with index [' . $conjunctIndex . '] in disjunct with index [' . $disjunctIndex . '] has invalid field name [' . $conjunct[0] . '].';
                }

                if (!\in_array($conjunct[1],$this->allowedComparisonOperators,true) || !\in_array($conjunct[1],$modelConfig->getAllowedComparisonOperators(),true)) {
                    $hasError = true;
                    $errors[] = 'Conjunct with index [' . $conjunctIndex . '] in disjunct with index ['     . $disjunctIndex . '] has invalid operator name [' . $conjunct[1] . '].';
                }

            }
        }
        if (\count($errors) > 0) {
            $errors[] = 'Criteria: ' . print_r($criteriaArray,1);
            $this->logger->error('Entity [' . $entityName . '] - criteria errors: ' . print_r($errors,1));
        }
        return !$hasError;
    }

    /**
     * @param \DynCom\dc\common\interfaces\ModelDBConfigInterface $mapToConfig
     * @param \DynCom\dc\common\interfaces\ModelDBConfigInterface $mapFromConfig
     * @param array                   $criteriaFieldMappings
     *
     * @return bool
     * @internal param array $allowedComparisons
     */
    protected function _validateCriteriaFieldMappings( ModelDBConfigInterface $mapToConfig, ModelDBConfigInterface $mapFromConfig, array $criteriaFieldMappings ) {

        $mapToFields     = $mapToConfig->getMappedFields();
        $mapToFieldNames = array();
        foreach ($mapToFields as $toFieldArr) {
            $mapToFieldNames[] = $toFieldArr['name'];
        }

        $mapFromFields     = $mapFromConfig->getMappedFields();
        $mapFromFieldNames = array();
        foreach ($mapFromFields as $fromFieldArr) {
            $mapFromFieldNames[] = $fromFieldArr['name'];
        }

        foreach ($criteriaFieldMappings as $fieldMapping) {
            if (
                   ($mapFromConfig->getAllowedComparisonOperators() !== $mapToConfig->getAllowedComparisonOperators())
                || (count($fieldMapping) < 2)
                || (count($fieldMapping) > 3)
                || (count($fieldMapping) > 2 && ($fieldMapping[1] == 'IS NULL' || $fieldMapping[1] == 'IS NOT NULL'))
                || ($fieldMapping[1] == 'IN' && !(is_array($fieldMapping[2])))
                || !(in_array($fieldMapping[0], $mapToFieldNames))
                || !(in_array($fieldMapping[1], $this->allowedComparisonOperators))
                || !(in_array($fieldMapping[1], $mapFromConfig->getAllowedComparisonOperators()))
                || ((count($fieldMapping) > 2) && !(in_array($fieldMapping[2], $mapFromFieldNames)))
            ) {
                return FALSE;
            }
        }

        return TRUE;

    }

    /**
     * @param \DynCom\dc\common\interfaces\ModelDBConfigInterface $mapToConfig
     * @param \DynCom\dc\common\interfaces\ModelDBConfigInterface $mapFromConfig
     * @param array                   $criteriaFieldMappings
     *
     * @return bool
     * @internal param array $allowedComparisons
     */
    public function validateCriteriaFieldMappings( ModelDBConfigInterface $mapToConfig, ModelDBConfigInterface $mapFromConfig, array $criteriaFieldMappings ) {

        return $this->_validateCriteriaFieldMappings($mapToConfig,$mapFromConfig,$criteriaFieldMappings);

    }
}