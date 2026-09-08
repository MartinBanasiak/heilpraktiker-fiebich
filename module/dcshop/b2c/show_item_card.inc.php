<?

use DynCom\dc\common\classes\FormBuilder;
use DynCom\dc\common\classes\Hook;
use DynCom\dc\dcShop\classes\ItemOrderButtonBuilder;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

$baseDirectory = rtrim(dirname(__DIR__, 3), '/');
require_once($baseDirectory . '/module/tracking/tracking_functions.php');

require_once __DIR__ . DIRECTORY_SEPARATOR . 'show_item_card_functions.inc.php';
$startTime = microtime(true);
/** @var \DynCom\dc\dcShop\classes\WebshopItemBuilder $itemBuilder */
$itemBuilder = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemBuilder');
$id = (int)$_GET['card'];
$varCode = filter_var($_GET['variant'], FILTER_SANITIZE_STRING);
/** @var WebshopItemInterface $itemObj */
$itemObj = $itemBuilder->getFirstActiveVariant($id, $varCode);

if (is_null($itemObj)) {
    echo $GLOBALS["tc"]["item_not_found"];
    exit;
}

$item = item_obj_to_itemcard_array($itemObj);
$parent_item = $item;
$parent_item['item_no'] = $item['parent_item_no'];

$orderable = $itemBuilder->getWebshopItemOrderableEntityByPrimary($item['item_no'], $varCode);
$notifications = get_item_user_notifications($orderable);
$item['notifications'] = $notifications;

