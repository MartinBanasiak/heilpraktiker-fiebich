<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 19.03.2016
 * Time: 15:27
 */
require 'MarketplaceWorkerSupervisor.php';
$supervisor = new MarketplaceWorkerSupervisor();
$supervisor->supervise();