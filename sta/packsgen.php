<?php

require_once dirname(__FILE__).'/../api/listid.php';
require_once dirname(__FILE__).'/main.php';
require_once dirname(__FILE__).'/genpack.php';

consoleLogger(brand('listid'));
$ids = uupListIds();
if(isset($ids['error'])) {
    throwError($ids['error']);
}

foreach($ids['builds'] as $val) {
    generatePack($val['uuid']);
}
?>
