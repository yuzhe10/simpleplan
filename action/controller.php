<?php

/*
 * Project: simpleplan
 * File: controller.php
 * Date: 10:28:56 PM  Feb 21, 2013
 * Encoding: UTF-8
 * Version: 1.00
 * Copyright (C) Wang Yu Zhe <yuzhewong@gmail.com> 2013
 *  Desc: module selection & do some filters
 */
require '../includes/header.inc.php';
$module = !empty($_GET['module']) ? $_GET['module'] : NULL;
if($module !== NULL) {
    if(file_exists($module.'.php')) {
        if(!isset($_SESSION)) session_start();
        require $module.'.php';
    } else {
        exit('Wrong module');
    }
} else {
    exit('No module');
}
?>
