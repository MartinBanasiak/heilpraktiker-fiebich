<?
$messages = array();
$error    = FALSE;

switch ($_REQUEST["action"]) {
    case 'edit':
        edit_layout();
        break;
    case 'delete':
        delete_layout();
        break;
    case 'new':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_cardform.inc.php';
        break;
    case 'save':
        save_layout();
        break;
    case 'edit_area':
        edit_layout_area();
        break;
    case 'delete_area':
        delete_layout_area();
        break;
    case 'new_area':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_area_cardform.inc.php';
        break;
    case 'save_area':
        save_layout_area();
        break;
    case 'moveup_inclusion':
        moveup_inclusion();
        edit_layout();
        break;
    case 'movedown_inclusion':
        movedown_inclusion();
        edit_layout();
        break;
    case 'delete_inclusion':
        delete_inclusion();
        edit_layout();
        break;
    case 'edit_inclusion':
        edit_inclusion();
        break;
    case 'new_inclusion':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_inclusions_cardform.inc.php';
        break;
    case 'save_inclusion':
        save_inclusion($sitepart);
        break;
    case 'moveup_area':
        moveup_area();
        edit_layout();
        break;
    case 'movedown_area':
        movedown_area();
        edit_layout();
        break;
    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_files_listform.inc.php';
        break;
}

function show_dir_structure( $site, $language ) {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");

    // config des ck plugins auslesen
    $config      = array();
    $not_include = TRUE;
    include CMS_PATH . '../plugins/ckfinder/config.php';
    $resources = $config['resourceTypes'];

    echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"linklist\"><tr>";
    echo "<td><div>" . $translation->get("name") . "</div></td>";
    echo "<td width=\"160\" align=\"left\"><div>" . $translation->get("count_files") . "</div></td>";
    echo "<td width=\"160\" align=\"left\"><div>" . $translation->get("changed_on") . "</div></td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td class=\"linklist_seperator\"></td>";
    echo "<td class=\"linklist_seperator\"></td>";
    echo "<td class=\"linklist_seperator\"></td>";
    echo "</tr>";

    echo "</table>";
    show_dir_structure_rek($site, $language, 0, 1, 0, $resources, NULL);

    echo "\n";
    return TRUE;
}

function resolveUrl( $_url ) {
    //dummy function needed for ckfinder config
}


function show_dir_structure_rek( $site, $language, $parent_id, $level, $input_counter, $resources, $iterator = NULL ) {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");


    if ($parent_id == 0) {
        echo "<ol class=\"linklist sortable\">\n";
    } else {
        echo "<ol>\n";
    }

    $counter = 1;

    foreach ($resources as $res) {
        $level     = -1;
        $olCreated = array();
        $resource_directory = $res['absoluteUrl'];

        $folderInfo = new SplFileInfo(CMS_PATH . '../' . $resource_directory);
        echo "<li id=\"list_{$counter}\">";
        echo "<div data-function=\"open_ckfinder\" data-startresource=\"" . $res['name'] . "\" data-startfolder=\"\" data-inputid=\"" . $counter . "\" data-action=\"edit_navigation\" data-inputname=\"input_main_navigation_id\" data-checked=\"false\" class=\"linklist_content dd-handle\"><span class=\"disclose\"><span>&nbsp;</span></span>" . $res['name'] . "</div>\n";
        echo "<div class=\"dd-count-files\">" . count_files_in_folder(CMS_PATH . '../' . $resource_directory) . "</div>";
        echo "<div class=\"dd-gaendert-am-files\">" . date('d.m.Y H:i', $folderInfo->getMTime()) . "</div>";

        $Directory = new RecursiveDirectoryIterator(CMS_PATH . '../' . $resource_directory);
        $Iterator  = new RecursiveIteratorIterator($Directory, RecursiveIteratorIterator::SELF_FIRST);

        $Iterator->rewind();

        $openedOLs = 0;
        $lastDepth = 0;
        while ($Iterator->valid()) {
            $counter++;

            if (!$Iterator->isDir() || $Iterator->isDot()) {
                $Iterator->next();
                continue;
            }

            if ($Iterator->getDepth() > $level) {
                $openedOLs++;
                echo "<ol>\n";
                $level = $Iterator->getDepth();
            } elseif ($Iterator->getDepth() < $level) {
                $repeat = $level - $Iterator->getDepth();
                echo str_repeat("</li></ol>\n", $repeat);
                echo "</li>\n";

                $openedOLs = $openedOLs - $repeat;
                $level     = $Iterator->getDepth();
            } elseif ($Iterator->getDepth() == $level) {
                echo "</li>\n";
            }

            $folderInfo = new SplFileInfo($Iterator->getPathname());
            echo "<li id=\"list_" . $counter . "\">\n";
            echo "<div data-function=\"open_ckfinder\" data-startresource=\"" . $res['name'] . "\" data-startfolder=\"" . $Iterator->getSubPathname() . "\" data-inputid=\"" . $counter . "\" data-action=\"edit_navigation\" data-inputname=\"input_main_navigation_id\" data-checked=\"false\" class=\"linklist_content dd-handle\"><span class=\"disclose\"><span>&nbsp;</span></span>" . $Iterator->getFilename() . "</div>\n";
            echo "<div class=\"dd-count-files\">" . count_files_in_folder($Iterator->getPathname()) . "</div>";
            echo "<div class=\"dd-gaendert-am-files\">" . date('d.m.Y H:i', $folderInfo->getMTime()) . "</div>";

            $lastDepth = $Iterator->getDepth();
            $Iterator->next();
        }
        if ($openedOLs > 0) {
            echo str_repeat("</li></ol>\n", $openedOLs);
            echo "</li>\n";
        } else {
            echo "</li>\n";
        }
    }

    echo "</ol>\n\n";
}

function count_files_in_folder( $_folder ) {
    $dir = new DirectoryIterator($_folder);
    $x   = 0;
    foreach ($dir as $file) {
        $x += ($file->isFile()) ? 1 : 0;
    }
    return $x;
}