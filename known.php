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

$search = isset($_GET['q']) ? $_GET['q'] : null;
$page = isset($_GET['p']) ? intval($_GET['p']) : 1;
$sort = isset($_GET['sort']) ? $_GET['sort'] : null;

require_once 'api/listid.php';
require_once 'shared/style.php';

$ids = uupListIds($search, $sort);
if(isset($ids['error'])) {
    fancyError($ids['error'], 'downloads');
    die();
}

if(!isset($ids['builds']) || empty($ids['builds'])) {
    fancyError('NO_BUILDS_IN_FILEINFO', 'downloads');
    die();
}

$ids = $ids['builds'];
$count = count($ids);

$perPage = 100;
$pages = ceil($count / $perPage);
$startItem = ($page - 1) * $perPage;

$prevPageUrl = ($page != 1) ? getUrlWithoutParam('p').'p='.$page - 1 : '';
$nextPageUrl = ($page != $pages) ? getUrlWithoutParam('p').'p='.$page + 1 : '';

if($page < 1 || $page > $pages) {
    fancyError('INVALID_PAGE', 'downloads');
    die();
}

$idsPaginated = array_splice($ids, $startItem, $perPage);

if($search != null) {
    $pageTitle = "$search - {$s['browseKnown']}";
    $htmlQuery = htmlentities($search);
} else {
    $pageTitle = $s['browseKnown'];
    $htmlQuery = '';
}

$dateSortChecked = $sort ? 'checked' : '';

$templateOk = true;

styleUpper('downloads', $pageTitle);
require 'templates/known.php';
styleLower();
