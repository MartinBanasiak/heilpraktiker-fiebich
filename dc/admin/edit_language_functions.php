<?php
/**
 * Extra language function class
 *
 * User: tomaszbrniak
 * Date: 02.07.16
 * Time: 15:06
 */

require_once(MODULE_PATH . "gallery/gallery.config.inc.php");
require_once(MODULE_PATH . "filegallery/filegallery.config.inc.php");
require_once(MODULE_PATH . "collection/collection_config.inc.php");

class LanguageFunctions {

    /**
     * Error messages
     *
     * @var array
     */
    public $errorMessages = array();

    /**
     * Current language id to copy
     *
     * @var int
     */
    public $languageId;

    /**
     * New generated language id
     *
     * @var int
     */
    public $newLanguageId;

    /**
     * New generated site Id or null for current site id of $languageId
     *
     * @var null|int
     */
    public $siteId = null;

    /**
     * Text for all select statements
     *
     * @var int|string
     */
    public $siteIdForInsert = null;

    /**
     * Public message for user
     *
     * @var string
     */
    public $message = "";

    /**
     * Next step to copy
     *
     * @var string
     */
    public $nextStep = "";

    /**
     * Array with sitepart Header Ids to copy
     *
     * @var null|array
     */
    protected $sitePartHeaderIds = null;

    /**
     * Array with sitepartIds to copy
     *
     * @var null|array
     */
    protected $sitePartIds = null;

    /**
     * Copy only specific siteparts
     *
     * @var bool
     */
    protected $sitePartdIdsFromArrayOnly = false;

    /**
     * Copy Page Component Include Link
     *
     * @var bool
     */
    protected $copyPageComponentIcludeLink = true;

    /**
     * Copy Page collection Group Link
     *
     * @var bool
     */
    protected $copyPageCollectionGroupLink = true;

    /**
     * Copy Page layout inclusion link
     *
     * @var bool
     */
    protected $copyPageLayoutInclusionLink = true;

    /**
     * Main Layout Id from Target language
     *
     * @var bool
     */
    protected $mainLayoutId = 0;

    /**
     * LanguageFunctions constructor.
     *
     * @param int $languageId Language to copy
     * @param null $siteId new Website id or null for old id
     */
    public function __construct($languageId, $siteId = null, $newLanguageId = null) {
        $this->languageId = $languageId;
        $this->siteId = $siteId;

        $this->siteIdForInsert = "main_site_id";
        if($this->siteId != null) {
            $this->siteIdForInsert = $this->siteId;
        }

        $this->newLanguageId = $newLanguageId;
        if($this->newLanguageId === null) {
            $this->newLanguageId = $this->get_new_id($languageId, 'main_language');
        }

        $query = "SELECT main_layout_id from main_language WHERE id = '" . $this->newLanguageId."'";
        $layoutResult = @mysqli_query($GLOBALS['mysql_con'], $query);
        $layoutRow = mysqli_fetch_array($layoutResult);
        $this->mainLayoutId = $layoutRow['main_layout_id'];
    }

    /**
     * Copy language data
     *
     * @return bool
     */
    public function copy_language() {
        $translation = \DynCom\dc\common\classes\Registry::get("translation");
        $languageColumns = $this->get_column_names_to_copy("main_language");
        $languageColumns['main_site_id'] = $this->siteIdForInsert;

        $query = "INSERT INTO main_language 
            SELECT " . join(", ", $languageColumns) . " 
            FROM main_language where id = " . $this->languageId;
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $this->newLanguageId = mysqli_insert_id($GLOBALS['mysql_con']);
        if((int)$this->newLanguageId <= 0) {
            $this->errorMessages[] = $translation->get("copy_failed");
            return false;
        }

        $query = "UPDATE main_language set name = CONCAT(name, ' (" . $translation->get("copy") . ")'), code = '" . $this->newLanguageId . "' WHERE id = " . $this->newLanguageId;
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $this->insert_copy_link($this->languageId, $this->newLanguageId, "main_language");

        $this->message = $translation->get("copy_language_main_finish");
        $this->nextStep = "copy_siteparts";

        return true;
    }

