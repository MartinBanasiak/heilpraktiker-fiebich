<?php

namespace DynCom\dc\tracking;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 20.02.2017
 * Time: 16:51
 */

use Dotenv\Dotenv;
use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class TrackingAPIController
 */
class TrackingAPIController
{

    protected const TRACKING_ENV_FILE_DIR_RELATIVE_TO_PROJECT_BASE = 'config/tracking/';

    protected const RESOURCE_NAME = 'TrackingEvent';

    protected const CONTENT_TYPE_APPLICATION_JSON = 'application/json';

    protected const METHOD_GET = 'GET';
    protected const METHOD_HEAD = 'HEAD';
    protected const METHOD_POST = 'POST';
    protected const METHOD_PUT = 'PUT';
    protected const METHOD_PATCH = 'PATCH';
    protected const METHOD_DELETE = 'DELETE';
    protected const METHOD_OPTIONS = 'OPTIONS';

    public const ERR_AUTH_INVALID = 0;
    public const ERR_RESOURCE_ID_NOT_FOUND = 1;
    public const ERR_UNSUPPORTED_MEDIA_TYPE = 2;
    public const ERR_INVALID_DATA = 3;
    public const ERR_METHOD_NOT_IMPLEMENTED = 4;
    public const ERR_BACKING_SERVICE_UNAVAILABLE = 5;
    public const ERR_BACKING_SERVICE_INVALID_RESPONSE = 6;

    protected const KEY_REQUEST_METHOD = 'REQUEST_METHOD';
    protected const KEY_REQUESTED_RESOURCE_NAME = 'REQUESTED_RESOURCE_NAME';
    protected const KEY_RESOURCE_UUID = 'resource_uuid';
    protected const KEY_SENT_DATA = 'posted_json_data';
    protected const KEY_SUPPLEMENTAL_ERROR_MESSAGE = 'supplemental_error_message';
    protected const KEY_UUID = 'uuid';
    protected const KEY_EVENT_DATA = 'event_data';
    protected const KEY_VISITOR_ID = 'visitor_id';
    protected const KEY_USER_ID = 'user_id';
    protected const KEY_CUSTOMER_ID = 'customer_id';
    protected const KEY_ITEM_ID = 'item_id';
    protected const KEY_CATEGORY_ID = 'category_id';
    protected const KEY_EXISTS = 'exists';


    protected const ALLOWED_METHODS = [
        self::METHOD_GET,
        self::METHOD_HEAD,
        self::METHOD_POST,
        self::METHOD_PATCH,
    ];

    protected const ALLOWED_POST_CONTENT_TYPES = [
        self::CONTENT_TYPE_APPLICATION_JSON,
    ];

    protected const ALLOWED_PATCH_CONTENT_TYPES = [
        self::CONTENT_TYPE_APPLICATION_JSON,
    ];

    protected const ALLOWED_PUT_CONTENT_TYPES = [
        self::CONTENT_TYPE_APPLICATION_JSON,
    ];


    /**
     * @var TrackingAPIService
     */
    protected $trackingAPIService;

    /**
     * @var string
     */
    protected $trackingAPIRoot;

    /**
     * @var string
     */
    protected $trackingAPIVersion;

    /**
     * @var LoggerInterface
     */
    protected $logger;


    /**
     * TrackingAPIController constructor.
     * @param TrackingAPIService $trackingAPIService
     */
    public function __construct(TrackingAPIService $trackingAPIService, LoggerInterface $logger = null)
    {
        $this->initialize($trackingAPIService, $logger);
    }

    /**
     * @param TrackingAPIService $trackingAPIService
     */
    protected function initialize(TrackingAPIService $trackingAPIService, LoggerInterface $logger = null): void
    {
        $this->trackingAPIService = $trackingAPIService;
        if (null === $logger) {
            $logger = new NullLogger();
        }
        $this->logger = $logger;
        $this->loadEnvVariables();
        $this->trackingAPIRoot = getenv('TRACKING_API_ROOT') ?: 'tracking-api';
        $this->trackingAPIVersion = getenv('TRACKING_API_VERSION') ?: 'v0';

    }

