<?php
namespace DynCom\dc\dcShop\classes;

use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\traits\hookableTrait;
use DynCom\dc\dcShop\interfaces\ItemAvailabilityProvider;
use DynCom\dc\dcShop\interfaces\OrderableEntityInterface;
use DynCom\dc\dcShop\interfaces\UserBasket;
use DynCom\dc\dcShop\interfaces\UserItemPermissionProvider;
use DynCom\dc\dcShop\interfaces\WebshopItemOrderabilityService;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 09.10.2015
 * Time: 15:52
 */
class UserBasketListener
{

    use hookableTrait;

    const ERROR_ITEM_NOT_ACTIVE = 'basket_error_item_not_active';
    const ERROR_ITEM_NOT_AVAILABLE = 'basket_error_item_not_available';
    const ERROR_USER_NOT_LOGGED_IN = 'basket_error_user_not_logged_in';
    const ERROR_USER_NO_ORDER_PERMISSION = 'basket_error_user_no_order_permission';
    const ERROR_USER_NO_ITEM_PERMISSION = 'basket_error_no_item_permission';
    const ERROR_ITEM_NOT_LISTED = 'basket_error_item_not_listed';

    private $currShopConfiguration;
    private $basketRepository;
    private $itemBuilder;
    private $currUserBasket;
    private $availabilityProvider;
    private $permissionProvider;

    private $user;
    private $customerWithGroups;

    /**
     * @var CustomizationService
     */
    private $customizationService;

    /**
     * @var GreetingCardService
     */
    private $greetingCardService;

    /**
     * @var WebshopItemOrderabilityService
     */
    private $itemOrderabilityService;

    /**
     * @var PDOQueryWrapper
     */
    private $db;

    private static $orderItemsQuery = '
                    SELECT shop_item.id,shop_sales_line.quantity,shop_sales_line.variant_code 
                    FROM shop_sales_line 
                    LEFT JOIN shop_item ON 
                    shop_item.item_no = shop_sales_line.item_no AND 
                    shop_item.company = :shop_company AND 
                    shop_item.shop_code = :shop_code AND 
                    shop_item.language_code = :shop_language_code
                    WHERE 
                    shop_sales_line.order_no = :order_no AND 
                    shop_item.active = 1 AND 
                    (shop_sales_line.company = :shop_company OR shop_sales_line.company = :user_company) AND 
                    shop_sales_line.shop_code = :shop_code AND 
                    shop_sales_line.language_code = :shop_language_code;';

    private static $getItemIdByItemNoQuery = '
                    SELECT id 
                    FROM shop_item
                    WHERE
                    company = :shop_company AND 
                    shop_code = :shop_code AND
                    language_code = :shop_language_code AND 
                    item_no = :item_no AND
                    active = 1
                    LIMIT 1;';


    /**
     * UserBasketListener constructor.
     * @param PDOQueryWrapper $queryWrapper
     * @param CurrShopConfiguration $configuration
     * @param UserBasketRepository $basketRepository
     * @param UserBasket $currUserBasket
     * @param WebshopItemBuilder $itemBuilder
     * @param ItemAvailabilityProvider $availabilityProvider
     * @param UserItemPermissionProvider $permissionProvider
     * @param CustomizationService $customizationService
     * @param GreetingCardService $greetingCardService
     * @param WebshopItemOrderabilityService $itemOrderabilityService
     */
    public function __construct(
        PDOQueryWrapper $queryWrapper,
        CurrShopConfiguration $configuration,
        UserBasketRepository $basketRepository,
        UserBasket $currUserBasket,
        WebshopItemBuilder $itemBuilder,
        ItemAvailabilityProvider $availabilityProvider,
        UserItemPermissionProvider $permissionProvider,
        CustomizationService $customizationService,
        GreetingCardService $greetingCardService,
        WebshopItemOrderabilityService $itemOrderabilityService
    )
    {
        $this->currShopConfiguration = $configuration;
        $this->basketRepository = $basketRepository;
        $this->itemBuilder = $itemBuilder;
        $this->currUserBasket = $currUserBasket;
        $this->availabilityProvider = $availabilityProvider;
        $this->permissionProvider = $permissionProvider;
        $this->user = $configuration->getUser();
        $this->customerWithGroups = new CustomerPermissionGroupDecorator($configuration->getCustomer(), $queryWrapper);
        $this->customizationService = $customizationService;
        $this->greetingCardService = $greetingCardService;
        $this->db = $queryWrapper;
        $this->itemOrderabilityService = $itemOrderabilityService;
    }