    /**
     * Copy all siteparts content
     *
     * @return bool
     */
    public function copy_siteparts() {
        $translation = \DynCom\dc\common\classes\Registry::get("translation");
        $siteparts = \DynCom\dc\common\classes\Siteparts::get();
        foreach($siteparts as $sitepartId => $sitepart) {

            if($this->sitePartdIdsFromArrayOnly && !in_array($sitepartId, $this->sitePartIds)) {
                continue;
            }

            // Spalten sammeln
            $headerColumns = $this->get_column_names_to_copy($sitepart['header_table']);
            if($headerColumns === false) {
                continue;
            }
            $lineColumns = array();
            if($sitepart['line_table'] != "") {
                $lineColumns = $this->get_column_names_to_copy($sitepart['line_table']);
            }

            // header kopieren
            $query = "SELECT * FROM " . $sitepart['header_table'] . " WHERE main_language_id = " . $this->languageId;
            $result = @mysqli_query($GLOBALS['mysql_con'], $query);
            while($row = mysqli_fetch_array($result)) {
                if($this->sitePartdIdsFromArrayOnly && !in_array($row['id'], $this->sitePartHeaderIds)) {
                    continue;
                }

                $query = "INSERT INTO " . $sitepart['header_table'] . " 
                            SELECT " . join(", ", $headerColumns) . " FROM " . $sitepart['header_table'] . " 
                            WHERE id = " . $row['id'];
                @mysqli_query($GLOBALS['mysql_con'], $query);
                $newHeaderId = mysqli_insert_id($GLOBALS['mysql_con']);

                // alle lines zur header_id kopieren
                if($sitepart['line_table'] != "") {
                    $query = "SELECT * FROM " . $sitepart['line_table'] . " WHERE header_id = " . $row['id'];
                    $lineResult = @mysqli_query($GLOBALS['mysql_con'], $query);
                    while($lineRow = mysqli_fetch_array($lineResult)) {
                        $finalLineColumns = $lineColumns;
                        $finalLineColumns["header_id"] = $newHeaderId;

                        $query = "INSERT INTO " . $sitepart['line_table'] . " 
                            SELECT " . join(", ", $finalLineColumns) . " FROM " . $sitepart['line_table'] . " 
                            WHERE id = " . $lineRow['id'];
                        @mysqli_query($GLOBALS['mysql_con'], $query);
                        $newLineId = mysqli_insert_id($GLOBALS['mysql_con']);

                        if($sitepart['line_table'] == "gallery_line") {
                            $this->copy_gallery_image($newLineId, $lineRow);
                        }

                        if($sitepart['line_table'] == "filegallery_line") {
                            $this->copy_filegallery_file($newLineId, $lineRow);
                        }
                    }
                }

                // neue Id zwischenspeichern fuer spaetere Zuordnung
                $this->insert_copy_link($row['id'], $newHeaderId, $sitepart['header_table']);
            }
        }

        $columns = $this->get_column_names_to_copy("main_sitepart_changes");
        $query = "SELECT * FROM main_sitepart_changes WHERE main_language_id = " . $this->languageId;
        $changesResult = @mysqli_query($GLOBALS['mysql_con'], $query);
        while($lineRow = mysqli_fetch_array($changesResult)) {
            if($this->sitePartdIdsFromArrayOnly && !in_array($lineRow['main_sitepart_header_id'], $this->sitePartHeaderIds)) {
                continue;
            }

            if($this->sitePartdIdsFromArrayOnly && !in_array($lineRow['main_sitepart_id'], $this->sitePartIds)) {
                continue;
            }

            $finalLineColumns = $columns;
            $finalLineColumns["main_sitepart_header_id"] = $this->get_new_id($lineRow['main_sitepart_header_id'], $siteparts[$lineRow['main_sitepart_id']]['header_table']);

            $query = "INSERT INTO main_sitepart_changes 
                            SELECT " . join(", ", $finalLineColumns) . " FROM main_sitepart_changes 
                            WHERE id = " . $lineRow['id'];
            @mysqli_query($GLOBALS['mysql_con'], $query);
        }

        $this->message = $translation->get("copy_language_sitepart_finish");
        $this->nextStep = "copy_collection_setup";

        return true;
    }

    /**
     * Helper function to copy the image of a gallery_line row
     *
     * @param $newLineId
     * @param $lineRow
     */
    protected function copy_gallery_image($newLineId, $lineRow) {
        $filename = $lineRow['filename'];
        $newFilename = str_replace($lineRow['id'] . "_", $newLineId . "_", $filename);

        $previewImageString = "<img src=\'" . PATH_ICON_GALLERY . $newFilename . "?time=" . time() . "\'>";
        $query = "UPDATE gallery_line SET preview = '" . $previewImageString . "', filename = '" . $newFilename . "' where id = " . $newLineId;
        @mysqli_query($GLOBALS['mysql_con'], $query);

        if(!file_exists(MODULE_PATH . "gallery/" . PATH_ORIGINAL_PICTURE_GALLERY . $filename)) {
            return;
        }

        copy(MODULE_PATH . "gallery/" . PATH_ORIGINAL_PICTURE_GALLERY . $filename   , MODULE_PATH . "gallery/" . PATH_ORIGINAL_PICTURE_GALLERY . $newFilename);
        copy(MODULE_PATH . "gallery/" . PATH_RESIZE_GALLERY . $filename             , MODULE_PATH . "gallery/" . PATH_RESIZE_GALLERY . $newFilename);
        copy(MODULE_PATH . "gallery/" . PATH_THUMBNAIL_GALLERY . $filename          , MODULE_PATH . "gallery/" . PATH_THUMBNAIL_GALLERY . $newFilename);
        copy(MODULE_PATH . "gallery/../.." . PATH_ICON_GALLERY . $filename          , MODULE_PATH . "gallery/../.." . PATH_ICON_GALLERY . $newFilename);
    }

