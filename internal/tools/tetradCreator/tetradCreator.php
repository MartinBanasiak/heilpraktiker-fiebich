<?php
include(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . '/vendor/autoload.php');

//Load environment variables from config if exists
$envDir = rtrim(dirname(dirname(__DIR__)),'/') . '/config';

if (is_dir($envDir)) {
    $dotenv = new \Dotenv\Dotenv($envDir);
    $dotenv->load();
}
$appEnv = getenv('APP_ENV');
if (strcasecmp($appEnv,'DEV') !== 0) {
    exit(1);
}

//include('/plugins/paygate/includes/function.inc.php');
include(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . '/dc/common/classes/Validator.php');
include(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . '/dc/common/classes/FormElement.php');
include(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . '/dc/common/classes/Form.php');
include(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . '/dc/common/classes/FormBuilder.php');
require_once(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . "/dc/frontend/frontend_functions.inc.php");
require_once(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . "/dc/common/common_functions.inc.php");
require_once((local_environment()) ? rtrim($_SERVER['DOCUMENT_ROOT'],'/') . "/dc/dc.config.php" : rtrim($_SERVER['DOCUMENT_ROOT'],'/') . "/dc/dc-server.config.php");
require_once(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . "/module/dcshop/common/shop_functions.inc.php");
require_once(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . "/module/dcshop/common/category_functions.inc.php");
require_once(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . "/module/dcshop/common/item_functions.inc.php");
@require_once(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . "/module/dcshop/shop.config.php");
$GLOBALS["myservername"] = getenv('MAIN_MYSQL_DB_HOST');
$GLOBALS["mylogin"] = getenv('MAIN_MYSQL_DB_USER');
$GLOBALS["mypass"] = getenv('MAIN_MYSQL_DB_PASS');
$GLOBALS['mydb'] = getenv('MAIN_MYSQL_DB_SCHEMA');
db_connect();


$tableName = (isset($_GET['tableName']) ? $_GET['tableName']:'');
$modelName = (isset($_GET['modelName']) ? $_GET['modelName']:'');
$path = (isset($_GET['path']) ? urldecode($_GET['path']):'');

if($tableName && $modelName && $path) {
    $createTableQuery = 'SHOW CREATE TABLE ' . $tableName;
    $createTableResult = mysqli_query($GLOBALS['mysql_con'], $createTableQuery);
    if($createTableResult) {
        while($row = mysqli_fetch_row($createTableResult)) {
            if(isset($row[1])) {
                $createTableStmt = $row[1];
            }
        }
    }
}

