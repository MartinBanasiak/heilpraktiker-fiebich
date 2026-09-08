<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 20.02.2017
 * Time: 15:09
 */
namespace DynCom\dc\tracking;
use Dotenv\Dotenv;
use PDO,InvalidArgumentException,ErrorException;



/**
 * @return PDO
 */
function get_tracking_pdo_connection_from_env()
{
    static $memo;
    if (null !== $memo) {
        return $memo;
    }
    $rootDir = dirname(__DIR__, 2);
    $configDir = $rootDir . DIRECTORY_SEPARATOR . 'config';
    $trackingDir = $configDir . DIRECTORY_SEPARATOR . 'tracking';
    $vendorDir = $rootDir . DIRECTORY_SEPARATOR . 'vendor';
    require_once($vendorDir . DIRECTORY_SEPARATOR . '/autoload.php');

    $trackingEnvFilePath = $trackingDir . DIRECTORY_SEPARATOR . '.env';
    if (file_exists($trackingEnvFilePath) && is_file($trackingEnvFilePath) && is_readable($trackingEnvFilePath)) {
        $dotenvTracking = new Dotenv($trackingDir);
        $dotenvTracking->load();
    }

    $host = getenv('TRACKING_MYSQL_DB_HOST');
    $port = getenv('TRACKING_MYSQL_DB_PORT');
    $schema = getenv('TRACKING_MYSQL_DB_SCHEMA');
    $user = getenv('TRACKING_MYSQL_DB_USER');
    $pass = getenv('TRACKING_MYSQL_DB_PASS');


    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

    $dsn = 'mysql:dbname=' . $schema . ';host=' . $host . ';port=' . (string)$port . ';charset=utf8mb4';
    if ($host && $schema && $user && $pass) {
        $pdo = new PDO($dsn, $user, $pass, $options);
        $memo = $pdo;
        return $pdo;
    } else {
        return null;
    }
}


/**
 * @return string
 */
function create_uuid()
{
    static $byte_length = 16;
    $string = create_random_bytes($byte_length, true);
    return $string;
}

/**
 * @return string
 */
function get_req_resp_cycle_uuid()
{
    static $currUUID;
    if (null === $currUUID) {
        $currUUID = create_uuid();
    }
    return $currUUID;
}

/**
 * @return string
 */
function get_event_uuid_and_echo_div_once()
{
    static $echoed;
    static $event_uuid;
    if (null === $echoed) {
        $event_uuid = get_req_resp_cycle_uuid();
        $next_event_uuid = create_uuid();
        echo ' <div style="display: none;" id="page_event_data"  data-event_unique_id="'.$event_uuid.'" data-next_event_unique_id="' . $next_event_uuid . '"></div> ';
        $echoed = true;
    }
    return $event_uuid;
}


function echo_tracking_data_div_once()
{
    static $echoed;
    static $resource_name = 'TrackingEvent';

    if (null === $echoed) {
        $tracking_auth_token = get_and_add_tracking_auth_token();
        echo ' <div style="display: none;" id="tracking_data"   data-tracking_auth_token="'.$tracking_auth_token.'"
          data-tracking_api_root="'.htmlentities(getenv('TRACKING_API_ROOT')).'"
           data-tracking_api_version="'.htmlentities(getenv('TRACKING_API_VERSION')).'"
              data-tracking_api_resource_name="'.$resource_name.'" 
              data-session_id_name="sid'. $GLOBALS['site']['code'] .'"
              data-visitor_id="'. (isset($GLOBALS['visitor']['id']) ? (int)$GLOBALS['visitor']['id'] : '') .'"
              data-user_id="'. (isset($GLOBALS['shop_user']['id']) ? (int)$GLOBALS['shop_user']['id'] : '') .'"
              data-customer_id="'. (isset($GLOBALS['shop_customer']['id']) ? (int)$GLOBALS['shop_customer']['id'] : '') .'"
              data-item_id="'. (isset($GLOBALS['item']['id']) ? (int)$GLOBALS['item']['id'] : '') .'"
              data-category_id="'.(isset($GLOBALS['category']['id']) ? (int)$GLOBALS['category']['id'] : '').'"></div> ';
        $echoed = true;
    }
}


