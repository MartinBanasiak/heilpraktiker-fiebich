<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 19.10.2015
 * Time: 18:44
 */
ini_set('display_errors',1);
error_reporting(E_ALL);
$basketPersistenceHandler->checkAndPersist($currUserBasket);