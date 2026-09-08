<?php
/**
 * @var $shopConfig \DynCom\dc\dcShop\classes\CurrShopConfiguration
 */
$shopConfig = $IOCContainer->create('$CurrShopConfig');
$domain = $shopConfig->getBaseShopUrl();
$customer = $shopConfig->getCustomer();
$user = $shopConfig->getUser();
$currencyCode = $shopConfig->getCurrencyCode();
$advancedPriceProvider = $IOCContainer->create('DynCom\dc\dcShop\classes\AdvancedPriceProvider');
/*
 * @var $advancedPriceProvider AdvancedPriceProvider
 */
$graduatedPrices = $advancedPriceProvider->getGraduatedPrices($itemObj, $customer, $currencyCode);
$defaultItemPrice = $advancedPriceProvider->getItemCustomerPrice($itemObj, 1, $customer, $currencyCode);
//$PDO = $IOCContainer->create('PDOQueryWrapper');

$subscriptionDataBuilder = $IOCContainer->create('DynCom\dc\dcShop\subscriptions\classes\ItemSubscriptionDataBuilder');

$validator = new \DynCom\dc\common\classes\Validator([], []);
$formBuilder = new \DynCom\dc\common\classes\FormBuilder('dummyID');
$templating = $IOCContainer->create('DynCom\dc\common\classes\Templating');

$availabilityProvider = $IOCContainer->create('DynCom\dc\dcShop\classes\DefaultItemAvailabilityProvider');

$availability_code = $availabilityProvider->getItemAvailability($itemObj);
$is_available = $availabilityProvider->isItemAvailable($itemObj);

$variantService = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemVariantService');
/**
 * @var $variantService \DynCom\dc\dcShop\classes\WebshopItemVariantService
 */
if (!empty($parent_item)) {
    $variants = $variantService->getAllVariants($parentItemObj);
}
?>
<div class="table_area itemcard_details itemcard_infoblock">

    <div class="table_row item_no">
        <div class="table_cell details_label"><?= $GLOBALS["tc"]["item_no"] ?></div>
        <div class="table_cell details_value"><?= $item["item_no"] ?></div>
    </div>
    <?
    $query = "SELECT item_reference_no,description
  			FROM shop_item_cross_reference
  			WHERE item_no='" . $item['item_no'] . "'
  				AND (customer_no ='" . $GLOBALS['shop_customer']['customer_no'] . "' OR customer_no='')
  				AND company = '" . $GLOBALS['shop']['company'] . "'";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if (mysqli_num_rows($result) > 0) {
        $i = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['item_reference_no'] != "") {
                echo("<div class='table_row item_reference_no'>
		    <div class='table_cell details_label'>" . $GLOBALS["tc"]["reference_no"] . "</div>
		    <div class='table_cell details_value'>" . $row['item_reference_no'] . "</div>
		  	</div>");
            }
        }
    }
    ?>

</div>


<?

$hasVariants = count($variants) > 0;
/*
if (!$hasVariants) {
    $subscriptionData = $subscriptionDataBuilder->getItemSubscriptionData($itemObj);
    $builder = new \DynCom\dc\dcShop\classes\ItemOrderButtonBuilder($shopConfig, $availabilityProvider, $validator, $formBuilder, $templating);
    $htmlString = $builder->getItemcardButtonHTML($itemObj,$defaultItemPrice,$subscriptionData, true);
    $orderbox = "<div class='orderbox'><div class='orderbox_button'>".$htmlString."</div></div>";
    echo $orderbox;
}*/
?>
<? if ($GLOBALS['language']['catalog_login']) { ?>
    <div class='orderbox'>
        <div class='orderbox_button'>
            <div class="submit_button">
                <input type="button" class="button" onclick="beforeShowModal(this);" name="login_buy_buttton"
                       id="login_buy_buttton_<?= $item['id'] ?>" data-item="<?= $item['id'] ?>" data-toggle="modal"
                       data-target="#login_buy_modal" value="<?= $GLOBALS['tc']['login_buy'] ?>">
            </div>
        </div>
    </div>
<? }


$query = "SELECT  * 
             FROM main_shop_login
                where main_language_id = " . $GLOBALS["language"]["id"] . " order by id desc  ";

$sitePartResult = @mysqli_query($GLOBALS['mysql_con'], $query);
$siteParts = @mysqli_fetch_array($result);

$siteLanguageUsedLoginSitePart = array();
    while ($sitePart = @mysqli_fetch_assoc($sitePartResult)) {

        $query = "SELECT  * 
                 FROM main_component_link
                    where main_sitepart_id = 13 and main_sitepart_header_id = " . $sitePart['id'] . "  ";

        $sitePartlinkResult = @mysqli_query($GLOBALS['mysql_con'], $query);
        $sitePartLinks = @mysqli_fetch_array($sitePartlinkResult);
        if (count($sitePartLinks) > 0) {
            $siteLanguageUsedLoginSitePart = $sitePart;
            break;
        }
    }

$redirectSiteCode = $siteLanguageUsedLoginSitePart['target_site_code'];

$loginShopUrl = ' https://' . $_SERVER['SERVER_NAME'] . "/" . ltrim($siteLanguageUsedLoginSitePart['target_url'], '/');

create_shop_login_modal($loginShopUrl);


$companyCodeValue = $GLOBALS["shop"]["company"];
$loginSiteId = $GLOBALS["language"]["logout_site_id"];
$loginLangaugeId = $GLOBALS["language"]["logout_language_id"];

$query = "SELECT main_language.*
                         FROM main_language 
                            JOIN main_site 
                              ON main_language.id = main_site.std_main_language_id and main_site.code= '" . $redirectSiteCode . "'
                              
                WHERE   main_language.company =  '" . $companyCodeValue . "'     
                         ";

$result = @mysqli_query($GLOBALS['mysql_con'], $query);
$redirectMainLanguage = @mysqli_fetch_array($result);

?>
<script language="JavaScript">
    function beforeShowModal(clickedButton) {
        var item_id = $(clickedButton).data("item");
        $("#catalog_selected_item").val(item_id);
        $("#login_error_Message").hide();
    }

    var companyCodeValue = "<?= $GLOBALS["shop"]["company"]?>";
    var siteValue = "<?= $redirectMainLanguage['shop_code']?>";
    var langaugeValue = "<?= $redirectMainLanguage['shop_language_code']?>";
    function checkUserData() {
        var forminputs = $("#form_shop_login_model").serializeArray();
        var companyData = {};
        companyData['name'] = "company";
        companyData['value'] = companyCodeValue;
        forminputs.push(companyData);


        var siteData = {};
        siteData['name'] = "site";
        siteData['value'] = siteValue;
        forminputs.push(siteData);

        var languageData = {};
        languageData['name'] = "language";
        languageData['value'] = langaugeValue;
        forminputs.push(languageData);
        return $.ajax({
            type: "POST",
            url: "/module/dcshop/user_check_ajax.php?action=shop_login",
            data: forminputs,
            success: function (data) {
                if (data == true) {
                    $("#form_shop_login_model").submit();
                }
                else {
                    $("#login_error_Message").show();
                    return false;
                }


            }
        });
    }


    $("#login_buy_modal_submit_button").click(function () {
        checkUserData();
    });


</script>





