<?php

function getLogFilePath($file) {
    $root = rtrim(dirname(dirname(__DIR__)),'/\\');
    $logsDir = $root . DIRECTORY_SEPARATOR . 'logs';
    $date = date('Y-m-d');
    $logFilePath = $logsDir . DIRECTORY_SEPARATOR . str_replace('.php','',basename($file)) . '_log_' . $date . '.log';
    return $logFilePath;
}

function navconnectLog($msg,$file,$line) {
    $logFilePath = getLogFilePath($file);
    $date = date('Y-m-d H:i:s');
    $prefix = $file . ' - ' . $line . ' - ' . $date . ': ';
    $message = rtrim($prefix . $msg,PHP_EOL)  . PHP_EOL;
    try {
        file_put_contents($logFilePath, $message, FILE_APPEND);
    } catch (Throwable $t) {
        echo $t->getMessage() . '|TRACE: ' . $t->getTraceAsString();
    }
};

navconnectLog('Received Request',__FILE__,__LINE__);

//Vendor Autoloader
//Load environment variables from config if exists
$rootDir = rtrim(dirname(dirname(__DIR__)),'/');
$vendorDir = $rootDir . '/vendor';
$vendorAutoloadPath = $vendorDir . '/autoload.php';
if (file_exists($vendorAutoloadPath)) {
    require $vendorAutoloadPath;
}
$envDir = $rootDir . '/config';
if (is_dir($envDir)) {
	$dotenv = new \Dotenv\Dotenv($envDir);
	$dotenv->load();
}
ini_set('display_errors',0);

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'dc/common/common_functions.inc.php';

$initCMSPath = $rootDir . '/dc/init.php';
$initShopPath = $rootDir . '/module/dcshop/common/init.php';
if (file_exists($initCMSPath)) {
    require $initCMSPath;
}
if (file_exists($initShopPath)) {
    require $initShopPath;
}
$rootDir = rtrim(dirname(dirname(__DIR__)),'/\\');
$dcConfigLocation = (local_environment()) ? $rootDir . DIRECTORY_SEPARATOR . 'dc/dc.config.php' : $rootDir . DIRECTORY_SEPARATOR . 'dc/dc-server.config.php';
require_once($dcConfigLocation);
require_once __DIR__ . DIRECTORY_SEPARATOR . 'shop.config.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'common/shop_functions.inc.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'common/navconnect_functions.inc.php';
ini_set('display_errors',0);



//require für Newslettermodul
//require_once __DIR__ . DIRECTORY_SEPARATOR . 'newsletter/newsletter_navconnect_functions.inc.php';

// Verbindung mit Datenbank herstellen
db_connect();

// Variablen schützen
$_GET = secure_array($_GET);
$_POST = secure_array($_POST);

//XML in Post-Parameter umwandeln für neue Upload-Funktion
xml2post();

if ($_REQUEST["force"] == "1") {
    google_merchant_center();
}

// Passwort prüfen und Funktion aufrufen
if($_POST['pass'] == $GLOBALS["shop_setup"]["shop_password"]) {
  if(!($_GET["action"]=='wrapper')){
	switch($_GET['action']) {
		case 'connection_test': echo "100"; break;
		case 'upload_attachment': upload_attachment($GLOBALS["shop_setup"]["uploaddir_attachements"]); break;
		default:
			if(function_exists($_GET['action'])) {
				$_GET['action']();
			} else {
				print_r($_GET);
				echo "Webshop Funktion '" . $_GET['action'] . "' nicht gefunden";
			}
		break;
	}
  } else {
	$wrapperdata=json_decode(stripslashes($_POST["wrapperdata"]),true);
	if(strlen($wrapperdata[0]["reqaction"])>0){
		$errormessages = array();		
		$sum_successful = 0;
		$request_count = count($wrapperdata);
		for($i=0; $i < $request_count; $i++){
			$_GET["action"]=$wrapperdata[$i]["reqaction"];
			parse_str($wrapperdata[$i]["reqdata"],$_POST);		
			switch($_GET['action']) {
				case 'connection_test': echo "100"; break;
				case 'upload_attachment': upload_attachment($GLOBALS["shop_setup"]["uploaddir_attachements"]); break;
				default:
					if(function_exists($_GET['action'])) {
						ob_start();
						$_GET['action']();
						$response = ob_get_contents();
						ob_end_clean();
						if($response=="100" || $response==""){
							if($i>80){
								//echo "success";
							}
							$sum_successful++;
						} else {
							$errormessages[]=$response;
						}
					} else {
						print_r($_GET);
						echo "Webshop Funktion '" . $_GET['action'] . "' nicht gefunden";
					}
				break;
			}
		}
		if($sum_successful==$request_count){
			echo "100";
		} else {
			echo "succ: ".$sum_successful." req: ".$request_count." Err:";
			print_r($errormessages);
		}
	} else {
		echo "wrapper invalid";
	}		
  }
} else {
	echo "Webshop Zugangsdaten sind nicht korrekt.";
}

function xml2post(){
	
	if (!isset($_POST['<?xml_version'])){
		return;
	}
	$xml = $_POST['<?xml_version'];
	
	/*File_Name*/
	$tag = '<filename>';
	$start = strpos($xml,$tag) + strlen($tag);
	$length = strpos($xml,'</filename>') - $start;
	$_POST['filename'] = substr($xml,$start,$length);
	
	/*File_Content*/
	$tag = '<filedata>';
	$start = strpos($xml,$tag) + strlen($tag);
	$length = strpos($xml,'</filedata>') - $start;
	$_POST['filedata'] = substr($xml,$start,$length);
	$_POST['filedata'] = str_replace('\r\n','',$_POST['filedata']);
	$_POST['filedata'] = str_replace(' ','+',$_POST['filedata']);

	
	/*Password*/
	$tag = '<pass>';
	$start = strpos($xml,$tag) + strlen($tag);
	$length = strpos($xml,'</pass>') - $start;
	$_POST['pass'] = substr($xml,$start,$length);	
	
	/*File_Content*/
	$tag = '<action>';
	$start = strpos($xml,$tag) + strlen($tag);
	$length = strpos($xml,'</action>') - $start;
	$_POST['action'] = substr($xml,$start,$length);	
}

// Hochladen von Dateien über Dynamics NAV
function upload_attachment($uploadpath) {
	$uploadpath = '../..' . $uploadpath;
	switch($_POST["action"]) {
		case 'create':
			$handler = fopen($uploadpath . $_POST["filename"],"w+");
			fclose($handler);
			echo "100";
		break;
		case 'write':
			$content = file_get_contents($uploadpath . $_POST["filename"]);
			file_put_contents($uploadpath . $_POST["filename"], $content .= utf8_encode($_POST["filedata"]));
			echo "100";
		break;
		case 'convert':
			$content = file_get_contents($uploadpath . $_POST["filename"]);
			file_put_contents($uploadpath . $_POST["filename"], base64_decode($content));
			echo "100";
		break;
	}
}

// Artikeldatei (Bilder, Dokumente, Videos) löschen
function delete_item_file() {
    $rootDir = rtrim(dirname(dirname(__DIR__)),'\\/');
	$query = "SELECT id, filename FROM shop_item_file WHERE company = '" . $_POST["company"] . "' AND shop_code = '" . $_POST["shop_code"] . "' AND language_code = '" . $_POST["language_code"] . "' AND item_no = '" . $_POST["item_no"] . "' AND type = '" . $_POST["type"] . "' AND line_no = '" . $_POST["line_no"] . "'";
	$result = @mysqli_query($GLOBALS['mysql_con'],$query);
	if(@mysqli_num_rows($result)==1) {
		$file = @mysqli_fetch_array($result);
		if($file["filename"]<>'') {
			switch($_POST["type"]) {
			    //Image
				case \DynCom\dc\dcShop\classes\WebshopItemFile::TYPE_IMAGE:
					foreach($GLOBALS["shop_setup"]["image_config"] as $image) {
						$filename = $rootDir . $image["path"] . $file["filename"];
						if(file_exists($filename)) {
							unlink($filename);
						}
					}
				break;
				//Document
				case \DynCom\dc\dcShop\classes\WebshopItemFile::TYPE_DOCUMENT_FILE:
					$filename = $rootDir . $GLOBALS["shop_setup"]["uploaddir_documents"] . $file["filename"];
					if(file_exists($filename)) {
						unlink($filename);
					}
				break;
				//Video
				case \DynCom\dc\dcShop\classes\WebshopItemFile::TYPE_VIDEO_FILE:
					$filename = $rootDir . $GLOBALS["shop_setup"]["uploaddir_videos"] . $file["filename"];
					if(file_exists($filename)) {
						unlink($filename);
					}
				break;
				//Youtube Video ID
                case \DynCom\dc\dcShop\classes\WebshopItemFile::TYPE_YOUTUBE_ID:
                    //No files for video id, do nothing
                    break;
                //360DegreeImage
                case \DynCom\dc\dcShop\classes\WebshopItemFile::TYPE_360_DEGREE_IMAGE:
                    $filename = $rootDir . $GLOBALS['shop_setup']['uploaddir_360_degree_images'] . DIRECTORY_SEPARATOR . $file['filename'];
                    //Chek if dir exists and below rootDir
                    $realFilePath = realpath($filename);
                    $realRootPath = realpath($rootDir);
                    navconnectLog('In Delete 360 Img with raw filepath [' . $filename . '], realFilePath [' . $realFilePath . '] and realRootPath [' . $realRootPath . '].',__FILE__,__LINE__);
                    if (
                            (mb_strlen($realFilePath) > mb_strlen($realRootPath))
                        &&  is_dir($realFilePath)
                        &&  is_writable($realFilePath)
                    ) {
                        navconnectLog('RealfiePath-length > realRootPath-length && is dir realFilePath && is writable realFilePath',__FILE__,__LINE__);
                        $output = [];
                        $returnVar = 0;
                        //IF os-string starts with "WIN"
                        //Cannot compare !== false because
                        if (0 === stripos(PHP_OS,'WIN')) {
                            $cmd = sprintf("rd /s /q %s", escapeshellarg($realFilePath));
                            exec($cmd,$output,$returnVar);
                        } else {
                        //Linux
                            $cmd = sprintf("rm -rf %s", escapeshellarg($realFilePath));
                            exec($cmd,$output,$returnVar);
                        }
                        navconnectLog('Output of exec [' . $cmd . '] is [' . print_r($output,1) . '] with return-var [' . $returnVar . '].',__FILE__,__LINE__);
                    } else {
                        navconnectLog('RealfiePath-length !> realRootPath-length || !is dir realFilePath || !is writable realFilePath',__FILE__,__LINE__);
                    }
                    clearstatcache(true);
                    $isDir =  is_dir($realFilePath);
                    navconnectLog('Checked is_dir on [' . $realFilePath . '] - result: [' . $isDir . '].',__FILE__,__LINE__);
                    if ($isDir) {
                        echo "Fehler in Funktion 'delete_item_file' - konnte pfad [$realFilePath] nicht löschen.";
                        exit;
                    }

			}
		}
		$query = "DELETE FROM shop_item_file WHERE id = '" . $file["id"] . "'";
		if(!@mysqli_query($GLOBALS['mysql_con'],$query)) {
			echo "Fehler in Funktion 'delete_item_file' \nquery: " . $query . " \n";
		}
	}
	echo "100";
}

