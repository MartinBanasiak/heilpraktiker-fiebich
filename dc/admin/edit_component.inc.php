<?php
/**
 * component edit:
 * Alle actions die mit der bearbeitung der Seite zu tun haben, müssen auf _component enden.
 * z.B. edit_component.
 * Alle anderen actions werden erstmal zum verknüpfen der Subparts genommen.
 * 
 * @package dc cms
 */

$action = (isset($_GET['action']) ? $_GET['action'] : "");

$input_component_id = $_REQUEST["input_component_id"];
$subaction = substr($action, strrpos($action, '_')+1);

update_visitor_data(array(
	'collection_options' => array(
		'collection_edit' => false
	)
));

if(isset($_POST['update_visitor_data']) && $_POST['update_visitor_data'] == 1) {
	$input_active = ($_POST["input_active"] == "on") ? 1 : 0;
	
	update_visitor_data(array(
		'component_options' => array(
			'component_option_layoutarea' => $_POST['input_layout_area_id'],
			'component_option_active' => $input_active
		)
	));
}

if($action == "" || $subaction == 'component' || $subaction == 'page') {
	// Wenn keine action ubergeben wurde oder es sich um eine _component action handelt
	switch($_GET["action"]) {
		case 'edit_component': case 'edit_content_page': edit_component(); break;
		case 'delete_component': delete_component(); break;
		case 'new_component': require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_component_cardform.inc.php'; break;
		case 'save_component': save_component(); break;
		case 'assoc_line_component': assoc_sitepart(); break;
		case 'delete_line_component' : delete_sitepart(); break;
		case 'edit_line_component': edit_line_component(); break;
		case 'edit_line_properties_component': edit_line_properties_component(); break;
		case 'save_line_properties_component' : save_line_properties_component(); break;
		case 'moveup_line_component': moveup_line_component(); edit_component(); break;
		case 'movedown_line_component': movedown_line_component(); edit_component(); break;
		
		// Seiten filtern
		case 'filter_component': filter_component(); break; 
		
		// zeige Liste der zuogeordneten Inhalte an
		case 'edit_content_component': require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_component_line_listform.inc.php'; break;
		
		
		// Hinzufuegen eines neuen Textcontents
		case 'list_textcontent_page': require_once(MODULE_PATH . "textcontent/component_textcontent_listform.inc.php"); break;
		
		// Hinzufuegen eines neuen Kontaktformulars
		case 'list_contactform_page': require_once(MODULE_PATH . "contactform/component_contactform_listform.inc.php"); break;
		
		// Hinzufuegen einer neuen slideshow
		case 'list_slideshow_page': require_once(MODULE_PATH . "slideshow/component_slideshow_listform.inc.php"); break;
		
		// Hinzufuegen einer neuen scrollbar
		case 'list_magicscroll_page': require_once(MODULE_PATH . "magicscroll/component_magicscroll_listform.inc.php"); break;
		
		// Hinzufuegen einer neuen Gallery
		case 'list_gallery_page': require_once(MODULE_PATH . "gallery/component_gallery_listform.inc.php"); break;
		
		// Hinzufuegen einer neuen dateigallery
		case 'list_filegallery_page': require_once(MODULE_PATH . "filegallery/component_filegallery_listform.inc.php"); break;
		
		// Hinzufuegen einer neuen google map karte
		case 'list_googlemaps_page': require_once(MODULE_PATH . "googlemaps/component_googlemaps_listform.inc.php"); break;
		
		// Hinzufuegen einer neuen facebook box
		case 'list_facebook_page': require_once(MODULE_PATH . "facebook/component_facebook_listform.inc.php"); break;
		
		// Hinzufuegen einer neuen youtube box
		case 'list_youtube_page': require_once(MODULE_PATH . "youtube/component_youtube_listform.inc.php"); break;
		
		// Hinzufuegen einer neuen iframe box
		case 'list_iframe_page': require_once(MODULE_PATH . "iframe/component_iframe_listform.inc.php"); break;
		
		// Hinzufuegen eines neuen slidecontents
		case 'list_slidecontent_page': require_once(MODULE_PATH . "slidecontent/component_slidecontent_listform.inc.php"); break;
		
		// Hinzufuegen einer neuen collection vorschau
		case 'list_collection_preview_page': $input_line = array(); require_once(MODULE_PATH . "collection/component_collection_preview_cardform.inc.php"); break;
		case 'edit_collection_preview_page': edit_collection_preview_page(); break;
		case 'save_collection_preview_page': save_collection_preview_page(); break;
		case 'load_collection_pages_page' : load_collection_pages(); break;
		case 'load_collections_page' : load_collections(); break;
		
		// shop siteparts
		case 'list_shop_login_page':
		case 'list_shop_main_page':
		case 'list_shop_item_preview_page':
			new_shop_sitepart();
			break;
		
		// wenn die karte direkt beim seitenaufruf geoeffnet werden soll
		case 'open_card_component': open_card_component();
		default: require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_component_listform.inc.php'; break;
	}
} else {
	// Ansonsten ist es eine subpart action
	$siteparts = \DynCom\dc\common\classes\Siteparts::get();
	$sitepart_id = null;
	if(isset($_POST['sitepart_id']) && !empty($_POST['sitepart_id'])) {
		$sitepart_id = (int) $_POST['sitepart_id'];
	} elseif(isset($_POST['data-sitepartid']) && !empty($_POST['data-sitepartid'])) {
		$sitepart_id = (int) $_POST['data-sitepartid'];
	}
	
	if($sitepart_id != null && $siteparts[$sitepart_id]['header_table'] == "") {
		// keine header_table, einfach direkt zuordnen
		set_component_link($sitepart_id, 0);
		edit_component();
	} else {
		
		// schneller hack um das sitepart anhand des codes herauszufinden
		//@TODO Sitepart ID sollte immer uebergeben werden
		$currentSitepart = null;
		foreach($siteparts as $sitepartData) {
			if($subaction == $sitepartData['code']) {
				$currentSitepart = $sitepartData;
				break;
			}
		}
		
		if($sitepart_id != null) {
			$currentSitepart = $siteparts[$sitepart_id];
		}
		
		$path = realpath(MODULE_PATH . $currentSitepart['folder']) . DIRECTORY_SEPARATOR . 'edit_' . $currentSitepart['code'] . ".inc.php";
		if(file_exists($path)) {
			require_once($path);
		}
	}	
}

