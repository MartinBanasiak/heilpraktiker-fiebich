<?php
declare(strict_types = 1);
namespace DynCom\dc\workerqueue\processManagement;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 14.11.2016
 * Time: 13:44
 */

use Psr\Log\LoggerInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ProcessSupervisor
 * @package DynCom\dc\workerqueue\processManagement
 */
class ProcessSupervisor
{
    use managesProcesses;

    public const CONFIG_FILENAME = 'workers.yml';
    protected const WORKERQUEUE_CONFIG_DIR = 'config' . DIRECTORY_SEPARATOR . 'workerqueue';
    protected const ENV_KEY_WORKERQUEUE_CONFIG_DIR = 'WORKERQUEUE_CONFIG_DIR';

    /**
     * @var LoggerInterface
     */
    protected $logger;
    protected $basePath;
    protected $basePathWorkerqueue;
    protected $configDir;
    protected $configuration = [];

    protected $startedProcesses;

    /**
     * ProcessSupervisor constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->basePath = dirname(dirname(dirname(__DIR__)));
        $this->basePathWorkerqueue = dirname(__DIR__);
    }

    public function superviseProcesses(): void
    {
        $this->loadWorkerConfig();
        $basePathRaw = $this->basePathWorkerqueue;
        $basePath = rtrim(realpath($basePathRaw), DIRECTORY_SEPARATOR);
        $this->logger->info('Basepath is [' . $basePath . '].');
        if (!empty($basePath)) {
            foreach ($this->configuration['workers'] as $workerName => $workerInfo) {
                $this->superviseProcess($basePath, $workerName, $workerInfo);
            }
        }
        return;
    }

    protected function loadWorkerConfig(): void
    {

        $this->configDir = self::WORKERQUEUE_CONFIG_DIR;
        $path = rtrim(realpath($this->basePath), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . rtrim((string)$this->configDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'workers.yml';
        if (file_exists($path) && is_readable($path) && is_file($path)) {
            try {
                $config = Yaml::parse(file_get_contents($path));
                $this->configuration = $config;
            } catch (ParseException $e) {
                $logMsg = 'Could not parse config loaded from path [' . $path . '].';
                $this->logger->alert($logMsg);
                throw $e;
            }
        } else {
            $logMsg = 'No readable config file at path [' . $path . '].';
            $this->logger->alert($logMsg);
            throw new \InvalidArgumentException($logMsg);
        }
        return;
    }

    /**
     * @param $basePath
     * @param $workerName
     * @param array $processSpec
     */
    protected function superviseProcess(string $basePath, string $workerName, array $processSpec): void
    {
        $pathSegment = $processSpec['path'];
        $path = $basePath . DIRECTORY_SEPARATOR . $pathSegment;
        $this->logger->info('Checking path [' . $path . '] for registered worker of name [' . $workerName . '].');
        $instances = (int)$processSpec['instances'];
        if ($instances < 1) {
            $instances = 1;
        }
        $turnoverMinutes = (int)$processSpec['instance_restart_minutes'];

        if (file_exists($path) && is_readable($path) && is_file($path)) {
            $pids = $this->getPIDsForPath($path);
            $logMsg = 'Worker at path [' . $path . '] is currently running under the following PIDs [' . implode(',', $pids) . '].';
            $this->logger->info($logMsg);
            $noOfRunningInstances = count($pids);
            //Start new instances if less than the configured number of instances are running
            for ($i = $noOfRunningInstances; $i < $instances; $i++) {
                $this->startWorker($path);
                sleep(1);
            }
            //Turnover other instances if needed
            foreach ($pids as $pid) {
                $runtimeSeconds = $this->getProcessRuntimeSecondsByPID((int)$pid);
                $runtimeMinutes = $runtimeSeconds / 60;
                $logMsg = 'Checking turnover for process with path [' . $path . '] and PIDS [' . implode(',', $pids) . ']. Runtime in minutes is [' . $runtimeMinutes . '] - turnover limit in minutes is [' . $turnoverMinutes . ']';
                $this->logger->info($logMsg);
                if ($runtimeMinutes >= $turnoverMinutes) {
                    $logMsg = 'Turnover reached for PID [' . $pid . '] for worker at path [' . $path . ']. Terminating and starting a new instance.';
                    $this->logger->info($logMsg);
                    $newPid = $this->turnoverWorker((int)$pid, $path);
                    $logMsg = 'New PID after turnover is [' . $newPid . '].';
                    $this->logger->info($logMsg);
                    sleep(1);
                }
            }
            $pids = $this->getPIDsForPath($path);
            $noOfRunningInstances = count($pids);
            if ($noOfRunningInstances === $instances) {
                $logMsg = 'All [' . $instances . '] configured instances for worker at path [' . $path . '] are running.';
                $this->logger->info($logMsg);
            } else {
                $logMsg = 'Only [' . $noOfRunningInstances . '] instances out of [' . $instances . '] configured for worker at path [' . $path . '] are currently running. Please check manually.';
                $this->logger->alert($logMsg);
            }
        } else {
            $logMsg = 'No readable file at path [' . $path . ']!';
            $this->logger->alert($logMsg);
        }
        return;
    }

