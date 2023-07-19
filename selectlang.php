<?php
/*
Copyright 2020 whatever127

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

$updateId = isset($_GET['id']) ? $_GET['id'] : 0;
$getPacks = isset($_GET['packs']) ? $_GET['packs'] : 0;

require_once 'api/listlangs.php';
require_once 'api/updateinfo.php';
require_once 'shared/style.php';
require_once 'shared/utils.php';
require_once dirname(__FILE__).'/sta/main.php';
require_once dirname(__FILE__).'/sta/genpack.php';

function getLangs($updateId, $s) {
    $langs = uupListLangs($updateId);
    $langsTemp = array();

    foreach($langs['langList'] as $lang) {
        if(isset($s["lang_$lang"])) {
            $langsTemp[$lang] = $s["lang_$lang"];
        } else {
            $langsTemp[$lang] = $langs['langFancyNames'][$lang];
        }
    }

    $langs = $langsTemp;
    locasort($langs, $s['code']);

    return $langs;
}

if(!$updateId) {
    fancyError('UNSPECIFIED_UPDATE', 'downloads');
    die();
}

if(!checkUpdateIdValidity($updateId)) {
    fancyError('INCORRECT_ID', 'downloads');
    die();
}

if($getPacks) {
    generatePack($updateId);
}

$updateInfo = uupUpdateInfo($updateId, ignoreFiles: true);
$updateInfo = isset($updateInfo['info']) ? $updateInfo['info'] : array();

if(!isset($updateInfo['title'])) {
    $updateTitle = 'Unknown update: '.$updateId;
} else {
    $updateTitle = $updateInfo['title'];
}

if(!isset($updateInfo['arch'])) {
    $updateArch = '';
} else {
    $updateArch = $updateInfo['arch'];
}

if(!isset($updateInfo['build'])) {
    $build = $s['unknown'];
    $buildNum = false;
} else {
    $build = $updateInfo['build'];
    $buildNum = @explode('.', $build)[0];
}

if(!isset($updateInfo['ring'])) {
    $ring = null;
} else {
    $ring = $updateInfo['ring'];
}

if(!isset($updateInfo['flight'])) {
    $flight = null;
} else {
    $flight = $updateInfo['flight'];
}

if(!isset($updateInfo['created'])) {
    $created = null;
} else {
    $created = $updateInfo['created'];
}

$updateTitle = $updateTitle.' '.$updateArch;

$updateBlocked = isUpdateBlocked($buildNum, $updateTitle);
$langs = $updateBlocked ? [] : getLangs($updateId, $s);

if(in_array(strtolower($s['code']), array_keys($langs))) {
    $defaultLang = strtolower($s['code']);
} else {
    $defaultLang = 'en-us';
}

//Set fancy name for channel and flight of build
if($ring == 'CANARY' && $flight == 'Active') {
    $fancyChannelName = $s['channel_canary'];
} elseif($ring == 'WIF' && $flight == 'Skip') {
    $fancyChannelName = $s['channel_skipAhead'];
} elseif($ring == 'WIF' && $flight == 'Active') {
    $fancyChannelName = $s['channel_dev'];
} elseif($ring == 'WIS' && $flight == 'Active') {
    $fancyChannelName = $s['channel_beta'];
} elseif($ring == 'RP' && $flight == 'Current') {
    $fancyChannelName = $s['channel_releasepreview'];
} elseif($ring == 'RETAIL') {
    $fancyChannelName = $s['channel_retail'];
} else {
    if($ring && $flight) {
        $fancyChannelName = "$ring, $flight";
    } elseif($ring) {
        $fancyChannelName = "$ring";
    } else {
        $fancyChannelName = $s['unknown'];
    }
}

$findFilesUrl = "findfiles.php?id=".htmlentities($updateId);

$langsAvailable = count($langs) > 0;
$packsAvailable = uupApiPacksExist($updateId);

$noLangsIcon = 'times circle outline';
$noLangsCause = $s['updateIsBlocked'];
$generatePacksButton = false;

if(!$packsAvailable) {
    $noLangsIcon = 'industry';
    $noLangsCause = 'Metadata for this update is not generated.';
    $updateBlocked = true;
	$generatePacksButton = true;
} else if(!$updateBlocked && !$langsAvailable) {
    $noLangsIcon = 'info';
    $noLangsCause = $s['noLangsAvailable'];
    $updateBlocked = true;
}

$templateOk = true;

styleUpper('downloads', sprintf($s['selectLangFor'], $updateTitle));
require 'templates/selectlang.php';
styleLower();