    /**
     * Helper function to copy the file of a filegallery_row
     *
     * @param $newLineId
     * @param $lineRow
     */
    protected function copy_filegallery_file($newLineId, $lineRow) {
        $filename = $lineRow['filename'];
        $newFilename = str_replace($lineRow['id'] . "_", $newLineId . "_", $filename);

        $query = "UPDATE filegallery_line SET filename = '" . $newFilename . "' where id = " . $newLineId;
        @mysqli_query($GLOBALS['mysql_con'], $query);

        if(!file_exists(MODULE_PATH . "filegallery/" . PATH_ORIGINAL_FILEGALLERY . $filename)) {
            return;
        }

        copy(MODULE_PATH . "filegallery/" . PATH_ORIGINAL_FILEGALLERY . $filename   , MODULE_PATH . "filegallery/" . PATH_ORIGINAL_FILEGALLERY . $newFilename);
    }

    /**
     * Helper function to copy the file of a collection image row
     *
     * @param $newLineId
     * @param $lineRow
     */
    protected function copy_collection_image($newLineId, $lineRow) {
        $filename = $lineRow['data'];
        $newFilename = $newLineId . "_" . $filename;

        $query = "UPDATE main_collection_link SET data = '" . $newFilename . "' where id = " . $newLineId;
        @mysqli_query($GLOBALS['mysql_con'], $query);

        if(!file_exists(MODULE_PATH . "collection/" . PATH_ORIGINAL_PICTURE_COLLECTION . $filename)) {
            return;
        }

        copy(MODULE_PATH . "collection/" . PATH_ORIGINAL_PICTURE_COLLECTION . $filename     , MODULE_PATH . "collection/" . PATH_ORIGINAL_PICTURE_COLLECTION . $newFilename);
        copy(MODULE_PATH . "collection/" . PATH_RESIZE_COLLECTION . $filename               , MODULE_PATH . "collection/" . PATH_RESIZE_COLLECTION . $newFilename);
        copy(MODULE_PATH . "collection/../.." . PATH_ICON_COLLECTION . $filename                 , MODULE_PATH . "collection/../.." . PATH_ICON_COLLECTION . $newFilename);
    }

    /**
     * Copy main navigation
     *
     * @return bool
     */
    public function copy_main_navigation() {
        $translation = \DynCom\dc\common\classes\Registry::get("translation");
        $navigationColumns = $this->get_column_names_to_copy("main_navigation");
        $navigationColumns["main_site_id"] = $this->siteIdForInsert;

        $query = "SELECT * FROM main_navigation WHERE main_language_id = " . $this->languageId;
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        while($row = mysqli_fetch_array($result)) {
            $query = "INSERT INTO main_navigation 
                            SELECT " . join(", ", $navigationColumns) . " FROM main_navigation 
                            WHERE id = " . $row['id'];
            @mysqli_query($GLOBALS['mysql_con'], $query);
            $newMainNavigationId = mysqli_insert_id($GLOBALS['mysql_con']);

            $this->insert_copy_link($row['id'], $newMainNavigationId, "main_navigation");
        }

        $query = "SELECT * FROM main_navigation WHERE main_language_id = " . $this->newLanguageId;
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        while($row = mysqli_fetch_array($result)) {
            $queryArray = array();
            if($row['parent_id'] > 0) {
                $queryArray[] = " parent_id = " . $this->get_new_id($row['parent_id'], 'main_navigation');
            }
            if($row['forward_navigation_id'] > 0) {
                $queryArray[] = " forward_navigation_id = " . $this->get_new_id($row['forward_navigation_id'], 'main_navigation');
            }
            if($row['forward_page_id'] > 0) {
                $queryArray[] = " forward_page_id = " . $this->get_new_id($row['forward_page_id'], 'main_page');
            }

            if(count($queryArray)) {
                $query = "UPDATE main_navigation SET " . join(", ", $queryArray) . " WHERE id = " . $row['id'];
                @mysqli_query($GLOBALS['mysql_con'], $query);
            }
        }

        $this->message = $translation->get("copy_language_navigation_finish");
        $this->nextStep = ""; // keine weiteren Schritte mehr

        return true;
    }

