<?php

$time = microtime(TRUE);

$GLOBALS["curr_logfile"]         = __DIR__ . '/copernica_log.txt';
$GLOBALS['copernica_log_active'] = TRUE;
$GLOBALS['log_to_file']          = TRUE;


function copernica_api( $login = NULL, $action = array('add_update_profile'), $values = '' ) {

    $text = microtime(TRUE) . " - Copernica Master Function called with parameters Login: " . print_r($login, 1) . " | Action: " . print_r($action, 1) . " | Values: " . print_r($values, 1);
    if ($GLOBALS['output_log']) {
        echo "<!-- LOG COPERNICA:
		$text 
		-->";
    }
    if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
        log_text($text);
    }

    if (!is_array($login)) {
        $db_id        = 4;
        $access_token = 'b46ff74daed1817f60d1388ef93a2deeee426bc220b86a004019ef632feafd41c9610b353805c7661baece6f473325571907244c9bb806f830e68c7d9d507838';
    } elseif (!empty($login['db_id']) && !empty($login['access_token'])) {
        $db_id        = $login['db_id'];
        $access_token = $login['access_token'];
    }

    if (in_array('add_update_profile', $action) || (!is_array($action))) {
        if (!empty($values['profile']) && is_array($values['profile'])) {
            $param_profile = $values['profile'];
        } elseif (!empty($values['EMail'])) {
            $param_profile = $values;
        } else {
            //FEHLER
            $error           = TRUE;
            $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__;
            return;
        }
        upsert_profile($db_id, $access_token, $param_profile);
    }

    if (in_array("add_collection", $action)) {
        add_sales_collection($db_id, $access_token, $values);
    }

    if (in_array("remove_profile", $action)) {
        if (!empty($values['profile']['ID'])) {
            $profile_id = $values['profile']['ID'];
        } elseif (!empty($values['profile']['id'])) {
            $profile_id = $values['profile']['id'];
        } elseif (empty($values['profile']) && !empty($values['EMail']) && !empty($values['ID'])) {
            $profile_id = $values['ID'];
        } elseif (empty($values['profile']) && !empty($values['EMail']) && !empty($values['id'])) {
            $profile_id = $values['id'];
        } elseif (!empty($values['profile'][0]['ID'])) {
            $profile_id = $values['profile'][0]['ID'];
        } elseif (!empty($values['profile'][0]['id'])) {
            $profile_id = $values['profile'][0]['id'];
        }
        delete_profile($db_id, $access_token, $profile_id);
    }

    if (in_array("import_profiles", $action) && is_array($values['import'])) {
        $users = $values['import'];
        import_profiles($db_id, $access_token, $users);
    }

}

function get_profiles( $db_id, $access_token, $fields = NULL ) {
    $text = microtime(TRUE) . " - Function " . __FUNCTION__ . " called with parameters DB-ID: " . $db_id . " | Access Token: " . $access_token . " | Fields: " . print_r($fields, 1);
    if ($GLOBALS['output_log']) {
        echo "<!-- LOG COPERNICA:
		$text 
		-->";
    }
    if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
        log_text($text);
    }
    if (!((int)$db_id > 0)) {
        $text = "ERROR in function " . __FUNCTION__ . " - Invalid DB-ID in Line " . __LINE__ . "";
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA:
			$text 
			-->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($text);
        }
        //FEHLER
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__;
        return;
    }
    if (empty($access_token)) {
        $text = "ERROR in function " . __FUNCTION__ . " - Access Token empty in Line " . __LINE__ . "";
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA:
			$text 
			-->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($text);
        }
        //FEHLER
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__;
        return;
    }
    $base_url          = 'https://api.copernica.com/database/' . (string)((int)$db_id) . '/profiles?';
    $fields_url_string = '';
    if (is_array($fields) && count($fields) > 0) {
        foreach ($fields as $fieldname => $fieldvalue) {
            $fields_url_string .= 'fields[]=';
            $fields_url_string .= urlencode($fieldname) . urlencode('==') . urlencode($fieldvalue) . '&';
        }
    }
    $url = $base_url . $fields_url_string . 'access_token=' . $access_token;

    // set up the curl resource
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

    // execute the request

    $text = microtime(TRUE) . " - Function " . __FUNCTION__ . " Line " . __LINE__ . " - Sending Request: " . $url;
    if ($GLOBALS['output_log']) {
        echo "<!-- LOG COPERNICA:
		$text 
		-->";
    }
    if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
        log_text($text);
    }

    $output = curl_exec($ch);

    $text = microtime(TRUE) . " - Function " . __FUNCTION__ . " Line " . __LINE__ . " - Response: " . $output;
    if ($GLOBALS['output_log']) {
        echo "<!-- LOG COPERNICA: $text -->";
    }
    if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
        log_text($text);
    }

    // close curl resource to free up system resources
    curl_close($ch);

    // process output

    $output_arr = json_decode($output, TRUE);

    if (!is_array($output_arr)) {
        $text = "ERROR in function " . __FUNCTION__ . " - No output array in Line " . __LINE__ . " | Output-Raw: " . $output;
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA:
			$text 
			-->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($text);
        }
        //FEHLER
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__;
        return;
    } else {
        return $output_arr;
    }
}


