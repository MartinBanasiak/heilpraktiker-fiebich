<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 24.03.2016
 * Time: 11:58
 */

//Includes
$marketplaceDir = __DIR__;
$rootDir = dirname(dirname(dirname(__DIR__)));
require_once $rootDir . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $marketplaceDir . DIRECTORY_SEPARATOR . 'PHPScriptProcessSupervisor.php';

//load environment variables
$dotenv = new \Dotenv\Dotenv($rootDir . "/config/");
$dotenv->load();

//Define Error Mail Recipient
$errorMailRecipientAddr = 'lorenz@dc-solution.de';
$errorMailRecipientName = 'Michael Bauer';

//Mailer and Message
$smtpTransporter = Swift_SmtpTransport::newInstance();
$mailer = Swift_Mailer::newInstance($smtpTransporter);
$msg = Swift_Message::newInstance('Error in queue-worker-management');
$msg->addFrom('error@mysydeshop.de');
$msg->addTo($errorMailRecipientAddr,$errorMailRecipientName);

//Logger
$logFilePath = $marketplaceDir . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'MarketplaceQueueWorkers.log';
$rotatingFileHandler = new \Monolog\Handler\RotatingFileHandler($logFilePath,10);
$formatter = new \Monolog\Formatter\LineFormatter(null,null,false,true);
$rotatingFileHandler->setFormatter($formatter);
$mailFormatter = new \Monolog\Formatter\HtmlFormatter();
$mailHandler = new \Monolog\Handler\SwiftMailerHandler($mailer,$msg);
$mailHandler->setFormatter($formatter);
$logger = new \Monolog\Logger('MarketplaceQueueWorkersLogger',[$rotatingFileHandler,$mailHandler]);

//Check password
$passwordKey = 'SHOP_PASSWORD';
$storedPass = getenv($passwordKey);
$sentPass = $_POST[$passwordKey];
if ($storedPass !== $sentPass) {
    $logger->error('killProcesses was called with empty or invalid shop-password from IP ' . $_SERVER['REMOTE_ADDR']);
    throw new InvalidArgumentException('Shop password is not given or does not match');
}

//define files
$files = [
    __DIR__ . DIRECTORY_SEPARATOR . 'WorkerQueue.php',
    __DIR__ . DIRECTORY_SEPARATOR . 'WorkerSubmissions.php',
    __DIR__ . DIRECTORY_SEPARATOR . 'WorkerSubmissionsReady.php',
    __DIR__ . DIRECTORY_SEPARATOR . 'WorkerSubmissionsResult.php',
    __DIR__ . DIRECTORY_SEPARATOR . 'WorkerFileDelete.php',
];

//Instantiate PDO
$dsn = 'mysql:host=' . getenv('MAIN_MYSQL_DB_HOST') . ';dbname=' . getenv('MAIN_MYSQL_DB_SCHEMA');
$username = getenv('MAIN_MYSQL_DB_USER');
$pass = getenv('MAIN_MYSQL_DB_PASS');
$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
);
$pdo = new PDO($dsn,$username,$pass,$options);

//Terminate Workers
$processSupervisor = new PHPScriptProcessSupervisor($pdo, $files,$logger);
$processSupervisor->terminateWorkers();

