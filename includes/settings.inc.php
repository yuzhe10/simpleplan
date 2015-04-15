<?php

/*
 * Project: simpleplan
 * File: settings.inc.php
 * Date: 10:40:51 PM  Feb 21, 2013
 * Encoding: UTF-8
 * Version: 1.00
 * Copyright (C) Wang Yu Zhe <yuzhewong@gmail.com> 2013
 */

$settings = array();
//Inital settings
$settings['siteRoot'] = DIR_FS_DOCUMENT_ROOT; // production
$settings['dataBase'] = array('server' => DB_MASTER_SERVER . ':' . DB_MASTER_DATABASE_PORT, 'username' => DB_MASTER_SERVER_USERNAME, 'password' => DB_MASTER_SERVER_PASSWORD, 'database' => DB_MASTER_DATABASE);
if ($_SERVER['HTTP_HOST'] == 'localhost') {
    $settings['siteRoot'] = TESTDIR_FS_DOCUMENT_ROOT; // test
    $settings['dataBase'] = array('server' => DB_TEST_SERVER, 'username' => DB_TEST_SERVER_USERNAME, 'password' => DB_TEST_SERVER_PASSWORD, 'database' => DB_TEST_DATABASE);
}
$settings['includesPath'] = $settings['siteRoot'] . 'includes/';
$settings['classPath'] = $settings['includesPath'] . 'classes/';
$settings['functionPath'] = $settings['includesPath'] . 'functions/';
$settings['textPath'] = $settings['includesPath'] . 'text.ini.php';
$settings['staticPath'] = $settings['siteRoot'] . 'static/';
$settings['imgPath'] = $settings['staticPath'] . 'images/';
$settings['JSPath'] = $settings['staticPath'] . 'js/';
$settings['CSSPath'] = $settings['staticPath'] . 'css/';
$settings['templatePath'] = $settings['siteRoot'] . 'template/';
$settings['daoPath'] = $settings['siteRoot'] . 'dao/';
$settings['entityPath'] = $settings['siteRoot'] . 'entity/';
//Regular settings
$settings['utf8'] = true;
$settings['autoconnect'] = false;
$settings['classDirectories'] = array($settings['classPath'], $settings['daoPath'], $settings['entityPath']);
$GLOBALS['settings'] = $settings;
?>