    public function handleRequest()
    {
        $reqAction = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';
        $action = filter_var($reqAction, FILTER_SANITIZE_STRING);
        switch ($action) {
            case 'shop_add_item_to_basket':
                $this->addItemToBasket();
                break;
            case 'shop_add_item_to_basket_card':
                $this->addItemToBasketCard();
                break;
            case 'direct_order':
                $this->directOrder();
                break;
            case 'shop_add_item_to_basket_list':
                $this->addItemToBasketList();
                break;
            case 'shop_add_all_items_to_basket':
                $this->addAllItemsToBasket();
                break;
            case 'shop_refresh_user_basket':
                $this->refreshUserBasketQtys();
                $this->checkForRedirect();
                break;
            case 'shop_remove_item_from_basket':
                $this->removeItemFromBasketByTypeIDSubidentifier();
                break;
            case 'shop_remove_item_from_basket_by_id':
                $this->removeItemFromBasketByBasketLineID();
                break;
            case 'shop_empty_user_basket':
                $this->emptyBasket();
                break;
            case 'shop_add_all_items_from_order_to_basket':
                $this->addAllItemsToBasketFromOrder();
                break;
            case 'add_greeting_card_text':
                $this->addGreetingCardText();
                break;
            case 'change_greeting_card_text':
                $this->changeGreetingCardText();
                break;
            case 'shop_add_gift_wrapping':
                $this->addGiftWrapping();
                break;
            case 'shop_remove_item_from_basket_by_key':
                $this->removeItemFromBasketByKey();
                break;
            case "shop_add_item_to_favorites":
                shop_add_item_to_favorites($GLOBALS["visitor"]["id"], $_GET["action_id"]);
                break;
            case "shop_remove_item_from_favorites":
                shop_remove_item_from_favorites($GLOBALS["visitor"]["id"], $_GET["action_id"]);
                break;
            case "shop_empty_user_favorites":
                shop_empty_user_favorites($GLOBALS["visitor"]["id"]);
                break;
            case "shop_refresh_user_basket_customize":
                $this->refreshUserBasketCustomization();
                break;
            default:
                break;
        }
    }

    private function checkForRedirect() {
        if (array_key_exists('redirect',$_REQUEST)) {
            $reqRedirect = urldecode($_REQUEST['redirect']);
            $redirect = filter_var($reqRedirect, FILTER_SANITIZE_URL);
            universal_redirect($redirect);
        }
    }

    private function addItemToBasket()
    {
        $reqActionID = array_key_exists('action_id',$_REQUEST) ? $_REQUEST['action_id'] : 0;
        $reqVarCode = array_key_exists('var_code',$_REQUEST) ? $_REQUEST['var_code'] : '';
        $itemID = filter_var($reqActionID, FILTER_SANITIZE_NUMBER_INT);
        $varCode = filter_var($reqVarCode, FILTER_SANITIZE_STRING);
        $qty = (float)filter_var($_REQUEST['item_qty'], FILTER_SANITIZE_NUMBER_FLOAT);
        $customizationHash = $this->customizationService->handleCustomizationRequestAndGetHash();
        $greetingCardText = $this->greetingCardService->handleGreetingCardRequestAndGetText();
        //$this->addItemByIDVarCodeQtyCustomization((int)$itemID, $varCode, null, $customizationHash, null, $greetingCardText);
        $this->addItemByIDVarCodeQtyCustomization((int)$itemID, $varCode, (float)$qty, $customizationHash, $greetingCardText);
    }

