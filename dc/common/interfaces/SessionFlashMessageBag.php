<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 19.09.2016
 * Time: 11:12
 */
interface SessionFlashMessageBag
{
    const TYPE_INFO = 'info';
    const TYPE_NOTICE = 'notice';
    const TYPE_WARNING = 'warning';
    const TYPE_ERROR = 'error';
    const TYPE_SUCCESS = 'success';

    /**
     * sets message(s) for a given type
     * @param string $type
     * @param string|array $message
     */
    public function set($type, $message);

    /**
     * Gets and clears messages of a given type
     * @param string $type
     * @param array $default
     * @return array
     */
    public function get($type, array $default = []);

    /**
     * Gets and clears messages of all types
     * @return mixed
     */
    public function all();

    /**
     * Gets messages of given type without clearing
     * @param string $type
     * @param array $default
     * @return array
     */
    public function peek($type, array $default = []);


    /**
     * Gets messages of all types without clearing
     * @return mixed
     */
    public function peekAll();

    /**
     * @param string $type
     * @return bool
     */
    public function has($type);

    /**
     * Gets all defined types
     * @return array
     */
    public function keys();

    /**
     * Clears all messages
     */
    public function clear();

}