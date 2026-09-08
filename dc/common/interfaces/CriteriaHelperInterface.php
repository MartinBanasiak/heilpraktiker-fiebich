<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 15.01.2015
 * Time: 23:16
 */

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 15.01.2015
 * Time: 21:43
 */
interface CriteriaHelperInterface {

    /**
     * @param ModelDBConfigInterface $modelConfig
     * @param array $criteriaArray
     * @param array $errors
     * @return mixed
     */
    public function validateCriteria( ModelDBConfigInterface $modelConfig, array $criteriaArray, array &$errors = []);

    /**
     * @param \DynCom\dc\common\interfaces\ModelDBConfigInterface $mapToConfig
     * @param \DynCom\dc\common\interfaces\ModelDBConfigInterface $mapFromConfig
     * @param array                   $criteriaFieldMappings
     *
     * @return bool
     */
    public function validateCriteriaFieldMappings( ModelDBConfigInterface $mapToConfig, ModelDBConfigInterface $mapFromConfig, array $criteriaFieldMappings );

}