    /**
     * @param $itemID
     * @param $varCode
     * @param float|null $qty
     * @param string $customizationHash
     * @param string $greetingCardText
     * @param array $entityLinkData
     * @return bool
     */
    public function addItemByIDVarCodeQtyCustomization($itemID, $varCode, $qty = null, $customizationHash = '', $greetingCardText = '', array $entityLinkData = [])
    {
        $eventName = 'beforeAddToBasket';
        $eventData = ['itemID' => &$itemID, 'varCode' => &$varCode, 'qty' => &$qty, 'customizationHash' => &$customizationHash, 'greetingCardText' => &$greetingCardText, 'entityLinkData' => &$entityLinkData];
        $this->updateHooks($eventName, $eventData);
        $successfullyAdded = false;
        $key = '';
        $refNewQty = $qty;
        $newQty = $qty;
        $success = false;
        $qtyAdjusted = false;

        if ($itemID > 0) {

            $item = $this->itemBuilder->getWebshopItemByIDVarCode($itemID, $varCode);
            $itemNo = $item->getItemNo();
            if ($qty === null) {
                $qty = (float)$item->getMinQty();
            }
            $error = null;

            if (!$this->itemOrderabilityService->isWebshopItemOrderable($this->user, $this->customerWithGroups, $item, $error)) {
                $eventName = 'basketAddError';
                $data['error_text_code'] = $error;
                $data['item_no'] = $item->getItemNo();
                $this->updateHooks($eventName, $data);
                return false;
            }

            if (!($item instanceof OrderableEntityInterface)) {
                $orderableEntity = $this->itemBuilder->decorateWebshopItemOrderable($item);
            } else {
                $orderableEntity = $item;
            }

            //Customization
            $orderableEntity->setCustomizationHash($customizationHash);
            if ($customizationHash != "") {
                $priceData = $orderableEntity->getPriceData();
                $priceData->setPrice($orderableEntity->getCustomizationPrice());
                $orderableEntity->setPrice($priceData);
            }

            //Greeting Card
            $orderableEntity->setGreetingCardText($greetingCardText);

            //EntityLink
            $isLinked = false;
            $linkedEntityKey = '';
            $entityLinkType = 0;
            $linkedEntityQty = 0.00;
            if ($entityLinkData !== null && array_key_exists('links_to_entity_key', $entityLinkData) && array_key_exists(
                    'entity_link_type', $entityLinkData) && $entityLinkData['links_to_entity_key'] && $entityLinkData['entity_link_type']
            ) {
                $isLinked = true;
                $linkedEntityKey = $entityLinkData['links_to_entity_key'];
                $entityLinkType = $entityLinkData['entity_link_type'];
                $orderableEntity->setBasketEntityLink($linkedEntityKey, $entityLinkType);
                $linkedEntityQty = $this->currUserBasket->getItemQtyByKey($linkedEntityKey);
            }

            $key = $this->currUserBasket->getKey($orderableEntity);
            $hasItem = $this->currUserBasket->hasItemForKey($key);
            $maxQty = $orderableEntity->getMaxQty();
            $minQty = $orderableEntity->getMinQty();
            $step = $orderableEntity->getQtyStep();

            $refQty = $qty;
            adjust_qty_by_min_max_step($qty, 0, $minQty, $maxQty, $step);

            $qtyAdjusted = ($refQty !== $qty) && ($refNewQty > 0);
            $added = true;
            $qtyChanged = false;
            $removed = false;
            $qtyChange = $qty;
            $newQty = $qty;

            if ($hasItem) {
                $prevQty = $this->currUserBasket->getItemQtyByKey($key);
                $newQty = (float)$prevQty + (float)$qty;
                $refNewQty = $newQty;
                adjust_qty_by_min_max_step($newQty, $prevQty, $minQty, $maxQty, $step);
                $qtyAdjusted = $refNewQty !== $newQty;
                if ($isLinked && $newQty > $linkedEntityQty) {
                    $newQty = $linkedEntityQty;
                }
                $qtyChange = $newQty - $prevQty;

                $added = false;
                if ($newQty <= 0) {
                    $qtyChanged = false;
                    $removed = true;
                    $success = $this->currUserBasket->removeItemByKey($key);
                } else {
                    $qtyChanged = true;
                    $removed = false;
                    $success = $this->currUserBasket->changeItemQtyByKey($key, $newQty);
                }

            } else {
                //EntityLink
                if ($isLinked && $qty > $linkedEntityQty) {
                    $newQty = $linkedEntityQty;
                }
                $basketEntity = $this->itemBuilder->decorateWebshopItemBasket($orderableEntity, $newQty);
                $basketEntity->setQtySourceType(ItemPriceData::QTY_SOURCE_USER);
                $basketEntity->setQtySourceID($this->currShopConfiguration->getVisitorID());
                $basketEntity->setCreationSourceType(BasketEntity::CREATION_SOURCE_TYPE_USER);
                $basketEntity->setCreationSourceID($this->currShopConfiguration->getVisitorID());
                $this->currUserBasket->addItem($basketEntity);
                $success = $this->currUserBasket->hasItemForKey($key);
            }
        }
        if ($newQty <= 0) {
            $qtyChanged = false;
            $removed = true;
        }
        if ($qtyAdjusted) {
            $eventName = 'basketQtyAdujsted';
            $eventData = ['itemID' => $itemID, 'varCode' => $varCode, 'requested_quantity' => $refNewQty, 'new_qty' => $newQty, 'shop_type' => (int)$GLOBALS["shop"]["shop_typ"]];
            $this->updateHooks($eventName,$eventData);
        }

        return (bool)$success;
    }