function get_subprofiles( $db_id, $access_token, $profile_id, $collection_id, $fields ) {
    $text = microtime(TRUE) . " - Function " . __FUNCTION__ . " called with parameters DB-ID: " . $db_id . " | Access Token: " . $access_token . " | Fields: " . print_r($fields, 1);
    if ($GLOBALS['output_log']) {
        echo "<!-- LOG COPERNICA:
		$text 
		-->";
    }
    if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
        log_text($text);
    }
    if (!((int)$db_id > 0)) {
        $text = "ERROR in function " . __FUNCTION__ . " - Invalid DB-ID in Line " . __LINE__ . "";
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA:
			$text 
			-->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($text);
        }
        //FEHLER
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__;
        return;
    }
    if (empty($access_token)) {
        $text = "ERROR in function " . __FUNCTION__ . " - Access Token empty in Line " . __LINE__ . "";
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA:
			$text 
			-->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($text);
        }
        //FEHLER
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEINE GÜLTIGE EMAIL ANGEGEBEN";
        return;
    }
    $base_url          = 'https://api.copernica.com/profile/' . (string)((int)$profile_id) . '/subprofiles/' . (string)((int)$collection_id) . '?';
    $fields_url_string = '';
    if (is_array($fields) && count($fields) > 0) {
        foreach ($fields as $fieldname => $fieldvalue) {
            $fields_url_string .= 'fields[]=';
            $fields_url_string .= urlencode($fieldname) . urlencode('==') . urlencode($fieldvalue) . '&';
        }
    }
    $url = $base_url . $fields_url_string . 'access_token=' . $access_token;

    // set up the curl resource
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

    // execute the request

    $text = microtime(TRUE) . " - Function " . __FUNCTION__ . " Line " . __LINE__ . " - Sending Request: " . $url;
    if ($GLOBALS['output_log']) {
        echo "<!-- LOG COPERNICA:
		$text 
		-->";
    }
    if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
        log_text($text);
    }

    $output = curl_exec($ch);

    $text = microtime(TRUE) . " - Function " . __FUNCTION__ . " Line " . __LINE__ . " - Response: " . $output;
    if ($GLOBALS['output_log']) {
        echo "<!-- LOG COPERNICA: $text -->";
    }
    if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
        log_text($text);
    }

    // close curl resource to free up system resources
    curl_close($ch);

    // process output

    $output_arr = json_decode($output, TRUE);

    if (!is_array($output_arr)) {
        $text = "ERROR in function " . __FUNCTION__ . " - No output array in Line " . __LINE__ . " | Output-Raw: " . $output;
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA:
			$text 
			-->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($text);
        }
        //FEHLER
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . " | OUTPUT: " . $output;
        return;
    } else {
        return $output_arr;
    }
}


function create_profile( $db_id, $access_token, $data ) {
    $text = microtime(TRUE) . " - Function " . __FUNCTION__ . " called with parameters DB-ID: " . $db_id . " | Access Token: " . $access_token . " | Data: " . print_r($data, 1) . "";
    if ($GLOBALS['output_log']) {
        echo "<!-- LOG COPERNICA:
		$text 
		-->";
    }
    if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
        log_text($text);
    }
    if (!((int)$db_id > 0)) {
        $text = "ERROR in function " . __FUNCTION__ . " - Invalid DB-ID in Line " . __LINE__ . "";
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA:
			$text 
			-->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($text);
        }
        //FEHLER keine DB-ID
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEINE DB-ID ANGEGEBEN";
        return;
    }
    if (empty($access_token)) {
        $text = "ERROR in function " . __FUNCTION__ . " - Access Token empty in Line " . __LINE__ . "";
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA:
			$text 
			-->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($text);
        }
        //FEHLER kein access token
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEIN ACCESS TOKEN ANGEGEBEN";
        return;
    }
    if (!is_array($data)) {
        //FEHLER keine Felder
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEINE DATEN ANGEGEBEN";
        return;
    }
    if (empty($data['EMail']) || !(filter_var($data['EMail'], FILTER_VALIDATE_EMAIL))) {
        //FEHLER keine gültige EMail
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEINE GÜLTIGE EMAIL ANGEGEBEN";
        return;
    }

    $data['Mail_Domain'] = substr(strrchr($data['EMail'], "@"), 1);

    /*
    foreach($data AS $key => &$value) {
        $value = utf8_decode($value);
    }
    */

    $data_string = json_encode($data);
    utf8_decode($data_string);

    // set up the curl resource
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.copernica.com/database/' . (string)((int)$db_id) . '/profiles?access_token=' . $access_token);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string)
    ));

    $text = microtime(TRUE) . " - Function " . __FUNCTION__ . " Line " . __LINE__ . " - Sending Request: " . "https://api.copernica.com/database/" . (string)((int)$db_id) . "/profiles?access_token=" . $access_token . "\r\n Data: " . print_r($data, 1);
    if ($GLOBALS['output_log']) {
        echo "<!-- LOG COPERNICA:
		$text 
		-->";
    }
    if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
        log_text($text);
    }

    // execute the request
    $output = curl_exec($ch);
    $text   = microtime(TRUE) . " - Function " . __FUNCTION__ . " Line " . __LINE__ . " - Response: " . $output;
    if ($GLOBALS['output_log']) {
        echo "<!-- LOG COPERNICA: $text -->";
    }
    if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
        log_text($text);
    }
    // close curl resource to free up system resources
    curl_close($ch);

    $headers = get_headers_from_curl_response($output);
    if (empty($headers[0]['Location']) || !(strlen($headers[0]['Location']) > 0)) {

        $text = "Creating Profile failed - Output: " . $output;
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA: $text -->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($text);
        }
        //FEHLER
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEINE PROFIL-URI IN ANTWORT";
        return;
    } else {
        preg_match('/(?<=profile\/)([\d]+)/', $headers[0]['Location'], $profile_id);
        if ((int)$profile_id > 0) {
            $text = "Creating Profile succeeded - Location: " . $headers[0]['Location'];
            if ($GLOBALS['output_log']) {
                echo "<!-- LOG COPERNICA: $text -->";
            }
            if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
                log_text($text);
            }
            return $profile_id;
        } else {
            //FEHLER
            $error           = TRUE;
            $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEINE PROFIL-ID IN LOCATION-FELD AUS ANTWORT";
            return;
        }
    }

}