    /**
     * Copy collection setup
     *
     * @return bool
     */
    public function copy_collection_setup($setupId = "", $addCopySuffix = false) {
        $translation = \DynCom\dc\common\classes\Registry::get("translation");
        $table = "main_collection_setup";
        $columns = $this->get_column_names_to_copy($table);

        $query = "SELECT * FROM $table WHERE main_language_id = " . $this->languageId;
        if($setupId != "") {
            $query .= " AND id = '" . $setupId."'";
        }

        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        while($row = mysqli_fetch_array($result)) {
            if($addCopySuffix) {
                $columns['description'] = "CONCAT(description, ' (" . $translation->get("copy") . ")')";
            }

            $query = "INSERT INTO $table
                            SELECT " . join(", ", $columns) . " FROM $table
                            WHERE id = " . $row['id'];
            @mysqli_query($GLOBALS['mysql_con'], $query);
            $newId = mysqli_insert_id($GLOBALS['mysql_con']);

            if($addCopySuffix) {
                $query = "UPDATE $table set code = CONCAT(code, '-$newId') WHERE id = " . $newId;
                @mysqli_query($GLOBALS['mysql_con'], $query);
            }

            $this->insert_copy_link($row['id'], $newId, $table);

            // collection setup content kopieren
            $collectionSetupContentColumns = $this->get_column_names_to_copy("main_collection_setup_content");
            $query = "SELECT * FROM main_collection_setup_content WHERE main_collection_setup_id = " . $row['id'];
            $lineResult = @mysqli_query($GLOBALS['mysql_con'], $query);
            while($lineRow = mysqli_fetch_array($lineResult)) {
                $finalLineColumns = $collectionSetupContentColumns;
                $finalLineColumns["main_collection_setup_id"] = $newId;

                $query = "INSERT INTO main_collection_setup_content 
                            SELECT " . join(", ", $finalLineColumns) . " FROM main_collection_setup_content 
                            WHERE id = " . $lineRow['id'];
                @mysqli_query($GLOBALS['mysql_con'], $query);
                $newLineId = mysqli_insert_id($GLOBALS['mysql_con']);

                $this->insert_copy_link($lineRow['id'], $newLineId, "main_collection_setup_content");
            }

            // collection setup gruppen kopieren
            $collectionSetupGroupColumns = $this->get_column_names_to_copy("main_collection_setup_group");
            $query = "SELECT * FROM main_collection_setup_group WHERE main_collection_setup_id = " . $row['id'];
            $lineResult = @mysqli_query($GLOBALS['mysql_con'], $query);
            while($lineRow = mysqli_fetch_array($lineResult)) {
                $finalLineColumns = $collectionSetupGroupColumns;
                $finalLineColumns["main_collection_setup_id"] = $newId;

                $query = "INSERT INTO main_collection_setup_group 
                            SELECT " . join(", ", $finalLineColumns) . " FROM main_collection_setup_group 
                            WHERE id = " . $lineRow['id'];
                @mysqli_query($GLOBALS['mysql_con'], $query);
                $newLineId = mysqli_insert_id($GLOBALS['mysql_con']);

                $this->insert_copy_link($lineRow['id'], $newLineId, "main_collection_setup_group");
            }
        }

        $this->message = $translation->get("copy_language_collection_setup_finish");
        $this->nextStep = "copy_collection";

        return true;
    }

    /**
     * Copy collections
     *
     * @return bool
     */
    public function copy_collection($setupId = "", $addCopySuffix = false) {
        $translation = \DynCom\dc\common\classes\Registry::get("translation");
        $siteparts = \DynCom\dc\common\classes\Siteparts::get();
        $table = "main_collection";
        $columns = $this->get_column_names_to_copy($table);

        $query = "SELECT * FROM $table WHERE main_language_id = " . $this->languageId;
        if($setupId != "") {
            $query .= " AND main_collection_setup_id = '" . $setupId."'";
        }

        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        while($row = mysqli_fetch_array($result)) {
            $finalColumns = $columns;
            $finalColumns['main_collection_setup_id'] = $this->get_new_id($row['main_collection_setup_id'], 'main_collection_setup');
            if($row['registration_contactform_id'] > 0) {
                $finalColumns['registration_contactform_id'] = $this->get_new_id($row['registration_contactform_id'], 'contactform_header');
            }
            if($addCopySuffix) {
                $finalColumns['description'] = "CONCAT(description, ' (" . $translation->get("copy") . ")')";
            }

            $query = "INSERT INTO $table
                            SELECT " . join(", ", $finalColumns) . " FROM $table
                            WHERE id = " . $row['id'];
            @mysqli_query($GLOBALS['mysql_con'], $query);
            $newId = mysqli_insert_id($GLOBALS['mysql_con']);

            $this->insert_copy_link($row['id'], $newId, $table);

            // collection group link kopieren
            $collectionGroupLinkColumns = $this->get_column_names_to_copy("main_collection_group_link");
            $query = "SELECT * FROM main_collection_group_link WHERE main_collection_id = " . $row['id'];
            $lineResult = @mysqli_query($GLOBALS['mysql_con'], $query);
            while($lineRow = mysqli_fetch_array($lineResult)) {
                $finalLineColumns = $collectionGroupLinkColumns;
                $finalLineColumns["main_collection_id"] = $newId;
                $finalLineColumns["main_collection_setup_group_id"] = $this->get_new_id($lineRow['main_collection_setup_group_id'], 'main_collection_setup_group');

                $query = "INSERT INTO main_collection_group_link 
                            SELECT " . join(", ", $finalLineColumns) . " FROM main_collection_group_link 
                            WHERE id = " . $lineRow['id'];
                @mysqli_query($GLOBALS['mysql_con'], $query);
                $newLineId = mysqli_insert_id($GLOBALS['mysql_con']);

                $this->insert_copy_link($lineRow['id'], $newLineId, "main_collection_group_link");
            }

            // collection link kopieren
            $collectionLinkColumns = $this->get_column_names_to_copy("main_collection_link");
            $query = "SELECT * FROM main_collection_link WHERE main_collection_id = " . $row['id'];
            $lineResult = @mysqli_query($GLOBALS['mysql_con'], $query);
            while($lineRow = mysqli_fetch_array($lineResult)) {
                $finalLineColumns = $collectionLinkColumns;
                $finalLineColumns["main_collection_id"] = $newId;
                $finalLineColumns["main_collection_setup_content_id"] = $this->get_new_id($lineRow['main_collection_setup_content_id'], 'main_collection_setup_content');
                if($lineRow['main_sitepart_id'] > 0) {
                    $finalLineColumns['main_sitepart_header_id'] = $this->get_new_id($lineRow['main_sitepart_header_id'], $siteparts[$lineRow['main_sitepart_id']]['header_table']);
                }

                $query = "INSERT INTO main_collection_link 
                            SELECT " . join(", ", $finalLineColumns) . " FROM main_collection_link 
                            WHERE id = " . $lineRow['id'];
                @mysqli_query($GLOBALS['mysql_con'], $query);
                $newLineId = mysqli_insert_id($GLOBALS['mysql_con']);

                if($lineRow['main_sitepart_id'] == 0) {
                    $query = "SELECT type_id from main_collection_setup_content where id = " . $lineRow['main_collection_setup_content_id'];
                    $setupContentResult = @mysqli_query($GLOBALS['mysql_con'], $query);
                    $setupContentRow = mysqli_fetch_array($setupContentResult);
                    if($setupContentRow['type_id'] == 5) { // Bild
                        $this->copy_collection_image($newLineId, $lineRow);
                    }
                }

                $this->insert_copy_link($lineRow['id'], $newLineId, "main_collection_link");
            }
        }

        $this->message = $translation->get("copy_language_collection_finish");
        $this->nextStep = "copy_component";

        return true;
    }

