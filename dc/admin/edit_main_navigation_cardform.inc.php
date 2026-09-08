<? $formname        = "form_edit_main_navigation_card";
$main_layout_id     = $GLOBALS["language"]["main_layout_id"];
$main_navigation_id = $input_main_navigation["id"];
$translation        = \DynCom\dc\common\classes\Registry::get("translation");
require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'shop_functions.inc.php';

?>

<script type="text/javascript">
    function select_forwardtype( value ) {
        $('#select_navigation_id, #select_page_id, #external_url, #insert_path').hide();
        value = parseInt(value);
        switch (value) {
            case 1:
                $('#select_page_id').show();
                break;

            case 2:
                $('#select_navigation_id').show();
                break;

            case 3:
                $('#external_url').show();
                break;

            case 5:
            	$('#insert_path').show();
            	break;

            case 6:
                $('#shop_category').show();
                break;
        }
    }

    function on_page_select(value) {
        switch (value) {
            case 'new_site_from_template':
                $('#template_select').show();
                break;

            default:
                $('#template_select').hide();
                break;
        }
    }
</script>

<div id="overlaycrumb">
    <?php if ($input_main_navigation["id"] == "") {
        echo $translation->get("new_menuitem_headline");
    } else {
        echo $translation->get("edit_menuitem");
    }
    ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<ul class="toolbar_menu">
    <?= button("save", $translation->get("save"), $formname, "loadCard('save_navigation', true)"); ?>
    <?= button("save", $translation->get("save_and_close"), $formname, "loadCard('save_navigation', true, '', true)"); ?>
    <li>
        <ul>

            <?php
            if ($input_main_navigation["id"] != "") {

                if ($input_main_navigation['forward_type'] == 1 && $input_main_navigation['forward_page_id'] > 0) {
                    button("edit", $translation->get("edit_site"), $formname, "gotoLocation('" . get_menu_link('structure/sites', $GLOBALS["site"], $GLOBALS["language"], "?action=open_card_page&input_page_id=" . $input_main_navigation['forward_page_id']) . "')", "", FALSE);
                }

                echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_navigation', true, '{$translation->get('delete_navitem_confirm')}', true)", "", FALSE);

            }
            ?>
            <?= button("reset", $translation->get("restore"), $formname, "?action=edit", "", FALSE); ?>
        </ul>
    </li>
</ul>
<div class="clearfix"></div>

<?php
if (isset($messages) && count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
}
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_main_navigation_id" type="hidden" value="<?= $input_main_navigation["id"] ?>">
    <input name="input_main_layout_id" type="hidden" value="<?= $main_layout_id ?>">

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <? input($translation->get("textkey"), "input_code", "code", $input_main_navigation["code"], 20) ?>
                <? input($translation->get("name"), "input_menu_name", "text", $input_main_navigation["menu_name"], 100) ?>
                <br />
                <?php input_select($translation->get("use_menuitem_for"), "input_forward_type", array(0 => "1", 1 => "2", 2 => "3", 3 => "4", 4 => "5", 5 => "6"), array(0 => $translation->get("assign_page"), 1 => $translation->get("internal_forward"), 2 => $translation->get("external_forward"), 3 => $translation->get("placeholder"), 4 => $translation->get("internal_path_forward"), 5 => $translation->get("shop_category_forward")), $input_main_navigation['forward_type'], FALSE, FALSE, 'onchange="select_forwardtype(this.value);"') ?>
                <div
                    id="select_navigation_id" <?php echo $input_main_navigation['forward_type'] != 2 ? 'style="display:none;"' : ''; ?>>
                    <? navigation_select($translation->get("choose_navigation_item"), "input_forward_navigation_id", $input_main_navigation["forward_navigation_id"], $GLOBALS["site"]['id'], $GLOBALS["language"]['id'], FALSE); ?>
                </div>

                <div
                    id="select_page_id" <?php echo $input_main_navigation['forward_type'] != 1 && $input_main_navigation['forward_type'] != 0 ? 'style="display:none;"' : ''; ?>>
                    <? page_select($translation->get("choose_page"), "input_forward_page_id", $input_main_navigation, FALSE, "on_page_select(this.value);"); ?>
                    <div id="template_select">
                        <? template_select($translation->get("choose_template"), "input_template_id", $input_main_navigation['template_id'], FALSE); ?>
                    </div>

                </div>

                <div
                    id="external_url" <?php echo $input_main_navigation['forward_type'] != 3 ? 'style="display:none;"' : ''; ?>>
                    <? input($translation->get("external_url"), "input_forward_url_external", "text", $input_main_navigation["forward_url"], 255) ?>
                </div>

                <div id="insert_path" <?php echo $input_main_navigation['forward_type'] != 5 ? 'style="display:none;"' : ''; ?>>
                	<? input($translation->get("internal_url"), "input_forward_url", "text", $input_main_navigation["forward_url"], 255) ?>
                </div>

                <div id="shop_category" <?php echo $input_main_navigation['forward_type'] != 6 ? 'style="display:none;"' : ''; ?>>
                    <?
                    $company = $GLOBALS['language']['company'];
                    $shop_code = $GLOBALS['language']['shop_code'];
                    $language_code = $GLOBALS['language']['shop_language_code'];
                    $shop = get_shop($company,$shop_code);
                    $category_shop_code = !empty($shop['use_categorys_from_shop_code']) ? $shop['use_categorys_from_shop_code'] : $shop_code;
                    $pdo = get_main_db_pdo_from_env_single_instance();
                    echo category_select($pdo,$company,$category_shop_code,$language_code,0,$translation->get('forward_shop_category'),'input_forward_shop_category',(int)$input_main_navigation["forward_shop_category"],false);
                    ?>
                    <? //input($translation->get("forward_shop_category"), "input_forward_shop_category", "text", $input_main_navigation["forward_shop_category"], 255) ?>
                </div>
            </td>
            <td>
                <? input($translation->get("active"), "input_active", "checkbox", $input_main_navigation["active"]) ?>
                <? input($translation->get("validity_from"), "input_validity_from", "date", datefromsql($input_main_navigation["validity_from"]), 10) ?>
                <? input($translation->get("validity_to"), "input_validity_to", "date", datefromsql($input_main_navigation["validity_to"]), 10) ?>
                <? input($translation->get("is_landing_page"), "input_is_landing_page", "checkbox", $input_main_navigation["is_landing_page"]) ?>
                <br />
            </td>
        </tr>
    </table>

</form>
<script>
    $('#input_forward_page_id').change();
</script>