    private function addItemToBasketCard()
    {
        $itemID = filter_var($_REQUEST['item_id'], FILTER_SANITIZE_NUMBER_INT);
        if (!($itemID) > 0) {
            $itemID = filter_var($_REQUEST['card'], FILTER_SANITIZE_NUMBER_INT);
        }
        $varCode = filter_var($_REQUEST['item_var_code'], FILTER_SANITIZE_STRING);
        $qty = (float)$_REQUEST['item_qty'];

        $customizationHash = $this->customizationService->handleCustomizationRequestAndGetHash();
        $greetingCardText = filter_var($_REQUEST['input_message'], FILTER_SANITIZE_STRING);
        //SL+++
        if (is_array($customizationHash) && count($customizationHash) > 0) {
            $customizationHash = array_unique($customizationHash);
            foreach ($customizationHash as $hash) {
                $this->addItemByIDVarCodeQtyCustomization((int)$itemID, $varCode, 1, $hash, $greetingCardText);
            }
        } else {

            $this->addItemByIDVarCodeQtyCustomization((int)$itemID, $varCode, (float)$qty, '', $greetingCardText);
        }
        //SL---

    }

    private function directOrder()
    {
        $itemNo = filter_var($_REQUEST['input_item_no'], FILTER_SANITIZE_STRING);

        $qty = (float)$_REQUEST['input_item_quantity'];

        $variantType = $this->currShopConfiguration->getVariantType();
        $variantCode = '';
        if ($variantType === 2) {
            $variantCode = $_REQUEST['input_var_code'] ?? '';
        } elseif (!empty($_REQUEST['input_var_code'])) {
            $itemNo = $_REQUEST['input_var_code'] ?? '';
        }
        $paramArr = [
            [':item_no', $itemNo, \PDO::PARAM_STR],
            [':shop_company', $this->currShopConfiguration->getCompany(), \PDO::PARAM_STR],
            [':shop_code', $this->currShopConfiguration->getShopCode(), \PDO::PARAM_STR],
            [':shop_language_code', $this->currShopConfiguration->getShopLanguageCode(), \PDO::PARAM_STR]
        ];
        $this->db->setQuery(self::$getItemIdByItemNoQuery);
        $this->db->prepareQuery();
        $this->db->bindParameters($paramArr);
        $this->db->executePreparedStatement();
        $resArr = $this->db->getResultArray();
        $itemID = $resArr[0]["id"] ?? 0;

        if ($itemID > 0 && $qty > 0) {
            $this->addItemByIDVarCodeQtyCustomization((int)$itemID, $variantCode, $qty, '');
        } elseif ($itemID === 0) {
            $eventName = AddToBasketErrorListener::EVENT_NAME;
            $eventData = [
                'item_no' => $itemNo,
                'error_text_code' => 'error_direct_order_no_such_item',
            ];
            $this->updateHooks($eventName,$eventData);

        }
    }

