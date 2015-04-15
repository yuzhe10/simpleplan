<?php

/*
 * Project: simpleplan
 * File: member.php
 * Date: 4:08:49 PM  Feb 23, 2013
 * Encoding: UTF-8
 * Version: 1.00
 * Copyright (C) Wang Yu Zhe <yuzhewong@gmail.com> 2013
 */
$result = array();
if (!empty($_GET['action'])) {
    switch ($_GET['action']) {
        case 'check':
            if (!empty($_SESSION['member'])) {
                $result['result'] = 1;
                $result['member'] = $_SESSION['member'];
            } elseif (isset($_COOKIE['member'])) {
                $_member = unserialize(base64_decode($_COOKIE['member']));
                $member = new Member();
                $member->setEmail($_member['Email']);
                $member->setPassword($_member['password']); // encrypted already
                $verifiedMember = $GLOBALS['SS']->get($member);
                if ($verifiedMember) {
                    $result['result'] = 1;
                    $_SESSION['member'] = $verifiedMember[0];
                    $result['member'] = $_SESSION['member'];
                } else {
                    $result['result'] = -3;
                }
            } else {
                $result['result'] = -4;
            }
            echo json_encode($result);
            break;
        case 'check_email':
            $member = new Member();
            $member->setEmail($_REQUEST['Email']);
            $_member = $GLOBALS['SS']->get($member);
            if ($_member) {
                $result['result'] = 1;
            } else {
                $result['result'] = -1;
            }
            echo json_encode($result);
            break;
        case 'reg':
            // verify params
            if (empty($_REQUEST['email']) || empty($_REQUEST['email']) || empty($_REQUEST['password'])) {
                $result['result'] = -1;
                echo json_encode($result);
                die();
            }
            // beta
//            if (strpos($_REQUEST['email'], '@gameloft.com') === FALSE && strpos($_REQUEST['email'], '@aliyun.com') === FALSE) {
//                $result['result'] = -3;
//                echo json_encode($result);
//                die();
//            }
            $member = new Member();
            $member->setUserName($_REQUEST['name']);
            $member->setEmail($_REQUEST['email']);
            // encrypt password
            $sha1 = encrypt($_REQUEST['password']);
            $member->setPassword($sha1);
            $member->setLastVisit(time());
            $ip = getIPAddress();
            $member->setLastIP($ip);
            $member->setRegIP($ip);
            $member->setRegDate(time());
            $UID = $GLOBALS['SS']->save($member);
            if ($UID) {
                $result['result'] = 1;
                $result['email'] = $_REQUEST['email'];
                $_member = $GLOBALS['SS']->get($member);
                $result['member'] = $_member[0];
                localStoreMember($_member[0]);
                echo json_encode($result);
            } else {
                $result['result'] = -4;
                echo json_encode($result);
            }
            break;
        case 'logout':
            session_unset();
            session_destroy();
            setcookie(session_name(), '', 1, '/');
            setcookie('member', '', 1, '/');
            echo json_encode(array('result' => 1));
            break;
        case 'log':
            // verify params
            if (empty($_REQUEST['email']) || empty($_REQUEST['password'])) {
                $result['result'] = -3;
                echo json_encode($result);
                exit();
            }
            $member = new Member();
            $member->setEmail($_REQUEST['email']);
            $sha1 = encrypt($_REQUEST['password']);
            $member->setPassword($sha1);
            $_member = $GLOBALS['SS']->get($member);
            if ($_member) {
                // update last visit
                $updateMember = new Member();
                $updateMember->setUID($_member[0]['UID']);
                $updateMember->setLastVisit(time());
                $updateMember->setLastIP(getIPAddress());
                if ($GLOBALS['SS']->update($updateMember, 'UID')) {
                    // store member info to cookie and session
                    localStoreMember($_member[0]);
                    $result['member'] = $_member[0];
                    $result['result'] = 1;
                } else {
                    $result['result'] = -4;
                }
            } else {
                $result['result'] = -1;
            }
            echo json_encode($result);
            break;
        case 'update_member':
            // verify params
            if (empty($_REQUEST['profileUserName'])) {
                $result['result'] = -1;
                echo json_encode($result);
                exit();
            }
            $member = new Member();
            $member->setEmail($_SESSION['member']['Email']);
            $_member = $GLOBALS['SS']->get($member);
            if ($_member) {
                if (!empty($_REQUEST['newPassword'])) {
                    if (encrypt($_REQUEST['originalPassword']) != $_member[0]['Password']) {
                        $result['result'] = -4;
                        echo json_encode($result);
                        exit();
                    }
                    $member->setPassword(encrypt($_REQUEST['newPassword']));
                }
                $member->setUserName($_REQUEST['profileUserName']);
                $member->setMSISDN($_REQUEST['profileMSISDN']);
                if ($GLOBALS['SS']->update($member, 'Email')) {
                    $updatedMember = $GLOBALS['SS']->get($member);
                    $result['member'] = $updatedMember[0];
                    localStoreMember($updatedMember[0]);
                    $result['result'] = 1;
                } else {
                    $result['result'] = -5;
                }
            } else {
                $result['result'] = -3;
            }
            echo json_encode($result);
            break;
        default:
            break;
    }
} else {
    $result['result'] = -2;
    echo json_encode($result);
}
?>
