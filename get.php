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

//get parameters
$updateId = isset($_GET['id']) ? $_GET['id'] : null;
$simple = isset($_GET['simple']) ? $_GET['simple'] : 0;
$aria2 = isset($_GET['aria2']) ? $_GET['aria2'] : 0;
$renameScript = isset($_GET['renscript']) ? $_GET['renscript'] : 0;
$autoDl = isset($_GET['autodl']) ? $_GET['autodl'] : 0;
$usePack = isset($_GET['pack']) ? $_GET['pack'] : 0;
$desiredEdition = isset($_GET['edition']) ? $_GET['edition'] : 0;

//post parameters
$autoDl = isset($_POST['autodl']) ? $_POST['autodl'] : $autoDl;
$desiredVE = isset($_POST['virtualEditions']) ? $_POST['virtualEditions'] : array();

require_once 'api/get.php';
require_once 'api/updateinfo.php';
require_once 'public/get.php';
require_once 'shared/style.php';
require_once 'shared/ratelimits.php';
require_once 'shared/autodl.php';

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

$resource = hash('sha1', strtolower("get-$updateId"));
if(checkIfUserIsRateLimited($resource)) {
    fancyError('RATE_LIMITED', 'downloads');
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

if($autoDl && !$aria2) {
    $autoDlConfig = new AutoDlConfig(
        $autoDl,
        $updateId,
        $usePack,
        $desiredEdition,
        $desiredEditionMixed,
        $desiredVE
    );

    $autoDlConfig->createPackage();
    die();
}

$files = uupGetFiles($updateId, $usePack, $desiredEditionMixed, 1);

if($aria2) {
    header('Content-Type: text/plain');

    if(isset($files['error'])) {
        if($aria2 == 2) {
            echo '#UUPDUMP_ERROR:';
        } else {
            http_response_code(400);
        }

        echo $files['error'];
        die();
    }

    if($autoDl) {
        header('Content-Disposition: attachment; filename="aria2_script.txt"');
    }

    $files = $files['files'];
    $filesKeys = array_keys($files);

    usort($filesKeys, 'sortBySize');
    foreach($filesKeys as $val) {
        echo $files[$val]['url']."\n";
        echo '  out='.$val."\n";
        echo '  checksum=sha-1='.$files[$val]['sha1']."\n\n";
    }
    /*
    //add debugging information to the file
    echo "# --- BEGIN UUP DUMP DEBUG INFO ---\n";
    foreach($filesKeys as $val) {
        echo "#debug=";
        echo base64_encode($val);
        echo ":";
        echo base64_encode($files[$val]['debug']);
        echo "\n";
    }
    echo "# --- END UUP DUMP DEBUG INFO ---\n";
    */
    die();
}

if(isset($files['error'])) {
    if($files['error'] == 'EMPTY_FILELIST') {
        $oldError = $files['error'];
        $files = uupGetFiles($updateId, $usePack, $desiredEditionMixed, 2);
        if(isset($files['error'])) {
            $files['error'] = 'NOT_FOUND';
        } else {
            $files['error'] = $oldError;
        }
    }

    fancyError($files['error'], 'downloads');
    die();
}

$updateName = $files['updateName'];
$updateBuild = $files['build'];
$updateArch = $files['arch'];
$files = $files['files'];
$filesKeys = array_keys($files);

$request = explode('?', $_SERVER['REQUEST_URI'], 2);

if($renameScript) {
    if($renameScript == 2) {
        header('Content-Type: application/sh');
        header('Content-Disposition: attachment; filename="rename_script.sh"');

        echo "#!/bin/bash\n\n";
        foreach($filesKeys as $val) {
            echo 'mv "'.$files[$val]['uuid'].'" "'.$val."\" 2>/dev/null\n";
        }
        /*
        //add debugging information to the file
        echo "\n# --- BEGIN UUP DUMP DEBUG INFO ---\n";
        foreach($filesKeys as $val) {
            echo "#debug=";
            echo base64_encode($val);
            echo ":";
            echo base64_encode($files[$val]['debug']);
            echo "\n";
        }
        echo "# --- END UUP DUMP DEBUG INFO ---\n";
        */
        die();
    }

    header('Content-Type: application/cmd');
    header('Content-Disposition: attachment; filename="rename_script.cmd"');

    echo "@echo off\r\ncd /d \"%~dp0\"\r\n\r\n";
    foreach($filesKeys as $val) {
        echo "IF EXIST \"{$files[$val]['uuid']}\" ";
        echo 'RENAME "'.$files[$val]['uuid'].'" "'.$val."\"\r\n";
    }
    /*
    //add debugging information to the file
    echo "\n:: --- BEGIN UUP DUMP DEBUG INFO ---\n";
    foreach($filesKeys as $val) {
        echo "::debug=";
        echo base64_encode($val);
        echo ":";
        echo base64_encode($files[$val]['debug']);
        echo "\n";
    }
    echo ":: --- END UUP DUMP DEBUG INFO ---\n";
    */
    die();
}

if($simple) {
    header('Content-Type: text/plain');
    usort($filesKeys, 'sortBySize');
    foreach($filesKeys as $val) {
        echo $val."|".$files[$val]['sha1']."|".$files[$val]['url']."\n";
    }
    die();
}

$renameTextArea = "@echo off\n";
$renameTextArea .= "cd /d \"%~dp0\"\n";
foreach($filesKeys as $val) {
    $renameTextArea .= 'rename "'.$files[$val]['uuid'].'" "'.$val."\"\n";
}

$sha1TextArea = '';
foreach($filesKeys as $val) {
    $sha1TextArea .= $files[$val]['sha1'].' *'.$val."\n";
}

$templateOk = true;

styleUpper('downloads', sprintf($s['listOfFilesFor'], "$updateName $updateArch"));
require 'templates/get.php';
styleLower();