function new_shop_sitepart() {
	$siteparts = \DynCom\dc\common\classes\Siteparts::get();
	$currentSitepart = $siteparts[$_POST['sitepart_id']];
	$path = realpath(MODULE_PATH . $currentSitepart['folder']) . DIRECTORY_SEPARATOR . 'edit_' . $currentSitepart['code'] . ".inc.php";
	if(file_exists($path)) {
		require_once($path);
	}
}

function save_collection_preview_page() {
	$translation = \DynCom\dc\common\classes\Registry::get("translation");
	$error = false;

	if($_POST['main_collection_setup_id'] == "") {
		$messages[] = "<div class=\"errorbox\">" . $translation->get("error_collection_choose_setup") . "</div>\n";
		$error = true;
	}
	
	if(!isset($_POST['main_collection_page_list_id']) || $_POST['main_collection_page_list_id'] == "") {
		$messages[] = "<div class=\"errorbox\">" . $translation->get("error_collection_choose_page_with_list") . "</div>\n";
		$error = true;
	}
	
	if($_POST['main_collection_view_type'] == 0 && (int)$_POST['main_collection_id'] <= 0) {
		$messages[] = "<div class=\"errorbox\">" . $translation->get("error_collection_choose_collection_for_preview") . "</div>\n";
		$error = true;
	}
	
	if($_POST['main_collection_view_type'] == 1 && (int)$_POST['main_collection_items'] <= 0) {
		$messages[] = "<div class=\"errorbox\">" . $translation->get("error_collection_choose_collection_items") . "</div>\n";
		$error = true;
	}
	
	if($error === false) {
		if($_REQUEST["input_id"] != "") {
			// update
			$query = "UPDATE main_component_link SET 
				main_collection_id = '" . (int)$_POST['main_collection_id'] . "',
				main_collection_setup_id = '" . (int)$_POST['main_collection_setup_id'] . "',
				main_collection_page_list_id = '" . (int)$_POST['main_collection_page_list_id'] . "',
				main_collection_items = '" . (int)$_POST['main_collection_items'] . "',
				main_collection_view_type = '" . (int)$_POST['main_collection_view_type'] . "',
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . ",
				modified_date = " . time() . "

				WHERE id = '" . $_REQUEST["input_id"] . "'
			";
			@mysqli_query($GLOBALS['mysql_con'],$query);
			
			$messages = array(
				'<div class="successbox">' . $translation->get("collection_preview_edit_success") . '</div>'
			);
			
		} else {
			set_component_link(0, 0, 2, (int)$_POST['main_collection_id'], (int)$_POST['main_collection_setup_id'], (int)$_POST['main_collection_view_type'], (int)$_POST['main_collection_items'], (int)$_POST['main_collection_page_list_id']);
			
			$messages = array(
				'<div class="successbox">' . $translation->get("page_line_assoc_success") . '</div>'
			);
		}
		
		// gruppen hinzufuegen
		mysqli_query($GLOBALS['mysql_con'],"DELETE FROM main_component_collection_group_link WHERE main_component_link_id = '" . (int)$_REQUEST["input_id"]."'");
		
		$query = "SELECT * FROM main_collection_setup_group WHERE main_collection_setup_id = '" . (int)$_POST['main_collection_setup_id']."'";
		$result = @mysqli_query($GLOBALS['mysql_con'],$query);
		while ($row = @mysqli_fetch_assoc($result)) {
			$arr_ind = "input_collection_group_" . $row["id"] . "_active";
			if ($_POST[$arr_ind] != "on") {
				continue;
			}
			
			$query_3 = "INSERT INTO main_component_collection_group_link SET
				main_component_link_id = '" . (int)$_REQUEST["input_id"] . "',
				main_collection_setup_group_id = " . $row["id"];
			@mysqli_query($GLOBALS['mysql_con'],$query_3);
			
		}
		
		if($_REQUEST['save_and_close'] == 1) {
			edit_component("", $messages);
		} else {
			edit_collection_preview_page("", $messages);
		}
		
	} else {
		$input_line = $_POST;
		require_once(MODULE_PATH . "collection/component_collection_preview_cardform.inc.php");
	}
}

