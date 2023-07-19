<?php
$arch = isset($argv[1]) ? $argv[1] : 'amd64';
$ring = isset($argv[2]) ? $argv[2] : 'WIF';
$flight = isset($argv[3]) ? $argv[3] : 'Active';
$build = isset($argv[4]) ? intval($argv[4]) : 16251;
$minor = isset($argv[5]) ? intval($argv[5]) : 0;
$sku = isset($argv[6]) ? intval($argv[6]) : 48;
$type = isset($argv[7]) ? $argv[7] : 'Production';

require_once dirname(__FILE__).'/../api/fetchupd.php';
require_once dirname(__FILE__).'/main.php';

consoleLogger(brand('fetchupd'));
$fetchedUpdate = uupFetchUpd($arch, $ring, $flight, $build, $minor, $sku, $type);
if(isset($fetchedUpdate['error'])) {
    throwError($fetchedUpdate['error']);
}

foreach($fetchedUpdate['updateArray'] as $update) {
    echo $update['foundBuild'];
    echo '|';
    echo $update['arch'];
    echo '|';
    echo $update['updateId'];
    echo '|';
    echo $update['updateTitle'];
    echo '|';
    echo $update['fileWrite'];
    echo "\n";
}
?>
