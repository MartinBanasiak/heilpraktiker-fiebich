<?php
declare(strict_types = 1);
namespace DynCom\dc\workerqueue;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 12.10.2016
 * Time: 12:47
 */

use Dotenv\Dotenv;
use DynCom\dc\workerqueue\main\GenericCronSpecScheduledJob;
use DynCom\dc\workerqueue\main\GenericJob;
use DynCom\dc\workerqueue\main\PDOCronSpecScheduledJobGateway;
use DynCom\dc\workerqueue\main\PDOJobQueueGateway;

date_default_timezone_set('Europe/Berlin');
echo "TEST STARTED... " . PHP_EOL . PHP_EOL;

$baseDir = rtrim(dirname(dirname(__DIR__)), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
$vendorDir = $baseDir . 'vendor/';
$vendorAutoloaderPath = $vendorDir . 'autoload.php';
echo "vendor autoload path: $vendorAutoloaderPath";
include($vendorAutoloaderPath);

$envDir = $baseDir . 'config/';
$envDirWorkerqueue = $baseDir . '/config/workerqueue';

if (is_dir($envDir)) {
    $dotenv = new Dotenv($envDir);
    $dotenv->load();
}
if (is_dir($envDirWorkerqueue)) {
    $dotenv = new Dotenv($envDirWorkerqueue);
    $dotenv->load();
}

$pdoHost = getenv('WORKERQUEUE_DB_HOST');
$pdoPort = getenv('WORKERQUEUE_DB_PORT');
$pdoUser = getenv('WORKERQUEUE_DB_USER');
$pdoPass = getenv('WORKERQUEUE_DB_PASS');
$pdoSchema = getenv('WORKERQUEUE_DB_SCHEMA');
$pdoDsn = 'mysql:dbname=' . $pdoSchema . ';host=' . $pdoHost . ';port=' . (int)$pdoPort . ';charset=utf8mb4';
echo "USER $pdoUser PASS $pdoPass SCHEMA $pdoSchema";
$PDOJobQueueGateway = new PDOJobQueueGateway($pdoDsn, $pdoUser, $pdoPass);
$PDOCronScheduledJobGateway = new PDOCronSpecScheduledJobGateway($pdoDsn, $pdoUser, $pdoPass);


$queueName = 'sendemail';

$payloadArr = [
    'sender_name' => 'Testmail-Workerqueue',
    'sender_mail' => 'workerqueue@dc.dev',
    'recipients' => [
        [
            'recipient_name' => 'Michael Bauer',
            'recipient_mail' => 'bauer@dc-solution.de',
        ],
        [
            'recipient_name' => 'Sebastian Lorenz',
            'recipient_mail' => 'lorenz@dc-solution.de',
        ]
    ],
    'mail_subject' => 'Testmail job scheduler worker-queue system',
    'ccs' => [
        [
            'cc_name' => 'Michael Bauer',
            'cc_mail' => 'mbauer.mphil@googlemail.com',
        ]
    ],
    'bccs' => [],
    'attachments' => [],
    'mail_body' => 'Hi! This is test-mail from the dc worker-queue-system. If you\'re reading this, it worked - yay!',
    'mail_body_content_type' => 'text/plain',
    'mail_parts' => [],
];


$payloadJson = json_encode($payloadArr);
ini_set('display_errors', '1');


$timeToLive = 0;
$creationTimestamp = 0;
$maxNoOfRetries = 3;
$noOfUnsuccessfulAttempts = 0;
$dt = new \DateTime();
$interval = \DateInterval::createFromDateString('2 minutes');
$dt->add($interval);
$scheduledSingleExecutionDateTime = \DateTimeImmutable::createFromMutable($dt);
/*
$cronSpec = '0 5 * * * *'; //Every 5th hour of the day every day every month every year
$job1Payload = [];
$repeatingJob = new GenericCronSpecScheduledJob('currencyconversion', (int)microtime(true), $timeToLive, $maxNoOfRetries, $noOfUnsuccessfulAttempts, $job1Payload, $cronSpec);
$PDOCronScheduledJobGateway->createScheduledJob($repeatingJob);

$cronSpec2 = '30 3 * * * *'; //Every Minute 30 of hour 3 of the day every day every month every year
//$job2Payload = ['users' => [1,2,22]]; //UserIDs
$job2Payload = [];
$repeatingJob2 = new GenericCronSpecScheduledJob('customertopitems', (int)microtime(true), $timeToLive, $maxNoOfRetries, $noOfUnsuccessfulAttempts, $job2Payload, $cronSpec2);
$PDOCronScheduledJobGateway->createScheduledJob($repeatingJob2);
*/
$bareJob1 = new GenericJob('customertopitems', (int)microtime(true), $timeToLive, $maxNoOfRetries, $noOfUnsuccessfulAttempts, []);
$PDOJobQueueGateway->createJob($bareJob1);


$bareJob2 = new GenericJob('customertopitems',(int)microtime(true),$timeToLive,$maxNoOfRetries,$noOfUnsuccessfulAttempts,[]);
$PDOJobQueueGateway->createJob($bareJob2);

$bareJob3 = new GenericJob('sendemail',(int)microtime(true),$timeToLive,$maxNoOfRetries,$noOfUnsuccessfulAttempts,$payloadArr);
$PDOJobQueueGateway->createJob($bareJob3);
//Try to retrieve
/*
$nextOpenJob = $gateway->getNextOpenJob($queueName);

echo 'NEXT OPEN JOB AFTER CREATION ATTEMPT: ' . PHP_EOL . PHP_EOL;
var_dump($nextOpenJob);
*/
echo "... TEST FINISHED!\n";
