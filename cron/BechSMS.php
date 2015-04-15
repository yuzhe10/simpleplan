<?php

/*
 * Project: simpleplan
 * File: BechSMS.php
 * Date: 23:02:09  2013-3-24
 * Encoding: UTF-8
 * Version: 1.00
 * Copyright (C) Wang Yu Zhe <yuzhewong@gmail.com> 2013
 */

$akey = '10010'; //BechSMS平台上的accesskey,不是SAE平台上的accesskey 
$skey = 'df94984875784'; 
$bechsms = apibus::init("bechsms");
$code = $bechsms->sendmsg($akey,$skey,"13126975040","test for sae");
print_r($code); 
?>
