<?php
$updateId = isset($argv[1]) ? $argv[1] : 'c2a1d787-647b-486d-b264-f90f3782cdc6';

require_once dirname(__FILE__).'/sta/main.php';
require_once dirname(__FILE__).'/sta/genpack.php';

consoleLogger(brand('get'));

if(!get7ZipLocation()) {
    throwError('NO_7ZIP');
}

if(!generatePack($updateId)) {
    throwError('PACKS_FAILED');
}
