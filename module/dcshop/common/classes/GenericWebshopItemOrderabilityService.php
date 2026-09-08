<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\dcShop\interfaces\CustomerWithPermissionGroupsInterface;
use DynCom\dc\dcShop\interfaces\ItemAvailabilityProvider;
use DynCom\dc\dcShop\interfaces\UserItemPermissionProvider;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;
use DynCom\dc\dcShop\interfaces\WebshopItemOrderabilityService;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 07.09.2016
 * Time: 14:39
 */
class GenericWebshopItemOrderabilityService implements WebshopItemOrderabilityService
{

    const ERROR_ITEM_NOT_ACTIVE =           'basket_error_item_not_active';
    const ERROR_ITEM_NOT_AVAILABLE =        'basket_error_item_not_available';
    const ERROR_USER_NOT_LOGGED_IN =        'basket_error_user_not_logged_in';
    const ERROR_USER_NO_ORDER_PERMISSION =  'basket_error_user_no_order_permission';
    const ERROR_USER_NO_ITEM_PERMISSION =   'basket_error_no_item_permission';
    const ERROR_ITEM_NOT_LISTED =           'basket_error_item_not_listed';

    /**
     * @var ItemAvailabilityProvider
     */
    private $availabilityProvider;

    /**
     * @var UserItemPermissionProvider
     */
    private $permissionProvider;

    /**
     * GenericWebshopItemOrderabilityService constructor.
     * @param ItemAvailabilityProvider $availabilityProvider
     * @param UserItemPermissionProvider $permissionProvider
     */
    public function __construct(ItemAvailabilityProvider $availabilityProvider, UserItemPermissionProvider $permissionProvider)
    {
        $this->availabilityProvider = $availabilityProvider;
        $this->permissionProvider = $permissionProvider;
    }

    /**
     * @param User $user
     * @param CustomerWithPermissionGroupsInterface $customer
     * @param WebshopItemInterface $webshopItem
     * @param string|null $error
     * @return bool
     */
    public function isWebshopItemOrderable(
        User $user,
        CustomerWithPermissionGroupsInterface $customer,
        WebshopItemInterface $webshopItem,
        &$error = null
    ) {
        $error = null;
        if (!$webshopItem->isActive()) {
            $error = self::ERROR_ITEM_NOT_ACTIVE;
            return false;
        }
        if (!$this->availabilityProvider->isItemOrderable($webshopItem)) {
            $error = self::ERROR_ITEM_NOT_AVAILABLE;
            return false;
        }
        if (!$this->permissionProvider->userHasItemPermission($user, $customer, $webshopItem)) {
            $error = self::ERROR_USER_NO_ITEM_PERMISSION;
            return false;
        }
        return true;
    }

}