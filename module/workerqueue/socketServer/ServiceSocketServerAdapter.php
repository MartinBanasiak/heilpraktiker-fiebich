<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 24.11.2016
 * Time: 19:11
 */

namespace DynCom\dc\workerqueue\socketServer;


/**
 * Interface ServiceSocketServerAdapter
 * @package DynCom\dc\workerqueue\socketServer
 */
interface ServiceSocketServerAdapter
{

    /**
     * @return string
     */
    public function getRawMessageStartSequence(): string;

    /**
     * @return string
     */
    public function getRawMessageTerminationSequence(): string;

    /**
     * Has to parse the message, extract the method of the adapted service to call,
     * extract and check the parameters of the method to call, call it and return a potential return value as string
     * @param string $message
     * @return string
     */
    public function adaptMessageForService(string $message): string;


}