<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 24.11.2016
 * Time: 02:29
 */

namespace DynCom\dc\workerqueue\processManagement;


use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Class logsMemoryUsage
 * @package DynCom\dc\workerqueue\processManagement
 */
trait logsMemoryUsage
{

    /**
     * @param LoggerInterface $logger
     * @param int $level
     */
    protected function logMemoryUsage(LoggerInterface $logger, int $level = Logger::INFO): void
    {
        $peakMemAllocated = memory_get_peak_usage(true) / 1024;
        $peakMemAllocUnit = 'KiB';
        if ($peakMemAllocated > 1024) {
            $peakMemAllocated /= 1024;
            $peakMemAllocUnit = 'MiB';
        }
        $peakMemActual = memory_get_peak_usage(false) / 1024;
        $peakMemActualUnit = 'KiB';
        if ($peakMemActual > 1024) {
            $peakMemActual /= 1024;
            $peakMemActualUnit = 'MiB';
        }
        $logMsg = 'Peak memory usage was [' . $peakMemAllocated . '] ' . $peakMemAllocUnit . ' (allocated) and [' . $peakMemActual . '] ' . $peakMemActualUnit . ' (actual)';
        $logger->log($level, $logMsg);
        return;
    }
}