    /**
     * Copy components
     *
     * @return bool
     */
    public function copy_component() {
        $translation = \DynCom\dc\common\classes\Registry::get("translation");
        $siteparts = \DynCom\dc\common\classes\Siteparts::get();
        $table = "main_component";
        $columns = $this->get_column_names_to_copy($table);

        $query = "SELECT * FROM $table WHERE main_language_id = " . $this->languageId;
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        while($row = mysqli_fetch_array($result)) {
            $query = "INSERT INTO $table
                            SELECT " . join(", ", $columns) . " FROM $table
                            WHERE id = " . $row['id'];
            @mysqli_query($GLOBALS['mysql_con'], $query);
            $newId = mysqli_insert_id($GLOBALS['mysql_con']);

            $this->insert_copy_link($row['id'], $newId, $table);

            // component Link kopieren
            $componentLinkColumns = $this->get_column_names_to_copy("main_component_link");
            $query = "SELECT * FROM main_component_link WHERE main_component_id = " . $row['id'];
            $lineResult = @mysqli_query($GLOBALS['mysql_con'], $query);
            while($lineRow = mysqli_fetch_array($lineResult)) {
                $finalLineColumns = $componentLinkColumns;
                $finalLineColumns["main_component_id"] = $newId;
                if($lineRow['main_sitepart_header_id'] > 0) {
                    $finalLineColumns['main_sitepart_header_id'] = $this->get_new_id($lineRow['main_sitepart_header_id'], $siteparts[$lineRow['main_sitepart_id']]['header_table']);
                }
                if($lineRow['main_collection_id'] > 0) {
                    $finalLineColumns['main_collection_id'] = $this->get_new_id($lineRow['main_collection_id'], "main_collection");
                }
                if($lineRow['main_collection_setup_id'] > 0) {
                    $finalLineColumns['main_collection_setup_id'] = $this->get_new_id($lineRow['main_collection_setup_id'], "main_collection_setup");
                }
                if($lineRow['main_collection_page_list_id'] > 0) {
                    $finalLineColumns['main_collection_page_list_id'] = $this->get_new_id($lineRow['main_collection_page_list_id'], "main_navigation");
                }

                $query = "INSERT INTO main_component_link 
                            SELECT " . join(", ", $finalLineColumns) . " FROM main_component_link 
                            WHERE id = " . $lineRow['id'];
                @mysqli_query($GLOBALS['mysql_con'], $query);
                $newLineId = mysqli_insert_id($GLOBALS['mysql_con']);

                $this->insert_copy_link($lineRow['id'], $newLineId, "main_component_link");

                // component collection group link kopieren
                $componentCollectionGoupLinkColumns = $this->get_column_names_to_copy("main_component_collection_group_link");
                $query = "SELECT * FROM main_component_collection_group_link WHERE main_component_link_id = " . $lineRow['id'];
                $collectionGroupLinkResult = @mysqli_query($GLOBALS['mysql_con'], $query);
                while($collectionGroupLinkLine = mysqli_fetch_array($collectionGroupLinkResult)) {
                    $finalGroupLinkLineColumns = $componentCollectionGoupLinkColumns;
                    $finalGroupLinkLineColumns['main_component_link_id'] = $newLineId;
                    $finalGroupLinkLineColumns['main_collection_setup_group_id'] = $this->get_new_id($collectionGroupLinkLine['main_collection_setup_group_id'], "main_collection_setup_group");

                    $query = "INSERT INTO main_component_collection_group_link 
                            SELECT " . join(", ", $finalGroupLinkLineColumns) . " FROM main_component_collection_group_link 
                            WHERE id = " . $collectionGroupLinkLine['id'];
                    @mysqli_query($GLOBALS['mysql_con'], $query);
                    $newGroupLinkLineId = mysqli_insert_id($GLOBALS['mysql_con']);

                    $this->insert_copy_link($collectionGroupLinkLine['id'], $newGroupLinkLineId, "main_component_collection_group_link");
                }
            }
        }

        $this->message = $translation->get("copy_language_component_finish");
        $this->nextStep = "copy_main_page";

        return true;
    }

