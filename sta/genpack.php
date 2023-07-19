<?php
require_once dirname(__FILE__).'/../api/get.php';
require_once dirname(__FILE__).'/main.php';

function get7ZipLocation() {
    if(PHP_OS == 'WINNT') {
        $z7z = realpath(dirname(__FILE__)).'/7za.exe';
        if(!file_exists($z7z)) return false;
    } else {
        exec('command -v 7z', $out, $errCode);
        if($errCode != 0) {
            return false;
        }
        $z7z = '7z';
    }

    return $z7z;
}

function generatePack($updateId) {
    $z7z = get7ZipLocation();
    $tmp = 'uuptmp';
    if(!file_exists($tmp)) mkdir($tmp);

    if(file_exists('packs/'.$updateId.'.json.gz')) {
        return 2;
    }

    if($updateId == 'd7ac1db8-230e-47df-8e6f-49fb976f6f5c') return 2;

    consoleLogger('Generating packs for '.$updateId.'...');
    $files = uupGetFiles($updateId, 0, 0);
    if(isset($files['error'])) {
        return 0;
    }

    $isku = $files['sku'];
    $ibld = $files['build'];
    $files = $files['files'];

    $filesKeys = array_keys($files);

    $filesToRead = array();
    $appsToRead = array();
    $aggregatedMetadata = preg_grep('/AggregatedMetadata/i', $filesKeys);

    if(!empty($aggregatedMetadata)) {
        sort($aggregatedMetadata);
        $checkFile = $aggregatedMetadata[0];

        if(!$files[$checkFile]['sha256']) {
            consoleLogger('Update is not SHA-256 capable!');
            return 0;
        }

        $url = $files[$checkFile]['url'];
        $loc = "$tmp/$checkFile";

        consoleLogger('Downloading aggregated metadata: '.$checkFile);
        downloadFile($url, $loc);
        if(!file_exists($loc)) {
            throwError('INFO_DOWNLOAD_ERROR');
        }

        consoleLogger('Unpacking aggregated metadata: '.$checkFile);
        exec("\"$z7z\" l -slt \"$loc\"", $out, $errCode);
        if($errCode != 0) {
            unlink($loc);
            throwError('7ZIP_ERROR');
        }

        $files = preg_grep('/Path = /', $out);
        $files = preg_replace('/Path = /', '', $files);
        $dataFiles = preg_grep('/DesktopTargetCompDB_.*_.*\.|ServerTargetCompDB_.*_.*\.|ModernPCTargetCompDB_.*\.|HolographicTargetCompDB_.*\./i', $files);
        if($ibld > 22557) {
            $dataFiles = preg_grep('/DesktopTargetCompDB_App_.*\.|ServerTargetCompDB_App_.*\./i', $dataFiles, PREG_GREP_INVERT);
            $dataApps = preg_grep('/DesktopTargetCompDB_App_.*\.|ServerTargetCompDB_App_.*\./i', $files);
		}
        unset($out);

        exec("\"$z7z\" x -o\"$tmp\" \"$loc\" -y", $out, $errCode);
        if($errCode != 0) {
            unlink($loc);
            throwError('7ZIP_ERROR');
        }
        unset($out);

        foreach($dataFiles as $val) {
            //consoleLogger('Unpacking info file: '.$val);

            if(preg_match('/.cab$/i', $val)) {
                exec("\"$z7z\" x -bb2 -o\"$tmp\" \"$tmp/$val\" -y", $out, $errCode);
                if($errCode != 0) {
                    unlink($loc);
                    throwError('7ZIP_ERROR');
                }

                $temp = preg_grep('/^-.*DesktopTargetCompDB_.*_.*\.|^-.*ServerTargetCompDB_.*_.*\.|^-.*ModernPCTargetCompDB_.*\.|^-.*HolographicTargetCompDB_.*\./i', $out);
                sort($temp);
                $temp = preg_replace('/^- /', '', $temp[0]);

                $filesToRead[] = preg_replace('/.cab$/i', '', $temp);
                unlink("$tmp/$val");
                unset($temp, $out);
            } else {
                $filesToRead[] = $val;
            }
        }

        if(!empty($dataApps)) foreach($dataApps as $val) {
            consoleLogger('Unpacking info file: '.$val);

            if(preg_match('/.cab$/i', $val)) {
                exec("\"$z7z\" x -bb2 -o\"$tmp\" \"$tmp/$val\" -y", $out, $errCode);
                if($errCode != 0) {
                    unlink($loc);
                    throwError('7ZIP_ERROR');
                }

                $temp = preg_grep('/^-.*DesktopTargetCompDB_App_.*\.|ServerTargetCompDB_App_.*\./i', $out);
                sort($temp);
                $temp = preg_replace('/^- /', '', $temp[0]);

                $appsToRead[] = preg_replace('/.cab$/i', '', $temp);
                unlink("$tmp/$val");
                unset($temp, $out);
            } else {
                $appsToRead[] = $val;
            }
        }

        unlink($loc);
        unset($loc, $checkFile, $checkEd, $dataFiles, $dataApps);
    } else {
        $dataFiles = preg_grep('/DesktopTargetCompDB_.*_.*\.|ServerTargetCompDB_.*_.*\.|ModernPCTargetCompDB\.|HolographicTargetCompDB\./i', $filesKeys);
        if($ibld > 22557) {
            $dataFiles = preg_grep('/DesktopTargetCompDB_App_.*\.|ServerTargetCompDB_App_.*\./i', $dataFiles, PREG_GREP_INVERT);
            $dataApps = preg_grep('/DesktopTargetCompDB_App_.*\.|ServerTargetCompDB_App_.*\./i', $filesKeys);
		}

        foreach($dataFiles as $val) {
            if(!$files[$val]['sha256']) {
                consoleLogger('Update is not SHA-256 capable!');
                return 0;
            }

            $url = $files[$val]['url'];
            $loc = "$tmp/$val";

            consoleLogger('Downloading info file: '.$val);
            downloadFile($url, $loc);
            if(!file_exists($loc)) {
                throwError('INFO_DOWNLOAD_ERROR');
            }

            if(preg_match('/.cab$/i', $val)) {
                exec("\"$z7z\" x -bb2 -o\"$tmp\" \"$tmp/$val\" -y", $out, $errCode);
                if($errCode != 0) {
                    unlink($loc);
                    throwError('7ZIP_ERROR');
                }

                $temp = preg_grep('/^-.*DesktopTargetCompDB_.*_.*\.|^-.*ServerTargetCompDB_.*_.*\.|^-.*ModernPCTargetCompDB\.|^-.*HolographicTargetCompDB\./i', $out);
                sort($temp);
                $temp = preg_replace('/^- /', '', $temp[0]);

                $filesToRead[] = preg_replace('/.cab$/i', '', $temp);
                unlink("$tmp/$val");
                unset($temp, $out);
            } else {
                $filesToRead[] = $val;
            }
        }

        if(!empty($dataApps)) foreach($dataApps as $val) {
            if(!$files[$val]['sha256']) {
                consoleLogger('Update is not SHA-256 capable!');
                return 0;
            }

            $url = $files[$val]['url'];
            $loc = "$tmp/$val";

            consoleLogger('Downloading info file: '.$val);
            downloadFile($url, $loc);
            if(!file_exists($loc)) {
                throwError('INFO_DOWNLOAD_ERROR');
            }

            if(preg_match('/.cab$/i', $val)) {
                exec("\"$z7z\" x -bb2 -o\"$tmp\" \"$tmp/$val\" -y", $out, $errCode);
                if($errCode != 0) {
                    unlink($loc);
                    throwError('7ZIP_ERROR');
                }

                $temp = preg_grep('/^-.*DesktopTargetCompDB_App_.*\.|ServerTargetCompDB_App_.*\./i', $out);
                sort($temp);
                $temp = preg_replace('/^- /', '', $temp[0]);

                $appsToRead[] = preg_replace('/.cab$/i', '', $temp);
                unlink("$tmp/$val");
                unset($temp, $out);
            } else {
                $appsToRead[] = $val;
            }
        }

        unset($loc, $checkEd, $dataFiles, $dataApps);
    }

    $optAppx = array();
    $langsEditions = array();
    $packages = array();
    foreach($filesToRead as $val) {
        $filNam = preg_replace('/\.xml.*/', '', $val);
        $file = $tmp.'/'.$val;
        $xml = simplexml_load_file($file);

        $lang = preg_replace('/.*DesktopTargetCompDB_.*_|.*ServerTargetCompDB_.*_/', '', $filNam);
        $edition = preg_replace('/.*DesktopTargetCompDB_|.*ServerTargetCompDB_|_'.$lang.'/', '', $filNam);
        if($isku == 189) {
            $lang = 'en-us';
            $edition = 'Lite';
        }
        if($isku == 135) {
            $lang = 'en-us';
            $edition = 'Holographic';
        }

        $lang = strtolower($lang);
        $edition = strtoupper($edition);

        foreach($xml->Packages->Package as $val) {
            foreach($val->Payload->PayloadItem as $PayloadItem) {
                $sha256 = bin2hex(base64_decode($PayloadItem['PayloadHash']));
                $packages[$lang][$edition][] = $sha256;
            }
        }

        if(@count($xml->AppX)) foreach($xml->AppX->AppXPackages->Package as $val) {
            foreach($val->Payload->PayloadItem as $PayloadItem) {
                $sha256 = bin2hex(base64_decode($PayloadItem['PayloadHash']));
                $packages[$lang][$edition][] = $sha256;
            }
        }

        if($ibld > 22557 && @count($xml->Features->Feature->Dependencies)) {
            foreach($xml->Features->Feature->Dependencies->Feature as $ftr) {
                if(isset($ftr['Group']) && ($ftr['Group'] == 'PreinstalledApps')) $optAppx[] = strtolower($ftr['FeatureID']);
            }
        }

        $packages[$lang][$edition] = array_unique($packages[$lang][$edition]);
        sort($packages[$lang][$edition]);

        unlink($file);
        unset($file, $xml, $name, $newName, $lang, $edition);
    }

    $appxOpt = array_flip($optAppx);
    $paks = array();
    if(isset($appsToRead)) foreach($appsToRead as $val) {
        $filNam = preg_replace('/\.xml.*/', '', $val);
        $file = $tmp.'/'.$val;
        $xml = simplexml_load_file($file);

        $lang = preg_replace('/.*DesktopTargetCompDB_.*_|.*ServerTargetCompDB_.*_/', '', $filNam);
        $edition = preg_replace('/.*DesktopTargetCompDB_|.*ServerTargetCompDB_|_'.$lang.'/', '', $filNam);

        $lang = strtolower($lang);
        $edition = strtoupper($edition);

        foreach($xml->Packages->Package as $ppi) {
            $pid = (string)$ppi['ID'];
            foreach($ppi->Payload->PayloadItem as $PayloadItem) {
                $sha256 = bin2hex(base64_decode($PayloadItem['PayloadHash']));
                $paks[$pid][] = $sha256;
            }
        }

        foreach($xml->Features->Feature as $ftr) {
            if($ftr['Type'] == 'MSIXFramework') {
                foreach($ftr->Packages->Package as $pkg) {
                    $chk = (string)$pkg['ID'];
                    $packages[$lang][$edition][] = $paks[$chk][0];
                }
                continue;
			}
            if(!isset($appxOpt[strtolower($ftr['FeatureID'])])) continue;
            foreach($ftr->Packages->Package as $pkg) {
                $chk = (string)$pkg['ID'];
                $packages[$lang][$edition][] = $paks[$chk][0];
            }
        }

        $packages[$lang][$edition] = array_unique($packages[$lang][$edition]);
        sort($packages[$lang][$edition]);

        unlink($file);
        unset($file, $xml, $sha256, $lang, $edition, $pid, $chk);
    }

    $removeFiles = scandir($tmp);
    foreach($removeFiles as $val) {
        if($val == '.' || $val == '..') continue;
        unlink($tmp.'/'.$val);
    }

    if(!file_exists('packs')) mkdir('packs');

    $success = file_put_contents(
        'packs/'.$updateId.'.json.gz',
        gzencode(json_encode($packages)."\n")
    );

    if($success) {
        consoleLogger('Successfully written generated packs.');
    } else {
        consoleLogger('An error has occured while writing generated packs to the disk.');
        return 0;
    }

    return 1;
}
