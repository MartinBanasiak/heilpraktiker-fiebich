<?php
namespace DynCom\dc\common\classes;
use Monolog\Formatter\HtmlFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\SwiftMailerHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 06.05.2016
 * Time: 10:42
 */
class GeneralErrorExceptionHandling
{

    protected const DEFAULT_ERROR_LOG_PATH                            = '/logs/unhandled_errors.log';
    protected const DEFAULT_ERROR_LOGGER_NAME                         = 'UnhandledErrorLogging';

    protected const DEFAULT_EXCEPTION_LOG_PATH                        = '/logs/unhandled_exceptions.log';
    protected const DEFAULT_EXCEPTION_LOGGER_NAME                     = 'UnhandledExceptionLogging';

    protected const DEFAULT_ERROR_MAIL_SENDER_ADDR                    = 'error@projectname.dc-solution';
    protected const DEFAULT_ERROR_MAIL_SUBJECT                        = 'Unhandled Error in project projectname';

    protected const DEFAULT_EXCEPTION_MAIL_SENDER_ADDR                = 'exception@projectname.dc-solution';
    protected const DEFAULT_EXCEPTION_MAIL_SUBJECT                    = 'Unhandled Exception in project projectname';

    protected const DEFAULT_ERROR_EXCEPTION_MAIL_RECIPIENT_ADDRESS    = '';
    protected const DEFAULT_ERROR_EXCEPTION_MAIL_RECIPIENT_NAME       = '';


    private static $isSetAsErrorHandler;
    private static $isSetAsExceptionHandler;
    private static $redirectErrorPath;
    private static $redirectErrorMsg;
    private static $redirectExceptionPath;
    private static $redirectExceptionMsg;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private static $defaultErrorLogger;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private static $defaultExceptionLogger;


    /**
     * @return LoggerInterface
     */
    public static function getDefaultErrorLogger()
    {
        if (self::$defaultErrorLogger === null) {
            $baseDir = dirname(dirname(dirname(__DIR__)));
            $loggerPath = rtrim($baseDir,'/') . self::DEFAULT_ERROR_LOG_PATH;
            if (empty($_SERVER['DOCUMENT_ROOT'])) { //Output only on CLI
                //echo "Class " . __CLASS__ . " logging errors to path [$loggerPath]" . PHP_EOL;
            }
            self::$defaultErrorLogger = self::createDefaultLogger($loggerPath, self::DEFAULT_ERROR_LOGGER_NAME,self::DEFAULT_ERROR_MAIL_SUBJECT,self::DEFAULT_ERROR_MAIL_SENDER_ADDR);
        }
        return self::$defaultErrorLogger;
    }

    /**
     * @return Logger|LoggerInterface
     */
    public static function getDefaultExceptionLogger()
    {
        if (self::$defaultExceptionLogger === null) {
            $baseDir = dirname(dirname(dirname(__DIR__)));
            $loggerPath = rtrim($baseDir,'/') . self::DEFAULT_EXCEPTION_LOG_PATH;
            if (empty($_SERVER['DOCUMENT_ROOT'])) { //Output only on CLI
                  //echo "Class " . __CLASS__ . " logging exceptions to path [$loggerPath]" . PHP_EOL;
            }
            self::$defaultExceptionLogger = self::createDefaultLogger($loggerPath, self::DEFAULT_EXCEPTION_LOGGER_NAME,self::DEFAULT_EXCEPTION_MAIL_SUBJECT,self::DEFAULT_EXCEPTION_MAIL_SENDER_ADDR);
        }
        return self::$defaultExceptionLogger;
    }

    /**
     * @param $logFilePath
     * @param $loggerName
     * @param $mailSubject
     * @param $mailFromAddr
     * @return \Monolog\Logger
     */
    private static function createDefaultLogger($logFilePath,$loggerName,$mailSubject,$mailFromAddr)
    {
        $handlers = [];
        $rotatingFileHandler = new RotatingFileHandler($logFilePath, 10);
        $formatter = new LineFormatter(null, null, false, true);
        $rotatingFileHandler->setFormatter($formatter);
        $handlers[] = $rotatingFileHandler;

        $mailRecipientAddr = self::DEFAULT_ERROR_EXCEPTION_MAIL_RECIPIENT_ADDRESS;
        if ($mailRecipientAddr !== '' && filter_var($mailRecipientAddr,FILTER_VALIDATE_EMAIL)) {
            $handlers[] = self::getDefaultMailHandler($mailSubject,$mailFromAddr,self::DEFAULT_ERROR_EXCEPTION_MAIL_RECIPIENT_ADDRESS,self::DEFAULT_ERROR_EXCEPTION_MAIL_RECIPIENT_NAME);
        }
        $logger = new Logger($loggerName,$handlers);
        return $logger;
    }

