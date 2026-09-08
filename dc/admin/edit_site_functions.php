<?php
/**
 * Extra Site functions class
 *
 * User: tomaszbrniak
 * Date: 02.07.16
 * Time: 15:04
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_language_functions.php';

class SiteFunctions {

    /**
     * Error messages
     *
     * @var array
     */
    public $errorMessages = array();

    /**
     * Public message for user
     *
     * @var string
     */
    public $message = "";

    /**
     * current language Id for copy
     *
     * @var int
     */
    public $copyLanguageId = 0;

    /**
     * copy website, all languages and contents
     *
     * @param $siteId - id from main_site
     * @return bool
     */
    public function copy_site($siteId) {
        $translation = \DynCom\dc\common\classes\Registry::get("translation");
        $columns = $this->get_column_names_to_copy("main_site");
        $query = "INSERT INTO main_site SELECT " . join(", ", $columns) . " FROM main_site where id = '" . $siteId."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $newSiteId = mysqli_insert_id($GLOBALS['mysql_con']);
        if((int)$newSiteId <= 0) {
            $this->errorMessages[] = $translation->get("copy_failed");
            return false;
        }

        $this->insert_copy_link($siteId, $newSiteId, "main_site");
        $query = "UPDATE main_site set name = CONCAT(name, ' (" . $translation->get("copy") . ")'), code = CONCAT(code, '-$newSiteId') WHERE id = " . $newSiteId;
        @mysqli_query($GLOBALS['mysql_con'], $query);

        return true;
    }

    /**
     * Copy a language of a site
     *
     * @param $siteId
     * @return bool
     */
    public function copy_language($siteId) {
        $translation = \DynCom\dc\common\classes\Registry::get("translation");
        $query = "SELECT * FROM main_language where main_site_id = '" . $siteId."'";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if(@mysqli_num_rows($result) == 0) {
            $this->errorMessages[] = $translation->get("copy_no_language");
            return false;
        }

        while($row = mysqli_fetch_array($result)) {
            $newLanguageId = $this->get_new_id($row['id'], "main_language");
            if($newLanguageId === false) {
                $this->message = $translation->get("copy_start_language") . " " . $row['name'];
                $this->copyLanguageId = $row['id'];
            } else {
                continue;
            }

            $newSiteId = $this->get_new_id($siteId, "main_site");

            $LanguageFunctions = new LanguageFunctions($row['id'], $newSiteId);
            if(!$LanguageFunctions->copy_language()) {
                $this->errorMessages = $LanguageFunctions->errorMessages;
                return false;
            }

            return true; // kopieren hier erstmal abbrechen
        }

        return true;
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
        $query = "SELECT new_id FROM copy_link WHERE old_id = $oldId AND source_table = '" . $table . "' AND session_id = '" . session_id() . "'";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if(@mysqli_num_rows($result) == 0) {
            return false;
        }
        $row = mysqli_fetch_array($result);
        return $row['new_id'];
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
        while($row = mysqli_fetch_array($result)) {
            if($row['Field'] == "id") {
                $columns[] = 'null';
                continue;
            }

            if($row['Field'] == "main_language_id") {
                $columns[] = $this->newLanguageId;
                continue;
            }

            if($row['Field'] == "modified_date") {
                $columns[] = time();
                continue;
            }

            if($row['Field'] == "modified_user") {
                $columns[] = (int)$GLOBALS["admin_user"]['id'];
                continue;
            }

            $columns[$row['Field']] = $row['Field'];
        }

        return $columns;
    }
}