<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'collection_config.inc.php';

function collection_show() {
    require __DIR__ . DIRECTORY_SEPARATOR . 'show_collection.inc.php';
}

function collection_edit() {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection.inc.php';
}

function collection_edit_setup() {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_setup.inc.php';
}