if ($itemObj instanceof WebshopItemInterface) {

    $eventName = 'onShowItemCard';
    $eventData = ['item_obj' => $itemObj];
    Hook::update($eventName, $eventData);
    /**
     * @var $shopConfig \DynCom\dc\dcShop\classes\CurrShopConfiguration
     */
    $shopConfig = $IOCContainer->create('$CurrShopConfig');
    $domain = $shopConfig->getBaseShopUrl();
    $customer = $shopConfig->getCustomer();
    $user = $shopConfig->getUser();
    $currencyCode = $shopConfig->getCurrencyCode();
    /** @var \DynCom\dc\dcShop\classes\AdvancedPriceProvider $advancedPriceProvider */
    $advancedPriceProvider = $IOCContainer->create('DynCom\dc\dcShop\classes\AdvancedPriceProvider');
    $defaultItemPrice = $advancedPriceProvider->getItemCustomerPrice($itemObj, 1, $customer, $currencyCode);
    //$PDO = $IOCContainer->create('PDOQueryWrapper');

    /** @var \DynCom\dc\dcShop\subscriptions\classes\ItemSubscriptionDataBuilder $subscriptionDataBuilder */
    $subscriptionDataBuilder = $IOCContainer->create('DynCom\dc\dcShop\subscriptions\classes\ItemSubscriptionDataBuilder');

    $variantService = $IOCContainer->create(\DynCom\dc\dcShop\classes\WebshopItemVariantService::class);
    $fileRepo = $IOCContainer->create(\DynCom\dc\dcShop\classes\WebshopItemFileRepository::class);
    $logger = get_logger('show_item_card');
    $itemFileDecorator = new \DynCom\dc\dcShop\classes\WebshopItemFilesDecorator($itemObj,$variantService,$fileRepo,true,$logger);
    /**
     * @var $fileRepo \DynCom\dc\dcShop\classes\WebshopItemFileRepository
     */
    $mainMediumCollection = $fileRepo->findByCriteria(
            [
                    [
                            ['company','=',$itemObj->getCompany()],
                            ['shop_code','=',$itemObj->getShopCode()],
                            ['language_code','=',$itemObj->getLanguageCode()],
                            ['item_no','=',$itemObj->getItemNo()],
                            ['main_medium','=','1']
                    ]
            ]
    );
    if (!(\count($mainMediumCollection) > 0)) {
        /**
         * @var $mainMedium \DynCom\dc\dcShop\classes\WebshopItemFile|null
         */
        $mainMedium = $itemFileDecorator->getMainImage();
    } else {
        /**
         * @var $mainMedium \DynCom\dc\dcShop\classes\WebshopItemFile|null
         */
        $mainMedium = $mainMediumCollection->getFirst();
    }
    $validator = new \DynCom\dc\common\classes\Validator([], []);
    $formBuilder = new FormBuilder('dummyID');
    $templating = $IOCContainer->create('DynCom\dc\common\classes\Templating');

    /** @var \DynCom\dc\dcShop\classes\DefaultItemAvailabilityProvider $availabilityProvider */
    $availabilityProvider = $IOCContainer->create('DynCom\dc\dcShop\classes\DefaultItemAvailabilityProvider');

    $availability_code = $availabilityProvider->getItemAvailability($itemObj);
    $is_available = $availabilityProvider->isItemAvailable($itemObj);

    $isRegistered = false;
    $justRegistered = false;
    //Evaluate registration for availability-notification if sent
    if (isset($_POST['input_availability_mail'])) {
        $isRegistered = evaluate_availability_notification_registration(
                $GLOBALS['site']['code'],
                $GLOBALS['language']['code'],
                $itemObj->getItemNo(),
                $varCode,
                $customer->getCustomerNo(),
                $user->name,
                $domain->getFullURL()
            ) === true;
        $justRegistered = $isRegistered;
    }
    $sessionValueName = 'item_' . $itemObj->getItemNo() . '_' . $itemObj->getVariantCode() . '_availability_registered';
    if ($justRegistered && !isset($_SESSION[$sessionValueName])) {
        $_SESSION[$sessionValueName] = true;
    }
    if (isset($_SESSION[$sessionValueName]) && $_SESSION[$sessionValueName]) {
        $isRegistered = true;
    }
    $subscriptionData = $subscriptionDataBuilder->getItemSubscriptionData($itemObj);
    $vatMgr = $IOCContainer->create('$VATManager');
    $builder = new ItemOrderButtonBuilder($shopConfig, $availabilityProvider, $validator, $formBuilder, $templating,$vatMgr);
    $htmlString = $builder->getItemcardButtonHTML($itemObj, $defaultItemPrice, $subscriptionData, true);
    $orderbox = "<div class='orderbox' itemprop=\"offers\" itemscope itemtype=\"http://schema.org/AggregateOffer\" ><div class='orderbox_button'>" . $htmlString . "</div></div>";
    if (!$isRegistered && !$is_available) {
        $orderbox .= get_itemcard_availability_notification();
    } elseif ($justRegistered) {
        get_requestbox($GLOBALS['tc']['availability_registration_successful'], '', 'success');
    }


    if (!empty(getenv('TRACKING_API_ROOT'))) {


        include_once $baseDirectory . '/module/tracking/TrackingEvent.php';
        include_once $baseDirectory . '/module/tracking/TrackingAPIService.php';

        $trackingAPIService = new \DynCom\dc\tracking\TrackingAPIService();

        //Set referrer data
        \DynCom\dc\tracking\tracking_set_referrer_info(\DynCom\dc\tracking\TrackingEvent::EVENT_TYPE_PAGEVIEW_ITEM, $id);


        //collect tracking data
        $event_uuid = \DynCom\dc\tracking\get_event_uuid_and_echo_div_once();
        \DynCom\dc\tracking\echo_tracking_data_div_once();
        $last_pageview_type = isset($GLOBALS['referrer_data']['LAST_PAGEVIEW_TYPE']) ? $GLOBALS['referrer_data']['LAST_PAGEVIEW_TYPE'] : '';
        $last_pageview_id = isset($GLOBALS['referrer_data']['LAST_PAGEVIEW_ID']) ? $GLOBALS['referrer_data']['LAST_PAGEVIEW_ID'] : '';

        $trackingData = [
            'unique_id' => $event_uuid,
            'current_item_id' => $id,
            'current_visitor_id' => $GLOBALS['visitor']['id'],
            'current_user_id' => $GLOBALS['shop_user']['id'],
            'current_customer_id' => $GLOBALS['shop_customer']['id'],
            'visit_start_timestamp' => time(),
            'http_referrer' => $_SERVER['HTTP_REFERER'],
            'previous_pageview_type' => $last_pageview_type,
            'previous_pageview_id' => $last_pageview_id,
        ];

        $visitorID = isset($GLOBALS['visitor']['id']) ? (int)$GLOBALS['visitor']['id'] : null;
        $userID = isset($GLOBALS['shop_user']['id']) ? (int)$GLOBALS['shop_user']['id'] : null;
        $customerID = isset($GLOBALS['shop_customer']['id']) ? (int)$GLOBALS['shop_customer']['id'] : null;
        $itemID = $itemObj->getID();
        $categoryID = isset($GLOBALS['category']['id']) ? (int)$GLOBALS['category']['id'] : null;
        $timestamp = (string)time();
        $event = new \DynCom\dc\tracking\TrackingEvent($event_uuid, session_id(), \DynCom\dc\tracking\TrackingEvent::EVENT_TYPE_PAGEVIEW_ITEM, $timestamp, $timestamp, $trackingData, $visitorID, $userID, $customerID, $itemID, $categoryID);
        $trackingSuccess = $trackingAPIService->addTrackingEvent($event);
    }

    ?>
    <div class="banners_campaign">
        <? show_item_campaign_banners($item, FALSE, FALSE, TRUE); ?>
    </div>
    <?

    ?>

    <div id="itemcard" itemscope itemtype="http://schema.org/product">
        <div id="itemcard_top">
            <div class="row">
                <?
                switch ($GLOBALS['shop_setup']['itemcard_layout']) {
                    case 2:
                        ?>
                        <div id="itemcard_left" class="col-xs-12 col-sm-12 col-md-7 col-lg-8">
                            <? show_item_images($item, $parent_item, $GLOBALS["shop_setup"]["image_config"]) ?>
                        </div>
                        <div id="itemcard_right" class="col-xs-12 col-sm-12 col-md-5 col-lg-4">
                            <div id="itemcard_info">
                                <div class="itemcard_brand">
                                    <?= get_brand_logo($item) ?>
                                </div>
                                <h1 class="itemcard_item_name">
                                    <?= get_brand_name($item) ?>
                                    <span itemprop="name">
                                            <?= $item["description"] ?>
                                        </span>
                                </h1>
                                <? if ($GLOBALS['shop_setup']['rating_active']) {
                                    $item_rating = get_item_rating($item);
                                    ?>
                                    <div class="item_card_rating">
                                        <? if ($item_rating['counter'] > 0) { ?>
                                            <div class="item_card_rating_counter" itemprop="aggregateRating" itemscope
                                                 itemtype="http://schema.org/AggregateRating">

                                                <a href="#itemcard_comments">
                                                    <? show_item_rating($item, true); ?> (<span
                                                            itemprop="reviewCount"><?= $item_rating['counter'] ?></span> <?= $GLOBALS['tc']['item_comments'] ?>
                                                    )
                                                </a>
                                            </div>
                                            <?
                                        } ?>
                                        <? /*<div class="item_card_rating_button">
                                                <? require("shop_item_comments.inc.php"); ?>
                                            </div> */ ?>
                                    </div>
                                    <?
                                } ?>

                                <div class="itemcard_short_description">
                                    <?= ($itemObj->getSummary() <> '') ? "<div class=\"itemcard_summary\">" . $itemObj->getSummary() . "</div>\n" : "" ?>
                                    <div class="itemcard_item_no"><?= $itemObj->getItemNo() ?></div>
                                    <? show_item_description(1, $item["item_no"], $item["language_code"], $parent_item["item_no"], $parent_item["language_code"], 1) ?>
                                    <? show_item_description(1, $item["item_no"], $item["language_code"], $parent_item["item_no"], $parent_item["language_code"], 2) ?>
                                </div>
                                <div class="itemcard_trust_box">
                                    <? if (!empty($GLOBALS["shop_language"]["itemcard_trust_box_text_module"])) {
                                        echo get_text_module($GLOBALS["shop"]["company"], $GLOBALS["shop_language"]["itemcard_trust_box_text_module"]);
                                    } ?>
                                </div>
                                <? //show_item_videos($item, $parent_item, $GLOBALS["shop_setup"]["uploaddir_videos"])
                                ?>
                                <? //show_item_documents($item, $parent_item, $GLOBALS["shop_setup"]["uploaddir_documents"])
                                ?>

                            </div>
                            <?
                            if ($itemObj->isVariant() || $itemObj->hasVariants()) {
                                ?>
                                <div class="itemcard_variants">
                                    <div class="itemcard_variants_headline"><?= $GLOBALS['tc']['select_variant'] ?></div>
                                    <? /*<div class="variant">
                                                <div class="variant_item">S</div>
                                                <div class="variant_item">M</div>
                                                <div class="variant_item not-available">L</div>
                                                <div class="variant_item active">XL</div>
                                            </div> */ ?>
                                    <? if ($GLOBALS['shop']['variant_typ'] != '2') {
                                        variant_select_new($item);
                                    } else {
                                        variant_select_nav_new($item);
                                        if (isset($_GET['variant']) && $_GET['variant'] != "") {
                                            $query = "SELECT * FROM shop_item_variant WHERE item_no = '" . $itemObj->getItemNo() . "' AND code = '" . $itemObj->getVariantCode() . "'";
                                            $result = mysqli_query($GLOBALS['mysql_con'], $query);
                                            $row = mysqli_fetch_assoc($result);
                                            $code = $row['code'];
                                        } else {
                                            $code = '';
                                        }
                                    } ?>
                                </div>
                            <? } ?>
                            <? show_header_attributes($item, $parent_item) ?>
                            <? echo $orderbox; ?>
                            <div class="itemcard_trust_box">
                                <? if (!empty($GLOBALS["shop_language"]["itemcard_trust_box_text_module_2"])) {
                                    echo get_text_module($GLOBALS["shop"]["company"], $GLOBALS["shop_language"]["itemcard_trust_box_text_module_2"]);
                                } ?>
                            </div>
                            <? get_favorite_sign(($parent_item["id"] <> '') ? $parent_item : $item) ?>
                            <? get_itemcard_sharing($item); ?>
                        </div>
                        <?
                        break;
                    default:
                        ?>

                        <div id="itemcard_info" class="col-xs-12 col-sm-12 col-md-7 col-lg-8">
                            <div class="row">
                                <div id="itemcard_left" class="col-xs-12 col-sm-6">
                                    <div class="itemcard_brand">
                                        <?= get_brand_logo($item) ?>
                                    </div>
                                    <h1 class="itemcard_item_name">
                                        <?= get_brand_name($item) ?>
                                        <span itemprop="name">
                                            <?= $item["description"] ?>
                                        </span>
                                    </h1>
                                    <? if ($GLOBALS['shop_setup']['rating_active']) {
                                        $item_rating = get_item_rating($item);
                                        ?>
                                        <div class="item_card_rating">
                                            <? if ($item_rating['counter'] > 0) { ?>
                                                <div class="item_card_rating_counter" itemprop="aggregateRating"
                                                     itemscope itemtype="http://schema.org/AggregateRating">
                                                    <a href="#itemcard_comments">
                                                        <? show_item_rating($item, true); ?> (<span
                                                                itemprop="reviewCount"><?= $item_rating['counter'] ?></span> <?= $GLOBALS['tc']['item_comments'] ?>
                                                        )
                                                    </a>
                                                </div>
                                                <?
                                            } ?>
                                            <? /*<div class="item_card_rating_button">
                                                <? require("shop_item_comments.inc.php"); ?>
                                            </div> */ ?>
                                        </div>
                                        <?
                                    } ?>
                                    <div class="itemcard_short_description">
                                        <?= ($itemObj->getSummary() <> '') ? "<div class=\"itemcard_summary\">" . $itemObj->getSummary() . "</div>\n" : "" ?>
                                        <div class="itemcard_item_no"><?= $itemObj->getItemNo() ?></div>
                                        <? show_item_description(1, $item["item_no"], $item["language_code"], $parent_item["item_no"], $parent_item["language_code"], 1) ?>
                                        <? show_item_description(1, $item["item_no"], $item["language_code"], $parent_item["item_no"], $parent_item["language_code"], 2) ?>
                                        <?/*<div class="item_card_item_no"><?= $GLOBALS["tc"]["item_no"] ?> <?= $itemObj->getItemNo() ?></div>*/ ?>
                                    </div>
                                    <div class="itemcard_trust_box">
                                        <? if (!empty($GLOBALS["shop_language"]["itemcard_trust_box_text_module"])) {
                                            echo get_text_module($GLOBALS["shop"]["company"], $GLOBALS["shop_language"]["itemcard_trust_box_text_module"]);
                                        } ?>
                                    </div>
                                    <? //show_item_videos($item, $parent_item, $GLOBALS["shop_setup"]["uploaddir_videos"])
                                    ?>
                                    <? //show_item_documents($item, $parent_item, $GLOBALS["shop_setup"]["uploaddir_documents"])
                                    ?>
                                </div>
                                <?
                                /*if($item['customizable'] == 1 &&  isset($_GET['customized']) && $_GET['customized'] == true)
                                     { ?>
                                        <div id="itemcard_middle_customize" class="col-xs-12 col-sm-6">
                                                <? show_item_images($item, $parent_item, $GLOBALS["shop_setup"]["image_config"], 1) ?>
                                        </div>
                                     <? }else{ ?>

                                    <div  id="itemcard_middle" class="col-xs-12 col-sm-6">
                                        <? show_item_images($item, $parent_item, $GLOBALS["shop_setup"]["image_config"], 0) ?>
                                    </div>

                               <? } */ ?>

                                <div id="itemcard_middle" class="col-xs-12 col-sm-6">
                                    <div class="itemcard_middle_container">
                                        <? show_item_images($item, $parent_item, $GLOBALS["shop_setup"]["image_config"], 0) ?>
                                    </div>
                                    <? if ($item['customizable'] == 1) { ?>
                                        <div class="itemcard_middle_container_customize">
                                            <? show_item_images($item, $parent_item, $GLOBALS["shop_setup"]["image_config"], 1) ?>
                                        </div>
                                    <? } ?>
                                </div>

                            </div>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="ShowShipingTermsModal" tabindex="-1" role="dialog"
                             aria-labelledby="myModalLabel">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <!-- <h4 class="modal-title" id="myModalLabel">Modal title</h4> -->
                                    </div>
                                    <div class="modal-body">
                                        <? get_content("shipping_terms_modal", TRUE, $IOCContainer); ?>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div id="itemcard_right" class="col-xs-12 col-sm-12 col-md-5 col-lg-4">
                            <?php
                            if ($itemObj->isVariant() || $itemObj->hasVariants()) {
                                ?>
                                <div class="itemcard_variants">
                                    <div class="itemcard_variants_headline"><?= $GLOBALS['tc']['select_variant'] ?></div>
                                    <? /*<div class="variant">
                                                <div class="variant_item">S</div>
                                                <div class="variant_item">M</div>
                                                <div class="variant_item not-available">L</div>
                                                <div class="variant_item active">XL</div>
                                            </div> */ ?>
                                    <? if ($GLOBALS['shop']['variant_typ'] != '2') {
                                        variant_select_new($item);
                                    } else {
                                        variant_select_nav_new($item);
                                        if (isset($_GET['variant']) && $_GET['variant'] != "") {
                                            $query = "SELECT * FROM shop_item_variant WHERE item_no = '" . $itemObj->getItemNo() . "' AND code = '" . $itemObj->getVariantCode() . "'";
                                            $result = mysqli_query($GLOBALS['mysql_con'], $query);
                                            $row = mysqli_fetch_assoc($result);
                                            $code = $row['code'];
                                        } else {
                                            $code = '';
                                        }
                                    } ?>
                                </div>
                            <? } ?>
                            <? show_header_attributes($item, $parent_item) ?>
                            <? echo $orderbox; ?>
                            <div class="itemcard_trust_box">
                                <? if (!empty($GLOBALS["shop_language"]["itemcard_trust_box_text_module_2"])) {
                                    echo get_text_module($GLOBALS["shop"]["company"], $GLOBALS["shop_language"]["itemcard_trust_box_text_module_2"]);
                                } ?>
                            </div>
                            <? get_favorite_sign(($parent_item["id"] <> '') ? $parent_item : $item) ?>
                            <? get_itemcard_sharing($item); ?>
                        </div>
                        <?
                }

                ?>
            </div>
        </div>


        <div id="itemcard_bottom">
            <? //show_item_tabs($item, $parent_item) ?>
            <? //show_item_documents($item, $parent_item, $GLOBALS["shop_setup"]["uploaddir_documents"]) ?>
            <? //show_item_acc_items($item, $parent_item, 6, 3) ?>
            <? //show_item_spare_parts($item, $parent_item, 6, 3) ?>
            <? //show_item_fit_item($item, $parent_item, 6, 3) ?>
            <?php
             //show_item_details_new($itemFileDecorator);
             show_item_details($item, $parent_item) ?>
            <? show_item_alt_item($item, $parent_item, 6, 3) ?>
        </div>

    </div>
    <?
}
// Funktionen zum anzeigen von Artikelvarianten

