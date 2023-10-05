<?php
/*
Copyright 2022 eraseyourknees

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

require_once 'api/get.php';
require_once 'shared/utils.php';
require_once 'public/get.php';

class AutoDlConfig {
    private $files;

    private $sku;
    private $build;
    private $buildNum;
    private $arch;
    private $title;

    private $lang;
    private $edition;
    private $archiveName;

    private $url;
    private $appUrl;

    public function __construct(
        private $autoDl,
        private $updateId,
        private $usePack,
        private $desiredEdition,
        private $desiredEditionMixed,
        private $desiredVE
    ) {
        $this->setOfflineFilesOrDie();
        $this->setUpdateInfo();
        $this->setArchiveNames();
        $this->setUrlsForPacks();
    }

    private function setOfflineFilesOrDie() {
        $files = uupGetFiles(
            $this->updateId,
            $this->usePack,
            $this->desiredEditionMixed,
            2
        );

        if(isset($files['error'])) {
            fancyError($files['error'], 'downloads');
            die();
        }

        $this->files = $files;
    }

    private function setUpdateInfo() {
        $info = uupUpdateInfo($this->updateId, ignoreFiles: true);
        $info = @$info['info'];

        $this->sku = isset($info['sku']) ? $info['sku'] : 48;
        $this->build = isset($info['build']) ? $info['build'] : 'UNKNOWN';
        $this->arch = isset($info['arch']) ? $info['arch'] : 'UNKNOWN';
        $this->title = isset($info['title']) ? $info['title'] : 'UNKNOWN';

        $this->buildNum = uupApiBuildMajor($this->build);
    }

    private function setArchiveNames() {
        $usePack = $this->usePack;
        $desiredEditionMixed = $this->desiredEditionMixed;

        $build = $this->build;
        $arch = $this->arch;

        $lang = $usePack ? $usePack : 'all';

        if(is_array($desiredEditionMixed)) {
            $edition = count($desiredEditionMixed) == 1 ? strtolower($desiredEditionMixed[0]) : 'multi';
        } else {
            $edition = $desiredEditionMixed ? strtolower($desiredEditionMixed) : 'all';
        }

        if($edition == 'multi') {
            foreach($desiredEditionMixed as $val) {
                if(strtolower($val) == 'app' || strtolower($val) == 'app_moment') $edition = 'app';
            }
        }

        $id = substr($this->updateId, 0, 8);
        $this->archiveName = $edition == 'updateonly' ? "{$build}_{$arch}_updates_{$id}" : "{$build}_{$arch}_{$lang}_{$edition}_{$id}";

        $this->lang = $lang;
        $this->edition = $edition;
    }

    private function skipApps() {
        $desiredEditionMixed = $this->desiredEditionMixed;
        $appSkip = false;

        if(is_array($desiredEditionMixed)) {
            foreach($desiredEditionMixed as $val) {
                $edition = strtolower($val);
                if($edition == 'updateonly' || $edition == 'app' || $edition == 'app_moment') $appSkip = true;
            }
        } else {
            $edition = $desiredEditionMixed ? strtolower($desiredEditionMixed) : 'all';
            if($edition == 'updateonly' || $edition == 'app' || $edition == 'app_moment') $appSkip = true;
        }

        return $appSkip;
    }

    private function supportsApps() {
        $isBlocked = isUpdateBlocked($this->buildNum, $this->title);

        if($this->buildNum <= 22557 || $isBlocked || $this->skipApps())
            return false;

        $genPack = uupGetGenPacks($this->buildNum, $this->arch, $this->updateId);

        if(empty($genPack) || !isset($genPack['neutral']))
            return false;

        $isAPP = false;
        foreach(array_keys($genPack['neutral']) as $edition) {
            if($edition == 'APP') $isAPP = true;
        }

        return $isAPP;
    }

    private function setUrlsForPacks() {
        $updateId = $this->updateId;
        $usePack = $this->usePack;
        $desiredEdition = $this->desiredEdition;

        if(isset($_SERVER['HTTPS'])) {
            $url = 'https://';
        } else {
            $url = 'http://';
        }

        $url .=  $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
        $app = $url;

        $url .= '?id='.$updateId.'&pack='.$usePack.'&edition='.$desiredEdition.'&aria2=2';
        $app .= '?id='.$updateId.'&pack=neutral&edition=app&aria2=2';

        $this->url = $url;

        $supportsApps = $this->supportsApps();
        $this->appUrl = $supportsApps ? $app : null;
    }

    private function getMoreOptionsFromPost() {
        if(!isset($_GET['autodl'])) {
            $updates = isset($_POST['updates']) ? $_POST['updates'] : 0;
        } else {
            $updates = 1;
        }

        $cleanup = isset($_POST['cleanup']) ? $_POST['cleanup'] : 0;
        $netfx = isset($_POST['netfx']) ? $_POST['netfx'] : 0;
        $esd = isset($_POST['esd']) ? $_POST['esd'] : 0;

        $moreOptions = [];
        $moreOptions['updates'] = $updates;
        $moreOptions['cleanup'] = $cleanup;
        $moreOptions['netfx'] = $netfx;
        $moreOptions['esd'] = $esd;

        return $moreOptions;
    }

    private function isVeAvailable() {
        $supportsVE = areVirtualEditonsSupported($this->buildNum, $this->sku);
        $isApps = ($this->edition == 'app' || $this->edition == 'app_moment');

        return $supportsVE && !$isApps;
    }

    private function verifyVeAndDieOnError() {
        if(!$this->isVeAvailable()) {
            fancyError('VE_UNAVAILABLE', 'downloads');
            die();
        } else if (count($this->desiredVE) == 0) {
            fancyError('UNSPECIFIED_VE', 'downloads');
            die();
        }
    }

    private function createDownloadOnlyPackage() {
        $url = $this->url;
        $name = $this->archiveName;
        $app = $this->appUrl;

        createAria2Package($url, $name, $app);
    }

    private function createConversionPackage($isVe) {
        $url = $this->url;
        $name = $this->archiveName;
        $ve = $this->desiredVE;
        $opt = $this->getMoreOptionsFromPost();
        $app = $this->appUrl;

        if($isVe) {
            $this->verifyVeAndDieOnError();
        }

        createUupConvertPackage($url, $name, $isVe, $ve, $opt, $app);
    }

    public function createPackage() {
        $isConversion = $this->autoDl > 1;
        $isVe = $this->autoDl == 3 ? 1 : 0;

        if(!$isConversion) {
            $this->createDownloadOnlyPackage();
        } else {
            $this->createConversionPackage($isVe);
        }
    }
}