    public function handleTrackingRequest(): void
    {
        $method = $_SERVER[self::KEY_REQUEST_METHOD];

        $this->guardMethod($method);
        switch ($method) {
            case self::METHOD_OPTIONS:
                $this->respondToOptionsRequest();
                break;
            case self::METHOD_HEAD:
                $this->handleHeadTrackingEvent();
                break;
            case self::METHOD_GET:
                $this->handleGetTrackingEvent();
                break;
            case self::METHOD_POST:
                $this->handlePostTrackingEvent();
                break;
            case self::METHOD_PATCH:
                $this->handlePatchTrackingEvent();
                break;
            case self::METHOD_DELETE:
                $this->handleError(
                    self::ERR_METHOD_NOT_IMPLEMENTED,
                    [self::KEY_REQUEST_METHOD => $method, self::KEY_REQUESTED_RESOURCE_NAME => self::RESOURCE_NAME]
                );
                break;
            case self::METHOD_PUT:
                $this->handleError(
                    self::ERR_METHOD_NOT_IMPLEMENTED,
                    [self::KEY_REQUEST_METHOD => $method, self::KEY_REQUESTED_RESOURCE_NAME => self::RESOURCE_NAME]
                );
                break;
            default:
                $this->handleError(
                    self::ERR_METHOD_NOT_IMPLEMENTED,
                    [self::KEY_REQUEST_METHOD => $method, self::KEY_REQUESTED_RESOURCE_NAME => self::RESOURCE_NAME]
                );
                break;
        }
    }

    protected function respondToOptionsRequest(): void
    {
        $allowedMethodsString = implode(', ', self::ALLOWED_METHODS);
        $allowHeader = 'Allow: ' . $allowedMethodsString;
        $accessControlAllowMethodsHeader = 'Access-Control-Allow-Methods: ' . $allowedMethodsString;
        headerFunctionBridge($allowHeader);
        headerFunctionBridge($accessControlAllowMethodsHeader);
        exit(0);
    }

    protected function handleHeadTrackingEvent(): void
    {
        $resourceID = $this->getResourceIDFromURL($this->trackingAPIRoot, $this->trackingAPIVersion);
        $exists = $this->trackingAPIService->existsTrackingEventForUniqueID($resourceID);

        if (!$exists) {
            $this->handleError(
                self::ERR_RESOURCE_ID_NOT_FOUND,
                [self::KEY_REQUESTED_RESOURCE_NAME => self::RESOURCE_NAME, self::KEY_RESOURCE_UUID => $resourceID]
            );
        } else {
            $resource = $this->trackingAPIService->getTrackingEventByUniqueID($resourceID);
            if (null !== $resource) {
                $creationTimestamp = $resource->getCreatedTimestamp();
                $lastModifiedTimestamp = $resource->getLastModifiedTimestamp() ?: $creationTimestamp;
                $lastModifiedHeaderDate = gmdate('D, d M Y H:i:s', $lastModifiedTimestamp) . ' GMT';
                $contentLength = mb_strlen(
                    json_encode(
                        $resource,
                        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION
                    )
                );
                $contentTypeHeader = 'Content-Type: application/json';
                $contentLengthHeader = 'Content-Length: ' . $contentLength;
                $lastModifiedHeader = 'Last-Modified: ' . $lastModifiedHeaderDate;
                headerFunctionBridge($contentTypeHeader);
                headerFunctionBridge($contentLengthHeader);
                headerFunctionBridge($lastModifiedHeader);
                exit(0);
            }
        }

    }

