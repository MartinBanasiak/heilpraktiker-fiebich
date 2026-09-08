<?php
namespace DynCom\dc\dcShop\campaigns;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;
use DynCom\dc\dcShop\interfaces\WebshopItemWithCategories;

/**
 * Class GenericCampaignElementCollection
 */
class GenericCampaignElementCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param GenericCampaignElementConfig                                    $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct(GenericCampaignElementConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return GenericCampaignElementCollection
     */
    public function getEmptyCollection() {
        return new self($this->config,$this->criteriaValidationService);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function add( $instance, $idCheck = FALSE ) {
        return $this->_addGenericCampaignElement($instance, $idCheck);
    }

    /**
     * @param GenericCampaignElement $genericCampaignElement
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _addGenericCampaignElement( GenericCampaignElement $genericCampaignElement, $idCheck = FALSE ) {
        return $this->_add($genericCampaignElement, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeGenericCampaignElement($instance,$idCheck);
    }

    /**
     * @param GenericCampaignElement $genericCampaignElement
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _removeGenericCampaignElement( GenericCampaignElement $genericCampaignElement, $idCheck = FALSE ) {
        return $this->_remove($genericCampaignElement,$idCheck);
    }

    /**
     * @param WebshopItemWithCategories $item
     * @return bool
     */
    public function itemMeetsCriteria(WebshopItemWithCategories $item)
    {
        $meetsCriteria = false;
        $itemKey = $item->getItemNo() . '|' . $item->getVariantCode();
        $parentItemKey = $item->getParentItemNo() . '|';
        $count = $this->count();
        if ($count === 0) {
            return true;
        }
        $includedItems = [];
        $includedCategories = [];
        $excludedItems = [];
        $excludedCategories = [];
        $hasItemIncludes = false;
        $hasCategoryIncludes = false;
        $hasItemExcludes = false;
        $hasCategoryExcludes = false;
        foreach ($this as $element) {
            if ($element instanceof GenericCampaignElement) {
                if ((int)$element->include_exclude === (int)GenericCampaignElement::SET_OPERATOR_INCLUDE) {
                    if ((int)$element->type === (int)GenericCampaignElement::TYPE_ITEM) {
                        $key = $element->item_no . '|' . $element->item_var_code;
                        $includedItems[] = $key;
                        $hasItemIncludes = true;
                    } else {
                        $catLineNo = (int)$element->category_line_no;
                        $includedCategories[] = (string)$catLineNo;
                        $hasCategoryIncludes = true;
                    }
                } else {
                    if ((int)$element->type === (int)GenericCampaignElement::TYPE_ITEM) {
                        $key = $element->item_no . '|' . $element->item_var_code;
                        $hasItemExcludes = true;
                        $excludedItems[] = $key;
                    } else {
                        $catLineNo = (int)$element->category_line_no;
                        $hasCategoryExcludes = true;
                        $excludedCategories[] = $catLineNo;
                    }
                }
            }
        }

        $itemCategoryLineNos = array_column($item->getCategoryArr(), 'line_no');

        //If there are no inclusive conditions
        //Item will match per default
        //If there are inclusive conditions
        //The default is no match

        if (!$hasItemIncludes && !$hasCategoryIncludes) {
            $meetsCriteria = true;
            if ($hasItemExcludes && (in_array($itemKey, $excludedItems, true) || in_array($parentItemKey,$excludedItems, true))) {
                $meetsCriteria = false;
            }
            if ($hasCategoryExcludes) {
                if (count(array_intersect($itemCategoryLineNos, $excludedCategories)) > 0) {
                    $meetsCriteria = false;
                }
            }
        } else {
            $meetsCriteria = false;
            if (($hasItemIncludes && (in_array($itemKey, $includedItems,false) || in_array($parentItemKey, $includedItems,false)))) {
                $meetsCriteria = true;
            }
            if ($hasCategoryIncludes && count(array_intersect($itemCategoryLineNos, $includedCategories)) > 0) {
                $meetsCriteria = true;
            }
            if ($hasItemExcludes && (in_array($itemKey, $excludedItems,false) || in_array($parentItemKey, $excludedItems,false))) {
                $meetsCriteria = false;
            }
            if ($hasCategoryExcludes && count(array_intersect($itemCategoryLineNos, $excludedCategories)) > 0) {
                $meetsCriteria = false;
            }
        }
        return $meetsCriteria;
    }
}