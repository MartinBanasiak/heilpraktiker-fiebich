<?php
namespace DynCom\dc\common\classes;

use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\Observer;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 08.07.2015
 * Time: 13:29
 */
class Hook
{

    /**
     * @var $logger \Psr\Log\LoggerInterface
     */
    protected static $logger;
    protected static $listenedEventNames = [];

    /**
     * @param Observer $listener
     * @param $eventName
     * @param string $namespace
     * @param string $callbackName
     */
    public static function registerEventListener(Observer $listener, $eventName, $namespace = 'global', $callbackName = null)
    {
        self::ensureLoggerSet();
        $listenerID = spl_object_hash($listener);
        static::$listenedEventNames[$namespace][$eventName]['listeners'][$listenerID]['obj'] = &$listener;
        static::$listenedEventNames[$namespace][$eventName]['listeners'][$listenerID]['callback'] = self::getCallbackCallable($listener,$callbackName);
        static::$listenedEventNames[$namespace][$eventName]['listeners'][$listenerID]['IDs'] = [];
        self::$logger->debug('EventListener of class [' . get_class($listener) . '] registered for eventName [' . $eventName . '] in namespace [' . $namespace . '] with callback name [' . $callbackName . ']');
    }


    /**
     * @param Observer $listener
     * @param $callback
     * @return null|\Closure
     */
    protected static function getCallbackCallable(Observer $listener, $callback)
    {
        if (null === $callback || is_callable($callback)) {
            return $callback;
        }
        if (method_exists($listener,$callback)) {
            return function($eventName, $data) use ($listener,$callback) {
                return $listener->$callback($eventName,$data);
            };
        }
    }

    /**
     * @param Observer $listener
     * @param $eventName
     * @param $id
     * @param string $namespace
     */
    public static function addSourceIDForEventListener(Observer $listener, $eventName, $id, $namespace = 'global')
    {
        self::ensureLoggerSet();
        $listenerID = spl_object_hash($listener);
        if (!isset(static::$listenedEventNames[$namespace][$eventName]['listeners'][$listenerID])) {
            throw new \InvalidArgumentException(
                'Observer ' . get_class($listener) . ' is not registered for Event ' . strip_tags($eventName) . ' in Namespace ' . strip_tags($namespace)
            );
        }
        static::$listenedEventNames[$namespace][$eventName]['listeners'][$listenerID]['IDs'][] = $id;
        self::$logger->debug('added SourceID [' . $id . '] for EventListener of class [' . get_class($listener) . '] registered for eventName [' . $eventName . '] in namespace [' . $namespace . ']');
    }

    /**
     * @param Observer $listener
     * @param $eventName
     * @param string $namespace
     */
    public static function unregisterEventListener(Observer $listener, $eventName, $namespace = 'global')
    {
        self::ensureLoggerSet();
        $listenerID = spl_object_hash($listener);
        unset(static::$listenedEventNames[$namespace][$eventName]['listeners'][$listenerID]);
        self::$logger->debug('Unregistered EventListener of class [' . get_class($listener) . '] for eventName [' . $eventName . '] in namespace [' . $namespace . ']');
    }

    /**
     * @param $eventName
     * @param $data
     * @param string $namespace
     */
    public static function update($eventName, $data, $namespace = 'global')
    {
        self::ensureLoggerSet();
        self::$logger->debug('Event of name [' . $eventName . '] occurred');
        if (!is_array(static::$listenedEventNames) || !array_key_exists($namespace, static::$listenedEventNames) || !is_array(static::$listenedEventNames[$namespace]) || !array_key_exists($eventName, static::$listenedEventNames[$namespace])) {
            //No listener for event
            return;
        }
        foreach (static::$listenedEventNames[$namespace][$eventName]['listeners'] as $listenerRegistryEntry) {

            self::notifyEventListener($eventName, $data, $listenerRegistryEntry);
        }
    }

    protected static function ensureLoggerSet()
    {
        if (!self::isLoggerSet()) {
            self::$logger = self::getLogger();
        }
    }

    /**
     * @return bool
     */
    protected static function isLoggerSet()
    {
        return self::$logger instanceof LoggerInterface;
    }

    /**
     * @return Logger
     */
    protected static function getLogger()
    {

        $envGlobalsLoglevel = getenv('LOGLEVEL_GLOBAL');
        $globalLogLevel = $envGlobalsLoglevel ? $envGlobalsLoglevel : 'CRITICAL';
        $envLogLevelHook = getenv('LOGLEVEL_HOOK');
        $hookLogLevel = $envLogLevelHook ? $envLogLevelHook : $globalLogLevel;

        $baseDir = dirname(dirname(dirname(__DIR__)));
        $logDir = rtrim($baseDir, '/') . '/logs/';
        $logFilePath = $logDir . 'hook_log.log';

        $handlers = [];
        $rotatingFileHandler = new RotatingFileHandler($logFilePath, 10);
        $rotatingFileHandler->setLevel($hookLogLevel);
        $formatter = new LineFormatter(null, null, false, true);
        $rotatingFileHandler->setFormatter($formatter);
        $handlers[] = $rotatingFileHandler;

        $logger = new Logger('hook_log', $handlers);

        return $logger;
    }

    /**
     * @param $eventName
     * @param $data
     * @param array $listenerRegistryEntry
     */
    protected static function notifyEventListener($eventName, $data, array $listenerRegistryEntry)
    {
        $listener = array_key_exists('obj',$listenerRegistryEntry) && ($listenerRegistryEntry['obj'] instanceof Observer) ? $listenerRegistryEntry['obj'] : null;

        if ($listener) {
            $idCount = count($listenerRegistryEntry['IDs']);
            if ($idCount === 0 || (($data instanceof Entity) && in_array($data->getID(), $listenerRegistryEntry['IDs'], true))) {
                $listener->notify($eventName, $data);
                self::$logger->debug('Listener of class [' . get_class($listener) . '] found for eventName [' . $eventName . '] notified.');
                if (null !== $listenerRegistryEntry['callback']) {
                    $listenerRegistryEntry['callback']($eventName, $data);
                }
            }
        }
    }

}