/**
 * @return array
 */
function tracking_get_referrer_info()
{
    static $key_pageview_type = 'LAST_PAGEVIEW_TYPE';
    static $key_pageview_id = 'LAST_PAGEVIEW_ID';

    $referrer_info = [
        $key_pageview_type => null,
        $key_pageview_id => null,
    ];

    $referrer_info = [];

    if (array_key_exists($key_pageview_type, $_SESSION) && array_key_exists($key_pageview_id, $_SESSION)) {
        $referrer_info[$key_pageview_type] = $_SESSION[$key_pageview_type];
        $referrer_info[$key_pageview_id] = $_SESSION[$key_pageview_id];

        unset($_SESSION[$key_pageview_type], $_SESSION[$key_pageview_id]);
    }

    return $referrer_info;

}

/**
 * @param string $pageview_type
 * @param int $pageview_id
 */
function tracking_set_referrer_info(string $pageview_type, int $pageview_id)
{
    static $key_pageview_type = 'LAST_PAGEVIEW_TYPE';
    static $key_pageview_id = 'LAST_PAGEVIEW_ID';
    static $valid_pageview_types = [
        TrackingEvent::EVENT_TYPE_PAGEVIEW_ITEM,
        TrackingEvent::EVENT_TYPE_PAGEVIEW_CATEGORY,
        TrackingEvent::EVENT_TYPE_PAGEVIEW_BASKET,
        TrackingEvent::EVENT_TYPE_PAGEVIEW_ORDER,
    ];

    if (in_array($pageview_type, $valid_pageview_types) && !empty($pageview_id)) {
        $_SESSION[$key_pageview_type] = $pageview_type;
        $_SESSION[$key_pageview_id] = $pageview_id;
    } else {
        throw new InvalidArgumentException('Pageview-type must be valid and id non-empty.');
    }
}

/**
 * @return string
 * @throws ErrorException
 */
function get_and_add_tracking_auth_token()
{
    static $addQuery = '
        INSERT INTO
          `auth_tokens`
        SET
          `token` = :token,
          `creation_time` = NOW()
    ';

    $token = create_random_bytes(16, true);
    $pdo = get_tracking_pdo_connection_from_env();
    if ($pdo) {
        $addStmt = $pdo->prepare($addQuery);
        $addStmt->bindValue(':token', $token, PDO::PARAM_STR);
        if (!$addStmt->execute()) {
            $errorInfo = $addStmt->errorInfo();
            $sqlStateErrorCode = isset($errorInfo[0]) ? $errorInfo[0] : 0;
            $driverSpecificErrorCode = isset($errorInfo[1]) ? $errorInfo[1] : 0;
            $driverSpecificErrorMsg = isset($errorInfo[2]) ? $errorInfo[2] : '';
            throw new ErrorException('Could not add token to db. SQLSTATE Error Code: [' . $sqlStateErrorCode . ']. Driver-specific error code: [' . $driverSpecificErrorCode . ']. Driver-specific error message: [' . $driverSpecificErrorMsg . '].');
        }
        return $token;
    } else {
        return null;
    }
}

/**
 * @param $authToken
 * @return bool
 * @throws ErrorException
 */
