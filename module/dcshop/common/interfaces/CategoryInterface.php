<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 14.09.2017
 * Time: 11:08
 */

namespace DynCom\dc\dcShop\interfaces;

use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;

/**
 * Class Category
 */
interface CategoryInterface extends GenericDBModelInterface, Entity
{

    /**
     * @return mixed
     */
    public function getCompany();

    /**
     * @return mixed
     */
    public function getShopCode();

    /**
     * @return mixed
     */
    public function getLanguageCode();

    /**
     * @return mixed
     */
    public function getLineNo();

    /**
     * @return mixed
     */
    public function getParentLineNo();

    /**
     * @return mixed
     */
    public function getSorting();

    /**
     * @return mixed
     */
    public function getLevel();

    /**
     * @return mixed
     */
    public function getName();

    /**
     * @return mixed
     */
    public function getRootLineNo();

    /**
     * @return mixed
     */
    public function getCode();

    /**
     * @return mixed
     */
    public function getShowRandomItems();

    /**
     * @return mixed
     */
    public function getNoOfRandomItems();

    /**
     * @return mixed
     */
    public function getShowCampainItems();

    /**
     * @return mixed
     */
    public function getNoOfCampainItems();

    /**
     * @return mixed
     */
    public function getShowAllItems();

    /**
     * @return mixed
     */
    public function getUserSorting();

    /**
     * @return mixed
     */
    public function getSortItems();

    /**
     * @return mixed
     */
    public function getShowSubCategorys();

    /**
     * @return mixed
     */
    public function getCategoryPicture();

    /**
     * @return mixed
     */
    public function getCategoryIcon();

    /**
     * @return mixed
     */
    public function getCategoryDescription();

    /**
     * @return mixed
     */
    public function getCategoryDescription2();

    /**
     * @return mixed
     */
    public function getActive();

    /**
     * @return mixed
     */
    public function getValidityFrom();

    /**
     * @return mixed
     */
    public function getValidityTo();

    /**
     * @return mixed
     */
    public function getPromotionActive();

    /**
     * @return mixed
     */
    public function getPromotionValidityFrom();

    /**
     * @return mixed
     */
    public function getPromotionValidityTo();

    /**
     * @return mixed
     */
    public function getPromotionLabel();

    /**
     * @return mixed
     */
    public function getPromotionDescription();

    /**
     * @return mixed
     */
    public function getSearchQuery();

    /**
     * @return mixed
     */
    public function getMetaKeywords();

    /**
     * @return mixed
     */
    public function getMetaDescription();

    /**
     * @return mixed
     */
    public function getSiteTitel();

    /**
     * @return mixed
     */
    public function getFilterActive();

    /**
     * @return mixed
     */
    public function getMaxNoOfFilter();

    /**
     * @return mixed
     */
    public function getToDelete();
}