function create_subprofile( $db_id, $access_token, $profile_id, $collection_id, $fields ) {
    if (!((int)$db_id > 0)) {
        $text = "ERROR in function " . __FUNCTION__ . " - Invalid DB-ID in Line " . __LINE__ . "";
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA:
			$text 
			-->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($text);
        }
        //FEHLER keine DB-ID
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEINE DB-ID ANGEGEBEN";
        return;
    }
    if (empty($access_token)) {
        $text = "ERROR in function " . __FUNCTION__ . " - Access Token empty in Line " . __LINE__ . "";
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA:
			$text 
			-->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($text);
        }
        //FEHLER kein access token
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEIN ACCESS TOKEN ANGEGEBEN";
        return;
    }
    if (!((int)$profile_id > 0)) {
        //FEHLER keine Profil-ID
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEIN PROFIL ANGEGEBEN";
        return;
    }
    if (!((int)$collection_id > 0)) {
        //FEHLER keine Profil-ID
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEINE KOLLEKTION ANGEGEBEN";
        return;
    }
    if (!is_array($fields)) {
        //FEHLER keine Felder
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEINE FELDER ANGEGEBEN";
        return;
    }
    $url = 'https://api.copernica.com/profile/' . (string)((int)$profile_id) . '/subprofiles/' . (string)((int)$collection_id) . '?access_token=' . $access_token;

    /*
    foreach($fields as $key => &$value) {
        $value = utf8_decode($value);
    }
    */

    $fields_string = json_encode($fields);
    utf8_decode($fields_string);


    $text = "Creating Subprofile\r\n\tFields: " . $fields_string;
    if ($GLOBALS['output_log']) {
        echo "<!-- LOG COPERNICA: $text -->";
    }
    if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
        log_text($text);
    }
    // set up the curl resource
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($fields_string)
    ));

    $text = microtime(TRUE) . " - Function " . __FUNCTION__ . " Line " . __LINE__ . " - Sending Request: " . $url . "\r\n Fields: " . $fields_string;
    if ($GLOBALS['output_log']) {
        echo "<!-- LOG COPERNICA:
		$text 
		-->";
    }
    if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
        log_text($text);
    }

    // execute the request
    $output = curl_exec($ch);

    $text = microtime(TRUE) . " - Function " . __FUNCTION__ . " Line " . __LINE__ . " - Response: " . $output;
    if ($GLOBALS['output_log']) {
        echo "<!-- LOG COPERNICA: $text -->";
    }
    if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
        log_text($text);
    }
    // close curl resource to free up system resources
    curl_close($ch);

    $headers = get_headers_from_curl_response($output);
    if (empty($headers[0]['Location']) || !(strlen($headers[0]['Location']) > 0)) {
        //FEHLER
        $text = "Creating Subprofile failed\r\n\tResponse: " . $output;
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA: $text -->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($text);
        }
        die("FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . " | URL: " . $url . " | Fields: " . $fields_string . " | HEADERS: " . print_r($headers[0], 1) . " | OUTPUT-RAW: " . $output);
    } else {
        preg_match('/(?<=subprofile\/)([\d]+)/', $headers[0]['Location'], $subprofile_id);
        if ((int)$subprofile_id > 0) {
            $text = "Creating Subprofile succeeded\r\n\tLocation: " . $headers[0]['Location'];
            if ($GLOBALS['output_log']) {
                echo "<!-- LOG COPERNICA: $text -->";
            }
            if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
                log_text($text);
            }
            return $subprofile_id;
        } else {
            //FEHLER
            $error           = TRUE;
            $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->UNTERPROFIL WURDE NICHT ERFOLGREICH ERSTELLT";
            return;
        }
    }
}

