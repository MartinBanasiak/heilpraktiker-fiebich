<?php
error_reporting (E_ALL);
ini_set('display_errors',1);
$rootDir = dirname(dirname(dirname(__DIR__)));
$moduleDir = $rootDir . '/module';
$dcShopDir = $moduleDir . '/dcshop';
$marketplaceDir = $dcShopDir . '/marketplaces';

require_once($rootDir . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

if (!isset($GLOBALS['logger']) || !($GLOBALS['logger'] instanceof \Monolog\Logger)) {
    $logfileName = $marketplaceDir . DIRECTORY_SEPARATOR . 'workers.log';
    $fileLogger = new \Monolog\Handler\RotatingFileHandler($logfileName, 10);
    $logger = new \Monolog\Logger('QueueWorkerLogger',[$fileLogger]);
    $GLOBALS['logger'] = $logger;
}

//load environment variables
$dotenv = new \Dotenv\Dotenv($rootDir.'/config/');
$dotenv->load();

require_once($rootDir . "/dc/common/common_functions.inc.php");
require_once((local_environment()) ? $rootDir . "/dc/dc.config.php" : $rootDir . "/dc/dc-server.config.php");
require_once($dcShopDir . "/common/shop_functions.inc.php");
require_once($dcShopDir . "/common/item_functions.inc.php");
require_once($dcShopDir . "/common/navconnect_functions.inc.php");


// Verbindung mit Datenbank herstellen
db_connect();


// kein teimlimit
set_time_limit(0);

include "marketplace_functions.inc.php";


abstract class WorkerAbstract {

    /**
     * Von der Anfrage zurueckgegebene ID
     *
     * @access protected
     * @var int
     */
    protected $submissionId = 0;

    /**
     * Aktuell vom worker verarbeitende Zeile
     *
     * @access protected
     * @var array
     */
    protected $queueRow = null;

    protected $payload = null;

    /**
     * Zeile erfolgreich veratbeitet ja/nein
     *
     * @access protected
     * @var bool
     */
    protected $processOk = false;


    /**
     * Gibt an, ob das skript als dauerläufer ausgeführt werden soll
     * Über das Posix Signal Handling können die skripte dennoch herunter gefahren werden
     * @var bool
     */
    protected $continuous = false;

    protected $parentQueueId = 0;

    protected $lastQueueId = false;

    protected $currentQueueId = 0;

    /**
     * @param boolean $continuous
     */
    public function setContinuous($continuous)
    {
        $this->continuous = (bool)$continuous;
    }



    /**
     * Setzt processOK
     *
     * @access protected
     */
    protected function setProcessOk($processOk) {
        $this->processOk = $processOk;
    }

    /**
     * Gibt zurueck, ob Zeile erfolgreich verarbeitet werden konnte
     *
     * @access protected
     * @return bool
     */
    protected function processOk() {
        return $this->processOk;
    }

    /**
     * Fuegt eine Errormessage hinzu
     *
     * @access protected
     */
    protected function insertError($marketplace_type, $operation, $parameter, $errormsg) {

        $query = "INSERT INTO shop_marketplace_errors (marketplace_type, operation, parameter, errormsg, timestamp) VALUES (
			" . $marketplace_type . ",
			'" . $operation . "',
			'" . $parameter . "',
			'" . $errormsg . "',
			" . time() . "
		)";
        @mysqli_query($GLOBALS['mysql_con'], $query);

    }


    protected function setupSignalHandling()
    {
        pcntl_signal(SIGTERM, function($signo) {$this->handlePosixSignal($signo);});
        pcntl_signal(SIGHUP,  function($signo) {$this->handlePosixSignal($signo);});
        pcntl_signal(SIGINT, function($signo) {$this->handlePosixSignal($signo);});
    }

    protected function handlePosixSignal($sig)
    {
        $this->logger->debug(__FILE__ . ' has received posix signal ' . $sig . ' ... handling...');
        switch($sig)
        {
            case SIGHUP:
                $this->logger->debug('...signal is SIGHUP - calling handleSIGTERM method.');
                $this->handleSIGHUP();
                break;
            case SIGINT:
                $this->logger->debug('...signal is SIGINT - calling handleSIGTERM method.');
                $this->handleSIGINT();
                break;
            case SIGTERM:
                $this->logger->debug('...signal is SIGTERM - calling handleSIGTERM method.');
                $this->handleSIGTERM();
                break;
            default:
                break;
        }
    }

    /**
     * Funktion fuer das Hauptprogramm
     *
     * @access public
     * @return void
     */
    public function run() {

        if ($this->continuous) {
            //handle signals
            $this->setupSignalHandling();
            $sleepDuration = (int)$this->getSleepDuration();
            while (true) {
                pcntl_signal_dispatch();
                $this->processJob();
                sleep($sleepDuration);
            }
        } else {
            $this->processJob();
        }

    }

    protected function handleSIGINT()
    {
        $this->releaseJob();
        exit();
    }

    protected function handleSIGTERM()
    {
        $this->releaseJob();
        exit();
    }

    protected function handleSIGHUP()
    {
        $this->releaseJob();
        exit();
    }


    /**
     * @var int
     */
    protected $pid;
    /**
     * @var string
     */
    protected $user;
    /**
     * @var string
     */
    protected $group;
    /**
     * @var int
     */
    protected $starttime;
    /**
     * @var \Monolog\Logger
     */
    protected $logger;



    public function __construct()
    {
        $this->setLogger();
        $this->registerProcess();
    }

    protected function setLogger()
    {
        $logger = $GLOBALS['logger'];
        if (!($logger instanceof \Monolog\Logger)) {
            $logfileName = __DIR__ . DIRECTORY_SEPARATOR . 'workers.log';
            $fileLogger = new \Monolog\Handler\RotatingFileHandler($logfileName, 10);
            $logger = new \Monolog\Logger('QueueWorkerLogger', [$fileLogger]);
            $GLOBALS['logger'] = $logger;
        }
        $this->logger = $logger;
    }

    protected function registerProcess() {

        $pid = getmypid();
        $username = get_current_user();
        $groupinfo = posix_getgrgid(posix_getgid());
        $groupname = $groupinfo['name'];
        $startTime = date('Y-m-d G:i:s');;
        $reflectionClass = new ReflectionClass($this);
        $filename = $reflectionClass->getFileName();

        $this->pid = (string)$pid;
        $this->user = (string)$username;
        $this->group = (string)$groupname;
        $this->starttime = (string)$startTime;

        $setupQuery = "
          INSERT INTO queue_worker_processes (pid, start_time, username, groupname, filepath) VALUES ('$pid','$startTime','$username','$groupname','$filename')
          ";
        mysqli_query($GLOBALS['mysql_con'],$setupQuery);

        $teardownQuery = "
        DELETE FROM
          queue_worker_processes
        WHERE
          pid = {$this->pid}
        ";
        $teardownfunction = function() use($teardownQuery) {
            @mysqli_query($GLOBALS['mysql_con'],$teardownQuery);
        };
        register_shutdown_function($teardownfunction);
    }

    abstract protected function getSleepDuration();

    abstract protected function processJob();

    abstract protected function deleteJob();

    abstract protected function releaseJob();
}