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

require_once dirname(__FILE__).'/main.php';

function styleUpper($pageType = 'home', $subtitle = '') {
    global $websiteVersion, $s, $languageCoreSelectorModal;

    if($subtitle) {
        $title = sprintf($s['uupdumpSub'], $subtitle);
        $description = htmlentities(sprintf($s['uupDumpDescSub'], $subtitle));
        $subTitleOnly = htmlentities($subtitle);
    } else {
        $title = $s['uupdump'];
        $description = htmlentities($s['uupDumpDesc']);
        $subTitleOnly = htmlentities($s['uupdump']);
    }

    if($_SERVER['SERVER_NAME'] == '0.0.0.0') {
        //$domain = $_SERVER['REMOTE_ADDR'];
        $domain = substr_replace($_SERVER['HTTP_HOST'], '', -(strlen($_SERVER['SERVER_PORT'])+1));
    } else {
        $domain = $_SERVER['SERVER_NAME'];
    }

    $darkModeOptions = array(
        'expires' => time()+60*60*24*30,
        'path' => '/',
        'domain' => $domain,
        'secure' => isset($_SERVER['HTTPS']) ? true : false,
        'httponly' => true,
        'samesite' => 'Strict'
    );

    $enableDarkMode = -1;
    if(isset($_GET['dark'])) {
        switch($_GET['dark']) {
            case 0:
                setcookie('Dark-Mode', 0, $darkModeOptions);
                $enableDarkMode = 0;
                break;

            case 1:
                setcookie('Dark-Mode', 1, $darkModeOptions);
                $enableDarkMode = 1;
                break;

            default:
                setcookie('Dark-Mode', 0, $darkModeOptions);
                $enableDarkMode = -1;
                break;
        }
    } elseif(isset($_COOKIE['Dark-Mode'])) {
        switch($_COOKIE['Dark-Mode']) {
            case 0:
                setcookie('Dark-Mode', 0, $darkModeOptions);
                $enableDarkMode = 0;
                break;

            case 1:
                setcookie('Dark-Mode', 1, $darkModeOptions);
                $enableDarkMode = 1;
                break;

            default:
                setcookie('Dark-Mode', 0, $darkModeOptions);
                $enableDarkMode = -1;
                break;
        }
    }

    $baseUrl = getBaseUrl();
    $fullUrl = htmlentities(getBaseUrl().$_SERVER['REQUEST_URI']);
    $url = htmlentities(getUrlWithoutParam('dark'));

    $darkSwitch = <<<EOD
<a class="item light-mode-btn" href="{$url}dark=0">
    <i class="eye slash icon"></i>
    {$s['lightMode']}
</a>
<a class="item dark-mode-btn" href="{$url}dark=1">
    <i class="eye icon"></i>
    {$s['darkMode']}
</a>
EOD;

    $darkMode = '';
    if($enableDarkMode == 1) {
        $darkMode = '<link rel="stylesheet" href="/css/darkmode.css">'."\n";
    } elseif($enableDarkMode < 0) {
        $darkMode = '<style>@import url(\'/css/darkmode.css\') (prefers-color-scheme: dark);</style>';
    }

    switch ($pageType) {
        case 'home':
            $navbarLink = '<a class="active item" href="./"><i class="home icon"></i>'.$s['home'].'</a>'.
                          '<a class="item" href="known.php"><i class="download icon"></i>'.$s['downloads'].'</a>'.
                          '<a class="item" href="newbuild.php"><i class="plus circle icon"></i>'.$s['newBuild'].'</a>'.
                          '<a class="item" target=_blank href="https://git.uupdump.net/uup-dump/containment-zone/src/branch/master/FAQ.md"><i class="question circle icon"></i>'.$s['faq'].'</a>';
        break;

        case 'downloads':
            $navbarLink = '<a class="item" href="./"><i class="home icon"></i>'.$s['home'].'</a>'.
                          '<a class="active item" href="known.php"><i class="download icon"></i>'.$s['downloads'].'</a>'.
                          '<a class="item" href="newbuild.php"><i class="plus circle icon"></i>'.$s['newBuild'].'</a>'.
                          '<a class="item" target=_blank href="https://git.uupdump.net/uup-dump/containment-zone/src/branch/master/FAQ.md"><i class="question circle icon"></i>'.$s['faq'].'</a>';
        break;

        case 'newbuild':
            $navbarLink = '<a class="item" href="./"><i class="home icon"></i>'.$s['home'].'</a>'.
                          '<a class="item" href="known.php"><i class="download icon"></i>'.$s['downloads'].'</a>'.
                          '<a class="active item" href="newbuild.php"><i class="plus circle icon"></i>'.$s['newBuild'].'</a>'.
                          '<a class="item" target=_blank href="https://git.uupdump.net/uup-dump/containment-zone/src/branch/master/FAQ.md"><i class="question circle icon"></i>'.$s['faq'].'</a>';
        break;

        default:
            $navbarLink = '<a class="active item" href="./">'.$s['home'].'</a>';
        break;
    }

    $langSelect = '<a class="item" onClick="openLanguageSelector();"><i class="globe icon"></i>'.$s['currentLanguage'].'</a>';
    $sourceCodeLink = '<a class="item" target=_blank href="https://git.uupdump.net/uup-dump"><i class="code icon"></i>'.$s['sourceCode'].'</a>';
    $discordInvite = '<a class="item" target=_blank href="https://discord.gg/yVRbtb2"><i class="discord icon"></i>Discord</a>';

    $navbarRight = $langSelect.$darkSwitch.$sourceCodeLink.$discordInvite;
    $navbarMobile = $darkSwitch.$sourceCodeLink.$discordInvite.$langSelect;

    $iso639lang = preg_replace("/-.*/i", "", $s['code']);
    $title = htmlentities($title);

    echo <<<HTML
<!DOCTYPE html>
<html lang="$iso639lang">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta property="description" content="$description">

        <meta property="twitter:card" content="summary_large_image">
        <meta property="og:site_name" content="{$s['uupdump']}">
        <meta property="og:title" content="$subTitleOnly">
        <meta property="og:type" content="website">
        <meta property="og:description" content="$description">
        <meta property="og:image" content="$baseUrl/img/cover.png">
        <meta property="og:url" content="$fullUrl">

        <link rel="stylesheet" href="/css/semantic.min.css">
        <link rel="stylesheet" href="/css/style.css">

        <script src="/js/jquery.min.js"></script>
        <script src="/js/semantic.min.js"></script>

        <title>$title</title>

        <script>
            function openLanguageSelector() {
                $('.ui.modal.select-language').modal('show');
                $('.ui.sidebar').sidebar('hide');
            }

            function sidebar() {
                $('.ui.sidebar').sidebar('setting', 'transition', 'overlay');
                $('.ui.sidebar').sidebar('setting', 'mobileTransition', 'overlay');
                $('.ui.sidebar').sidebar('toggle');
            }
        </script>

        $darkMode
    </head>
    <body>
        <div class="ui sidebar inverted vertical menu">
            <div class="ui container">
                $navbarLink $navbarMobile
            </div>
        </div>
        <div class="pusher">
            <div class="page-header">
                <div class="ui title container">
                    <h1 title="{$s['uupdump']} v$websiteVersion">
                        <img src="/img/logo.svg" class="logo" alt="">{$s['uupdump']}
                    </h1>
                </div>

                <div class="ui one column grid page-header-menu">
                    <div class="ui attached secondary menu computer only column">
                        <div class="ui container">
                            $navbarLink
                            <div class="right menu">
                                $navbarRight
                            </div>
                        </div>
                    </div>
                    <div class="ui attached secondary menu mobile tablet only column">
                        <div class="ui container">
                            <a class="item" onClick="sidebar();"><i class="bars icon"></i>{$s['menu']}</a>
                        </div>
                    </div>
                </div>

                <div class="shadow"></div>
            </div>

            $languageCoreSelectorModal

            <div class="ui container">
HTML;
}

