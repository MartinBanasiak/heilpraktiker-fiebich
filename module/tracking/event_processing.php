<?php
namespace DynCom\dc\tracking;

use Dotenv\Dotenv;
use DynCom\dc\pipeline\GenericPipeline;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

//ini_set('display_errors',1);
$startTime = microtime(true);


//Directories
$rootDir = dirname(dirname(__DIR__));
$vendorDir = $rootDir . DIRECTORY_SEPARATOR . 'vendor';
$globalConfigDir = $rootDir . DIRECTORY_SEPARATOR . 'config';
$globalLogsDir = $rootDir . DIRECTORY_SEPARATOR . 'logs';
$globalEnvFilePath = $globalConfigDir . DIRECTORY_SEPARATOR . '.env';
$trackingConfigDir = $globalConfigDir . DIRECTORY_SEPARATOR . 'tracking';
$trackingEnvFilePath = $trackingConfigDir . DIRECTORY_SEPARATOR . '.env';
$moduleDir = $rootDir . DIRECTORY_SEPARATOR . 'module';
$trackingDir = $moduleDir . DIRECTORY_SEPARATOR . 'tracking';

//Vendor autoload
require_once $vendorDir . '/autoload.php';


//Logging
$logLevel = Logger::ERROR;
$envLogLevel = getenv('LOGLEVEL_GLOBAL');
if ($envLogLevel) {
    $logLevel = $envLogLevel;
}

$logFilePath = $rootDir . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'tracking_event_processing.log';
$logFileHandler = new RotatingFileHandler($logFilePath, 10, 'ERROR');
$processor = new PsrLogMessageProcessor();
$logFileHandler->pushProcessor($processor);
$logger = new Logger('TrackingEventProcessing', [$logFileHandler]);
//Environment
if (file_exists($globalEnvFilePath) && is_file($globalEnvFilePath) && is_readable($globalEnvFilePath)) {
    $dotenvGlobal = new Dotenv($globalConfigDir);
    $dotenvGlobal->load();
}

if (file_exists($trackingEnvFilePath) && is_file($trackingEnvFilePath) && is_readable($trackingEnvFilePath)) {
    $dotenvTracking = new Dotenv($trackingConfigDir);
    $dotenvTracking->load();
}


//PDO
$shopDBHost     = getenv('MAIN_MYSQL_DB_HOST');
$shopDBPort     = getenv('MAIN_MYSQL_DB_PORT');
$shopDBSchema   = getenv('MAIN_MYSQL_DB_SCHEMA');
$shopDBUser     = getenv('MAIN_MYSQL_DB_USER');
$shopDBPass     = getenv('MAIN_MYSQL_DB_PASS');
$shopDSN = 'mysql:dbname=' . $shopDBSchema . ';host=' . $shopDBHost . ';port=' . (string)$shopDBPort . ';charset=utf8mb4';

$trackingDBHost     = getenv('TRACKING_MYSQL_DB_HOST');
$trackingDBPort     = getenv('TRACKING_MYSQL_DB_PORT');
$trackingDBSchema   = getenv('TRACKING_MYSQL_DB_SCHEMA');
$trackingDBUser     = getenv('TRACKING_MYSQL_DB_USER');
$trackingDBPass     = getenv('TRACKING_MYSQL_DB_PASS');
$trackingDSN = 'mysql:dbname=' . $trackingDBSchema . ';host=' . $trackingDBHost . ';port=' . (string)$trackingDBPort . ';charset=utf8mb4';

$shopPDO = new \PDO($shopDSN,$shopDBUser,$shopDBPass);
$trackingPDO = new \PDO($trackingDSN,$trackingDBUser,$trackingDBPass);

//Services
$trackingAPIService = new TrackingAPIService();

