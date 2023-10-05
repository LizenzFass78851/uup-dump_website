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

$updateId = isset($_GET['id']) ? $_GET['id'] : 0;
$selectedLang = isset($_GET['pack']) ? $_GET['pack'] : 0;

require_once 'api/listlangs.php';
require_once 'api/listeditions.php';
require_once 'api/updateinfo.php';
require_once 'shared/style.php';
require_once 'shared/utils.php';

if(!$updateId) {
    fancyError('UNSPECIFIED_UPDATE', 'downloads');
    die();
}

if(!checkUpdateIdValidity($updateId)) {
    fancyError('INCORRECT_ID', 'downloads');
    die();
}

if(!uupApiPacksExist($updateId)) {
    fancyError('UNSUPPORTED_LANG', 'downloads');
    die();
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

if(!isset($updateInfo['sku'])) {
    $uSku = 48;
} else {
    $uSku = $updateInfo['sku'];
}

$hiddenEditions = ['PPIPRO'];
$recommendedEditions = ['CORE', 'PROFESSIONAL'];

$build = isset($updateInfo['build']) ? $updateInfo['build'] : null;
$buildNum = uupApiBuildMajor($build);
$disableVE = 0;
if(!areVirtualEditonsSupported($buildNum, $uSku)) {
    $disableVE = 1;
}

$updateTitle = $updateTitle.' '.$updateArch;

if($selectedLang) {
    if(isset($s['lang_'.strtolower($selectedLang)])) {
        $selectedLangName = $s['lang_'.strtolower($selectedLang)];
    } else {
        $langs = uupListLangs($updateId);
        $langs = $langs['langFancyNames'];

        if(isset($langs[strtolower($selectedLang)])) {
            $selectedLangName = $langs[strtolower($selectedLang)];
        } else {
            $selectedLangName = strtolower($selectedLang);
        }
    }

    $editions = uupListEditions($selectedLang, $updateId);
    if(isset($editions['error'])) {
        fancyError($editions['error'], 'downloads');
        die();
    }
    $editions = $editions['editionFancyNames'];
    asort($editions);
} else {
    $editions = array();
    $selectedLangName = $s['allLangs'];
}

$editionsNum = count($editions);

$recommend = !array_diff($recommendedEditions, array_keys($editions));

if($editionsNum == 1) {
    if(isset($editions['APP']) || isset($editions['APP_MOMENT'])) $disableVE = 1;
} else {
    if(isset($editions['APP']) && isset($editions['APP_MOMENT'])) $disableVE = 1;
}

$templateOk = true;

styleUpper('downloads', sprintf($s['selectEditionFor'], "$updateTitle, $selectedLangName"));
require 'templates/selectedition.php';
styleLower();
