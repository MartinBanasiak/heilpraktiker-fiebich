<?
$formname      = "form_collection_cardform_new";
$translation   = \DynCom\dc\common\classes\Registry::get("translation");
$inputname     = "input_id";
$input_page_id = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");

// erstelle kollektionen sammeln
$collection_setups = array();
$query             = "SELECT * FROM main_collection_setup WHERE (main_language_id = " . (int)$GLOBALS["language"]['id'] . " OR all_languages = 1)";
$result            = @mysqli_query($GLOBALS['mysql_con'], $query);
while ($row = @mysqli_fetch_array($result, 1)) {
    $collection_setups[$row['id']] = $row;
}

?>

<script type="text/javascript">

    function create_new_collection( _id ) {
        $('.main_collection_setup_id', $('#<?php echo $formname; ?>')).val(_id);
        loadCard('save_collection', true);
    }

</script>


<div id="overlaycrumb">
    <?php
    echo $translation->get("new_collection");
    ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<?php if (count($collection_setups) == 0) {

    // Wenn keine Kollektionseinstellungen angelegt wurden, dann nicht weiter machen

    echo "<div class=\"infobox\">" . $translation->get("no_rows_found") . "</div>";

    exit();
}
?>

<div class="clearfix"></div>


<form id="<?php echo $formname; ?>" name="<?php echo $formname; ?>" method="post">
    <input name="input_collection_id" type="hidden" value="">
    <input name="main_collection_setup_id" class="main_collection_setup_id" type="hidden" value="" />
    <input name="firstsave" value="1" type="hidden" />

    <div id="srollerInhalteHeadline"><h2><?php echo $translation->get("choose_collection_type"); ?></h2></div>
    <div id="pagecontentScroller" class="pagecontentScroller">
        <div class="scroller_inhalte">

            <ul class="inhalte_menu new_collection">
                <?php
                foreach ($collection_setups as $collectionId => $collectionData) {
                    button("inhalt_collection_setup", '<img src="' . $GLOBALS['projectRoot'] . PATH_SETUP_ICON_COLLECTION . $collectionData['icon'] . '" /><span>' . $collectionData['description'] . '</span>', 'form_collection_cardform_new', "create_new_collection({$collectionId})");
                }
                ?>


            </ul>

        </div>
    </div>
</form>