// Kategoriedatei (Bilder, Beschreibungen) löschen
function delete_category_file() {
	$countquery = "SELECT id, category_picture, category_icon FROM shop_category WHERE company = '" . $_POST["company"] . "' AND shop_code = '" . $_POST["shop_code"] . "' AND language_code = '" . $_POST["language_code"] . "' AND line_no = '" . $_POST["line_no"] . "'";
	$result = @mysqli_query($GLOBALS['mysql_con'],$countquery);
	if(@mysqli_num_rows($result)==1) {
		$category = @mysqli_fetch_array($result);
		switch($_POST["type"]) {
			case 0:
				$query = "UPDATE shop_category SET category_picture = '' WHERE id = " . $category["id"];
				$filename = ($category["category_picture"] <> '') ? "../.." . $GLOBALS["shop_setup"]['uploaddir_category_picture'] . $category["category_picture"] : "";
				break;
			case 1:
				$query = "UPDATE shop_category SET category_icon = '' WHERE id = " . $category["id"];
				$filename = ($category["category_icon"] <> '') ? "../.." . $GLOBALS["shop_setup"]['uploaddir_category_icon'] . $category["category_icon"] : "";
				break;
			case 2: $query = "UPDATE shop_category SET category_description = '' WHERE id = " . $category["id"]; break;
			case 3: $query = "UPDATE shop_category SET promotion_description = '' WHERE id = " . $category["id"]; break;
			case 4: $query = "UPDATE shop_category SET category_description_2 = '' WHERE id = " . $category["id"]; break;
		}
		if ($filename <> "") {
			if (file_exists($filename)) {
				@unlink($filename);
			}
		}
		if (!@mysqli_query($GLOBALS['mysql_con'],$query)) {
			echo "Fehler in Funktion 'delete_category_file' \nquery: " . $query . " \n";
		}
		echo "100";
	} else {
		if(@mysqli_num_rows($result) <> 0) {
			echo "Fehler in Funktion 'delete_category_file' \nquery: " . $countquery . " \n";
		} else {
			echo "100";
		}
	}
}

//Kategorien tauschen
function swap_category() {
	if(($_POST["company"] <> '') && ($_POST["from_line_no"] <> '') && ($_POST["to_line_no"] <> '') && ($_POST["language_code"] <> '') && ($_POST["shop_code"] <> '')) {
	
		$shopquery="SELECT * FROM shop_shop WHERE company='".$_POST["company"]."' AND code='".$_POST["shop_code"]."'";
		$result=@mysqli_query($GLOBALS['mysql_con'],$shopquery);
		$shop=@mysqli_fetch_assoc($result);
		$query_add='';
		if($shop["use_items_from_shop_code"]!='' && $shop["use_items_from_shop_code"]!=$_POST["shop_code"]){
			$query_add .= " AND shop_code='".$shop["use_items_from_shop_code"]."'";
		} else {
			$query_add .= " AND shop_code='".$_POST["shop_code"]."'";
		}
		if($shop["use_categorys_from_shop_code"]!='' && $shop["use_categorys_from_shop_code"]!=$_POST["shop_code"]){
			$query_add .= " AND category_shop_code='".$shop["use_categorys_from_shop_code"]."'";
		} else {
			$query_add .= " AND category_shop_code='".$_POST["shop_code"]."'";
		}
	
	
		$query = "SELECT * FROM shop_category WHERE company = '" .  $_POST["company"] . "' AND shop_code = '" . $_POST["shop_code"] . "' AND language_code = '" . $_POST["language_code"] . "' AND line_no = '" . $_POST["from_line_no"] . "'";
		$result = @mysqli_query($GLOBALS['mysql_con'],$query);
		if(@mysqli_num_rows($result) == 1) {
			$from_category = @mysqli_fetch_array($result);
		}
		$query = "SELECT * FROM shop_category WHERE company = '" .  $_POST["company"] . "' AND shop_code = '" . $_POST["shop_code"] . "' AND language_code = '" . $_POST["language_code"] . "' AND line_no = '" . $_POST["to_line_no"] . "'";
		$result = @mysqli_query($GLOBALS['mysql_con'],$query);
		if(@mysqli_num_rows($result) == 1) {
			$to_category = @mysqli_fetch_array($result);
		}
		if($from_category["id"] <> '') {
			$query = "UPDATE shop_category SET line_no = '" . $_POST["to_line_no"] . "', sorting = '".$to_category["sorting"]."' WHERE id = '" . $from_category["id"] . "'";
			@mysqli_query($GLOBALS['mysql_con'],$query);
			
			$query = "UPDATE shop_item_has_category SET category_line_no = '-" . $from_category["line_no"] . "' WHERE company = '" .  $from_category["company"] . "' AND language_code = '" . $from_category["language_code"] . "' AND category_line_no = '" . $from_category["line_no"] . "'"; //AND category_shop_code = '" . $from_category["shop_code"] . "' 
			$query .= $query_add;
			
			@mysqli_query($GLOBALS['mysql_con'],$query);
			$query = "UPDATE shop_attribute_link SET line_no = '-" . $from_category["line_no"] . "' WHERE company = '" .  $from_category["company"] . "' AND shop_code = '" . $from_category["shop_code"] . "' AND language_code = '" . $from_category["language_code"] . "' AND line_no = '" . $from_category["line_no"] . "'";
			@mysqli_query($GLOBALS['mysql_con'],$query);

			$query = "UPDATE shop_attribute_link SET line_no = '-" . $to_category["line_no"] . "' WHERE company = '" .  $from_category["company"] . "' AND shop_code = '" . $from_category["shop_code"] . "' AND language_code = '" . $from_category["language_code"] . "' AND line_no = '" . $to_category["line_no"] . "'";
			@mysqli_query($GLOBALS['mysql_con'],$query);
		}
		if($to_category["id"] <> '') {
			$query = "UPDATE shop_category SET line_no = '" . $_POST["from_line_no"] . "', sorting = '".$from_category["sorting"]."' WHERE id = '" . $to_category["id"] . "'";
			@mysqli_query($GLOBALS['mysql_con'],$query);
			$query = "UPDATE shop_item_has_category SET category_line_no = '" . $from_category["line_no"] . "' WHERE company = '" .  $to_category["company"] . "' AND language_code = '" . $to_category["language_code"] . "' AND category_line_no = '" . $to_category["line_no"] . "'";
			$query .= $query_add;
			@mysqli_query($GLOBALS['mysql_con'],$query);
		}
		//SH: 24.02.14 das Minus weg, da sonst Aktualisierung aus dem NAV notwendig
		if($from_category["id"] <> '') {
			//$query = "UPDATE shop_item_has_category SET category_line_no = '-" . $to_category["line_no"] . "' WHERE company = '" .  $from_category["company"] . "' AND shop_code = '" . $from_category["shop_code"] . "' AND language_code = '" . $from_category["language_code"] . "' AND category_line_no = '-" . $from_category["line_no"] . "'";
			$query = "UPDATE shop_item_has_category SET category_line_no = '" . $to_category["line_no"] . "' WHERE company = '" .  $from_category["company"] . "' AND language_code = '" . $from_category["language_code"] . "' AND category_line_no = '-" . $from_category["line_no"] . "'";
			$query .= $query_add;
			@mysqli_query($GLOBALS['mysql_con'],$query);
			$query = "UPDATE shop_attribute_link SET line_no = '" . $to_category["line_no"] . "' WHERE company = '" .  $from_category["company"] . "' AND shop_code = '" . $from_category["shop_code"] . "' AND language_code = '" . $from_category["language_code"] . "' AND line_no = '-" . $from_category["line_no"] . "'";
			@mysqli_query($GLOBALS['mysql_con'],$query);
			$query = "UPDATE shop_attribute_link SET line_no = '" . $from_category["line_no"] . "' WHERE company = '" .  $from_category["company"] . "' AND shop_code = '" . $from_category["shop_code"] . "' AND language_code = '" . $from_category["language_code"] . "' AND line_no = '-" . $to_category["line_no"] . "'";
			@mysqli_query($GLOBALS['mysql_con'],$query);
		}
		echo "100";
	}
}

// Artikel-Platzhalterbild in Shop-Sprache entfernen
function delete_item_placeholder_image() {
	$countquery = "SELECT id, item_placeholder_image FROM shop_language WHERE company = '" . $_POST["company"] . "' AND shop_code = '" . $_POST["shop_code"] . "' AND code = '" . $_POST["code"] . "'";
	$result = @mysqli_query($GLOBALS['mysql_con'],$countquery);
	if(@mysqli_num_rows($result)==1) {
		$language = @mysqli_fetch_array($result);
		$query = "UPDATE shop_language SET item_placeholder_image = '' WHERE id = " . $language["id"];
		foreach($GLOBALS["shop_setup"]["image_config"] as $image) {
			$filename = "../.." . $image["path"] . $language["item_placeholder_image"];
			if(file_exists($filename)) {
				@unlink($filename);
			}
		}
		if (!@mysqli_query($GLOBALS['mysql_con'],$query)) {
			echo "Fehler in Funktion 'delete_item_placeholder_image' \nquery: " . $query . " \n";
		}
		echo "100";
	} else {
		echo "Fehler in Funktion 'delete_item_placeholder_image' \nquery: " . $countquery . " \n";
	}
}

// Zugangsdaten an Debitor versenden
function send_customer_login() {
	if($_POST["customer_no"]<>'') {

		// Debitor  ermitteln
		$customerquery = "SELECT * FROM shop_customer WHERE company = '" . $_POST["company"] . "' AND customer_no = '" . $_POST["customer_no"] . "'";
		$customerresult = @mysqli_query($GLOBALS['mysql_con'],$customerquery);
		if(@mysqli_num_rows($customerresult) == 1) {
		 	$customer = @mysqli_fetch_assoc($customerresult);
		 	$shop = get_shop($customer["company"],$customer["shop_code"]);
			$language = get_shop_language($customer["company"],$customer["shop_code"],$customer["language_code"]);

			// Benutzer ermitteln oder anlegen
			$user_query = "SELECT * FROM shop_user WHERE company = '" . $customer["company"] . "' AND customer_no = '" . $customer["customer_no"] . "' AND shop_code = '".$customer["shop_code"]."' AND main_user = TRUE";
			$user_result = @mysqli_query($GLOBALS['mysql_con'],$user_query);
			if(@mysqli_num_rows($user_result)==0) {
				$query = "INSERT INTO shop_user (id, company, shop_code, customer_no, main_user) VALUES (NULL,'".$customer["company"]."','" . $customer["shop_code"] . "','" . $customer["customer_no"] . "',TRUE)";
				@mysqli_query($GLOBALS['mysql_con'],$query);
			}
			$result = @mysqli_query($GLOBALS['mysql_con'],$user_query);
			if(@mysqli_num_rows($result)==1) {

				// Passwort generieren und in Benutzer speichern
				$user = @mysqli_fetch_array($result);
				$password = generate_password();
				$name = ($user["name"] <> '') ? $user["name"] : $customer["name"];
				$login = ($user["login"] <> '') ? $user["login"] : $_POST["customer_no"];
				$query = "UPDATE shop_user SET name = '" . $name . "', email = '" . $_POST["email"] . "', login = '" . $login . "', password = '" . md5($password)  . "', right_user_management = TRUE, right_order_history = TRUE WHERE id = '" . $user["id"] . "'";
				if (@mysqli_query($GLOBALS['mysql_con'],$query)) {

					// Platzhalter füllen
					$spacer["%customer_no%"] = $customer["customer_no"];
					$spacer["%customer_name%"] = $customer["customer_name"];
					$spacer["%user_name%"] = $user["name"];
					$spacer["%login%"] = $login;
					$spacer["%password%"] = $password;
                    $spacer['%email%'] = $_POST["email"];

					// Nachricht und Betreff aus Textbausteinen auslesen
					$message = get_text_module($language["company"],$language["email_login_text_module"],$spacer);
					$subject = get_text_module($language["company"],$language["email_login_text_module"],$spacer,TRUE);

					// Fehler vor dem versenden der E-Mail ausschließen
					$error = ($shop["email_sender"] == "") ? "\nAbsender '' ungültig" : "";
					$error .= ($_POST["email"] == "") ? "\nEmpfänger '' ungültig" : "";
					$error .= ($message == "") ? "\nNachricht '' ungültig" : "";
					$error .= ($subject == "") ? "\nBetreff '' ungültig" : "";
					if($error <> '') {
						echo utf8_decode("Fehler in der Funktion 'send_customer_mail' \n" . $error . "\n\n");
					} else {
						// Nachricht und Betreff aus Textbausteinen auslesen
						if(mail_create($subject, $message, $shop["email_sender"], $_POST["email"], "", "", TRUE, 0, '')) {
							mail_send();
							echo "100";
						} else {
							echo "Fehler in der Funktion 'send_customer_login'\nDie E-Mail konnte nicht versendet werden.";
						}
					}
				} else {
					echo "Fehler in Funktion 'send_customer_login' \nnquery: " . $query . " \n\n";
				}
			} else {
				echo "Fehler in Funktion 'send_customer_login' \n\nquery: " . $user_query . " \n\n";
			}
		} else {
			echo "Fehler in Funktion 'send_customer_login' \n\nquery: " . $customerquery . " \n\n";
		}
	}
}

