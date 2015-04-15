<?php

/*
 * Project: simpleplan
 * File: common.fun.inc.php
 * Date: 11:01:31 PM  Feb 21, 2013
 * Encoding: UTF-8
 * Version: 1.00
 * Copyright (C) Wang Yu Zhe <yuzhewong@gmail.com> 2013
 */

function getMicrotime() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}

function getIPAddress($getlast = false) {
    $ip = extractProxiedIP($getlast);
    if (!empty($ip)) {
        return $ip;
    }
    if (getenv('HTTP_CLIENT_IP')) {
        return getenv('HTTP_CLIENT_IP');
    }
    return $_SERVER['REMOTE_ADDR'];
}

function extractProxiedIP($getlast = false) {
    $forwardedAddr = extractAllProxiedIPs();
    if ($getlast) {
        $forwardedAddr = array_reverse($forwardedAddr);
    }
    return $forwardedAddr[0];
}

function extractAllProxiedIPs() {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $forwardedAddr = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ips = array();
        foreach ($forwardedAddr as $ip) {
            $ip = trim($ip);
            $long = ip2long($ip);
            if ($long > 0) {
                $ips[] = long2ip($long);
            }
        }
        return $ips;
    }
    return array();
}

function encrypt($originalPassword) {
    return sha1($originalPassword);
}

function localStoreMember($member) {
    $cookie = base64_encode(serialize($member));
    $expired_time = time() + 604800;
    setcookie('member', $cookie, $expired_time, '/');
    $_SESSION['member'] = $member;
}

?>