    /**
     * Copy pages
     *
     * @return bool
     */
    public function copy_main_page($pageId = "", $addCopySuffix = false) {
        $translation = \DynCom\dc\common\classes\Registry::get("translation");
        $siteparts = \DynCom\dc\common\classes\Siteparts::get();
        $table = "main_page";
        $columns = $this->get_column_names_to_copy($table);

        $query = "SELECT * FROM $table WHERE main_language_id = " . $this->languageId;

        if($pageId != "") {
            $query .= " AND id = '" . $pageId."'";
        }

        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        while($row = mysqli_fetch_array($result)) {
            if($addCopySuffix) {
                $columns['title'] = "CONCAT(title, ' (" . $translation->get("copy") . ")')";
            }

            $query = "INSERT INTO $table
                            SELECT " . join(", ", $columns) . " FROM $table
                            WHERE id = " . $row['id'];

            @mysqli_query($GLOBALS['mysql_con'], $query);
            $newId = mysqli_insert_id($GLOBALS['mysql_con']);

            $this->insert_copy_link($row['id'], $newId, $table);

            // page Link kopieren
            $pageLinkColumns = $this->get_column_names_to_copy("main_page_link");
            $query = "SELECT * FROM main_page_link WHERE main_page_id = " . $row['id'] . " AND main_page_link_parent_id = 0";
            $lineResult = @mysqli_query($GLOBALS['mysql_con'], $query);
            while($lineRow = mysqli_fetch_array($lineResult)) {
                $this->helper_copy_page_link($newId, $lineRow, $pageLinkColumns);
            }

            $query = "SELECT * FROM main_page_link WHERE main_page_id = " . $row['id'] . " AND main_page_link_parent_id > 0";
            $lineResult = @mysqli_query($GLOBALS['mysql_con'], $query);
            while($lineRow = mysqli_fetch_array($lineResult)) {
                $this->helper_copy_page_link($newId, $lineRow, $pageLinkColumns);
            }

            // update main page link parent id
            $query = "SELECT * FROM main_page_link WHERE main_page_id = " . $newId . " AND main_page_link_parent_id > 0";
            $lineResult = @mysqli_query($GLOBALS['mysql_con'], $query);
            while($lineRow = mysqli_fetch_array($lineResult)) {
                $query = "UPDATE main_page_link SET main_page_link_parent_id = " . $this->get_new_id($lineRow['main_page_link_parent_id'], "main_page_link") . " WHERE id = " . $lineRow['id'];
                @mysqli_query($GLOBALS['mysql_con'], $query);
            }

            // page component include link kopieren
            if($this->copyPageComponentIcludeLink) {
                $pageComponentIncludeLinkColumns = $this->get_column_names_to_copy("main_page_component_include_link");
                $query = "SELECT * FROM main_page_component_include_link WHERE main_page_id = " . $row['id'];
                $lineResult = @mysqli_query($GLOBALS['mysql_con'], $query);
                while ($lineRow = mysqli_fetch_array($lineResult)) {
                    $finalLineColumns = $pageComponentIncludeLinkColumns;
                    $finalLineColumns["main_page_id"] = $newId;
                    $finalLineColumns["main_component_id"] = $this->get_new_id($lineRow['main_component_id'], 'main_component');

                    $query = "INSERT INTO main_page_component_include_link 
                                SELECT " . join(", ", $finalLineColumns) . " FROM main_page_component_include_link 
                                WHERE id = " . $lineRow['id'];
                    @mysqli_query($GLOBALS['mysql_con'], $query);
                    $newLineId = mysqli_insert_id($GLOBALS['mysql_con']);

                    $this->insert_copy_link($lineRow['id'], $newLineId, "main_page_component_include_link");
                }
            }

            // page layout inclusions link kopieren
            if($this->copyPageLayoutInclusionLink) {
                $pageLayoutInclusionsColumns = $this->get_column_names_to_copy("main_page_layout_inclusion_link");
                $query = "SELECT * FROM main_page_layout_inclusion_link WHERE main_page_id = " . $row['id'];
                $lineResult = @mysqli_query($GLOBALS['mysql_con'], $query);
                while ($lineRow = mysqli_fetch_array($lineResult)) {
                    $finalLineColumns = $pageLayoutInclusionsColumns;
                    $finalLineColumns["main_page_id"] = $newId;

                    $query = "INSERT INTO main_page_layout_inclusion_link 
                                SELECT " . join(", ", $finalLineColumns) . " FROM main_page_layout_inclusion_link 
                                WHERE id = " . $lineRow['id'];
                    @mysqli_query($GLOBALS['mysql_con'], $query);
                    $newLineId = mysqli_insert_id($GLOBALS['mysql_con']);

                    $this->insert_copy_link($lineRow['id'], $newLineId, "main_page_layout_inclusion_link");
                }
            }
        }

        $this->message = $translation->get("copy_language_page_finish");
        $this->nextStep = "copy_main_navigation";

        return true;
    }

