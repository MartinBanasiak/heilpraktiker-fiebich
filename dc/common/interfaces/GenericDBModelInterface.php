<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 19.01.2015
 * Time: 00:22
 */

interface GenericDBModelInterface {

    /**
     * @return int|null
     */
    public function getID();

    /**
     * @return ModelDBConfigInterface
     */
    public function getConfig();

    /**
     * @return mixed NullObject for ModelClass
     *               If the class has no methods (with exception of construct, getters and setters),
     *               it suffices to pass a new instance without setting its values.
     *               Class methods that depend on data have to check whether those are set first.
     */
    public function getNullObject();

    /**
     * @param array $fields
     * @return array
     */
    public function getFieldData(array $fields);

}