    /**
     * @param string $workerPath
     * @return int|null
     */
    protected function startWorker(string $workerPath): ?int
    {
        $php = $this->checkCommandExists('php71') ? 'php71' : 'php';
        $proc = $php . ' -q -f' . escapeshellarg($workerPath);
        $pid = $this->startProcessInBackground($proc);
        if (!((int)$pid > 0)) {
            $this->logger->alert('Instance for worker at path [' . $workerPath . '] could not be started. Output was [' . $pid . ']. Attempting again in one second.');
            sleep(1);
            $pid = $this->startProcessInBackground($proc);
            if (!((int)$pid > 0)) {
                $this->logger->error('Second attempt at starting worker at path [' . $workerPath . '] failed.');
                return null;
            }
        } else {
            $this->logger->debug('Instance for worker at path [' . $workerPath . '] started with PID [' . $pid . '].');
        }
        //Return PID
        return (int)$pid;
    }

    /**
     * @param int $pid
     * @param string $path
     * @return int
     */
    protected function turnoverWorker(int $pid, string $path): int
    {
        $this->terminateWorker($pid, $path);
        sleep(1);
        $newPid = (int)$this->startWorker($path);
        return $newPid;
    }

    /**
     * @param $pid
     * @param $path
     */
    protected function terminateWorker(int $pid, string $path): void
    {
        $success = $this->terminateProcessByPID($pid, SIGTERM);
        if (!$success) {
            $logMsg = 'Terminating Worker process with PID [' . $pid . '] and script-path [' . $path . '] failed. Sending SIGKILL once after one second.';
            $this->logger->alert($logMsg);

            sleep(1);
            $retrySuccess = $this->terminateProcessByPID($pid, SIGKILL);
            if (!$retrySuccess) {
                $logMsg = 'Second attempt at terminating Worker process with PID [' . $pid . '] and script-path [' . $path . '] failed! Please check manually.';
                $this->logger->critical($logMsg);
            }
        }
        return;
    }

    public function stopSupervisedProcesses(): void
    {
        $this->loadWorkerConfig();
        $basePath = rtrim(realpath(rtrim($this->basePathWorkerqueue, DIRECTORY_SEPARATOR) . rtrim($this->configDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $this->configuration['basepath']), DIRECTORY_SEPARATOR);
        if (!empty($basePath)) {
            foreach ($this->configuration['workers'] as $workerName => $workerInfo) {
                $pathSegment = $workerInfo['path'];
                $path = $basePath . DIRECTORY_SEPARATOR . $pathSegment;
                if (file_exists($path) && is_readable($path) && is_file($path)) {
                    $pids = $this->getPIDsForPath($path);
                    foreach ($pids as $pid) {
                        $this->terminateWorker($pid, $path);
                    }
                }
            }
        }
        return;
    }

}