<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\ServicePayload;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 17.08.2015
 * Time: 09:42
 */
class GenericServicePayload implements ServicePayload
{

    /**
     * @var ServiceRequestID
     */
    protected $requestID;

    /**
     * @var string
     */
    protected $serviceName;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var string
     */
    protected $statusMessage;

    /**
     * @var array
     */
    protected $payloadData;

    /**
     * @var array
     */
    protected $devErrors;

    /**
     * @var array
     */
    protected $devWarnings;

    /**
     * @var array
     */
    protected $devNotifications;

    /**
     * @var array
     */
    protected $userSuccessMsgs;

    /**
     * @var array
     */
    protected $userErrors;

    /**
     * @var array
     */
    protected $userWarnings;

    /**
     * @var array
     */
    protected $userNotifications;


    /**
     * GenericServicePayload constructor.
     * @param ServiceRequestID $requestID
     * @param $serviceName
     * @param $statusCode
     * @param $statusMessage
     * @param array $payloadData
     * @param array $devErrors
     * @param array $devWarnings
     * @param array $devNotifications
     * @param array $userSuccessMsgs
     * @param array $userErrors
     * @param array $userWarnings
     * @param array $userNotifications
     */
    public function __construct(
        ServiceRequestID $requestID,
        $serviceName,
        $statusCode,
        $statusMessage,
        array $payloadData,
        array $devErrors,
        array $devWarnings,
        array $devNotifications,
        array $userSuccessMsgs,
        array $userErrors,
        array $userWarnings,
        array $userNotifications)
    {

        $this->requestID = clone $requestID;

        $this->serviceName = (string)$serviceName;

        $this->statusCode = (int)$statusCode;

        $this->statusMessage = (string)$statusMessage;

        $this->payloadData = $payloadData;

        $this->devErrors = $devErrors;

        $this->devWarnings = $devWarnings;

        $this->devNotifications = $devNotifications;

        $this->userSuccessMsgs = $userSuccessMsgs;

        $this->userErrors = $userErrors;

        $this->userWarnings = $userWarnings;

        $this->userNotifications = $userNotifications;

    }

    /**
     * @return ServiceRequestID
     */
    public function getRequestID()
    {
        return $this->requestID;
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }

    /**
     * @return array
     */
    public function getPayloadData()
    {
        return $this->payloadData;
    }

    /**
     * @return array
     */
    public function getDevErrors()
    {
        return $this->devErrors;
    }

    /**
     * @return array
     */
    public function getDevWarnings()
    {
        return $this->devWarnings;
    }

    /**
     * @return array
     */
    public function getDevNotifications()
    {
        return $this->devNotifications;
    }

    /**
     * @return array
     */
    public function getUserSuccessMsgs()
    {
        return $this->userSuccessMsgs;
    }

    /**
     * @return array
     */
    public function getUserErrors()
    {
        return $this->userErrors;
    }

    /**
     * @return array
     */
    public function getUserWarnings()
    {
        return $this->userWarnings;
    }

    /**
     * @return array
     */
    public function getUserNotifications()
    {
        return $this->userNotifications;
    }

}