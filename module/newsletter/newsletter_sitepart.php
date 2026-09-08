<?
function newsletter_sitepart_show( $sitepart_id ) {

    $translation = \DynCom\dc\common\classes\Registry::get("translation");

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

    $tempSitepart = array(
        "navigation_has_sitepart_id" => $sitepart_id
    );

    // daten sammeln
    $query               = "SELECT * FROM newsletter_sitepart WHERE id = '" . $sitepart_id."'";
    $result              = @mysqli_query($GLOBALS['mysql_con'], $query);
    $newsletter_sitepart = @mysqli_fetch_assoc($result);
    $filetypeError       = FALSE;
    $error               = FALSE;
    $message             = "";

    //Email vorfüllen
    $email_prefill = $GLOBALS['shop_user']['email'];
    if (!empty($_REQUEST["from_sitepart"]) && $newsletter_sitepart['type'] == 0) {
        $email_prefill = filter_var($_REQUEST['input_email'], FILTER_VALIDATE_EMAIL);
    }

    //Felder definieren
    $fields = array();
    if ($newsletter_sitepart['label_position'] != 1) {
        $email_field = '
        <div class="form-group">
              <label for="input_email">' . $translation->get('email') . '</label>
              <input id="input_email" class="form-control text" name="input_email" type="text" value="' . $email_prefill . '" />
        </div>
		';
    } else {
        $email_field = '
        <div class="form-group">
              <label for="input_email">&nbsp;</label>
              <input id="input_email" class="form-control text" placeholder="' . $translation->get('email') . '" name="input_email" type="text" value="' . $email_prefill . '" />
        </div>
		';
    }

    $salutation_field = '';
    $first_name_field = '';
    $last_name_field  = '';
    $name_field       = '';
    $post_code_field  = '';
    $city_field       = '';
    $birthday_field   = '';
    if ($newsletter_sitepart['type'] == 0 || $newsletter_sitepart['type'] == 2 || $newsletter_sitepart['type'] == 3) {

        if (($newsletter_sitepart['salutation_active'] == 1) && ($newsletter_sitepart['label_position'] != 1) && ($newsletter_sitepart['name_status'] > 0)) {
            $salutation_field = '
                    <div class="form-group">
                        <label for="input_salutation">' . $translation->get('salutation') . '</label>   
                        <div class="input select_body">
                            <select id="input_salutation" name="input_salutation">
                                <option value="0">' . $translation->get('Mr.') . '</option>
                                <option value="1">' . $translation->get('Ms.') . '</option>
                            </select>
                        </div>         
                    </div>
			';
        } elseif (($newsletter_sitepart['salutation_active'] == 1) && ($newsletter_sitepart['name_status'] > 0)) {
            $salutation_field = '
                    <div class="form-group">
                        <label for="input_salutation">' . $translation->get('salutation') . '</label>   
                        <div class="input select_body">
							<optgroup label="' . $translation->get('salutation') . '">
								<option value="0">' . $translation->get('Mr.') . '</option>
								<option value="1">' . $translation->get('Ms.') . '</option>
							</optgroup>
                        </div>         
                    </div>
			';
        }


        if (($newsletter_sitepart['name_status'] == 1) && ($newsletter_sitepart['label_position'] != 1)) {
            $first_name_field = '
                    <div class="form-group">
                        <label for="input_first_name">' . $translation->get('first_name') . '</label>   
                        <input id="input_first_name" class="form-control text" name="input_first_name" type="text" value="" />
                    </div>
			';
            $last_name_field  = '
                    <div class="form-group">
                        <label for="input_last_name">' . $translation->get('last_name') . '</label>   
                        <input id="input_last_name" class="form-control text" name="input_last_name" type="text" value="" />
                    </div>
			';

        } elseif ($newsletter_sitepart['name_status'] == 1) {
            $first_name_field = '
                    <div class="form-group">
                        <label for="input_first_name">&nbsp;</label>   
                        <input id="input_first_name" class="form-control text" name="input_first_name" type="text" value="" placeholder="' . $translation->get('first_name') . '" />
                    </div>
			';
            $last_name_field  = '
                    <div class="form-group">
                        <label for="input_last_name">&nbsp;</label>   
                        <input id="input_last_name" class="form-control text" name="input_last_name" type="text" value="" placeholder="' . $translation->get('last_name') . '" />
                    </div>
			';
        } elseif (($newsletter_sitepart['name_status'] == 2) && ($newsletter_sitepart['label_position'] != 1)) {
            $name_field = '	
                    <div class="form-group">
                        <label for="input_name">' . $translation->get('name') . '</label>   
                        <input id="input_name" class="form-control text" name="input_name" type="text" value="" />
                    </div>	
			';
        } elseif ($newsletter_sitepart['name_status'] == 2) {
            $name_field = '
                    <div class="form-group">
                        <label for="input_name">&nbsp;</label>   
                        <input id="input_name" class="form-control text" name="input_name" placeholder="' . $translation->get('name') . '" type="text" value="" />
                    </div>		
			';
        }


        if (($newsletter_sitepart['city_active'] == 1) && ($newsletter_sitepart['label_position'] != 1)) {
            $city_field = '
                    <div class="form-group">
                        <label for="input_city">' . $translation->get('city') . '</label>   
                        <input id="input_city" class="form-control text" name="input_city" type="text" value="" />
                    </div>	
			';

        } elseif ($newsletter_sitepart['city_active'] == 1) {
            $city_field = '
                    <div class="form-group">
                        <label for="input_city">&nbsp;</label>   
                        <input id="input_city" class="form-control text" name="input_city" placeholder="' . $translation->get('city') . '" type="text" value="" />
                    </div>	
				';
        }


        if (($newsletter_sitepart['post_code_active'] == 1) && ($newsletter_sitepart['label_position'] != 1)) {
            $post_code_field = '
                    <div class="form-group">
                        <label for="input_post_code">' . $translation->get('post_code') . '</label>   
                        <input id="input_post_code" class="form-control text" name="input_post_code" type="text" value="" />
                    </div>	
			';

        } elseif ($newsletter_sitepart['post_code_active'] == 1) {
            $post_code_field = '
                    <div class="form-group">
                        <label for="input_post_code">&nbsp;</label>   
                        <input id="input_post_code" class="form-control text" name="input_post_code" placeholder="' . $translation->get('post_code') . '" type="text" value="" />
                    </div>	
				';
        }


        if (($newsletter_sitepart['birthday_active'] == 1) && ($newsletter_sitepart['label_position'] != 1)) {
            $birthday_field = '
                    <div class="form-group">
                        <label for="input_birthday">' . $translation->get('birthday') . '</label>   
                        <div class="date input-group input-append">
                            <input id="input_birthday" class="form-control text" name="input_birthday" type="text" value="" />
                            <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                        </div>
                    </div>
			';

        } elseif ($newsletter_sitepart['birthday_active'] == 1) {
            $birthday_field = '
                    <div class="form-group">
                        <label for="input_birthday">&nbsp;</label>   
                        <input id="input_birthday" class="form-control text" name="input_birthday" placeholder="' . $translation->get('birthday') . '" type="text" value="" />
                    </div>	
				';
        }

    }


    $classname = ($newsletter_sitepart['type'] == 0 ? 'newsletter_registration' : 'newsletter_deregistration');
    $message   = '';
    if (isset($_GET["action" . $sitepart_id]) && $_GET["action" . $sitepart_id] == "send") {
        //Spam-Prüfung
        if (($_POST["input_captcha1"] <> "") | ((time() - $_POST["input_captcha2"]) < 2)) {
            $error = TRUE;
            $message .= $translation->get('spam_warning') . "<br/>";
        }


        //Globales Error-Handling
        if ($newsletter_version == 1) {
            if (empty($newsletter_sitepart['db_id']) || empty($newsletter_sitepart['access_token'])) {
                $error = TRUE;
                $message .= $translation->get('newsletter_no_login_data_error') . "<br/>";
            } else {
                $db_id        = $newsletter_sitepart['db_id'];
                $access_token = $newsletter_sitepart['access_token'];
            }
        } else {
            if (empty($newsletter_sitepart['group_name']) || empty($newsletter_sitepart['account']) || empty($newsletter_sitepart['login']) || empty($newsletter_sitepart['password'])) {
                $error = TRUE;
                $message .= $translation->get('newsletter_no_login_data_error') . "<br/>";
            } else {
                $group          = ((!empty($newsletter_sitepart['group_id']) && is_int($newsletter_sitepart['group_id'])) ? $newsletter_sitepart['group_id'] : $newsletter_sitepart['group_name']);
                $account        = $newsletter_sitepart['account'];
                $login          = $newsletter_sitepart['login'];
                $password       = $newsletter_sitepart['password'];
            }
        }


        if (empty($newsletter_sitepart['segment'])) {
            $error = TRUE;
            $message .= $translation->get('newsletter_no_segment_error') . "<br/>";
        }

        if (!array_key_exists('input_email',$_REQUEST) || empty($_REQUEST["input_email"])) {
            $error = TRUE;
            $message .= $translation->get('newsletter_no_email_error') . "<br/>";
        }


        if ($newsletter_sitepart['type'] == 0 || $newsletter_sitepart['type'] == 3) {
            $input_fields = array(
                'Segment'           =>      $newsletter_sitepart['segment'],
                'EMail'             =>      filter_var($_REQUEST['input_email'], FILTER_VALIDATE_EMAIL),
                'salutation'        =>      filter_var($_REQUEST['input_salutation'], FILTER_SANITIZE_NUMBER_INT),
                'first_name'        =>      filter_var($_REQUEST['input_first_name'], FILTER_SANITIZE_STRING),
                'last_name'         =>      filter_var($_REQUEST['input_last_name'], FILTER_SANITIZE_STRING),
                'name'              =>      filter_var($_REQUEST['input_name'], FILTER_SANITIZE_STRING),
                'city'              =>      filter_var($_REQUEST['input_city'], FILTER_SANITIZE_STRING),
                'post_code'         =>      filter_var($_REQUEST['input_post_code'], FILTER_SANITIZE_STRING),
                'birthday'          =>      filter_var($_REQUEST['input_birthday'], FILTER_SANITIZE_STRING),
                'subscribeFemale'   =>      filter_var($_REQUEST['subscribeFemale'], FILTER_SANITIZE_STRING),
                'subscribeMale'     =>      filter_var($_REQUEST['subscribeMale'], FILTER_SANITIZE_STRING)
            );

            //Error-Handling
            if ($error) {
                $frontend_message = $message;
                get_requestbox($frontend_message, "", "error");
            } else {

                $language_code = $GLOBALS['language']['code'];
                if ($newsletter_version == 1) {
                    $output_profile = array(
                        'EMail'        => $input_fields['EMail'],
                        'Segment'      => $input_fields['Segment'],
                        'SOI'          => 1,
                        'Sprachcode'   => $language_code
                    );
                } else {
                    $receivers = array();
                    $receivers[] = array();

                    $receivers[0] = array(
                        "email"                         =>      $input_fields['EMail'],
                        "activated"                     =>      0,
                        "deactivated"                   =>      0,
                        "registered"                    =>      time(),
                        "source"                        =>      $input_fields['Segment'],
                        "send_doi_mail"                 =>      true,
                        "global_attributes"	            =>      array(
                            "languagecode"              =>      array(
                                "value"                 =>      $language_code,
                                "type"                  =>      'text',
                                "description"           =>      'Sprachcode',
                            ),
                        ),
                        "attributes"	            =>      array(
                            "segment"                   =>      array(
                                "value"                 =>      $input_fields['Segment'],
                                "type"                  =>      'text',
                                "description"           =>      'Segment',
                            ),
                        )
                    );
                }



                $salutation = '';
                if (!empty($input_fields['salutation'])) {
                    if ($input_fields['salutation'] == 0) {
                        $salutation = $translation->get('Mr.');
                    } elseif ($input_fields['salutation'] == 1) {
                        $salutation = $translation->get('Ms.');
                    }
                } else {
                    if (!empty($input_fields["subscribeMale"])) {
                        $salutation = $translation->get('Mr.');
                    } elseif (!empty($input_fields["subscribeFemale"])) {
                        $salutation = $translation->get('Ms.');
                    }
                }

                if (!empty($salutation)){
                    if ($newsletter_version == 1) {
                        $output_profile['Anrede'] = $salutation;
                    } else {
                        $receivers[0]["global_attributes"]["salutation"] = array(
                            "value" => $salutation,
                            "type" => 'text',
                            "description" => 'Anrede',
                        );
                    }
                }

                $name       = '';
                $first_name = '';
                $last_name  = '';
                if (empty($input_fields['last_name']) && !empty($input_fields['name'])) {
                    $name       = $input_fields['name'];
                    $name_arr   = split_name($name);
                    $first_name = $name_arr['first'];
                    $last_name  = $name_arr['last'];
                } elseif (!empty($input_fields['last_name']) || !empty($input_fields['first_name'])) {
                    $first_name = trim($input_fields['first_name']);
                    $last_name  = trim($input_fields['last_name']);
                    $name       = $first_name;
                    if (strlen($last_name) > 0) {
                        $name .= ' ' . $last_name;
                    }
                }

                if (!empty($name)){
                    if ($newsletter_version == 1) {
                        $output_profile['Name'] = $name;
                    } else {
                        $receivers[0]["global_attributes"]["name"] = array(
                            "value"         =>      $name,
                            "type"          =>      'text',
                            "description"   =>      'Name',
                        );
                    }
                }

                if (!empty($first_name)){
                    if ($newsletter_version == 1) {
                        $output_profile['Vorname'] = $first_name;
                    } else {
                        $receivers[0]["global_attributes"]["first_name"] = array(
                            "value"         =>      $first_name,
                            "type"          =>      'text',
                            "description"   =>      'Vorname',
                        );
                    }
                }

                if (!empty($last_name)) {
                    if ($newsletter_version == 1) {
                        $output_profile['Nachname'] = $last_name;
                    } else {
                        $receivers[0]["global_attributes"]["last_name"] = array(
                            "value"         =>      $last_name,
                            "type"          =>      'text',
                            "description"   =>      'Nachname',
                        );
                    }
                }

                $birthday = '';
                if (!empty($input_fields['birthday'])) {
                    $birthday = $input_fields['birthday'];
                    if ($newsletter_version == 1) {
                        $output_profile['Geburtsdatum'] = $birthday;
                    } else {
                        $receivers[0]["global_attributes"]["birthday"] = array(
                            "value"         =>      $birthday,
                            "type"          =>      'text',
                            "description"   =>      'Geburtsdatum',
                        );
                    }
                }

                $post_code = '';
                if (!empty($input_fields['post_code'])) {
                    $post_code = $input_fields['post_code'];
                    if ($newsletter_version == 1) {
                        $output_profile['PLZ'] = $post_code;
                    } else {
                        $receivers[0]["global_attributes"]["post_code"] = array(
                            "value"         =>      $post_code,
                            "type"          =>      'text',
                            "description"   =>      'PLZ',
                        );
                    }
                }

                $city = '';
                if (!empty($input_fields['city'])) {
                    $city = $input_fields['city'];
                    if ($newsletter_version == 1) {
                        $output_profile['Ort'] = $city;
                    } else {
                        $receivers[0]["global_attributes"]["city"] = array(
                            "value"         =>      $city,
                            "type"          =>      'text',
                            "description"   =>      'Ort',
                        );
                    }
                }

                //$nl_coupon = get_nl_coupon();

                if ($newsletter_version == 1) {
                    require_once(realpath(MODULE_PATH . DIRECTORY_SEPARATOR . 'dcshop') . DIRECTORY_SEPARATOR . 'newsletter_copernica' . DIRECTORY_SEPARATOR . 'copernica_rest_api.php');
                    $feedback = upsert_profile($newsletter_sitepart['db_id'], $newsletter_sitepart['access_token'], $output_profile);
                    if (!(strlen((string)$feedback) > 0)) {
                        $error2   = TRUE;
                        $message2 = $translation->get('newsletter_no_response_error') . "<br/>";
                    } else {
                        $message2 = $translation->get('newsletter_subscribe_success') . "<br/>";
                    }
                } else {
                    /** @var DynCom\dc\common\classes\CleverReachConnector $cleverReachConnector */
                    $cleverReachConnector = new DynCom\dc\common\classes\CleverReachConnector($account,$login,$password,$group);
                    if (!$cleverReachConnector->upsertReceivers($receivers) || $cleverReachConnector->isError()) {
                        $error2   = TRUE;
                        $message2 = $translation->get('newsletter_no_response_error') . "<br/>";
                    } else {
                        $message2 = $translation->get('newsletter_subscribe_success') . "<br/>";
                    }
                }

            }
        } elseif ($newsletter_sitepart['type'] == 1) {

            if (!empty($error) && $error) {
                $frontend_message = $message;
                get_requestbox($frontend_message, "", "error");
            } else {

                if ($newsletter_version == 1) {
                    $path = realpath(MODULE_PATH . DIRECTORY_SEPARATOR . 'dcshop') . DIRECTORY_SEPARATOR . 'newsletter_copernica' . DIRECTORY_SEPARATOR . 'copernica_rest_api.php';
                    include_once($path);
                    $feedback = update_existing_profile_only($db_id, $access_token, $newsletter_sitepart['segment'], $_REQUEST['input_email'], array('DOI' => 2));
                    if (!(strlen((string)$feedback) > 0)) {
                        $error2   = TRUE;
                        $message2 = $translation->get('newsletter_no_response_error') . "<br/>";
                    } else {
                        $message2 = $translation->get('newsletter_unsubscribe_success') . "<br/>";
                    }
                } else {
                    $receivers = array();
                    $receivers[] = array();
                    $receivers[0] = array(
                        "email"                         =>      $_REQUEST['input_email'],
                    );

                    /** @var DynCom\dc\common\classes\CleverReachConnector $cleverReachConnector */
                    $cleverReachConnector = new DynCom\dc\common\classes\CleverReachConnector($account,$login,$password,$group);
                    if (!$cleverReachConnector->unsubscrineReceivers($receivers) || $cleverReachConnector->isError()) {
                        $error2   = TRUE;
                        $message2 = $translation->get('newsletter_no_response_error') . "<br/>";
                    } else {
                        $message2 = $translation->get('newsletter_unsubscribe_success') . "<br/>";
                    }
                }
            }
        }

        if (!$error2) {
            $frontend_message = $message2;
            get_requestbox($frontend_message, "Newsletter", "success");
        } else {
            $frontend_message = $message2;
            get_requestbox($frontend_message, "Newsletter", "error");
        }

    }
    ?>
	<div class="sitepart_<?= $sitepart_id ?>">
		<div class="newsletter_text">
			<?= $newsletter_sitepart['text'] ?>
		</div>
        <?
        if ($newsletter_sitepart['type'] == 2 && !empty($newsletter_sitepart['forwarding_url'])) {
            $nl_action = $newsletter_sitepart['forwarding_url'];
        } else {
            $nl_action = ml($tempSitepart, "action", "send");
        }

        ?>
        <form id="<?= $classname ?>_<?= $sitepart_id ?>" name="<?= $classname ?>_<?= $sitepart_id ?>" class="newsletter_form<? if ($newsletter_sitepart['type'] == 3) { echo " two_buttons"; } ?>" method="post" action="<?= $nl_action ?>" enctype="multipart/form-data">
                <?
                if (strlen($salutation_field) > 0) {
                    echo $salutation_field;
                }
                if (strlen($first_name_field) > 0 && strlen($last_name_field) > 0) {
                    echo $first_name_field;
                    echo $last_name_field;
                } elseif (strlen($name_field) > 0) {
                    echo $name_field;
                }

                echo $email_field;

                $address_fields = $post_code_field . $city_field;
                if (strlen($address_fields) > 0) {
                    echo $address_fields;
                }

                if (strlen($birthday_field) > 0) {
                    echo $birthday_field;
                }
                ?>
            <div style="display:none;">
                <input type="text" name="input_captcha1" id="input_captcha1" value=""/>
                <input type="text" name="input_captcha2" id="input_captcha2" value="<?= time() ?>" />
                <?
                if ($newsletter_sitepart['type'] == 2 && !empty($newsletter_sitepart['forwarding_url'])) {
                    ?>
                    <input type="hidden" name="from_sitepart" id="from_sitepart" value="1"/>
                    <?
                }
                ?>
            </div>
            <br/>
            <?
            if ($newsletter_sitepart['type'] == 3) {
                ?>
                <input type="submit" name="subscribeFemale" class="button" id="button_subscribeFemale" value="<?= $translation->get('Mss.') ?>"/>
                <input type="submit" name="subscribeMale" class="button" id="button_subscribeMale" value="<?= $translation->get('Mrs.') ?>"/>
                <?
            } else {
                ?>
                <input type="submit" name="button" class="button" id="button" value="<?= $translation->get('send') ?>"/>
                <?
            }
            ?>
        </form>
	</div>
	<br />
	<?
}

function newsletter_sitepart_edit() {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_newsletter_sitepart.inc.php';
}

function show_newsletter_sitepart( $sitepart_id ) {
    newsletter_sitepart_show($sitepart_id);
}
/*
if((int) $sitepart['main_sitepart_header_id'] > 0 ){
	shop_main_show($sitepart['main_sitepart_header_id']);
}
require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_main.inc.php';*/