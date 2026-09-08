<?php
namespace DynCom\dc\common\classes;
/**
 * Registry pattern class
 *
 * Example usage:
 * \DynCom\dc\common\classes\Registry::set('translation', new \DynCom\dc\common\classes\Translate());
 * $translation = \DynCom\dc\common\classes\Registry::get('translation');
 *
 * @package  DcCMS admin
 * @author   Tomasz Brniak <tomaszbrniak@web.de>
 * @access   public
 */
class Registry {

    /**
     * Registry array of objects
     *
     * @access private
     */
    private static $objects = array();

    /**
     * The instance of the registry
     *
     * @access private
     */
    private static $instance;

    /**
     * Prevent directly access.
     *
     * @access private
     */
    private function __construct() {
    }

    /**
     * Get stored object
     *
     * @param string $_key
     *
     * @access public
     * @static
     * @return mixed
     */
    public static function get( $_key ) {
        static $instance;
        if ($instance === null) {
            $instance =self::getInstance();
        }
        $obj = $instance->getObject($_key);
        return $obj;
    }

    /**
     * Get object protected function you can use only
     * inside from this class.
     *
     * @param string $_key unique key
     *
     * @access protected
     * @return mixed
     */
    protected function getObject( $_key ) {
        if (isset(self::$objects[$_key])) {
            return self::$objects[$_key];
        }
        return NULL;
    }

    /**
     * Singleton method used to access the object
     *
     * @access public
     */
    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Store object
     *
     * @param $_key
     * @param $_instance
     *
     * @access public
     * @static
     * @return mixed
     */
    public static function set( $_key, $_instance ) {
        return self::getInstance()->storeObject($_key, $_instance);
    }

    /**
     * Set object protected function you can use only
     * inside from this class.
     *
     * @param string $_key
     * @param mixed  $_val
     *
     * @access protected
     * @return void
     */
    protected function storeObject( $_key, $_val ) {
        self::$objects[$_key] = $_val;
    }

    /**
     * Prevent clone
     *
     * @access private
     * @return void
     */
    private function __clone() {
    }
}