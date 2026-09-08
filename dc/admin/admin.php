<?php
//Vendor Autoloader
$rootDir = rtrim(dirname(dirname(__DIR__)),'/\\');
include($rootDir . '/vendor/autoload.php');
require_once  $rootDir . DIRECTORY_SEPARATOR . 'dc' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'common_functions.inc.php';
//include(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/dc/DcAutoloader.php');
//$dcAutoloader = new DcAutoloader();
//$dcAutoloader->register();

//Load environment variables from config if exists
$envDir = rtrim(dirname(dirname(__DIR__)), '/') . '/config';
if (is_dir($envDir)) {
    $dotenv = new \Dotenv\Dotenv($envDir);
    $dotenv->load();
}

//Load frontend function
require_once(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'frontend/frontend_functions.inc.php');


admin_extract_site_request_uri_variables();

function admin_extract_site_request_uri_variables()
{
    $site_base_pattern = '#/dc/([^/]+)/([^/]+)/([^/]+/){0,1}([^/]+/){0,1}([^/]+/){0,1}([^/]+/){0,1}([^/]+/){0,1}#';
    $site_base_pattern_matches = [];
    preg_match($site_base_pattern, $_SERVER['REQUEST_URI'], $site_base_pattern_matches);
    if (
        array_key_exists(1, $site_base_pattern_matches)
        && !empty($site_base_pattern_matches[1])
        && array_key_exists(2, $site_base_pattern_matches)
        && !empty($site_base_pattern_matches[2])
    ) {
        $_GET['site'] = filter_var($site_base_pattern_matches[1], FILTER_SANITIZE_STRING);
        if (empty($_GET['site'])) {
            $_GET['site'] = admin_get_site_code_from_domain();
        }
        $_GET['language'] = filter_var($site_base_pattern_matches[2], FILTER_SANITIZE_STRING);


        //Try to max category level 5 (index 3 to 8)
        $noOfPathSegments = count($site_base_pattern_matches);
        if ($noOfPathSegments > 6) {
            $noOfPathSegments = 6;
        }

        for ($i = 3; $i <= $noOfPathSegments; $i++) {
            $levelindex = $i - 2;
            $matchedPattern = array_key_exists($i, $site_base_pattern_matches) ? $site_base_pattern_matches[$i] : '';
            $code = filter_var(rtrim($matchedPattern, '/'), FILTER_SANITIZE_STRING);
            $_GET['level_' . $levelindex] = $code;
        }
    }
}