    protected function handleGetTrackingEvent(): void
    {
        $resourceID = $this->getResourceIDFromURL($this->trackingAPIRoot, $this->trackingAPIVersion);
        $exists = $this->trackingAPIService->existsTrackingEventForUniqueID($resourceID);

        if (!$exists) {
            $this->handleError(
                self::ERR_RESOURCE_ID_NOT_FOUND,
                [self::KEY_REQUESTED_RESOURCE_NAME => self::RESOURCE_NAME, self::KEY_RESOURCE_UUID => $resourceID]
            );
        } else {
            $resource = $this->trackingAPIService->getTrackingEventByUniqueID($resourceID);
            if (null !== $resource) {
                $creationTimestamp = $resource->getCreatedTimestamp();
                $lastModifiedTimestamp = $resource->getLastModifiedTimestamp() ?: $creationTimestamp;
                $lastModifiedHeaderDate = gmdate('D, d M Y H:i:s', $lastModifiedTimestamp) . ' GMT';
                $jsonEncodedResource = json_encode(
                    $resource,
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION
                );
                $contentLength = mb_strlen($jsonEncodedResource);
                $contentTypeHeader = 'Content-Type: ' . self::CONTENT_TYPE_APPLICATION_JSON;
                $contentLengthHeader = 'Content-Length: ' . $contentLength;
                $lastModifiedHeader = 'Last-Modified: ' . $lastModifiedHeaderDate;
                headerFunctionBridge($contentTypeHeader);
                headerFunctionBridge($contentLengthHeader);
                headerFunctionBridge($lastModifiedHeader);
                echo $jsonEncodedResource;
                exit(0);
            }
        }
    }

    protected function handlePostTrackingEvent(): void
    {
        $this->handleContentType(self::ALLOWED_POST_CONTENT_TYPES);

        $json = file_get_contents('php://input'); //Raw POST Request-Body

        $trackingEvent = null;
        try {
            $trackingEvent = TrackingEvent::fromJSON($json);
        } catch (Exception $e) {
            $errData = [
                self::KEY_SENT_DATA => $json,
            ];
            $jsonErrCode = json_last_error();
            $jsonErrMsg = '';
            $this->handleError(self::ERR_INVALID_DATA, $errData);
        }

        if (null !== $trackingEvent && $trackingEvent instanceof TrackingEvent && $this->trackingAPIService->addTrackingEvent(
                $trackingEvent
            )
        ) {
            http_send_status(200);
            echo 'OK';
            exit(0);
        } else {
            $supplementalMsg = 'The TrackingEvent was successfully parsed but could not be stored in the database.';
            $errData = [
                self::KEY_SUPPLEMENTAL_ERROR_MESSAGE => $supplementalMsg,
            ];
            exit(0);
        }
    }

    protected function handlePatchTrackingEvent(): void
    {
        $this->handleContentType(self::ALLOWED_PATCH_CONTENT_TYPES);
        $receivedDataArray = [];
        $json = '';

        $resourceID = $this->getResourceIDFromURL($this->trackingAPIRoot, $this->trackingAPIVersion);
        $exists = $this->trackingAPIService->existsTrackingEventForUniqueID($resourceID);

        if (!$exists) {
            $errData = [
                self::KEY_RESOURCE_UUID => $resourceID,
            ];
            $this->handleError(self::ERR_RESOURCE_ID_NOT_FOUND, $errData);
            exit(0);
        }

        $error = false;
        $supplementalErrMsg = '';
        try {
            $json = file_get_contents('php://input'); //Raw POST Request-Body
            $receivedDataArray = json_decode($json, true);
        } catch (Exception $e) {
            $error = true;
            $supplementalErrMsg .= '' . PHP_EOL;
        }


        if (!$error) {
            if (!array_key_exists(self::KEY_EVENT_DATA, $receivedDataArray) || !is_array(
                    $receivedDataArray[self::KEY_EVENT_DATA]
                )
            ) {
                $error = true;
                $supplementalErrMsg .= 'Data must contain an event-data field of type array.' . PHP_EOL;
            }
            if (array_key_exists(self::KEY_UUID, $receivedDataArray) || array_key_exists(
                    'event_type',
                    $receivedDataArray
                ) || array_key_exists('creation_timestamp', $receivedDataArray) || array_key_exists(
                    'last_modified_timestamp',
                    $receivedDataArray
                )
            ) {
                $error = true;
                $supplementalErrMsg .= 'Fields [uuid,event_type,creation_timestamp,last_modified_timestamp] cannot be patched manually. Only event_data can be patched.';
            }
        }

        if ($error) {
            $this->handleError(self::ERR_INVALID_DATA, [self::KEY_SUPPLEMENTAL_ERROR_MESSAGE => $supplementalErrMsg]);
        }

        $resource = $this->trackingAPIService->getTrackingEventByUniqueID($resourceID);
        try {
            $newResource = $resource->withJSONMergePatchData($json);
        } catch (Exception $e) {
            $this->handleError(self::ERR_INVALID_DATA, [self::KEY_SUPPLEMENTAL_ERROR_MESSAGE => $e->getMessage()]);
            exit(0);
        }


        try {
            if ($this->trackingAPIService->putTrackingEvent($newResource)) {
                $apiRoot = $this->trackingAPIRoot;
                $apiVersion = $this->trackingAPIVersion;
                $resourceeName = self::RESOURCE_NAME;
                $uuid = $newResource->getUuid();
                $location = '/' . $apiRoot . '/' . $apiVersion . '/' . $resourceeName . '/' . $resourceID;
                $locationHeader = 'Location: ' . $location;

                headerFunctionBridge($locationHeader);
                http_send_status(201);
                echo 'Creatd';
                exit(0);
            } else {
                $this->handleError(self::ERR_BACKING_SERVICE_INVALID_RESPONSE);
                exit(0);
            }

        } catch (Exception $e) {
            $errData = [
                self::KEY_SENT_DATA => $json,
                self::KEY_SUPPLEMENTAL_ERROR_MESSAGE => $e->getMessage(),
            ];
            if ($e instanceof \PDOException) {
                $err = self::ERR_BACKING_SERVICE_INVALID_RESPONSE;
            } else {
                $err = self::ERR_INVALID_DATA;
            }
            $this->handleError($err, $errData);
            exit(0);
        }

    }

