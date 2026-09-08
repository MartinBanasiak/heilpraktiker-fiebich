<?php
/**
 * Page edit:
 * Alle actions die mit der bearbeitung der Seite zu tun haben, müssen auf _page enden.
 * z.B. edit_page.
 * Alle anderen actions werden erstmal zum verknüpfen der Subparts genommen.
 * 
 * @package dc cms
 */
require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'shop_functions.inc.php';
$action = (isset($_GET['action']) ? $_GET['action'] : "");

$input_page_id = $_REQUEST["input_page_id"];
$subaction = substr($action, strrpos($action, '_')+1);

update_visitor_data(array(
	'collection_options' => array(
		'collection_edit' => false
	)
));

if(isset($_POST['update_visitor_data']) && $_POST['update_visitor_data'] == 1) {
	$input_active = ($_POST["input_active"] == "on") ? 1 : 0;
	
	update_visitor_data(array(
		'page_options' => array(
			'page_option_layoutarea' => $_POST['input_layout_area_id'],
			'page_option_active' => $input_active
		)
	));
}

if($action == "get_ckeditor_pages") {
	get_pages($GLOBALS["site"]['id'],$GLOBALS["language"]['id'], 0);
	exit();
}

if($action == "" || $subaction == 'page') {
	// Wenn keine action ubergeben wurde oder es sich um eine _page action handelt
	switch($_GET["action"]) {
		case 'edit_page': edit_page(); break;
		case 'delete_page': delete_page(); break;
		case 'new_page': require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_cardform.inc.php'; break;
		case 'save_page': save_page(); break;
		case 'assoc_line_page': assoc_sitepart(); break;
		case 'assoc_collection_list_page' : assoc_collection_list(); break;
		case 'delete_line_page' : delete_sitepart(); break;
		case 'edit_line_page': edit_line_page(); break;
		case 'edit_line_properties_page': edit_line_properties_page(); break;
		case 'save_line_properties_page' : save_line_properties_page(); break;
		case 'moveup_line_page': moveup_line_page(); require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_line_listform.inc.php'; break;
		case 'movedown_line_page': movedown_line_page(); require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_line_listform.inc.php'; break;

		// Layout klassen
		case 'new_page_link_layout_class_page': require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_line_layout_class_listform.inc.php'; break;
		case 'assoc_page_link_layout_class_page': assoc_page_link_layout_class_page(); break;
		case 'delete_page_link_layout_class_page': delete_page_link_layout_class_page(); break;
		case 'update_sortorder_page_link_layout_class_page': update_sortorder_page_link_layout_class_page(); break;
		case 'moveup_page_link_layout_class_page': moveup_page_link_layout_class_page(); break;
		case 'movedown_page_link_layout_class_page': movedown_page_link_layout_class_page(); break;
		
		// Seiten filtern
		case 'filter_page': filter_page(); break; 
		
		// zeige Liste der zuogeordneten Inhalte an
		case 'edit_content_page': edit_content_page(); break;
		
		// zeige Liste der zuogeordneten Inhalte an - wenn man die karte zum ersten mal aufmacht
		case 'edit_content_start_page': edit_content_page(); break;

		// live bearbeitung der seite
		case 'edit_live_content_page': edit_live_content_page(); break;
		
		// inline bearbeitung eines textcontents
		case 'edit_line_sitepart_update_page': edit_line_sitepart_update_page(); break;
		
		// Hinzufuegen eines neuen Textcontents
		case 'list_textcontent_page': require_once(MODULE_PATH . "textcontent/page_textcontent_listform.inc.php"); break;
		
		// Hinzufuegen eines neuen Kontaktformulars
		case 'list_contactform_page': require_once(MODULE_PATH . "contactform/page_contactform_listform.inc.php"); break;
		
		// Hinzufuegen einer neuen slideshow
		case 'list_slideshow_page': require_once(MODULE_PATH . "slideshow/page_slideshow_listform.inc.php"); break;
		
		// Hinzufuegen einer neuen scrollbar
		case 'list_magicscroll_page': require_once(MODULE_PATH . "magicscroll/page_magicscroll_listform.inc.php"); break;
		
		// Hinzufuegen einer neuen Gallery
		case 'list_gallery_page': require_once(MODULE_PATH . "gallery/page_gallery_listform.inc.php"); break;
		
		// Hinzufuegen einer neuen dateigallery
		case 'list_filegallery_page': require_once(MODULE_PATH . "filegallery/page_filegallery_listform.inc.php"); break;
		
		// Hinzufuegen einer neuen google map karte
		case 'list_googlemaps_page': require_once(MODULE_PATH . "googlemaps/page_googlemaps_listform.inc.php"); break;
		
		// Hinzufuegen einer neuen facebook box
		case 'list_facebook_page': require_once(MODULE_PATH . "facebook/page_facebook_listform.inc.php"); break;
		
		// Hinzufuegen einer neuen facebook box
		case 'list_youtube_page': require_once(MODULE_PATH . "youtube/page_youtube_listform.inc.php"); break;
		
		// Hinzufuegen einer neuen iframe box
		case 'list_iframe_page': require_once(MODULE_PATH . "iframe/page_iframe_listform.inc.php"); break;
		
		// Hinzufuegen eines slidecontents
		case 'list_slidecontent_page': require_once(MODULE_PATH . "slidecontent/page_slidecontent_listform.inc.php"); break;
		
		// Hinzufuegen einer neuen collections liste
		case 'list_collection_list_page': $input_line = array(); require_once(MODULE_PATH . "collection/page_collection_preview_cardform.inc.php"); break;
		case 'edit_collection_list_page': edit_collection_preview_page(); break;
		
		// Hinzufuegen einer neuen collection vorschau
		case 'list_collection_preview_page': $input_line = array(); $_REQUEST['is_collection_preview']  = 1; require_once(MODULE_PATH . "collection/page_collection_preview_cardform.inc.php"); break;
		case 'edit_collection_preview_page': $_REQUEST['is_collection_preview']  = 1; edit_collection_preview_page(); break;
		case 'save_collection_preview_page': save_collection_preview_page(); break;
		case 'load_collection_pages_page' : load_collection_pages(); break;
		case 'load_collections_page' :  load_collections(); break;
		
		// Hinzufuegen einer neuen gruppe
		case 'list_group_page': require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_line_group_cardform.inc.php'; break;
		case 'edit_group_page': edit_group_page(); break;
		case 'save_group_page': save_group_page(); break;

		// reihenfolge aendern
		case 'reorder_content_page': reorder_content_page(); break;
		
		// shop siteparts
		case 'list_shop_login_page':
		case 'list_shop_main_page':
		case 'list_shop_item_preview_page':
			new_shop_sitepart();
			break;

		// Kopieren einer Seite
		case 'copy_page': copy_page(); break;
		case 'copy_page_load_languages_page': load_site_languages(); break;
		case 'copy_and_save_page': copy_and_save_page(); break;

		// seite aus vorlage erstellen
		case 'select_template_page': select_template_page(); break;
		
		// wenn die karte direkt beim seitenaufruf geoeffnet werden soll
		case 'open_card_page': open_card_page();
		default: default_function(); break;
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
		set_page_link($sitepart_id, 0);
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_line_listform.inc.php';
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

function select_template_page($messages = array()) {
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_select_template.inc.php';
}

function assoc_page_link_layout_class_page() {
	$translation = \DynCom\dc\common\classes\Registry::get("translation");
	$messages = array();

	$newsort      = mysqli_result(mysqli_query($GLOBALS['mysql_con'], "SELECT MAX(sorting) FROM main_page_link_layout_class_link WHERE main_page_link_id = " . $_REQUEST["input_id"]), 0) + 1;

	$query = "INSERT INTO main_page_link_layout_class_link
 			  (main_page_link_id, main_layout_class_id, modified_user, modified_date, sorting)
 		  	  VALUES (
 		  	  	" . $_REQUEST["input_id"] . ",
 		  	  	" . $_REQUEST["input_layout_class_id"] . ",
 		  	  	" . (int)$GLOBALS["admin_user"]['id'] . ",
 		  	  	" . time() . ",
 		  	  	" . $newsort . "
 		  	  )
 	";

	$messages[] = '<div class="successbox">' . $translation->get("page_link_layout_class_assoc_success") . '</div>';

	@mysqli_query($GLOBALS['mysql_con'], $query);

	edit_line_properties_page("", $messages);
}

function delete_page_link_layout_class_page() {
	$query = "DELETE FROM main_page_link_layout_class_link WHERE id = " . $_POST['input_page_link_layout_class_id'] . " LIMIT 1";
	@mysqli_query($GLOBALS['mysql_con'],$query);

	edit_line_properties_page();
}

function update_sortorder_page_link_layout_class_page() {
	$sorting = 1;
	foreach ($_POST['linklistid'] as $itemId) {
		$query = "UPDATE main_page_link_layout_class_link SET sorting = " . $sorting . " WHERE id = " . $itemId;
		@mysqli_query($GLOBALS['mysql_con'], $query);
		$sorting++;
	}
}

function move_page_link_layout_class( $way, $order, $layout_class_id ) {
	$query  = "SELECT * FROM main_page_link_layout_class_link WHERE id = " . $layout_class_id . " LIMIT 1";
	$result = @mysqli_query($GLOBALS['mysql_con'], $query);
	if (@mysqli_num_rows($result) == 1) {
		$curr_class = @mysqli_fetch_array($result);
	}
	$query  = "SELECT * FROM main_page_link_layout_class_link WHERE main_page_link_id = '" . $curr_class["main_page_link_id"] . "' AND sorting " . $way . " " . $curr_class["sorting"] . " ORDER BY sorting " . $order . " LIMIT 1";
	$result = @mysqli_query($GLOBALS['mysql_con'], $query);
	if (@mysqli_num_rows($result) == 1) {
		$change_class = @mysqli_fetch_array($result);
	}
	if (($curr_class["id"] <> '') && ($change_class["id"] <> '')) {
		$query = "UPDATE main_page_link_layout_class_link SET sorting = " . $change_class["sorting"] . " WHERE id = " . $curr_class["id"];
		@mysqli_query($GLOBALS['mysql_con'], $query);
		$query = "UPDATE main_page_link_layout_class_link SET sorting = " . $curr_class["sorting"] . " WHERE id = " . $change_class["id"];
		@mysqli_query($GLOBALS['mysql_con'], $query);
	}

	edit_line_properties_page();
}

function moveup_page_link_layout_class_page() {
	move_page_link_layout_class("<", "DESC", $_REQUEST["input_page_link_layout_class_id"]);
}

function movedown_page_link_layout_class_page() {
	move_page_link_layout_class(">", "ASC", $_REQUEST["input_page_link_layout_class_id"]);
}

function copy_page($_id = "", $messages = array()) {
	if((int)$_id > 0) {
		$input_page_id = $_id;
	} else {
		$input_page_id = $_REQUEST["input_page_id"];
	}

	if($input_page_id <> '') {

        $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
        $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
        $pdoUser = getenv('MAIN_MYSQL_DB_USER');
        $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
        $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

        $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

        $prepStatement = " SELECT * FROM main_page WHERE id = :id LIMIT 1      ";
        $params = [
            [':id', $input_page_id, PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $result = $pdo->getResultArray();

		if(count($result) == 1) {
			$input_page = $result[0];
			require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_copy.inc.php';
		}
	} else {
		$input_page = array();
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_cardform.inc.php';
	}
}

function load_site_languages() {
	$translation = \DynCom\dc\common\classes\Registry::get("translation");
	ob_start();
	language_select($translation->get("language"), "main_language_id", null, FALSE, $_POST['site_id'], true, "", true);
	$output1 = ob_get_clean();

	echo json_encode(array('site_languages' => $output1));
}

function copy_and_save_page() {
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_language_functions.php';
	$translation = \DynCom\dc\common\classes\Registry::get("translation");
	$messages = array();
	$error = false;

	$input_page_id = $_REQUEST["input_page_id"];
	$input_site_id = $_REQUEST["site_id"];
	$input_language_id = (isset($_REQUEST["main_language_id"]) ? $_REQUEST["main_language_id"] : "");
	$input_copy_from_template = (isset($_REQUEST["input_copy_from_template"]) ? $_REQUEST["input_copy_from_template"] : "0");

	if(($input_site_id == '')) {
		$messages[] = "<div class=\"errorbox\">" . $translation->get("copy_choose_site_error") . "</div>\n";
		$error = true;
	}

	if(($input_language_id == '')) {
		$messages[] = "<div class=\"errorbox\">" . $translation->get("copy_choose_language_error") . "</div>\n";
		$error = true;
	}

	if(!$error) {

		$new_page_id = copy_main_page($input_page_id, $input_language_id, $input_site_id);

		$messages[] = '<div class="successbox">' . $translation->get("copy_site_success") . '</div>';
		if($input_copy_from_template == 1) {
			$messages = array('<div class="successbox">' . $translation->get("copy_from_template_success") . '</div>');
			$query = "UPDATE main_page SET modified_date = " . time() . "is_template = 0, is_shopping_world = " . (int)on_shopping_world() . " WHERE id = " . $new_page_id;
			@mysqli_query($GLOBALS['mysql_con'], $query);
			edit_page($new_page_id, $messages);
		} else {
			copy_page($input_page_id, $messages);
		}


	} else {
	 headerFunctionBridge('EDIT_ERROR: 1');
		if($input_copy_from_template == 1) {
			select_template_page($messages);
		} else {
			copy_page($input_page_id, $messages);
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

function default_function() {
	update_visitor_data(array(
		'live_edit' => false
	));
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_listform.inc.php';
}

function edit_content_page() {
	$sessiondata = get_visitor_data();
	if($sessiondata['live_edit'] === true) {
		edit_live_content_page();
	} else {
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_line_listform.inc.php';
	}
}

function reorder_content_page() {
	global $parentSort;
	$parentSort = array();

	$list = $_POST['list'];
	if(!is_array($list) || count($list) == 0) { return; }

	foreach($list as $site) {
		if (!array_key_exists(0, $parentSort)) {
			$parentSort[0] = 1;
		}

		$query = "UPDATE main_page_link SET sorting = " . $parentSort[0] . ", main_page_link_parent_id = 0 WHERE id = " . $site['id'];
		mysqli_query($GLOBALS['mysql_con'],$query);
		$parentSort[0]++;

		if(!isset($site['children']) || count($site['children']) == 0) continue; // keine children enthalten, weiter

		update_children_page($site['id'], $site['children']);
	}
}

function update_children_page( $_parentId, $_list ) {
	global $parentSort;

	foreach ($_list as $site) {
		if (!array_key_exists($_parentId, $parentSort)) {
			$parentSort[$_parentId] = 1;
		}
		$query = "UPDATE main_page_link SET sorting = " . $parentSort[$_parentId] . ", main_page_link_parent_id = " . $_parentId . " WHERE id = " . (int)$site['id'];
		mysqli_query($GLOBALS['mysql_con'], $query);
		$parentSort[$_parentId]++;
		if (isset($site['children']) && count($site['children'])) {
			update_children_page($site['id'], $site['children']);
		}
	}
}

function edit_live_content_page() {
	$page_id = $_REQUEST["input_page_id"];
	
	// Pruefen ob schon Inhalte zugeordnet wurden
	$query = "SELECT id FROM main_page_link WHERE main_page_id = '" . (int)$page_id."'";
	$result = @mysqli_query($GLOBALS['mysql_con'],$query);
	if(@mysqli_num_rows($result) > 0) {
		update_visitor_data(array(
			'live_edit' => true
		));
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_live_cardform.inc.php';
	} else {
		update_visitor_data(array(
			'live_edit' => false
		));
		// keine Inhalte zugeordnet, also kein live edit moeglich.
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_line_listform.inc.php';
	}
}

function edit_line_sitepart_update_page() {
	$content = $_REQUEST['content'];
	$sitepart_header_id = $_POST['sitepaer_header_id'];
	
	$query = "
		UPDATE textcontent_header SET 
			content = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $content) . "',
			modified_date = '" . date('Y-m-d H:i:s',time()) . "',
			modified_user = " . (int)$GLOBALS["admin_user"]['id'] . "
 		WHERE id = '" . $sitepart_header_id."'";
	@mysqli_query($GLOBALS['mysql_con'],$query);
}

function save_collection_preview_page() {
	$translation = \DynCom\dc\common\classes\Registry::get("translation");
	$error = false;

	if($_POST['main_collection_setup_id'] == "") {
		$messages[] = "<div class=\"errorbox\">" . $translation->get("error_collection_choose_setup") . "</div>\n";
		$error = true;
	}
	
	if($_REQUEST['is_collection_preview'] == 1) {
	
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
		
	}
	
	if($error === false) {
		if($_REQUEST["input_id"] != "") {
			// update
			$query = "UPDATE main_page_link SET 
				main_collection_id = '" . (int)$_POST['main_collection_id'] . "',
				main_collection_setup_id = '" . (int)$_POST['main_collection_setup_id'] . "',
				main_collection_page_list_id = '" . (int)$_POST['main_collection_page_list_id'] . "',
				main_collection_items = '" . (int)$_POST['main_collection_items'] . "',
				main_collection_view_type = '" . (int)$_POST['main_collection_view_type'] . "',
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . ",
				modified_date = " . time() . "

				WHERE id = " . $_REQUEST["input_id"] . "
			";
			@mysqli_query($GLOBALS['mysql_con'],$query);
			
			$messages = array(
				'<div class="successbox">' . $translation->get("collection_preview_edit_success") . '</div>'
			);
			
		} else {
			assoc_collection_list();
			
			$messages = array(
				'<div class="successbox">' . $translation->get("page_line_assoc_success") . '</div>'
			);
		}
		
		// gruppen hinzufuegen
		mysqli_query($GLOBALS['mysql_con'],"DELETE FROM main_page_collection_group_link WHERE main_page_link_id = '" . (int)$_REQUEST["input_id"]."'");
		
		$query = "SELECT * FROM main_collection_setup_group WHERE main_collection_setup_id = '" . (int)$_POST['main_collection_setup_id']."'";
		$result = @mysqli_query($GLOBALS['mysql_con'],$query);
		while ($row = @mysqli_fetch_assoc($result)) {
			$arr_ind = "input_collection_group_" . $row["id"] . "_active";
			if ($_POST[$arr_ind] != "on") {
				continue;
			}
			
			$query_3 = "INSERT INTO main_page_collection_group_link SET
				main_page_link_id = '" . (int)$_REQUEST["input_id"] . "',
				main_collection_setup_group_id = " . $row["id"];
			@mysqli_query($GLOBALS['mysql_con'],$query_3);
			
		}
		
		if($_REQUEST['save_and_close'] == 1) {
			require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_line_listform.inc.php';
		} else {
			edit_collection_preview_page("", $messages);
		}
		
	} else {
		$input_line = $_POST;
		require_once(MODULE_PATH . "collection/page_collection_preview_cardform.inc.php");
	}
}

function edit_collection_preview_page($_id = "", $messages = array()) {
	if((int)$_id > 0) {
		$input_id = $_id;
	} else {
		$input_id = $_REQUEST["input_id"];
	}
	
	if($input_id <> '') {
		$query="SELECT * FROM main_page_link WHERE id = '" . $input_id . "' LIMIT 1";
		$result = @mysqli_query($GLOBALS['mysql_con'],$query);
		if(@mysqli_num_rows($result) == 1) {
			$input_line = @mysqli_fetch_array($result);
			require_once(MODULE_PATH . "collection/page_collection_preview_cardform.inc.php");
		} 
	} else {
		$input_line = array();
		require_once(MODULE_PATH . "collection/page_collection_preview_cardform.inc.php");
	}
}

function load_collection_pages() {
	$translation = \DynCom\dc\common\classes\Registry::get("translation");
	ob_start();
	create_page_with_collection_select($_POST['collection_setup_id'], $translation->get("page_with_collections"), "main_collection_page_list_id", array(), false);
	$output1 = ob_get_clean();
	
	$active_groups = array();
	$query = "SELECT main_collection_setup_group_id from main_page_collection_group_link where main_page_link_id = '" . $_POST['page_link_id']."'";
	$result = @mysqli_query($GLOBALS['mysql_con'],$query);
	while ($row = @mysqli_fetch_array($result)) {
		$active_groups[] = $row['main_collection_setup_group_id'];	
	}	
	ob_start();
	create_collection_preview_group_select($_POST['collection_setup_id'], $_POST['page_link_id'], $active_groups);
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

function filter_page() {
	$translation = \DynCom\dc\common\classes\Registry::get("translation");
	
	$filter_id = $_REQUEST['filter_id'];
	$include_children = $_REQUEST['include_sub'];
	
	if((int)$filter_id <= 0) {
		$query = "SELECT main_page.id, title AS '" . $translation->get("description") . "', main_page.active AS '" . $translation->get("active") . "', from_unixtime(modified_date, '%d.%m.%Y %H:%i:%s') AS '" . $translation->get("changed_on") . "', main_admin_user.name AS '" . $translation->get("changed_by") . "' from main_page left join main_admin_user ON main_page.modified_user = main_admin_user.id where main_page.is_shopping_world = " . (int)on_shopping_world() . " AND main_page.is_template = " . (int)on_templates() . " AND main_language_id = '" . (int)$GLOBALS["language"]['id']."'";
		$format = array('option', 'text','boolean_active','text','text');
		if($result = @mysqli_query($GLOBALS['mysql_con'],$query)) {
			linklist($result,"form_page_list",$format, "input_page_id", "edit_content_page");	
		}
		
		return;
	}
	
	$page_ids = array();
	// seiten IDs, die dem Navigationspunkt und dessen Kindern zugerodnet sind
	$query = "SELECT forward_page_id FROM main_navigation WHERE id = '" . (int)$filter_id . "' AND forward_page_id > 0";
	$result = @mysqli_query($GLOBALS['mysql_con'],$query);
	if(@mysqli_num_rows($result) > 0) {
		$row = @mysqli_fetch_array($result);
		if(!in_array($row['forward_page_id'], $page_ids)) {
			$page_ids[] = $row['forward_page_id'];
		}
	}
	
	if($include_children == 1) {
		filter_page_children($filter_id, $page_ids);
	}

	if(count($page_ids) > 0) {
		$query = "SELECT main_page.id, title AS '" . $translation->get("description") . "', main_page.active AS '" . $translation->get("active") . "', from_unixtime(modified_date, '%d.%m.%Y %H:%i:%s') AS '" . $translation->get("changed_on") . "', main_admin_user.name AS '" . $translation->get("changed_by") . "' from main_page left join main_admin_user ON main_page.modified_user = main_admin_user.id where main_page.is_shopping_world = 0 AND main_page.is_template = 0 AND main_language_id = '" . (int)$GLOBALS["language"]['id'] . "' AND main_page.id IN (" . join(',', $page_ids) . ")";
		$format = array('option', 'text','boolean_active','text','text');
		if($result = @mysqli_query($GLOBALS['mysql_con'],$query)) {
			linklist($result,"form_page_list",$format, "input_page_id", "edit_live_content_page");	
		}
	} else {
		echo "<div class=\"infobox\">" . $translation->get("no_rows_found") . "</div>";
	}
}

function filter_page_children($_parentid, &$page_ids) {
	$query = "SELECT id, forward_page_id FROM main_navigation WHERE parent_id = '" . $_parentid."'";
	$result = @mysqli_query($GLOBALS['mysql_con'],$query);
	if(@mysqli_num_rows($result) > 0) {
		while ($row = @mysqli_fetch_array($result)) {
			if((int)$row['forward_page_id'] > 0 && !in_array($row['forward_page_id'], $page_ids)) {
				$page_ids[] = $row['forward_page_id'];
			}
			filter_page_children($row['id'], $page_ids);
		} 	
	}
}

function filter_page_children_rek() {
	
}

function open_card_page() {
	echo "<script type=\"text/javascript\">";
	
	echo "openCardOnStart = true; openCardAction='edit_content_page';";
	
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
	
	set_page_link($sitepartId, $inputid);
	
	$messages = array(
		'<div class="successbox">' . $translation->get("page_line_assoc_success") . '</div>'
	);
	
	if($_close === true) {
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_line_listform.inc.php';
	}
}

function assoc_collection_list() {
	$translation = \DynCom\dc\common\classes\Registry::get("translation");
	
	if($_REQUEST['is_collection_preview'] == 1) {
		set_page_link(0, 0, 2, (int)$_POST['main_collection_id'], (int)$_POST['main_collection_setup_id'], (int)$_POST['main_collection_view_type'], (int)$_POST['main_collection_items'], (int)$_POST['main_collection_page_list_id']);
	} else {
		set_page_link(0, 0, 1, 0, $_POST['main_collection_setup_id']);
	}
	
	
//	$messages = array(
//		'<div class="successbox">' . $translation->get("page_line_assoc_success") . '</div>'
//	);
	
	//require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_line_listform.inc.php';
}

function delete_sitepart() {
	$query = "DELETE FROM main_page_link WHERE id = '" . $_POST['input_id'] . "' LIMIT 1";
	@mysqli_query($GLOBALS['mysql_con'],$query);
	
	$query = "UPDATE main_page_link SET main_page_link_parent_id = 0 WHERE main_page_link_parent_id = '" . $_POST['input_id']."'";
	@mysqli_query($GLOBALS['mysql_con'],$query);
	
	$query = "UPDATE main_page set modified_date = '" . date('Y-m-d H:i:s',time()) . "', modified_user = " . (int)$GLOBALS["admin_user"]['id'] . " where id = '" . (int)$_REQUEST["input_page_id"]."'";
	@mysqli_query($GLOBALS['mysql_con'],$query);
	
	$query = "DELETE FROM main_page_collection_group_link WHERE main_page_link_id = '" . $_POST['input_id']."'";
	@mysqli_query($GLOBALS['mysql_con'],$query);
	
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_line_listform.inc.php';
}

function edit_line_page() {
	// anhand der input_id sitepart art auslesen
	$sitepartId = $_REQUEST['data-sitepartid'];
	$query = "SELECT * FROM main_page_link WHERE id = '" . $_POST['input_id']."'";
	$result = @mysqli_query($GLOBALS['mysql_con'],$query);
	$row = @mysqli_fetch_array($result);
	
	if($row['main_page_group'] == 1) {
		edit_group_page();
		return;
	}
	
	if($row['main_collection_list'] == 1) {
		edit_collection_preview_page();
		return;
	}
	
	if($row['main_collection_list'] == 2) {
		$_REQUEST['is_collection_preview']  = 1;
		edit_collection_preview_page();
		return;
	}
	
	if((int)$row['main_sitepart_header_id'] <= 0) {
		// keine header_table, also nur Seiteneinstellungen aufrufen
		edit_line_properties_page();
		return;
	}
	
	$siteparts = \DynCom\dc\common\classes\Siteparts::get();
	
	$sitepartcode = $siteparts[$row['main_sitepart_id']]['code'];
	$sitepartfolder = $siteparts[$row['main_sitepart_id']]['folder'];
	$_REQUEST['input_id'] = $row['main_sitepart_header_id'];
	
	$custom_action = 'edit_' . $sitepartcode;
	require_once(MODULE_PATH . $sitepartfolder . "/edit_" . $sitepartcode . ".inc.php");
}

function save_line_properties_page() {
	$messages = array();
	$translation = \DynCom\dc\common\classes\Registry::get("translation");
	
	$input_id = $_POST['input_id'];
	$input_active = ($_POST["input_active"] == "on") ? 1 : 0;
	$layout_area_id = (int)$_POST['input_layout_area_id'];
	$layout_class_id = (int)$_POST['input_layout_class_id'];
	$backGroundImage = $_POST['input_background_image'];
	
	$query = "UPDATE main_page_link SET 
				layout_area_id = '" . $layout_area_id . "', 
				layout_class_id = '" . $layout_class_id . "',
				background_image_path = '" . $backGroundImage . "',
				active = " . $input_active . "
			WHERE id = '" . $input_id."'";
	@mysqli_query($GLOBALS['mysql_con'],$query);

	$messages[] = '<div class="successbox">' . $translation->get("page_properties_save_success") . '</div>';
	
	if($_REQUEST['save_and_close'] == 1) {
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_line_listform.inc.php';
	} else {
		edit_line_properties_page($input_id, $messages);
	}
}

function edit_line_properties_page($_id = "", $messages = array()) {
	if((int)$_id > 0) {
		$input_id = $_id;
	} else {
		$input_id = $_REQUEST["input_id"];
	}
	
	if($input_id <> '') {
		$query="SELECT * FROM main_page_link WHERE id = '" . $input_id . "' LIMIT 1";
		$result = @mysqli_query($GLOBALS['mysql_con'],$query);
		if(@mysqli_num_rows($result) == 1) {
			$input_line = @mysqli_fetch_array($result);
			require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_line_properties_cardform.inc.php';
		} 
	} else {
		$input_line = array();
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_line_properties_cardform.inc.php';
	}
}

function edit_page($_id = "", $messages = array()) {
	if((int)$_id > 0) {
		$input_page_id = $_id;
	} else {
		$input_page_id = $_REQUEST["input_page_id"];
	}
	
	if($input_page_id <> '') {
		$query="SELECT * FROM main_page WHERE id = '" . $input_page_id . "' LIMIT 1";
		$result = @mysqli_query($GLOBALS['mysql_con'],$query);
		if(@mysqli_num_rows($result) == 1) {
			$input_page = @mysqli_fetch_array($result);
			require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_cardform.inc.php';
		} 
	} else {
		$input_page = array();
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_cardform.inc.php';
	}
}

function delete_page() {
	if($_REQUEST["input_page_id"] <> '') {
		$query = "DELETE FROM main_page WHERE id = '" . $_REQUEST["input_page_id"] . "' LIMIT 1";
		@mysqli_query($GLOBALS['mysql_con'],$query);
		
		$query = "DELETE FROM main_page_link WHERE main_page_id = '" . (int)$_REQUEST["input_page_id"]."'";
		@mysqli_query($GLOBALS['mysql_con'],$query);

		$query = "UPDATE shop_category SET in_use_with_page_id = 0, hide_category_sub_navigation = 0 WHERE in_use_with_page_id = '".$_REQUEST["input_page_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'],$query);

	}
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_listform.inc.php';
}	

function save_page() {
	
	$translation = \DynCom\dc\common\classes\Registry::get("translation");
	$messages = array();
	
	$input_page_id = "";
	$input_active = ($_POST["input_active"] == "on") ? 1 : 0;
    $input_shopping_world = (on_shopping_world()) ? 1 : 0;
	$input_template = (on_templates()) ? 1 : 0;
    $input_hide_category_sub_navigation = ($_POST["input_hide_category_sub_navigation"] == "on") ? 1 : 0;
    $input_noindex = ($_POST["input_noindex"] == "on") ? 1 : 0;
    $input_nofollow = ($_POST["input_nofollow"] == "on") ? 1 : 0;
	$input_validity_from = datetosql($_POST["input_validity_from"]);
	$input_validity_to = datetosql($_POST["input_validity_to"]);
    $input_in_use_with_category = $_POST["input_category_line_no"];
	$language_part = 'page';
	if($_GET['level_2'] == 'templates') {
		$language_part = 'template';
	}

	if($_REQUEST["input_page_id"] <> '') {
		$query = "UPDATE main_page SET
				title = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_page_name"]) . "',
				subtitle = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_browser_title"]) . "',
				meta_keywords = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_meta_keywords"]) . "',
				meta_description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_meta_description"]) . "',
				modified_date = '" . time() . "',
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . ",
				active = " . $input_active . ",
				validity_from = " . $input_validity_from . ",
				validity_to = " . $input_validity_to . ",
				is_shopping_world = ".$input_shopping_world.",
				is_template = ".$input_template.",
				noindex = " . $input_noindex . ",
				nofollow = " . $input_nofollow . "
				WHERE id = '" . $_REQUEST["input_page_id"] . "' 
			LIMIT 1";
		$inserted = false;
		$input_page_id = $_REQUEST["input_page_id"];
	} else {
		$query = "INSERT INTO main_page
			(title, subtitle, meta_keywords, meta_description, main_language_id, active, modified_date, modified_user, validity_from, validity_to, noindex, nofollow, is_shopping_world, is_template)
			VALUES (
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_page_name']) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_browser_title']) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_meta_keywords']) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_meta_description']) . "',
				'" . $GLOBALS["language"]['id'] . "',
				" . $input_active . ",
				" . time() . ",
				" . (int)$GLOBALS["admin_user"]['id'] . ",
				" . $input_validity_from . ",
				" . $input_validity_to . ",
				" . $input_noindex . ",
				" . $input_nofollow . ",
				" . $input_shopping_world . ",
				" . $input_template . "
			)";
		$inserted = true;
	}

    $company = $GLOBALS['language']['company'];
    $shop_code = $GLOBALS['language']['shop_code'];
    $language_code = $GLOBALS['language']['shop_language_code'];
    $shop = get_shop($company,$shop_code);
    $category_shop_code = !empty($shop['use_categorys_from_shop_code']) ? $shop['use_categorys_from_shop_code'] : $shop_code;

    if($input_in_use_with_category > 0){
        $query2 = "UPDATE shop_category SET
                in_use_with_page_id = 0,
                hide_category_sub_navigation = 0 
				WHERE in_use_with_page_id = '" . $_REQUEST["input_page_id"] . "'";
        $query3 = "UPDATE shop_category SET
				in_use_with_page_id = '" . $_REQUEST["input_page_id"] . "',
				hide_category_sub_navigation = '" . $input_hide_category_sub_navigation . "'
				WHERE line_no = '" . $input_in_use_with_category . "'
				AND company = '".$company."'
				AND shop_code = '".$category_shop_code."'
				AND language_code = '".$language_code."'
				";
    }
    if($input_shopping_world != 1){
        $query4 = "UPDATE shop_category SET
                in_use_with_page_id = 0,
                hide_category_sub_navigation = 0  
				WHERE in_use_with_page_id = '" . $_REQUEST["input_page_id"] . "'";
    }

	if(($_REQUEST["input_page_name"] == '')) {
		$messages[] = "<div class=\"errorbox\">" . $translation->get("error_description") . "</div>\n";
		$error = true;
	}		
	
	if(!$error) {
		@mysqli_query($GLOBALS['mysql_con'],$query);
        if(!empty($query2)){
            @mysqli_query($GLOBALS['mysql_con'],$query2);
            @mysqli_query($GLOBALS['mysql_con'],$query3);
        }
        if(!empty($query4)){
            @mysqli_query($GLOBALS['mysql_con'],$query4);
        }
		if($inserted == true) {
			$input_page_id = mysqli_insert_id($GLOBALS['mysql_con']);
			$messages[] = '<div class="successbox">' . $translation->get($language_part . "_msg_success1") . '</div>';
		} else {
			$messages[] = '<div class="successbox">' . $translation->get($language_part . "_msg_success2") . '</div>';
		}
		
		// layout includes (css / js)
		$query = "SELECT id, default_active FROM main_layout_inclusions WHERE main_layout_id = '" . $_POST["input_main_layout_id"] . "' ORDER BY sorting ASC";
		$result = @mysqli_query($GLOBALS['mysql_con'],$query);
		$box = array();
		WHILE ($box = @mysqli_fetch_assoc($result)) {
			$query_2 = "SELECT id, active FROM main_page_layout_inclusion_link WHERE main_layout_includes_id = '" . $box["id"] . "' AND main_page_id = '" . $input_page_id ."' LIMIT 1";
			$result_2 = @mysqli_query($GLOBALS['mysql_con'],$query_2);
			$arr_ind = "input_script_" . $box["id"] . "_active";
			IF ($_POST[$arr_ind] == "on") {
					$active = '1';
				} ELSE {
					$active = '0';
				}
			IF (@mysqli_num_rows($result_2) == 0) {
				IF ($active != $box["default_active"]) {
					$query_3 = "INSERT INTO main_page_layout_inclusion_link SET
						main_page_id = " . $input_page_id . ",
						main_layout_includes_id = " . $box["id"] . ",
						active = " . $active;
					@mysqli_query($GLOBALS['mysql_con'],$query_3);
				}
			} ELSE {
				$row = @mysqli_fetch_assoc($result_2);
				$result_3 = $row[0];
				IF ($active != $result_3["active"]) {
					$query_3 = "UPDATE main_page_layout_inclusion_link SET
						active = " . $active . " WHERE
						main_page_id = " . $input_page_id . " AND
						main_layout_includes_id = " . $box["id"];
					@mysqli_query($GLOBALS['mysql_con'],$query_3);
				}
			}
		}
		
		// bausteine auf der seite
		$query = "SELECT id, default_active FROM main_component WHERE (main_language_id = " . (int)$GLOBALS["language"]['id'] . " OR all_languages = 1)";
		$result = @mysqli_query($GLOBALS['mysql_con'],$query);
		$box = array();
		WHILE ($box = @mysqli_fetch_assoc($result)) {
			$query_2 = "SELECT id, active FROM main_page_component_include_link WHERE main_component_id = '" . $box["id"] . "' AND main_page_id = '" . $input_page_id ."' LIMIT 1";
			$result_2 = @mysqli_query($GLOBALS['mysql_con'],$query_2);
			$arr_ind = "input_component_" . $box["id"] . "_active";
			IF ($_POST[$arr_ind] == "on") {
					$active = '1';
				} ELSE {
					$active = '0';
				}
			IF (@mysqli_num_rows($result_2) == 0) {
				IF ($active != $box["default_active"]) {
					$query_3 = "INSERT INTO main_page_component_include_link SET
						main_page_id = " . $input_page_id . ",
						main_component_id = " . $box["id"] . ",
						active = " . $active;
					@mysqli_query($GLOBALS['mysql_con'],$query_3);
				}
			} ELSE {
				$row = @mysqli_fetch_assoc($result_2);
				$result_3 = $row[0];
				IF ($active != $result_3["active"]) {
					$query_3 = "UPDATE main_page_component_include_link SET
						active = " . $active . " WHERE
						main_page_id = " . $input_page_id . " AND
						main_component_id = " . $box["id"];
					@mysqli_query($GLOBALS['mysql_con'],$query_3);
				}
			}
		}
		
		edit_page($input_page_id, $messages);
	} else {
	 headerFunctionBridge('EDIT_ERROR: 1');
		$input_page = array();
		$input_page["title"] = $_REQUEST["input_page_name"];
		$input_page["subtitle"] = $_REQUEST["input_browser_title"];
		$input_page["meta_keywords"] = $_REQUEST["input_meta_keywords"];
		$input_page["meta_description"] = $_REQUEST["input_meta_description"];
		$input_page["active"] = $_REQUEST["input_active"]; 
		$input_page["validity_from"] = $_REQUEST["input_validity_from"];
		$input_page["validity_to"] = $_REQUEST["input_validity_to"];
        $input_page["noindex"] = $_REQUEST["input_noindex"];
        $input_page["nofollow"] = $_REQUEST["input_nofollow"];
        $input_page["shopping_world"] = $_REQUEST["input_shopping_world"];

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_cardform.inc.php';
	}
}

function moveup_line_page() {
	move_line_page("<","DESC",$_REQUEST["input_id"]);
}

function movedown_line_page() {
	move_line_page(">","ASC",$_REQUEST["input_id"]);
}

function move_line_page($way,$order,$field_id) {	
	$query = "SELECT * FROM main_page_link WHERE id = '" . $field_id . "' LIMIT 1";
	$result = @mysqli_query($GLOBALS['mysql_con'],$query);
	if(@mysqli_num_rows($result) == 1) {
		$curr_line = @mysqli_fetch_array($result);
	}
	
	$query = "SELECT * FROM main_page_link WHERE main_page_id = " . (int)$curr_line["main_page_id"] . " AND main_page_link_parent_id = " . $curr_line['main_page_link_parent_id'] . " AND sorting " . $way . " " . $curr_line["sorting"] . " ORDER BY sorting " . $order . " LIMIT 1";
	$result = @mysqli_query($GLOBALS['mysql_con'],$query);
	if(@mysqli_num_rows($result) == 1) {
		$change_line = @mysqli_fetch_array($result);
	}
	if(($curr_line["id"]<>'') && ($change_line["id"]<>'')) {
		$query = "UPDATE main_page_link SET sorting = " . $change_line["sorting"] . " WHERE id = " . $curr_line["id"];
		@mysqli_query($GLOBALS['mysql_con'],$query);
		$query = "UPDATE main_page_link SET sorting = " . $curr_line["sorting"] . " WHERE id = " . $change_line["id"];
		@mysqli_query($GLOBALS['mysql_con'],$query);
		$query = "UPDATE main_page set modified_date = '" . date('Y-m-d H:i:s',time()) . "', modified_user = " . (int)$GLOBALS["admin_user"]['id'] . " where id = " . $curr_line["main_page_id"];
		@mysqli_query($GLOBALS['mysql_con'],$query);
	}
}

function line_already_exists($_pageid, $_sitepartid, $sitepart_headerid) {
	$query = "SELECT id FROM main_page_link WHERE main_page_id = '" . (int)$_pageid . "' AND main_sitepart_id = '" . (int)$_sitepartid . "' AND main_sitepart_header_id = '" . (int)$sitepart_headerid."'";
	$result = @mysqli_query($GLOBALS['mysql_con'],$query);
	if(@mysqli_num_rows($result) >= 1) {
		$row = @mysqli_fetch_array($result);
		return $row['id'];	
	} else {
		return false;
	}
}

function get_real_page_link_id() {
	if(isset($_REQUEST['get_real_page_link_id']) && $_REQUEST['get_real_page_link_id'] == 1) {
		$_REQUEST['input_id'] = line_already_exists($_REQUEST["input_page_id"], $_REQUEST['data-sitepartid'], $_REQUEST["input_id"]);
	}
}

function page_filter($site_id, $language_id, $selected_id, $include_sub = 1, $from_level = 1, $to_level = 3) {
	$translation = \DynCom\dc\common\classes\Registry::get("translation");
	$checked = "";
	if($include_sub === 1) {
		$checked = 'checked="checked"';
	}

	echo "<div class=\"input\"><select onchange=\"filter_page();\" class=\"bigselect\" name=\"page_filter\" id=\"page_filter\">";
	echo "<option value=''>" . $translation->get("show_all") . "</option>";
	page_filter_rek($site_id,$language_id, 0, 1, "", $from_level, $to_level, $selected_id);
	echo "</select>";
	echo "<input onclick=\"filter_page();\" $checked type=\"checkbox\" name=\"include_children\" id=\"include_children\" />&nbsp;" . $translation->get("include_subpages");
	echo "</div>";
}

function page_filter_rek($site_id, $language_id, $parent_id, $level, $navcode, $from_level, $to_level, $selected_id) {
	$spaces = "";
	$count = 1;
	while($count < $level) {
		$spaces .= "&nbsp;&nbsp;&nbsp;";
		$count++;
	}
	$parent_id_query = ($parent_id == 0) ? " AND parent_id IS NULL" : " AND parent_id = '" . $parent_id."'";
	$result = @mysqli_query($GLOBALS['mysql_con'],"SELECT * FROM main_navigation WHERE main_site_id = '" . $site_id . "' AND main_language_id = '" . $language_id."'" . $parent_id_query . " ORDER BY sorting ASC");
	$show_level = (($level >= $from_level) && ($level <= $to_level)) ? true : false;
	if(@mysqli_num_rows($result) > 0) {
		while ($nav = @mysqli_fetch_array($result)) {
            //TODO prepared statement
		    $res = @mysqli_query($GLOBALS['mysql_con'],"SELECT is_shopping_world FROM main_page WHERE id = '" . $nav["forward_page_id"] . "'");
			if(mysqli_num_rows($res) == 0) {
				$shopping_world_result = array();
				$shopping_world_result['is_shopping_world'] = 0;
			} else {
				$shopping_world_result = @mysqli_fetch_array($res);
			}

            if (($shopping_world_result['is_shopping_world'] == 0 && !on_shopping_world()) || ($shopping_world_result['is_shopping_world'] == 1 && on_shopping_world())) {
                $newnavcode = $navcode . $nav["code"] . "/";
                $active = "";
                if ($show_level) {
                    if($selected_id == $nav['id']) {
                        $active = 'selected="selected"';
                    }

                    echo "\n<option " . $active . "value=\"" . $nav["id"] . "\">" .  $spaces . $nav["menu_name"] . "</option>";
                    page_filter_rek($site_id, $language_id, $nav["id"], ($level+1),$newnavcode, $from_level, $to_level, $selected_id );
                }
            }
		}
	} else {
		if ($level == 1) {
			echo "\n<option selected=\"selected\" value=\"0\"> - </option>";
		}
	}
	return true;
}

/*******************************************
 * FUnktionen fuer gruppen
 *******************************************/
function save_group_page() {
	$translation = \DynCom\dc\common\classes\Registry::get("translation");
	$error = false;

	if(($_REQUEST["input_main_page_group_code"] == '') | ($_REQUEST["input_main_page_group_name"] == '')) {
		$messages[] = "<div class=\"errorbox\">" . $translation->get("error_textkey_name") . "</div>\n";
		$error = true;
	}
	
	if($error === false) {
		if($_REQUEST["input_id"] != "") {
			// update
			$query = "UPDATE main_page_link SET 
				main_page_group_code = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_main_page_group_code']) . "',
				main_page_group_name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_main_page_group_name']) . "',
				main_page_group_link = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_main_page_group_link']) . "',
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . ",
				modified_date = " . time() . "

				WHERE id = '" . $_REQUEST["input_id"] . "'
			";
			
			@mysqli_query($GLOBALS['mysql_con'],$query);
			
			$messages = array(
				'<div class="successbox">' . $translation->get("group_edit_success") . '</div>'
			);
			
		} else {
			set_page_link(0,0,0,0,0,0,0,0,1,mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_main_page_group_code"]), mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_main_page_group_name"]), mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_main_page_group_link"]));
			
			$messages = array(
				'<div class="successbox">' . $translation->get("page_line_assoc_success") . '</div>'
			);
		}
		
		if($_REQUEST['save_and_close'] == 1) {
			require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_line_listform.inc.php';
		} else {
			edit_group_page("", $messages);
		}
		
	} else {
		$input_line = array();
		$input_line['id'] = $_REQUEST["input_id"];
		$input_line['main_page_group_code'] = $_POST['input_main_page_group_code'];
		$input_line['main_page_group_name'] = $_POST['input_main_page_group_name'];
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_line_group_cardform.inc.php';
	}
}

function edit_group_page($_id = "", $messages = array()) {
	if((int)$_id > 0) {
		$input_id = $_id;
	} else {
		$input_id = $_REQUEST["input_id"];
	}
	
	if($input_id <> '') {
		$query="SELECT * FROM main_page_link WHERE id = '" . $input_id . "' LIMIT 1";
		$result = @mysqli_query($GLOBALS['mysql_con'],$query);
		if(@mysqli_num_rows($result) == 1) {
			$input_line = @mysqli_fetch_array($result);
			require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_line_group_cardform.inc.php';
		} 
	} else {
		$input_line = array();
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_line_group_cardform.inc.php';
	}
}

function group_select($page_id, $line_id, $name,$selectname = "input_main_page_link_parent_id", $value = NULL, $disabled = FALSE) {
	$translation = \DynCom\dc\common\classes\Registry::get("translation");
	
	// pruefen ob gruppen vorhanden sind und ob der inhalt selbst keine gruppe ist
	$query = "SELECT main_page_group FROM main_page_link where id = '" . $line_id."'";
	$result = @mysqli_query($GLOBALS['mysql_con'],$query);
	$row = @mysqli_fetch_array($result);
	$showGroup = true;
	
	if($row['main_page_group'] == 1) {
		$showGroup = false;
	}
	
	$query = "SELECT COUNT(*) AS anzahl from main_page_link where main_page_group = 1 and main_page_id = '" . $page_id."'";
	$result = @mysqli_query($GLOBALS['mysql_con'],$query);
	$row = @mysqli_fetch_array($result);
	if($row['anzahl'] == 0) {
		$showGroup = false;
	}
	
	if($showGroup === false) {
		echo "<input type=\"hidden\" name=\"input_main_page_link_parent_id\" value=\"0\" />";
		return;
	}
	
	// Ab hier sind gruppen auf der seite vorhanden und aktueller inhalt ist keine Gruppe
	
	$disabled_text = ($disabled) ? " disabled=\"disabled\"" : "";
    echo "<div class=\"label\"><label for=\"" . $selectname . "\">" . utf8_decode($name) . "</label></div>";
	echo "<div class=\"input\"><select" . $disabled_text . " class=\"select\" name=\"" . $selectname . "\" id=\"" . $selectname . "\">";
	
	echo "\n<option value=\"0\">" . $translation->get('please_choose') . "</option>"; 
	
	$result = mysqli_query($GLOBALS['mysql_con'],"SELECT * FROM main_page_link WHERE main_page_id = '" . $page_id . "' AND main_page_group = 1 ORDER BY sorting ASC");
	if(mysqli_num_rows($result) > 0) {
		while($row = mysqli_fetch_array($result)) {
			if($row["id"] == $value) {
				echo "<option selected=\"selected\" value=\"" . $row["id"] . "\">" . $row["main_page_group_name"] . "</option>";
			} else {
				echo "<option value=\"" . $row["id"] . "\">" . $row["main_page_group_name"] . "</option>";
			}
		}
	} else {
		echo "<option value=\"\"> - </option>";
	}
	echo "</select></div>";
}

function show_page_link_rows() {
	$main_layout_id = $GLOBALS["language"]["main_layout_id"];
	$input_page_id = $_REQUEST["input_page_id"];
	$translation = \DynCom\dc\common\classes\Registry::get("translation");
	global $layoutareas;
	
	// alle layoutareas
	$layoutareas = array();
	$query = "SELECT * FROM main_layout_area where main_layout_id = '" . $main_layout_id."'";
	if($result = @mysqli_query($GLOBALS['mysql_con'],$query)) {
		while ($row = @mysqli_fetch_array($result,1)) {
			$layoutareas[$row['id']] = $row;
		}
	}
	
	$query = "SELECT 
				main_page_link.id, 
				main_page_link.main_sitepart_id, 
				main_page_link.main_sitepart_header_id,
				main_page_link.main_collection_list, 
				main_page_link.main_collection_id, 
				main_page_link.main_collection_setup_id,
				main_page_link.main_page_group,
				main_page_link.main_page_group_name,
				main_page_link.active,
				main_page_link.layout_area_id,
				from_unixtime(main_page_link.modified_date, '%d.%m.%Y %H:%i:%s') AS aenderungs_datum, 
				main_admin_user.name AS username 
			from 
				main_page_link left join main_admin_user 
			ON 
				main_page_link.modified_user = main_admin_user.id 
			where 
				main_page_link.main_page_id = '" . $input_page_id . "' 
				AND main_page_link.main_page_link_parent_id = 0
			ORDER BY 
				sorting ASC";
				
	if($result = @mysqli_query($GLOBALS['mysql_con'],$query)) {
		$num_rows = @mysqli_num_rows($result);
		if($num_rows >= 1) {
			echo "  <table cellpadding=0 cellspacing=0 border=0 class=\"linklist\">\n";
			
			echo "<tr>";
			
			echo "<td><div>" . $translation->get("description") . "</div></td>";
			echo "<td width=\"150\" align=\"left\"><div></div></td>";
			echo "<td width=\"150\" align=\"left\"><div>" . $translation->get("type") . "</div></td>";
			echo "<td width=\"150\" align=\"left\"><div>" . $translation->get("state") . "</div></td>";
			echo "<td width=\"250\" align=\"left\"><div>" . $translation->get("layout_area") . "</div></td>";
			
			echo "</tr>";
			
			echo "<tr>";
			echo format_seperator("");
			echo format_seperator("");
			echo format_seperator("");
			echo format_seperator("");
			echo format_seperator("");
			echo "</tr>";
			
			echo "  </table>\n";
			
			get_real_page_link_id();
			
			echo "<ol class=\"linklist sortable contentlist\">";
			while ($row = @mysqli_fetch_array($result,1)) {
				handle_page_link_row($row);
			}
			echo "</ol>";
			
			
		} else {
			echo "<div class=\"infobox\">" . $translation->get("no_rows_found") . "</div>";
		}
		
	}
}

function handle_page_link_row($row, $level=0) {
	$siteparts = \DynCom\dc\common\classes\Siteparts::get();
	global $layoutareas;
	$translation = \DynCom\dc\common\classes\Registry::get("translation");
	
	$isGroup = false;
	
	$sitepartDescription = "";
	$row_sitepart_description = array();
	if((int)$row['main_sitepart_id'] > 0) {
		
		if((int)$row['main_sitepart_header_id'] <= 0) {
			
			// siteparts, die keine header_table brauchen
			$sitepartType = $siteparts[$row['main_sitepart_id']]['description'];
			$row_sitepart_description['description'] = $siteparts[$row['main_sitepart_id']]['sub_description'];
			$dblClickAction = "edit_line_properties_page";
			
		} else {
			
			// siteparts mit einer header_table
			$query_description = "SELECT description FROM " . $siteparts[$row['main_sitepart_id']]['header_table'] . " WHERE id = " . $row['main_sitepart_header_id'];
			$result_description = @mysqli_query($GLOBALS['mysql_con'],$query_description);
			$row_sitepart_description = @mysqli_fetch_array($result_description);
			$sitepartType = $siteparts[$row['main_sitepart_id']]['description'];
			$dblClickAction = "edit_line_page";
			if(empty($row_sitepart_description['description'])) {
				$row_sitepart_description['description'] = $siteparts[$row['main_sitepart_id']]['description'];
			}			
		}
		
	} elseif((int)$row['main_collection_list'] == 2) {
		
		// kollektionsvorschau
		$sitepartType = $translation->get("collection_preview");
		$query_description = "SELECT description FROM main_collection_setup WHERE id = " . $row['main_collection_setup_id'];
		$result_description = @mysqli_query($GLOBALS['mysql_con'],$query_description);
		$row_sitepart_description = @mysqli_fetch_array($result_description);
		$dblClickAction = "edit_collection_preview_page";
		
	} elseif((int)$row['main_collection_list'] == 1) {
		
		// Komplette Kollektion
		$sitepartType = $translation->get("collection");
		$query_description = "SELECT description FROM main_collection_setup WHERE id = " . $row['main_collection_setup_id'];
		$result_description = @mysqli_query($GLOBALS['mysql_con'],$query_description);
		$row_sitepart_description = @mysqli_fetch_array($result_description);
		$dblClickAction = "edit_collection_list_page";
	} elseif((int)$row['main_page_group'] > 0) {
		
		// Gruppe
		$sitepartType = $translation->get("group");
		$row_sitepart_description['description'] =  $row['main_page_group_name'];
		$dblClickAction = "edit_group_page";
		
		$isGroup = true;
	}
	
	
	$layoutarea_description = $layoutareas[$row['layout_area_id']]['name'];
	
	$sitepartCode = $siteparts[$row['main_sitepart_id']]['code'];
	
	$checkedRow = $row['id'] == $_REQUEST["input_id"] ? 'true' : 'false';
	
	$aktivText = ($row["active"] == 1 ? $translation->get("active") : $translation->get("inactive"));
	
	
//	echo "    <tr class=\"linklist_content\" data-sitepartid=\"{$row['main_sitepart_id']}\" data-checked=\"{$checkedRow}\" data-inputname=\"input_id\" data-action=\"" . $dblClickAction . "\" data-inputid=\"{$row['id']}\">\n";
//	
	
//	echo format_value($sitepartType, "input_id");
//	echo format_value($row_sitepart_description['description'], "input_id");
//	echo format_value($aktivText, "input_id");
//	echo format_value($layoutarea_description, "input_id");
//	
//	echo "    </tr>\n";
	
	$noNestingClass = "mjs-nestedSortable-no-nesting";
	if($isGroup === true) {
		$noNestingClass = "";
	}
	
	
	echo "<li class=\"" . $noNestingClass . "\" id=\"list_{$row['id']}\">";
	echo "<div  data-inputid=\"" . $row['id'] . "\" data-sitepartid=\"{$row['main_sitepart_id']}\" data-action=\"" . $dblClickAction . "\" data-inputname=\"input_id\" data-checked=\"{$checkedRow}\" class=\"linklist_content dd-handle\"><span class=\"disclose\"><span>&nbsp;</span></span>" . $row_sitepart_description['description'] . "<div class=\"dd-icon\"></div>";
	echo "<div class=\"dd-code\">" . $sitepartType . "</div>";
	echo "<div class=\"dd-status\">" . $aktivText . "</div>";
	echo "<div class=\"dd-layout\">" . $layoutarea_description . "</div></div>";
	
	if($isGroup === true) {
		$level++;
		// wenn gruppe, dann alle datensaetze aus der gruppe auslesen
		$query = "SELECT 
				main_page_link.id, 
				main_page_link.main_sitepart_id, 
				main_page_link.main_sitepart_header_id,
				main_page_link.main_collection_id, 
				main_page_link.main_collection_setup_id,
				main_page_link.main_collection_list,
				main_page_link.main_page_group,
				main_page_link.main_page_group_name,
				main_page_link.active,
				main_page_link.layout_area_id,
				from_unixtime(main_page_link.modified_date, '%d.%m.%Y %H:%i:%s') AS aenderungs_datum, 
				main_admin_user.name AS username 
			from 
				main_page_link left join main_admin_user
			ON 
				main_page_link.modified_user = main_admin_user.id 
			where 
				main_page_link.main_page_link_parent_id = " . $row['id'] . "
			ORDER BY 
				sorting ASC";
		
		if(!$result = @mysqli_query($GLOBALS['mysql_con'],$query)) {
			return;
		}
		
		$num_rows = @mysqli_num_rows($result);
		if($num_rows <= 0) {		
			return;
		}
		echo "<ol>";
		while ($row = @mysqli_fetch_array($result,1)) {
			handle_page_link_row($row, $level);
		}
		echo "</ol>";
	}
	
	echo "</li>";
}