    private function addItemToBasketList()
    {
        $inputIndexes = $this->getItemInputIndexes();
        foreach ($inputIndexes as $index) {
            $itemID = filter_var($_REQUEST['input_item_id_' . $index], FILTER_SANITIZE_NUMBER_INT);
            $qty = (float)$_REQUEST['input_item_quantity_' . $index];
            $varCode = filter_var($_REQUEST['input_item_variant_' . $index], FILTER_SANITIZE_STRING);
            if (empty($varCode) && array_key_exists('input_variant_code_' . $index, $_REQUEST)) {
                $varCode = filter_var($_REQUEST['input_variant_code_' . $index], FILTER_SANITIZE_STRING);
            }
            if ($qty > 0) {
                $customizationHash = $this->customizationService->handleCustomizationRequestAndGetHash();
                $greetingCardText = $this->greetingCardService->handleGreetingCardRequestAndGetText();
                $this->addItemByIDVarCodeQtyCustomization($itemID, $varCode, (float)$qty, $customizationHash, $greetingCardText);
            }
        }
    }

    /**
     * @return array
     */
    private function getItemInputIndexes()
    {
        $indexes = [];
        foreach ($_REQUEST as $key => $val) {
            if (strpos($key, 'input_item_id_') === 0) {
                $indexes[] = (int)str_replace('input_item_id_', '', $key);
            }
        }
        return $indexes;

    }

    private function addAllItemsToBasket()
    {
        $this->addItemToBasketList();
    }

    private function refreshUserBasketQtys()
    {
        $inputIndexes = $this->getItemInputIndexes();
        foreach ($inputIndexes as $index) {
            $itemID = filter_var($_REQUEST['input_item_id_' . $index], FILTER_SANITIZE_NUMBER_INT);
            $qty = (float)$_REQUEST['input_item_quantity_' . $index];
            $varCode = filter_var($_REQUEST['input_item_variant_' . $index], FILTER_SANITIZE_STRING);
            if (empty($varCode) && array_key_exists('input_variant_code_' . $index, $_REQUEST)) {
                $varCode = filter_var($_REQUEST['input_variant_code_' . $index], FILTER_SANITIZE_STRING);
            }
            $itemKey = filter_var($_REQUEST['input_item_key_' . $index], FILTER_SANITIZE_STRING);
            if ($itemKey !== '' && $this->currUserBasket->hasItemForKey($itemKey)) {
                $item = $this->currUserBasket->getItemByKey($itemKey);
                //echo "... basket has item for key $itemKey";
                $itemNo = $item->getIdentifier();
                $oldQty = $this->currUserBasket->getItemQtyByKey($itemKey);
                if ($qty !== $oldQty) {
                    $item = $this->currUserBasket->getItemByKey($itemKey);
                    $min = $item->getMinQty();
                    $max = $item->getMaxQty();
                    $step = $item->getQtyStep();
                    $refNewQty = $qty;
                    adjust_qty_by_min_max_step($qty, $oldQty, $min, $max, $step);
                    $qtyAdjusted = $qty !== $refNewQty;
                    $this->currUserBasket->changeItemQtyByKey($this->currUserBasket->getKey($item), $qty);

                    if ($qtyAdjusted) {
                        $eventName = 'basketQtyAdujsted';
                        $eventData = ['itemID' => $itemID, 'item_no' => $itemNo, 'varCode' => $varCode, 'requested_quantity' => $refNewQty, 'new_qty' => $qty];
                        $this->updateHooks($eventName,$eventData);
                    }
                }
            }
        }
    }