function edit_collection_preview_page($_id = "", $messages = array()) {
	if((int)$_id > 0) {
		$input_id = $_id;
	} else {
		$input_id = $_REQUEST["input_id"];
	}
	
	if($input_id <> '') {
		$query="SELECT * FROM main_component_link WHERE id = '" . $input_id . "' LIMIT 1";
		$result = @mysqli_query($GLOBALS['mysql_con'],$query);
		if(@mysqli_num_rows($result) == 1) {
			$input_line = @mysqli_fetch_array($result);
			require_once(MODULE_PATH . "collection/component_collection_preview_cardform.inc.php");
		} 
	} else {
		$input_line = array();
		require_once(MODULE_PATH . "collection/component_collection_preview_cardform.inc.php");
	}
}

function load_collection_pages() {	
	$translation = \DynCom\dc\common\classes\Registry::get("translation");
	ob_start();
	create_page_with_collection_select($_POST['collection_setup_id'], $translation->get("page_with_collections"), "main_collection_page_list_id", array(), false);
	$output1 = ob_get_clean();
	
	$active_groups = array();
	$query = "SELECT main_collection_setup_group_id from main_component_collection_group_link where main_component_link_id = '" . $_POST['component_link_id']."'";
	$result = @mysqli_query($GLOBALS['mysql_con'],$query);
	while ($row = @mysqli_fetch_array($result)) {
		$active_groups[] = $row['main_collection_setup_group_id'];	
	}	
	ob_start();
	create_collection_preview_group_select($_POST['collection_setup_id'], $_POST['component_link_id'], $active_groups);
	$output2 = ob_get_clean();
	
	echo json_encode(array('page_with_collection_select' => $output1, 'collection_preview_group_select' => $output2));
}

function load_collections() {
	// ajax call
	$translation = \DynCom\dc\common\classes\Registry::get("translation");
	$setup_id = $_POST['main_collection_setup_id'];
	$collections = array();
	$collection_ids = array();
	if($setup_id != "") {
		$query = "SELECT * FROM main_collection WHERE main_collection_setup_id = '" . $setup_id . "' AND description != ''";
		$result = @mysqli_query($GLOBALS['mysql_con'],$query);
		while ($row = @mysqli_fetch_array($result,1)) {
			$collections[] = $row['description'];
			$collection_ids[] = $row['id'];
		}
	}
	
	input_select($translation->get("choose_collection"),"main_collection_id",$values = $collection_ids,$value_names = $collections,"", false, false);
}