function variant_select_new($item)
{
    $parent_item = get_item_variant_parent($item);
    if ($parent_item["id"] <> '') {
        $query = "
			SELECT shop_view_active_item.*
			FROM shop_view_active_item
			LEFT JOIN shop_item_link ON (shop_item_link.type = 0 AND shop_item_link.item_no = '" . $parent_item["item_no"] . "' AND shop_item_link.linked_item_no=shop_view_active_item.item_no)
			WHERE
				shop_view_active_item.company = '" . $GLOBALS['shop']['company'] . "'
        	  AND
				shop_view_active_item.shop_code= '" . $parent_item["shop_code"] . "'
        	  AND
				shop_view_active_item.language_code = '" . $parent_item["language_code"] . "'
			  AND
				NOT ISNULL(shop_item_link.id)
          	ORDER BY shop_view_active_item.item_no ASC
		";
    } else {
        /*$query = "SELECT DISTINCT shop_view_active_item.*
                  FROM shop_item_link
                  INNER JOIN shop_view_active_item ON shop_view_active_item.item_no = shop_item_link.linked_item_no
                  WHERE shop_item_link.type = '0'
                    AND shop_item_link.item_no = '" . $item["item_no"] . "'
                    AND shop_view_active_item.company = '".$GLOBALS['shop']['company']."'
                      AND shop_view_active_item.shop_code= '" . $item["shop_code"] . "'
                      AND shop_view_active_item.language_code = '" . $item["language_code"] . "'
                      ORDER BY shop_view_active_item.base_price ASC";*/

        $query = "SELECT shop_view_active_item.*
				FROM shop_view_active_item
				LEFT JOIN shop_item_link ON (shop_item_link.type=0 AND shop_item_link.item_no = '" . $item["item_no"] . "' AND shop_item_link.linked_item_no=shop_view_active_item.item_no)
				WHERE
					shop_view_active_item.company = '" . $GLOBALS['shop']['company'] . "'
        		  AND
					shop_view_active_item.shop_code= '" . $item["shop_code"] . "'
        		  AND 
					shop_view_active_item.language_code = '" . $item["language_code"] . "'
				  AND
					NOT ISNULL(shop_item_link.id)
        		ORDER BY shop_view_active_item.item_no ASC";
    }
    if ($query <> '') {
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) > 0) {
            echo "<div class='select_body_div'>";
            if ($parent_item == FALSE) {
                $descr = ($item["variant_type"] == "") ? $item["description"] : $item["variant_type"];
                echo "<div id=\"variant_select\" class='select_body_header' onclick=\"toggle('variant_select_over_" . $item["id"] . "');\">" . $descr . "</div>\n";
            } else {
                $descr = ($item["variant_type"] == "") ? $item["description"] : $item["variant_type"];
                echo "<div id=\"variant_select\" class='select_body_header' onclick=\"toggle('variant_select_over_" . $item["id"] . "');\">" . $descr . "</div>\n";
            }
            echo "<div id=\"variant_select_over_" . $item["id"] . "\" class=\"variant_select_over select_body_options\">";

            if ($GLOBALS["shop"]["variant_typ"] != 0) {
                if ($parent_item != "") {
                    $item = $parent_item;
                }
                $descr = ($item["variant_type"] == "") ? $item["description"] : $item["variant_type"];
                    $itemlink = "?var=true";

                echo "<div onclick=\"window.location.href = '" . $itemlink . "'\">" .
                    $descr;
                //get_inventory_sign($item);
                echo "</div>";
            }
            while ($variant_item = @mysqli_fetch_array($result)) {
                $variant_itemlink = "/".customizeUrl()."/".$variant_item["item_slug"].'-p'. $variant_item["id"] . "/?var=true";
                $descr = ($variant_item["variant_type"] == "") ? $variant_item["item_slug"] : $variant_item["variant_type"];
                echo "<div onclick=\"window.location.href = '" . $variant_itemlink . "'\">" .
                    $descr;
                //get_inventory_sign($variant_item);
                echo "</div>";
            }
            echo "</div></div>";
        }
    }
}