function update_profile( $db_id, $access_token, $select_array, $update_data ) {
    if (!((int)$db_id > 0)) {
        $text = "ERROR in function " . __FUNCTION__ . " - Invalid DB-ID in Line " . __LINE__ . "";
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA:
			$text 
			-->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($text);
        }
        //FEHLER
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEIN PROFIL MIT EMAIL ANGEGEBEN";
        return;
    }
    if (empty($access_token)) {
        $text = "ERROR in function " . __FUNCTION__ . " - Access Token empty in Line " . __LINE__ . "";
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA:
			$text 
			-->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($text);
        }
        //FEHLER
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEIN ACCESS TOKEN ANGEGEBEN";
        return;
    }
    if (empty($update_data) || !(count($update_data) > 0)) {
        //FEHLER
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEINE UPDATE-DATEN ANGEGEBEN";
        return;
    }

    /*
    foreach($update_data AS $key => &$value) {
        $value = utf8_decode($value);
    }*/

    $data_string = json_encode($update_data);

    utf8_decode($data_string);

    $select_url_string = '';
    if (is_array($select_array) && !empty($select_array)) {
        $select_array['limit'] = 1;
        $select_url_string = '';
        foreach ($select_array as $key => $value) {
            if (!empty($value)) {
                $select_url_string .= 'fields[]=' . urlencode($key . '==' . $value) . '&';
            }
        }
    }

    $url = 'https://api.copernica.com/database/' . (string)((int)$db_id) . '/profiles/?' . $select_url_string . 'access_token=' . $access_token;

    $text = "Updating Profiles\r\n\tURL: " . $url . "\r\n\tData: " . $data_string;
    if ($GLOBALS['output_log']) {
        echo "<!-- LOG COPERNICA: $text -->";
    }
    if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
        log_text($text);
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); // note the PUT here

    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string)
    ));

    $text = microtime(TRUE) . " - Function " . __FUNCTION__ . " Line " . __LINE__ . " - Sending Request: " . $url . "\r\n Data: " . $data_string;
    if ($GLOBALS['output_log']) {
        echo "<!-- LOG COPERNICA:
		$text 
		-->";
    }
    if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
        log_text($text);
    }

    // execute the request
    $output = curl_exec($ch);
    $text   = microtime(TRUE) . " - Function " . __FUNCTION__ . " Line " . __LINE__ . " - Response: " . $output;
    if ($GLOBALS['output_log']) {
        echo "<!-- LOG COPERNICA: $text -->";
    }
    if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
        log_text($text);
    }
    // close curl resource to free up system resources

    curl_close($ch);

    $text = "Response: " . $output;
    if ($GLOBALS['output_log']) {
        echo "<!-- LOG COPERNICA: $text -->";
    }
    if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
        log_text($text);
    }

    return $output;

}


