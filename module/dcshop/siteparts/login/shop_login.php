<?
function shop_login_show( $sitepart_id ) {
    if (!($GLOBALS["visitor"]["frontend_login"])) {
        $query2  = "SELECT * FROM main_shop_login WHERE id = '" . $sitepart_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query2);
        if (@mysqli_num_rows($result) == 1) {

            $login_sitepart        = @mysqli_fetch_array($result);
            $show_labels_in_fields = (bool)$login_sitepart['shop_labels_in_fields'];
            $email_field = (
            $show_labels_in_fields ?
                "
					<div class=\"form-group\">
                        <input type=\"text\" class=\"form-control\" id=\"input_email\" name=\"input_email\" value='".$_COOKIE['login_used_data']."'  placeholder=\"".$GLOBALS['tc']['email']."\">
                    </div>
					"
                :
                "
                    <div class=\"form-group\">
                        <label for=\"input_email\">" . $GLOBALS['tc']['email'] . "</label>
                        <input type=\"text\" class=\"form-control\" name=\"input_email\" value='".$_COOKIE['login_used_data']."' id=\"input_email\">
                    </div>
				"
            );

            $login_field = (
            $show_labels_in_fields ?
                "
                        <div class=\"form-group\">
                            <input type=\"text\" class=\"form-control\" name=\"input_login\" id=\"input_login\" value='".$_COOKIE['login_used_data']."' placeholder=\"".$GLOBALS['tc']['login_b2b']."\">
                        </div>
                        "
                :
                "
                        <div class=\"form-group\">
                            <label for=\"input_login\">" . $GLOBALS['tc']['login_b2b'] . "</label>
                            <input type=\"text\" class=\"form-control\" name=\"input_login\" value='".$_COOKIE['login_used_data']."' id=\"input_login\">
                        </div>
                    "
            );

            $customer_no_field = (
            $show_labels_in_fields ?
                "
                        <div class=\"form-group\">
                            <input type=\"text\" class=\"form-control\" name=\"input_customer_no\" value='".$_COOKIE['customer_used_data']."' id=\"input_customer_no\" placeholder=\"".$GLOBALS['tc']['customer_no']."\">
                        </div>
                        "
                :
                "
                        <div class=\"form-group\">
                            <label for=\"input_customer_no\">" . $GLOBALS['tc']['customer_no'] . "</label>
                            <input type=\"text\" class=\"form-control\" value='".$_COOKIE['customer_used_data']."' name=\"input_customer_no\" id=\"input_customer_no\">
                        </div>
                    "
            );

            $password_field = (
            $show_labels_in_fields ?
                "
                        <div class=\"form-group\">
                            <input type=\"text\" class=\"form-control\" name=\"input_password\" id=\"input_password\" placeholder=\"".$GLOBALS['tc']['password']."\">
                        </div>
                        "
                :
                "
                        <div class=\"form-group\">
                            <label for=\"input_password\">" . $GLOBALS['tc']['password'] . "</label>
                            <input type=\"password\" class=\"form-control\" name=\"input_password\" id=\"input_password\">
                        </div>
                    "
            );

            $field_1_html = '';
            $field_2_html = '';

            $target_site_code      = $login_sitepart['target_site_code'];
            $target_language_code  = $login_sitepart['target_language_code'];

            $query       = "
				SELECT 
					shop_shop.*
				FROM 
					main_language,
					main_site,
					shop_shop
				WHERE
					main_site.code = '" . $target_site_code . "'
				  AND
					main_language.main_site_id = main_site.id
				  AND
					main_language.code = '" . $target_language_code . "'
				  AND
					shop_shop.company = main_language.company
				  AND
					shop_shop.code = main_language.shop_code
				";
            $shop_result = @mysqli_query($GLOBALS['mysql_con'], $query);
            $shop        = @mysqli_fetch_assoc($shop_result);

            $loginDataType= $_POST['input_email'];
            switch ($shop['login_type']) {
                case 0: //E-Mail & Passwort
                    $field_1_html = $email_field;
                    $loginDataType= $_POST['input_email'];
                    break;
                case 1: //Login & Passwort
                    $field_1_html = $login_field;
                    $loginDataType= $_POST['input_login'];
                    break;
                case 2: //Kunden-Nr., Login & Passwort
                    $field_1_html = $customer_no_field;
                    $field_2_html = $login_field;
                    $loginDataType= $_POST['input_login']."_".$_POST['input_customer_no'];
                    break;
                case 3: //Kunden-Nr., E-Mail & Passwort
                    $field_1_html = $customer_no_field;
                    $field_2_html = $email_field;
                    $loginDataType= $_POST['input_email'];
                    break;
                case 4: //Kunden-Nr. & Passwort
                    $field_1_html = $customer_no_field;
                    $loginDataType= $_POST['input_customer_no'];
                    break;
                default:
                    break;
            }


            $falseLoginData = get_false_password_login_counter(md5(getUserIP()),md5($loginDataType));
            $now = time();
            $dataBaseCounter = 0;
            $blockTime = $now;
            $blockErrorCounter = 0;

            if(count($falseLoginData) > 0)
            {
                $dataBaseCounter = $falseLoginData[0]['total_counter'];
                $blockTime = $falseLoginData[0]['next_login_time'];
                $blockErrorCounter = $falseLoginData[0]['block_counter'];
            }

            $counter = $dataBaseCounter / $GLOBALS['shop_setup']['allowed_false_login_times'];
            $counter = (int)$counter;
            $waitingSeconds = $counter * $GLOBALS['shop_setup']['false_login_waiting_seconds'];



            if($counter != $blockErrorCounter)
            {
                $blockTime =  $now + $waitingSeconds;
                add_false_password_loign_counter(md5(getUserIP()), md5($loginDataType), $dataBaseCounter ,$blockTime,  $counter);
            }


            if( $blockTime >  $now)
            {
                $message = str_replace('%number_of_times%',$dataBaseCounter ,$GLOBALS['tc']['reach_false_login_times']);
                $message = str_replace('%minutes_number%',((int)$waitingSeconds/60),$message);
                echo '<h2>'.$message.'</h2>';

            }
            else
            {
                $target_url            = $login_sitepart['target_url'];
                $method                = 'POST';
                $show_labels_in_fields = (bool)$login_sitepart['shop_labels_in_fields'];

                //Shop holen

                if ($show_labels_in_fields == 1) { ?>
                    <script type="text/javascript">
                        function loginLabels( loginElement, textValue ) {
                            if (loginElement != '') {
                                loginElement.val(textValue);
                                loginElement.bind('focus', function () {
                                    if ($(this).val() == textValue) {
                                        $(this).val('');
                                    }
                                });
                                loginElement.bind('blur', function () {
                                    if ($(this).val() == '' || $(this).val() == textValue) {
                                        $(this).val(textValue);
                                    }
                                });
                            }
                        }
                        $(document).ready(function () {
                            var input_customer_noElement = $('#input_customer_no');
                            var input_loginElement = $('#input_login');
                            var input_emailElement = $('#input_email');
                            var input_passwordElement = $('#input_password');
                            var input_customer_noValue = '<?= $GLOBALS["tc"]["customer_no"]?>';
                            var input_loginValue = '<?= $GLOBALS["tc"]["username"] ?>';
                            var input_emailValue = '<?= $GLOBALS["tc"]["email"] ?>';
                            var input_passwordValue = '<?= $GLOBALS["tc"]["password"] ?>';
                            loginLabels(input_customer_noElement, input_customer_noValue);
                            loginLabels(input_loginElement, input_loginValue);
                            loginLabels(input_emailElement, input_emailValue);
                            loginLabels(input_passwordElement, input_passwordValue);
                        });
                    </script>
                <? }


                ?>
                <div class="login" id="sitepart_<?= $sitepart["navigation_has_sitepart_id"] ?>">

                    <?
                    if ($_GET['login_error']) {
                        get_requestbox($GLOBALS['tc']['login_error']);
                    }
                    ?>
                    <form id="form_shop_login" name="form_shop_login" method="<?= $method ?>" action="<?= $target_url ?>">
                        <input type="hidden" name="action" value="shop_login" />
                        <?
                        echo $field_1_html;
                        echo $field_2_html;
                        echo $password_field;
                        ?>
                        <!-- <div class="form-check">
                            <label class="form-check-label">
                                <input class="form-check-input" name="remember_login" id="remember_login" type="checkbox" value="">
                                <?= $GLOBALS['tc']['remember_login'] ?>
                            </label>
                        </div> -->
                        <br/>
                        <div class="submit_button">
                            <input type="submit" class="button" value="<?= $GLOBALS['tc']['login'] ?>" />
                        </div>
                    </form>
                </div>
                <?
            }

        }
    } else //EINGELOGTER BEREICH
    {
    }
}

function shop_login_edit() {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_login.inc.php';
}
/*
if((int) $sitepart['main_sitepart_header_id'] > 0 ){
	shop_main_show($sitepart['main_sitepart_header_id']);
}
require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_main.inc.php';*/