function change_basket_owner(int $fromVisitorID,int $toVisitorID)
{
    if ($fromVisitorID > 0 && $toVisitorID > 0 && ($fromVisitorID !== $toVisitorID)) {
        $query = "UPDATE shop_user_basket_header_new SET shop_visitor_id = $toVisitorID WHERE shop_visitor_id = $fromVisitorID";
    }
    mysqli_query($GLOBALS['mysql_con'],$query);
}

function change_favorites_owner(int $fromVisitorID, int $toVisitorID)
{
    if ($fromVisitorID > 0 && $toVisitorID > 0 && ($fromVisitorID !== $toVisitorID)) {
        $query = "UPDATE shop_user_favorites SET shop_visitor_id = $toVisitorID WHERE shop_visitor_id = $fromVisitorID";
    }
    mysqli_query($GLOBALS['mysql_con'],$query);
}

// Debitorzugang öffnen (Erzeugt gültige Session die Login ohne Zugangsdaten ermöglichst)
function open_customer_login() {
    if(($_POST["customer_no"]<>'') && ($_POST["company"]<>'')) {
        $query = "SELECT * FROM shop_user WHERE company = '" . $_POST["company"] . "' AND customer_no = '" . $_POST["customer_no"] . "' AND main_user = TRUE";
        $result = @mysqli_query($GLOBALS['mysql_con'],$query);
        if(@mysqli_num_rows($result) == 1) {
            $user = @mysqli_fetch_assoc($result);
            $shop_code = $user['shop_code'];
            $company = $_POST['company'];
            $language_code = $user['language_code'];
            $last_visitor_id = (int)$user['last_visitor_id'];
            $visitor_query = 'SELECT id,session_id FROM main_visitor WHERE frontend_login=1 AND main_user_id=\''.$user['id'].'\' AND (remember_token = \'\' OR remember_token IS NULL) AND cookie_only = 0 AND nav_login=1';
            $visitor_result=@mysqli_query($GLOBALS['mysql_con'],$visitor_query);
            if(@mysqli_num_rows($visitor_result)==1){
                $new_visitor_id = (int)@mysqli_result($visitor_result,0,0);
                $visitor_sid=@mysqli_result($visitor_result,0,1);
                if(strlen($visitor_sid)>0){
                    change_basket_owner($last_visitor_id,$new_visitor_id);
                    echo $visitor_sid;
                    exit;
                }
            }
            session_destroy();
            session_name("sid");
            session_regenerate_id(true);
            session_start();
            session_regenerate_id(true);
            session_name("sid");
            $sid   = session_id();

            $check_if_session_id_allready_exists = "SELECT id,session_id FROM main_visitor WHERE session_id = '". $sid ."'";
            $check_if_session_id_allready_exists_result = @mysqli_query($GLOBALS['mysql_con'],$check_if_session_id_allready_exists);
            if(@mysqli_num_rows($check_if_session_id_allready_exists_result) > 0) {
                $currVisitor = @mysqli_fetch_array($check_if_session_id_allready_exists_result);
                $new_visitor_id = (int)$currVisitor['id'];
                $update_curr_visitor_query = "UPDATE  main_visitor SET main_user_id = '". $user["id"] ."' WHERE id = $new_visitor_id";
                if (@mysqli_query($GLOBALS['mysql_con'], $update_curr_visitor_query)) {
                    echo $currVisitor["session_id"];
                }
                change_basket_owner($last_visitor_id,$new_visitor_id);
            } else {
                $query = "INSERT INTO main_visitor (id,session_id,session_date,frontend_login,cookie_only,main_user_id,nav_login) VALUES (NULL,'" . $sid . "',NOW(),1,0,'" . $user["id"] . "',1)";
                if (@mysqli_query($GLOBALS['mysql_con'], $query)) {
                    $new_visitor_id = (int)mysqli_insert_id($GLOBALS['mysql_con']);
                    change_basket_owner($last_visitor_id,$new_visitor_id);
                    echo $sid;
                }
            }
        }
    } else {
        echo "Fehler in Funktion 'open_customer_login \nParameter 'customer_no' und 'company' müssen angegeben werden";
    }
}


// Verkäuferzugang öffnen (Erzeugt gültige Session die Login ohne Zugangsdaten ermöglichst)
function open_salesperson_login() {
	if(($_POST["salesperson_code"]<>'') && ($_POST["company"]<>'')) {
		$query = "
		SELECT DISTINCT
			shop_salesperson.*, 
			main_language.code AS 'site_language' 
		FROM shop_salesperson 
		LEFT JOIN main_language 
			ON (
				main_language.company = '".$_POST["company"]."' 
			  AND 
				main_language.shop_code = shop_salesperson.shop_code 
			  AND 
				main_language.shop_language_code = shop_salesperson.language_code 
			)
		WHERE 
			shop_salesperson.company = '" . $_POST["company"] . "' 
		  AND 
			shop_salesperson.salesperson_code = '" . $_POST["salesperson_code"] . "' 
		  AND 
			shop_salesperson.shop_code != '' 
		  AND 
			shop_salesperson.language_code != '' 
		  AND 
			shop_salesperson.password != ''";
		$result = @mysqli_query($GLOBALS['mysql_con'],$query);
		if(@mysqli_num_rows($result) == 1) {
			$salesperson = @mysqli_fetch_assoc($result);
			$visitor_query = 'SELECT session_id FROM main_visitor WHERE frontend_login=1 AND shop_salesperson_id=\''.$salesperson['id'].'\'  AND (remember_token = \'\' OR remember_token IS NULL) AND nav_login=1';
			$visitor_result=@mysqli_query($GLOBALS['mysql_con'],$visitor_query);
			if(@mysqli_num_rows($visitor_result)==1){
				$visitor_sid=@mysqli_result($GLOBALS['mysql_con'],$visitor_result);
				if(strlen($visitor_sid)>0){
					echo $salesperson['site_language'].'/?sid='.$visitor_sid;
					exit;
				}
			} elseif (@mysqli_num_rows($visitor_result)>1){
				$max_id_query = 'SELECT MAX(id) FROM main_visitor WHERE frontend_login=1 AND shop_salesperson_id=\''.$salesperson['id'].'\' AND (remember_token = \'\' OR remember_token IS NULL) AND nav_login=1';
				$max_id_result = @mysqli_query($GLOBALS['mysql_con'],$max_id_query);
				$max_id = @mysqli_result($GLOBALS['mysql_con'],$max_id_result);
				$visitor_delete = 'DELETE FROM main_visitor WHERE shop_salesperson_id = \''.$salesperson['id'].'\' AND id != \''.$max_id.'\' AND frontend_login = 1 AND (remember_token = \'\' OR remember_token IS NULL) AND nav_login=1';
				$delete_result=@mysqli_query($GLOBALS['mysql_con'],$visitor_query);
				$visitor_query = 'SELECT session_id FROM main_visitor WHERE id=\''.$max_id.'\'';
				$visitor_result = @mysqli_query($GLOBALS['mysql_con'],$visitor_query);
				$visitor_sid=@mysqli_result($GLOBALS['mysql_con'],$visitor_result);
				if(strlen($visitor_sid)>0){
					echo $salesperson['site_language'].'/?sid='.$visitor_sid;
					exit;
				}
				
			}
			session_name("sid");
			session_start();
			$sid = session_id();
			$query = "INSERT INTO main_visitor (id,session_id,session_date,frontend_login,main_user_id,shop_salesperson_id,nav_login) VALUES (NULL,'" . $sid . "',NOW(),1,'" . $salesperson["id"] . "','".$salesperson['id']."',1)";
			if(@mysqli_query($GLOBALS['mysql_con'],$query)) {
				echo $salesperson['site_language'].'/?sid='.$sid;
			}
		} else {
			echo "Keine Daten gefunden! QUERY:\r\n".$query;
		}
	} else {
		echo "Fehler in Funktion 'open_salesperson_login \nParameter 'salesperson_code' und 'company' müssen angegeben werden";
	}
}

// Gibt den Weblink für einen Artikel zurück (Für Vorschau-Button in Dynamics NAV)
function get_item_link() {
	if(($_POST["company"] <> '') && ($_POST["shop_code"] <> '') && ($_POST["language_code"] <> '')) {
		if ($_POST["customer_no"] <> '') {
			$query = "SELECT * FROM shop_user WHERE company = '" . $_POST["company"] . "' AND customer_no = '" . $_POST["customer_no"] . "' AND main_user = TRUE";
			$result = @mysqli_query($GLOBALS['mysql_con'],$query);
			if(@mysqli_num_rows($result) == 1) {
				$user = @mysqli_fetch_array($result);
				session_name("sid");
				session_start();
				$query = "INSERT INTO main_visitor (id,session_id,session_date,frontend_login,main_user_id) VALUES (NULL,'" . session_id() . "',NOW(),TRUE,'" . $user["id"] . "')";
				if(@mysqli_query($GLOBALS['mysql_con'],$query)) {
					$session = session_id();
				}
			}
		}
		$query2 = "SELECT id FROM shop_item WHERE company = '" . $_POST["company"] . "' AND shop_code = '" . $_POST["shop_code"] . "' AND language_code = '" . $_POST["language_code"] . "' AND item_no='" . $_POST['item_no'] . "'";
		$result2 = mysqli_query($GLOBALS['mysql_con'],$query2);
		if(@mysqli_num_rows($result2) == 1) {
			$item = mysqli_fetch_assoc($result2);
			$query3 = "SELECT code FROM main_language WHERE company='".$_POST["company"]."' AND shop_code='".$_POST["shop_code"]."' AND shop_language_code='".$_POST["language_code"]."'";
			$result3 = mysqli_query($GLOBALS['mysql_con'],$query3);
			$lang_code = mysqli_result($GLOBALS['mysql_con'],$result3,0);
			echo "-p" . $item['id']."/";
			if ($session <> '') {
				echo "&sid=" . session_id();
			}
		}
	} else {
		echo "Fehler in Funktion 'open_customer_login \nParameter 'company', 'shop_code', 'language_code' und customer_no' müssen angegeben werden";
	}
}

