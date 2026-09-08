<?php
$formname    = "form_admin_user_card";
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$inputname   = "input_id";
$disabled    = TRUE;

$query       = "SELECT id, code, name FROM main_site ORDER BY id ASC";
$result      = @mysqli_query($GLOBALS['mysql_con'], $query);
$sites       = array();
$site_values = array();
$i           = 0;
while ($row = @mysqli_fetch_array($result)) {
    $sites[]         = $row['name'];
    $site_values[$i] = $row['id'];
    $i++;
}

$passwordLength = (int)getenv('PASSWORD_STRENGTH_LENGTH');
if($passwordLength < 1)
{
    $passwordLength = 6;
}

function create_adminuser_site_checkbox( $user_id ) {

    if($user_id == "") {
        $siteAdminLink = false;
    } else {
        $query             = "SELECT * FROM main_site_admin_user_link where main_admin_user_id = '" . $user_id."'";
        $result            = @mysqli_query($GLOBALS['mysql_con'], $query);
        $siteAdminAvailableSites = array();
        if (@mysqli_num_rows($result) > 0) {
            $siteAdminLink = true;
            while ($row = @mysqli_fetch_array($result)) {
                $siteAdminAvailableSites[] = $row['main_site_id'];
            }
        } else {
            $siteAdminLink = false;
        }
    }

    $query = "SELECT * from main_site";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    while ($siterow = @mysqli_fetch_assoc($result)) {
        if($siteAdminLink === false || in_array($siterow['id'], $siteAdminAvailableSites)) {
            $ckecked = 1;
        } else {
            $ckecked = 0;
        }

        input($siterow['name'], "input_site_admin_link_" . $siterow["id"], "checkbox", $ckecked);
    }
}

function create_adminuser_shop_checkbox( $user_id ) {

    if($user_id == "") {
        $shopAdminLink = false;
    } else {
        $query             = "SELECT * FROM main_admin_user_shop_link where main_admin_user_id = '" . $user_id."'";
        $result            = @mysqli_query($GLOBALS['mysql_con'], $query);
        $siteAdminAvailableShops = array();
        if (@mysqli_num_rows($result) > 0) {
            $shopAdminLink = true;
            while ($row = @mysqli_fetch_array($result)) {
                if($row['active'] == 1) {
                    $siteAdminAvailableShops[] = $row['shop_code'];
                }
            }
        } else {
            $shopAdminLink = false;
        }
    }

    $query = "SELECT * from shop_shop";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    while ($siterow = @mysqli_fetch_assoc($result)) {
        if($shopAdminLink === false || in_array($siterow['code'], $siteAdminAvailableShops)) {
            $ckecked = 1;
        } else {
            $ckecked = 0;
        }

        input($siterow['description'], "input_shop_admin_link_" . $siterow["id"], "checkbox", $ckecked);
    }
}

?>

<div id="overlaycrumb">
    <?php if ($input_admin_user["id"] == "") {
        echo $translation->get("new_user");
    } else {
        echo $translation->get("edit_user");
    }
    ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<? if ($GLOBALS['admin_user']['right_create_user'] == 1 || $GLOBALS['admin_user']['is_super_user'] == 1) {
    $disabled = FALSE;
}?>
    <ul class="toolbar_menu">
        <?= button("save", $translation->get("save"), $formname, "loadCard('save', true)"); ?>
        <?= button("save", $translation->get("save_and_close"), $formname, "loadCard('save', true, '', true)"); ?>
        <li>
            <ul>
                <?php
                if ($input_admin_user["id"] != "" && ($GLOBALS['admin_user']['right_create_user'] == 1 || $GLOBALS['admin_user']['is_super_user'] == 1)) {
                    echo button("delete", $translation->get("delete"), $formname, "loadCard('delete', true, '{$translation->get('delete_user_confirm')}', true)", "", FALSE);
                }
                ?>
                <?= button("reset", $translation->get("restore"), $formname, "?action=edit", "", FALSE); ?>
            </ul>
        </li>
    </ul>
    <div class="clearfix"></div>
<? //} ?>

<?php
if (count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
}
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" type="hidden" value="<?= $input_admin_user["id"] ?>">

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <? input($translation->get("name"), "input_name", "hidden", $input_admin_user["name"], 45) ?>
                <? input($translation->get("email"), "input_email", "hidden", $input_admin_user["email"], 100) ?>
                <? input($translation->get("login"), "input_login", "hidden", $input_admin_user["login"], 20) ?>
                <? input($translation->get("create_new_user_right"), "input_right_create_user", "hidden", $input_admin_user["right_create_user"], 45) ?>


                <? input($translation->get("name"), "input_name", "text", $input_admin_user["name"], 45, $disabled) ?>
                <? input($translation->get("email"), "input_email", "text", $input_admin_user["email"], 100, $disabled) ?>
                <br />
                <? input($translation->get("login"), "input_login", "code", $input_admin_user["login"], 20, $disabled) ?>
                <? if($input_admin_user['id'] == $GLOBALS['admin_user']['id']) { input($translation->get("old_password"), "input_old_password", "password","", 45, false);} ?>
                <? input($translation->get("password"), "input_password", "password","", 45, false) ?>
                <? input($translation->get("password_repeat"), "input_password2", "password","", 45, false) ?>
                <? input("", "input_score", "hidden", 0, 45) ?>
                <? input($translation->get("create_new_user_right"), "input_right_create_user", "checkbox", $input_admin_user["right_create_user"], 45, $disabled) ?>
                <? input_select($translation->get("edit_mode"), "input_edit_mode", array(0, 1), array($translation->get('live_edit'), $translation->get('backend_edit')), $input_admin_user["edit_mode"]); ?>
            </td>
            <td>
                <? input_select($translation->get("main_website"), "main_site_id", $values = $site_values, $value_names = $sites, $input_admin_user["main_site_id"]); ?>
                <? input_select($translation->get("backend_language"), "main_language", $values = array(0 => 'de', 1 => 'en'), $value_names = array('Deutsch', 'Englisch'), $input_admin_user["main_language"]); ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <h2><?php echo $translation->get("admin_user_site"); ?></h2>
                <?php create_adminuser_site_checkbox($input_admin_user["id"]); ?>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <h2><?php echo $translation->get("admin_user_shop"); ?></h2>
                <?php create_adminuser_shop_checkbox($input_admin_user["id"]); ?>
            </td>
        </tr>
    </table>

</form>
<script type="text/javascript">

    jQuery(document).ready(function($) {

      var  options = {
            shortPass: '<?= $GLOBALS['tc']['short_password'] ?> ',
            badPass: '<?= $GLOBALS['tc']['weak_password'] ?> ',
            goodPass:  '<?= $GLOBALS['tc']['good_password'] ?> ',
            strongPass:  '<?= $GLOBALS['tc']['strong_password'] ?> ',
            containsUsername: '',
            enterPass: '',
            showPercent: false,
            showText: false, // shows the text tips
            animate: true, // whether or not to animate the progress bar on input blur/focus
            animateSpeed: 'fast', // the above animation speed
            username: false, // select the username field (selector or jQuery instance) for better password checks
            usernamePartialMatch: true, // whether to check for username partials
            minimumLength: '<?= $passwordLength ?>' // minimum password length (below this threshold, the score is 0)
        };


        $('#input_password').password(options).bind('password.score', function (e, score) {
            $('#input_score').val(score);
        });

    });





</script>