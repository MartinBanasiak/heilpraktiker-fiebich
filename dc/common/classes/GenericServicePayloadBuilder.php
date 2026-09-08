<?php
namespace DynCom\dc\common\classes;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 17.08.2015
 * Time: 15:49
 */
class GenericServicePayloadBuilder
{
    /**
     * @var ServiceRequestID
     */
    protected $requestID;

    /**
     * @var string
     */
    protected $serviceName = '';

    /**
     * @var int
     */
    protected $statusCode = 400;

    /**
     * @var string
     */
    protected $statusMessage = '';

    /**
     * @var array
     */
    protected $payloadData = [];

    /**
     * @var array
     */
    protected $devErrors = [];

    /**
     * @var array
     */
    protected $devWarnings = [];


    /**
     * @var array
     */
    protected $devNotifications = [];

    /**
     * @var array
     */
    protected $userSuccessMsgs = [];

    /**
     * @var array
     */
    protected $userErrors = [];

    /**
     * @var array
     */
    protected $userWarnings = [];

    /**
     * @var array
     */
    protected $userNotifications = [];

    /**
     * @return ServiceRequestID
     */
    public function getRequestID()
    {
        return $this->requestID;
    }

    /**
     * @param ServiceRequestID $requestID
     */
    public function setRequestID($requestID)
    {
        $this->requestID = $requestID;
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * @param string $serviceName
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }

    /**
     * @param string $statusMessage
     */
    public function setStatusMessage($statusMessage)
    {
        $this->statusMessage = $statusMessage;
    }

    /**
     * @return array
     */
    public function getPayloadData()
    {
        return $this->payloadData;
    }

    /**
     * @param array $payloadData
     */
    public function setPayloadData($payloadData)
    {
        $this->payloadData = $payloadData;
    }

    /**
     * @return array
     */
    public function getDevErrors()
    {
        return $this->devErrors;
    }

    /**
     * @param array $devErrors
     */
    public function setDevErrors($devErrors)
    {
        $this->devErrors = $devErrors;
    }

    /**
     * @return array
     */
    public function getDevWarnings()
    {
        return $this->devWarnings;
    }

    /**
     * @param array $devWarnings
     */
    public function setDevWarnings($devWarnings)
    {
        $this->devWarnings = $devWarnings;
    }

    /**
     * @return array
     */
    public function getDevNotifications()
    {
        return $this->devNotifications;
    }

    /**
     * @param array $devNotifications
     */
    public function setDevNotifications($devNotifications)
    {
        $this->devNotifications = $devNotifications;
    }

    /**
     * @return array
     */
    public function getUserSuccessMsgs()
    {
        return $this->userSuccessMsgs;
    }

    /**
     * @param array $userSuccessMsgs
     */
    public function setUserSuccessMsgs($userSuccessMsgs)
    {
        $this->userSuccessMsgs = $userSuccessMsgs;
    }

    /**
     * @return array
     */
    public function getUserErrors()
    {
        return $this->userErrors;
    }

    /**
     * @param array $userErrors
     */
    public function setUserErrors($userErrors)
    {
        $this->userErrors = $userErrors;
    }

    /**
     * @return array
     */
    public function getUserWarnings()
    {
        return $this->userWarnings;
    }

    /**
     * @param array $userWarnings
     */
    public function setUserWarnings($userWarnings)
    {
        $this->userWarnings = $userWarnings;
    }

    /**
     * @return array
     */
    public function getUserNotifications()
    {
        return $this->userNotifications;
    }

    /**
     * @param array $userNotifications
     */
    public function setUserNotifications($userNotifications)
    {
        $this->userNotifications = $userNotifications;
    }


    public function reset()
    {
        unset($this->requestID);
        $this->serviceName = '';
        $this->statusCode = 400;
        $this->statusMessage = '';
        $this->devErrors = [];
        $this->devWarnings = [];
        $this->devNotifications = [];
        $this->userSuccessMsgs = [];
        $this->userErrors = [];
        $this->userNotifications = [];
    }

