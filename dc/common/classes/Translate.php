<?php
namespace DynCom\dc\common\classes;
/**
 * Translation class for admin backend translation
 *
 * Example usage:
 * $translate = new \DynCom\dc\common\classes\Translate();
 * echo $translate->get('translation_index');
 *
 * @package  DcCMS admin
 * @author   Tomasz Brniak <tomaszbrniak@web.de>
 * @access   public
 */
class Translate {

    /**
     * Store all text constants of the current language
     *
     * @access private
     */
    private $_constants = array();

    /**
     * current language
     *
     * @access private
     */
    private $_language;

    /**
     * init translation class
     *
     * @param string $_lang current language code
     *
     * @access public
     */
    public function __construct( $_lang = 'de' ) {
        $this->_language = $_lang;
        $this->_loadFile();
    }

    /**
     * Load all Text constants
     * Uses a fixed file "text_constants.inc.php"
     *
     * @access private
     * @return void
     */
    private function _loadFile() {
        $text_constant = array();
        $path = CMS_PATH . "admin/text_constants.inc.php";
        require($path);
        $this->_constants = $text_constant[$this->_language];
    }

    /**
     * Get the translation of the given key
     *
     * @param string $_key unique key of the translation constant
     *
     * @access public
     * @return string
     */
    public function get( $_key ) {
        if (isset($this->_constants[$_key])) {
            return $this->_constants[$_key];
        } else {
            return $_key;
        }
    }
}
