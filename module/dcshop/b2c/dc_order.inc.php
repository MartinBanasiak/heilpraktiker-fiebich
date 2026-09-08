<?
$layer_no = get_layer_no();
$category = $GLOBALS['category'];
switch ($_GET["action"]) {
    case '1':
        show_dc_step_1($category);
        break;
    case '2':
        $dc_id = save_dc_order($_GET["dc_id"], $category);
        ($dc_id) ? show_dc_step_2($dc_id, $category) : show_dc_step_1($category);
        break;
    case '3':
        finish_dc_order($_POST["dc_id"]);
        break;
    default:
        show_dc_step_1($category);
        break;
}

function show_dc_step_1($category = '') {
    $dc = get_dc($_GET["dc_id"]);
    ($dc["background_image"] == '') ? $dc["background_image"] = 1 : '';
    ($dc["amount_id"] == '') ? $dc["amount_id"] = 1 : '';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'dc_order_step_1.inc.php';
}

function show_dc_step_2($dc_id, $category = '') {
    $dc                = get_dc($dc_id);
    $_SESSION["dc_id"] = $dc_id;
    if (strlen($_POST["input_from_email"]) > 0 && strlen($_POST["input_message"]) > 0 && ($_POST["input_amount"] > 0 || $_POST["input_amount_id"] > 0)) {
        require_once("dc_order_step_2.inc.php");
    } else {
        $_POST["dc_error"] = TRUE;
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'dc_order_step_1.inc.php';
    }
}

function save_dc_order($dc_id, $category = '') {
    if ($_POST["input_coupon_amount"] > 0 && $_POST["input_coupon_amount"] != '') {
        $_POST["input_amount"]    = $_POST["input_coupon_amount"];
        $_POST["input_amount_id"] = 99;
    } else {
        $_POST["input_amount"] = $GLOBALS["shop_language"]["digital_coupon_amount_" . $_POST["input_amount_id"]];
    }
    $_POST["input_amount"] = str_replace(',', '.', $_POST["input_amount"]);
    if ($_POST["dc_shipping_option"] == 0) {
        $_POST["input_to_email0"]  = $_POST["input_from_email1"];
        $_POST["input_from_email"] = $_POST["input_from_email1"];
    } else {
        $_POST["input_from_email"] = $_POST["input_from_email2"];
    }
    $_POST["input_message"] = str_replace('\\r', '', $_POST["input_message"]);
    $_POST["input_message"] = str_replace('\\n', '<br/>', $_POST["input_message"]);
    $_POST["input_message"] = str_replace('\\', '', $_POST["input_message"]);

    if ($dc_id == '') {
        $query = "INSERT INTO shop_digital_coupon
				  SET id = NULL,
				  coupon_header = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["shop_language"]["coupon_header_digital_coupon"]) . "',
				  shop_coupon_line_id = '',
				  from_name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_POST["input_from_name"]) . "',
				  from_email = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_POST["input_from_email"]) . "',
				  to_name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], nl2br($_POST["input_to_name"])) . "',
				  to_email = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_POST["input_to_email0"]) . "',
				  amount_id = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_POST["input_amount_id"]) . "',
				  amount = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_POST["input_amount"]) . "',
				  background_image = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_POST["input_background_image_id"]) . "',
				  message = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], nl2br($_POST["input_message"])) . "',
				  shipping_option = '" . $_POST["dc_shipping_option"] . "'";
        if (@mysqli_query($GLOBALS['mysql_con'], $query)) {
            return @mysqli_insert_id($GLOBALS['mysql_con']);
        } else {
            return FALSE;
        }
    } else {
        $query = "UPDATE shop_digital_coupon SET
				  coupon_header = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["shop_language"]["coupon_header_digital_coupon"]) . "',
				  shop_coupon_line_id = '',
				  from_name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_POST["input_from_name"]) . "',
				  from_email = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_POST["input_from_email"]) . "',
				  to_name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], nl2br($_POST["input_to_name"])) . "',
				  to_email = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_POST["input_to_email0"]) . "',
				  amount_id = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_POST["input_amount_id"]) . "',
				  amount = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_POST["input_amount"]) . "',
				  background_image = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_POST["input_background_image_id"]) . "',
				  message = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], nl2br($_POST["input_message"])) . "',
				  shipping_option = '" . $_POST["dc_shipping_option"] . "'
				  WHERE id = '" . $dc_id . "'";
        if (@mysqli_query($GLOBALS['mysql_con'], $query)) {
            return $dc_id;
        } else {
            return FALSE;
        }
    }
}