    protected function handleDeleteTrackingEvent(): void
    {
        //Not implemented yet
    }

    /**
     * @param string $method
     */
    protected function guardMethod(string $method): void
    {

        if (self::METHOD_OPTIONS === $method) {
            return;
        }

        $authToken = $this->getAuthorizeHeaderBearerToken();
        $validToken = check_and_invalidate_auth_token($authToken);
        if (!$validToken) {
            $this->handleError(self::ERR_AUTH_INVALID);
            exit(0);
        }

    }

    /**
     * @param $errType
     * @param array $errData
     * @throws ErrorException
     */
    protected function handleError(int $errType, array $errData = []): void
    {
        $code = 500;
        $msg = 'Unspecified internal error.';
        switch ($errType) {

            case self::ERR_AUTH_INVALID:
                $code = 401;
                $msg = 'Auth-token could not be validated.';
                break;

            case self::ERR_RESOURCE_ID_NOT_FOUND:
                $code = 404;
                $resourceName = self::RESOURCE_NAME;
                $resourceID = isset($errData[self::KEY_RESOURCE_UUID]) ? $errData[self::KEY_RESOURCE_UUID] : '';
                $msg = 'The resource of tpye [' . $resourceName . '] with the id [' . $resourceID . '] could not be found.';
                break;

            case self::ERR_UNSUPPORTED_MEDIA_TYPE:
                $code = 415;
                $mediaType = $_SERVER['CONTENT_TYPE'];
                $msg = 'The only [application/json] is accepted as a Content-Type for posting and patching TrackingEvents. Given Content-Type was [' . $mediaType . '].';
                break;

            case self::ERR_INVALID_DATA:
                $code = 400;
                $sentData = isset($errData[self::KEY_SENT_DATA]) ? $errData[self::KEY_SENT_DATA] : '';
                $msg = 'The data sent from the client for posting or patching resource [' . self::RESOURCE_NAME . '] was invalid. Data sent was [' . $errData[self::KEY_SENT_DATA];
                break;

            case self::ERR_METHOD_NOT_IMPLEMENTED:
                $code = 405;
                $method = isset($errData[self::KEY_REQUEST_METHOD]) ? $errData[self::KEY_REQUEST_METHOD] : '';
                $resourceName = isset($errData[self::KEY_REQUESTED_RESOURCE_NAME]) ? $errData[self::KEY_REQUESTED_RESOURCE_NAME] : '';
                $msg = 'The requested method [' . $_SERVER[self::KEY_REQUEST_METHOD] . '] is not implemented for the requested resource.';
                break;

            case self::ERR_BACKING_SERVICE_UNAVAILABLE:
                $code = 502;
                $msg = 'A backing-service for the API did not deliver a valid response. Please contact support.';
                break;

            case self::ERR_BACKING_SERVICE_INVALID_RESPONSE:
                $code = 502;
                $msg = 'A backing-service for the API did not deliver a valid response. Please contact support.';
                break;

            default:
                throw new ErrorException('Unknown error.');
        }
        if (array_key_exists(self::KEY_SUPPLEMENTAL_ERROR_MESSAGE, $errData)) {
            $msg .= PHP_EOL . PHP_EOL . $errData[self::KEY_SUPPLEMENTAL_ERROR_MESSAGE];
        }

        $msgLen = mb_strlen($msg);

        http_response_code($code);
        headerFunctionBridge('Content-Type: text/plain');
        headerFunctionBridge('Content-Length: ' . $msgLen);

        echo $msg;
        exit(0);
    }

