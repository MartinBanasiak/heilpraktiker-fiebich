<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\SessionFlashMessageBag;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 19.09.2016
 * Time: 11:35
 */
class NativeSessionFlashMessageBag implements SessionFlashMessageBag
{

    protected const STORAGE_KEY_SUFFIX = '_MSGBAG';

    private $isInitialized = false;

    /**
     * @var string
     */
    private $storageKeyPrefix;

    /**
     * @var string
     */
    private $storageKey;

    /**
     * @var array messages
     */
    private $messages;

    /**
     * NativeSessionFlashMessageBag constructor.
     * @param string $storageKeyPrefix
     */
    public function __construct($storageKeyPrefix = '')
    {
        $this->guardForActiveSession();
        $this->storageKeyPrefix = $storageKeyPrefix;
        $this->initialize($storageKeyPrefix);
    }

    /**
     * sets message(s) for a given type
     * @param string $type
     * @param string|array $message
     */
    public function set($type, $message)
    {
        $this->guardForActiveSession();
        if (is_string($message)) {
            $this->setMessage($type,$message);
        } elseif(is_array($message)) {
            foreach ($message as $msg) {
                $this->setMessage($type,$msg);
            }
        }
    }

    /**
     * Gets and clears messages of a given type
     * @param string $type
     * @param array $default
     * @return array
     */
    public function get($type, array $default = [])
    {
        $this->guardForActiveSession();
        return $this->getMessages($type,true,$default);
    }

    /**
     * Gets and clears messages of all types
     * @return mixed
     */
    public function all()
    {
        $this->guardForActiveSession();
        return $this->getMessages();
    }

    /**
     * Gets messages of given type without clearing
     * @param string $type
     * @param array $default
     * @return array
     */
    public function peek($type, array $default = [])
    {
        $this->guardForActiveSession();
        return $this->getMessages($type,false,$default);
    }

    /**
     * Gets messages of all types without clearing
     * @return mixed
     */
    public function peekAll()
    {
        $this->guardForActiveSession();
        return $this->getMessages('',false);
    }

    /**
     * @param string $type
     * @return bool
     */
    public function has($type)
    {
        $this->guardForActiveSession();
        return (array_key_exists($type,$this->messages) && count($this->messages[$type]) > 0);
    }

    /**
     * Gets all defined types
     * @return array
     */
    public function keys()
    {
        $this->guardForActiveSession();
        return array_keys($this->messages);
    }

    /**
     * Clears all messages
     */
    public function clear()
    {
        $this->guardForActiveSession();
        $this->messages = [];
        $this->persistToSession();
    }

    /**
     * @return bool
     */
    private function isSessionActive()
    {
        //return session_status() === PHP_SESSION_ACTIVE;
        return \strlen(session_id()) > 0;
    }

    private function guardForActiveSession()
    {
        if (!$this->isSessionActive()) {
            throw new \BadMethodCallException('Session must be active in order to use SessionFlashMessageBag.');
        }
    }

    /**
     * @param $storageKeyPrefix
     */
    private function initialize($storageKeyPrefix)
    {
        if (!$this->isInitialized) {
            $sessionID = session_id();
            $this->storageKeyPrefix = $storageKeyPrefix ? $storageKeyPrefix . '_' : '';
            $this->storageKey = $this->storageKeyPrefix . $sessionID . self::STORAGE_KEY_SUFFIX;
            if (array_key_exists($this->storageKey, $_SESSION) && is_array($_SESSION[$this->storageKey])) {
                $this->messages = $_SESSION[$this->storageKey];
            }
            $this->isInitialized = true;
        }
    }

    /**
     * @param $type
     * @param $message
     */
    private function setMessage($type, $message)
    {
        if (!array_key_exists($type,$this->messages) || !is_array($this->messages[$type]) || !in_array($message,$this->messages[$type],true)) {
            $this->messages[$type][] = $message;
            $this->persistToSession();
        }
    }

    /**
     * @param string $type
     * @param bool $clear
     * @param array $defaultValue
     * @return array|mixed
     */
    private function getMessages($type = '', $clear = true, $defaultValue = [])
    {
        if ($type !== '') {
            $msgs = &$this->messages[$type];
        } else {
            $msgs = &$this->messages;
        }
        $returnArr = $defaultValue;
        if (is_array($msgs) && count($msgs) > 0) {
          $returnArr = $msgs;
        }
        if ($clear) {
            $msgs = [];
        }
        $this->persistToSession();
        return $returnArr;
    }

    private function persistToSession()
    {
        $_SESSION[$this->storageKey] = $this->messages;
    }

}