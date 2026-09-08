<?php
declare(ticks = 1);
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 24.11.2016
 * Time: 19:58
 */

namespace DynCom\dc\workerqueue\socketServer;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;


/**
 * Class TCPStreamSocketServer
 * @package DynCom\dc\workerqueue\socketServer
 */
class TCPStreamSocketServer implements SocketServer
{
    const SOCKET_SELECT_TIMEOUT_SEC = 5;
    const STREAM_SOCKET_ACCEPT_TIMEOUT_SEC = 60;

    protected $adapter;
    protected $port;
    protected $addr;
    protected $serverSocketResource;
    protected $connectedClients;
    protected $currClientResponse;
    protected $keepListening = true;
    protected $connectionCache = [];
    protected $incomingConnections = [];
    protected $outgoingConnections = [];
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * TCPStreamSocketServer constructor.
     * @param ServiceSocketServerAdapter $adapter
     * @param LoggerInterface|null $logger
     */
    public function __construct(ServiceSocketServerAdapter $adapter, LoggerInterface $logger = null)
    {
        $this->adapter = $adapter;
        $this->logger = $logger !== null ? $logger : new NullLogger();
        $this->setupSignalHandling();
    }

    protected function setupSignalHandling(): void
    {
        pcntl_signal(SIGINT, 'self::signalHandler');
        pcntl_signal(SIGHUP, 'self::signalHandler');
    }

    /**
     * @param $addr
     */
    public function listen(string $addr): void
    {
        pcntl_signal_dispatch();
        $this->port = substr($addr, strpos($addr, ':') + 1);
        $this->addr = $addr;
        $errno = 0;
        $errstr = '';
        $this->serverSocketResource = stream_socket_server($this->addr, $errno, $errstr);
        if (!is_resource($this->serverSocketResource)) {
            throw new \RuntimeException('Could not open server at addr [' . $this->addr . ']. ' . $errstr . " ($errno)");
        }

        $this->connectionCache = [];
        $this->connectionCache[] = $this->serverSocketResource;

        stream_set_blocking($this->serverSocketResource, 0);


        while ($this->keepListening) {
            $listenToSockets = $this->incomingConnections;
            $listenToSockets[] = $this->serverSocketResource;

            $exceptions = [];

            $noOfResourcesChanged = @stream_select($listenToSockets, $null, $exceptions, self::SOCKET_SELECT_TIMEOUT_SEC);

            if (false == $noOfResourcesChanged) {
                continue;
            }
            $this->logger->debug('Stream_select has indicated [' . $noOfResourcesChanged . '] streams have changed.');

            foreach ($listenToSockets as $readSocket) {

                if ($readSocket === $this->serverSocketResource && is_resource($readSocket)) {
                    $peerName = '';
                    if (($readSocket = @stream_socket_accept($readSocket, self::STREAM_SOCKET_ACCEPT_TIMEOUT_SEC, $peerName)) === false) {
                        echo '[Error] ' . __LINE__ . ' Stream socket accept failed for peer [' . $peerName . '].';
                        usleep(50000);
                        continue;
                    }
                    if ($readSocket > 0) {
                        $this->logger->debug('Server handle changed - calling handle client for socket [' . $readSocket . '].');
                        $this->handleClient($this->serverSocketResource, $readSocket, $peerName);
                        /*$this->incomingConnections[] = $readSocket;*/
                    }
                } else {
                    $peerName = '';

                    if (($readSocket = @stream_socket_accept($readSocket, self::STREAM_SOCKET_ACCEPT_TIMEOUT_SEC, $peerName)) === false) {
                        echo '[Error] ' . __LINE__ . '  Stream socket accept failed for peer [' . $peerName . '].';
                        usleep(50000);
                        continue;
                    }
                    if ($readSocket > 0) {
                        $this->logger->debug('Client handle changed - calling handle client for socket [' . $readSocket . '].');
                        /*$this->handleClient($this->serverSocketResource, $readSocket);*/
                        /*if (is_resource($this->serverSocketResource)) {
                            $this->connectionCache[] = $this->serverSocketResource;
                        }*/
                    }
                }
                /*
                                if ($readSocket === $this->serverSocketResource && is_resource($readSocket)) {
                                    $peer = '';
                                    $clientFromServer = stream_socket_accept($readSocket, 5, $peer);
                                    if (!$clientFromServer) {
                                        usleep(50000);
                                    } elseif ($clientFromServer > 0) {
                                        $this->handleClient($this->serverSocketResource, $clientFromServer);
                                        if (is_resource($this->serverSocketResource)) {
                                            $this->connectionCache[] = $this->serverSocketResource;
                                        }
                                    }
                                } else {
                                    $msg = fread($readSocket, 1024);//$this->getNextClientResponseFromSocket($read[$i],$this->adapter->getRawMessageStartSequence(),$this->adapter->getRawMessageTerminationSequence());
                                    if ($msg === '') {
                                        //connection closed
                                        $delKey = array_search($readSocket, $this->connectionCache, true);
                                        if (isset($readSocket)) {
                                            stream_socket_shutdown($readSocket, STREAM_SHUT_RDWR);
                                        }
                                        unset($this->connectionCache[$delKey]);
                                    } elseif ($msg === false) {
                                        $delKey = array_search($readSocket, $this->connectionCache, true);
                                        unset($this->connectionCache[$delKey]);
                                    } else {
                                        stream_socket_sendto($readSocket, "You have sent :[" . $msg . "]\n");
                                        stream_socket_shutdown($readSocket, STREAM_SHUT_RDWR);
                                        unset($this->connectionCache[array_search($readSocket, $this->connectionCache)]);
                                    }
                                }
                */
            }
        }

        die();

    }

