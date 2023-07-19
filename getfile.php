<?php
/*
Copyright 2019 whatever127

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

$updateId = isset($_GET['id']) ? $_GET['id'] : null;
$file = isset($_GET['file']) ? $_GET['file'] : null;
$aria2 = isset($_GET['aria2']) ? $_GET['aria2'] : 0;

if(empty($updateId)) die('Unspecified update id');
if(empty($file)) die('Unspecified file');

require_once 'api/get.php';
require_once 'shared/style.php';
require_once 'shared/ratelimits.php';

if(!checkUpdateIdValidity($updateId)) {
    fancyError('INCORRECT_ID', 'downloads');
    die();
}

$resource = hash('sha1', strtolower("getfile-$updateId-$file"));
if(checkIfUserIsRateLimited($resource, 5, 1)) {
    fancyError('RATE_LIMITED', 'downloads');
    die();
}

$files = uupGetFiles($updateId, 0, 0, 1);
if(isset($files['error'])) {
    $resource = hash('sha1', strtolower("get-$updateId-failed"));
    checkIfUserIsRateLimited($resource, 0, 0);

    fancyError($files['error'], 'downloads');
    die();
}

$updateName = $files['updateName'];
$updateArch = $files['arch'];
$files = $files['files'];

if(!isset($files[$file]['url'])) {
    fancyError('NO_FILES', 'downloads', $file);
    die();
}

$files[$file]['url'] = uupApiFixDownloadLink($files[$file]['url']);

if($aria2) {
    header('Content-Type: text/plain');
    echo $files[$file]['url']."\n";
    echo '  out='.$file."\n";
    echo '  checksum=sha-1='.$files[$file]['sha1']."\n\n";
    die();
}

if(!str_contains($_SERVER['HTTP_USER_AGENT'], 'Mozilla')) {
    header('Location: '.$files[$file]['url']);
    echo '<h1>Moved to <a href="'.htmlentities($files[$file]['url']).'">here</a>.';
    die();
}

$filesKeys = [$file];
$skipTextBoxes = true;
$templateOk = true;

styleUpper('downloads', sprintf($s['listOfFilesFor'], "$updateName $updateArch"));
require 'templates/get.php';
styleLower();