function admin_get_site_code_from_domain()
{
    $prepStatement = 'SELECT id,code FROM main_site WHERE site_url = :domain ORDER BY id LIMIT 1';

    $siteDomain = $_SERVER['SERVER_NAME'];
    $siteCode = '';
    $dbHost = getenv('MAIN_MYSQL_DB_HOST');
    $dbPort = (int)getenv('MAIN_MYSQL_DB_PORT');
    $dbUser = getenv('MAIN_MYSQL_DB_USER');
    $dbSchema = getenv('MAIN_MYSQL_DB_SCHEMA');
    $dbPass = getenv('MAIN_MYSQL_DB_PASS');

    try {
        $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($dbHost, $dbPort, $dbSchema, $dbUser, $dbPass);
        $params = [
            [':domain', $siteDomain, PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $resArr = $pdo->getResultArray();
        if (array_key_exists(0, $resArr) && array_key_exists('id', $resArr[0]) && $resArr[0]['id'] > 0) {
            $siteCode = $resArr[0]['code'];
        }
    } catch (Exception $e) {
        //Do noting
    }
    return $siteCode;
}



?>
<?php headerFunctionBridge('Content-Type: text/html; charset=UTF-8') ?>
<?php require_once  dirname(__DIR__) . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'common_functions.inc.php'; ?>
<?php require_once (local_environment()) ? dirname(__DIR__) . '/dc.config.php' : dirname(__DIR__) . '/dc-server.config.php'; ?>
<?php require_once __DIR__ . DIRECTORY_SEPARATOR . 'admin_functions.inc.php'; ?>
<?php require_once __DIR__ . DIRECTORY_SEPARATOR . 'start.inc.php'; ?>
<?php if (!$GLOBALS["visitor"]["admin_login"]) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'login.inc.php';
    exit(0);
} ?>
    <!DOCTYPE html>
    <html lang="de">
    <?php require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'common/version_comment.inc.php'; ?>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title><?= admin_site_title() ?></title>
        <script type="text/javascript" src="<?= $GLOBALS['projectRoot'] ?>/plugins/jquery/jquery-1.6.4.min.js"></script>
        <script type="text/javascript" src="<?= $GLOBALS['projectRoot'] ?>/plugins/jquery/jquery-ui-1.8.16.custom.min.js"></script>
        <script type="text/javascript" src="<?= $GLOBALS['projectRoot'] ?>/plugins/formdata/formdata.js"></script>
        <script type="text/javascript" src="<?= $GLOBALS['projectRoot'] ?>/plugins/jquery/jquery.cookie.js"></script>
        <script type="text/javascript" src="<?= $GLOBALS['projectRoot'] ?>/plugins/jQuery-actual/jquery.actual.min.js"></script>
        <script type="text/javascript" src="<?= $GLOBALS['projectRoot'] ?>/dc/admin/admin.js?t=18"></script>
        <script type="text/javascript" src="<?= $GLOBALS['projectRoot'] ?>/plugins/ckeditor/ckeditor.js"></script>
        <script type="text/javascript" src="<?= $GLOBALS['projectRoot'] ?>/plugins/ckeditor/adapters/jquery.js"></script>
        <script type="text/javascript" src="<?= $GLOBALS['projectRoot'] ?>/plugins/ckfinder/ckfinder.js"></script>
        <script type="text/javascript" src="<?= $GLOBALS['projectRoot'] ?>/plugins/dropzone/dropzone.js"></script>

        <script type="text/javascript" src="<?= $GLOBALS['projectRoot'] ?>/plugins/jquery-nestedSortable/jquery.mjs.nestedSortable.js"></script>
        <script  src="<?= $GLOBALS['projectRoot'] ?>/components/password-strength-meter/dist/password.min.js"></script>

        <link rel="apple-touch-icon" sizes="180x180" href="<?= $GLOBALS['projectRoot'] ?>/layout/admin/img/2016/favicons/apple-touch-icon.png">
        <link rel="icon" type="image/png" href="<?= $GLOBALS['projectRoot'] ?>/layout/admin/img/2016/favicons/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="<?= $GLOBALS['projectRoot'] ?>/layout/admin/img/2016/favicons/favicon-16x16.png" sizes="16x16">
        <link rel="manifest" href="<?= $GLOBALS['projectRoot'] ?>/layout/admin/img/2016/favicons/manifest.json">
        <link rel="mask-icon" href="<?= $GLOBALS['projectRoot'] ?>/layout/admin/img/2016/favicons/safari-pinned-tab.svg" color="#99c137">
        <meta name="theme-color" content="#ffffff">

        <link href="<?= $GLOBALS['projectRoot'] ?>/plugins/jqueryui/css/metro/jquery-ui.css" rel="stylesheet" type="text/css"/>
        <link href="<?= $GLOBALS['projectRoot'] ?>/layout/admin/css/fonts.css" rel="stylesheet" type="text/css"/>
        <link href="<?= $GLOBALS['projectRoot'] ?>/layout/admin/css/dc.css?t=18" rel="stylesheet" type="text/css"/>
        <link href="<?= $GLOBALS['projectRoot'] ?>/plugins/dropzone/dropzone.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" type="text/css" href="<?= $GLOBALS['projectRoot'] ?>/components/password-strength-meter/dist/password.min.css" />

    </head>
    <body>
    <div id="container">
        <div id="container_1"></div>
        <div id="container_2">
            <div id="header">
                <div id="header_1"><a href="/dc">&nbsp;</a></div>
                <div id="header_2"><?php require_once __DIR__ . DIRECTORY_SEPARATOR . 'header.inc.php'; ?></div>
                <div id="header_3"></div>
            </div>
            <div id="gadget">
                <div id="gadget_1"></div>
                <div id="gadget_2"></div>
                <div id="gadget_3"></div>
            </div>
            <div id="menu" class="<?php echo $GLOBALS["menu_opened"] === true ? 'menu_opened' : ''; ?>">
                <div id="menu_1"></div>
                <div id="menu_2"><?php require_once __DIR__ . DIRECTORY_SEPARATOR . 'submenu.inc.php'; ?></div>
                <div id="menu_3"></div>
            </div>
            <div id="content">
                <div id="content_1"></div>
                <div id="content_2"></div>
                <?php require_once __DIR__ . DIRECTORY_SEPARATOR . 'content.inc.php'; ?>
            </div>
            <div id="box">
                <div id="box_1"></div>
                <div id="box_2"></div>
                <div id="box_3"></div>
            </div>
            <div id="footer">
                <div id="footer_1"></div>
                <div id="footer_2"></div>
                <div id="footer_3"></div>
            </div>
            <div id="container_3"></div>
        </div>
        <div id="overlay"></div>
        <div id="overlayWrapper">
            <div id="overlayContent"></div>
        </div>
        <div id="overlayLoader"></div>
    </body>
    </html>
<?php require_once __DIR__ . DIRECTORY_SEPARATOR . 'close.inc.php'; ?>