<?php
namespace DynCom\dc\dcShop\interfaces;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;

/**
 * Class InvoiceLine
 */
interface GenericDocLineInterface extends GenericDBModelInterface {

    /**
     * @return array
     */
    public function getDocCriteriaArray();

    /**
     * @return array
     */
    public function getDocCriteriaFieldMappings();

    /**
     * @return array
     */
    public function getItemCriteriaArray();

    /**
     * @return array
     */
    public function getItemCriteriaFieldMappings();

    /**
     * @return DocumentModelDBConfigInterface
     */
    public function getDocConfig();

    /**
     * @return ModelDBConfigInterface
     */
    public function getItemConfig();

}