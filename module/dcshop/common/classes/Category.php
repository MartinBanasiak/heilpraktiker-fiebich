<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;
use DynCom\dc\dcShop\interfaces\CategoryInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 19.01.2015
 * Time: 14:08
 */

/**
 * Class Category
 */
class Category implements CategoryInterface
{

    use genericDBModelTrait, universallyGettableTrait;

    public const SHOW_SUBCATEGORY_OPTION_NONE = 0;
    public const SHOW_SUBCATEGORY_OPTION_DEFAULT = 1;
    public const SHOW_SUBCATEGORY_OPTION_WITH_ITEMS = 2;

    protected $id;
    protected $company;
    protected $shop_code;
    protected $language_code;
    protected $line_no;
    protected $parent_line_no;
    protected $sorting;
    protected $level;
    protected $name;
    protected $root_line_no;
    protected $code;
    protected $show_random_items;
    protected $no_of_random_items;
    protected $show_campain_items;
    protected $no_of_campain_items;
    protected $show_all_items;
    protected $user_sorting;
    protected $sort_items;
    protected $show_sub_categorys;
    protected $category_picture;
    protected $category_icon;
    protected $category_description;
    protected $category_description_2;
    protected $category_description_excerpt;
    protected $active;
    protected $validity_from;
    protected $validity_to;
    protected $promotion_active;
    protected $promotion_validity_from;
    protected $promotion_validity_to;
    protected $promotion_label;
    protected $promotion_description;
    protected $search_query;
    protected $meta_keywords;
    protected $meta_description;
    protected $site_titel;
    protected $filter_active;
    protected $max_no_of_filter;
    protected $in_use_with_page_id;
    protected $hide_category_sub_navigation;
    protected $to_delete;

    /**
     * @param CategoryConfig $config
     */
    public function __construct( CategoryConfig $config ) {
        $this->config = $config;
    }

    /**
     * @return Category
     */
    public function getNullObject() {
        return new self($this->config);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return mixed
     */
    public function getShopCode()
    {
        return $this->shop_code;
    }

    /**
     * @return mixed
     */
    public function getLanguageCode()
    {
        return $this->language_code;
    }

    /**
     * @return mixed
     */
    public function getLineNo()
    {
        return $this->line_no;
    }

    /**
     * @return mixed
     */
    public function getParentLineNo()
    {
        return $this->parent_line_no;
    }

    /**
     * @return mixed
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getRootLineNo()
    {
        return $this->root_line_no;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getShowRandomItems()
    {
        return $this->show_random_items;
    }

    /**
     * @return mixed
     */
    public function getNoOfRandomItems()
    {
        return $this->no_of_random_items;
    }

    /**
     * @return mixed
     */
    public function getShowCampainItems()
    {
        return $this->show_campain_items;
    }

    /**
     * @return mixed
     */
    public function getNoOfCampainItems()
    {
        return $this->no_of_campain_items;
    }

    /**
     * @return mixed
     */
    public function getShowAllItems()
    {
        return $this->show_all_items;
    }

    /**
     * @return mixed
     */
    public function getUserSorting()
    {
        return $this->user_sorting;
    }

    /**
     * @return mixed
     */
    public function getSortItems()
    {
        return $this->sort_items;
    }

    /**
     * @return mixed
     */
    public function getShowSubCategorys()
    {
        return $this->show_sub_categorys;
    }

    /**
     * @return mixed
     */
    public function getCategoryPicture()
    {
        return $this->category_picture;
    }

    /**
     * @return mixed
     */
    public function getCategoryIcon()
    {
        return $this->category_icon;
    }

    /**
     * @return mixed
     */
    public function getCategoryDescription()
    {
        return $this->category_description;
    }

    /**
     * @return mixed
     */
    public function getCategoryDescription2()
    {
        return $this->category_description_2;
    }

    /**
     * @return mixed
     */
    public function getCategoryDescriptionExcerpt()
    {
        return $this->category_description_excerpt;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @return mixed
     */
    public function getValidityFrom()
    {
        return $this->validity_from;
    }

    /**
     * @return mixed
     */
    public function getValidityTo()
    {
        return $this->validity_to;
    }

    /**
     * @return mixed
     */
    public function getPromotionActive()
    {
        return $this->promotion_active;
    }

    /**
     * @return mixed
     */
    public function getPromotionValidityFrom()
    {
        return $this->promotion_validity_from;
    }

    /**
     * @return mixed
     */
    public function getPromotionValidityTo()
    {
        return $this->promotion_validity_to;
    }

    /**
     * @return mixed
     */
    public function getPromotionLabel()
    {
        return $this->promotion_label;
    }

    /**
     * @return mixed
     */
    public function getPromotionDescription()
    {
        return $this->promotion_description;
    }

    /**
     * @return mixed
     */
    public function getSearchQuery()
    {
        return $this->search_query;
    }

    /**
     * @return mixed
     */
    public function getMetaKeywords()
    {
        return $this->meta_keywords;
    }

    /**
     * @return mixed
     */
    public function getMetaDescription()
    {
        return $this->meta_description;
    }

    /**
     * @return mixed
     */
    public function getSiteTitel()
    {
        return $this->site_titel;
    }

    /**
     * @return mixed
     */
    public function getFilterActive()
    {
        return $this->filter_active;
    }

    /**
     * @return mixed
     */
    public function getMaxNoOfFilter()
    {
        return $this->max_no_of_filter;
    }

    /**
     * @return mixed
     */
    public function getToDelete()
    {
        return $this->to_delete;
    }


    public function getSubcategoryDisplayOption()
    {
        return (int)$this->show_sub_categorys;
    }

}