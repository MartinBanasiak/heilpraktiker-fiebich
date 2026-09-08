<?php
namespace DynCom\dc\dcShop\interfaces;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 14.01.2015
 * Time: 02:33
 */
interface DocLineModelDBConfigInterface extends ModelDBConfigInterface {

    /**
     * @return string
     */
    public function getDocClassName();

    /**
     * @return array
     */
    public function getDocCriteriaFieldMappings();

    /**
     * @return int
     */
    public function getItemType();

    /**
     * @return array
     */
    public function getItemCriteriaFieldMappings();

}