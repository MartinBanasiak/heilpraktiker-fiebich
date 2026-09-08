<?php
require_once '../../plugins/ckfinder/ckfinder.php';

// You can use the "CKFinder" class to render CKFinder in a page:
$finder           = new CKFinder();
$finder->BasePath = '/plugins/ckfinder/';    // The path for the installation of CKFinder (default = "/ckfinder/").
//$finder->SelectFunction = 'ShowFileInfo' ;
$finder->Create();
?>