$pipeline = (new GenericPipeline())
    ->addStage(new TrackingEventBasicValidator(TrackingEventBasicValidator::MODE_CONTINUE_WITH_NULL_PAYLOAD))
    ->addStage(new TrackingEventBasicVisitorDataCopier($shopPDO,$trackingPDO,$logger))
    ->addStage(new TrackingEventBasicUserDataCopier($shopPDO,$trackingPDO,$logger))
    ->addStage(new TrackingEventBasicCustomerDataCopier($shopPDO,$trackingPDO,$logger))
    ->addStage(new TrackingEventBasicItemDataCopier($shopPDO,$trackingPDO,$logger))
    ->addStage(new TrackingEventBasicCategoryDataCopier($shopPDO,$trackingPDO,$logger))
    ->addStage(new TrackingEventUserIdentifiedHandler($shopPDO,$trackingPDO,$trackingAPIService,$logger))
    ->addStage(new TrackingEventCustomerIdentifiedHandler($shopPDO,$trackingPDO,$trackingAPIService,$logger))
    ->addStage(new TrackingEventVisitorCounterUpdater($trackingPDO,$trackingAPIService,$logger))
    ->addStage(new TrackingEventUserCounterUpdater($trackingPDO,$trackingAPIService,$logger))
    ->addStage(new TrackingEventCustomerCounterUpdater($trackingPDO,$trackingAPIService,$logger))
    ->addStage(new TrackingEventItemCounterUpdater($trackingPDO,$trackingAPIService,$logger))
    ->addStage(new TrackingEventVisitorItemCounterUpdater($trackingPDO,$trackingAPIService,$logger))
    ->addStage(new TrackingEventUserItemCounterUpdater($trackingPDO,$trackingAPIService,$logger))
    ->addStage(new TrackingEventCustomerItemCounterUpdater($trackingPDO,$trackingAPIService,$logger))
    ->addStage(new TrackingEventCategoryCounterUpdater($trackingPDO,$trackingAPIService,$logger))
    ->addStage(new TrackingEventVisitorCategoryCounterUpdater($trackingPDO,$trackingAPIService,$logger))
    ->addStage(new TrackingEventUserCategoryCounterUpdater($trackingPDO,$trackingAPIService,$logger))
    ->addStage(new TrackingEventCustomerCategoryCounterUpdater($trackingPDO,$trackingAPIService,$logger));




$queryNextUnhandledEvent = '
    SELECT     
      *
    FROM
      tracking_events
    WHERE 
		last_handler_hash != :pipeline_hash
	ORDER BY creation_timestamp ASC
    LIMIT 1
';

$queryUpdateEvent = '
    INSERT INTO
      tracking_event_handling
    SET
      event_uuid = :event_uuid,
      `handler` = :pipeline_hash,
      handled_timestamp = NOW()
';

$queryUpdateEvent2 = '
	UPDATE
		tracking_events
	SET
		last_handler_hash = :pipeline_hash
	WHERE
		uuid = :event_uuid
';

$pipelineHash = $pipeline->getHash();

$limitPerExecution = 0;
$envLimit = getenv('TRACKING_EVENT_PROCESSING_MAX_EVENTS_PER_EXECUTION');
if ($envLimit) {
    $limitPerExecution = (int)$envLimit;
} else {
	$limitPerExecution = 500;
}
$noOfHandledEvents = 0;
$handledUUIDs = [];
$lastEventUUID = null;
$lastCreationTimestamp = '';

do {
    $getStmt = $trackingPDO->prepare($queryNextUnhandledEvent);
    $getStmt->bindValue(':pipeline_hash',$pipelineHash,\PDO::PARAM_STR);
    $getStmt->execute();
    $resArr = $getStmt->fetch(\PDO::FETCH_ASSOC);
    $isValidLine = is_array($resArr) && array_key_exists('uuid',$resArr) && $resArr['uuid'];
    if ($isValidLine && $lastEventUUID !== $resArr['uuid'] && !in_array($resArr['uuid'],$handledUUIDs,true)) {
        $evt = new TrackingEvent($resArr['uuid'],$resArr['session_id'],$resArr['event_type'],$resArr['creation_timestamp'],$resArr['last_modified_timestamp'],json_decode($resArr['event_data'],true));
        $pipeline->process($evt);
        $handledUUIDs[] = $evt->getUuid();
		$lastEventUUID = $evt->getUuid();
		$lastCreationTimestamp = $resArr['creation_timestamp'];
        $noOfHandledEvents++;
        $handledStmt = $trackingPDO->prepare($queryUpdateEvent2);
        $handledStmt->bindValue(':event_uuid',$evt->getUuid(),\PDO::PARAM_STR);
        $handledStmt->bindValue(':pipeline_hash',$pipelineHash,\PDO::PARAM_STR);
        $handledStmt->execute();
    }
	// Warte 0.05 Sekunden
	usleep(50000);
} while($isValidLine && (!$limitPerExecution || $noOfHandledEvents < $limitPerExecution));

$finishedTime = microtime(true);
$duration = $finishedTime - $startTime;
echo "Handled [$noOfHandledEvents] in [$duration] seconds.";