    /**
     * @param $serverSocket
     * @param $clientSocket
     * @param string $peername
     */
    protected function handleClient(&$serverSocket, &$clientSocket, string $peername): void
    {
        $pid = pcntl_fork();
        if ($pid == -1) {
            /* fork failed */
            echo "fork failure!\n";
            die;
        } elseif ($pid == 0) {
            /* child process */
            $this->keepListening = false;
            $this->interactWithClientSocket($clientSocket, $peername);
            stream_socket_shutdown($clientSocket, STREAM_SHUT_RDWR);
            die();
        } else {
            /* parent process */
            $status = '';
            pcntl_wait($status);
            //TEST
            stream_socket_shutdown($clientSocket, STREAM_SHUT_RDWR);
        }
    }

    /**
     * @param $clientSocket
     * @param string $peername
     */
    protected function interactWithClientSocket(&$clientSocket, string $peername): void
    {
        $this->logger->debug('In method [' . __METHOD__ . '] with parameter [' . $clientSocket . '].');
        if (feof($clientSocket) || feof($this->serverSocketResource)) {
            $this->logger->debug('FEOF of either server or client stream reached. Terminating...');
            stream_socket_shutdown($clientSocket, STREAM_SHUT_RDWR);
            stream_socket_shutdown($this->serverSocketResource, STREAM_SHUT_RDWR);
        }

        while ($msg = $this->getNextClientResponseFromSocket($clientSocket, $this->adapter->getRawMessageStartSequence(), $this->adapter->getRawMessageTerminationSequence())) {
            $this->logger->debug('Received message [' . $msg . '] from client. Adapting for service...');
            $response = $this->adapter->adaptMessageForService($msg);
            $this->logger->debug('Return from adapter was [' . $response . '].');
            stream_socket_sendto($clientSocket, $response);
        }

        if (is_resource($clientSocket)) {
            $this->connectionCache[] = $clientSocket;
        }
        if (is_resource($this->serverSocketResource)) {
            $this->connectionCache[] = $this->serverSocketResource;
        }

    }

    /**
     * @param $clientSocket
     * @param $msgStart
     * @param $msgTerm
     * @return string
     */
    protected function getNextClientResponseFromSocket($clientSocket, string $msgStart, string $msgTerm): string
    {

        /**
         * @var $buf array
         */
        static $buf;
        if (null === $buf) {
            $buf = [];
        }
        if (!is_resource($clientSocket)) {
            return '';
        }
        //var_dump($clientSocket);
        // Cast to int first to get resource id
        // Treat as hashmap instead of numerical array so as to
        // preserve resourceID<>buffer associations during array operations
        $idStr = md5((string)(int)$clientSocket);
        if (!array_key_exists($idStr, $buf)) {
            $buf[$idStr] = '';
        }
        $clientBuffer = &$buf[$idStr];


        $strlenMsgStart = strlen($msgStart);
        try {
            while ($chunk = stream_socket_recvfrom($clientSocket, 1024)) {
                $clientBuffer .= $chunk;
                $bufStrposMsgStart = strpos($clientBuffer, $msgStart);
                $bufStrposMsgTerm = strpos($clientBuffer, $msgTerm);
                if (0 === $bufStrposMsgStart && $bufStrposMsgTerm > 0) {
                    $length = $bufStrposMsgTerm + $strlenMsgStart;
                    $msg = substr($clientBuffer, 0, $length);
                    $substr = substr($clientBuffer, $length);
                    $clientBuffer = isset($substr) ? $substr : '';
                    $msg = str_replace([$msgStart, $msgTerm], '', $msg);
                    return $msg;
                }
            }
        } catch (\Throwable $t) {
            return '';
        }
        return '';
    }

    /**
     * @param int $sig
     */
    protected function signalHandler(int $sig): void
    {
        switch ($sig) {
            case SIGINT:
                $this->logger->debug('SIGINT received - shutting down.');
                $this->shutdownServer();
                break;
            case SIGHUP:
                $this->logger->debug('SIGHUP received - shutting down.');
                $this->shutdownServer();
                break;
            default:
                break;
        }
    }

    protected function shutdownServer(): void
    {
        foreach ($this->incomingConnections as $conn) {
            if (is_resource($conn)) {
                stream_socket_shutdown($conn, STREAM_SHUT_RDWR);
            }
        }
        foreach ($this->outgoingConnections as $conn) {
            if (is_resource($conn)) {
                stream_socket_shutdown($conn, STREAM_SHUT_RDWR);
            }
        }
        if (is_resource($this->serverSocketResource)) {
            stream_socket_shutdown($this->serverSocketResource, STREAM_SHUT_RDWR);
        }
        exit();
    }

}