<?php
declare(strict_types = 1);
namespace DynCom\dc\workerqueue\main;
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 18.07.2016
 * Time: 13:30
 */
interface QueueWorker
{

    /**
     * In a loop: fetches Job, attempts processing and updates job
     * @return void
     */
    public function doWork(): void;

}