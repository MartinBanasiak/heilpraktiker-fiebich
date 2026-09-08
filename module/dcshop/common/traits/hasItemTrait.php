<?php
namespace DynCom\dc\dcShop\traits;
use DynCom\dc\dcShop\classes\WebshopItem;

/**
 * Class hasItemTrait
 */
trait hasItemTrait {

    /**
     * @param null $shopPrimary
     *
     * @return bool|null|WebshopItem
     */
    public function getItem( $shopPrimary = NULL ) {
        return $this->_getItem($shopPrimary);
    }

    /**
     * @param null $shopPrimary
     *
     * @return bool|null|WebshopItem
     */
    protected function _getItem( $shopPrimary = NULL ) {

        $itemIDField      = (empty(static::$itemIDField) ? NULL : static::$itemIDField);
        $itemIDProperty   = (empty(static::$itemIDProperty) ? NULL : static::$itemIDProperty);
        $itemNoField      = (empty(static::$itemNoField) ? NULL : static::$itemNoField);
        $itemNoProperty   = (empty(static::$itemNoProperty) ? NULL : static::$itemNoProperty);
        $itemType         = (empty(static::$itemType) ? NULL : static::$itemType);
        $itemTypeField    = (empty(static::$itemTypeField) ? NULL : static::$itemTypeField);
        $itemTypeProperty = (empty(static::$itemTypeProperty) ? NULL : static::$itemTypeProperty);

        if (isset($itemType) && isset($itemTypeField) && isset($itemTypeProperty)) {
            $propName = $itemTypeProperty;
            if ($this->$propName != $itemType) {
                return FALSE;
            }
        }
        if (
            (
                empty($itemNoField)
                ||
                empty($itemNoProperty)
            )
            &&
            (
                empty($itemIDField)
                ||
                empty($itemIDProperty)
            )
        ) {
            throw new \Exception('No Fieldname and Property-Name for Item-identifier defined');
            return NULL;
        }

        if (!empty($itemNoField) && (empty($shopPrimary['company']) || empty($shopPrimary['shop_code']) || empty($shopPrimary['langauge_code']))) {
            return FALSE;
        }

        if (empty($itemNoField)) {
            $propName = $itemIDProperty;
            if (empty($this->$propName)) {
                return FALSE;
            }
            $item  = new WebshopItem($this->db);
            $tmpID = (int)$this->$propName;
            if ($item->setByID($tmpID)) {
                return $item;
            }
            return FALSE;
        }
        $propName = $itemNoProperty;
        if (empty($this->$propName) || empty($shopPrimary['company']) || empty($shopPrimary['shop_code']) || empty($shopPrimary['langauge_code'])) {
            throw new \Exception('No Item primary');
            return NULL;
        }
        $item       = new WebshopItem($this->db);
        $itemKeyArr = array('company' => $shopPrimary['company'], 'shop_code' => $shopPrimary['shop_code'], 'language_code' => $shopPrimary['langauge_code'], 'item_no' => $this->$propName);
        if ($item->setByAltPrimary($itemKeyArr)) {
            return $item;
        }
        return FALSE;
    }
}