function filter_component() {
	$translation = \DynCom\dc\common\classes\Registry::get("translation");
	
	$filter_id = $_REQUEST['filter_id'];
	$include_children = $_REQUEST['include_children'];
	
	if((int)$filter_id <= 0) {
		$query = "SELECT main_component.id, title AS '" . $translation->get("description") . "', from_unixtime(modified_date, '%d.%m.%Y %H:%i:%s') AS '" . $translation->get("changed_on") . "', main_admin_user.name AS '" . $translation->get("changed_by") . "' from main_component left join main_admin_user ON main_component.modified_user = main_admin_user.id where main_language_id = '" . (int)$GLOBALS["language"]['id']."'";
		$format = array('option', 'text','text','text');
		if($result = @mysqli_query($GLOBALS['mysql_con'],$query)) {
			linklist($result,"form_component_list",$format, "input_component_id", "edit_content_component");	
		}
		
		return;
	}
	
	$component_ids = array();
	// seiten IDs, die dem Navigationspunkt und dessen Kindern zugerodnet sind
	$query = "SELECT forward_component_id FROM main_navigation WHERE id = '" . (int)$filter_id . "' AND forward_component_id > 0";
	$result = @mysqli_query($GLOBALS['mysql_con'],$query);
	if(@mysqli_num_rows($result) > 0) {
		$row = @mysqli_fetch_array($result);
		if(!in_array($row['forward_component_id'], $component_ids)) {
			$component_ids[] = $row['forward_component_id'];
		}
	}
	
	if($include_children == 1) {
		filter_component_children($filter_id, $component_ids);
	}

	if(count($component_ids) > 0) {
		$query = "SELECT main_component.id, title AS '" . $translation->get("description") . "', from_unixtime(modified_date, '%d.%m.%Y %H:%i:%s') AS '" . $translation->get("changed_on") . "', main_admin_user.name AS '" . $translation->get("changed_by") . "' from main_component left join main_admin_user ON main_component.modified_user = main_admin_user.id where main_language_id = '" . (int)$GLOBALS["language"]['id'] . "' AND main_component.id IN (" . join(',', $component_ids) . ")";
		$format = array('option', 'text','text','text');
		if($result = @mysqli_query($GLOBALS['mysql_con'],$query)) {
			linklist($result,"form_component_list",$format, "input_component_id", "edit_content_component");	
		}
	} else {
		echo "<div class=\"infobox\">" . $translation->get("no_rows_found") . "</div>";
	}
}

function filter_component_children($_parentid, &$component_ids) {
	$query = "SELECT id, forward_component_id FROM main_navigation WHERE parent_id = '" . $_parentid."'";
	$result = @mysqli_query($GLOBALS['mysql_con'],$query);
	if(@mysqli_num_rows($result) > 0) {
		while ($row = @mysqli_fetch_array($result)) {
			if((int)$row['forward_component_id'] > 0 && !in_array($row['forward_component_id'], $component_ids)) {
				$component_ids[] = $row['forward_component_id'];
			}
			filter_component_children($row['id'], $component_ids);
		} 	
	}
}

function filter_component_children_rek() {
	
}

function open_card_component() {
	echo "<script type=\"text/javascript\">";
	
	echo "openCardOnStart = true; openCardAction='edit_component';";
	
	echo "</script>";
}

function assoc_sitepart($_sitepartId="", $_inputid = "", $_close = true) {
	$translation = \DynCom\dc\common\classes\Registry::get("translation");
	
	if($_sitepartId != "") {
		$sitepartId = $_sitepartId;
	} else {
		$sitepartId = $_REQUEST['data-sitepartid'];
	}
	
	if($_inputid != "") {
		$inputid = $_inputid;
	} else {
		$inputid = $_REQUEST['input_id'];
	}
	
	set_component_link($sitepartId, $inputid);
	
	$messages = array(
		'<div class="successbox">' . $translation->get("component_line_assoc_success") . '</div>'
	);
	
	if($_close === true) {
		edit_component("", $messages);
	}
}

