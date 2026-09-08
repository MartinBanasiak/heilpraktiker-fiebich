<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 24.11.2016
 * Time: 16:57
 */

namespace DynCom\dc\workerqueue\socketServer;


/**
 * Interface SocketServer
 * @package DynCom\dc\workerqueue\socketServer
 */
interface SocketServer
{
    /**
     * @param string $addr
     */
    public function listen(string $addr): void;
}