<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);
ini_set('display_errors', 1);
$rootDir = dirname(dirname(dirname(__DIR__)));
ignore_user_abort(true);
set_time_limit(0);

include_once "WorkerAbstract.php";
include_once "WorkerInterface.php";

class WorkerFileDelete extends WorkerAbstract implements WorkerInterface
{
    /**
     * Sleep-Duration between executions in continuous mode
     * Provided in seconds
     * @var int
     */
    protected $sleepDuration = 3600;

    private $folderName = '';

    protected function getSleepDuration()
    {
        return $this->sleepDuration;
    }

    protected function getFolderName()
    {
        return $this->folderName;
    }

    public function setFolderName($folderName)
    {
        $this->folderName = $folderName;
    }


    public function processJob()
    {
        if (file_exists($this->getFolderName())) {
            foreach (new DirectoryIterator($this->getFolderName()) as $fileInfo) {
                if ($fileInfo->isDot()) {
                    continue;
                }
                if ($fileInfo->isFile() && time() - $fileInfo->getCTime() >= 7*24*60*60) {
                    unlink($fileInfo->getRealPath());
                }
            }
        }
    }

    public function releaseJob()
    {

    }

    protected function deleteJob()
    {

    }

}

// Hauptprogramm starten
$worker = new WorkerFileDelete();
$worker->setContinuous(true);
$worker->setFolderName(__DIR__ . DIRECTORY_SEPARATOR . 'xml');
$worker->run();