function variant_select_nav_new($item)
{
    $var_query = "SELECT *
				  FROM shop_item_variant
				  WHERE item_no = '" . $item['item_no'] . "'
				  AND company = '" . $GLOBALS['shop']['company'] . "'";
    $var_result = mysqli_query($GLOBALS['mysql_con'], $var_query);
    if (mysqli_num_rows($var_result) > 0) {
        ?>
        <select class='select' name='input_variant'
                onchange="location.href='?var=true&variant='+this.options[this.selectedIndex].value;">
            <?
            while ($var_row = mysqli_fetch_assoc($var_result)) {
                if ($_GET['variant'] == "") {
                    $_GET['variant'] = $var_row['code'];
                }
                if ($_GET['variant'] == $var_row['code']) {
                    $selected = " selected";
                } else {
                    $selected = "";
                }
                //Übersetzung suchen
                $description = get_variant_translation($item['item_no'], $var_row['code']);
                if ($description == '') {
                    $description = $var_row['description'];
                }
                echo("<option value=\"" . urlencode($var_row['code']) . "\"" . $selected . ">" . $description . "</option>");
            }
            ?>
        </select><br/><br/>
        <?
    }
}

function get_itemcard_sharing($item)
{
    require_once(__DIR__ . DIRECTORY_SEPARATOR . "recommend_item.php");
}