function create_background_select( $active_id ) {
    if ($active_id <> '') {
        $imagelink2 = $GLOBALS["shop_setup"]["dc_image_config"][2]["path"] . "/" . $GLOBALS["shop_language"]["digital_coupon_background_" . $active_id];
        ?>
        <script type="text/javascript">
            $(document).ready(function () {
                set_background('<?= $imagelink2 ?>');
                set_background_active(<?= $active_id ?>);
            });
        </script>
    <?
    }
    ?>
    <div id="dc_background_select">
        <h2><?= $GLOBALS["tc"]["dc_motifs"] ?></h2>
        <?
        for ($i = 1; $i < 10; $i++) {
            if ($GLOBALS["shop_language"]["digital_coupon_background_" . $i] <> '') {
                $imagelink  = $GLOBALS["shop_setup"]["dc_image_config"][1]["path"] . "/" . $GLOBALS["shop_language"]["digital_coupon_background_" . $i];
                $imagelink2 = $GLOBALS["shop_setup"]["dc_image_config"][2]["path"] . "/" . $GLOBALS["shop_language"]["digital_coupon_background_" . $i];
                $onclick    = "set_background('" . $imagelink2 . "'); set_background_active(" . $i . ");";
                echo "<div class=\"dc_background\" id=\"dc_background_" . $i . "\" onclick=\"" . $onclick . "\">";
                echo "<img src=\"" . $imagelink . "\" alt='".$GLOBALS["tc"]["dc_motifs"]."_".$i."' >";
                echo "</div>";
            }
        }
        ?>
        <div class="clearfloat"></div>
    </div>
    <input type="hidden" id="input_background_image_id" name="input_background_image_id"
           value="<?= $dc["background_image"] ?>">
<?
}

function create_amount_select( $active_id ) {
    echo "<div id=\"dc_amount_select\">";
    for ($i = 1; $i < 6; $i++) {
        if ($GLOBALS["shop_language"]["digital_coupon_amount_" . $i] > 0) {
            $onclick = "set_amount_active(" . $i . ");";
            ($active_id == $i) ? $active = ' active' : $active = '';
            echo "<div id=\"dc_amount_" . $i . "\"  onclick=\"" . $onclick . "\" class=\"dc_amount " . $active . "\">";
            echo format_amount($GLOBALS["shop_language"]["digital_coupon_amount_" . $i], FALSE);
            echo "</div>";
        }
    }
    echo "</div><div class=\"clearfloat\"></div>";
    if (empty($dc["amount_id"])) {
        $dc["amount_id"] = 1;
    }
    echo "<input type=\"hidden\" id=\"input_amount_id\" name=\"input_amount_id\" value=\"" . $dc["amount_id"] . "\">";
}

function create_individual_amount_select( $value, $amnt_id, $title = "") {
    if (!($value > 0) || ($amnt_id > 0 && $amnt_id <> 99)) {
        $value = '';
    }

    if($title != ""){
        $label = "<label>".$title."</label>";
    }

    if ($GLOBALS["shop_language"]["max_coupon_amount"] > 0 && $GLOBALS["shop_language"]["minimum_coupon_amount"] > 0) {
        $onclick = "$('.dc_amount').removeClass('active');
					if($(this).val() == '" . $GLOBALS["tc"]["own_amount"] . "'){ $(this).val(''); };";
        $onblur  = "if($(this).val() == ''){ $(this).val('" . $GLOBALS["tc"]["own_amount"] . "'); } if($(this).val() != '" . $GLOBALS["tc"]["own_amount"] . "') { set_amount_active($('#input_amount_id').val()); }";
        echo "<div class=\"form-group\">".$label."<input class='form-control' type=\"text\" value=\"" . $value . "\" name=\"input_coupon_amount\" id=\"input_coupon_amount\" placeholder='".$GLOBALS["tc"]["own_amount"] ." (". $GLOBALS["tc"]["from"] ." ". format_amount($GLOBALS["shop_language"]["minimum_coupon_amount"], FALSE, false) ." " . $GLOBALS["tc"]["period_to"] ." " . format_amount($GLOBALS["shop_language"]["max_coupon_amount"], FALSE, false) .")'/></div>";
        //echo "<div>(" . $GLOBALS["tc"]["from"] . " " . format_amount($GLOBALS["shop_language"]["minimum_coupon_amount"], FALSE) . " " . $GLOBALS["tc"]["period_to"] . " " . format_amount($GLOBALS["shop_language"]["max_coupon_amount"], FALSE) . ")</div>";
    }
    echo "<input type=\"hidden\" id=\"input_amount\" name=\"input_amount\" value=\"" . $dc["amount"] . "\">";
    ?>
<?
}

function create_shipping_option_select( $dc ) {?>
    <div id="dc_shipping_option_select">
        <div class="form-check dc_shipping_option">
            <label class="form-check-label">
                <input class="form-check-input" name="dc_shipping_option" id="dc_shipping_option1" value="1" type="radio" checked="checked">
                <?=$GLOBALS["tc"]["send_to_other"];?>
            </label>
        </div>
        <div class="form-check dc_shipping_option">
            <label class="form-check-label">
                <input class="form-check-input" name="dc_shipping_option" id="dc_shipping_option0" value="0" type="radio">
                <?=$GLOBALS["tc"]["send_to_me"];?>
            </label>
        </div>
    </div>
<?}


?>