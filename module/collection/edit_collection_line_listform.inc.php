<?php
$translation   = \DynCom\dc\common\classes\Registry::get("translation");
$formname      = "form_collection_line_list";
$inputname     = "input_id";
$input_page_id = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");

$main_collection_setup_id = $input_collection['main_collection_setup_id'];
$query                    = "SELECT * FROM main_collection_setup WHERE id = '" . $main_collection_setup_id."'";
$result                   = @mysqli_query($GLOBALS['mysql_con'], $query);
$main_collection_setup    = @mysqli_fetch_array($result);
?>

<div class="collectionform">
    <?php
    $collectionFieldSetups = array();
    $query                 = "SELECT * FROM main_collection_setup_content WHERE main_collection_setup_id = '" . $main_collection_setup_id . "' ORDER BY sorting ASC";
    $result_collection     = @mysqli_query($GLOBALS['mysql_con'], $query);
    while ($row = @mysqli_fetch_array($result_collection, 1)) {
        $collectionFieldSetups[] = $row;
    }

    if (count($collectionFieldSetups)) {
        foreach ($collectionFieldSetups as $row) {
            echo "<tr><td>";

            create_collection_input_field($row, $input_collection);

            echo "</td></tr>";
        }
    }
    ?>
</div>
