<?php
require_once 'shared/style.php';

$templateOk = true;

styleUpper('newbuild', $s['addNewBuild']);
require 'templates/newbuild.php';
styleLower();
