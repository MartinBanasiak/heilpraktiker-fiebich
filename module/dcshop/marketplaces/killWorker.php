<?php

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 19.03.2016
 * Time: 15:28
 */
class MarketplaceWorkerKiller
{
    protected $workerPaths;

    public function __construct()
    {
        $dir = __DIR__;
        $this->workerPaths = [
            $dir . DIRECTORY_SEPARATOR . 'WorkerQueue.php',
            $dir . DIRECTORY_SEPARATOR . 'WorkerSubmissions.php',
            $dir . DIRECTORY_SEPARATOR . 'WorkerSubmissionsReady.php',
            $dir . DIRECTORY_SEPARATOR . 'WorkerSubmissionsResult.php',
            $dir . DIRECTORY_SEPARATOR . 'WorkerFileDelete.php',
        ];
    }

    public function supervise()
    {

        $iterations = 1;
        for ($i = 0;$i < $iterations;$i++) {
            foreach ($this->workerPaths as $path) {
                if (file_exists($path) && $this->isWorkerRunning($path)) {
                    echo "KILL";
                    $this->killWorker($this->getWorkerPid($path));
                }
            }
        }
    }

    protected function isWorkerRunning($workerPath)
    {
        $running = (bool)exec("ps aux|grep ". basename($workerPath) ."|grep -v grep|wc -l");
        return $running;
    }

    protected function getWorkerPid($workerPath)
    {
        //$running = exec("ps aux|grep ". basename($workerPath) ."|grep -v");
        $running = exec("ps aux | grep ". basename($workerPath) ." | grep -v grep | awk '{print $2}'");
        return $running;
    }

    protected function startWorker($workerPath)
    {
        $proc = 'php ' . $workerPath;
        $outputFile = '/dev/null';

        //shell_exec(sprintf('%s > %s 2>&1 & echo $!', $proc, $outputFile));
        exec('nohup ' . $proc . ' >' . $outputFile . '  2>&1 &');
    }

    protected function killWorker($pid)
    {
        exec('kill -9 ' . $pid);
    }

}


/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 19.03.2016
 * Time: 15:27
 */
$supervisor = new MarketplaceWorkerKiller();
$supervisor->supervise();