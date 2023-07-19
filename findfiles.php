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

$updateId = isset($_GET['id']) ? $_GET['id'] : null;
$search = isset($_GET['q']) ? $_GET['q'] : null;
$aria2 = isset($_GET['aria2']) ? $_GET['aria2'] : null;

require_once 'api/get.php';
require_once 'api/updateinfo.php';
require_once 'public/get.php';
require_once 'shared/style.php';
require_once 'shared/ratelimits.php';

if(!$updateId) {
    fancyError('UNSPECIFIED_UPDATE', 'downloads');
    die();
}

if(!checkUpdateIdValidity($updateId)) {
    fancyError('INCORRECT_ID', 'downloads');
    die();
}

$files = uupGetFiles($updateId, 0, 0, 2);
if(isset($files['error'])) {
    fancyError($files['error'], 'downloads');
    die();
}

$updateName = $files['updateName'];
$updateBuild = $files['build'];
$updateArch = $files['arch'];
$files = $files['files'];
$filesKeys = array_keys($files);

if($search != null) {
    $searchSafe = preg_quote($search, '/');
    if($searchSafe == "Windows KB") {
        $searchSafe = "Windows KB|SSU-";
        if($updateBuild > 21380) $searchSafe = "Windows KB|SSU-|DesktopDeployment|AggregatedMetadata";
    }
    if(preg_match('/^".*"$/', $searchSafe)) {
        $searchSafe = preg_replace('/^"|"$/', '', $searchSafe);
    } else {
        $searchSafe = str_replace(' ', '.*', $searchSafe);
    }

    $removeKeys = preg_grep('/.*'.$searchSafe.'.*/i', $filesKeys, PREG_GREP_INVERT);
    if($search == "Windows KB") {
        $removeKeys = array_merge($removeKeys, preg_grep('/Windows(10|11)\.0-KB.*-EXPRESS|SSU-.*-EXPRESS|SSU-.*?\.psf/i', $filesKeys));
        if($updateBuild < 17763) $removeKeys = array_merge($removeKeys, preg_grep('/Windows(10|11)\.0-KB.*_\d\.psf$/i', $filesKeys));
    }

    foreach($removeKeys as $value) {
        unset($files[$value]);
    }

    if(empty($files)) {
        fancyError('SEARCH_NO_RESULTS', 'downloads');
        die();
    }

    unset($removeKeys);
    $filesKeys = array_keys($files);
}

$urlBase = "getfile.php?id=$updateId";

if($aria2) {
    $urlBase = getBaseUrl()."/".$urlBase;
    header('Content-Type: text/plain');

    usort($filesKeys, 'sortBySize');
    foreach($filesKeys as $val) {
        echo "$urlBase&file=$val\n";
        echo '  out='.$val."\n";
        echo '  checksum=sha-1='.$files[$val]['sha1']."\n\n";
    }

    die();
}

$count = count($filesKeys);

$htmlQuery = '';
$pageTitle = sprintf($s['findFilesIn'], "$updateName $updateArch");

if($search != null) {
    $pageTitle = "$search - ".$pageTitle;
    $htmlQuery = htmlentities($search);
}

$sha1TextArea = '';
foreach($filesKeys as $val) {
    $sha1TextArea .= $files[$val]['sha1'].' *'.$val."\n";
}

$templateOk = true;

styleUpper('downloads', $pageTitle);
require 'templates/findfiles.php';
styleLower();