function styleLower() {
    global $websiteVersion, $s;
    $api = uupApiVersion();

    $config = uupDumpApiGetConfig();
    $renderText = '';
    if(isset($config['show_render_time']) && $config['show_render_time']) {
        $render = floor((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"])*1000);
        $renderText = "Page rendered in $render ms.";
    }

    $copyright = sprintf(
        $s['copyrightNew'],
        date('Y')
    );

    echo <<<HTML
                <div class="footer">
                    <div class="ui divider"></div>
                    <p><i>$renderText
                        <b>{$s['uupdump']}</b> v$websiteVersion
                        (<b>API</b> v$api)
                        $copyright
                        <span class="info">{$s['notAffiliated']}</span>
                    </i></p>
                </div>
            </div>
        </div>
    </body>
</html>
HTML;
}

function fancyError($errorCode = 'ERROR', $pageType = 'home', $moreText = 0) {
    global $s;

    $errorNumber = 500;
    switch ($errorCode) {
        case 'ERROR':
            $errorFancy = $s['error_ERROR'];
            break;
        case 'UNSUPPORTED_API':
            $errorFancy = $s['error_UNSUPPORTED_API'];
            break;
        case 'NO_FILEINFO_DIR':
            $errorFancy = $s['error_NO_FILEINFO_DIR'];
            break;
        case 'NO_BUILDS_IN_FILEINFO':
            $errorFancy = $s['error_NO_BUILDS_IN_FILEINFO'];
            break;
        case 'SEARCH_NO_RESULTS':
            $errorNumber = 400;
            $errorFancy = $s['error_SEARCH_NO_RESULTS'];
            break;
        case 'UNKNOWN_ARCH':
            $errorNumber = 400;
            $errorFancy = $s['error_UNKNOWN_ARCH'];
            break;
        case 'UNKNOWN_RING':
            $errorNumber = 400;
            $errorFancy = $s['error_UNKNOWN_RING'];
            break;
        case 'UNKNOWN_FLIGHT':
            $errorNumber = 400;
            $errorFancy = $s['error_UNKNOWN_FLIGHT'];
            break;
        case 'UNKNOWN_COMBINATION':
            $errorNumber = 400;
            $errorFancy = $s['error_UNKNOWN_COMBINATION'];
            break;
        case 'ILLEGAL_BUILD':
            $errorNumber = 400;
            $errorFancy = sprintf($s['error_ILLEGAL_BUILD'], 9841, PHP_INT_MAX-1);
            break;
        case 'ILLEGAL_MINOR':
            $errorNumber = 400;
            $errorFancy = $s['error_ILLEGAL_MINOR'];
            break;
        case 'NO_UPDATE_FOUND':
            $errorFancy = $s['error_NO_UPDATE_FOUND'];
            break;
        case 'XML_PARSE_ERROR':
            $errorFancy = $s['error_XML_PARSE_ERROR'];
            break;
        case 'EMPTY_FILELIST':
            $errorFancy = $s['error_EMPTY_FILELIST'];
            break;
        case 'NO_FILES':
            $errorNumber = 404;
            $errorFancy = $s['error_NO_FILES'];
            break;
        case 'NOT_FOUND':
            $errorNumber = 404;
            $errorFancy = $s['error_NOT_FOUND'];
            break;
        case 'MISSING_FILES':
            $errorFancy = $s['error_MISSING_FILES'];
            break;
        case 'NO_METADATA_ESD':
            $errorFancy = $s['error_NO_METADATA_ESD'];
            break;
        case 'UNSUPPORTED_LANG':
            $errorNumber = 400;
            $errorFancy = $s['error_UNSUPPORTED_LANG'];
            break;
        case 'UNSPECIFIED_LANG':
            $errorNumber = 400;
            $errorFancy = $s['error_UNSPECIFIED_LANG'];
            break;
        case 'UNSUPPORTED_EDITION':
            $errorNumber = 400;
            $errorFancy = $s['error_UNSUPPORTED_EDITION'];
            break;
        case 'UNSUPPORTED_COMBINATION':
            $errorNumber = 400;
            $errorFancy = $s['error_UNSUPPORTED_COMBINATION'];
            break;
        case 'NOT_CUMULATIVE_UPDATE':
            $errorNumber = 400;
            $errorFancy = $s['error_NOT_CUMULATIVE_UPDATE'];
            break;
        case 'UPDATE_INFORMATION_NOT_EXISTS':
            $errorFancy = $s['error_UPDATE_INFORMATION_NOT_EXISTS'];
            break;
        case 'KEY_NOT_EXISTS':
            $errorNumber = 400;
            $errorFancy = $s['error_KEY_NOT_EXISTS'];
            break;
        case 'UNSPECIFIED_UPDATE':
            $errorNumber = 400;
            $errorFancy = $s['error_UNSPECIFIED_UPDATE'];
            break;
        case 'INCORRECT_ID':
            $errorNumber = 400;
            $errorFancy = $s['error_INCORRECT_ID'];
            break;
        case 'RATE_LIMITED':
            $errorNumber = 429;
            $errorFancy = $s['error_RATE_LIMITED'];
            break;
        case 'UNSPECIFIED_VE':
            $errorNumber = 400;
            $errorFancy = $s['error_UNSPECIFIED_VE'];
            break;
        case 'VE_UNAVAILABLE':
            $errorNumber = 400;
            $errorFancy = $s['error_VE_UNAVAILABLE'];
            break;
        case 'INVALID_PAGE':
            $errorNumber = 400;
            $errorFancy = $s['error_INVALID_PAGE'];
            break;
        case 'WU_REQUEST_FAILED':
            $errorFancy = $s['error_WU_REQUEST_FAILED'];
            break;
        default:
            $errorFancy = "<i>{$s['errorNoMessage']}</i><br><br>$errorCode";
            break;
    }

    $safeError = htmlentities($errorFancy);

    if($moreText) {
        $safeError = $safeError.'<br>'.htmlentities($moreText);
    }

    http_response_code($errorNumber);
    header('Cache-Control: no-cache');

    if($errorNumber == 429) {
        header('Retry-After: 10');
    }

    styleUpper($pageType, $s['error']);

    echo <<<ERROR
<h3 class="ui centered header">
    <div class="content">
        <i class="fitted exclamation triangle icon"></i>&nbsp;
        {$s['requestNotSuccessful']}
    </div>
</h3>

<div class="ui negative icon message">
    <i class="remove circle icon"></i>
    <div class="content">
        <div class="header">{$s['error']}</div>
        <p>{$s['anErrorHasOccurred']}<br>
        $safeError</p>
    </div>
</div>
ERROR;

    styleLower();
}

function styleCluelessUserArm64Warn() {
    global $s;

    echo <<<INFO
<div class="ui negative small icon message">
    <i class="mobile alternate icon"></i>
        <div class="header">{$s['arm64Warning2023']}</div>
</div>

INFO;
}
