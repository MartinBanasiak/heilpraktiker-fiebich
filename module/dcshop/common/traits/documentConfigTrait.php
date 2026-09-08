<?php
namespace DynCom\dc\dcShop\traits;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 19.01.2015
 * Time: 11:23
 */

trait documentConfigTrait {

    use genericConfigTrait;

    /**
     * @return  string
     */
    public function getLinesClassName() {
        return $this->linesClassName;
    }

    /**
     * @return  array   Array containing a mapping-schema for a selection-criteria-array,
     *                  used by the document repository to build such a selection-criteria-array
     *                  for consumption by the findByCriteria-method of the document-line repository
     *                  so that the document repository can fetch the lines of its documents
     */
    public function getLinesCriteriaFieldMappings() {
        return $this->linesCriteriaFieldMappings;
    }
}