function upsert_profile( $db_id, $access_token, $param_profile ) {

    $text = microtime(TRUE) . " - Function " . __FUNCTION__ . " called with parameters DB-ID: " . $db_id . " | Access Token: " . $access_token . " | Profile: " . print_r($profile, 1);
    if ($GLOBALS['output_log']) {
        echo "<!-- LOG COPERNICA:
		$text 
		-->";
    }
    if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
        log_text($text);
    }

    if (!((int)$db_id > 0)) {
        $text = "ERROR in function " . __FUNCTION__ . " - Invalid DB-ID in Line " . __LINE__ . "";
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA:
			$text 
			-->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($text);
        }
        //FEHLER
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEINE DB-ID ANGEGEBEN";
        return;
    }
    if (empty($access_token)) {
        $text = "ERROR in function " . __FUNCTION__ . " - Access Token empty in Line " . __LINE__ . "";
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA:
			$text 
			-->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($text);
        }
        //FEHLER
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEIN ACCESS TOKEN ANGEGEBEN";
        return;
    }
    if (empty($param_profile['EMail'])) {
        //FEHLER
        die("FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . " | Profile: " . print_r($param_profile, 1));
    }
    $profile_raw = get_profiles($db_id, $access_token, array('EMail' => $param_profile['EMail'], 'Segment' => $param_profile['Segment']));
    $text        = "Function " . __FUNCTION__ . " - Selected Profiles for Update: " . print_r($profile_raw, 1);
    if ($GLOBALS['output_log']) {
        echo "<!-- LOG COPERNICA: $text -->";
    }
    if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
        log_text($text);
    }
    $no_of_profiles = (int)$profile_raw['total'];
    unset($profiles);
    if ($no_of_profiles == 1) {
        $profiles = array($profile_raw['data']);
    } elseif ($no_of_profiles > 1) {
        $profiles = $profile_raw['data'];
    }
    $feedback = "";
    if ($no_of_profiles > 0) {
        unset($profile);
        foreach ($profiles as $profile) {
            if (!empty($profile[0]['ID']) && $profile[0]['ID'] > 0) {
                $profile_id    = $profile[0]['ID'];
                $profile       = $profile[0]['fields'];
                $profile['ID'] = $profile_id;
            } elseif (!empty($profile['ID']) && $profile['ID'] > 0) {
                $profile_id    = $profile['ID'];
                $profile       = $profile['fields'];
                $profile['ID'] = $profile_id;
            }
            $text = "\tProfile: " . json_encode($profile);
            if ($GLOBALS['output_log']) {
                echo "<!-- LOG COPERNICA: $text -->";
            }
            if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
                log_text($text);
            }

            // ein bestellprozess darf keinen bestätigten Double Opt in überschreiben

            if ((int)$param_profile['OOI'] >= 1) {
                if ((int)$profile['DOI'] == 2) {
                    $param_profile['DOI'] = 0;
                } else {
                    unset($param_profile['DOI']);
                }
            }

            // ein bereits bestehender order-opt-in oder SOI darf nicht überschrieben werden
            if ((int)$param_profile['OOI'] == 2 && (int)$profile['OOI'] >= 1) {
                unset($param_profile['OOI']);
                if ((int)$profile['SOI'] > 0) {
                    unset($param_profile['SOI']);
                }
            }

            //keinen neuen Gutscheincode übermitteln
            unset($param_profile["CouponCode"]);


            if (($param_profile['SOI'] == 1) ||
                ($param_profile['DOI'] == 2) ||
                ((empty($param_profile['SOI']) ||
                        !($param_profile['SOI'] == 1)) &&
                    (empty($param_profile['DOI']) ||
                        !($param_profile['DOI'] == 2)
                    )
                )
            ) {
                $text = "\tUpdating Profile " . json_encode($profile) . "\r\n\tData: " . json_encode($param_profile);
                if ($GLOBALS['output_log']) {
                    echo "<!-- LOG COPERNICA: $text -->";
                }
                if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
                    log_text($text);
                }
                $feedback = update_profile($db_id, $access_token, array('ID' => $profile['ID']), $param_profile);

                $text = "\tResponse: " . $feedback;
                if ($GLOBALS['output_log']) {
                    echo "<!-- LOG COPERNICA: $text -->";
                }
                if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
                    log_text($text);
                }
            }


            if (empty($feedback)) {
                //FEHLER
                $error           = TRUE;
                $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEINE ANTWORT VON API ERHALTEN";
                return;
            }
        }
    } elseif (($no_of_profiles == 0) && $param_profile['DOI'] != 2) {

        //Kein Profil in der Datenbank & Double-Opt-In != unsubscribe => neues Profil
        $text = "\tNo Profile found for E-Mail " . $param_profile['EMail'] . ", Segment " . $param_profile['Segment'] . " - creating new profile. ";
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA: $text -->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($text);
        }
        $param_profile['DOI'] = 0;
        $nl_coupon = get_nl_coupon();
        $param_profile['CouponCode'] = $nl_coupon;
        $newprofile_id        = create_profile($db_id, $access_token, $param_profile);
        $feedback             = $newprofile_id;
        //$error = true;
        $errormessages[] = 'NEWPROFILE: ' . $newprofile_id;
    }

    return $feedback;
}


function import_profiles( $db_id, $access_token, $users ) {
    if (!((int)$db_id > 0)) {
        $text = "ERROR in function " . __FUNCTION__ . " - Invalid DB-ID in Line " . __LINE__ . "";
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA:
			$text 
			-->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($text);
        }
        //FEHLER
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEINE DB-ID ANGEGEBEN";
        return;
    }
    if (empty($access_token)) {
        $text = "ERROR in function " . __FUNCTION__ . " - Access Token empty in Line " . __LINE__ . "";
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA:
			$text 
			-->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($text);
        }
        //FEHLER
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEIN ACCESS TOKEN ANGEGEBEN";
        return;
    }
    if (!is_array($users)) {
        //FEHLER
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEINE PROFILE ZUM IMPORTIEREN";
        return;
    }
    foreach ($users as $user) {
        $data_string = json_encode($user);

        $profiles_raw = get_profiles($db_id, $access_token, array('EMail' => $user['EMail']));
        if (isset($profiles_raw['total'])) {
            $no_of_profiles = (int)$profiles_raw['total'];
            if ($no_of_profiles == 1) {
                $profiles = array($profiles_raw['data']);
            } elseif ($no_of_profiles > 1) {
                $profiles = $profiles_raw['data'];
            }
            if ($no_of_profiles > 0) {
                foreach ($profiles as $profile) {
                    if (!empty($profile[0]['ID']) && $profile[0]['ID'] > 0) {
                        $profile_id    = $profile[0]['ID'];
                        $profile       = $profile[0]['fields'];
                        $profile['ID'] = $profile_id;
                    } elseif (!empty($profile['ID']) && $profile['ID'] > 0) {
                        $profile_id    = $profile['ID'];
                        $profile       = $profile['fields'];
                        $profile['ID'] = $profile_id;
                    }
                    if ($user['overwrite']) {
                        update_profile($db_id, $access_token, array('ID' => $profile['ID']), $user);
                    }
                }
            } else {
                $newprofile_id = create_profile($db_id, $access_token, $user);
            }

        } else {
            //FEHLER
            $error           = TRUE;
            $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__;
            return;
        }
    }
}