function delete_sitepart() {
	$query = "DELETE FROM main_component_link WHERE id = '" . $_POST['input_id'] . "' LIMIT 1";
	@mysqli_query($GLOBALS['mysql_con'],$query);
	
	$query = "UPDATE main_component set modified_date = '" . date('Y-m-d H:i:s',time()) . "', modified_user = " . (int)$GLOBALS["admin_user"]['id'] . " where id = '" . (int)$_REQUEST["input_component_id"]."'";
	@mysqli_query($GLOBALS['mysql_con'],$query);
	
	$query = "DELETE FROM main_component_collection_group_link WHERE main_component_link_id = '" . $_POST['input_id']."'";
	@mysqli_query($GLOBALS['mysql_con'],$query);
	
	edit_component();
}

function edit_line_component() {
	// anhand der input_id sitepart art auslesen
	$sitepartId = $_REQUEST['data-sitepartid'];
	$query = "SELECT * FROM main_component_link WHERE id = '" . $_POST['input_id']."'";
	$result = @mysqli_query($GLOBALS['mysql_con'],$query);
	$row = @mysqli_fetch_array($result);
	
	if($row['main_collection_list'] == 2) {
		edit_collection_preview_page();
		return;
	}
	
	if((int)$row['main_sitepart_header_id'] <= 0) {
		// keine header_table, also nur Seiteneinstellungen aufrufen
		edit_component();
		return;
	}
	
	$siteparts = \DynCom\dc\common\classes\Siteparts::get();
	
	$sitepartcode = $siteparts[$row['main_sitepart_id']]['code'];
	$sitepartfolder = $siteparts[$row['main_sitepart_id']]['folder'];
	$_REQUEST['input_id'] = $row['main_sitepart_header_id'];
	
	$custom_action = 'edit_' . $sitepartcode;
	require_once(MODULE_PATH . $sitepartfolder . "/edit_" . $sitepartcode . ".inc.php");
}

function save_line_properties_component() {
	$messages = array();
	$translation = \DynCom\dc\common\classes\Registry::get("translation");
	
	$input_id = $_POST['input_id'];
	$input_active = ($_POST["input_active"] == "on") ? 1 : 0;
	$layout_area_id = $_POST['input_layout_area_id'];
	
	$query = "UPDATE main_component_link SET layout_area_id = '" . $layout_area_id . "' , active = " . $input_active . " WHERE id = '" . $input_id."'";
	@mysqli_query($GLOBALS['mysql_con'],$query);
	
	$messages[] = '<div class="successbox">' . $translation->get("component_properties_save_success") . '</div>';
	
	if($_REQUEST['save_and_close'] == 1) {
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_component_line_listform.inc.php';
	} else {
		edit_line_properties_component($input_id, $messages);
	}
}

function edit_line_properties_component($_id = "", $messages = array()) {
	if((int)$_id > 0) {
		$input_id = $_id;
	} else {
		$input_id = $_REQUEST["input_id"];
	}
	
	if($input_id <> '') {
		$query="SELECT * FROM main_component_link WHERE id = '" . $input_id . "' LIMIT 1";
		$result = @mysqli_query($GLOBALS['mysql_con'],$query);
		if(@mysqli_num_rows($result) == 1) {
			$input_line = @mysqli_fetch_array($result);
			require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_component_line_properties_cardform.inc.php';
		} 
	} else {
		$input_line = array();
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_component_line_properties_cardform.inc.php';
	}
}

function edit_component($_id = "", $messages = array()) {
	if((int)$_id > 0) {
		$input_component_id = $_id;
	} else {
		$input_component_id = $_REQUEST["input_component_id"];
	}
	
	if($input_component_id <> '') {
		$query="SELECT * FROM main_component WHERE id = '" . $input_component_id . "' LIMIT 1";
		$result = @mysqli_query($GLOBALS['mysql_con'],$query);
		if(@mysqli_num_rows($result) == 1) {
			$input_component = @mysqli_fetch_array($result);
			require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_component_cardform.inc.php';
		} 
	} else {
		$input_component = array();
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_component_cardform.inc.php';
	}
}

