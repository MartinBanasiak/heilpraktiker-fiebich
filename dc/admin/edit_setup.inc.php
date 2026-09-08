<?

switch ($_GET["action"]) {
    case 'save':
        save_setup();
        break;
    default:
        setup_card();
        break;
}

function setup_card() {
    $query  = "SELECT * FROM main_setup";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $input_setup = @mysqli_fetch_array($result);
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_setup_cardform.inc.php';
    }
}

function save_setup() {
    if ($GLOBALS['admin_user']['is_super_user'] == 1) {
        $query = "UPDATE main_setup SET admin_name = '" . $_POST["input_admin_name"] . "', admin_link = '" . $_POST["input_admin_link"] . "', std_main_site_id = '" . $_POST["input_std_main_site_id"] . "' , support_recipient_email = '" . $_POST['input_support_recipient_email'] . "' WHERE id = '" . $_POST["input_id"] . "' LIMIT 1";
        if (($_POST["input_admin_name"] == '') | ($_POST["input_admin_link"] == '')) {
            echo "<div class=\"errorbox\">F&uuml;llen Sie die Felder Admin-Browsertitel und Admin-Webadresse aus</div>\n";
            $error = TRUE;
        }
        if (!$error) {
            @mysqli_query($GLOBALS['mysql_con'], $query);
            setup_card();
        } else {
            $input_setup["id"]                      = $_POST["input_id"];
            $input_setup["admin_name"]              = $_POST["input_admin_name"];
            $input_setup["admin_link"]              = $_POST["input_admin_link"];
            $input_setup["std_main_site_id"]        = $_POST["input_std_main_site_id"];
            $input_setup["support_recipient_email"] = $_POST["input_support_recipient_email"];
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_setup_cardform.inc.php';
        }
    } else {
        setup_card();
    }
}

?>