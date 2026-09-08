<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 08.07.2015
 * Time: 01:36
 */
interface Entity
{

    public function getID();

    public function getAllFieldsAsArray();

    /**
     * @param array $array
     * @return mixed
     */
    public function mapFromArray(array $array);

    public function getAltPrimaryFieldNames();

    public function getAltPrimaryFieldValues();

}