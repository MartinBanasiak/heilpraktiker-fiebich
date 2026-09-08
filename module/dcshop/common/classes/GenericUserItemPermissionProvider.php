<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\dcShop\interfaces\CustomerWithPermissionGroupsInterface;
use DynCom\dc\dcShop\interfaces\UserItemPermissionProvider;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 10/11/2015
 * Time: 10:05 PM
 */
class GenericUserItemPermissionProvider implements UserItemPermissionProvider
{

    const UNAVAILABLE_REASON_NOT_LOGGED_IN = 'permission_error_visitor_not_logged_in';
    const UNAVAILABLE_REASON_NO_SHOP_PERMISSION = 'permission_error_no_shop_permission';
    const UNAVAILABLE_REASON_ITEM_NOT_PERMITTED = 'permission_error_item_not_permitted';
    const UNAVAILABLE_REASON_USER_NO_ORDER_PERMISSION = 'permission_error_no_order_permission';

    private $shop;
    private $customerRepository;
    private $db;
    private $shopLanguageCode;
    private $cachedItemPermissions = [];
	/**
	* @var WebshopItemBuilder
	*/
	private $itemBuilder;

    /**
     * @var CurrShopConfiguration
     */
    private $currShopConfiguration;

    /**
     * GenericUserItemPermissionProvider constructor.
     * @param Shop $shop
     * @param CustomerRepository $customerRepository
     * @param PDOQueryWrapper $db
     * @param WebshopItemBuilder $itemBuilder
     * @param CurrShopConfiguration $currShopConfiguration
     */
    public function __construct(Shop $shop, CustomerRepository $customerRepository, PDOQueryWrapper $db, WebshopItemBuilder $itemBuilder, CurrShopConfiguration $currShopConfiguration)
    {
        $this->shop = $shop;
        $this->customerRepository = $customerRepository;
        $this->db = $db;
		$this->itemBuilder = $itemBuilder;
        $this->currShopConfiguration = $currShopConfiguration;
    }

    /**
     * @param User $user
     * @param CustomerWithPermissionGroupsInterface $customer
     * @param WebshopItemInterface $webshopItem
     * @param null $failureReason
     * @return bool
     */
    public function userHasItemPermission(User $user, CustomerWithPermissionGroupsInterface $customer, WebshopItemInterface $webshopItem, &$failureReason = null)
    {
        $itemID = $webshopItem->getID();
        $unavailableReason = null;
        if(!$this->shop->isB2C()) {
            if(!($user->getID() > 0) || empty($user->customer_no) || ($user->shop_code !== (NULL === $this->shop->customer_source ? $this->shop->getCode() : $this->shop->customer_source))) {
                    $unavailableReason = self::UNAVAILABLE_REASON_NOT_LOGGED_IN;
            } elseif(!$user->hasRightOrder()) {
                $unavailableReason = self::UNAVAILABLE_REASON_USER_NO_ORDER_PERMISSION;
            } else {
                $key = md5($webshopItem->getItemNo() . '|' . $customer->getCustomerNo());
                if(array_key_exists($key,$this->cachedItemPermissions)) {
                    if($this->cachedItemPermissions[$key] === false) {
                        $unavailableReason = self::UNAVAILABLE_REASON_ITEM_NOT_PERMITTED;
                    }
                } else {
                    $isPermitted = (bool)$this->isPermissionLinkPermitted($customer,$webshopItem);
                    $this->cachedItemPermissions[$key] = $isPermitted;
                    if(!$isPermitted) {
                        $unavailableReason = self::UNAVAILABLE_REASON_ITEM_NOT_PERMITTED;
                    }
                }
            }
        }
        $failureReason = $unavailableReason;
        return ($unavailableReason === null);
    }

    /**
     * @param CustomerWithPermissionGroupsInterface $customer
     * @param WebshopItemInterface $item
     * @return bool
     */
    private function isPermissionLinkPermitted(CustomerWithPermissionGroupsInterface $customer, WebshopItemInterface $item)
    {

        $groupSnippet = '';
        $permissionGroups = $customer->getPermissionGroupCodes();
        foreach($permissionGroups as $permissionGroup) {
            $groupSnippet .= ' OR permission_group_code = \'' . $permissionGroup . '\'';
        }


        $query = '
          SELECT
            1
          FROM
            DUAL
          WHERE
           (
              NOT EXISTS(
                SELECT
                  1
                FROM
                  shop_permissions_group_link
                WHERE
                  company = \'' . $this->shop->company . '\' AND
                  item_no = \'' . $item->getItemNo() . '\'
              )
            ) OR
           (
              EXISTS(
                SELECT
                  1
                FROM
                  shop_permissions_group_link
                WHERE
                  company = \'' . $this->shop->company . '\' AND
                  item_no = \'' . $item->getItemNo() . '\' AND
                  (
                    customer_no = \'' . $customer->getCustomerNo() . '\'
                    ' . $groupSnippet . '
                  )
              )
            )';
        $this->db->setQuery($query)->doQuery();
        return ($this->db->getNoOfReturnedRows() > 0);
    }



}