function delete_profile( $db_id, $access_token, $profile_id ) {
    if (!((int)$db_id > 0)) {
        $text = "ERROR in function " . __FUNCTION__ . " - Invalid DB-ID in Line " . __LINE__ . "";
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA:
			$text 
			-->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($text);
        }
        //FEHLER
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__;
        return;
    }
    if (empty($access_token)) {
        $text = "ERROR in function " . __FUNCTION__ . " - Access Token empty in Line " . __LINE__ . "";
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA:
			$text 
			-->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($text);
        }
        //FEHLER
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__;
        return;
    }
    if (!((int)$profile_id > 0)) {
        //FEHLER
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__;
        return;
    }
    $url = 'https://api.copernica.com/profile/' . (string)((int)($profile_id)) . '?access_token=' . $access_token;

    // set up the curl resource
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.copernica.com/profile/$profileID?access_token=$token");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

    $text = microtime(TRUE) . " - Function " . __FUNCTION__ . " Line " . __LINE__ . " - Sending Request: " . "https://api.copernica.com/profile/$profileID?access_token=$token" . " - DELETE";
    if ($GLOBALS['output_log']) {
        echo "<!-- LOG COPERNICA:
		$text 
		-->";
    }
    if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
        log_text($text);
    }

    // execute the request
    $output = curl_exec($ch);

    $text = microtime(TRUE) . " - Function " . __FUNCTION__ . " Line " . __LINE__ . " - Response: " . $output;
    if ($GLOBALS['output_log']) {
        echo "<!-- LOG COPERNICA: $text -->";
    }
    if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
        log_text($text);
    }

    // close curl resource to free up system resources
    curl_close($ch);

    $headers = get_headers_from_curl_response($output);
    if (empty($headers[0]['X-Deleted'])) {
        //FEHLER
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\PROFIL WURDE NICHT ERFOLGREICH GELÖSCHT";
        return;
    } else {
        return TRUE;
    }
}

function get_collections( $db_id, $access_token, $identifier = NULL ) {
    if (!((int)$db_id > 0)) {
        $text = "ERROR in function " . __FUNCTION__ . " - Invalid DB-ID in Line " . __LINE__ . "";
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA:
			$text 
			-->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($text);
        }
        //FEHLER
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEINE DB-ID ANGEGEBEN";
        return;
    }
    if (empty($access_token)) {
        $text = "ERROR in function " . __FUNCTION__ . " - Access Token empty in Line " . __LINE__ . "";
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA:
			$text 
			-->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($text);
        }
        //FEHLER
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__;
        return;
    }
    $url = 'https://api.copernica.com/database/' . (string)((int)$db_id) . '/collections/?access_token=' . $access_token;

    // set up the curl resource
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

    $text = microtime(TRUE) . " - Function " . __FUNCTION__ . " Line " . __LINE__ . " - Sending Request: " . $url;
    if ($GLOBALS['output_log']) {
        echo "<!-- LOG COPERNICA:
		$text 
		-->";
    }
    if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
        log_text($text);
    }

    // execute the request
    $output = curl_exec($ch);

    $text = microtime(TRUE) . " - Function " . __FUNCTION__ . " Line " . __LINE__ . " - Response: " . $output;
    if ($GLOBALS['output_log']) {
        echo "<!-- LOG COPERNICA: $text -->";
    }
    if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
        log_text($text);
    }

    // close curl resource to free up system resources
    curl_close($ch);

    $output_arr = json_decode($output, TRUE);
    if (!isset($output_arr['total']) || !((int)$output_arr['total'] > 0)) {
        //FEHLER
        die("FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . " | URL: " . $url . " | OUTPUT-RAW: " . $output . " | OUTPUT-ARR: " . print_r($output_arr, 1));
    } elseif (isset($output_arr['total']) && !((int)$output_arr['total'] > 0)) {
        return FALSE;
    } else {
        $no_of_collections = $output_arr['total'];
        if ($no_of_collections == 1) {
            $collections = array($output_arr['data']);
        } else {
            $collections = $output_arr['data'];
        }
        if (!empty($identifier)) {
            foreach ($collections as $collection) {
                if (empty($collection['ID']) && !empty($collection[0]['ID'])) {
                    $collection = $collection[0];
                }
                if ((trim($collection['name']) == trim($identifier)) || ($collection['ID'] == (int)$identifier)) {
                    return $collection;
                }
            }
        } else {
            return $collections;
        }
    }
}


