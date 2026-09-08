<?php
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 25.11.2016
 * Time: 04:07
 */

namespace DynCom\dc\workerqueue\socketServer;


/**
 * Class TCPServer
 * @package DynCom\dc\workerqueue\socketServer
 */
class TCPServer
{
    protected $adapter;
    protected $port;
    protected $addr;
    protected $serverSocketResource;
    protected $connectedClients;
    protected $currClientResponse;
    protected $keepListening = true;

    /**
     * TCPServer constructor.
     * @param ServiceSocketServerAdapter $adapter
     */
    public function __construct(ServiceSocketServerAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param $port
     */
    public function listen($port)
    {
        $this->port = $port;
        $this->addr = 'tcp://127.0.0.1:' . $this->port;

        $this->serverSocketResource = stream_socket_server($this->addr, $errno = 0, $errstr = '');
        stream_set_blocking($this->serverSocketResource, 0);
        echo "waiting for inbound connections...";

        while ($this->keepListening) {
            $clientAddr = '';

            $connection = stream_socket_accept($this->serverSocketResource, null, $clientAddr);
            if ($connection === false) {
                usleep(100);
            } elseif ($connection > 0) {
                $this->handleClient($this->serverSocketResource, $connection);
            } else {
                echo "error: " . socket_strerror($connection);
                die;
            }
        }

    }

    /**
     * @param resource $serverSocket
     * @param resource $clientSocket
     */
    protected function handleClient(resource &$serverSocket, resource &$clientSocket)
    {
        $pid = pcntl_fork();
        if ($pid == -1) {
            /* fork failed */
            echo "fork failure!\n";
            die;
        } elseif ($pid == 0) {
            /* child process */
            $this->keepListening = false;
            socket_close($serverSocket);
            $this->interactWithClientSocket($clientSocket);
            socket_close($clientSocket);
        } else {
            /* parent process */
            socket_close($clientSocket);
        }
    }

    /**
     * @param resource $clientSocket
     */
    protected function interactWithClientSocket(resource &$clientSocket)
    {
        $msgStart = $this->adapter->getRawMessageStartSequence();
        $msgTerm = $this->adapter->getRawMessageTerminationSequence();
        while ($msg = $this->getNextClientResponseFromSocket($clientSocket, $msgStart, $msgTerm)) {
            $response = $this->adapter->adaptMessageForService($msg);
            stream_socket_sendto($clientSocket, $response);
        }
    }

    /**
     * @param resource $clientSocket
     * @param $msgStart
     * @param $msgTerm
     * @return \Generator
     */
    protected function getNextClientResponseFromSocket(resource &$clientSocket, $msgStart, $msgTerm): \Generator
    {
        $response = '';
        $strlenMsgStart = strlen($msgStart);
        while ($chunk = stream_socket_recvfrom($clientSocket, 128)) {
            $strposMsgStart = strpos($chunk, $msgStart);
            $strposMsgTerm = strpos($chunk, $msgTerm);
            if ($strposMsgStart === false && $strposMsgTerm === false) {
                continue;
            } elseif ($strposMsgStart === 0) {
                $length = null;
                if ($strposMsgTerm) {
                    $length = $strposMsgTerm - $strlenMsgStart;
                    $response .= substr($chunk, $strlenMsgStart, $length);
                    yield $response;
                    $response = '';
                } else {
                    $response .= substr($chunk, $strlenMsgStart);
                }
            } elseif ($strposMsgTerm >= 0) {
                $response .= substr($chunk, 0, $strposMsgTerm);
                yield $response;
                $response = '';
            }
        }
    }

}