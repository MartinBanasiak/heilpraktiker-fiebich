<?php
    if (!(bool)$get["legacy"]) {
        ?>
        <script type="text/javascript">
            var changes = false;

            $(document).ready(function () {
                for (var i in CKEDITOR.instances) {
                    CKEDITOR.instances[i].on('change', function () {
                        changes = true;
                        SendMessage(window.parent, "changedetected", "", "*");
                    });
                }

                $('#test').click(function () {
                    for (instance in CKEDITOR.instances) {
                        CKEDITOR.instances[instance].updateElement();
                    }

                    //Submit Form
                    var formData = new FormData($("#editor")[0]);
                    $.ajax({
                        url: $("#editor").attr('action'),
                        type: $("#editor").attr('method'),
                        dataType: 'html',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (data) {
                            SendMessage(evt.source, "saved", "", "*");
                        },
                        error: function (xhr, err) {
                            SendMessage(evt.source, "error", "", "*");
                        }
                    });
                });

            });

            function ReceiveMessage(evt) {
                var data = JSON.parse(evt.data);
                action = data.Event;
                data = data.Data;
                if (action == "save") {
                    for (instance in CKEDITOR.instances) {
                        CKEDITOR.instances[instance].updateElement();
                    }

                    //Submit Form
                    var formData = new FormData($("#editor")[0]);
                    $.ajax({
                        //url: $("#editor").attr('action') + '&ajax=1',
                        url: $("#editor").attr('action'),
                        type: $("#editor").attr('method'),
                        dataType: 'html',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (data) {
                            SendMessage(evt.source, "saved", "", "*");
                        },
                        error: function (xhr, err) {
                            SendMessage(evt.source, "error", "", "*");
                        }
                    });
                    return false;
                }

                if (action == "changes") {
                    if (changes) {
                        SendMessage(evt.source, "changes", "", "*");
                    } else {
                        SendMessage(evt.source, "nochanges", "", "*");
                    }
                }
            }
        </script>

        <?
    }
    if ($request["webform"] == "TextModul") {
        // Textbaustein anhand der GET-Parameter ermitteln
        $countquery = "SELECT * FROM shop_text_module WHERE company = '" . $get["company"] . "' AND code = '" . $get["code"] . "'";
        $result = @mysqli_query($GLOBALS['mysql_con'], $countquery);
        if (@mysqli_num_rows($result) == 1) {
            $input_text = @mysqli_fetch_assoc($result);

            // Änderungen speichern
            if ($get["send"]) {
                $query = "UPDATE shop_text_module SET content = '" . $post["textcontent"] . "' WHERE id = '" . $input_text["id"] . "' LIMIT 1";
                if (!@mysqli_query($GLOBALS['mysql_con'], $query)) {
                    echo "Fehler in Webform 'edit_text_module'. query: " . $query . "\n";
                    exit();
                }
                $result = @mysqli_query($GLOBALS['mysql_con'], $countquery);
                $input_text = @mysqli_fetch_assoc($result);
            }
        } else {
            echo "Fehler in Webform. \nquery: " . $countquery . "\n";
        }
    }

    if ($request["webform"] == "ItemDescription") {
        // Beschreibung anhand der GET-Parameter ermitteln
        $countquery = "SELECT * FROM shop_item_description WHERE company = '" . $get["company"] . "' AND shop_code = '" . $get["shop_code"] . "' AND language_code = '" . $get["language_code"] . "' AND item_no = '" . $get["item_no"] . "'  AND line_no = '" . $get["line_no"] . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $countquery);
        if (@mysqli_num_rows($result) == 1) {
            $input_text = @mysqli_fetch_assoc($result);
            // Änderungen speichern
            if ($get["send"]) {
                $query = "UPDATE shop_item_description SET content = '" . $post["textcontent"] . "' WHERE id = '" . $input_text["id"] . "'";
                if (!@mysqli_query($GLOBALS['mysql_con'], $query)) {
                    echo "Fehler in Webform 'edit_description'. query: " . $query . "\n";
                    exit();
                }
                $result = @mysqli_query($GLOBALS['mysql_con'], $countquery);
                $input_text = @mysqli_fetch_assoc($result);

                //Update shop_item_search.long_description
                $query2 = "
			set group_concat_max_len=10000;";
                @mysqli_query($GLOBALS['mysql_con'], $query2);
                $query2 = "
			SELECT
				GROUP_CONCAT(HTML_UnEncode(strip_tags(sid.content)) SEPARATOR ' ') AS 'long_description'
			FROM
				shop_item_description sid
			WHERE
				sid.company='" . $input_text["company"] . "'
			  AND
				sid.shop_code='" . $input_text["shop_code"] . "'
			  AND
				sid.language_code='" . $input_text["language_code"] . "'
			  AND
				sid.item_no='" . $input_text["item_no"] . "'
			GROUP BY sid.item_no
		";
                $result2 = mysqli_query($GLOBALS['mysql_con'], $query2);
                $long_text_raw = @mysqli_result($result2, 0);
                if (strlen($long_text_raw > 0) || true) {
                    $long_text = preg_replace('/[^A-Za-z0-9\s\']/', ' ', html_entity_decode(strip_tags($long_text_raw)));
                    $query3 = "
				UPDATE
					shop_item_search
				SET
					long_description = '" . $long_text . "',
					last_datetime_updated = NOW()
				WHERE
					company='" . $input_text["company"] . "'
				  AND
					shop_code='" . $input_text["shop_code"] . "'
				  AND
					language_code='" . $input_text["language_code"] . "'
				  AND
					item_no='" . $input_text["item_no"] . "'
			";
                    @mysqli_query($GLOBALS['mysql_con'], $query3);
                }

                //Update Marketplace
                $query = "SELECT * FROM shop_shop WHERE (code = '" . $input_text["shop_code"] . "' OR use_items_from_shop_code = '" . $input_text["shop_code"] . "')";
                $result = mysqli_query($GLOBALS["mysql_con"],$query);
                while ($shop = mysqli_fetch_array($result)) {
                    $query2 = "UPDATE shop_marketplace_item_update SET marketplace_update = 1 WHERE 
                        item_no = '" . $input_text["item_no"] . "' 
                        AND item_shop_code = '" . $input_text["shop_code"] . "' 
                        AND item_language_code = '" . $input_text["language_code"] . "' 
                        AND language_code = '".$shop["code"]."'
                        AND shop_code = '".$shop["code"]."'
                        ";
                    @mysqli_query($GLOBALS["mysql_con"],$query2);
                }

            }
        } else {
            echo "Fehler in Webform. \nquery: " . $countquery . "\n";
        }
    }
    if ($request["webform"] == "CategoryDesc") {
        // Textbaustein anhand der GET-Parameter ermitteln
        $countquery = "SELECT id,category_description,category_description_2,category_description_excerpt,promotion_description FROM shop_category WHERE company = '" . $get["company"] . "' AND shop_code = '" . $get["shop_code"] . "' AND language_code = '" . $get["language_code"] . "' AND line_no = '" . $get["line_no"] . "'";
        $result = @mysqli_query($GLOBALS['mysql_con'], $countquery);
        if (@mysqli_num_rows($result) == 1) {
            $input_text = @mysqli_fetch_assoc($result);
            switch ($get["type"]) {
                case 0:
                    $content_field = "category_description";
                    break;
                case 1:
                    $content_field = "promotion_description";
                    break;
                case 2:
                    $content_field = "category_description_2";
                    break;
                case 3:
                    $content_field = "category_description_excerpt";
                    break;
            }
            $input_text["content"] = $input_text[$content_field];
            // Änderungen speichern
            if ($get["send"]) {
                $query = "UPDATE shop_category SET " . $content_field . " = '" . $post["textcontent"] . "' WHERE id = '" . $input_text["id"] . "' LIMIT 1";

                if (!@mysqli_query($GLOBALS['mysql_con'], $query)) {
                    echo "Fehler in Webform 'edit_category_description'. query: " . $query . "\n";
                    exit();
                }
                $result = @mysqli_query($GLOBALS['mysql_con'], $countquery);
                $input_text = @mysqli_fetch_assoc($result);
            }
        } else {
            echo "Fehler in Webform. \nquery: " . $countquery . "\n";
        }
    }

    if ($request["webform"] == "ShippingOption") {
        // Textbaustein anhand der GET-Parameter ermitteln
        $countquery = "SELECT * FROM shop_shipping_option WHERE company = '" . $get["company"] . "' AND shipping_group_code = '" . $get["shipping_group_code"] . "' AND line_no = '" . $get["line_no"] . "'";
        $result = @mysqli_query($GLOBALS['mysql_con'], $countquery);
        if (@mysqli_num_rows($result) == 1) {
            $input_text = @mysqli_fetch_assoc($result);

            // Änderungen speichern
            if ($get["send"]) {
                $query = "UPDATE shop_shipping_option SET content = '" . $post["textcontent"] . "' WHERE id = '" . $input_text["id"] . "' LIMIT 1";
                if (!@mysqli_query($GLOBALS['mysql_con'], $query)) {
                    echo "Fehler in Webform . query: " . $query . "\n";
                    exit();
                }
                $result = @mysqli_query($GLOBALS['mysql_con'], $countquery);
                $input_text = @mysqli_fetch_assoc($result);
            }
        } else {
            echo "Fehler in Webform. \nquery: " . $countquery . "\n";
        }
    }

