<?php
$translation               = \DynCom\dc\common\classes\Registry::get("translation");
$formname                  = "form_newsletter_sitepart_card";
$input_page_id             = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_component_id        = (isset($_REQUEST["input_component_id"]) ? $_REQUEST["input_component_id"] : "");
$input_newsletter_sitepart = $newsletter_sitepart;

//Load environment variables from config if exists
$envDir = rtrim(dirname(dirname(__DIR__)),'/') . '/config';
if (is_dir($envDir)) {
    $dotenv = new \Dotenv\Dotenv($envDir);
    $dotenv->load();
}

$newsletter_version = getenv('NEWSLETTER_VERSION');
if (empty($newsletter_version)) {
    $newsletter_version = 0;
}

?>

<div id="overlaycrumb">
    <?php if (empty($input_newsletter_sitepart["id"])) {
        echo $translation->get("new_newsletter_sitepart");
    } else {
        echo $translation->get("edit_newsletter_sitepart");
    }
    ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<ul class="toolbar_menu">
    <?php
    if (is_page_edit() || is_component_edit()) {
        button("left", $translation->get("back"), $formname, "loadCard('edit_content_page', true)");
    }
    ?>

    <?= button("save", $translation->get("save"), $formname, "loadCard('save_newsletter_sitepart', true)"); ?>


    <?= button("save", $translation->get("save_and_close"), $formname, "loadCard('save_newsletter_sitepart', true, '', true)"); ?>
    <li>
        <ul>
            <?php
            if ($input_newsletter_sitepart["id"] != "" && is_page_edit() === FALSE && is_component_edit() === FALSE) {
                echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_newsletter_sitepart', true, '{$translation->get('delete_newsletter_sitepart_confirm')}', true)", "", FALSE);
            }
            ?>
            <?= button("reset", $translation->get("restore"), $formname, "?action=edit_iframe", "", FALSE); ?>
        </ul>
    </li>
</ul>
<div class="clearfix"></div>

<?php
if (count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
}

