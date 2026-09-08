<?
$formname      = "form_collection_cardform";
$translation   = \DynCom\dc\common\classes\Registry::get("translation");
$inputname     = "input_id";
$input_page_id = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
global $collection_setup_images;

?>

    <script type="text/javascript">

        var PATH_SETUP_ICON_COLLECTION = '<?php echo PATH_SETUP_ICON_COLLECTION; ?>';

        function change_setup_icon( _image ) {
            $('.setup_icon', $('#<?php echo $formname; ?>')).val(_image);
            $('.setup_icon_container img').remove();
            $('.setup_icon_container').append($('<img>').attr("src", PATH_SETUP_ICON_COLLECTION + _image));
        }

    </script>

    <div id="overlaycrumb">
        <?php if ($input_collection["id"] == "") {
            echo $translation->get("new_collection_setup");
        } else {
            echo $translation->get("edit_collection_setup");
        }
        ?>
        <div id="closeoverlay" onclick="disableOverlay();"></div>
    </div>

    <ul class="toolbar_menu">
        <?php
        // Zurueck zur uebersicht
        if (is_page_edit()) {
            button("left", $translation->get("show_all"), $formname, "loadCard('list_collection_page', true)");
        }

        // Speichern button
        button("save", $translation->get("save"), $formname, "loadCard('save_collection_setup', true)");

        // Speichern und schliessen
        button("save", $translation->get("save_and_close"), $formname, "loadCard('save_collection_setup', true, '', true)");

        if ($input_collection["id"] != "") {
            button("copy", $translation->get("copy_collection_setup"), $formname, "loadCard('copy_collection_setup', true)");
        }

        ?>
        <li>
            <ul>
                <?php
                // Loeschen  button
                if ($input_collection["id"] != "" && is_page_edit() === FALSE) {
                    echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_collection_setup', true, '{$translation->get('delete_collection_setup_confirm')}', true)", "", FALSE);
                }

                // Zuruecksetzen
                button("reset", $translation->get("restore"), $formname, "?action=edit", "", FALSE);
                ?>
            </ul>
        </li>
    </ul>
    <div class="clearfix"></div>

<?php
if (count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
}
?>

    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
        <input name="input_id" type="hidden" value="<?= $input_collection["id"] ?>">
        <input type="hidden" name="get_real_page_link_id" value="1" />
        <input type="hidden" name="input_page_id" value="<?php echo $input_page_id; ?>" />
        <input name="data-sitepartid" type="hidden" value="8">
        <input class="setup_icon" name="setup_icon" id="setup_icon" type="hidden"
               value="<?php echo $input_collection['icon']; ?>" />

        <table class="cardform" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>

                    <? input($translation->get("textkey"), "input_code", "code", $input_collection["code"], 20) ?>
                    <? input($translation->get("description"), "input_description", "text", $input_collection["description"], 255) ?>
                    <? input($translation->get("collection_linked"), "input_linked", "checkbox", $input_collection["linked"]) ?>
                    <? input($translation->get("show_in_all_languages"), "input_all_languages", "checkbox", $input_collection["all_languages"]) ?>
                    <div class="clearfix"></div>
                    <div class="label"><?php echo $translation->get("current_icon"); ?></div>
                    <div class="setup_icon_container">
                        <?php
                        if ($input_collection['icon'] != "") {
                            ?>
                            <img src="<?php echo PATH_SETUP_ICON_COLLECTION . $input_collection['icon']; ?>" />
                        <?php
                        }
                        ?>
                    </div>
                </td>
                <td>
                    <?php
                    if ($input_collection["id"] != "") {
                        show_changed_on($input_collection["id"], "main_collection_setup");
                        show_changed_by($input_collection["id"], "main_collection_setup");
                    }
                    ?>
                </td>
            </tr>


        </table>
    </form>

    <div id="srollerInhalteHeadline"><h2><?php echo $translation->get("choose_collection_image"); ?></h2></div>
    <div id="pagecontentScroller" class="pagecontentScroller">
        <div class="scroller_inhalte">
            <ul class="inhalte_menu">
                <?php
                foreach ($collection_setup_images as $imageName) {
                    button("inhalt_collection_setup", '<img src="' . $GLOBALS['projectRoot'] . PATH_SETUP_ICON_COLLECTION . $imageName . '" />', 'form_collection_cardform_new', "change_setup_icon('{$imageName}')");
                }
                ?>
            </ul>
        </div>
    </div>

<?
if ($input_collection['id'] != "") {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_setup_line_listform.inc.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_setup_group_listform.inc.php';
}
?>