    /**
     * @param $subject
     * @param $fromAddr
     * @param $toAddr
     * @param $toName
     * @return SwiftMailerHandler
     */
    private static function getDefaultMailHandler($subject, $fromAddr, $toAddr, $toName)
    {
        $smtpTransporter = \Swift_SmtpTransport::newInstance();
        $mailer = \Swift_Mailer::newInstance($smtpTransporter);
        $msg = \Swift_Message::newInstance($subject);
        $msg->addFrom($fromAddr);
        $msg->addTo($toAddr, $toName);
        $mailFormatter = new HtmlFormatter();
        $mailHandler = new SwiftMailerHandler($mailer, $msg);
        $mailHandler->setFormatter($mailFormatter);
        return $mailHandler;
    }

    /**
     * @param $exception
     */
    public static function handleException($exception)
    {
        $logger = self::getDefaultExceptionLogger();
        $msg =  'An unhandled exception occurred! ' . PHP_EOL .
            "    Errno: {$exception->getCode()}" . PHP_EOL .
            "    Msg: {$exception->getMessage()}"  . PHP_EOL;

        $fileName = $exception->getFile();
        if ($fileName !== '') {
            $msg .=     "    File: $fileName " . PHP_EOL;
        }

        $lineNo = $exception->getLine();
        if ($lineNo !== 0) {
            $msg .=     "    Line: $lineNo" . PHP_EOL;
        }

        $traceString = str_replace("\n",PHP_EOL,$exception->getTraceAsString());
        if ($traceString !== '') {
            $msg .=     "    Trace:  $traceString" .  PHP_EOL;
        }
        $msg .= PHP_EOL;

        $displayErrors = (bool)getenv('DISPLAY_ERRORS');
        $hideErrorPage = (bool)getenv('HIDE_ERROR_PAGE');
        if($displayErrors && $hideErrorPage)
        {
            echo $msg;
        }

        $logger->addError($msg);
        self::doConditionalRedirect(self::$redirectExceptionPath,self::$redirectExceptionMsg);
    }

    /**
     * @param $errno
     * @param $errstr
     * @param string $errfileName
     * @param null $errLine
     * @param array|null $errContext
     */
    public static function handleError($errno, $errstr, $errfileName = '', $errLine = null, array $errContext = null)
    {
        $logger = self::getDefaultErrorLogger();
        $msg =  'An error occurred!' . PHP_EOL .
                "    Errno: $errno" . PHP_EOL .
                "    Msg: $errstr"  . PHP_EOL;
        if ($errfileName !== '') {
            $msg .=     "    File: $errfileName" . PHP_EOL;
        }
        if ($errLine !== null) {
            $msg .=     "    Line: $errLine" . PHP_EOL;
        }
        if ($errContext !== null && count($errContext) > 0) {
            $msg .=     '    Context Variables: ' . print_r($errContext,1) .  PHP_EOL;
        }
        $msg .= PHP_EOL;

        $logger->addError($msg);
        self::doConditionalRedirect(self::$redirectErrorPath,self::$redirectErrorMsg);
    }

    /**
     * @param string $redirectToPagePath
     * @param string $redirectWithMessage
     */
    public static function setExceptionHandler($redirectToPagePath = '', $redirectWithMessage = '')
    {
        if (!self::$isSetAsExceptionHandler) {
            self::$redirectExceptionPath = $redirectToPagePath;
            self::$redirectExceptionMsg = $redirectWithMessage;
            $callable = array(__CLASS__,'handleException');
            set_exception_handler($callable);
        }
    }

    /**
     * @param string $redirectToPagePath
     * @param string $redirectWithMessage
     */
    public static function setErrorHandler($redirectToPagePath = '', $redirectWithMessage = '')
    {
        if (!self::$isSetAsErrorHandler) {
            self::$redirectErrorPath = $redirectToPagePath;
            self::$redirectErrorMsg = $redirectWithMessage;
            $callable = array(__CLASS__,'handleError');
            set_error_handler($callable,self::getErrorHandlingLevel());
        }
    }

    /**
     * @return int
     */
    private static function getErrorHandlingLevel()
    {
        return E_ALL^E_NOTICE^E_STRICT^E_WARNING;
    }

    /**
     * @param string $path
     * @param string $msg
     */
    private static function doConditionalRedirect($path = '', $msg = '')
    {
        if ($path !== '' && !self::isPathCurrentUrl($path)) {
            self::saveErrorFlashMessageToSession($msg);
            universal_redirect($path);
        }

    }

    /**
     * @param string $msg
     */
    private static function saveErrorFlashMessageToSession($msg = '')
    {
        static $i = null;
        if ($msg !== '') {
            $errorMsgs = isset($_SESSION['msgBag']['errors']) ? $_SESSION['msgBag']['errors'] : [];
            if (!in_array($msg,$errorMsgs,true)) {
              $errorMsgs[] = $msg;
            }
            $_SESSION['msgBag']['errors'] = $errorMsgs;
        }
    }

    /**
     * @param string $path
     * @return bool
     */
    private static function isPathCurrentUrl($path = '')
    {
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        $request = $_SERVER['REQUEST_URI'];
        $currPath = $protocol.$domainName.$request;
        return $path === $currPath;
    }

}

