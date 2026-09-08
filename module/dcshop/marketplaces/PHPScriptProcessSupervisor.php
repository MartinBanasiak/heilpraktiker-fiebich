<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 24.03.2016
 * Time: 11:48
 */
class PHPScriptProcessSupervisor
{
    /**
     * @var PDO
     */
    protected $db;
    /**
     * @var array
     */
    protected $filePaths;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(PDO $pdo, array $filePaths, \Psr\Log\LoggerInterface $logger = null)
    {
        $this->initializeLogger($logger);
        $pathsString = '|' . implode('|',$filePaths) . '|';
        $this->logger->notice('PHPScriptProcessSupervisor called with filePaths: ' . $pathsString);
        $this->db = $pdo;
        $invalidPaths = [];
        if (!$this->validateFilePaths($filePaths,$invalidPaths)) {
            $this->logger->error('The following paths have no file or the file is no php script: ',['paths' => $invalidPaths]);
            throw new InvalidArgumentException('Paramter must be an array of valid filepaths to php scripts.');
        }
        $this->filePaths = $filePaths;
    }

    protected function validateFilePaths(array $filePaths, array &$invalidPaths)
    {
        $success = true;
        foreach ($filePaths as $filePath) {
            if (
                    !file_exists($filePath)
                ||  !is_file($filePath)
                ||  !is_readable($filePath)
                ||  (pathinfo($filePath)['extension'] !== 'php')
            ) {
                $success = false;
                $invalidPaths[] = $filePath;
            }
        }
        return $success;
    }

    public function startWorkers()
    {
        $this->logger->notice('Starting workers...');
        $iterations = 3;
        for ($i = 0;$i < $iterations;$i++) {
            foreach ($this->filePaths as $path) {
                if (file_exists($path)) {
                    if ($this->isWorkerRunning($path)) {
                        $this->logger->notice('Worker at path "' . $path . '" already running');
                    } else {
                        $pid = $this->startWorker($path);
                        $this->logger->notice('Started worker at path "' . $path . '"');
                    }
                } else {
                    $this->logger->warning('No file exists at path: ' . $path);
                }
            }
            sleep(1);
        }
        $this->logger->notice('Finished attempting to start workers if not running.');
    }

    public function terminateWorkers()
    {
        $this->logger->notice('Terminating workers...');
        $workers = $this->filePaths;
        foreach ($workers as $worker) {
            $pids = $this->getWorkerPidsLinux($worker);
            $pidstring = '|' . implode('|',$pids) . '|';
            $this->logger->notice('Checking path "' . $worker . '" - PIDs are: ' . $pidstring . '...');
            foreach ($pids as $pid) {
                posix_kill($pid,SIGTERM);
                $this->logger->notice('sending SIGTERM to PID ' . $pid);
            }
        }
        $this->logger->notice('finished attempting termination.');

    }

    protected function getWorkerPIDs($workerFilePath)
    {
        $query = "
            SELECT
                pid
            FROM
              queue_worker_processes
            WHERE
              filepath = '$workerFilePath'
        ";
        $pids = [];
        $stmt = $this->db->query($query);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            $pids[] = $row['pid'];
        }
        return $pids;
    }

    protected function isWorkerRunning($workerPath)
    {
        $running = (bool)exec('ps aux|grep \'' . get_current_user() . ' .*' . basename($workerPath) . '\'|grep -v grep|wc -l');
        return $running;
    }

    protected function startWorker($workerPath)
    {
        $proc = 'php71 ' . $workerPath;
        $outputFile = __DIR__ . DIRECTORY_SEPARATOR . 'supervisor.log';
        //$outputFile = '/dev/null';

        //shell_exec(sprintf('%s > %s 2>&1 & echo $!', $proc, $outputFile));
        //exec('nohup ' . $proc . ' >' . $outputFile . '  2>&1 &');
        $output = exec($proc . ' >' . $outputFile . '  2>&1 &');
        return $output;
    }

    protected function getWorkerPidsLinux($workerPath)
    {
        //$running = exec("ps aux|grep ". basename($workerPath) ."|grep -v");
        $out = [];
        exec("ps aux | grep ". basename($workerPath) ." | grep -v grep | awk '{print $2}'",$out);
        return $out;
    }

    private function initializeLogger(\Psr\Log\LoggerInterface $logger = null)
    {
        if (null === $logger) {
            $logger = new \Psr\Log\NullLogger();
        }
        $this->logger = $logger;
    }


}