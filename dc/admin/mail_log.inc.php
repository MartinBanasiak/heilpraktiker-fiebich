<?

switch ($_GET["action"]) {
    case 'list':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'mail_log_listform.inc.php';
        break;
    case 'card':
        mail_log_card();
        break;
    case 'send':
        send_mail_log(TRUE);
        break;
    case 'send_list' :
        send_mail_log(FALSE);
        break;
    case 'filter' :
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'mail_log_listform.inc.php';
        break;
    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'mail_log_listform.inc.php';
        break;
}

function mail_log_card( $messages = array() ) {
    if ($_POST["input_id"] <> '') {
        $query  = "SELECT *, DATE_FORMAT(send_date,'%d.%m.%Y') AS format_send_date, DATE_FORMAT(send_date,'%H:%i') AS format_send_time FROM main_mail_log WHERE id = '" . $_POST["input_id"] . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $input_mail_log = @mysqli_fetch_array($result);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'mail_log_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'mail_log_listform.inc.php';
    }
}

function send_mail_log( $_card = TRUE ) {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $messages    = array();
    if ($_POST["input_id"] <> '') {
        $query  = "SELECT * FROM main_mail_log WHERE id = '" . $_POST["input_id"] . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $mail = @mysqli_fetch_array($result);
            if (mail_create($mail["subject"], $mail["message"], $mail["from_adress"], $mail["to_adress"], $mail["to_name"], $mail["from_name"], $mail["html_mail"], $mail["attachment"], 0, '')) {
                mail_send();
                $messages[] = '<div class="successbox">' . $translation->get("send_again_successfull") . '</div>';
            }
        }
        if ($_card === TRUE) {
            mail_log_card($messages);
        } else {
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'mail_log_listform.inc.php';
        }

    }
}

function mail_filter( $site_id, $language_id, $from_level = 1, $to_level = 3 ) {
    $current_filter = (isset($_POST['filter_days']) ? $_POST['filter_days'] : 7);
    $translation    = \DynCom\dc\common\classes\Registry::get("translation");

    echo "<div class=\"input\"><select onchange=\"filter_mails(this.value);\" class=\"bigselect\" name=\"page_filter\" id=\"page_filter\">";
    echo "<option value='1 WEEK' " . ($current_filter == '1 WEEK' ? 'selected="selected"' : '') . ">" . $translation->get("last_week") . "</option>";
    echo "<option value='1 MONTH' " . ($current_filter == '1 MONTH' ? 'selected="selected"' : '') . ">" . $translation->get("last_month") . "</option>";
    echo "<option value='1 YEAR' " . ($current_filter == '1 YEAR' ? 'selected="selected"' : '') . ">" . $translation->get("last_year") . "</option>";
    echo "<option value='100 YEAR' " . ($current_filter == '100 YEAR' ? 'selected="selected"' : '') . ">" . $translation->get("show_all") . "</option>";
    echo "</select>";
    echo "</div>";
}
