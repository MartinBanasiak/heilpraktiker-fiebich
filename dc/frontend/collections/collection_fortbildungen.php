<?
$preview = "";
if ($collectionPreview){
    $preview = "isPreview";
}
?>

<div class="collection collection<?= $code ?> <?= $view ?> <?=$preview?>">
    <?
    foreach ($collections as $collection_id => $collectionData) {
        if ($linked == 1 && $fullView === FALSE) {
            if ($collectionPreview === TRUE) {
                // weiterleitung zu einer anderen seite
                $query = "SELECT * FROM main_page_link WHERE main_collection_setup_id = '" . $collection_setup['main_collection_setup_id'] . "' AND main_collection_view_type = 0 LIMIT 1";
                $result = mysqli_query($GLOBALS["mysql_con"], $query);
                if (@mysqli_num_rows($result) == 1) {
                    $mainPageLink = @mysqli_fetch_array($result);
                    $query = "SELECT * FROM main_navigation WHERE forward_page_id = '" . $mainPageLink['main_page_id'] . "' LIMIT 1";
                    $result = mysqli_query($GLOBALS["mysql_con"], $query);
                    if (@mysqli_num_rows($result) == 1) {
                        $mainNavigation = @mysqli_fetch_array($result);
                        $collection_setup['main_collection_page_list_id'] = $mainNavigation["id"];
                    }
                }
                $fullviewLink = get_link_to_navigation($collection_setup['main_collection_page_list_id']) . get_collection_rewrite($collectionData['description'], $collection_id);
                $fullviewLinkAll = get_link_to_navigation($collection_setup['main_collection_page_list_id']);
            } else {
                // gleiche seite nochmal laden
                $fullviewLink = get_link_to_navigation($GLOBALS["navigation"]['id']) . get_collection_rewrite($collectionData['description'], $collection_id);
            }
        }

        $collectionFullLines = array();
        foreach ($setup_fields as $fieldId => $fieldData) {
            if (!isset($collection_lines[$collection_id])) {
                continue;
            }

            if (!isset($collection_lines[$collection_id][$fieldId])) {
                continue;
            }

            if ($fullView === FALSE && $fieldData['is_teaser'] == 0) {
                continue;
            }

            $collection_lines[$collection_id][$fieldId]['code'] = $fieldData['code'];
            $collection_lines[$collection_id][$fieldId]['fieldname'] = $fieldData['fieldname'];
            $collection_lines[$collection_id][$fieldId]['type_id'] = $fieldData['type_id'];
            $collection_lines[$collection_id][$fieldId]['fieldtype'] = $fieldData['fieldtype'];

            $collectionFullLines[$fieldData['code']] = $collection_lines[$collection_id][$fieldId];
        }

        $groups = "";
        $groupclasses = "";

        if (isset($collectionData['groups'])) {
            $i = 0;
            foreach ($collectionData['groups'] as $groupid => $groupdata) {
                $groupclasses .= " collection_group_" . strtolower($groupdata['description']);

                if($i > 0){
                    $groups .= ", ";
                }
                $groups .= $groupdata['description'];
                $i++;
            }
        }
        if (!$fullView) {
            include('collection_' . $code . '_list.php');
        } else {
            include('collection_' . $code . '_full.php');
        } ?>
    <? } ?>
</div>