if ($request["webform"] == "ShippingOptionTranslation") {
    // Textbaustein anhand der GET-Parameter ermitteln
    $countquery = "SELECT * FROM shipping_option_translation WHERE company = '" . $get["company"] . "' AND shipping_group_code = '" . $get["shipping_group_code"] . "' AND language_code = '" . $get["language_code"] . "' AND line_no = '" . $get["line_no"] . "'";
    $result = @mysqli_query($GLOBALS['mysql_con'], $countquery);
    if (@mysqli_num_rows($result) == 1) {
        $input_text = @mysqli_fetch_assoc($result);

        // Änderungen speichern
        if ($get["send"]) {
            $query = "UPDATE shipping_option_translation SET content = '" . $post["textcontent"] . "' WHERE id = '" . $input_text["id"] . "' LIMIT 1";
            if (!@mysqli_query($GLOBALS['mysql_con'], $query)) {
                echo "Fehler in Webform . query: " . $query . "\n";
                exit();
            }
            $result = @mysqli_query($GLOBALS['mysql_con'], $countquery);
            $input_text = @mysqli_fetch_assoc($result);
        }
    } else {
        echo "Fehler in Webform. \nquery: " . $countquery . "\n";
    }
}

    if ($request["webform"] == "PaymentOption") {
        // Textbaustein anhand der GET-Parameter ermitteln
        $countquery = "SELECT * FROM shop_payment_option WHERE company = '" . $get["company"] . "' AND shop_code = '" . $get["shop_code"] . "' AND language_code = '" . $get["language_code"] . "' AND line_no = '" . $get["line_no"] . "'";
        $result = @mysqli_query($GLOBALS['mysql_con'], $countquery);
        if (@mysqli_num_rows($result) == 1) {
            $input_text = @mysqli_fetch_assoc($result);

            // Änderungen speichern
            if ($get["send"]) {
                $query = "UPDATE shop_payment_option SET content = '" . $post["textcontent"] . "' WHERE id = '" . $input_text["id"] . "' LIMIT 1";
                if (!@mysqli_query($GLOBALS['mysql_con'], $query)) {
                    echo "Fehler in Webform . query: " . $query . "\n";
                    exit();
                }
                $result = @mysqli_query($GLOBALS['mysql_con'], $countquery);
                $input_text = @mysqli_fetch_assoc($result);
            }
        } else {
            echo "Fehler in Webform. \nquery: " . $countquery . "\n";
        }
    }


    if (@mysqli_num_rows($result) == 1) {
        ?>
        <form id="editor" name="editor"
              action="<?= $_SERVER["REQUEST_URI"] ?>&send=TRUE"
              method="post">

            <?
            if (empty($GLOBALS["shop_setup"]["fck_style_dir"])) {
                $GLOBALS["shop_setup"]["fck_style_dir"] = 'layout/admin/ckeditor.css';
            }
            if (empty($GLOBALS["shop_setup"]["fck_xml_dir"])) {
                $GLOBALS["shop_setup"]["fck_xml_dir"] = 'layout/admin/ck_styles.js';
            }
            if ((bool)$get["legacy"]) {
                show_ck_editor($input_text["content"], "nav_legacy", $GLOBALS["shop_setup"]["fck_style_dir"], $GLOBALS["shop_setup"]["fck_xml_dir"]);
            } else {
                show_ck_editor($input_text["content"], "nav_new", $GLOBALS["shop_setup"]["fck_style_dir"], $GLOBALS["shop_setup"]["fck_xml_dir"]);
            }
            ?>
        </form>
        <?
    } else {
        echo "Fehler in Webform. \nquery: " . $countquery . "\n";
    }
