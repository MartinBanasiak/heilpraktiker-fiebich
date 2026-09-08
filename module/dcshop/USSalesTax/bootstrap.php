<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 08.06.2017
 * Time: 12:00
 */
$rootDir = dirname(__DIR__,3);
$configDir = $rootDir . DIRECTORY_SEPARATOR . 'config';
$taxConfigDir = $configDir . DIRECTORY_SEPARATOR . 'USSalesTax';
$envFilePath = $taxConfigDir . DIRECTORY_SEPARATOR . '.env';
if (file_exists($envFilePath) && is_file($envFilePath) && is_readable($envFilePath)) {
    $dotenv = new \Dotenv\Dotenv($taxConfigDir);
    $dotenv->load();
}


