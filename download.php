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
$usePack = isset($_GET['pack']) ? $_GET['pack'] : 0;
$desiredEdition = isset($_GET['edition']) ? $_GET['edition'] : 0;

require_once 'api/get.php';
require_once 'api/listlangs.php';
require_once 'api/listeditions.php';
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
    fancyError('UNSUPPORTED_COMBINATION', 'downloads');
    die();
}

if(!$usePack) {
    $url = "./findfiles.php?id=$updateId";

    header("Location: $url");
    echo "<h1>Moved to <a href=\"$url\">here</a>.";
    die();
}

if(is_array($desiredEdition)) {
    $desiredEditionMixed = $desiredEdition;
    $desiredEdition = implode(';', $desiredEdition);
} else {
    $desiredEditionMixed = explode(';', $desiredEdition);

    if(count($desiredEditionMixed) == 1)
        $desiredEditionMixed = $desiredEdition;
}

$desiredEdition = strtolower($desiredEdition);
$url = "./get.php?id=$updateId&pack=$usePack&edition=$desiredEdition";
$urlUpd = "./get.php?id=$updateId&pack=0&edition=updateOnly";

if($desiredEdition == 'wubfile' || $desiredEdition == 'updateonly') {
    header("Location: $url");
    echo "<h1>Moved to <a href=\"$url\">here</a>.";
    die();
}

$files = uupGetFiles($updateId, $usePack, $desiredEditionMixed, 2);
if(isset($files['error'])) {
    fancyError($files['error'], 'downloads');
    die();
}

$hasUpdates = $files['hasUpdates'];

$uSku = $files['sku'];
$build = explode('.', $files['build']);
$build = @$build[0];

$EditionIsApp = isAppEdition($desiredEditionMixed);

$disableVE = 0;
if($EditionIsApp || !areVirtualEditonsSupported($build, $uSku)) {
    $disableVE = 1;
}

$updateTitle = "{$files['updateName']} {$files['arch']}";
$updateArch = $files['arch'];
$files = $files['files'];

$totalSize = 0;
foreach($files as $file) {
    $totalSize += $file['size'];
}

$totalSize = readableSize($totalSize, 2);

if($usePack) {
    if(isset($s['lang_'.strtolower($usePack)])) {
        $selectedLangName = $s['lang_'.strtolower($usePack)];
    } else {
        $langs = uupListLangs($updateId);
        $langs = $langs['langFancyNames'];

        $selectedLangName = $langs[strtolower($usePack)];
    }
} else {
    $selectedLangName = $s['allLanguages'];
}

if($usePack && $desiredEdition) {
    $editions = uupListEditions($usePack, $updateId);
    $editions = $editions['editionFancyNames'];

    if(isset($editions[strtoupper($desiredEdition)])) {
        $selectedEditionName = $editions[strtoupper($desiredEdition)];
    } else {
        $fancyNames = [];
        foreach($desiredEditionMixed as $edition) {
            $fancyNames[] = $editions[strtoupper($edition)];
        }

        $selectedEditionName = implode(', ', $fancyNames);
    }
} else {
    $selectedEditionName = $s['allEditions'];
}

$filesKeys = array_keys($files);
$virtualEditions = array();

if(preg_grep('/^.*Core_.*\.esd/i', $filesKeys)) {
    $virtualEditions['CoreSingleLanguage'] = 'Home Single Language';
}

if(preg_grep('/^.*Professional_.*\.esd/i', $filesKeys)) {
    $virtualEditions['ProfessionalWorkstation'] = 'Pro for Workstations';
    $virtualEditions['ProfessionalEducation'] = 'Pro Education';
    $virtualEditions['Education'] = 'Education';
    $virtualEditions['Enterprise'] = 'Enterprise';
    $virtualEditions['ServerRdsh'] = 'Enterprise multi-session / Virtual Desktops';

    if($build >= 18277) {
        $virtualEditions['IoTEnterprise'] = 'IoT Enterprise';
    }
}

if(preg_grep('/^.*ProfessionalN_.*\.esd/i', $filesKeys)) {
    $virtualEditions['ProfessionalWorkstationN'] = 'Pro N for Workstations';
    $virtualEditions['ProfessionalEducationN'] = 'Pro Education N';
    $virtualEditions['EducationN'] = 'Education N';
    $virtualEditions['EnterpriseN'] = 'Enterprise N';
}

$dlOnly = ($EditionIsApp || $uSku == 189 || $uSku == 135);

$templateOk = true;

styleUpper('downloads', sprintf($s['summaryFor'], "$updateTitle, $selectedLangName, $selectedEditionName"));
require 'templates/download.php';
styleLower();