// E-Mail an Kunden versenden (Auftragsbestätigung, Lieferbestätigung, Rechnungskopie, Lieferscheinkopie)
function send_customer_email() {
	if (($_POST["company"] <> '') && ($_POST["shop_code"] <> '') && ($_POST["language_code"] <> '')) {
		$shop_setup = get_shop_setup($_POST["company"]);
		$shop = get_shop($_POST["company"],$_POST["shop_code"]);
		$language = get_shop_language($_POST["company"],$_POST["shop_code"],$_POST["language_code"]);

        $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
        $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
        $pdoUser = getenv('MAIN_MYSQL_DB_USER');
        $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
        $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

        $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

        $sendAdditionalMail = false;
		switch($_POST["type"]) {
			case 'order_mail_2':
				$query = "SELECT * FROM shop_sales_header WHERE company = '" . $_POST["company"] . "' AND order_no = '" . $_POST["order_no"] . "'";
				$result = @mysqli_query($GLOBALS['mysql_con'],$query);
				if(mysqli_num_rows($result) == 1) {
					$sales_header = @mysqli_fetch_assoc($result);
				} else {
                    echo '100';
                    die();
                }
				$text_module = $language["email_order_2_text_module"];
				$spacer["%order_no%"] = $_POST["order_no"];
				$recipient = $sales_header["user_email"];
			break;
			case 'order_mail_3':
				$query = "SELECT * FROM shop_sales_header WHERE company = '" . $_POST["company"] . "' AND order_no = '" . $_POST["order_no"] . "'";
				$result = @mysqli_query($GLOBALS['mysql_con'],$query);
				if(mysqli_num_rows($result) == 1) {
					$sales_header = @mysqli_fetch_assoc($result);
				} else {
				    echo '100';
				    die();
                }
				$text_module = $language["email_order_3_text_module"];

                $textModule = getShippingOptionEmailText($sales_header, $pdo);
				if ($textModule != '') {
                    $text_module = $textModule;
                }

                $spacer["%tracking_no%"] = $_POST["tracking_no"];
				$spacer["%tracking_link%"] = $_POST["tracking_link"];
				$spacer["%order_no%"] = $_POST["order_no"];
				$recipient = $sales_header["user_email"];
			break;
            case 'order_mail_4':
                $query = "SELECT * FROM shop_sales_header WHERE company = '" . $_POST["company"] . "' AND order_no = '" . $_POST["order_no"] . "'";
                $sendToPaymentProvider = (bool)$_POST['send_to_payment_provider'];
                $textModuleCode = filter_input(INPUT_POST,'text_module_code',FILTER_SANITIZE_STRING);
                $paymentProviderAddress = filter_input(INPUT_POST,'payment_provider_address',FILTER_SANITIZE_EMAIL);

                $result = @mysqli_query($GLOBALS['mysql_con'],$query);
                if(mysqli_num_rows($result) == 1) {
                    $sales_header = @mysqli_fetch_assoc($result);
                } else {
                    echo '100';
                    die();
                }
                if ($textModuleCode) {
                    $text_module = $textModuleCode;
                } else {
                    $text_module = $language["email_order_4_text_module"];
                }

                $textModule = getPaymentOptionEmailText($sales_header, $pdo);
                if ($textModule != '') {
                    $text_module = $textModule;
                }

                $spacer["%invoice_no%"] = $_POST["invoice_no"];
                $spacer["%invoice_amount%"] = $_POST["amount"];
                $spacer["%order_no%"] = $_POST["order_no"];
                if ($paymentProviderAddress) {
                    $recipient = [$sales_header['user_email'],$paymentProviderAddress];
                } else {
                    $recipient = $sales_header["user_email"];
                }

                break;
			case 'invoice_copy':
				$text_module = $language["email_invoice_copy_text_module"];
				$spacer["%invoice_no%"] = $_POST["invoice_no"];
				$recipient = $_POST["request_mail"];
			break;
			case 'shipment_copy':
				$text_module = $language["email_shipment_copy_text_module"];
				$spacer["%shipment_no%"] = $_POST["shipment_no"];
				$recipient = $_POST["request_mail"];
			break;
            case 'cr_memo_copy':
                $text_module = $language['email_cr_memo_copy_text_module'];
                $spacer['%cr_memo_no%'] = $_POST['cr_memo_no'];
                $recipient = $_POST['request_mail'];
                break;
		}

		// Kunden auslesen um weitere Platzhaltertexte zu füllen
		$query = "SELECT * FROM shop_customer WHERE company = '" . $_POST["company"] . "' AND customer_no = '" . $_POST["customer_no"] . "'";
		$result = @mysqli_query($GLOBALS['mysql_con'],$query);
		if(@mysqli_num_rows($result) == 1) {
			$customer = @mysqli_fetch_assoc($result);
			$spacer["%customer_no%"] = $customer["customer_no"];
			$spacer["%customer_name%"] = $customer["name"];
		}


		// Nachricht und Betreff aus Textbausteinen auslesen
		$message = get_text_module($_POST["company"],$text_module,$spacer);
		$subject = get_text_module($_POST["company"],$text_module,$spacer,TRUE);

		// E-Mail Anhang prüfen
		if($_POST["attachment"] <> '') {
			if(file_exists(rtrim(dirname(dirname(__DIR__)),'/').$GLOBALS["shop_setup"]["uploaddir_attachements"] . $_POST["attachment"])) {
				$attachment = $GLOBALS["shop_setup"]["uploaddir_attachements"] . $_POST["attachment"];
			}
		}

		// Fehler vor dem versenden der E-Mail ausschließen
		$error = ($shop["email_sender"] == "") ? "\nAbsender '' ungültig" : "";
		$error .= ($recipient == "") ? "\nEmpfänger '' ungültig" : "";
		$error .= ($message == "") ? "\nNachricht '' ungültig" : "";
		$error .= ($subject == "") ? "\nBetreff '' ungültig" : "";
		if($error <> '') {
			//echo utf8_decode("Fehler in der Funktion 'send_customer_mail' \n" . $error . "\n\n");
            echo ("Fehler in der Funktion 'send_customer_mail' \n" . $error . "\n\n");
		} else {
		    if (is_string($recipient)) {
                // Mail versenden
                if (mail_create($subject, $message, $shop["email_sender"], $recipient, "", "", TRUE, $attachment, 0, '')) {
                    mail_send();
                    echo "100";
                } else {
                    //echo utf8_decode("Fehler in Funktion 'send_customer_mail'. Fehler beim versenden der E-Mail");
                    echo("Fehler in Funktion 'send_customer_mail'. Fehler beim versenden der E-Mail");
                }
            } elseif (is_array($recipient)) {
		        $failure = false;
		        foreach($recipient as $recip_addr) {
                    if (mail_create($subject, $message, $shop["email_sender"], $recip_addr, "", "", TRUE, $attachment, 0, '')) {
                        mail_send();
                    } else {
                        $failure = true;
                    }
                }
                if ($failure) {
                    echo("Fehler in Funktion 'send_customer_mail'. Fehler beim versenden der E-Mail");
                } else {
		            echo "100";
                }
            }
		}
	}
}

// E-Mail bei Fehler des NAS versenden
function send_mail_nas_error() {
	$shop_setup = get_shop_setup($_POST["company"]);
	$GLOBALS["shop_setup"] = $shop_setup;
	if(($shop_setup["reciever_nas_error"] <> '') && ($shop_setup["email_nas_error"] <> '')) {
		$spacer["%error%"] = $_POST["error_string"];
		$message = get_text_module($shop_setup["email_nas_error"],$spacer);
		$subject = get_text_module($shop_setup["email_nas_error"],$spacer,TRUE);
		mail_create($subject, $message, $shop_setup["sender_email"], $shop_setup["reciever_nas_error"], "", "", TRUE, 0, '');
        if($shop_setup["reciever_nas_error_2"] <> '') {
		  mail_create($subject, $message, $shop_setup["sender_email"], $shop_setup["reciever_nas_error_2"], "", "", TRUE, 0, '');
        }
		mail_send();
		echo "100";
	} else {
		echo utf8_decode("Fehler beim versenden der E-Mail, Überprüfen Sie die E-Mailadresse");
	}
}

// Buchungen oder Gutschriften an Paygate senden
function process_online_payment() {
	
	echo $GLOBALS["shop"]["computop_merchant_id"];
	echo $GLOBALS["shop"]["computop_password"];
	
	if($_POST['company'] <> '' && $_POST['shop_code'] <> '' && $_POST['language_code'] <> '' && $_POST['payment_line_no'] <> ''){
		require_once __DIR__ . DIRECTORY_SEPARATOR . '../../plugins/paygate/includes/function.inc.php';
		
		$GLOBALS["shop"] =  get_shop($_POST['company'],$_POST['shop_code']);
		
		$merchant_id = $GLOBALS["shop"]["computop_merchant_id"];
		$computop_password = $GLOBALS["shop"]["computop_password"];
		
		// Zahlungsart holen
		$query = "SELECT * FROM shop_payment_option WHERE company = '".$_POST["company"]."'
													AND shop_code = '".$_POST["shop_code"]."'
													AND language_code = '".$_POST["language_code"]."'
													AND line_no = '".$_POST["payment_line_no"]."'";
		$result = @mysqli_query($GLOBALS['mysql_con'],$query);
		if(@mysqli_num_rows($result)==1){
			$payment_line = @mysqli_fetch_assoc($result);
			
			// Allgemeine Parameter Festlegen
			$trans_id = "&TransID=".$_POST['transaction_id'];
			$pay_id = "&PayID=".$_POST['payment_id'];
			$currency = "&Currency=EUR";
			
			// Parameter für Kreditkarte & Paypal
			if($payment_line["checkout"]=='2' || $payment_line["checkout"]=='3' || $payment_line["checkout"]=='4' || $payment_line["checkout"] == '5'){
				if($_POST['amount'] > 0) {
					$amount = "&Amount=".round($_POST['amount']*100,0);
					$ref_nr = "&RefNr=".$_POST['reference_no'];
				} else {
					$amount = "&Amount=".round(abs($_POST['amount'])*100,0);
					$ref_nr = "&CredNo=".$_POST['reference_no'];
				}
				$currency = "&Currency=EUR";
				
				//$response="&Response=Encrypt";
				//unverschlüsselte GET-Parameter für Kreditkarte und PayPal
				$plaintext  = utf8_decode("MerchantID=".$merchant_id.$trans_id.$pay_id.$amount.$currency.$ref_nr);
				//Längen der GET-Parameter für Blowfisch-Verschlüsselung
				$len = strlen($plaintext);
				//Verschlüsseln mit Passwort von Paygate
				$BlowFish = new ctBlowfish;
				$Data = $BlowFish->ctEncrypt($plaintext, $len, $computop_password);
				if($_POST['amount'] > 0) {
					$html=file_get_contents('https://www.computop-paygate.com/capture.aspx?MerchantID='.$merchant_id.'&Len='.$len.'&Data='.$Data);
				} else {
					$html=file_get_contents('https://www.computop-paygate.com/credit.aspx?MerchantID='.$merchant_id.'&Len='.$len.'&Data='.$Data);
				}
			}

            // Parameter für Paydirekt
            if($payment_line["checkout"]=='10' || $payment_line["checkout"]=='11'){
                if($_POST['amount'] > 0) {
                    $amount = "&Amount=".round($_POST['amount']*100,0);
                    $ref_nr = "&RefNr=".$_POST['reference_no'];
                } else {
                    $amount = "&Amount=".round(abs($_POST['amount'])*100,0);
                    $ref_nr = "&CredNo=".$_POST['reference_no'] . "&RefNr=".$_POST['reference_no'];
                }
                $currency = "&Currency=EUR";

                //$response="&Response=Encrypt";
                //unverschlüsselte GET-Parameter für Kreditkarte und PayPal
                $plaintext  = utf8_decode("MerchantID=".$merchant_id.$trans_id.$pay_id.$amount.$currency.$ref_nr);
                //Längen der GET-Parameter für Blowfisch-Verschlüsselung
                $len = strlen($plaintext);
                //Verschlüsseln mit Passwort von Paygate
                $BlowFish = new ctBlowfish;
                $Data = $BlowFish->ctEncrypt($plaintext, $len, $computop_password);
                if($_POST['amount'] > 0) {
                    $html=file_get_contents('https://www.computop-paygate.com/capture.aspx?MerchantID='.$merchant_id.'&Len='.$len.'&Data='.$Data);
                } else {
                    $html=file_get_contents('https://www.computop-paygate.com/credit.aspx?MerchantID='.$merchant_id.'&Len='.$len.'&Data='.$Data);
                }
            }
			
			// Parameter für Billpay
			if($payment_line["checkout"]=='6' || $payment_line["checkout"] =='7'){
				if($_POST["amount"]>0){
					$amount = "&Amount=".number_format($_POST["amount"]*100,0,'','');
					$invoice_no = "&InvoiceNr=".$_POST["invoice_no"];	
					$orderdesc = "&OrderDesc=".$_POST["orderdesc"];
					$delay = "&Delay=".$_POST["delay"];
				}else{
					$amount = "&Amount=".number_format(abs($_POST["amount"])*100,0,'','');
					$netrebate = "&NetRebate=0"; // Rabatt auf Gesamtwert der ursprünglichen Bestellung Brutto
					$grrebate = "&GrRebate=0"; // Rabatt auf Gesamtwert der ursprünglichen Bestellung Netto
					$shrebate = "&shRebate=0"; // Reduzierung der Liefergebühren Netto
					$shrebategr = "&shRebateGr=0"; // Reduzierung der Liefergebühren Brutto
				}
				
				// evtl. itemlist und orderdesc mitgeben
				$itemlist = "&ArticleList=".$_POST["itemlist"];
				// Falls keine versandkosten, nachschauen, ob versandkosten im ursprungsauftrag
				$query = "SELECT * FROM shop_sales_header WHERE pay_id = '".$_POST['payment_id']."'";
				$result = @mysqli_query($GLOBALS['mysql_con'],$query);
				$row = @mysqli_fetch_array($result);
				$shipping_desc = explode(';',$orderdesc);
				
				$plaintext  = "MerchantID=".$merchant_id.$trans_id.$pay_id.$amount.$currency.$invoice_no.$delay.$itemlist.$orderdesc.$netrebate.$grrebate.$shrebate.$shrebategr;
				//Längen der GET-Parameter für Blowfisch-Verschlüsselung

				$len = strlen($plaintext);
				//Verschlüsseln mit Passwort von Paygate
				$BlowFish = new ctBlowfish;
				$Data = $BlowFish->ctEncrypt($plaintext, $len, $computop_password);
				if($_POST['amount'] > 0) {
					$html=file_get_contents('https://www.computop-paygate.com/capture.aspx?MerchantID='.$merchant_id.'&Len='.$len.'&Data='.$Data);
				} else {
					$html=file_get_contents('https://www.computop-paygate.com/reverse.aspx?MerchantID='.$merchant_id.'&Len='.$len.'&Data='.$Data);
				}
			}

            // Payolution ---
            // Nur bei Rechnungskauf, bei Ratenkauf erfolgt das Buchen automatisch (Quelle: Paygate Payolution Doku)
			if($payment_line["checkout"]=='17') {
                require_once __DIR__ . DIRECTORY_SEPARATOR . '../../plugins/paygate/payolution.inc.php';

                $amount = $_POST["amount"];

                if($amount > 0) {
                    $type = 0;
                } else {
                    $amount = abs($amount);
                    $type = 2;
                }

                if (isset($_POST["reverse"]) && !empty($_POST["reverse"])) {
                    $type = 1;
                }

                $shop = $GLOBALS["shop"];
                $pay_id = $_POST['payment_id'];
                $trans_id = $_POST['transaction_id'];
                $ref_nr = $_POST['reference_no'];

                $response = payolution_navconnect($type, $shop, $pay_id, $trans_id, $ref_nr, $amount, "EUR") ;

                if ($response["Status"] == "OK") {
                    echo '100';
                } elseif ($response["Status"] == "FAILED") {
                    if (array_key_exists('Description', $response)) {
                        echo $response['Description'];
                    } else {
                        echo 'Fehler ohne Fehlerbeschreibung';
                    }
                } else {
                    echo 'Falsches Antwortformat';
                }
                exit;
            }

            // Payolution +++
			
			
			// Antwort auswerten
			if($html == '') {
				echo("Verbindungsfehler");
			} else {
				$html_parts = explode('&',$html);
				if(count($html_parts) == 2) {
					$len_parts = explode('=',$html_parts[0]);
					$len = $len_parts[1];
					$data_parts = explode('=',$html_parts[1]);
					$data = $data_parts[1];
					$BlowFish = new ctBlowfish;
					$data_decrypted = $BlowFish->ctDecrypt($data, $len, $computop_password);
					$parts = explode('&',$data_decrypted);
					//Logfile
					$fp = fopen(rtrim(dirname(dirname(__DIR__)),'/\\') . "/userdata/logfile-navconnect-billpay.txt","a+b");
					if(count($parts) > 0) {
						$paramarray = array();
						foreach($parts AS $part) {
							$params = explode('=',$part);
							if(count($params) == 2){
								$paramarray[$params[0]] = $params[1];
								fwrite($fp,"\r\n".$params[0]."\r\n".$params[1]."\r\n");
							}
						}
						if(array_key_exists('Status', $paramarray)) {
							if($paramarray['Status'] == 'FAILED') {
								if(array_key_exists('Description', $paramarray)) {
									echo $paramarray['Description'];
								}else{
									echo 'Fehler ohne Fehlerbeschreibung';
								}
							}else{
								echo '100';
							}
						}else{
							echo 'Keine Statusmeldung erhalten';
						}
					}else{
						echo 'Falsches Antwortformat';
					}
					fclose($fp);
				}else{
					echo "Falsches Antwortformat";
				}
			}
			
		}else{
			echo "Zahlart nicht gefunden";	
		}
	}
}

