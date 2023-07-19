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

require_once dirname(__FILE__).'/../public/langs/en-us.php';
$lang = 'en-us';

if($_SERVER['SERVER_NAME'] == '0.0.0.0') {
    //$domain = $_SERVER['REMOTE_ADDR'];
    $domain = substr_replace($_SERVER['HTTP_HOST'], '', -(strlen($_SERVER['SERVER_PORT'])+1));
} else {
    $domain = $_SERVER['SERVER_NAME'];
}

$pageLanguageOptions = array(
    'expires' => time()+60*60*24*30,
    'path' => '/',
    'domain' => $domain,
    'secure' => isset($_SERVER['HTTPS']) ? true : false,
    'httponly' => true,
    'samesite' => 'Strict'
);

$sendCookie = false;
if(isset($_GET['lang'])) {
    $lang = strtolower($_GET['lang']);
    $sendCookie = true;
} elseif(isset($_COOKIE['Page-Language'])) {
    $lang = strtolower($_COOKIE['Page-Language']);
    $sendCookie = true;
}

$supportedLangs = array(
    'en-us',
    'pl-pl',
    'de-de',
    'pt-br',
    'pt-pt',
    'nl-nl',
    'fr-fr',
    'zh-cn',
    'ja-jp',
    'es-ar',
    'it-it',
    'ar-sa',
    'ko-kr',
    'zh-tw',
    'hu-hu',
    'tr-tr',
    'ro-ro',
    'ru-ru',
);

if(!in_array("$lang", $supportedLangs)) {
    $lang = 'en-us';
}

require_once "public/langs/$lang.php";

if($sendCookie) {
    setcookie('Page-Language', $lang, $pageLanguageOptions);
}

$url = htmlentities(getUrlWithoutParam('lang'));
$languageCoreSelectorModal = <<<EOD
<div class="ui normal mini modal select-language">
    <div class="header">
        {$s['selectLanguage']}
    </div>
    <div class="content">
        <p><a href="{$url}lang=de-de"><i class="de flag"></i>Deutsch</a></p>
        <p><a href="{$url}lang=en-us"><i class="us flag"></i>English (United States)</a></p>
        <p><a href="{$url}lang=es-ar"><i class="ar flag"></i>Español (Argentina)</a></p>
        <p><a href="{$url}lang=fr-fr"><i class="fr flag"></i>Français (France)</a></p>
        <p><a href="{$url}lang=it-it"><i class="it flag"></i>Italiano</a></p>
        <p><a href="{$url}lang=hu-hu"><i class="hu flag"></i>Magyar</a></p>
        <p><a href="{$url}lang=nl-nl"><i class="nl flag"></i>Nederlands</a></p>
        <p><a href="{$url}lang=pl-pl"><i class="pl flag"></i>polski</a></p>
        <p><a href="{$url}lang=pt-br"><i class="br flag"></i>Português (Brasil)</a></p>
        <p><a href="{$url}lang=pt-pt"><i class="pt flag"></i>Português (Portugal)</a></p>
        <p><a hreF="{$url}lang=ru-ru"><i class="ru flag"></i>Русский</a></p>
        <p><a href="{$url}lang=ro-ro"><i class="ro flag"></i>Română (România)</a></p>
        <p><a href="{$url}lang=tr-tr"><i class="tr flag"></i>Türkçe</a></p>
        <p><a href="{$url}lang=ar-sa"><i class="sa flag"></i>العربية</a></p>
        <p><a href="{$url}lang=ko-kr"><i class="kr flag"></i>한국어</a></p>
        <p><a href="{$url}lang=zh-cn"><i class="cn flag"></i>中文（简体）</a></p>
        <p><a href="{$url}lang=ja-jp"><i class="jp flag"></i>日本語</a></p>
        <p><a href="{$url}lang=zh-tw"><i class="tw flag"></i>中文（繁體）</a></p>
    </div>
    <div class="actions">
        <div class="ui ok button">
            <i class="close icon"></i>
            {$s['cancel']}
        </div>
    </div>
</div>

EOD;

$s['currentLanguage'] = $s["lang_$lang"];
date_default_timezone_set($s['timeZone']);

unset($url, $sendCookie, $supportedLangs, $pageLanguageOptions);
