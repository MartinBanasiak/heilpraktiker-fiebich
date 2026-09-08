<?
$tempSitepart = array(
    "navigation_has_sitepart_id" => $sitepart_id
);

$translation = \DynCom\dc\common\classes\Registry::get("translation");

if (isset($_GET["action" . $sitepart_id]) && $_GET["action" . $sitepart_id] == "send") {

    $requestmassage = "";
    $success = false;

    // daten sammeln
    $query              = "SELECT * FROM contactform_header WHERE id = '" . $sitepart_id."'";
    $result             = @mysqli_query($GLOBALS['mysql_con'], $query);
    $contactform_header = @mysqli_fetch_array($result);
    $filetypeError      = FALSE;
    $error              = FALSE;
    $message            = "";

    $query = "SELECT * FROM contactform_line WHERE header_id = '" . $sitepart_id . "'  ORDER BY sorting ASC";

    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) > 0) {
        unset($field_errors);
        $field_errors = array();
        $attachements = array();
        while ($field = @mysqli_fetch_array($result)) {
            if ($_POST["input_" . $field["code"]] == '' &&
                $field["mandatory"] &&
                $field["typ"] != 8
            ) {
                $error = TRUE;
                array_push($field_errors, $field['id']);
            }
            if(($field["name"] == "company" && $_POST["input_" . $field["code"]] == "google") || ($field["name"] == "Unternehmen" && $_POST["input_" . $field["code"]] == "google"))
            {
                die;
            }
            else
            {
                switch ($field["typ"]) {
                    case 1:
                        $message .= "<strong>" . $field["name"] . ":</strong> " . $_POST["input_" . $field["code"]] . "\n";
                        break;
                    case 2:
                        $search_array = array('\r\n', '\n\r', '\r', '\n');
                        $replace_array = array(PHP_EOL, PHP_EOL, PHP_EOL, PHP_EOL);
                        $cleaned_text = str_replace($search_array, $replace_array, $_POST["input_" . $field["code"]]);
                        $message .= " <strong>" . $field["name"] . "</strong> " . $cleaned_text . "";
                        break;
                    case 3:
                        $boolean_text = ($_POST["input_" . $field["code"]]) ? "Ja" : "Nein";
                        $message .= "\n<strong>" . $field["name"] . ":</strong> " . $boolean_text . "\n";
                        break;
                    case 4:
                        $message .= "<strong>" . $field["name"] . ":</strong> " . $_POST["input_" . $field["code"]] . "\n";
                        break;
                    case 5:
                        $message .= "<strong>" . $field["name"] . "</strong>\n";
                        break;
                    case 6:
                        $message .= "\n";
                        break;
                    case 7:
                        $message .= "<strong>" . $_POST["input_" . $field["code"]] . "</strong>\n";
                        break;
                    case 8:
                        $attachemend = $_FILES["input_" . $field["code"]];
                        if ($attachemend["name"] == "" && $field["mandatory"]) {
                            $error = TRUE;
                            break;
                        }

                        if ($attachemend["name"] == "") {
                            break;
                        }

                        $not_allowed_filetypes = array('php');
                        $pos = strrpos($attachemend["name"], '.');
                        $ext = strtolower(substr($attachemend["name"], $pos + 1));
                        if (in_array($ext, $not_allowed_filetypes)) {
                            $filetypeError = TRUE;
                            $error = TRUE;
                            break;
                        }

                        if (!is_valid_file($attachemend)) {
                            $filetypeError = TRUE;
                            $error = TRUE;
                            break;
                        }
                        if (copy($attachemend["tmp_name"], rtrim($_SERVER["DOCUMENT_ROOT"], "/") . '/userdata/mail_attachments/' . $attachemend["name"] . '')) {
                            $attachements[] = $attachemend["name"];
                        }
                        break;
                }
            }
        }
    }
    if (($_POST["input_captcha1"] <> "") || ((time() - $_POST["input_captcha2"]) < 2)) {
        $error = TRUE;
        $requestmassage .= $GLOBALS['tc']['contactform_spam'];
    }
    // Google Re Captcha
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array('secret' => "6LfIGJAaAAAAAPifXM2t8Wawkyf5lE4V_POboMlB", 'response' => $_POST["g-recaptcha-response"]);
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $decodedResult = json_decode($result);

    if (empty($decodedResult->success)) {
        $error = TRUE;
        $requestmassage .= $GLOBALS['tc']['contactform_spam'];
    }
    if (!$error) {
        foreach ($attachements as &$attachement_line) {
            $attachement_line = "/userdata/mail_attachments/" . $attachement_line;
        }
        unset($attachement_line);

        /*if ($attachemend["name"] == "") {
            $attachemend_path = "";
        } else {
            $attachemend_path = "/userdata/mail_attachements/";
        }*/
        if (mail_create($contactform_header["subject"],
            $message,
            $contactform_header["sender_email"],
            $contactform_header["recipient_email"],
            $contactform_header["sender_name"],
            $contactform_header["recipient_name"],
            FALSE,
            $attachements,
            '',
            '')) {
            mail_send();
            $success = true;
            $requestmassage .=  $GLOBALS['tc']['contactform_success'];
            foreach ($_POST as $key => $value) {
                unset($_POST[$key]);
            }
        } else {
            $error = TRUE;
            $requestmassage .=  $translation->get('not_send');
        }
    } else {
        $error = TRUE;
        $requestmassage .=  $GLOBALS['tc']['mandatory_fields_error'];
        if ($filetypeError === TRUE) {
            $requestmassage .=  $GLOBALS['tc']['contactform_error_php'];
        }
    }
    if($error || $success){
        if($success){
            get_requestbox($requestmassage,"","success");
        }else{
            get_requestbox($requestmassage,"");
        }
    }
}
?>
<div class="contactformular">
    <div class="sitepart_<?= $sitepart_id ?>">
        <form  name="contactformular_<?= $sitepart_id ?>" method="post" action="<?= ml($tempSitepart, "action", "send") ?>" enctype="multipart/form-data">

            <?
            $query  = "SELECT * FROM contactform_line WHERE header_id = '" . $sitepart_id . "' ORDER BY sorting ASC";
            $result = @mysqli_query($GLOBALS['mysql_con'], $query);
            if (@mysqli_num_rows($result) > 0) {
                while ($field = @mysqli_fetch_array($result)) {

                    if ($field['mandatory'] == 1) {
                        $mandatory_text = " *";
                        $required = "required";
                    } else {
                        $mandatory_text = "";
                        $required = "";
                    }
                    $prefillValue = '';
                    if (array_key_exists("input_" . $field["code"],$_POST)) {
                        $prefillValue = $_POST["input_" . $field["code"]];
                    }
                    if ($field["infield"] == "1") {
                        $placeholder = "placeholder='" . $field['name'] . $mandatory_text . "'";
                        $label = "";
                    } else {
                        $placeholder = "";
                        $label = $field['name'] . $mandatory_text;
                    }

                    if (isset($_GET["action" . $sitepart_id]) && $_GET["action" . $sitepart_id] == "send" && $field["mandatory"] == 1 && trim($_POST["input_" . $field["code"]]) == "") {
                        $errorclass = "error";
                    } else {
                        $errorclass = "";
                    }

                    if (isset($_GET["action" . $sitepart_id]) && $_GET["action" . $sitepart_id] == "send" && $field["mandatory"] == 1 && in_array($field['id'], $field_errors)) {
                        $errorclass = "error";
                    } else {
                        $errorclass = "";
                    }

                    switch ($field["typ"]) {
                        case 1:?>
                            <div class="form-group">
                                <label for="input_<?=$field["code"];?>"><?=$label?></label>
                                <input type="text" class=" text <?= $errorclass ?>" <?= $required ?>
                                       name="input_<?= $field['code'] ?>"
                                       id="input_<?= $field["code"]; ?>" <?= $placeholder; ?>
                                       value="<?= htmlspecialchars($_POST["input_" . $field["code"]], ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                            <?
                            break;
                        case 2:?>
                            <div class="form-group">
                                <label for="input_<?=$field["code"];?>"><?=$label?></label>
                                <textarea class=" textarea <?= $errorclass ?>" <?= $required ?>
                                          name="input_<?= $field['code'] ?>"
                                          id="input_<?= $field["code"]; ?>" <?= $placeholder; ?>
                                          value="<?= htmlspecialchars(str_replace( '\r\n', PHP_EOL, $_POST["input_" . $field["code"]]), ENT_QUOTES, 'UTF-8') ?>"></textarea>
                            </div>
                            <?
                            break;
                        case 3:
                            $checked_text = ($prefillValue) ? " checked=\"checked\"" : "";
                            ?>
                            <div class="form-group marginTop checkbox-group">
                                <label class="specialcheckbox">
                                    <input <?= $checked_text ?> <?= $required ?>
                                            name="input_<?= $field['code'] ?>" id="input_<?= $field["code"]; ?>"
                                            type="checkbox" value="<?= $field["code"]; ?>">
                                    <label for="input_<?= $field['code'] ?>"></label>
                                    <?=$field['name'] . $mandatory_text;?>
                                </label>
                            </div>
                            <?
                            break;
                        case 4:?>
                            <div class="form-group">
                                <label for="input_<?=$field["code"];?>"><?=$label?></label>
                                <div class="input select_body <?= $errorclass ?>">
                                    <select class="select" <?= $required ?> name="input_<?= $field['code'] ?>"
                                            id="input_<?= $field['code']; ?>">
                                        <?
                                        $options = explode(";", $field["option_string"]);
                                        foreach ($options as $option) {
                                            $selected_text = ($prefillValue == $option) ? " selected=\"selected\"" : "";
                                            echo "<option" . $selected_text . " value=\"" . $option . "\">" . $option . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <?
                            break;
                        case 5:
                            echo "<h2>" . $field["name"] . "</h2>";
                            break;
                        case 6:
                            echo "<br />";
                            break;
                        case 7:
                            if ($_GET["registration"] == 1 && (int)$_GET["collection_id"] > 0) {
                                $query      = "SELECT * FROM main_collection WHERE id = '" . $_GET["collection_id"]."'";
                                $newsresult = @mysqli_query($GLOBALS['mysql_con'], $query);
                                if (@mysqli_num_rows($newsresult) == 1) {
                                    $news = @mysqli_fetch_array($newsresult);
                                    echo "<h3>" . $news["description"] . "</h3>";
                                    echo "<input name=\"input_" . $field["code"] . "\" type=\"hidden\" id=\"input_" . $field["code"] . "\" value=\"" . $news["description"] . "\" /></td></tr>\n";
                                }
                            }
                            break;
                        case 8:?>
                            <div class="form-group">
                                <label for="input_<?=$field["code"];?>"><?=$label?></label>
                                <input class=" file <?= $errorclass ?>" <?= $required ?>
                                       name="input_<?= $field['code'] ?>" id="input_<?= $field["code"]; ?>"
                                       type="file"/>
                            </div>
                            <?
                            break;
                    }
                }
            }
            // Integration Google API JS
            ?>
            <script class='DCCookie_recaptcha' type="text/plain" src="https://www.google.com/recaptcha/api.js" async defer></script>
            <div style="display:none;">
                <input type="text" name="input_captcha1" id="input_captcha1" value="" />
                <input type="text" name="input_captcha2" id="input_captcha2" value="<?= time() ?>" />
            </div>
            <div>
                <input type="submit" name="button" class="button" id="button" value="<?= $GLOBALS["tc"]["send"] ?>" /><br/><br/>
                <div class="g-recaptcha" data-sitekey="6LfIGJAaAAAAAPqFCXMcYA1-Y4j1sgBJ1IDTBO9U"></div>
            </div>
        </form>
    </div>
</div>