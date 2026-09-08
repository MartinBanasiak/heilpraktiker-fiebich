<?php
namespace DynCom\dc\dcShop\interfaces;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 14.01.2015
 * Time: 02:34
 */
interface HasItemConfigInterface {

    /**
     * @return string
     */
    public function getItemNoFieldName();

    /**
     * @return string
     */
    public function getItemIDFieldName();

    /**
     * @return string
     */
    public function getItemTypeFieldName();

    /**
     * @return string
     */
    public function getItemType();

}