    /**
     * @return GenericServicePayload
     */
    public function makeGenericServicePayload()
    {
        return new GenericServicePayload($this->requestID, $this->serviceName, $this->statusCode, $this->statusMessage, $this->payloadData, $this->devErrors, $this->devWarnings, $this->devNotifications, $this->userSuccessMsgs, $this->userErrors, $this->userWarnings, $this->userNotifications);
    }


    /**
     * @param $serviceName
     * @param ServiceRequestID|null $requestID
     * @param array $messageData
     * @return GenericServicePayload
     */
    public function make400GenericServicePayload($serviceName, ServiceRequestID $requestID = null, array $messageData = []) {
        $this->reset();
        $this->serviceName = $serviceName;
        if(null !== $requestID) {
            $this->requestID = $requestID;
        }
        if(null === $this->requestID) {
            $this->requestID = new ServiceRequestID($this->serviceName);
        }

        $this->statusCode = 400;
        $this->statusMessage = 'Bad Request';
        $requestIDString = $this->requestID->getID();
        $this->devErrors = [
            $requestIDString => [
                'code' => 400,
                'message' => 'bad_request',
                'message_data' => $messageData
            ]
        ];
        $this->userErrors = [
            $requestIDString => [
                'code' => 400,
                'message' => 'bad_request',
                'message_data' => $messageData
            ]
        ];
        return $this->makeGenericServicePayload();
    }

    /**
     * @param $serviceName
     * @param ServiceRequestID|null $requestID
     * @param array $messageData
     * @return GenericServicePayload
     */
    public function make403GenericServicePayload($serviceName, ServiceRequestID $requestID = null, array $messageData = []) {
        $this->reset();
        $this->serviceName = $serviceName;
        if(null !== $requestID) {
            $this->requestID = $requestID;
        }
        if(null === $this->requestID) {
            $this->requestID = new ServiceRequestID($this->serviceName);
        }
        $this->statusCode = 403;
        $this->statusMessage = 'Forbidden.';
        $requestIDString = $this->requestID->getID();
        $this->devErrors = [
            $requestIDString => [
                'code' => 403,
                'message' => 'Forbidden.',
                'message_data' => $messageData
            ]
        ];
        $this->userErrors = [
            $requestIDString => [
                'code' => 403,
                'message' => 'not_found',
                'message_data' => $messageData
            ]
        ];
        return $this->makeGenericServicePayload();
    }

    /**
     * @param $serviceName
     * @param ServiceRequestID|null $requestID
     * @param array $messageData
     * @return GenericServicePayload
     */
    public function make404GenericServicePayload($serviceName, ServiceRequestID $requestID = null, array $messageData = []) {
        $this->reset();
        $this->serviceName = $serviceName;
        if(null !== $requestID) {
            $this->requestID = $requestID;
        }
        if(null === $this->requestID) {
            $this->requestID = new ServiceRequestID($this->serviceName);
        }
        $this->statusCode = 404;
        $this->statusMessage = 'Not Found.';
        $this->devErrors = [
            $this->requestID->getID() => [
                'code' => 404,
                'message' => 'Not Found.',
                'message_data' => $messageData
            ]
        ];
        $this->userErrors = [
            $this->requestID->getID() => [
                'code' => 404,
                'message' => 'not_found',
                'message_data' => $messageData
            ]
        ];
        return $this->makeGenericServicePayload();
    }

    /**
     * @param $serviceName
     * @param ServiceRequestID|null $requestID
     * @param array $messageData
     * @return GenericServicePayload
     */
    public function make405GenericServicePayload($serviceName, ServiceRequestID $requestID = null, array $messageData = []) {
        $this->reset();
        $this->serviceName = $serviceName;
        if(null !== $requestID) {
            $this->requestID = $requestID;
        }
        if(null === $this->requestID) {
            $this->requestID = new ServiceRequestID($this->serviceName);
        }
        $this->statusCode = 405;
        $this->statusMessage = 'Method not allowed.';
        $requestIDString = $this->requestID->getID();
        $this->devErrors = [
            $requestIDString => [
                'code' => 405,
                'message' => 'Method not allowed.',
                'message_data' => $messageData
            ]
        ];
        $this->userErrors = [
            $requestIDString => [
                'code' => 405,
                'message' => 'method_not_allowed',
                'message_data' => $messageData
            ]
        ];
        return $this->makeGenericServicePayload();
    }

}