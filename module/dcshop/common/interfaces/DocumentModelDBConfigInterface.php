<?php
namespace DynCom\dc\dcShop\interfaces;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 14.01.2015
 * Time: 02:30
 */
interface DocumentModelDBConfigInterface extends ModelDBConfigInterface {

    /**
     * @return  string
     */
    public function getLinesClassName();

    /**
     * @return  array   Array containing a mapping-schema for a selection-criteria-array,
     *                  used by the document repository to build such a selection-criteria-array
     *                  for consumption by the findByCriteria-method of the document-line repository
     *                  so that the document repository can fetch the lines of its documents
     */
    public function getLinesCriteriaFieldMappings();

}