function add_sales_collection( $db_id, $access_token, $data ) {

    if (!((int)$db_id > 0)) {
        $text = "ERROR in function " . __FUNCTION__ . " - Invalid DB-ID in Line " . __LINE__ . "";
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA:
			$text 
			-->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($text);
        }
        //FEHLER keine DB-ID
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEINE DB-ID ANGEGEBEN";
        return;
    }
    if (empty($access_token)) {
        $text = "ERROR in function " . __FUNCTION__ . " - Access Token empty in Line " . __LINE__ . "\r\n\t\t-->\tKEIN ACCESS TOKEN ANGEGEBEN";
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA:
			$text 
			-->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($text);
        }
        //FEHLER kein access_token
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__;
        return;
    }
    if (!empty($data['profile']['EMail']) && !empty($data['profile']['Segment']) && !empty($data['item']) && !empty($data['order'])) {

        $profiles_raw = get_profiles($db_id, $access_token, array('EMail' => $data['profile']['EMail'], 'Segment' => $data['profile']['Segment']));

        if (!empty($profiles_raw['total']) && ((int)$profiles_raw['total'] > 0)) {
            $no_of_profiles = $profiles_raw['total'];
            $maxindex       = $no_of_profiles - 1;
            if ($maxindex == 0) {
                $profiles = array($profiles_raw['data']);
            } else {
                $profiles = $profiles_raw['data'];
            }
            for ($i = 0; $i <= $maxindex; $i++) {
                $profile = $profiles[$i];
                if (!empty($profile[0]['ID']) && $profile[0]['ID'] > 0) {
                    $profile_id    = $profile[0]['ID'];
                    $profile       = $profile[0]['fields'];
                    $profile['ID'] = $profile_id;
                } elseif (!empty($profile['ID']) && $profile['ID'] > 0) {
                    $profile_id    = $profile['ID'];
                    $profile       = $profile['fields'];
                    $profile['ID'] = $profile_id;
                }

                //Artikelkollektion holen und subProfil für Artikel anlegen
                $item_collection = get_collections($db_id, $access_token, 'Artikel');

                if (($item_collection === FALSE) || (empty($item_collection['ID'])) || !((int)$item_collection['ID'] > 0)) {
                    //FEHLER
                    $error           = TRUE;
                    $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . " KEINE ARTIKELKOLLEKTION VORHANDEN";
                    return;
                }

                $j = 0;
                foreach ($data['item'] as $item) {
                    ++$j;
                    unset($item_data);
                    $item_data      = array(
                        'Artikelnr'     => $item['Artikelnr'],
                        'Artikelname'   => $item['Artikelname'],
                        'Kategoriecode' => $item['Kategoriecode'],
                        'Kategoriename' => $item['Kategoriename'],
                        'Bestellnr'     => $item['Bestellnr'],
                        'Artikelpreis'  => $item['Artikelpreis'],
                        'Menge'         => $item['Menge'],
                        'Waehrungscode' => $data['order']['Waehrungscode'],
                        'Variantencode' => $data['Variantencode'],
                        'Datum'         => date("Y-m-d")
                    );
                    $subprofile_raw = get_subprofiles($db_id, $access_token, $profile['ID'], $item_collection['ID'], array('Bestellnr' => $item_data['Bestellnr'], 'Artikelnr' => $item_data['Artikelnr']));
                    $text           = "Function " . __FUNCTION__ . " - Selected Subprofiles: " . print_r($subprofile_raw, 1);
                    if ($GLOBALS['output_log']) {
                        echo "<!-- LOG COPERNICA: $text -->";
                    }
                    if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
                        log_text($text);
                    }
                    $no_of_subprofiles = (int)$profile_raw['total'];

                    //Nur übertragen wenn nicht vorhanden
                    if (!($no_of_subprofiles >= 1)) {
                        $item_subprofile_id = create_subprofile($db_id, $access_token, $profile['ID'], $item_collection['ID'], $item_data);
                        if (empty($item_subprofile_id) || !((int)$item_subprofile_id > 0)) {
                            //FEHLER
                            $error           = TRUE;
                            $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tPROFIL NICHT ERFOLGREICH ANGELEGT";
                            return;
                        }
                    }
                }

                //Bestellkollektion holen und subProfil für Bestellung anlegen
                $order_collection = get_collections($db_id, $access_token, 'Bestellungen');
                if (($order_collection === FALSE) || (empty($order_collection['ID'])) || !((int)$order_collection['ID'] > 0)) {//FEHLER
                    $error           = TRUE;
                    $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . " KEINE BESTELLKOLLEKTION VORHANDEN";
                    return;
                }
                unset($order_data);
                $order_data     = array(
                    'Bestellnr'     => $data['order']['Bestellnr'],
                    'Warenwert'     => $data['order']['Warenwert'],
                    'Gesamtbetrag'  => $data['order']['Gesamtbetrag'],
                    'Waehrungscode' => $data['order']['Waehrungscode'],
                    'Datum'         => date("Y-m-d")
                );
                $subprofile_raw = get_subprofiles($db_id, $access_token, $profile['ID'], $item_collection['ID'], array('Bestellnr' => $item_data['Bestellnr']));
                $text           = "Function " . __FUNCTION__ . " - Selected Subprofiles: " . print_r($subprofile_raw, 1);
                if ($GLOBALS['output_log']) {
                    echo "<!-- LOG COPERNICA: $text -->";
                }
                if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
                    log_text($text);
                }
                $no_of_subprofiles = (int)$profile_raw['total'];

                //Nur übertragen wenn nicht vorhanden
                if (!($no_of_subprofiles >= 1)) {
                    $order_subprofile_id = create_subprofile($db_id, $access_token, $profile['ID'], $order_collection['ID'], $order_data);
                    if (empty($order_subprofile_id) || !((int)$order_subprofile_id > 0)) {
                        //FEHLER
                        $error           = TRUE;
                        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\PROFIL WURDE NICHT ERFOLGREICH ANGELEGT";
                        return;
                    }
                }
            }
        } else {
            //FEHLER keine Profile
            $error           = TRUE;
            $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEINE PROFILE GEFUNDEN";
            return;
        }

    } else {
        //FEHLER kein Profil mit EMail angegeben
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEIN PROFIL MIT EMAIL ANGEGEBEN";
        return;
    }
}


