<?php
$translation        = \DynCom\dc\common\classes\Registry::get("translation");
$formname           = "form_shop_login_card";
$input_page_id      = (!empty($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_component_id = (!empty($_REQUEST["input_component_id"]) ? $_REQUEST["input_component_id"] : "");
$input_shop_login   = $login_sitepart;
?>

<div id="overlaycrumb">
    <?php if ($input_shop_login["id"] == "") {
        echo $translation->get("new_shop_login");
    } else {
        echo $translation->get("edit_login");
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

    <?= button("save", $translation->get("save"), $formname, "loadCard('save_shop_login', true)"); ?>


    <?= button("save", $translation->get("save_and_close"), $formname, "loadCard('save_shop_login', true, '', true)"); ?>
    <li>
        <ul>
            <?php
            if ($input_shop_login["id"] != "" && is_page_edit() === FALSE && is_component_edit() === FALSE) {
                echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_shop_login', true, '{$translation->get('delete_shop_login_confirm')}', true)", "", FALSE);
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

//Get Sitecodes
$sites     = array(0 => 0);
$sitecodes = array(0 => '');
$sitenames = array(0 => ' - ');
$query     = 'SELECT id,code,name FROM main_site';
$result    = @mysqli_query($GLOBALS['mysql_con'], $query);
$i         = 1;
while ($row = @mysqli_fetch_assoc($result)) {
    $sitecodes[$i]     = $row['code'];
    $sitenames[$i]     = $row['name'];
    $sites[$i]['id']   = $row['id'];
    $sites[$i]['code'] = $row['code'];
    $sites[$i]['name'] = $row['name'];
    ++$i;
}


//If site set, get language codes
$languagecodes = array(0 => 0);
$languagenames = array(0 => ' - ');
if (!empty($input_shop_login['target_site_code']) && !empty($sites)) {
    $target_site_id   = 0;
    $target_site_name = '';
    foreach ($sites as $site) {
        if ($site['code'] == $input_shop_login['target_site_code']) {
            $target_site_id   = $site['id'];
            $target_site_name = $site['name'];
        }
    }
    $languages = array();
    $query1    = 'SELECT id,code,name FROM main_language WHERE main_site_id = ' . (int)$target_site_id;
    $result1   = @mysqli_query($GLOBALS['mysql_con'], $query1);
    $i         = 1;
    while ($row1 = @mysqli_fetch_assoc($result1)) {
        $languagenames[$i]     = $row1['name'];
        $languagecodes[$i]     = $row1['code'];
        $languages[$i]['id']   = $row1['id'];
        $languages[$i]['code'] = $row1['code'];
        $languages[$i]['name'] = $row1['name'];
        ++$i;
    }
}
if (empty($input_shop_login['target_url']) && !empty($input_shop_login['target_site_code']) && !empty($input_shop_login['target_language_code'])) {
    $input_target_url = '/' . $input_shop_login['target_site_code'] . '/' . $input_shop_login['target_language_code'] . '/?action=shop_login';
} elseif (!empty($input_shop_login['target_url'])) {
    $input_target_url = $input_shop_login['target_url'];
} else {
    $input_target_url = '';
}

$input_description = (empty($input_shop_login['description']) ? $translation->get('webshop_login') : $input_shop_login['description']);
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" type="hidden" value="<?= $input_shop_login["id"] ?>">
    <input name="input_page_id" type="hidden" value="<?= $input_page_id ?>">
    <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />
    <input name="data-sitepartid" type="hidden" value="13">
    <input type="hidden" name="get_real_page_link_id" value="1" />
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
                <? input_select($translation->get("target_site_code"), 'input_target_site_code', $sitecodes, $sitenames, $input_shop_login['target_site_code'], FALSE, FALSE, 'onchange="$(\'#overlayWrapper ul.toolbar_menu\').find(\'.button_save\').first().click();"') ?>
            </td>
        </tr>
        <tr>
            <td>
                <? input_select($translation->get("target_language_code"), 'input_target_language_code', $languagecodes, $languagenames, $input_shop_login['target_language_code'], FALSE, FALSE, 'onchange="$(\'#overlayWrapper ul.toolbar_menu\').find(\'.button_save\').first().click();"') ?>
            </td>
        </tr>
        <tr>
            <td>
                <div class='label'><label for="input_target_url"><?= $translation->get('target_url') ?></label></div>
                <div class='input'><input type="text" name="input_target_url" id="input_target_url"
                                          value="<?= $input_target_url ?>" class="text"></div>
            </td>
        </tr>
        <tr>
            <td>
                <div class='label'>
                    <label for='input_show_labels_in_fields'><?= $translation->get('show_labels_in_fields') ?></label>
                </div>
                <div class='input'>
                    <select name="input_show_labels_in_fields" class="select">
                        <option value="0" <? if ($input_shop_login['method'] == '0') {
                            echo "selected='selected'";
                        } ?>><?= $translation->get('no') ?></option>
                        <option value="1" <? if ($input_shop_login['method'] == '1') {
                            echo "selected='selected'";
                        } ?>><?= $translation->get('yes') ?></option>
                    </select>
                </div>
            </td>
        </tr>
    </table>
</form>