<?php

/*
 * Project: simpleplan
 * File: header.inc.php
 * Date: 16:17:09 PM  Feb 20, 2013
 * Encoding: UTF-8
 * Version: 1.00
 * Copyright (C) Wang Yu Zhe <yuzhewong@gmail.com> 2013
 */
//$_GET['debug'] = 1;
if (isset($_GET['debug']) && $_GET['debug'] == 1) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
date_default_timezone_set('Asia/Shanghai');
// core config file
require 'config.inc.php';
require 'settings.inc.php';
// common functions
require 'functions/common.fun.inc.php';
// time counter
$_begin = getMicrotime();
//FOR AUTO LOAD NECESSARY CLASSES
function __autoload($class) {
//    $class = strtolower($class);
    foreach ($GLOBALS['settings']['classDirectories'] as $classPath) {
        $file = $classPath . $class . '.class.php';
        if (file_exists($file)) {
            require $file;
            break;
        }
    }
}
// Global classes
$GLOBALS['DB'] = new Database(true);
$GLOBALS['SS'] = new Session();
?>