function update_existing_profile_only( $db_id, $access_token, $segment, $email, $fields ) {
    if (empty($db_id) || empty($access_token)) {
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEINE LOGIN DATEN";
        $errortext       = '';
        foreach ($errormessages as $msg) {
            $errortext .= (string)$msg;
        }
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA: $errortext -->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($errortext);
        }
        return;
    }

    if (empty($segment) || empty($email) || !(filter_var($email, FILTER_VALIDATE_EMAIL))) {
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEINE PROFIL-IDENTIFIKATION.\r\n\t\t\Segment: " . $segment . " | EMail: " . $email;
        $errortext       = '';
        foreach ($errormessages as $msg) {
            $errortext .= (string)$msg;
        }
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA: $errortext -->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($errortext);
        }
        return;
    }

    if (empty($fields) || !is_array($fields)) {
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEINE UPDATE-DATEN";
        $errortext       = '';
        foreach ($errormessages as $msg) {
            $errortext .= (string)$msg;
        }
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA: $errortext -->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($errortext);
        }
        return;
    }

    $select_fields = array(
        'Segment' => $segment,
        'EMail'   => $email
    );

    $profile_raw = get_profiles($db_id, $access_token, $select_fields);
    $text        = "Function " . __FUNCTION__ . " - Selected Profiles for Update: " . print_r($profile_raw, 1);
    if ($GLOBALS['output_log']) {
        echo "<!-- LOG COPERNICA: $text -->";
    }
    if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
        log_text($text);
    }
    $no_of_profiles = (int)$profile_raw['total'];


    if ($no_of_profiles != 1) {
        $error           = TRUE;
        $errormessages[] = "FEHLER in Datei: " . __FILE__ . " | Funktion: " . __FUNCTION__ . " | Zeile: " . __LINE__ . "\r\n\t\t-->\tKEIN EINDEUTIGES PROFIL";
        $errortext       = '';
        foreach ($errormessages as $msg) {
            $errortext .= (string)$msg;
        }
        if ($GLOBALS['output_log']) {
            echo "<!-- LOG COPERNICA: $text -->";
        }
        if (($GLOBALS['logging_active'] || $GLOBALS['copernica_log_active']) && $GLOBALS['log_to_file']) {
            log_text($errortext);
        }
        return;
    } else {
        $feedback = update_profile($db_id, $access_token, $select_fields, $fields);
        return $feedback;
    }
}


function get_headers_from_curl_response( $headerContent ) {

    $headers = array();

    // Split the string on every "double" new line.
    $arrRequests = explode("\r\n\r\n", $headerContent);

    // Loop of response headers. The "count() -1" is to 
    //avoid an empty row for the extra line break before the body of the response.
    for ($index = 0; $index < count($arrRequests) - 1; $index++) {

        foreach (explode("\r\n", $arrRequests[$index]) as $i => $line) {
            if ($i === 0) {
                $headers[$index]['http_code'] = $line;
            } else {
                list ($key, $value) = explode(': ', $line);
                $headers[$index][$key] = $value;
            }
        }
    }

    return $headers;
}