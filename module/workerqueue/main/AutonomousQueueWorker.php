<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 19.10.2016
 * Time: 11:06
 */

namespace DynCom\dc\workerqueue\main;

/**
 * Interface AutonomousQueueWorker
 * File containing AutonomousQueueWorker must be autonomously runnable. To this end,
 * any implementation must be as a class surrounded by a script for autoloading,
 * instantiating the class and executing the manageJobs-Method
 *
 * @package DynCom\dc\workerqueue
 */
interface AutonomousQueueWorker
{
    /**
     * Fetches and handles jobs via delegation to JobHandler
     */
    public function manageJobs(): void;
}