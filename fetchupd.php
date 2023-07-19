<?php
/*
Copyright 2021 whatever127

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

   http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/

$arch = isset($_GET['arch']) ? $_GET['arch'] : 'amd64';
$ring = isset($_GET['ring']) ? $_GET['ring'] : 'WIF';
$flight = isset($_GET['flight']) ? $_GET['flight'] : 'Active';
$build = isset($_GET['build']) ? $_GET['build'] : 16251;
$minor = isset($_GET['minor']) ? $_GET['minor'] : 0;
$sku = isset($_GET['sku']) ? $_GET['sku'] : 48;
$type = isset($_GET['type']) ? $_GET['type'] : 'Production';

require_once 'api/fetchupd.php';
require_once 'shared/style.php';
require_once 'shared/ratelimits.php';

$resource = hash('sha1', strtolower("fetch-$arch-$ring-$flight-$build-$minor-$sku-$type"));
if(checkIfUserIsRateLimited($resource)) {
    fancyError('RATE_LIMITED', 'downloads');
    die();
}

$fetchUpd = uupFetchUpd($arch, $ring, $flight, $build, $minor, $sku, $type, 1);
if(isset($fetchUpd['error'])) {
    fancyError($fetchUpd['error'], 'downloads');
    die();
}

$updateArray = $fetchUpd['updateArray'];

$templateOk = true;

styleUpper('downloads', $s['responseFromServer']);
require 'templates/fetchupd.php';
styleLower();