    /**
     * Helper function to copy a page_link row
     *
     * @param $pageId
     * @param $lineRow
     * @param $columns
     */
    protected function helper_copy_page_link($pageId, $lineRow, $columns) {
        $siteparts = \DynCom\dc\common\classes\Siteparts::get();
        $finalLineColumns = $columns;
        $finalLineColumns["main_page_id"] = $pageId;
        if($lineRow['main_sitepart_header_id'] > 0) {
            $finalLineColumns['main_sitepart_header_id'] = $this->get_new_id($lineRow['main_sitepart_header_id'], $siteparts[$lineRow['main_sitepart_id']]['header_table']);
        }
        if($lineRow['main_collection_id'] > 0) {
            $finalLineColumns['main_collection_id'] = $this->get_new_id($lineRow['main_collection_id'], "main_collection");
        }
        if($lineRow['main_collection_setup_id'] > 0) {
            $finalLineColumns['main_collection_setup_id'] = $this->get_new_id($lineRow['main_collection_setup_id'], "main_collection_setup");
        }
        if($lineRow['main_collection_page_list_id'] > 0) {
            $finalLineColumns['main_collection_page_list_id'] = $this->get_new_id($lineRow['main_collection_page_list_id'], "main_navigation");
        }

        // richtige Layout area auslesen
        $query = "SELECT * FROM main_layout_area WHERE id = " . $lineRow['layout_area_id'];
        $result = @mysqli_query($GLOBALS['mysql_con'],$query);
        $layoutAreaRow = @mysqli_fetch_array($result);

        $query = "SELECT * FROM main_layout_area WHERE main_layout_id = " . (int)$this->mainLayoutId . " AND code = '" . $layoutAreaRow['code'] . "'";
        $result = @mysqli_query($GLOBALS['mysql_con'],$query);
        if(@mysqli_num_rows($result) > 0) {
            $layoutAreaRow = @mysqli_fetch_array($result);
            $finalLineColumns['layout_area_id'] = $layoutAreaRow['id'];
        } else {
            $finalLineColumns['layout_area_id'] = 0;
        }

        $query = "INSERT INTO main_page_link 
                            SELECT " . join(", ", $finalLineColumns) . " FROM main_page_link 
                            WHERE id = " . $lineRow['id'];

        @mysqli_query($GLOBALS['mysql_con'], $query);
        $newLineId = mysqli_insert_id($GLOBALS['mysql_con']);

        $this->insert_copy_link($lineRow['id'], $newLineId, "main_page_link");

        // page collection group link kopieren
        $pageCollectionGoupLinkColumns = $this->get_column_names_to_copy("main_page_collection_group_link");
        $query = "SELECT * FROM main_page_collection_group_link WHERE main_page_link_id = " . $lineRow['id'];
        $lineResult = @mysqli_query($GLOBALS['mysql_con'], $query);
        while ($collectionGroupRow = mysqli_fetch_array($lineResult)) {
            $finalLineColumns = $pageCollectionGoupLinkColumns;
            $finalLineColumns['main_page_link_id'] = $newLineId;
            $finalLineColumns['main_collection_setup_group_id'] = $this->get_new_id($collectionGroupRow['main_collection_setup_group_id'], "main_collection_setup_group");

            $query = "INSERT INTO main_page_collection_group_link 
                            SELECT " . join(", ", $finalLineColumns) . " FROM main_page_collection_group_link 
                            WHERE id = " . $collectionGroupRow['id'];
            @mysqli_query($GLOBALS['mysql_con'], $query);
            $newGroupLinkLineId = mysqli_insert_id($GLOBALS['mysql_con']);

            $this->insert_copy_link($collectionGroupRow['id'], $newGroupLinkLineId, "main_page_collection_group_link");
        }

        // Layout klassen kopieren
        $pageLinkLayoutClassColumns = $this->get_column_names_to_copy("main_page_link_layout_class_link");
        $query = "SELECT * FROM main_page_link_layout_class_link WHERE main_page_link_id = " . $lineRow['id'];
        $lineResult = @mysqli_query($GLOBALS['mysql_con'], $query);
        while ($pageLinkLayoutClassRow = mysqli_fetch_array($lineResult)) {
            $finalLineColumns = $pageLinkLayoutClassColumns;
            $finalLineColumns['main_page_link_id'] = $newLineId;

            $query = "SELECT * FROM main_layout_class WHERE id = " . $pageLinkLayoutClassRow['main_layout_class_id'];
            $result = @mysqli_query($GLOBALS['mysql_con'],$query);
            $layoutClassRow = @mysqli_fetch_array($result);

            $query = "SELECT * FROM main_layout_class WHERE main_layout_id = " . (int)$this->mainLayoutId . " AND code = '" . $layoutClassRow['code'] . "'";
            $result = @mysqli_query($GLOBALS['mysql_con'],$query);
            if(@mysqli_num_rows($result) > 0) {
                $layoutClassRow = @mysqli_fetch_array($result);
                $finalLineColumns['main_layout_class_id'] = $layoutClassRow['id'];
            } else {
                continue;
            }

            $query = "INSERT INTO main_page_link_layout_class_link 
                            SELECT " . join(", ", $finalLineColumns) . " FROM main_page_link_layout_class_link 
                            WHERE id = " . $pageLinkLayoutClassRow['id'];
            @mysqli_query($GLOBALS['mysql_con'], $query);
            $newpageLinkId = mysqli_insert_id($GLOBALS['mysql_con']);

            $this->insert_copy_link($pageLinkLayoutClassRow['id'], $newpageLinkId, "main_page_link_layout_class_link");
        }
    }