$input_description = (empty($input_newsletter_sitepart['description']) ? $translation->get('newsletter_sitepart') : $input_newsletter_sitepart['description']);
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" type="hidden" value="<?= $input_newsletter_sitepart["id"] ?>">
    <input name="input_page_id" type="hidden" value="<?= $input_page_id ?>">
    <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />
    <input name="data-sitepartid" type="hidden" value="15">
    <input type="hidden" name="get_real_page_link_id" value="1" />
    <? if ($newsletter_version == 1) {}
    else {
        ?>
        <input type="hidden" name="input_group_id" value="<?= $input_newsletter_sitepart["group_id"] ?>"/>
        <?
    }
    ?>
    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <div class='label'><label for="input_description"><?= $translation->get('description') ?></label></div>
                <div class='input'><input type="text" name="input_description" id="input_description"
                                          value="<?= $input_description ?>" class="text"></div>
            </td>
        </tr>
        <tr>
            <td>
                <? if ($newsletter_version == 1) {
                    ?>
                    <div class='label'><label for="input_db_id"><?= $translation->get('db-id') ?></label></div>
                    <div class='input'><input type="text" name="input_db_id" id="input_db_id"
                                              value="<?= $input_newsletter_sitepart['db_id'] ?>" class="text"></div>
                    <?
                }
                else {
                ?>
                    <div class='label'><label for="input_group_name"><?= $translation->get('group') ?></label></div>
                    <div class='input'><input type="text" name="input_group_name" id="input_group_name"
                                              value="<?= $input_newsletter_sitepart['group_name'] ?>" class="text"></div>
                <? } ?>
            </td>
        </tr>
        <tr>
            <td>
                <? if ($newsletter_version == 1) {
                ?>
                    <div class='label'><label for="input_access_token"><?= $translation->get('access-token') ?></label>
                    </div>
                    <div class='input'><input type="text" name="input_access_token" id="input_access_token"
                                              value="<?= $input_newsletter_sitepart['access_token'] ?>" class="text"></div>
                    <?
                }
                else {
                ?>
                    <div class='label'><label for="input_account"><?= $translation->get('account') ?></label>
                    </div>
                    <div class='input'><input type="text" name="input_account" id="input_account"
                                              value="<?= $input_newsletter_sitepart['account'] ?>" class="text"></div>
                <? } ?>
            </td>
        </tr>
        <? if ($newsletter_version == 1) {
        ?>
            <?
        }
        else {
        ?>
            <tr>
                <td>
                    <div class='label'><label for="input_login"><?= $translation->get('login') ?></label>
                    </div>
                    <div class='input'><input type="text" name="input_login" id="input_login"
                                              value="<?= $input_newsletter_sitepart['login'] ?>" class="text"></div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class='label'><label for="input_password"><?= $translation->get('password') ?></label>
                    </div>
                    <div class='input'><input type="password" name="input_password" id="input_password"
                                              value="<?= $input_newsletter_sitepart['password'] ?>" class="text"></div>
                </td>
            </tr>
        <? } ?>
        <tr>
            <td>
                <div class='label'><label for="input_segment"><?= $translation->get('segment') ?></label></div>
                <div class='input'><input type="text" name="input_segment" id="input_segment"
                                          value="<?= $input_newsletter_sitepart['segment'] ?>" class="text"></div>
            </td>
        </tr>
        <tr>
            <td>
                <? input_select($translation->get("registration_deregistration"), 'input_type', array(0, 1, 2, 3), array($translation->get('registration'), $translation->get('deregistration'), $translation->get('newsletter_forward'), $translation->get('gender_buttons')), $input_newsletter_sitepart['type'], FALSE, FALSE, 'onchange="$(\'#overlayWrapper ul.toolbar_menu\').find(\'.button_save\').first().click();"') ?>
            </td>
        </tr>
        <tr class="forwarding_only" <? if ($input_newsletter_sitepart['type'] != 2) {
            echo " style='display: none;' ";
        } ?>>
            <td>
                <div class='label'><label for="input_forwarding_url"><?= $translation->get('forwarding_url') ?></label></div>
                <div class='input'><input type="text" name="input_forwarding_url" id="input_forwarding_url"
                                          value="<?= $input_newsletter_sitepart['forwarding_url'] ?>" class="text"></div>
            </td>
        </tr>
        <tr>
            <td>
                <? input_select($translation->get("label_position"), 'input_label_position', array(0, 1), array($translation->get('outside'), $translation->get('inside')), $input_newsletter_sitepart['label_position'], FALSE, FALSE, '') ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <h2><?php echo $translation->get("text"); ?></h2>

                <?php
                show_ck_editor($input_newsletter_sitepart['text'], "dc", 'layout/frontend/' . $GLOBALS["layout"]["code"] . '/dist/css/fck.css', 'layout/frontend/' . $GLOBALS["layout"]["code"] . '/fckstyles.xml');
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <h2><?= $translation->get('fields') ?></h2>
                <? input_select($translation->get("email"), 'input_type', array(0, 1), array($translation->get('active'), $translation->get('inactive')), $input_newsletter_sitepart['email'], TRUE, FALSE, '') ?>
            </td>
        </tr>
        <tr class="registration_only" <? if ($input_newsletter_sitepart['type'] == 1) {
            echo " style='display: none;' ";
        } ?>>
            <td>
                <h3><?= $translation->get('optional_fields') ?></h3>
                <? input_select($translation->get("salutation"), 'input_salutation_active', array(0, 1), array($translation->get('inactive'), $translation->get('active')), $input_newsletter_sitepart['salutation_active'], FALSE, FALSE, '') ?>
            </td>
        </tr>
        <tr class="registration_only" <? if ($input_newsletter_sitepart['type'] == 1) {
            echo " style='display: none;' ";
        } ?>>
            <td>
                <? input_select($translation->get("name_status"), 'input_name_status', array(0, 1, 2), array($translation->get('inactive'), $translation->get('name_split'), $translation->get('name_combined')), $input_newsletter_sitepart['name_status'], FALSE, FALSE, '') ?>
            </td>
        </tr>
        <tr class="registration_only" <? if ($input_newsletter_sitepart['type'] == 1) {
            echo " style='display: none;' ";
        } ?>>
            <td>
                <? input_select($translation->get("city"), 'input_city_active', array(0, 1), array($translation->get('inactive'), $translation->get('active')), $input_newsletter_sitepart['city_active'], FALSE, FALSE, '') ?>
            </td>
        </tr>
        <tr class="registration_only" <? if ($input_newsletter_sitepart['type'] == 1) {
            echo " style='display: none;' ";
        } ?>>
            <td>
                <? input_select($translation->get("post_code"), 'input_post_code_active', array(0, 1), array($translation->get('inactive'), $translation->get('active')), $input_newsletter_sitepart['post_code_active'], FALSE, FALSE, '') ?>
            </td>
        </tr>
        <tr class="registration_only" <? if ($input_newsletter_sitepart['type'] == 1) {
            echo " style='display: none;' ";
        } ?>>
            <td>
                <? input_select($translation->get("birthday"), 'input_birthday_active', array(0, 1), array($translation->get('inactive'), $translation->get('active')), $input_newsletter_sitepart['city_active'], FALSE, FALSE, '') ?>
            </td>
        </tr>
    </table>
</form>