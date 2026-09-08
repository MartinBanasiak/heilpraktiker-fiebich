<?
if (($GLOBALS['visitor']['frontend_login'] == 1 && $GLOBALS['visitor']['cookie_only'] == 1) || !$GLOBALS['visitor']['frontend_login']){
    ?>
    <div class="login_wrapper">
        <form id="form_shop_login" name="form_shop_login" method="post"
              action="?shop_category=account&action=edit_curr_shop_user&login=true">
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
?>
<div class="title">
    <?= $GLOBALS["tc"]["my_account"] ?>
</div>
<div id="account_body">
    <div class="shop_category_2 account_menu">
        <ul>
            <li><?= ($_GET["action"] <> "edit_curr_shop_user") ? "<a href=\"" . ml("", "action", "edit_curr_shop_user") . "\">&gt;&nbsp;" . $GLOBALS["tc"]["user_account"] . "</a>" : "<strong>&gt;&nbsp;" . $GLOBALS["tc"]["user_account"] . "</strong>"; ?></li>
            <li><?= ($_GET["action"] <> "edit_shop_customer") ? "<a href=\"" . ml("", "action", "edit_shop_customer") . "\">&gt;&nbsp;" . $GLOBALS["tc"]["customer_account"] . "</a>" : "<strong>&gt;&nbsp;" . $GLOBALS["tc"]["customer_account"] . "</strong>"; ?></li>
            <li><?= ($_GET["action"] <> "edit_shop_user") ? "<a href=\"" . ml("", "action", "edit_shop_user") . "\">&gt;&nbsp;" . $GLOBALS["tc"]["user_management"] . "</a>" : "<strong>&gt;&nbsp;" . $GLOBALS["tc"]["user_management"] . "</strong>"; ?></li>
            <li><?= ($_GET["action"] <> "edit_shipment_address") ? "<a href=\"" . ml("", "action", "edit_shipment_address") . "\">&gt;&nbsp;" . $GLOBALS["tc"]["shipment_addresses"] . "</a>" : "<strong>&gt;&nbsp;" . $GLOBALS["tc"]["shipment_addresses"] . "</strong>"; ?></li>
            <li><?= ($_GET["action"] <> "order_history" && $_GET["action"] <> "contract_history") ? "<a href=\"" . ml("", "action", "order_history") . "\">&gt;&nbsp;" . $GLOBALS["tc"]["order_history"] . "</a>" : "<strong>&gt;&nbsp;" . $GLOBALS["tc"]["order_history"] . "</strong>"; ?></li>
            <li><?= ($_GET["action"] <> "sales_invoice_history") ? "<a href=\"" . ml("", "action", "sales_invoice_history") . "\">&gt;&nbsp;" . $GLOBALS["tc"]["invoice_history"] . "</a>" : "<strong>&gt;&nbsp;" . $GLOBALS["tc"]["invoice_history"] . "</strong>"; ?></li>
            <li><?= ($_GET["action"] <> "sales_shipment_history") ? "<a href=\"" . ml("", "action", "sales_shipment_history") . "\">&gt;&nbsp;" . $GLOBALS["tc"]["shipment_history"] . "</a>" : "<strong>&gt;&nbsp;" . $GLOBALS["tc"]["shipment_history"] . "</strong>"; ?></li>
        </ul>
    </div>
    <?
    switch ($_GET["action"]) {
        case "edit_shipment_address":
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shipment_address.inc.php';
            break;
        case "edit_shop_user":
            if ($GLOBALS["shop_user"]["right_user_management"]) {
                require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_user.inc.php';
            } else {
                echo "<div class=\"infobox\">" . $text_constant["de"]["no_user_rights"] . "</div>";
            }
            break;
        case "edit_shop_customer":
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_customer.inc.php';
            break;
        case "edit_curr_shop_user":
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_curr_shop_user.inc.php';
            break;
        case "order_history":
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'order_history.inc.php';
            break;
        case "contract_history":
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'sales_contract_history.inc.php';
            break;
        case "sales_shipment_history":
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'sales_shipment_history.inc.php';
            break;
        case "sales_invoice_history":
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'sales_invoice_history.inc.php';
            break;
        case "edit_curr_salesperson":
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_curr_salesperson.inc.php';
            break;
        case "show_salesperson_customers":
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'show_salesperson_customers.inc.php';
            break;
    }

    }
    ?>
</div>