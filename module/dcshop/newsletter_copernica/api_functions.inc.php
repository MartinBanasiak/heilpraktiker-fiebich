<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'copernica_rest_api.php';
/**
 *  Example script to use the Copernica SOAP API.
 *
 * @documentation  private
 * @version        1.3
 */
/*
$GLOBALS['newsletter_keys'] = array("visitor_salutation_title"	=> "Anrede",
									"visitor_birthday" 			=> "Geburtsdatum",
									"visitor_surname"			=> "Vorname",
									"visitor_lastname"			=> "Nachname",
									"visitor_name"				=> "Name",
									"visitor_email"				=> "EMail",
									"visitor_post_code"			=> "PLZ",
									"visitor_city" 				=> "Ort",
									"newsletter_subscribe" 		=> "OOI",
									
									"item_no"				 	=> "Artikelnr",
									"description"				=> "Artikelname",
									"line_no"				 	=> "Kategoriecode",
									"name"				 		=> "Kategoriename");									
*/
$GLOBALS['newsletter_database_fields'] = array(
    'Anrede',
    'Anrede_formell',
    'Anrede_informell',
    'Segment',
    'Vorname',
    'Nachname',
    'EMail',
    'Name',
    'Geburtsdatum',
    'PLZ',
    'Ort',
    'DOI',
    'SOI',
    'OOI',
    'Leadscore',
    'LetzterKlick',
    'DatumAnmeldung',
    'DatumAbmeldung',
    'DatumGutschein',
    'Quelle',
    'Bestellnr',
    'Datum',
    'Warenwert',
    'Gesamtbetrag',
    'Artikelnr',
    'Artikelname',
    'Kategoriecode',
    'Kategoriename',
    'Menge',
    'Artikelpreis',
    'Mail_Domain',
    'Variantencode',
    'Waehrungscode',
    'Sprachcode'
);

$GLOBALS['newsletter_keys'] = array(
    "salutation_title"        => "Anrede",
    "birthday"                => "Geburtsdatum",
    "sur_name"                => "Vorname",
    "last_name"               => "Nachname",
    "bill_to_name"            => "Name",
    "user_email"              => "EMail",
    "bill_to_post_code"       => "PLZ",
    "bill_to_city"            => "Ort",
    "newsletter_registration" => "OOI",
    "currency_code"           => "Waehrungscode",
    "language_code"           => "Sprachcode",
    "item_no"                 => "Artikelnr",
    "variant_code"            => "Variantencode",
    "description"             => "Artikelname",
    "line_no"                 => "Kategoriecode",
    "name"                    => "Kategoriename",
    "quantity"                => "Menge",
    "unit_price"              => "Artikelpreis"
);

require_once __DIR__ . '/soapclient.php';


function new_array_keys( $arr, $keys = array("key_old" => "key_new"), $strict = FALSE ) {
    foreach ($arr as $key => $val) {
        if (array_key_exists($key, $keys)) {
            $new_key         = $keys[$key];
            $lines[$new_key] = $val;
            unset($new_key);
        } else {
            if ($strict == FALSE) {
                $lines[$key] = $val;
            }
        }
    }
    return $lines;
}

function array2values( $arr, $use_fields = NULL ) {
    if (!is_array($use_fields) || empty($use_fields)) {
        $use_fields = $GLOBALS['newsletter_database_fields'];
    }
    foreach ($arr as $key => $val) {
        if (in_array($key, $use_fields)) {
            $lines[$key] = $val;
        }
    }
    if ($lines['Name'] == '' && ($lines['Vorname'] != '' || $lines['Nachname'] != '')) {
        $lines['Name'] = $lines['Vorname'] . ' ' . $lines['Nachname'];
    }
    return $lines;
}

