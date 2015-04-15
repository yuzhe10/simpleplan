<?php

/*
 * Project: simpleplan
 * File: Mail.php
 * Date: 23:24:08  2013-3-24
 * Encoding: UTF-8
 * Version: 1.00
 * Copyright (C) Wang Yu Zhe <yuzhewong@gmail.com> 2013
 */
require '../includes/header.inc.php';

/**
 * Load undone plans
 */
$plan = new Plan();
$plan->setDone(FALSE);
$plan->setRecycle(FALSE);
$plan->addSort('Order');
$plan->setBy(Plan::SORT_TYPE_ASC);
$_plans = $GLOBALS['SS']->get($plan);
/**
 * Init mail
 */
$options = array(
    'from' => 'Simple Plan Service <yuzhe_wang@sina.cn>',
    'smtp_username' => 'yuzhe_wang',
    'smtp_host' => 'smtp.sina.cn',
    'smtp_password' => 'wxy475133',
    'smtp_port' => 25,
    'content_type' => 'HTML',
);
$mail = new SaeMail($options);
/**
 * Report to member
 */
$member = new Member();
$index = 0;
for($i = 0; $i < count($_plans); $i++) {
    $_uid = $_plans[$index]['UID'];
    $member->setUID($_uid);
    $_member = $GLOBALS['SS']->get($member);
    if ($_member) {
        $number = 1;
        $_content = '<dl>';
        foreach ($_plans as $key => $__plan) {
            if ($_uid == $__plan['UID']) {
                // Title
                $title = '<dt><h3 style="background-color: rgb(0, 94, 172); color: rgb(255, 255, 255);" >'.$number++.'.'.$__plan['Title'].'   优先级:'.$__plan['Order'].'</h3></dt>';
                $_content .= $title;
                // Plan progress info
                $progressInfo = '';
                if($__plan['ETA'] > 0) {
                    $progressInfo .= '<dd><h4 style="background-color: rgb(66, 204, 255); color: rgb(0, 0, 0);" >';
                    $progressInfo .= '预计'.date('Y-m-d', $__plan['ETA']).'完成计划 | ';
                    $progressInfo .= getRemainingInfo($__plan['ETA']);
                    if($__plan['Spend'] > 0) {
                        $spend = getTimesByElapsed($__plan['Spend']);
                        $progressInfo .= '目前累计用时'.$spend['days'].'天'.$spend['hours'].'小时'.$spend['minutes'].'分钟 | ';;
                    }
                    $progressInfo .= '</h5></dd>';
                }
                $_content .= $progressInfo;
                // Note
                $_content .= '<dd>';
                $_content .= $__plan['Note'];
                $_content .= '</dd>';
                unset($_plans[$key]);
            } else {
                $index = $key;
            }
        }
        $_content .= '</dl>';
        $_content .= '<span style="font-size: 12.5px; color: #000000; font-family: Tahoma;">
			Best Regards,<br />
                    </span>
                    <table>
                            <tr>
                            <td style="border: 0px; padding-right: 100px;" nowrap>
                                    <span style="font-weight: bold; font-size: 14px; color: #000000; font-family: Tahoma">
                                            <a target="_blank" href="http://simpleplan.sinaapp.com/">Simple Plan Service</a><br />
                                    </span>
                                    <span style="font-size: 12.5px; color: #000000; font-family: Tahoma;">
                                            Skype:mjwxy@yahoo.cn<br />
                                    </span>
                                    <div style="padding-top:4px; font-size: 10.5px; color: #000000; font-family: Tahoma;">
                                            Copyright (C) Wang Yu Zhe <yuzhewong@gmail.com> 2013<br />
                                    </div>
                            </td>
                            </tr>
                    </table>
                    <hr style="border: 0px; border-bottom: 1px solid #AAAAAA;" />';
        $to = $_member[0]['Email'];
        $_title = 'SimplePlan-待完成的计划';
        $opt = array(
            'to' => $to,
            'subject' => $_title,
            'content' => $_content
        );
//        print_r($opt);
        $mail->setOpt($opt);
        $ret = $mail->send();
        if ($ret === false) {
            var_dump($mail->errno(), $mail->errmsg());
        } else {
            echo $_member[0]['UID'] . '|';
        }
    }
}

function getRemainingInfo($ETA) {
    if (!is_numeric($ETA)) {
        return false;
    }
    $secsRemaining = $ETA - time();
    if ($secsRemaining >= 0) {
        $times = getTimesByElapsed($secsRemaining);
        return '离原计划还有'.$times['days'].'天'.$times['hours'].'小时'.$times['minutes'].'分钟 | ';
    } else {
        $times = getTimesByElapsed(-$secsRemaining);
        return '超过预期'.$times['days'].'天'.$times['hours'].'小时'.$times['minutes'].'分钟 | ';
    }
}

function getTimesByElapsed($elapsed) {
    $secPerMin = 60;
    $secPerHour = $secPerMin * 60;
    $secPerDay = $secPerHour * 24;
    $days = round($elapsed / $secPerDay);
    $hours = round(($elapsed % $secPerDay) / $secPerHour);
    $minutes = round((($elapsed % $secPerDay) % $secPerHour) / $secPerMin);
    return array('days'=>$days,'hours'=>$hours,'minutes'=>$minutes);
}
?>