function delete_component() {
	if($_REQUEST["input_component_id"] <> '') {
		$query = "DELETE FROM main_component WHERE id = '" . $_REQUEST["input_component_id"] . "' LIMIT 1";
		@mysqli_query($GLOBALS['mysql_con'],$query);
		
		$query = "DELETE FROM main_component_link WHERE main_component_id = '" . $_REQUEST["input_component_id"]."'";
		@mysqli_query($GLOBALS['mysql_con'],$query);
	}
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_component_listform.inc.php';
}	

function save_component() {
	
	$translation = \DynCom\dc\common\classes\Registry::get("translation");
	$messages = array();
	
	$input_component_id = "";
	$input_active = ($_POST["input_active"] == "on") ? 1 : 0;
	$input_validity_from = datetosql("");
	$input_validity_to = datetosql("");
	$default_active = $_REQUEST["input_component_default_active"];
	$input_all_languages = ($_POST["input_all_languages"] == "on") ? 1 : 0;

	if($_REQUEST["input_component_id"] <> '') {
		$query = "UPDATE main_component SET
				title = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_description"]) . "',
				code = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_code"]) . "',
				layout_area_id = '" . $_REQUEST['input_layout_area_id'] . "',
				modified_date = '" . date('Y-m-d H:i:s',time()) . "',
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . ",
				active = " . $input_active . ",
				default_active = '" . $default_active . "',
				validity_from = " . $input_validity_from . ",
				validity_to = " . $input_validity_to . ",
				all_languages = " . $input_all_languages . "
				WHERE id = " . $_REQUEST["input_component_id"] . " 
			LIMIT 1";
		$inserted = false;
		$input_component_id = $_REQUEST["input_component_id"];
	} else {
		$query = "INSERT INTO main_component
			(title, code, main_language_id, layout_area_id, active, default_active, modified_date, modified_user, validity_from, validity_to, all_languages)
			VALUES (
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_description']) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_code']) . "',
				" . (int)$GLOBALS["language"]['id'] . ",
				'" . $_REQUEST['input_layout_area_id'] . "',
				" . $input_active . ",
				'" . $default_active . "',
				" . time() . ",
				" . (int)$GLOBALS["admin_user"]['id'] . ",
				" . $input_validity_from . ",
				" . $input_validity_to . ",
				" . $input_all_languages . "
			)";
		$inserted = true;
	}

	if(($_REQUEST["input_code"] == '')|($_REQUEST["input_description"] == '')) {
		$messages[] = "<div class=\"errorbox\">" . $translation->get("error_textkey_description") . "</div>\n";
		$error = true;
	}		
	if($_REQUEST["input_code"] <> urlencode($_REQUEST["input_code"])) {
		$messages[] = "<div class=\"errorbox\">" . $translation->get("error_textkey_encoding") . "</div>\n";
		$error = true;
	}		
	
	if(!$error) {
		@mysqli_query($GLOBALS['mysql_con'],$query);
		if($inserted == true) {
			$input_component_id = mysqli_insert_id($GLOBALS['mysql_con']);
			$messages[] = '<div class="successbox">' . $translation->get("component_msg_success1") . '</div>';
		} else {
			$messages[] = '<div class="successbox">' . $translation->get("component_msg_success2") . '</div>';
		}
		
		edit_component($input_component_id, $messages);
	} else {
        headerFunctionBridge('EDIT_ERROR: 1');
		$input_component = array();
		$input_component['id'] = $input_component_id;
		$input_component["title"] = $_REQUEST["input_description"];
		$input_component["code"] = $_REQUEST["input_code"];
		$input_component['layout_area_id'] = $_REQUEST['input_layout_area_id'];
		$input_component["active"] = $input_active;
		$input_component["all_languages"] = $input_all_languages;
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_component_cardform.inc.php';
	}
}

function moveup_line_component() {
	move_line_component("<","DESC",$_REQUEST["input_id"]);
}

function movedown_line_component() {
	move_line_component(">","ASC",$_REQUEST["input_id"]);
}

