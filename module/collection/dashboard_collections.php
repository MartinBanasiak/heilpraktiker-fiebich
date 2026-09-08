<?php
require_once(MODULE_PATH . "collection/collection_config.inc.php");

$siteparts     = \DynCom\dc\common\classes\Siteparts::get();
$pageIds       = array();
$sitepartCount = array();
$formname      = "form_edit_collection";
$translation   = \DynCom\dc\common\classes\Registry::get("translation");

$action = (isset($_GET['action']) ? $_GET['action'] : "");

if ($action != "" && $action != "admin_login") {
    require_once(MODULE_PATH . "collection/edit_collection.inc.php");
    exit();
}

$collection_setups    = array();
$collection_setup_ids = array();
$collection_anzahl    = array();

$query    = "SELECT * FROM main_collection_setup WHERE (main_language_id = " . (int)$GLOBALS["language"]['id'] . " OR all_languages = 1) ORDER BY id ASC";
$result   = @mysqli_query($GLOBALS['mysql_con'], $query);
$num_rows = @mysqli_num_rows($result);
if ($num_rows >= 1) {
    while ($row = @mysqli_fetch_array($result, 1)) {
        $collection_setup_ids[]        = $row['id'];
        $collection_setups[$row['id']] = $row;
    }
}

if (count($collection_setup_ids)) {
    $query  = "SELECT main_collection_setup_id, count(*) AS anzahl FROM main_collection WHERE main_collection_setup_id IN (" . join(",", $collection_setup_ids) . ") AND description != '' GROUP BY main_collection_setup_id";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    while ($row = @mysqli_fetch_array($result, 1)) {
        $collection_anzahl[$row['main_collection_setup_id']] = $row['anzahl'];
    }
}
?>

<div id="mainContent">
    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation('top_collections'); ?></h1>

    <div class="dashboard_part_left">
        <h2><?php echo get_translation('top_collections'); ?></h2>

        <?php if (count($collection_setups)): ?>

            <?php
            foreach ($collection_setups as $collectionSetupid => $collectionSetupRow) {
                ?>
                <a class="dashoard_box button_inhalt_collection"
                   href="<?php echo get_menu_link($_GET["level_1"] . "/" . $collectionSetupid, $site, $language); ?>">
                    <span
                        class="dashboard_box_number"><?php echo(isset($collection_anzahl[$collectionSetupid]) ? $collection_anzahl[$collectionSetupid] : 0); ?></span>
                    <span class="dashboard_box_description"><?php echo $collectionSetupRow['description']; ?></span>
                    <img
                        src="<?php echo PATH_SETUP_ICON_COLLECTION . ($collectionSetupRow['icon'] != "" ? $collectionSetupRow['icon'] : 'dokumente_2.png'); ?>" />
                </a>
            <?php
            }
            ?>

        <?php else: ?>

            <?php echo "<div class=\"infobox\">" . $translation->get("no_rows_found") . "</div>"; ?>

        <?php endif; ?>
    </div>

    <div class="dashboard_part_right">
        <h2><?php echo get_translation('last_changed_collections'); ?></h2>

        <form id="<?= $formname ?>" name="<?= $formname ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" class="selected_linklist_row" name="input_collection_id" value="" />

            <div class="requestLoader"></div>
            <div id="page_linklist">
                <?
                $query = "SELECT
			main_collection.id, 
			main_collection_setup.description AS '" . $translation->get("type") . "',
			main_collection.description AS '" . $translation->get("description") . "',
			from_unixtime(main_collection.modified_date, '%d.%m.%Y %H:%i:%s') AS '" . $translation->get("changed_on") . "', 
			main_admin_user.name AS '" . $translation->get("changed_by") . "' 
		from 
			main_collection left join main_admin_user
		ON 
			main_collection.modified_user = main_admin_user.id
			inner join main_collection_setup
		ON
		 	main_collection.main_collection_setup_id = main_collection_setup.id
		where 
			(main_collection_setup.main_language_id = " . (int)$GLOBALS["language"]['id'] . " OR main_collection_setup.all_languages = 1) ORDER BY main_collection.modified_date desc limit 20";


                $format = array('option', 'text', 'text', 'text', 'text');
                if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
                    linklist($result, $formname, $format, "input_collection_id", "edit_collection");
                }
                ?>
            </div>
        </form>
    </div>