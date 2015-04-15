<?php

/*
 * Project: simpleplan
 * File: config.inc.php
 * Date: 16:19:16 PM  Feb 20, 2013
 * Encoding: UTF-8
 * Version: 1.00
 * Copyright (C) Wang Yu Zhe <yuzhewong@gmail.com> 2013
 */

//FOR MASTER DB SERVER
if (defined('SAE_MYSQL_HOST_M'))
define('DB_MASTER_SERVER', SAE_MYSQL_HOST_M);
if (defined('SAE_MYSQL_USER'))
define('DB_MASTER_SERVER_USERNAME', SAE_MYSQL_USER);
if (defined('SAE_MYSQL_PASS'))
define('DB_MASTER_SERVER_PASSWORD', SAE_MYSQL_PASS);
if (defined('SAE_MYSQL_DB'))
define('DB_MASTER_DATABASE', SAE_MYSQL_DB);
if (defined('SAE_MYSQL_PORT'))
define('DB_MASTER_DATABASE_PORT', SAE_MYSQL_PORT);



//FOR STAGING DB SERVER TEST
define('DB_TEST_SERVER', 'localhost');
define('DB_TEST_SERVER_USERNAME', 'root');
define('DB_TEST_SERVER_PASSWORD', 'god');
define('DB_TEST_DATABASE', 'simpleplan');
define('DB_TEST_DATABASE_PORT', '3306');

/* * ******************* */
/* SERVER DIRECTORIES */
/* * ******************* */
// FS = Filesystem (physical)
// WS = Webserver (virtual)
// Files location
//FOR PRODUCTION
define('DIR_FS_DOCUMENT_ROOT','../');
define("DIR_WS_INCLUDES", DIR_FS_DOCUMENT_ROOT . 'includes/');
if (isset($_SERVER['HTTP_HOST']))
define('WEB_URL', $_SERVER['HTTP_HOST']);

define('DIR_WS_ROOT', DIR_FS_DOCUMENT_ROOT);
define('DIR_WS_FUNCTIONS', DIR_WS_INCLUDES . 'functions/');
define('DIR_WS_CLASSES', DIR_WS_INCLUDES . 'classes/');
define('DIR_TEMPLATE', DIR_FS_DOCUMENT_ROOT . 'template/');
define('DIR_STATIC', DIR_FS_DOCUMENT_ROOT . 'static/');

//FOR STAGING
define('TESTDIR_FS_DOCUMENT_ROOT', 'C:/Apache2.2/htdocs/sae/1/'); //FOR HOME
//    define('TESTDIR_FS_DOCUMENT_ROOT', 'C:/Service/htdocs/couyikuaier/' );//FOR OFFICE
define("TESTDIR_WS_INCLUDES", TESTDIR_FS_DOCUMENT_ROOT . 'includes/');
define('TESTWEB_URL', 'http://localhost/simpleplan/');

define('TESTDIR_WS_ROOT', TESTDIR_FS_DOCUMENT_ROOT);
define('TESTDIR_WS_FUNCTIONS', TESTDIR_WS_INCLUDES . 'functions/');
define('TESTDIR_WS_CLASSES', TESTDIR_WS_INCLUDES . 'classes/');
define('TESTDIR_TEMPLATE', TESTDIR_FS_DOCUMENT_ROOT . 'template/');
define('TESTDIR_STATIC', TESTDIR_FS_DOCUMENT_ROOT . 'static/');
?>