function get_itemcard_availability_notification()
{
    $str = '
    <div class="availability_message" id="availability_registration_wrapper">
        <a class="availability_message_button" onclick="$(this).next(\'.availability_message_box\').slideToggle(\'fast\');"><i class="fa fa-envelope" aria-hidden="true"></i>' . $GLOBALS['tc']['availability_message_button'] . '</a>
        <div class="availability_message_box" style="display: none">
            <form id="itemcard_availability_message" method="POST">
                <div class="form-group">
                    <p><strong>' . $GLOBALS['tc']['availability_message_info'] . '</strong></p>
                    <input class="form-control mail" name="input_availability_mail" id="input_availability_mail" type="email" placeholder="' . $GLOBALS['tc']['your_mail'] . '">
                </div>
                <input name="button" class="button" id="button" value="' . $GLOBALS['tc']['send'] . '" type="submit">
            </form>
        </div>
    </div>
    ';
    return $str;
}

function evaluate_availability_notification_registration($siteCode, $siteLanguageCode, $itemNo, $variantCode, $customerNo, $userName, $schemaAndDomain)
{
    $userEmail = filter_input(INPUT_POST, 'input_availability_mail', FILTER_SANITIZE_EMAIL);

    if ($userEmail && $siteCode && $siteLanguageCode && $itemNo) {
        $isLocal = (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false || strpos($_SERVER['HTTP_HOST'], '::1') !== false);
        if ($isLocal) {
            $schemaAndDomain = 'http://localhost';
        }
        $guzzleClient = new GuzzleHttp\Client(
            [
                'base_uri' => $schemaAndDomain,
            ]
        );

        $addr = '/module/workerqueue/workers/itemAvailabilityNotification/register_notification.php';

        $response = $guzzleClient->request(
            'POST',
            $addr,
            [
                'form_params' => [
                    'site_code' => $siteCode,
                    'site_language_code' => $siteLanguageCode,
                    'item_no' => $itemNo,
                    'variant_code' => $variantCode,
                    'customer_no' => $customerNo,
                    'user_email' => $userEmail,
                    'user_name' => $userName,
                ],
            ]
        );
        $body = $response->getBody()->getContents();
        return json_decode($body);
    }
    return null;
}

?>