function copernicaAPI( $login = '', $action = array('add_update_profile'), $values = '' ) {

    if (!is_array($action)) {
        $action = array('add_update_profile');
    }
    if (!is_array($login)) {
        $login['email']    = 'kontakt@dc-solution.de';
        $login['password'] = 'IG#2013x';
        $login['account']  = 'dynamiccommerce';
        $login['url']      = 'http://mailing.dc-solution.de/';
        $login['charset']  = 'iso-8859-1';
        $login['verbose']  = FALSE;
        $login['database'] = 'Vorlage';
    }

    try {
        $email    = $login['email'];
        $password = $login['password'];
        $account  = $login['account'];
        $url      = $login['url'];
        $charset  = $login['charset'];
        $verbose  = $login['verbose'];

        /**
         *  Instantiate SOAP api client
         */
        if ($verbose) {
            echo "<pre>START\n----------------------------------------\n";
        }
        if ($verbose) {
            echo("[DO]:      Make connection to SOAP environment\n");
        }
        $soapclient = new PomSoapClient($email, $password, $account, $url, $charset);

        if (in_array("debug", $action)) {
            // debug information
            if ($verbose) {
                echo("[ERRORS]:  ");
            }
            if ($verbose) {
                var_dump($soapclient->__getLastResponse());
            }
        }

        if ($verbose) {
            echo("[DO]:      Check if database exists\n");
        }

        $database = $soapclient->Account_database(array(
            'identifier' => $login['database']
        ));

        if (!is_object($database)) {
            if ($verbose) {
                echo("[ERROR]:   Database '" . $login['database'] . "' doesn't exist \n");
            }
            exit;
        } else {
            if ($verbose) {
                echo("[SUCCESS]: Database '" . $login['database'] . "' exists \n");
            }
        }


        ### WORKING!
        #	=> $values['profile']
        if (in_array("add_update_profile", $action)) {
            if ($verbose) {
                echo("[DO]:      Working on profile (ADD/UPDATE) '" . $values['profile']['EMail'] . "'\n");
            }

            if (is_array($values['profile'])) {

                if ($verbose) {
                    echo("[DO]:      Searching profile '" . $values['profile']['EMail'] . "'\n");
                }


                $profiles = $soapclient->Database_searchProfiles(array(
                    'id'           => $database->id,
                    'requirements' => array(
                        $soapclient->toObject(array(
                            'fieldname' => 'EMail',
                            'value'     => $values['profile']['EMail'],
                            'operator'  => '='
                        )) /* ,
			                                    $soapclient->toObject(array(
			                                        'fieldname'     =>  'OOI',
			                                        'value'         =>  '0',
			                                        'operator'      =>  $values['profile']['OOI_operator']
			                                    )) */
                    )
                ));

                $pi = 0;
                foreach ($profiles->items as $profile) {
                    $pi++;
                    if ($verbose) {
                        echo("[FOUND]:   ID " . $profile->id . "\n");
                    }

                    // ein bestellprozess darf keinen bestätigten Double Opt in überschreiben
                    if ($values['profile']['OOI'] >= 1) {
                        if ($profile->DOI == 2) {
                            $values['profile']['DOI'] = 0;
                        } else {
                            unset($values['profile']['DOI']);
                        }

                    }

                    // if subscribe
                    if ($values['profile']['SOI'] == 1) {
                        // Complete update of the profile
                        $soapclient->Profile_updateFields(array(
                            'id'     => $profile->id,
                            'fields' => $values['profile']
                        ));
                        if ($verbose) {
                            echo("[UPDATED]: ID " . $profile->id . " (subscribe / all fields) \n");
                        }
                    }

                    // if unsubscribe
                    if ($values['profile']['DOI'] == 2) {

                        // Complete update of the profile
                        $soapclient->Profile_updateFields(array(
                            'id'     => $profile->id,
                            'fields' => array(
                                'DOI'            => $values['profile']['DOI'],
                                'DatumAbmeldung' => $values['profile']['DatumAbmeldung']
                            )
                        ));
                        if ($verbose) {
                            echo("[UPDATED]: ID " . $profile->id . " (unsubscribe / DOI+DatumAbmeldung) \n");
                        }

                    }
                }

                // $pi == Anzahl der gefundenen Einträge
                // $pi == 0 && DOI != unsubscribe { create Profile }

                if ($pi == 0 && $values['profile']['DOI'] != 2) {
                    if ($verbose) {
                        echo("[DO]:      0 profiles found so creating new one: '" . $values['profile']['EMail'] . "'\n");
                    }
                    $values['profile']['DOI'] = 0;
                    $profile1                 = $soapclient->Database_createProfile(array(
                        'id'     => $database->id,
                        'fields' => $values['profile']
                    ));
                }

            } else {
                if ($verbose) {
                    echo('[ERROR]:   $values[profile] is empty!' . "\n");
                }
            }
        }

        // Sammlung erweitern: "Artikel + Bestellungen"
        if (in_array("add_collection", $action)) {
            if ($verbose) {
                echo("[DO]:      Working on profile (COLLECTIONS) '" . $values['profile']['EMail'] . "'\n");
            }

            if (is_array($values['profile'])) {

                if ($verbose) {
                    echo("[DO]:      Searching profile '" . $values['profile']['EMail'] . "'\n");
                }

                $profiles = $soapclient->Database_searchProfiles(array(
                    'id'           => $database->id,
                    'requirements' => array(
                        $soapclient->toObject(array(
                            'fieldname' => 'EMail',
                            'value'     => $values['profile']['EMail'],
                            'operator'  => '='
                        )) /* ,
			                                    $soapclient->toObject(array(
			                                        'fieldname'     =>  'OOI',
			                                        'value'         =>  '0',
			                                        'operator'      =>  '!='
			                                    )) */
                    )
                ));

                foreach ($profiles->items as $profile) {
                    if ($verbose) {
                        echo("[FOUND]:   ID " . $profile->id . "\n");
                    }

                    $collection = $soapclient->Database_collection(array(
                        'id'         => $database->id,
                        'identifier' => "Artikel"
                    ));
                    if ($verbose) {
                        echo('[FOUND]:   Colection ID: ' . $collection->id . "\n");
                    }

                    $ii = 0;
                    foreach ($values["item"] as $item) {
                        $ii++;
                        $child[$ii] = $soapclient->Profile_createSubProfile(array(
                            'id'         => $profile->id,
                            'collection' => $soapclient->toObject(array(
                                'id' => $collection->id
                            )),
                            'fields'     => array(
                                'Zeile'         => $ii,
                                'Artikelnr'     => $item['Artikelnr'],
                                'Artikelname'   => $item['Artikelname'],
                                'Kategoriecode' => $item['Kategoriecode'],
                                'Kategoriename' => $item['Kategoriename'],
                                'Bestellnr'     => $item['Bestellnr'],
                                'Datum'         => date("Y-m-d")
                            )
                        ));
                    }


                    $collection2 = $soapclient->Database_collection(array(
                        'id'         => $database->id,
                        'identifier' => "Bestellungen"
                    ));
                    if ($verbose) {
                        echo('[FOUND]:   Colection ID: ' . $collection2->id . "\n");
                    }
                    // var_dump($values);
                    $child['bestellung'] = $soapclient->Profile_createSubProfile(array(
                        'id'         => $profile->id,
                        'collection' => $soapclient->toObject(array(
                            'id' => $collection2->id
                        )),
                        'fields'     => array(
                            'Bestellnr'    => $values['order']['Bestellnr'],
                            'Gesamtbetrag' => $values['order']['Gesamtbetrag'],
                            'Datum'        => date("Y-m-d")
                        )
                    ));


                }

            } else {
                if ($verbose) {
                    echo('[ERROR]:   $values[profile] is empty!' . "\n");
                }
            }
        }


        if (in_array("remove_profile", $action)) {
            if ($verbose) {
                echo("[DO]:      Working on profile (REMOVE) '" . $values['profile']['EMail'] . "'\n");
            }

            if (is_array($values['profile'])) {

                if ($verbose) {
                    echo("[DO]:      Searching profile '" . $values['profile']['EMail'] . "'\n");
                }
                // Find the profile
                $profiles = $soapclient->Database_searchProfiles(array(
                    'id'           => $database->id,
                    'requirements' => array(
                        $soapclient->toObject(array(
                            'fieldname' => 'EMail',
                            'value'     => $values['profile']['EMail'],
                            'operator'  => '='
                        ))
                    )
                ));
                foreach ($profiles->items as $profile) {
                    if ($verbose) {
                        echo("[FOUND]:   ID " . $profile->id . "\n");
                    }

                    $soapclient->Profile_remove(array('id' => $profile->id));
                    if ($verbose) {
                        echo("[REMOVE]:  ID " . $profile->id . "\n");
                    }

                }

            } else {
                if ($verbose) {
                    echo('[ERROR]:   $values[profile] is empty!' . "\n");
                }
            }
        }


        // Importing via $values[import] = array();
        if (in_array("import_profiles", $action)) {
            if ($verbose) {
                echo("[DO]:      Importing profiles into: '" . $login['account'] . ": " . $login['database'] . "'\n");
            }

            if (is_array($values['import'])) {

                foreach ($values['import'] as $user) {

                    unset($userdata);
                    $userdata = array2values($user);

                    if ($verbose) {
                        echo("[DO]:      Searching profile '" . $user['EMail'] . "'\n");
                    }
                    // Find the profile
                    $profiles = $soapclient->Database_searchProfiles(array(
                        'id'           => $database->id,
                        'requirements' => array(
                            $soapclient->toObject(array(
                                'fieldname' => 'EMail',
                                'value'     => $user['EMail'],
                                'operator'  => '='
                            ))
                        )
                    ));
                    $pi       = 0;
                    foreach ($profiles->items as $profile) {
                        $pi++;
                        if ($verbose) {
                            echo("[FOUND]:   ID " . $profile->id . "\n");
                        }

                        // Complete update of the profile
                        if ($user['overwrite'] == TRUE) {
                            $soapclient->Profile_updateFields(array(
                                'id'     => $profile->id,
                                'fields' => $userdata
                            ));
                            if ($verbose) {
                                echo("[UPDATED]: ID " . $profile->id . " (subscribe / all fields) \n");
                            }
                        }
                    }
                    if ($pi == 0) {
                        if ($verbose) {
                            echo("[DO]:      0 profiles found so creating new one: '" . $user['EMail'] . "'\n");
                        }
                        $profile1 = $soapclient->Database_createProfile(array(
                            'id'     => $database->id,
                            'fields' => $userdata
                        ));
                    }
                    if ($user['overwrite'] == FALSE && $pi >= 1) {
                        if ($verbose) {
                            echo("[ERROR]:   User already exists and $ user['overwrite'] = FALSE: '" . $user['EMail'] . "'\n");
                        }
                    }

                }

            } else {
                if ($verbose) {
                    echo('[ERROR]:   $values[import] is no array!' . "\n");
                }
            }
        }


        if ($verbose) {
            echo "\n----------------------------------------\nENDE</pre>";
        }
    }
    catch (Exception $e) {
        //$fp = fopen(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . "/userdata/logfile_copernica.txt","a+b");
        //fwrite($fp,"\r\n".date("d.m.y H:i:s")."\r\n".$e."\r\n");
        //fclose($fp);
        echo '<pre>' . print_r($e, 1) . '</pre>';
    }
}

?>
