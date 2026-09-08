<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 24.11.2016
 * Time: 19:55
 */

namespace DynCom\dc\workerqueue\socketServer;


/**
 * Class SocketServerFactory
 * @package DynCom\dc\workerqueue\socketServer
 */
class SocketServerFactory
{

    /**
     * @param ServiceSocketServerAdapter $adapter
     * @param $protocol
     * @param $hostAddressOrPathname
     * @param null $port
     */
    public function createSocketServer(ServiceSocketServerAdapter $adapter, string $protocol, string $hostAddressOrPathname, $port = null): void
    {

    }

}