function check_and_invalidate_auth_token($authToken)
{
    static $checkQuery = '
        SELECT EXISTS (
            SELECT 1 
            FROM `auth_tokens`
            WHERE `token` = :token
        ) AS \'exists\'
    ';

    static $delQuery = '
        DELETE FROM
          `auth_tokens`
        WHERE
          `token` = :token        
    ';

    $pdo = get_tracking_pdo_connection_from_env();

    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->bindValue(':token', $authToken, PDO::PARAM_STR);
    if (!$checkStmt->execute() || !($row = $checkStmt->fetch(PDO::FETCH_ASSOC)) || !$row || !is_array($row) || !array_key_exists('exists', $row)) {
        $errorInfo = $checkStmt->errorInfo();
        $sqlStateErrorCode = isset($errorInfo[0]) ? $errorInfo[0] : 0;
        $driverSpecificErrorCode = isset($errorInfo[1]) ? $errorInfo[1] : 0;
        $driverSpecificErrorMsg = isset($errorInfo[2]) ? $errorInfo[2] : '';
        throw new ErrorException('Could not check token against db. SQLSTATE Error Code: [' . $sqlStateErrorCode . ']. Driver-specific error code: [' . $driverSpecificErrorCode . ']. Driver-specific error message: [' . $driverSpecificErrorMsg . '].');
    }
    $exists = (bool)$row['exists'];
    if ($exists) {
        $delStmt = $pdo->prepare($delQuery);
        $delStmt->bindValue(':token', $authToken, PDO::PARAM_STR);
        if (!$delStmt->execute()) {
            $errorInfo = $delStmt->errorInfo();
            $sqlStateErrorCode = isset($errorInfo[0]) ? $errorInfo[0] : 0;
            $driverSpecificErrorCode = isset($errorInfo[1]) ? $errorInfo[1] : 0;
            $driverSpecificErrorMsg = isset($errorInfo[2]) ? $errorInfo[2] : '';
            throw new ErrorException('Could not delete token from db. SQLSTATE Error Code: [' . $sqlStateErrorCode . ']. Driver-specific error code: [' . $driverSpecificErrorCode . ']. Driver-specific error message: [' . $driverSpecificErrorMsg . '].');
        }
    }
    return $exists;
}

/**
 * @param int $byte_length
 * @param bool $hex
 * @return string
 * @throws ErrorException
 */
function create_random_bytes($byte_length = 16, $hex = false)
{
    $byte_length = (int)$byte_length;
    if (function_exists('random_bytes')) {
        $bytes = random_bytes($byte_length);
    } elseif (extension_loaded('openssl') && function_exists('openssl_random_pseudo_bytes')) {
        $cstrong = null;
        $bytes = openssl_random_pseudo_bytes($byte_length, $cstrong);
        if (false === $cstrong || false === $bytes) {
            throw new ErrorException('Could not generate cryptographically string random byte-string.');
        }
    } else {
        throw new ErrorException('Could not generate cryptographically string random byte-string. Neither random_bytes nor openssl_random_pseudo_bytes is available');
    }
    if ($hex) {
        $bytes = bin2hex($bytes);
    }
    return $bytes;
}

/**
 * @param $jsonStringToPatch
 * @param $patchJsonString
 * @return string
 */
function json_merge_patch_strings($jsonStringToPatch,$patchJsonString)
{
    $objToPatch = json_decode($jsonStringToPatch);
    $patchObj = json_decode($patchJsonString);

    $mergedObj = json_merge_patch($objToPatch,$patchObj);
    $encodedMergedObj = json_encode($mergedObj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
    return $encodedMergedObj;
}

/**
 * @param $target
 * @param $patch
 * @return mixed
 */
function json_merge_patch($target, $patch)
{
    if (!is_object($patch)) {
        return $patch;
    }

    if (!is_object($target)) {
        $target = new \stdClass();
    }

    foreach ($patch as $key => $val) {
        if (null === $val || 0x00 === $val || '0x00' === $val || 'null' === $val || 'NULL' === $val) {
            if (is_object($target) && property_exists($target, $key)) {
                unset($target->$key);
            } elseif (is_array($target) && array_key_exists($key, $target)) {
                unset($target[$key]);
            }
        }

        if (is_object($target)) {
            $prevVal = property_exists($target,$key) ? $target->$key : null;
            $target->$key = json_merge_patch($prevVal,$val);
        } elseif (is_array($target)) {
            $prevVal = array_key_exists($key,$target) ? $target[$key] : null;
            $target[$key] = json_merge_patch($prevVal,$val);
        }

    }
    return $target;

}