// Geänderten Warenkorb an Billypay senden.
function change_order_online_payment() {
	// Itemlists wegen Typüberlauf aus Tabelle zusammenstellen +++
	/*
	if($_POST["itemlist_id"]<>''){
		$query = "SELECT itemlist FROM shop_payment_itemlist WHERE id = '".$_POST["itemlist_id"]."'";
		$result = @mysqli_query($GLOBALS['mysql_con'],$query);
		if(@mysqli_num_rows($result)==1){
			$row = @mysqli_fetch_assoc($result);
			$_POST["itemlist"] = $row["itemlist"];
		}
	}
	if($_POST["itemlist2_id"]<>''){ 
		$query = "SELECT itemlist FROM shop_payment_itemlist WHERE id = '".$_POST["itemlist2_id"]."'";
		$result = @mysqli_query($GLOBALS['mysql_con'],$query);
		if(@mysqli_num_rows($result)==1){
			$row = @mysqli_fetch_assoc($result);
			$_POST["itemlist2"] = $row["itemlist"];
		}
	}
	if($_POST["itemlist2"]<>''){
		$_POST["itemlist"] .= $_POST["itemlist2"]; 
	}
	// Itemlists wegen Typüberlauf aus Tabelle zusammenstellen ---
	*/
	
	if($_POST['payment_id'] <> '' && $_POST['transaction_id'] <> '' && $_POST['amount'] <> ''){
		require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'plugins/paygate/includes/function.inc.php';
		
		$merchant_id = $GLOBALS["shop"]["computop_merchant_id"];
		$computop_password = $GLOBALS["shop"]["computop_password"];
		
		// Allgemeine Parameter Festlegen
		$pay_id = "&PayID=".$_POST['payment_id'];
		$trans_id = "&TransID=".$_POST['transaction_id'];			
		//$amount = "&Amount=".number_format($_POST["amount"],0,'','');
		$amount = "&Amount=".number_format($_POST["amount"]*100,0,'','');
		
		// ArticleList und OrderDesc mitgeben
		$itemlist = "&ArticleList=".str_replace("\\","",$_POST["itemlist"]);
		//$itemlist = str_replace(";;",";'';",$itemlist);
		$itemlist = utf8_decode($itemlist);
		$orderdesc = "&OrderDesc=".$_POST["orderdesc"];
		
		// EventToken für ChangeOrder mitgeben
		$eventtoken = "&EventToken=CO";
		
		$plaintext_change  = utf8_decode("MerchantID=".$merchant_id.$trans_id.$pay_id.$amount.$itemlist.$orderdesc.$eventtoken);
		
		//Verschlüsseln mit Passwort von Paygate
		$len = strlen($plaintext_change);
		$BlowFish_change = new ctBlowfish;
		$Data = $BlowFish_change->ctEncrypt($plaintext_change, $len, $computop_password);
		
		// Logfile schreiben
		$fp = fopen(rtrim(dirname(dirname(__DIR__)),'/\\') . "/userdata/logfile-billpay-change-order.txt","a+b");
		fwrite($fp,"\r\n".$plaintext."\r\n".$itemlist."\r\n".$orderdesc."\r\n");
		
		// Change Order absenden
		$html=file_get_contents('https://www.computop-paygate.com/Billpay.aspx?MerchantID='.$merchant_id.'&Len='.$len.'&Data='.$Data);
					
		// Antwort auswerten
		if($html == '') {
			echo("Verbindungsfehler");
		} else {
			$html_parts = explode('&',$html);
			if(count($html_parts) == 2) {
				$len_parts = explode('=',$html_parts[0]);
				$len = $len_parts[1];
				$data_parts = explode('=',$html_parts[1]);
				$data = $data_parts[1];
				$BlowFish = new ctBlowfish;
				$data_decrypted = $BlowFish->ctDecrypt($data, $len, $computop_password);
				$parts = explode('&',$data_decrypted);
				if(count($parts) > 0) {
					$paramarray = array();
					foreach($parts AS $part) {
						$params = explode('=',$part);
						if(count($params) == 2){
							$paramarray[$params[0]] = $params[1];
							fwrite($fp,"\r\n".$params[0]."\r\n".$params[1]."\r\n");
						}
					}
					
					if(array_key_exists('Status', $paramarray)) {
						if($paramarray['Status'] == 'FAILED') {
							if(array_key_exists('Description', $paramarray)) {
								echo $paramarray['Description'];
							}else{
								echo 'Fehler ohne Fehlerbeschreibung';
							}
						}else{
							echo '100';
						}
					}else{
						echo 'Keine Statusmeldung erhalten';
					}
				}else{
					echo 'Falsches Antwortformat';
				}
			}else{
				echo "Falsches Antwortformat";
			}
		}	
		fclose($fp);		
	}
}

//Zugangsdaten an Vertreter versenden
function send_salesperson_login() {
//	$site = site_getbycode($_POST["main_site_code"]);
//	$GLOBALS["site"] = $site;
//	$shop_setup = get_shop_setup($GLOBALS["site"]);
//	$GLOBALS["shop_setup"] = $shop_setup;
	if($_POST["salesperson_code"]<>'') {
		$countquery = "SELECT * FROM shop_salesperson WHERE salesperson_code = '" . $_POST["salesperson_code"] . "' AND company = '".$_POST['company']."'";
		$result = @mysqli_query($GLOBALS['mysql_con'],$countquery);
		if(@mysqli_num_rows($result)==1) {
			$salesperson = @mysqli_fetch_array($result);
			$shop = get_shop($salesperson["company"],$salesperson["shop_code"]);
			$language = get_shop_language($salesperson["company"],$salesperson["shop_code"],$salesperson["language_code"]);
			$password = generate_password();
			$login = $salesperson["email"];
			$query = "UPDATE shop_salesperson SET password = '" . md5($password)  . "' WHERE id = '" . $salesperson["id"] . "'";
			if (@mysqli_query($GLOBALS['mysql_con'],$query)) {
				$spacer["%name%"] = $salesperson["name"];
				$spacer["%login%"] = $login;
				$spacer["%password%"] = $password;
				//TODO: richtiges Textmodul finden!!
				$message = get_text_module($language["company"],$language["email_login_text_module"],$spacer);
				$subject = get_text_module($language["company"],$language["email_login_text_module"],$spacer,TRUE);
				if(mail_create($subject, $message, $shop["email_sender"], $salesperson["email"], "", "", TRUE, 0, '')) {
					mail_send();
					echo "100";
				} else {
					echo utf8_decode("Fehler beim versenden der E-Mail, überprüfen Sie die E-Mailadresse");
				}
			}
		}
	}
}

//Webshop-Artikelnummer ändern
function rename_item()
{
	//Verbindung zu Info-Datenbank
	$infodb = mysqli_connect($GLOBALS["myservername"],$GLOBALS["mylogin"],$GLOBALS["mypass"]) or die ("Keine Verbindung hergestellt!");
	mysqli_select_db($infodb,"information_schema") or die ("Keine Verbindung hergestellt!");
	@mysqli_query($infodb,'set character set utf8;',$infodb);
	$query = "SELECT TABLE_NAME AS 'table', COLUMN_NAME AS 'column'
			  FROM COLUMNS
			  WHERE TABLE_SCHEMA='" . $GLOBALS["mydb"] . "'
			  	AND COLUMN_NAME like '%item_no%'";
	$result = mysqli_query($infodb,$query);
	if(mysqli_num_rows($result) > 0)
	{
		//Verbindung zu Shop-Datenbank
        $GLOBALS['mysql_con'] = mysqli_connect($GLOBALS["myservername"],$GLOBALS["mylogin"],$GLOBALS["mypass"]) or die ("Keine Verbindung hergestellt!");
		mysqli_select_db($GLOBALS['mysql_con'],$GLOBALS["mydb"]) or die ("Keine Verbindung hergestellt!");
		@mysqli_query($GLOBALS['mysql_con'],'set character set utf8;');
		while($row = mysqli_fetch_assoc($result))
		{
			//Alle Tabellen Updaten
			$query = "UPDATE ".$row['table']."
					  SET ".$row['column']."='".$_POST['new_item_no']."'
					  WHERE ".$row['column']."='".$_POST['item_no']."'";
			mysqli_query($GLOBALS['mysql_con'],$query);
		}
	}
	echo "100";
}

function get_no_of_new_customer()
{
	echo(mysqli_num_rows(mysqli_query($GLOBALS['mysql_con'],"SELECT id FROM shop_customer WHERE customer_no LIKE '".$_POST['customer_no']."' AND company='".$_POST['company']."'")));
}

