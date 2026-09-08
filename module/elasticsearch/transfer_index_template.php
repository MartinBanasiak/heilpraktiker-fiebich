<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 26.09.2017
 * Time: 10:14
 */

$rootDir = dirname(__DIR__,2);
include $rootDir . '/vendor/autoload.php';

$configDir = $rootDir . '/config';
$envFilePath = $configDir . '/.env';
if (file_exists($envFilePath) && is_file($envFilePath) && is_readable($envFilePath)) {
    $dotEnv = new \Dotenv\Dotenv($configDir);
    $dotEnv->load();
}


//Instantiate Elasticsearch Client
$elasticUser = getenv('ELASTIC_USER');
$elasticPass = getenv('ELASTIC_PASS');
$elasticBaseURL = getenv('ELASTIC_BASE_URL');
$elasticPort = getenv('ELASTIC_PORT');
$elasticIndexName = getenv('ELASTIC_INDEX');
$elasticItemType = getenv('ELASTIC_ITEM_TYPE');

$hosts = [
    [
        'host' => $elasticBaseURL,
        'port' => $elasticPort,
        'user' => $elasticUser,
        'pass' => $elasticPass
    ]
];

//Instantiate Elasticsearch Client
$elasticUser = getenv('ELASTIC_USER');
$elasticPass = getenv('ELASTIC_PASS');
$elasticBaseURL = getenv('ELASTIC_BASE_URL');
$elasticPort = getenv('ELASTIC_PORT');
$elasticIndexName = getenv('ELASTIC_INDEX');
$elasticItemType = getenv('ELASTIC_ITEM_TYPE');
echo "Host is $elasticBaseURL with port $elasticPort. Index name is $elasticIndexName and user is $elasticUser." . PHP_EOL;
$hosts = [
    [
        'host' => $elasticBaseURL,
        'port' => $elasticPort,
        'user' => $elasticUser,
        'pass' => $elasticPass
    ]
];
$elasticClient = \Elasticsearch\ClientBuilder::create()->setHosts($hosts)->build();

echo 'Checking if index exists...' . PHP_EOL;
$queryParamExistsIndex = ['index' => $elasticIndexName];
$indexExists = $elasticClient->indices()->exists($queryParamExistsIndex);
$indexSettingsFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'index_settings.json';
if (!$indexExists ) {
    echo "Index with name [$elasticIndexName] does not exist - creating..." . PHP_EOL;
    if (!file_exists($indexSettingsFilePath) || !is_file($indexSettingsFilePath) || !is_readable($indexSettingsFilePath)) {
        throw new ErrorException('Index settings file not present or not a readable file at [' . $indexSettingsFilePath . '].' . PHP_EOL);
    }
    $indexParams = file_get_contents($indexSettingsFilePath);
    $indexCreateResponse = $elasticClient->indices()->putSettings(json_decode($indexParams,true));
    if (!$elasticClient->indices()->exists($hosts)) {
        throw new ErrorException('Could not create index. Response was ' . var_export($indexCreateResponse,true) . '.' . PHP_EOL);
    } else {
        echo '...Index successfully created' . PHP_EOL;
    }
} else {
    echo 'Index exists - putting index template...' . PHP_EOL;
}


$indexTemplateFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'index_template.json';
if (!file_exists($indexTemplateFilePath) || !is_file($indexTemplateFilePath) || !is_readable($indexTemplateFilePath)) {
    throw new ErrorException('Index template file not present or not a readable file at [' . $indexTemplateFilePath . '].');
}


$templateName = 'dcshop_item_index_template_v01';

$payload = [
    'name' => $templateName,
    'body' => file_get_contents($indexTemplateFilePath),
];

$templateExists = $elasticClient->indices()->existsTemplate(['name' => $templateName]);

if (!$templateExists) {
    echo 'Index template does not exist. Creating...' . PHP_EOL;$response = $elasticClient->indices()->putTemplate($payload);
    $success = is_array($response) && array_key_exists('acknowledged',$response) && $response['acknowledged'] === true;

    if ($success) {
        echo 'Template at [' . $indexTemplateFilePath . '] successfully transferred.' . PHP_EOL;
    } else {
        echo 'Template at [' . $indexTemplateFilePath . '] could not be transferred successfully. Response was: ' . PHP_EOL . var_export($response,true) . PHP_EOL;
    }
} else {
    echo 'Index template already exists. Nothing to do. Goodbye!' . PHP_EOL;
}

