<h3>Suchstatistik</h3><br />
<? $formname = "form_search_statistic"; ?>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <div class="infobox">
        <?
        $query  = "SELECT code,description FROM shop_shop";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        if (mysqli_num_rows($result) > 1) {
            ?>
            <div class="label">
                <label for="input_sitepart_type">Shop</label>
            </div>
            <div class="input">
                <select class="select" name="input_shop_code" id="input_shop_code" onchange="this.form.submit();">
                    <?
                    while ($shop = mysqli_fetch_assoc($result)) {
                        if ($_POST['input_shop_code'] == '') {
                            $_POST['input_shop_code'] = $shop['code'];
                        }
                        if ($_POST['input_shop_code'] == $shop['code']) {
                            $selected = "selected='selected'";
                        } else {
                            $selected = "";
                        }
                        echo("<option value='" . $shop['code'] . "' " . $selected . ">" . $shop['description'] . "</option>");
                    }
                    ?>
                </select>
            </div>
        <? } ?>
        <div class="label">
            <label for="input_sitepart_type">Typ</label>
        </div>
        <div class="input">
            <select class="select" name="input_sort_id" id="input_sort_id" onchange="this.form.submit();">
                <option<? if ($_POST["input_sort_id"] == 1) {
                    echo " selected=\"selected\"";
                } ?> value="1">Häufigkeit absteigend
                </option>
                <option<? if ($_POST["input_sort_id"] == 2) {
                    echo " selected=\"selected\"";
                } ?> value="2">Häufigkeit aufsteigend
                </option>
                <option<? if ($_POST["input_sort_id"] == 3) {
                    echo " selected=\"selected\"";
                } ?> value="3">Ergebnisse absteigend
                </option>
                <option<? if ($_POST["input_sort_id"] == 4) {
                    echo " selected=\"selected\"";
                } ?> value="4">Ergebnisse aufsteigend
                </option>
            </select>
        </div>
    </div>
    <?
    if ($_POST['input_shop_code'] != '') {
        $shop_query = "AND shop_code = '" . $_POST['input_shop_code'] . "'";
    }
    $query = "SELECT search_query AS 'Suchanfrage', COUNT(id) AS 'Häufigkeit', MAX(no_of_results) AS 'Ergebnisse' FROM shop_search_query WHERE search_datetime >= DATE_SUB(NOW(),INTERVAL 1 YEAR) " . $shop_query . " GROUP BY search_query";
    switch ($_POST["input_sort_id"]) {
        case "2":
            $query .= " ORDER BY `Häufigkeit` ASC";
            break;
        case "3":
            $query .= " ORDER BY `Ergebnisse` DESC";
            break;
        case "4":
            $query .= " ORDER BY `Ergebnisse` ASC";
            break;
        default:
            $query .= " ORDER BY `Häufigkeit` DESC";
            break;
    }
    if ($result = mysqli_query($GLOBALS['mysql_con'], $query)) {
        $format = array("text", "integer", "integer");
        linklist($result, $formname, $format);
    }
    ?>
</form>