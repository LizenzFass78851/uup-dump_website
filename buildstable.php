<?php
require_once 'api/listid.php';
require_once 'shared/style.php';

$buildsAvailable = 1;
$ids = uupListIds(null, 1);

if(isset($ids['error']) || !isset($ids['builds']) || empty($ids['builds'])) {
    die($s['error_SEARCH_NO_RESULTS']);
} else {
    $ids = $ids['builds'];
}

$templateOk = true;

require 'templates/buildstable.php';
