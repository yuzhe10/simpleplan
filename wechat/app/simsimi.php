<?php

/*
 * Project: simpleplan
 * File: simsimi.php
 * Date: 21:56:25  2013-11-4
 * Encoding: UTF-8
 * Version: 1.00
 * Copyright (C) Wang Yu Zhe <yuzhewong@gmail.com> 2013
 */

function callSimsimi($keyword) {
    $params['key'] = "64f5e557-0f2a-436d-ac44-3c096b110844";
    $params['lc'] = "ch";
    $params['ft'] = "1.0";
    $params['text'] = $keyword;

    $url = "http://sandbox.api.simsimi.com/request.p?" . http_build_query($params);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);

    $message = json_decode($output, true);
    $result = "";
    if ($message['result'] == 100) {
        $result = $message['response'];
    } else {
        $result = $message['result'] . "-" . $message['msg'];
    }
    return $result;
}

?>