    /**
     * @return string
     */
    protected function getClientRequestedURL(): string
    {
        $s = $_SERVER;
        $useForwardedHost = true;
        $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on');
        $sp = strtolower($s['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $port = $s['SERVER_PORT'];
        $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
        $host = ($useForwardedHost && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
        $host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
        $requestBasename = $protocol . '://' . rtrim($host, '/');

        $fullURL = $requestBasename . '/' . ltrim($s['REQUEST_URI'], '/');
        return $fullURL;
    }

    protected function loadEnvVariables(): void
    {
        $projectBaseDir = dirname(dirname(__DIR__));
        $envDir = $projectBaseDir . DIRECTORY_SEPARATOR . self::TRACKING_ENV_FILE_DIR_RELATIVE_TO_PROJECT_BASE;
        $envLoader = new Dotenv($envDir);
        $envLoader->load();
    }

    /**
     * @param string $apiRoot
     * @param string $apiVersion
     * @return string $resourceID
     */
    protected function getResourceIDFromURL(string $apiRoot, string $apiVersion): string
    {
        $resourceID = '';
        $delimiter = '#';
        $resourcePrefix = '/' . $apiRoot . '/' . $apiVersion . '/' . self::RESOURCE_NAME . '/';
        $escapedResourcePrefix = preg_quote($resourcePrefix, $delimiter);
        $pattern = $delimiter . $escapedResourcePrefix . '([^\/\?$\s\r\n]+)' . $delimiter . 'i';
        $matches = [];
        $url = $this->getClientRequestedURL();
        $noOfMatches = preg_match($pattern, $url, $matches);
        if (array_key_exists(1, $matches)) {
            $resourceID = (string)$matches[1];
        }
        return $resourceID;
    }

    /**
     * @param array $allowedContentTypes
     */
    protected function handleContentType(array $allowedContentTypes): void
    {
        if (!in_array($_SERVER['CONTENT_TYPE'], $allowedContentTypes, true)) {
            $this->handleError(self::ERR_UNSUPPORTED_MEDIA_TYPE);
            exit(0);
        }
    }

    /**
     * @return string
     */
    protected function getAuthorizeHeaderBearerToken(): string
    {
        $authHeader = isset($_SERVER['HTTP_AUTHORIZE']) ? $_SERVER['HTTP_AUTHORIZE'] : '';
        $regexPatternAuthTypeData = '#([A-Za-z]+?)\s+([^$\r\n\s]+)#';
        $matchesAuthTypeData = [];
        $noOfResults = preg_match($regexPatternAuthTypeData, $authHeader, $matchesAuthTypeData);
        $authToken = '';
        if (array_key_exists(1, $matchesAuthTypeData) && 'Bearer' === $matchesAuthTypeData[1] && array_key_exists(
                2,
                $matchesAuthTypeData
            )
        ) {
            $authToken = $matchesAuthTypeData[2];
        }
        return (string)$authToken;
    }

}