    private function removeItemFromBasketByTypeIDSubidentifier()
    {
        $itemID = filter_var($_REQUEST['action_id'], FILTER_SANITIZE_NUMBER_INT);
        $varCode = filter_var($_REQUEST['var_code'], FILTER_SANITIZE_STRING);
        if ($itemID > 0) {
            $item = $this->itemBuilder->getWebshopItemOrderableEntityByIDVarCode($itemID, $varCode);
            $key = $this->currUserBasket->getKey($item);
            if ($this->currUserBasket->hasItemForKey($key)) {
                $this->currUserBasket->removeItemByKey($key);
            }
        }
    }

    private function removeItemFromBasketByKey()
    {
        $pass = pad_string_to_mb_length($GLOBALS['shop_setup']['shop_password'], 16);
        $key = $pass;
        $encryptedItemKey = base64_decode(filter_var($_GET['item_key'], FILTER_SANITIZE_STRING));
        if ($encryptedItemKey) {
            //$itemKey = Crypto::decrypt($encryptedItemKey, $key);
            $itemKey = $encryptedItemKey;
            if ($itemKey && $this->currUserBasket->hasItemForKey($itemKey)) {
                $this->currUserBasket->removeItemByKey($itemKey);
            }
        }
    }

    private function removeItemFromBasketByBasketLineID()
    {
        $id = filter_var($_REQUEST['action_id'], FILTER_SANITIZE_NUMBER_INT);
        if ($id > 0) {
            $this->currUserBasket->removeItemByLineID($id);
        }
    }

    private function emptyBasket()
    {
        $this->currUserBasket->reset();
    }

    /**
     * @return UserBasket
     */
    public function getBasket()
    {
        return $this->currUserBasket;
    }

    private function addAllItemsToBasketFromOrder()
    {
        if (array_key_exists('order_no', $_REQUEST)) {
            $orderNo = filter_var($_REQUEST['order_no'], FILTER_SANITIZE_STRING);
            $paramArr = [
                [':order_no', $orderNo, \PDO::PARAM_STR],
                [':shop_company', $this->currShopConfiguration->getCompany(), \PDO::PARAM_STR],
                [':user_company', $this->currShopConfiguration->getUserCompany(), \PDO::PARAM_STR],
                [':shop_code', $this->currShopConfiguration->getShopCode(), \PDO::PARAM_STR],
                [':shop_language_code', $this->currShopConfiguration->getShopLanguageCode(), \PDO::PARAM_STR]
            ];
            $this->db->setQuery(self::$orderItemsQuery);
            $this->db->prepareQuery();
            $this->db->bindParameters($paramArr);
            $this->db->executePreparedStatement();
            $resArr = $this->db->getResultArray();
            foreach ($resArr as $row) {
                if (array_key_exists('id', $row) && array_key_exists('variant_code', $row) && array_key_exists('quantity', $row)) {
                    $this->addItemByIDVarCodeQtyCustomization((int)$row['id'], $row['variant_code'], (float)$row['quantity'], '');
                }

            }
        }
    }

