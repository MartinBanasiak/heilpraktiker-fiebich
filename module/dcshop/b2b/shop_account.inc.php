<?

if (($GLOBALS['visitor']['frontend_login'] == 1 && $GLOBALS['visitor']['cookie_only'] == 1) || !$GLOBALS['visitor']['frontend_login']) {
    ?>
    <div class="login_wrapper">
        <form id="form_shop_login" name="form_shop_login" method="post"
              action="?action=edit_curr_shop_user&login=true">
            <div class="login_headline"><strong><?= $GLOBALS['tc']['log_in_to_use'] ?></strong><br></div>
            <div class="login_input_wrapper side_login_input_wrapper">
                <?
                $fields = get_login_fields_html($GLOBALS['shop']);
                echo $fields;
                ?>
            </div>
            <div class="ok_button_wrapper">
                <div class="ok_button" onclick="document.forms['form_shop_login'].submit();"></div>
            </div>
        </form>
    </div>
    <script type="text/javascript">
        $(function () {
            $('#input_password').focus();
        });
    </script>

    <?
} else {


    $pdoWrapper = $IOCContainer->create('DynCom\dc\common\classes\PDOQueryWrapper');
    $serverRequest = get_psr7_request_from_globals_single_instance();

//Init ItemRepository
    $webshopItemConfig = new DynCom\dc\dcShop\classes\WebshopItemConfig();
    $selectionCriteriaHelper = new \DynCom\dc\common\classes\SelectionCriteriaHelper();
    $itemCollection = new  DynCom\dc\dcShop\classes\WebshopItemCollection($webshopItemConfig, $selectionCriteriaHelper);
    $webshopItemRepository = new DynCom\dc\dcShop\classes\WebshopItemRepository($pdoWrapper, $webshopItemConfig, $selectionCriteriaHelper, $itemCollection, false);

    $orderLineConfig = new \DynCom\dc\dcShop\Document\WebshopOrderLineConfig();
    $orderCollection = new \DynCom\dc\dcShop\Document\WebshopOrderCollection(new \DynCom\dc\dcShop\Document\WebshopOrderConfig(), $orderLineConfig, $selectionCriteriaHelper, false);
    $orderConfig = new \DynCom\dc\dcShop\Document\WebshopOrderConfig();
    $orderLineCollection = new \DynCom\dc\dcShop\Document\WebshopOrderLineCollection($orderLineConfig, $orderConfig, $webshopItemConfig, $selectionCriteriaHelper);
    $orderLineRepo = new \DynCom\dc\dcShop\Document\WebshopOrderLineRepository($pdoWrapper, $orderLineConfig, $orderConfig, $webshopItemRepository, $selectionCriteriaHelper, $orderLineCollection, false);
    $OrderRepo = new \DynCom\dc\dcShop\Document\WebshopOrderRepository($pdoWrapper, $orderConfig, $orderLineRepo, $selectionCriteriaHelper, $orderCollection, false);

    $invoiceLineConfig = new \DynCom\dc\dcShop\Document\InvoiceLineConfig();
    $invoiceConfig = new \DynCom\dc\dcShop\Document\InvoiceConfig();
    $invoiceLineCollection = new \DynCom\dc\dcShop\Document\InvoiceLineCollection($invoiceLineConfig, $invoiceConfig, $webshopItemConfig, $selectionCriteriaHelper);
    $invoiceLineRepo = new \DynCom\dc\dcShop\Document\InvoiceLineRepository($pdoWrapper, $invoiceLineConfig, $invoiceConfig, $webshopItemRepository, $selectionCriteriaHelper, $invoiceLineCollection, false);
    $invoiceCollection = new \DynCom\dc\dcShop\Document\InvoiceCollection($invoiceConfig, $invoiceLineConfig, $selectionCriteriaHelper, false);
    $invoiceRepo = new \DynCom\dc\dcShop\Document\InvoiceRepository($pdoWrapper, $invoiceConfig, $invoiceLineRepo, $selectionCriteriaHelper, $invoiceCollection, false);


    $shipmentLineConfig = new \DynCom\dc\dcShop\Document\ShipmentLineConfig();
    $shipmentConfig = new \DynCom\dc\dcShop\Document\ShipmentConfig();
    $shipmentLineCollection = new \DynCom\dc\dcShop\Document\ShipmentLineCollection($shipmentLineConfig, $shipmentConfig, $webshopItemConfig, $selectionCriteriaHelper);
    $shipmentLineRepo = new \DynCom\dc\dcShop\Document\ShipmentLineRepository($pdoWrapper, $shipmentLineConfig, $shipmentConfig, $webshopItemRepository, $selectionCriteriaHelper, $shipmentLineCollection, false);
    $shipmentCollection = new \DynCom\dc\dcShop\Document\ShipmentCollection($shipmentConfig, $shipmentLineConfig, $selectionCriteriaHelper, false);
    $shipmentRepo = new \DynCom\dc\dcShop\Document\ShipmentRepository($pdoWrapper, $shipmentConfig, $shipmentLineRepo, $selectionCriteriaHelper, $shipmentCollection, false);


    $navOrderLineConfig = new \DynCom\dc\dcShop\Document\NavOrderLineConfig();
    $navOrderConfig = new \DynCom\dc\dcShop\Document\NavOrderConfig();
    $navOrderLineCollection = new \DynCom\dc\dcShop\Document\NavOrderLineCollection($navOrderLineConfig, $navOrderConfig, $webshopItemConfig, $selectionCriteriaHelper);
    $navOrderLineRepo = new \DynCom\dc\dcShop\Document\NavOrderLineRepository($pdoWrapper, $navOrderLineConfig, $navOrderConfig, $webshopItemRepository, $selectionCriteriaHelper, $navOrderLineCollection, false);
    $navOrderCollection = new \DynCom\dc\dcShop\Document\NavOrderCollection($navOrderConfig, $navOrderLineConfig, $selectionCriteriaHelper, false);
    $navOrderRepo = new \DynCom\dc\dcShop\Document\NavOrderRepository($pdoWrapper, $navOrderConfig, $navOrderLineRepo, $selectionCriteriaHelper, $navOrderCollection, false);


    $creditMemoLineConfig = new \DynCom\dc\dcShop\Document\CreditMemoLineConfig();
    $creditMemoConfig = new \DynCom\dc\dcShop\Document\CreditMemoConfig();
    $creditMemoLineCollection = new \DynCom\dc\dcShop\Document\CreditMemoLineCollection($creditMemoLineConfig, $creditMemoConfig, $webshopItemConfig, $selectionCriteriaHelper);
    $creditMemoLineRepo = new \DynCom\dc\dcShop\Document\CreditMemoLineRepository($pdoWrapper, $creditMemoLineConfig, $creditMemoConfig, $webshopItemRepository, $selectionCriteriaHelper, $creditMemoLineCollection, false);
    $creditMemoCollection = new \DynCom\dc\dcShop\Document\CreditMemoCollection($creditMemoConfig, $creditMemoLineConfig, $selectionCriteriaHelper, false);
    $creditMemoRepo = new \DynCom\dc\dcShop\Document\CreditMemoRepository($pdoWrapper, $creditMemoConfig, $creditMemoLineRepo, $selectionCriteriaHelper, $creditMemoCollection, false);

    $locale = $GLOBALS['language']['locale_code'];
    $textProvider = new \DynCom\dc\regionalization\PHPFileRegionalizedTextProvider($locale);

    $variantService = $IOCContainer->create('\DynCom\dc\dcShop\classes\WebshopItemVariantService');

    $countryRepo = $IOCContainer->create('\DynCom\dc\dcShop\classes\CountryRepository');

    $documentController = new \DynCom\dc\dcShop\Document\DocumentArchiveContoller($pdoWrapper, $OrderRepo, $invoiceRepo, $shipmentRepo, $navOrderRepo, $creditMemoRepo, $webshopItemRepository, $variantService, $countryRepo, null, $textProvider);


    $target = $serverRequest->getRequestTarget();
    $queryParams = $serverRequest->getQueryParams();
    $parsedBody = $serverRequest->getParsedBody();

    $edit_current_shop_user_action = "edit_curr_shop_user";
    $edit_shop_customer_action = "edit_shop_customer";
    $edit_shop_user_action = "edit_shop_user";
    $edit_shipment_address = "edit_shipment_address";
    $view_customer_shop_doucments_history = "webshop_document_history";
    $view_customer_doucments_history = "document_archive";
    $view_sales_return_history = "sales_return_history";


    ?>
    <div class="row">
        <div class="main_content_left col-xs-12 col-sm-12 col-md-3">
            <div id="subnavigation" class="shop_category_2">
                <ul>
                    <li><a class="<? if (strpos($target,  $edit_current_shop_user_action ) !== false) {
                            echo 'active';
                        } ?>"
                           href="?action=<?= $edit_current_shop_user_action; ?>"><?= $GLOBALS["tc"]["user_account"]; ?></a>
                    </li>
                    <li><a class="<? if (strpos($target,  $edit_shop_customer_action ) !== false) {
                            echo 'active';
                        } ?>"
                           href="?action=<?= $edit_shop_customer_action ?>"><?= $GLOBALS["tc"]["customer_account"]; ?></a>
                    </li>
                    <? if ($GLOBALS["shop_user"]["right_user_management"]) { ?>
                        <li><a class="<? if (strpos($target,  $edit_shop_user_action ) !== false) {
                                echo 'active';
                            } ?>"
                               href="?action=<?= $edit_shop_user_action ?> "><?= $GLOBALS["tc"]["user_management"]; ?></a>
                        </li>
                    <? } ?>
                    <li><a class="<? if (strpos($target,  $edit_shipment_address ) !== false) {
                            echo 'active';
                        } ?>"
                           href="?action=<?= $edit_shipment_address ?>"><?= $GLOBALS["tc"]["shipment_addresses"]; ?></a>
                    </li>
                    <? if ($GLOBALS["shop_user"]["right_order_history"]) { ?>
                        <li>
                            <a class="<? if (strpos($target,  $view_customer_shop_doucments_history ) !== false) {
                                echo 'active';
                            } ?>"
                               href="?action=<?= $view_customer_shop_doucments_history ?>"><?= $GLOBALS["tc"]["order_history"]; ?></a>
                        </li>
                        <li><a class="<? if (strpos($target,  $view_customer_doucments_history ) !== false) {
                                echo 'active';
                            } ?>"
                               href="?action=<?= $view_customer_doucments_history ?>"><?= $GLOBALS["tc"]["document_archive"]; ?></a>
                        </li>
                    <? } ?>
                    <? if ($GLOBALS["shop_user"]["right_return_order"] && $GLOBALS["shop_setup"]["show_rma"] == 1) { ?>
                        <li><a class="<? if (strpos($target,  $view_sales_return_history )) {
                                echo 'active';
                            } ?>"
                               href="?action=<?= $view_sales_return_history ?>"><?= $GLOBALS["tc"]["return_history"]; ?></a>
                        </li>
                    <? } ?>

                </ul>
            </div>
        </div>
        <div class="main_content_right col-xs-12 col-sm-12 col-md-9">
            <?
            if (strpos($target,  $edit_current_shop_user_action ) !== false) {
                require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_curr_shop_user.inc.php';
            } elseif (strpos($target,  $edit_shop_customer_action ) !== false) {
                require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_customer.inc.php';
            } elseif (strpos($target,  $edit_shop_user_action ) !== false) {
                if ($GLOBALS["shop_user"]["right_user_management"]) {
                    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_user.inc.php';
                } else {
                    echo "<div class=\"infobox\">" . $text_constant["de"]["no_user_rights"] . "</div>";
                }
            } elseif (strpos($target,  $edit_shipment_address ) !== false) {
                require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shipment_address.inc.php';
            } elseif (strpos($target,  $view_customer_shop_doucments_history )) {

                if ($GLOBALS["shop_user"]["right_order_history"]) {
                    // display the webshop doucment history templates
                    $locale = $GLOBALS['language']['locale_code'];
                    $textProvider = new \DynCom\dc\regionalization\PHPFileRegionalizedTextProvider($locale);

                    $maxRowsNumbers = $GLOBALS['shop_setup']['num_orders_per_page'];
                    $itemImagesPath =    $GLOBALS["shop_setup"]["image_config"][1]["path"] . "/";
                    $formUrl = "/".customizeUrl()."/account/?action=webshop_document_history";
                    $currencyCode = "€";
                    if ($GLOBALS["shop_language"]["default_currency_code"] != "") {
                        $currencyCode =  $GLOBALS["shop_language"]["default_currency_code"];
                    }

                    $output =   $documentController->showWebshopOrderHistorySearchForm($serverRequest, $currShopConfig->getCompany(), $currShopConfig->getCustomer(), $currShopConfig->getShopCode(), $currShopConfig->getShopLanguageCode(), $currencyCode, $GLOBALS["shop_user"]['email'], $itemImagesPath, $formUrl, $maxRowsNumbers, $GLOBALS["shop"]['prices_including_vat']);

                    echo $output;
                } else {
                    echo "<div class=\"infobox\">" . $text_constant["de"]["no_user_rights"] . "</div>";
                }
            } elseif (strpos($target,  $view_customer_doucments_history )) {
                // display the webshop doucment history templates

                if ($GLOBALS["shop_user"]["right_order_history"]) {
                    // display the webshop doucment history templates


                    $locale = $GLOBALS['language']['locale_code'];
                    $textProvider = new \DynCom\dc\regionalization\PHPFileRegionalizedTextProvider($locale);

                    $maxRowsNumbers = $GLOBALS['shop_setup']['num_orders_per_page'];
                    $itemImagesPath = $GLOBALS["shop_setup"]["image_config"][1]["path"] . "/";
                    $formUrl = "/".customizeUrl()."/account/?action=document_archive";
                    $currencyCode = "€";
                    if ($GLOBALS["shop_language"]["default_currency_code"] != "") {
                        $currencyCode =  $GLOBALS["shop_language"]["default_currency_code"];
                    }

                    $output =   $documentController->showDocumentHistorySearchForm($serverRequest, $currShopConfig->getCompany(), $currShopConfig->getCustomer(), $currShopConfig->getShopCode(), $currShopConfig->getShopLanguageCode(), $currencyCode, $itemImagesPath, $formUrl, $maxRowsNumbers,  $GLOBALS["shop"]['prices_including_vat']);
                    echo $output;


                } else {
                    echo "<div class=\"infobox\">" . $text_constant["de"]["no_user_rights"] . "</div>";
                }

            } elseif (strpos($target,  $view_sales_return_history )) {
                require_once __DIR__ . DIRECTORY_SEPARATOR . 'sales_return_history.inc.php';
            }
            ?>
        </div>
    </div>
    <?
}
?>