function move_line_component($way,$order,$field_id) {	
	$query = "SELECT * FROM main_component_link WHERE id = '" . $field_id . "' LIMIT 1";
	$result = @mysqli_query($GLOBALS['mysql_con'],$query);
	if(@mysqli_num_rows($result) == 1) {
		$curr_line = @mysqli_fetch_array($result);
	}
	$query = "SELECT * FROM main_component_link WHERE main_component_id = '" . $curr_line["main_component_id"] . "' AND sorting " . $way . " " . $curr_line["sorting"] . " ORDER BY sorting " . $order . " LIMIT 1";
	$result = @mysqli_query($GLOBALS['mysql_con'],$query);
	if(@mysqli_num_rows($result) == 1) {
		$change_line = @mysqli_fetch_array($result);
	}
	if(($curr_line["id"]<>'') && ($change_line["id"]<>'')) {
		$query = "UPDATE main_component_link SET sorting = " . $change_line["sorting"] . " WHERE id = " . $curr_line["id"];
		@mysqli_query($GLOBALS['mysql_con'],$query);
		$query = "UPDATE main_component_link SET sorting = " . $curr_line["sorting"] . " WHERE id = " . $change_line["id"];
		@mysqli_query($GLOBALS['mysql_con'],$query);
		$query = "UPDATE main_component set modified_date = '" . date('Y-m-d H:i:s',time()) . "', modified_user = " . (int)$GLOBALS["admin_user"]['id'] . " where id = " . $curr_line["main_component_id"];
		@mysqli_query($GLOBALS['mysql_con'],$query);
	}
}

function line_already_exists($_componentid, $_sitepartid, $sitepart_headerid) {
	$query = "SELECT id FROM main_component_link WHERE main_component_id = '" . $_componentid . "' AND main_sitepart_id = '" . $_sitepartid . "' AND main_sitepart_header_id = '" . $sitepart_headerid."'";
	$result = @mysqli_query($GLOBALS['mysql_con'],$query);
	if(@mysqli_num_rows($result) >= 1) {
		$row = @mysqli_fetch_array($result);
		return $row['id'];	
	} else {
		return false;
	}
}

function get_real_component_link_id() {
	if(isset($_REQUEST['get_real_component_link_id']) && $_REQUEST['get_real_component_link_id'] == 1) {
		$_REQUEST['input_id'] = line_already_exists($_REQUEST["input_component_id"], $_REQUEST['data-sitepartid'], $_REQUEST["input_id"]);
	}
}

function component_filter($site_id, $language_id, $from_level = 1, $to_level = 3) {
	echo "<div class=\"input\"><select onchange=\"filter_component();\" class=\"bigselect\" name=\"component_filter\" id=\"component_filter\">";
	echo "<option value=''>Alle anzeigen</option>";
	component_filter_rek($site_id,$language_id, 0, 1, "", $from_level, $to_level);
	echo "</select>";
	echo "<input onclick=\"filter_component();\" checked=\"checked\" type=\"checkbox\" name=\"include_children\" id=\"include_children\" />&nbsp;Inkl. Unterseiten";
	echo "</div>";
}

function component_filter_rek($site_id, $language_id, $parent_id, $level, $navcode, $from_level, $to_level) {
	$spaces = "";
	$count = 1;
	while($count < $level) {
    	$spaces .= "&nbsp;&nbsp;&nbsp;";
    	$count++;
    }
 	$parent_id_query = ($parent_id == 0) ? " AND parent_id IS NULL" : " AND parent_id = '" . $parent_id."' ";
	$result = @mysqli_query($GLOBALS['mysql_con'],"SELECT * FROM main_navigation WHERE main_site_id = '" . $site_id . "' AND main_language_id = '" . $language_id."'" . $parent_id_query . " ORDER BY sorting ASC");
	$show_level = (($level >= $from_level) && ($level <= $to_level)) ? true : false;
	if(@mysqli_num_rows($result) > 0) {
  		while ($nav = @mysqli_fetch_array($result)) {
			$newnavcode = $navcode . $nav["code"] . "/";
			$active = "";
			if ($show_level) {
				echo "\n<option " . $active . "value=\"" . $nav["id"] . "\">" .  $spaces . $nav["menu_name"] . "</option>";
				component_filter_rek($site_id, $language_id, $nav["id"], ($level+1),$newnavcode, $from_level, $to_level );
			}
		}
	} else {
		if ($level == 1) {
			echo "\n<option selected=\"selected\" value=\"0\"> - </option>";
		}
	}
	return true;
}