function get_new_customer()
{
	$delimiter = $_POST['delimiter'];
	$query = "SELECT * FROM shop_customer WHERE customer_no LIKE '".$_POST['customer_no']."' AND company='".$_POST['company']."' LIMIT 1";
	$result = mysqli_query($GLOBALS['mysql_con'],$query);
	if(mysqli_num_rows($result) >0)
	{
		$customer = mysqli_fetch_assoc($result);
		$text = $customer['id'] . $delimiter;
		$text .= $customer['shop_code'] . $delimiter;
		$text .= $customer['language_code'] . $delimiter;
		$text .= $customer['customer_no'] . $delimiter;
		$text .= $customer['name'] . $delimiter;
		$text .= $customer['address'] . $delimiter;
		$text .= $customer['post_code'] . $delimiter;
		$text .= $customer['city'] . $delimiter;
		$text .= $customer['country'] . $delimiter;
		$text .= $customer['phone_no'] . $delimiter;
		$text .= $customer['email'] . $delimiter;
		$text .= $customer['currency_code'];
		echo utf8_decode($text);
	}
}

//Newsletteranmeldungen übergeben
function transfer_newsletter_recipients(){
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
    if ($newsletter_version == 1) {
        transfer_newsletter_recipients_copernica();
    } else {
        transfer_newsletter_recipients_cleverreach();
    }
}

