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

require_once 'api/shared/utils.php';

function checkApi() {
    global $requiredApi;
    $apiVer = parseSemVer(uupApiVersion());
    $reqApi = parseSemVer($requiredApi);

    if($apiVer['major'] != $reqApi['major']) {
        fancyError('UNSUPPORTED_API');
        die();
    }

    if($apiVer['minor'] < $reqApi['minor']) {
        fancyError('UNSUPPORTED_API');
        die();
    }

    if($apiVer['minor'] == $reqApi['minor']) {
        if($apiVer['patch'] < $reqApi['patch']) {
            fancyError('UNSUPPORTED_API');
            die();
        }
    }
}

function parseSemVer($version) {
    $patchArray = explode('-', $version);
    $versionArray = explode('.', $patchArray[0]);
    if(isset($patchArray[1])) {
        $metadataArray = explode('+', $patchArray[1]);
    }

    $major = $versionArray[0];
    $minor = $versionArray[1];
    $patch = $versionArray[2];

    if(isset($metadataArray[0])) {
        $prerelease = $metadataArray[0];
    } else {
        $prerelease = null;
    }

    if(isset($metadataArray[1])) {
        $metadata = $metadataArray[1];
    } else {
        $metadata = null;
    }

    return array(
        'major' => $major,
        'minor' => $minor,
        'patch' => $patch,
        'prerelease' => $prerelease,
        'metadata' => $metadata,
    );
}

function checkUpdateIdValidity($updateId) {
    return preg_match(
        '/^[\da-fA-F]{8}-([\da-fA-F]{4}-){3}[\da-fA-F]{12}(_rev\.\d+)?$/',
        $updateId
    );
}

function getBaseUrl() {
    $baseUrl = '';
    if(isset($_SERVER['HTTPS'])) {
        $baseUrl .= 'https://';
    } else {
        $baseUrl .= 'http://';
    }

    $baseUrl .=  $_SERVER['HTTP_HOST'];
    return $baseUrl;
}

function getUrlWithoutParam($param = null) {
    $baseUrl = getBaseUrl();

    $params = '';
    $separator = '?';
    foreach($_GET as $key => $val) {
        if($key == $param) continue;
        $params .= $separator.$key.'='.$val;
        $separator = '&';
    }
    $params .= $separator;

    $shelf = explode('?', $_SERVER['REQUEST_URI']);
    $url = $baseUrl.$shelf[0].$params;

    return $url;
}

function locasort(&$data, $locale = 'en_US') {
    if(class_exists('Collator')) {
        $coll = new Collator($locale);
        return $coll->asort($data);
    } else {
        return asort($data);
    }
}

function readableSize($size, $round = 0) {
    $prefixes = array('', 'Ki', 'Mi', 'Gi', 'Ti', 'Pi', 'Ei', 'Zi', 'Yi');

    for($i = 0; $i < count($prefixes)-1; $i++) {
        if($size < 1024) break;
        $size /= 1024;
    }

    $size = round($size, $round);
    $prefix = $prefixes[$i];

    return "$size {$prefix}B";
}

function isUpdateBlocked($buildNum, $updateTitle) {
    $isCumulative = str_contains($updateTitle, 'Cumulative Update');
    $isServer = str_contains($updateTitle, 'Server');

    return $buildNum > 22557 && $isCumulative && !$isServer;
}

function areVirtualEditonsSupported($build, $sku) {
    $isServer = uupApiIsServer($sku);
    return $build >= 17107 && !$isServer;
}

function getDefaultLanguage() {
    $config = uupDumpApiGetConfig();
    return isset($config['test_lang']) ? $config['test_lang'] : 'en-us';
}

function getPageLanguageFromUrl() {
    $uri = $_SERVER['REQUEST_URI'];
    preg_match('/^\/(..-..)\//', $uri, $matches);
    return isset($matches[1]) ? $matches[1] : getDefaultLanguage();
}

function urlWithoutLang($lang) {
    $part = "/$lang/";
    $len = strlen($part);
    $uri = $_SERVER['REQUEST_URI'];
    return str_starts_with($uri, $part) ? substr($uri, $len-1) : $uri;
}