    private function changeGreetingCardText()
    {

        $pass = $GLOBALS['shop_setup']['shop_password'];
        $pass = pad_string_to_mb_length($pass, 16);

        $key = $pass;
        $encryptedItemKey = base64_decode(filter_var($_REQUEST['item_key'], FILTER_SANITIZE_STRING));
        //$itemKey = Crypto::decrypt($encryptedItemKey, $key);
        $itemKey = $encryptedItemKey;
        $newText = filter_var($_REQUEST['input_message'], FILTER_SANITIZE_STRING);
        //echo "new greeting card text is: <pre>" . $newText . "</pre> oldhash: " . $this->currUserBasket->getHash();

        $item = $this->currUserBasket->getItemByKey($itemKey);
        if ($item->getEntityID() > 0) {
            $item->setGreetingCardText($newText);
        }
        $this->currUserBasket->recalculateValues();
        //echo "finished changing: new Hash: " . $this->currUserBasket->getHash();*/
    }

    private function addGreetingCardText()
    {

        $pass = $GLOBALS['shop_setup']['shop_password'];
        $pass = pad_string_to_mb_length($pass, 16);
        $key = $pass;
        $encryptedItemKey = base64_decode(filter_var($_REQUEST['item_key'], FILTER_SANITIZE_STRING));
        $itemKey = $encryptedItemKey;
        $itemID = base64_decode(filter_var($_REQUEST['action_id'], FILTER_SANITIZE_STRING));
        $varCode = '';
        $greetingCardText = filter_var($_REQUEST['input_message'], FILTER_SANITIZE_STRING);
        $customizationHash = $this->customizationService->handleCustomizationRequestAndGetHash();
        $this->addItemByIDVarCodeQtyCustomization((int)$itemID, $varCode, null, $customizationHash, $greetingCardText);
        $this->currUserBasket->recalculateValues();

    }

    private function addGiftWrapping()
    {
        $pass = $GLOBALS['shop_setup']['shop_password'];
        $pass = pad_string_to_mb_length($pass, 16);
        $key = $pass;
        $appliesToItemKeyEncrypted = base64_decode(filter_var($_GET['item_key'], FILTER_SANITIZE_STRING));
        //echo "string to decrypt is $appliesToItemKeyEncrypted";
        //$appliesToItemKey = Crypto::decrypt($appliesToItemKeyEncrypted, $key);
        $appliesToItemKey = $appliesToItemKeyEncrypted;
        $linkData = ['links_to_entity_key' => $appliesToItemKey, 'entity_link_type' => OrderableEntityInterface::BASKET_ENTITY_LINK_TYPE_WRAPPING_TO_ITEM];

        $wrappingID = filter_var($_GET['wrapping_item'], FILTER_SANITIZE_NUMBER_INT);
        //echo " - add gift wrapping with link-data: " . print_r($linkData,1) . " - ";
        $this->addItemByIDVarCodeQtyCustomization($wrappingID, '', 1.00, '', '', $linkData);
        $this->currUserBasket->setMinMaxQuantitiesForLinkedItems();

    }

    public function refreshUserBasketCustomization()
    {
        $customizationData = [];
        $itemHash = filter_var($_REQUEST['action_id']);
        if(isset($_REQUEST['save_data']))
        {
            $this->customizationService->populateCustomizationData($customizationData, $_POST, $itemHash);
            $this->customizationService->populateCustomizationData($customizationData, $_FILES, $itemHash);

            foreach ($customizationData as $key => $value) {
                $doUpdate = true;
                $customizationValue = $value[0]['customization_value'];
                $customizationItem = $this->customizationService->getCustomizationFieldSpecificationByID($value[0]['shop_item_customize_id']);
                if ($customizationItem['field_type'] == 1) {// file
                    $doUpdate = false;
                    $fileName = $this->customizationService->handleFileUploadForCustomizationFieldAndGetFilename($key, $customizationItem['id']);
                    $customizationValue = $fileName;
                    if ($fileName != "") {
                        $doUpdate = true;
                        $this->customizationService->deleteUserBasketCustomizationFile($itemHash);
                    }

                }
                if ($doUpdate) {
                    $this->customizationService->updateCustomizationValue($key, $customizationValue);
                }

            }

        }

    }


}