if(isset($createTableStmt)) {

    $modelNameLower = lcfirst($modelName);
    $configName = $modelName . 'Config';
    $collectionName = $modelName . 'Collection';
    $repositoryName = $modelName . 'Repository';

    $modelTemplate = file_get_contents('ModelClassTemplate.txt');
    $configTemplate = file_get_contents('ConfigClassTemplate.txt');
    $collectionTemplate = file_get_contents('CollectionClassTemplate.txt');
    $repositoryTemplate = file_get_contents('RepositoryClassTemplate.txt');

    $regexConfigArrIsolateSelect = '/(?:CREATE\sTABLE\s`[a-z_0-9]+`\s\(\n)|(?:,[\r\n\s\t]+PRIMARY[a-zA-Z0-9\r\n\s\t,`()\._]*.*)/';
    $regexConfigArrIsolateReplace = '';

    $isolatedFieldDefs = preg_replace($regexConfigArrIsolateSelect,'',$createTableStmt);


    $regexConfigArrSelect = '/`([a-z_0-9]+)`\s([a-zA-Z_]+)[^(]*(?:\((\d+)\)){0,1}.*/';
    $regexConfigArrReplace = '/[\'name\' => \'$1\', \'type\' => \'$2\', \'maxlen\' => \'$3\'],/';

    $configArrStr = preg_replace_callback($regexConfigArrSelect,
        function ($matches) {
            if(!isset($matches[3])) {
                $matches[3] = '';
            }
            if(!isset($matches[4])) {
                $matches[4] = '';
            }
            return "['name' => '{$matches[1]}', 'type' => '{$matches[2]}', 'maxlen' => '{$matches[3]}'],";
        },
        $isolatedFieldDefs
    );

    $regexNavPrimarySelect = '/(?<=nav_primary ON ' . $tableName . ' \()([^)]+)(?=\))/';
	$altRegexNavPrimarySelect = '/(?<=`nav_primary` \(`)([^)]+)(?=`\))/';
    $matches = array();
    $retVal = preg_match($altRegexNavPrimarySelect,$createTableStmt,$matches);
    $navPrimaryArrContent = '[';
    if(isset($matches[0])) {
        $matchesSplit = explode('`,`',$matches[0]);
        $i = 0;
        foreach($matchesSplit as $match) {
            if($i > 0) {
                $navPrimaryArrContent .= ", ";
            }
            $navPrimaryArrContent .= "'$match'";
            ++$i;
        }
    }
    $navPrimaryArrContent .= ']';

    $regexPropertiesArrSelect = '/`([a-z_0-9]+)`.*/';
    $regexPropertiesArrReplace = 'protected \\$1;';
    $propertiesStr = preg_replace_callback($regexPropertiesArrSelect,
        function ($matches) {
            return "protected $" . $matches[1] .";";

        },
        $isolatedFieldDefs
    );

    $searchArray = array(
        '%tablename%',
        '%modelclassname%',
        '%modelclassnameflower%',
        '%configclassname%',
        '%collectionclassname%',
        '%repositoryclassname%',
        '%altprimaryarr%',
        '%mappedfieldsarr%',
        '%properties%'
    );
    $replaceArray = array(
        $tableName,
        $modelName,
        $modelNameLower,
        $configName,
        $collectionName,
        $repositoryName,
        $navPrimaryArrContent,
        $configArrStr,
        $propertiesStr
    );


    $modelClassStr = str_replace($searchArray,$replaceArray,$modelTemplate);
    $configClassStr = str_replace($searchArray,$replaceArray,$configTemplate);
    $collectionClassStr = str_replace($searchArray,$replaceArray,$collectionTemplate);
    $repositoryClassStr = str_replace($searchArray,$replaceArray,$repositoryTemplate);
    $realBasepath = rtrim(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . $path,'/');
    //echo "PATH: " . $path;
    //echo "REALPATH: " . $realBasepath;
    file_put_contents($realBasepath . '/' . $modelName . '.php',$modelClassStr);
    file_put_contents($realBasepath . '/' . $configName . '.php',$configClassStr);
    file_put_contents($realBasepath . '/' . $collectionName . '.php',$collectionClassStr);
    file_put_contents($realBasepath . '/' . $repositoryName . '.php',$repositoryClassStr);

}

$formProps = array(
    'method' => 'get',
    'action' => $_SERVER['PHP_SELF']
);

$fb = new FormBuilder('tetradCreatorForm',$formProps);
$fb->addTextInput('tableName','tableName',null,null,'','Tabellenname: ');
$fb->addTextInput('modelName','modelName',null,null,'','Modellname: ');
$fb->addTextInput('path','path',null,null,'','Pfad: ');
$fb->addField(FormBuilder::ELEMENT_SUBMIT,array('id' => 'submitButton'));
$rulesArr = array(
    'tableName' => 'required',
    'modelName' => 'required',
    'path' => 'required|url'
);
$fb->setFieldRulesWithRuleString($rulesArr);
$formStr = $fb->render();
echo $formStr->getRenderedString();


function snakeCasetoCamel($string) {
    $underscoreReplaced = str_replace('_',' ',$string);
    $uwordsImploded = str_replace(' ','',ucwords($underscoreReplaced));
    return lcfirst($uwordsImploded);
}
	