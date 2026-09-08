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
    ?>
    <div class="row">
        <div class="main_content_left col-xs-12 col-sm-12 col-md-3">
            <div id="subnavigation" class="shop_category_2">
                <ul>
                    <li><a class="<? if ($_GET["action"] == "shop_login_account" || $_GET["action"] == "shop_login") {
                            echo 'active';
                        } ?>"
                           href="<?= ml("", "action", "shop_login_account"); ?>"><?= $GLOBALS["tc"]["homepage"]; ?></a>
                    </li>
                    <li><a class="<?if($_GET["action"] == "edit_curr_shop_user") { echo 'active'; }?>" href="?action=edit_curr_shop_user"><?=$GLOBALS["tc"]["user_account"];?></a></li>
                    <li><a class="<?if($_GET["action"] == "edit_shop_customer") { echo 'active'; }?>" href="?action=edit_shop_customer"><?=$GLOBALS["tc"]["customer_account"];?></a></li>
                    <li><a class="<?if($_GET["action"] == "edit_shipment_address") { echo 'active'; }?>" href="?action=edit_shipment_address"><?=$GLOBALS["tc"]["shipment_addresses"];?></a></li>
                    <li><a class="<?if($_GET["action"] == "order_history") { echo 'active'; }?>" href="?action=order_history"><?=$GLOBALS["tc"]["order_history"];?></a></li>
                    <li><a class="<?if($_GET["action"] == "newsletter") { echo 'active'; }?>" href="?action=newsletter"><?=$GLOBALS["tc"]["newsletter"];?></a></li>
                </ul>
            </div>
        </div>
        <div class="main_content_right col-xs-12 col-sm-12 col-md-9">
            <?
            switch ($_GET["action"]) {
                case "shop_login_account":
                case "shop_login":
                    if (($GLOBALS["visitor"]["frontend_login"])) {
                        //if ($GLOBALS['shop_customer']['surname'] != "") {
                        //    $name = $GLOBALS['shop_customer']['surname'];
                        //} else {
                            $name = $GLOBALS['shop_customer']['name'];
                        //}
                        ?>
                        <div class="category_info">
                            <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["welcome"] ?> <?= $name ?>!</h1>
                            <? if ($GLOBALS['shop_language']['login_welcome_text_module'] != '') {
                                $spacer = array();
                                echo(get_text_module($GLOBALS['shop_language']['company'], $GLOBALS['shop_language']['login_welcome_text_module'], $spacer));
                            } ?>
                        </div>
                        <div class="user_account_start">
                            <?get_content('user_account_start');?>
                        </div>
                        <?
                    }
                    break;
                case "edit_shipment_address":
                    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shipment_address.inc.php';
                    break;
                case "edit_shop_customer":
                    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_customer.inc.php';
                    break;
                case "edit_curr_shop_user":
                    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_curr_shop_user.inc.php';
                    break;
                case "order_history":
                    if ($GLOBALS["shop_user"]["right_order_history"]) {
                        require_once __DIR__ . DIRECTORY_SEPARATOR . 'order_history.inc.php';
                    } else {
                        echo "<div class=\"infobox\">" . $text_constant["de"]["no_user_rights"] . "</div>";
                    }
                    break;
                case "newsletter":
                    require_once __DIR__ . DIRECTORY_SEPARATOR . 'account_newsletter.inc.php';
                    break;
            }?>
        </div>
    </div>
<?}?>