function transfer_newsletter_recipients_copernica(){
    /*
	$GLOBALS["curr_logfile"] = __DIR__ .'/copernica_log2-navconnect.txt';
	$GLOBALS['copernica_log_active'] = TRUE;
	$GLOBALS['log_to_file'] = TRUE;
	*/

/// AH 2013.12.12 /// Newsletter Copernica API Funktionen!
    require_once ("../../module/dcshop/newsletter_copernica/api_functions.inc.php");




    // Alle Bestellungen mit to_transfer in $sales_header holen
    // Alle SalesLines in $sales_line holen
    // für jede SalesLine die Kategorie holen

    $query_sales_header = "SELECT * FROM shop_sales_header WHERE to_nl_transfer = 1";
    $result_sales_header = mysqli_query($GLOBALS['mysql_con'],$query_sales_header);
    if (mysqli_num_rows($result_sales_header) > 0) {
        mysqli_data_seek($result_sales_header,0);
        unset($sales_header);
        while ($sales_header = mysqli_fetch_assoc($result_sales_header)) {

            $shop = get_shop($sales_header["company"],$sales_header["shop_code"]);
            $db_id = $shop['copernica_db_id'];
            $access_token = $shop['copernica_access_token'];
            if(empty($db_id) || empty($access_token)) {
                //FEHLER
                echo "Fehler in Funktion 'transfer_newsletter_recipients' - keine Anmeldedaten hinterlegt";
            }

            $language_code = $sales_header['language_code'];
            $text = "Current Sales Header: ".print_r($sales_header,1);
            if($GLOBALS['output_log']){
                echo "<!-- LOG COPERNICA: 
				$text 
				-->";
            }
            if(($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']){
                log_text($text);
            }
            unset($newsletter_abo);

            $default_segment = $shop['default_copernica_segment'];

            $category_shop_code = (empty($shop['use_categorys_from_shop_code']) ? $shop['code'] : $shop['use_categorys_from_shop_code']);
            $item_shop_code = (empty($shop['use_items_from_shop_code']) ? $shop['code'] : $shop['use_items_from_shop_code']);

            $query_sales_line = "SELECT * FROM shop_sales_line WHERE order_no = ".$sales_header["order_no"];
            $text = "Getting Sales Lines: ".$query_sales_line;
            if($GLOBALS['output_log']){
                echo "<!-- LOG COPERNICA: 
				$text 
				-->";
            }
            if(($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']){
                log_text($text);
            }

            $result_sales_line = mysqli_query($GLOBALS['mysql_con'],$query_sales_line);
            if (mysqli_num_rows($result_sales_line) > 0) {
                $text = "Sales Lines found: ".mysqli_num_rows($result_sales_line);
                if($GLOBALS['output_log']){
                    echo "<!-- LOG COPERNICA: 
					$text 
					-->";
                }
                if(($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']){
                    log_text($text);
                }
                if ($sales_header["birthday"] == '0000-00-00') {
                    $sales_header["birthday"] = '';
                }
                mysqli_data_seek($result_sales_line,0);
                unset($basket_line);
                while($basket_line = mysqli_fetch_assoc($result_sales_line)) {
                    $text = "Current Sales Line: ".print_r($basket_line,1);
                    if($GLOBALS['output_log']){
                        echo "<!-- LOG COPERNICA: 
						$text 
						-->";
                    }
                    if(($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']){
                        log_text($text);
                    }
                    $discontinue = FALSE;
                    /// Newsletter Sammlungseintrag (Artikel)
                    $item_cat_query = " SELECT * FROM shop_item
										INNER JOIN shop_category ON 
											shop_category.company = shop_item.company 
										  AND 
											shop_category.shop_code = '".$category_shop_code."'											
										  AND 
											shop_category.language_code = shop_item.language_code
										  AND
											shop_item.main_category_line_no = shop_category.line_no
										WHERE
											shop_item.id = '".$basket_line["shop_item_id"]."'
											
											AND shop_item.main_category_line_no != '' 
										LIMIT 1";
                    $text = "Looking for Category - Query: ".$item_cat_query;
                    if($GLOBALS['output_log']){
                        echo "<!-- LOG COPERNICA: 
						$text 
						-->";
                    }
                    if(($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']){
                        log_text($text);
                    }
                    $item_cat_result = mysqli_query($GLOBALS['mysql_con'],$item_cat_query);
                    // PH 16.12.2013: Erweiterung, falls keine Hauptkategorie verknüpft ist +++
                    if (mysqli_num_rows($item_cat_result) > 0) {
                        $item_cat = mysqli_fetch_assoc($item_cat_result);
                        $text = "Found: ".print_r($item_cat,1);
                        if($GLOBALS['output_log']){
                            echo "<!-- LOG COPERNICA: 
							$text 
							-->";
                        }
                        if(($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']){
                            log_text($text);
                        }

                        $item_arr = new_array_keys($basket_line + $item_cat, $GLOBALS['newsletter_keys'], TRUE);
                        $item_arr['Bestellnr'] = $order_no;

                        $newsletter_abo['item'][] = array2values($item_arr);
                        unset($item_arr);

                    } else {
                        $item_cat_query = " SELECT * FROM shop_item
										JOIN shop_item_has_category ON 
											shop_item_has_category.company = shop_item.company 
										  AND 
											shop_item_has_category.shop_code = '".$item_shop_code."'
										  AND
											shop_item_has_category.category_shop_code = '".$category_shop_code."'										
										  AND 
											shop_item_has_category.language_code = shop_item.language_code										  
										  AND 
											shop_item_has_category.item_no='".$basket_line['item_no']."'
										JOIN shop_category ON 
											shop_category.company = shop_item.company 
										  AND 
											shop_category.shop_code = '".$category_shop_code."'
										  AND 
											shop_category.language_code = shop_item.language_code
										  AND
											shop_item_has_category.category_line_no = shop_category.line_no
										WHERE
											shop_item.id = '".$basket_line["shop_item_id"]."'
										ORDER BY shop_item_has_category.id ASC
											LIMIT 1";
                        $text = "Not found - Looking for any category-association: ".$item_cat_query;
                        if($GLOBALS['output_log']){
                            echo "<!-- LOG COPERNICA: 
							$text 
							-->";
                        }
                        if(($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']){
                            log_text($text);
                        }
                        $item_cat_result = mysqli_query($GLOBALS['mysql_con'],$item_cat_query);
                        if (mysqli_num_rows($item_cat_result) > 0) {
                            $item_cat = mysqli_fetch_assoc($item_cat_result);
                            $text = "Found: ".print_r($item_cat,1);
                            if($GLOBALS['output_log']){
                                echo "<!-- LOG COPERNICA: 
								$text 
								-->";
                            }
                            if(($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']){
                                log_text($text);
                            }

                            $item_arr = new_array_keys($basket_line + $item_cat, $GLOBALS['newsletter_keys'], TRUE);
                            $item_arr['Bestellnr'] = $sales_header["order_no"];

                            $newsletter_abo['item'][] = array2values($item_arr);
                            unset($item_arr);
                        } else {
                            $text = "Not found - Fallback auf Item";
                            if($GLOBALS['output_log']){
                                echo "<!-- LOG COPERNICA: 
								$text 
								-->";
                            }
                            if(($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']){
                                log_text($text);
                            }

                            //Fallback ohne Kategorie-Daten, kein Abbruch!
                            $item_query = "SELECT * FROM shop_item 
							WHERE 
								id='".$basket_line['shop_item_id']."'
							LIMIT 1
							";
                            $item_result = mysqli_query($GLOBALS['mysql_con'],$item_query);
                            if(!(mysqli_num_rows($item_result) > 0)) {
                                $text = "Not found - Discontinuing";
                                if($GLOBALS['output_log']){
                                    echo "<!-- LOG COPERNICA: 
									$text 
									-->";
                                }
                                if(($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']){
                                    log_text($text);
                                }
                                $discontinue = TRUE;
                            } else {
                                mysqli_data_seek($item_result,0);
                                $item = mysqli_fetch_assoc($item_result);
                                $item_arr = new_array_keys($basket_line + $item, $GLOBALS['newsletter_keys'], TRUE);
                                $item_arr['Bestellnr'] = $sales_header['order_no'];
                                $newsletter_abo['item'][] = array2values($item_arr);
                            }
                        }
                    }
                    // ---
                }
            }

            if ($discontinue == FALSE) {
                /// AH 2013.12.13 - Newsletter Eintrag (Vorbereitung)
                $newsletter_abo['profile_allfields'] = new_array_keys($sales_header, $GLOBALS['newsletter_keys'], TRUE);
                $text = "Profile after 'new_array_keys': ".print_r($newsletter_abo['profile_allfields'],1);
                if($GLOBALS['output_log']){
                    echo "<!-- LOG COPERNICA: 
					$text 
					-->";
                }
                if(($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']){
                    log_text($text);
                }

                $newsletter_abo['profile'] = array2values($newsletter_abo['profile_allfields']);
                $text = "Profile after 'array2values': ".print_r($newsletter_abo['profile'],1);
                if($GLOBALS['output_log']){
                    echo "<!-- LOG COPERNICA: 
					$text 
					-->";
                }
                if(($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']){
                    log_text($text);
                }
                $newsletter_abo['profile']['SOI'] 	 = 1;
                if ($newsletter_abo['profile']['OOI'] == 1) {
                    // OOI  1 == akzeptiert und setzt somit den evtl. DOI 2 (abgemeldet) wieder auf 0
                    $newsletter_abo['profile']['DOI'] 	 = 0;
                } else {
                    // OOI !1 == nicht aktzeptiert, wird daher mit 2 markiert (0 = man. Anmeldung, 1 = Bestellung (akzeptiert), 2 = Bestellung (nicht akzeptiert)
                    $newsletter_abo['profile']['OOI'] = 2;
                    //SH: 23.01.14 SOI auf 0, wenn Newsletter nicht akzeptiert
                    $newsletter_abo['profile']['SOI'] 	 = 0;
                }
                $text = "Profile after opt-in-assignment: ".print_r($newsletter_abo['profile'],1);
                if($GLOBALS['output_log']){
                    echo "<!-- LOG COPERNICA: 
					$text 
					-->";
                }
                if(($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']){
                    log_text($text);
                }
                $newsletter_abo['profile']['DatumAnmeldung'] = date("Y-m-d H:i:s");
                $newsletter_abo['profile']['Quelle'] = ($newsletter_abo['profile']['OOI'] == 1) ? 'Bestellung (akzeptiert)' : 'Bestellung (nicht akzeptiert)';

                $newsletter_abo['profile']['Segment'] = $default_segment;
                $nl_coupon = get_nl_coupon($sales_header);
                $newsletter_abo['profile']['CouponCode'] = $nl_coupon;
                $newsletter_abo['order']  = array(
                    'Bestellnr' 	 => $sales_header["order_no"],
                    'Warenwert'	 => $sales_header['subtotal'],
                    'Gesamtbetrag' => $sales_header["total"],
                    'Waehrungscode' => $sales_header['currency_code'],
                    'Datum'  		 => date("Y-m-d")
                );
                $login['db_id'] = $db_id;
                $login['access_token'] = $access_token;
                copernica_api($login, array("add_update_profile","add_collection"), $newsletter_abo);
                /// AH --- ENDE ---
            }

            // SalesHeader to_transfer auf False
            $sales_header_update_query = "UPDATE shop_sales_header SET to_nl_transfer = 0 WHERE id = ".$sales_header["id"];
            mysqli_query($GLOBALS['mysql_con'],$sales_header_update_query);
        }
    }
    echo "100";
}

function transfer_newsletter_recipients_cleverreach(){

    $query_sales_header = "SELECT * FROM shop_sales_header WHERE to_nl_transfer = 1 ORDER BY shop_code, language_code, order_no";
    $result_sales_header = mysqli_query($GLOBALS['mysql_con'],$query_sales_header);
    $receivers = array();
    $doiMailSend = array();
    if (mysqli_num_rows($result_sales_header) > 0) {
        $first = true;
        while ($sales_header = mysqli_fetch_assoc($result_sales_header)) {
            $shop = get_shop($sales_header["company"],$sales_header["shop_code"]);
            $shopLanguage = get_shop_language($sales_header["company"],$sales_header["shop_code"],$sales_header["language_code"]);
            $receiver = array();
            if (!(empty($shop['newsletter_group']) || empty($shop['newsletter_account']) || empty($shop['newsletter_login']) || empty($shop['newsletter_password']))) {

                if (!$first) {
                    if ($shop["code"] !== $lastShopCode || $shopLanguage["code"] !== $lastShopLanguageCode || count($receivers) === 1000) {
                        /** @var DynCom\dc\common\classes\CleverReachConnector $cleverReachConnector */
                        $cleverReachConnector = new DynCom\dc\common\classes\CleverReachConnector($account,$login,$password,$group);
                        $cleverReachConnector->upsertReceivers($receivers);
                        $receivers = array();
                    }
                }

                $account    =   $shop['newsletter_account'];
                $login      =   $shop['newsletter_login'];
                $password   =   $shop['newsletter_password'];
                $group      =   $shop['newsletter_group'];

                $language_code = $sales_header['language_code'];
                $default_segment = $shop['newsletter_segment'];
                $currency = "EUR";
                if ($shopLanguage["default_currency_code"] !== "") {
                    $currency = $shopLanguage["default_currency_code"];
                }

                $sendDoiMail = (bool)$sales_header["newsletter_registration"];
                if (array_key_exists($sales_header['user_email'],$doiMailSend)) {
                    $sendDoiMail = false;
                }

                if ($sales_header["newsletter_registration"]) {
                    $doiMailSend[$sales_header['user_email']] = true;
                }

                $receiver = array(
                    "email"                         =>      $sales_header['user_email'],
                    "activated"                     =>      0,
                    "deactivated"                   =>      0,
                    "registered"                    =>      time(),
                    "source"                        =>      $default_segment,
                    "send_doi_mail"                 =>      (bool)$sendDoiMail,
                    "global_attributes"	            =>      array(
                        "languagecode"              =>      array(
                            "value"                 =>      $language_code,
                            "type"                  =>      'text',
                            "description"           =>      'Sprachcode',
                        ),
                        "salutation"              =>      array(
                            "value"                 =>      $sales_header["salutation_title"],
                            "type"                  =>      'text',
                            "description"           =>      'Anrede',
                        ),
                        "name"              =>      array(
                            "value"                 =>      $sales_header["bill_to_name"],
                            "type"                  =>      'text',
                            "description"           =>      'Name',
                        ),
                        "first_name"              =>      array(
                            "value"                 =>      $sales_header["sur_name"],
                            "type"                  =>      'text',
                            "description"           =>      'Vorname',
                        ),
                        "last_name"              =>      array(
                            "value"                 =>      $sales_header["last_name"],
                            "type"                  =>      'text',
                            "description"           =>      'Nachname',
                        ),
                        "post_code"              =>      array(
                            "value"                 =>      $sales_header["bill_to_post_code"],
                            "type"                  =>      'text',
                            "description"           =>      'PLZ',
                        ),
                        "city"              =>      array(
                            "value"                 =>      $sales_header["bill_to_city"],
                            "type"                  =>      'text',
                            "description"           =>      'Ort',
                        ),
                    ),
                    "attributes"	            =>      array(
                        "segment"                   =>      array(
                            "value"                 =>      $default_segment,
                            "type"                  =>      'text',
                            "description"           =>      'Segment',
                        ),
                    )
                );

                $query_sales_line = "SELECT * FROM shop_sales_line WHERE order_no = ".$sales_header["order_no"];
                $result_sales_line = mysqli_query($GLOBALS['mysql_con'],$query_sales_line);
                if (mysqli_num_rows($result_sales_line) > 0) {
                    $orders = array();
                    $dtime = DateTime::createFromFormat("Y-m-d", $sales_header["order_date"]);
                    $orderTimeStamp = $dtime->getTimestamp();
                    while($sales_line = mysqli_fetch_assoc($result_sales_line)) {
                        $orders[] = array(
                            "order_id"      => $sales_header["order_no"],
                            "product"       => $sales_line["description"],
                            "product_id"    => $sales_line["item_no"],
                            "stamp"         => $orderTimeStamp,
                            "price"         => $sales_line["line_amount"],
                            "quantity"      => $sales_line["quantity"],
                            "source"        => $shop["code"],
                            "currency"      => $currency
                        );
                    }
                    $receiver['orders'] = $orders;
                }

                $receivers[] = $receiver;

                $lastShopCode = $shop["code"];
                $lastShopLanguageCode = $shopLanguage["code"];
                $first = false;
                $sales_header_update_query = "UPDATE shop_sales_header SET to_nl_transfer = 0 WHERE id = ".$sales_header["id"];
                mysqli_query($GLOBALS['mysql_con'],$sales_header_update_query);
            }
        }
        if (count($receivers) > 0 && !empty($account) && !empty($login) && !empty($password) && !empty($group)) {
            /** @var DynCom\dc\common\classes\CleverReachConnector $cleverReachConnector */
            $cleverReachConnector = new DynCom\dc\common\classes\CleverReachConnector($account,$login,$password,$group);
            $cleverReachConnector->upsertReceivers($receivers);
            $receivers = array();
        }
    }

    echo "100";
}


function google_merchant_center()
{
    // Parameter für den Aufruf der Schnittstelle e.g. http://localhost/module/dcshop/google_shopping.php?company=JTR%204%20DC&shop_code=B2C&country=DE
    $_config['shop']['company'] = $_REQUEST['company']; // $_GET Parameter
    $_config['shop']['shop_code'] = $_REQUEST['shop_code']; // $_GET Parameter
    $_config['shop']['item_source'] = $_REQUEST['item_source']; // $_GET Parameter
    $_config['shop']['category_source'] = $_REQUEST['category_source']; // $_GET Parameter
    $_config['country'] = "DE"; // $_GET Parameter

    // Parameter für den Google Merchant Center
    $query = "
		SELECT g_ftp_url,g_ftp_user,g_ftp_passwd,g_channel_title,g_channel_link,g_channel_desc,g_product_category
		FROM  shop_shop
		WHERE company = '".$_config['shop']['company']."' AND code = '".$_config['shop']['shop_code']."'";

    $result = @mysqli_query($GLOBALS['mysql_con'],$query);
    if (@mysqli_num_rows($result) == 1) {
        $_google_merchant = @mysqli_fetch_array($result);

        $_config['FTP']['host']     = $_google_merchant['g_ftp_url']; // shop_shop -> g_ftp_url
        $_config['FTP']['user']     = $_google_merchant['g_ftp_user']; // shop_shop -> g_ftp_user
        $_config['FTP']['password'] = $_google_merchant['g_ftp_passwd']; // shop_shop -> g_ftp_passwd

        $_config['channel']['title'] = $_google_merchant['g_channel_title']; // shop_shop -> g_channel_title
        $_config['channel']['link'] = $_google_merchant['g_channel_link']; // shop_shop -> g_channel_link
        $_config['channel']['description'] = $_google_merchant['g_channel_desc']; // shop_shop -> g_channel_desc
        $_config['channel']['product_category'] = $_google_merchant['g_product_category']; // shop_shop -> g_product_category

        $query = "SELECT * FROM main_language 
            WHERE company = '" . $_config['shop']['company'] . "' 
            AND shop_code = '" . $_config['shop']['shop_code'] . "' 
            AND shop_language_code = 'DEU' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'],$query);
        if (@mysqli_num_rows($result) == 1) {
            $mainLanguage = @mysqli_fetch_array($result);
            $query = "SELECT * FROM main_site 
                WHERE id = '" . $mainLanguage["main_site_id"] . "' LIMIT 1";
            $result = @mysqli_query($GLOBALS['mysql_con'],$query);
            if (@mysqli_num_rows($result) == 1) {
                $mainSite = @mysqli_fetch_array($result);
                $_config['site'] = $mainSite;
                $_config['language'] = $mainLanguage;

                $response = google_shopping_export($_config);
                if(stristr($response,"SUCCESS:"))
                {
                    echo "100";
                }
                else
                {
                    print_r($response);
                }
            }
        }
    }else{
        print_r('ERROR:Kein Google Account verfügbar');
    }

}

//SH: Digitaler Gutschein
function delete_dc_file() {
	$countquery = "SELECT * FROM shop_language WHERE company = '" . $_POST["company"] . "' AND shop_code='".$_POST["shop_code"]."' AND code = '" . $_POST["code"] . "'";
	
	$result = @mysqli_query($GLOBALS['mysql_con'],$countquery);
	if(@mysqli_num_rows($result)==1) {
		$file = @mysqli_fetch_array($result);
		if($file["digital_coupon_background_".$_POST["No"].""]<>'') {
			foreach($GLOBALS["shop_setup"]["dc_image_config"] as $image) {
				$filename = "../.." . $image["path"] . $file["digital_coupon_background_".$_POST["No"].""];
				if(file_exists($filename)) {
					unlink($filename);
				}
			}
		}
		$query = "UPDATE shop_language SET digital_coupon_background_".$_POST["No"]." = '' WHERE id = '" . $file["id"] . "'";
		if(!@mysqli_query($GLOBALS['mysql_con'],$query)) {
			echo "Fehler in Funktion 'delete_item_file' \nquery: " . $query . " \n";
		}
	}
	echo "100";
}

function delete_document() {
	if(strlen($_POST['document_no']) > 0 && (($_POST['header_table'] == 'shop_sales_invoice_header' && $_POST['lines_table'] == 'shop_sales_invoice_line')|| ($_POST['header_table'] == 'shop_sales_shipment_header' && $_POST['lines_table'] == 'shop_sales_shipment_line')|| ($_POST['header_table'] == 'shop_nav_sales_header' && $_POST['lines_table'] == 'shop_nav_sales_line'))) {
		$lines_query = "DELETE FROM `".$_POST['lines_table']."` WHERE document_no = '".$_POST['document_no']."'";
		if(mysqli_query($GLOBALS['mysql_con'],$lines_query)) {
			$header_query = "DELETE FROM `".$_POST['header_table']."` WHERE no = '".$_POST['document_no']."'";
			mysqli_query($GLOBALS['mysql_con'],$header_query);
		}
	}
}

function upsert_newsletter_profile() {
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

    if ($newsletter_version == 1) {
        upsert_copernica_profile();
    } else {
        upsert_cleverreach_profile();
    }
}

function upsert_cleverreach_profile() {

    if(empty($_POST['newsletter_account']) || empty($_POST['newsletter_login']) || empty($_POST['newsletter_password']) || empty($_POST['newsletter_group'])) {
        //FEHLER
        die("Fehler in Funktion 'upsert_newsletter_profile' - es konnten keine Newsletter-Daten aus shop_setup bezogen werden");
    }
    if(empty($_POST['EMail'])){
        //FEHLER
        die("Fehler in Funktion 'upsert_newsletter_profile' - E-Mail nicht gefüllt");
    }

    $first_name = $_POST['Vorname'];
    $last_name = $_POST['Nachname'];

    if(empty($_POST['Nachname'])) {
        if(!empty($_POST['Name'])) {
            $name_arr = split_name($_POST['Name']);
            $first_name = $name_arr['first'];
            $last_name = $name_arr['last'];
        } else {
            //FEHLER
            die("Fehler in Funktion 'upsert_newsletter_profile' - kein Nachname angegeben.\r\n".print_r($_POST,1));
        }
    }

    if(empty($_POST['Name']) && !empty($first_name) && !empty($last_name)) {
        $_POST['Name'] = $first_name . ' ' . $last_name;
    }

    $receivers[0] = array(
        "email"                         =>      (string)$_POST['EMail'],
        "activated"                     =>      0,
        "deactivated"                   =>      0,
        "registered"                    =>      time(),
        "source"                        =>      (string)$_POST['Quelle'],
        "send_doi_mail"                 =>      false,
        "global_attributes"	            =>      array(
            "languagecode"              =>      array(
                "value"                 =>      (string)$_POST['Sprachcode'],
                "type"                  =>      'text',
                "description"           =>      'Sprachcode',
            ),
            "salutation"              =>      array(
                "value"                 =>      (string)$_POST['Anrede'],
                "type"                  =>      'text',
                "description"           =>      'Anrede',
            ),
            "name"              =>      array(
                "value"                 =>      (string)$_POST['Name'],
                "type"                  =>      'text',
                "description"           =>      'Name',
            ),
            "first_name"              =>      array(
                "value"                 =>      (string)$first_name,
                "type"                  =>      'text',
                "description"           =>      'Vorname',
            ),
            "last_name"              =>      array(
                "value"                 =>      (string)$last_name,
                "type"                  =>      'text',
                "description"           =>      'Nachname',
            ),
            "post_code"              =>      array(
                "value"                 =>      (string)$_POST['PLZ'],
                "type"                  =>      'text',
                "description"           =>      'PLZ',
            ),
            "city"              =>      array(
                "value"                 =>      (string)$_POST['Ort'],
                "type"                  =>      'text',
                "description"           =>      'Ort',
            ),
        ),
        "attributes"	            =>      array(
            "segment"                   =>      array(
                "value"                 =>      (string)$_POST['Segment'],
                "type"                  =>      'text',
                "description"           =>      'Segment',
            ),
        )

    );

    /** @var DynCom\dc\common\classes\CleverReachConnector $cleverReachConnector */
    $cleverReachConnector = new DynCom\dc\common\classes\CleverReachConnector($_POST['newsletter_account'],$_POST['newsletter_login'],$_POST['newsletter_password'],$_POST['newsletter_group']);
    if (!$cleverReachConnector->upsertReceivers($receivers) || $cleverReachConnector->isError()) {
        echo "100";
    } else {
        echo "100";
    }
}

function upsert_copernica_profile() {

    $GLOBALS["curr_logfile"] = __DIR__ .'/copernica_log2.txt';
    $text = print_r($_POST,1);
    log_text($text);

    if(empty($_POST['copernica_db_id']) || empty($_POST['copernica_access_token'])) {
        //FEHLER
        die("Fehler in Funktion 'upsert_copernica_profile' - es konnten keine Copernica-Daten aus shop_setup bezogen werden");
    }
    if(empty($_POST['EMail'])){
        //FEHLER
        die("Fehler in Funktion 'upsert_copernica_profile' - E-Mail nicht gefüllt");
    }

    $first_name = $_POST['Vorname'];
    $last_name = $_POST['Nachname'];

    if(empty($_POST['Nachname'])) {
        if(!empty($_POST['Name'])) {
            $name_arr = split_name($_POST['Name']);
            $first_name = $name_arr['first'];
            $last_name = $name_arr['last'];
        } else {
            //FEHLER
            die("Fehler in Funktion 'upsert_copernica_profile' - kein Nachname angegeben.\r\n".print_r($_POST,1));
        }
    }

    if(empty($_POST['Name']) && !empty($first_name) && !empty($last_name)) {
        $_POST['Name'] = $first_name . ' ' . $last_name;
    }


    $profile = array(
        'Segment' => (string)$_POST['Segment'],
        'Anrede_formell' => (string)$_POST['Anrede_formell'],
        'Anrede_informell' => (string)$_POST['Anrede_informell'],
        'Anrede' => (string)$_POST['Anrede'],
        'Vorname' => (string)$first_name,
        'Nachname' => (string)$last_name,
        'EMail' => (string)$_POST['EMail'],
        'Unternehmen' => (string)$_POST['Unternehmen'],
        'PLZ' => (string)$_POST['PLZ'],
        'Ort' => (string)$_POST['Ort'],
        'Sprachcode' => (string)$_POST['Sprachcode'],
        'Quelle' => (string)$_POST['Quelle']
    );
    require_once ("/newsletter_copernica/copernica_rest_api.php");
    $feedback = upsert_profile((int)$_POST['copernica_db_id'],$_POST['copernica_access_token'],$profile);
    if(!empty($feedback)){
        echo "100";
    } else {
        echo "100";
    }
}

// Verkäuferbild entfernen
function delete_salesperson_image() {
    $countquery = "SELECT id, image FROM shop_salesperson WHERE company = '" . $_POST["company"] . "' AND salesperson_code = '" . $_POST["code"] . "'";
    $result = @mysqli_query($GLOBALS['mysql_con'],$countquery);
    if(@mysqli_num_rows($result)==1) {
        $salesperson = @mysqli_fetch_array($result);
        $query = "UPDATE shop_salesperson SET image = '' WHERE id = " . $salesperson["id"];
        $filename = "../.." . $GLOBALS["shop_setup"]["uploaddir_salesperson_images"] . $salesperson["image"];
        if(file_exists($filename)) {
            @unlink($filename);
        }

        if (!@mysqli_query($GLOBALS['mysql_con'],$query)) {
            echo "Fehler in Funktion 'delete_salesperson_image' \nquery: " . $query . " \n";
        }
        echo "100";
    } else {
        echo "Fehler in Funktion 'delete_salesperson_image' \nquery: " . $countquery . " \n";
    }
}

// Textmodul Anahng entfernen
function delete_textmodule_attachment() {

    $company = filter_var($_POST['company'],FILTER_SANITIZE_STRING);
    $code = filter_var($_POST['code'],FILTER_SANITIZE_STRING);
    $no = filter_var($_POST['no'],FILTER_SANITIZE_NUMBER_INT);

    if (isset($company) && isset($code) && isset($no)) {
        $countquery = "SELECT id, attachment_".$no." FROM shop_text_module WHERE company = '" . $company . "' AND code = '" . $code . "'";
        $result = @mysqli_query($GLOBALS['mysql_con'],$countquery);
        if(@mysqli_num_rows($result)==1) {
            $textmodule = @mysqli_fetch_array($result);
            $query = "UPDATE shop_text_module SET attachment_".$no." = '' WHERE id = " . $textmodule["id"];
            $filename = "../.." . $GLOBALS["shop_setup"]["uploaddir_documents"] . $textmodule["attachment_".$no];
            if(file_exists($filename)) {
                @unlink($filename);
            }

            if (!@mysqli_query($GLOBALS['mysql_con'],$query)) {
                echo "Fehler in Funktion 'delete_textmodule_attachment' \nquery: " . $query . " \n";
            }
            echo "100";
        } else {
            echo "Fehler in Funktion 'delete_textmodule_attachment' \nquery: " . $countquery . " \n";
        }
    } else {
        echo "Fehler in Funktion 'delete_textmodule_attachment'\n";
    }
}

function delete_shipping_payment_image_description() {

    $company = filter_var($_REQUEST["company"],FILTER_SANITIZE_STRING);
    $shopCode = filter_var($_REQUEST["shop_code"],FILTER_SANITIZE_STRING);
    $languageCode = filter_var($_REQUEST["language_code"],FILTER_SANITIZE_STRING);
    $lineNo = filter_var($_REQUEST["line_no"],FILTER_SANITIZE_NUMBER_INT);
    $table  = filter_var($_REQUEST["table"],FILTER_SANITIZE_STRING);
    $field  = filter_var($_REQUEST["field"],FILTER_SANITIZE_STRING);

    $countquery = "
        SELECT 
          id, " . $field . " 
        FROM 
          " .$table. " 
        WHERE 
          company = ? 
        AND 
           shop_code = ? 
        AND 
           language_code = ?
        AND 
           line_no = ?
    ";

    $stmt = mysqli_prepare($GLOBALS["mysql_con"], $countquery);
    mysqli_stmt_bind_param($stmt, "sssi", $company, $shopCode, $languageCode, $lineNo);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) == 1) {

        mysqli_stmt_bind_result($stmt, $id, $logo);
        while (mysqli_stmt_fetch($stmt)) {
            $id = $id;
        }
        mysqli_stmt_free_result($stmt);
        mysqli_stmt_close($stmt);

        $query = "
            UPDATE
              " .$table. "
            SET
              " . $field . " = ''
            WHERE
              id = ?
        ";

        $stmt = mysqli_prepare($GLOBALS["mysql_con"], $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_free_result($stmt);
        mysqli_stmt_close($stmt);

        echo "100";

    } else {
        echo "Fehler in Funktion 'delete_item_placeholder_image' \nquery: " . $countquery . " \n";
    }
}

function getShippingOptionEmailText($sales_header, $pdo)
{
    $textModule = '';
    if ($sales_header["shipping_option_line_no"] != 0 && isset($_POST['company'])) {

        $companyName = filter_var($_POST['company'], FILTER_SANITIZE_STRING);

        $prepStatement = '
                      SELECT 
                        send_order_mail_text 
                      FROM 
                        shop_shipping_option  
                      WHERE 
                            company = :company 
                        AND shop_code = :shop_code
                        AND language_code = :language_code
                        AND line_no = :line_no
                    ';

        $params = [
            [':company', $companyName, PDO::PARAM_STR],
            [':shop_code', $sales_header["shop_code"], PDO::PARAM_STR],
            [':language_code', $sales_header["language_code"], PDO::PARAM_STR],
            [':line_no', $sales_header["shipping_option_line_no"], PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $resultArray = $pdo->getResultArray();

        if (count($resultArray) > 0) {
            $textModule = $resultArray[0]['send_order_mail_text'];
        }

    }
    return $textModule;
}

function getPaymentOptionEmailText($sales_header, $pdo)
{
    $textModule = '';
    if ($sales_header["payment_option_line_no"] != 0 && isset($_POST['company'])) {

        $companyName = filter_var($_POST['company'], FILTER_SANITIZE_STRING);

        $prepStatement = '
                      SELECT 
                        send_order_mail_text 
                      FROM 
                        shop_payment_option  
                      WHERE 
                            company = :company 
                        AND shop_code = :shop_code
                        AND language_code = :language_code
                        AND line_no = :line_no
                    ';

        $params = [
            [':company', $companyName, PDO::PARAM_STR],
            [':shop_code', $sales_header["shop_code"], PDO::PARAM_STR],
            [':language_code', $sales_header["language_code"], PDO::PARAM_STR],
            [':line_no', $sales_header["payment_option_line_no"], PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $resultArray = $pdo->getResultArray();


        if (count($resultArray) > 0) {
            $textModule = $resultArray[0]['send_order_mail_text'];
        }

    }
    return $textModule;
}

function getPaymentOption($sales_header, $pdo)
{
    $paymentOption = '';
    if ($sales_header["payment_option_line_no"] != 0 && isset($_POST['company'])) {

        $companyName = filter_var($_POST['company'], FILTER_SANITIZE_STRING);

        $prepStatement = '
                      SELECT 
                        * 
                      FROM 
                        shop_payment_option  
                      WHERE 
                            company = :company 
                        AND shop_code = :shop_code
                        AND language_code = :language_code
                        AND line_no = :line_no
                    ';

        $params = [
            [':company', $companyName, PDO::PARAM_STR],
            [':shop_code', $sales_header["shop_code"], PDO::PARAM_STR],
            [':language_code', $sales_header["language_code"], PDO::PARAM_STR],
            [':line_no', $sales_header["payment_option_line_no"], PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $resultArray = $pdo->getResultArray();


        if (count($resultArray) > 0) {
            $paymentOption = $resultArray[0];
        }

    }
    return $paymentOption;
}

// Ausgeben von Fehlern in Navconnect-Funktionen
echo(mysqli_error($GLOBALS['mysql_con']));
?>