    /**
     * Get all Column names for an insert statement to copy
     *
     * @param $table
     * @return array
     */
    protected function get_column_names_to_copy($table) {
        $columns = array();
        $query = "SHOW COLUMNS FROM " . $table;
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if($result === false) {
            return false;
        }
        while($row = mysqli_fetch_array($result)) {
            if($row['Field'] == "id") {
                $columns[] = 'null';
                continue;
            }

            if($row['Field'] == "main_language_id") {
                $columns[] = $this->newLanguageId;
                continue;
            }

            $columns[$row['Field']] = $row['Field'];
        }

        return $columns;
    }

    /**
     * Helper function to store a new generated id of a table
     *
     * @param $oldId
     * @param $newId
     * @param $table
     */
    protected function insert_copy_link($oldId, $newId, $table) {
        $query = "INSERT INTO copy_link (old_id, new_id, source_table, session_id) VALUES 
                  (
                    " . $oldId . ",
                    " . $newId . ",
                    '" . $table . "',
                    '" . session_id() . "'
                  )";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }

    /**
     * Get new generated id of a table for a specific oldId
     *
     * @param $oldId
     * @param $table
     * @return array|bool|null
     */
    public function get_new_id($oldId, $table) {
        $query = "SELECT new_id FROM copy_link WHERE old_id = '".$oldId."' AND source_table = '" . $table . "' AND session_id = '" . session_id() . "'";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if(@mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);
            return $row['new_id'];
        } else {
            return $oldId;
        }
    }

    /**
     * Add Sitepart Id for copy
     *
     * @param $sitePartId
     */
    public function addSitePartId($sitePartId) {
        if(!in_array($sitePartId, $this->sitePartIds)) {
            $this->sitePartIds[] = $sitePartId;
        }
    }

    /**
     * Add Sitepart Header Id for copy
     *
     * @param $sitePartId
     */
    public function addSitePartHeaderId($sitePartHeaderId) {
        if(!in_array($sitePartHeaderId, $this->sitePartHeaderIds)) {
            $this->sitePartHeaderIds[] = $sitePartHeaderId;
        }
    }

    /**
     * Set mode to copy only specific siteparts
     *
     */
    public function set_copyOnlySpecificSiteparts() {
        $this->sitePartdIdsFromArrayOnly = true;
        if($this->sitePartIds === null) {
            $this->sitePartIds = array();
        }

        if($this->sitePartHeaderIds === null) {
            $this->sitePartHeaderIds = array();
        }
    }

    /**
     * Set no copy for page component include link
     *
     */
    public function set_noCopyPageComponentIncludeLink() {
        $this->copyPageComponentIcludeLink = false;
    }

    /**
     * Set no copy for page collection group link
     *
     */
    public function set_noCopyPageCollectionGroupLink() {
        $this->copyPageCollectionGroupLink = false;
    }

    /**
     * Set no copy for page layout inclusion link
     *
     */
    public function set_noCopyPageLayoutInclusionLink() {
        $this->copyPageLayoutInclusionLink = false;
    }
}