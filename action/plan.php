<?php

/*
 * Project: simpleplan
 * File: plan.php
 * Date: 15:29:34 PM  Feb 28, 2013
 * Encoding: UTF-8
 * Version: 1.00
 * Copyright (C) Wang Yu Zhe <yuzhewong@gmail.com> 2013
 */
$result = array();
if (!empty($_GET['action'])) {
    switch ($_GET['action']) {
        case 'add':
            $category = new Category();
            $category->setName($_REQUEST['category']);
            $category->setUID($_SESSION['member']['UID']);
            $_category = $GLOBALS['SS']->get($category);
            $plan = new Plan();
            $plan->setTitle($_REQUEST['title']);
            $plan->setNote($_REQUEST['note']);
            $plan->setUID($_SESSION['member']['UID']);
            if (!empty($_REQUEST['eta'])) {
                $plan->setETA(strtotime($_REQUEST['eta'].' 23:59:59'));
                $plan->setStatus(Plan::STATUS_SUSPEND); //sets status suspend if new plan was created with ETA
            }
            if (!empty($_REQUEST['order'])) {
                $plan->setOrder($_REQUEST['order']);
            }
            if (!$_category) {
                $CID = $GLOBALS['SS']->save($category);
                $plan->setCID($CID);
            } else {
                $plan->setCID($_category[0]['CID']);
            }
            $plan->setStart(time());
            $PID = $GLOBALS['SS']->save($plan);
            if ($PID) {
                $_plan = new Plan();
                $_plan->setPID($PID);
                $_resPlan = $GLOBALS['SS']->get($_plan);
                if ($_resPlan) {
                    $result['result'] = 1;
                    $result['plan'] = $_resPlan[0];
                }
            } else {
                $result['result'] = -1;
            }
            echo json_encode($result);
            break;
        case 'load_category':
            $category = new Category();
            $category->setUID($_SESSION['member']['UID']);
            $_categorys = $GLOBALS['SS']->get($category);
            if ($_categorys) {
                $result['result'] = 1;
                $result['categorys'] = $_categorys;
            } else {
                $result['result'] = -1;
            }
            echo json_encode($result);
            break;
        case 'load_done':
            $plan = new Plan();
            $plan->setUID($_SESSION['member']['UID']);
            $plan->setDone(TRUE);
            $plan->setRecycle(FALSE);
            $result['total_pages'] = ceil($GLOBALS['SS']->count($plan) / Plan::QUANTITY_PER_PAGE);
            $plan->setLimit(0, Plan::QUANTITY_PER_PAGE);
            $plan->addSort('order');
            $plan->setBy(Plan::SORT_TYPE_ASC);
            $_plans = $GLOBALS['SS']->get($plan);
            if ($_plans) {
                $result['result'] = 1;
                $result['plans'] = $_plans;
                $result['page'] = 1;
            } else {
                $result['result'] = -1;
            }
            echo json_encode($result);
            break;
        case "load_init":
        case 'load_undone':
            $plan = new Plan();
            $plan->setUID($_SESSION['member']['UID']);
            $plan->setDone(FALSE);
            $plan->setRecycle(FALSE);
            $result['total_pages'] = ceil($GLOBALS['SS']->count($plan) / Plan::QUANTITY_PER_PAGE);
            $plan->setLimit(0, Plan::QUANTITY_PER_PAGE);
            $plan->addSort('order');
            $plan->setBy(Plan::SORT_TYPE_ASC);
            $_plans = $GLOBALS['SS']->get($plan);
            if ($_plans) {
                $result['result'] = 1;
                $result['plans'] = $_plans;
                $result['page'] = 1;
            } else {
                $result['result'] = -1;
            }
            echo json_encode($result);
            break;
        case 'done':
        case 'undone':
            $plan = new Plan();
            $plan->setUID($_SESSION['member']['UID']);
            $plan->setPID($_REQUEST['PID']);
            $_plan = $GLOBALS['SS']->get($plan);
            if ($_plan) {
                $spend = $_plan[0]['Spend'];
                $spend += time() - $_plan[0]['Start'];
                if ($_GET['action'] == 'done') {
                    $plan->setDone(TRUE);
                    $plan->setFinish(time());
                    if ($_plan[0]['ETA'] > 0 && $_plan[0]['Status'] == Plan::STATUS_ONGOING) {
                        $plan->setSpend($spend);
                    }
                    $plan->setStatus(Plan::STATUS_COMPLETE);
                } else {
                    $plan->setDone(FALSE);
                    $plan->setFinish(0);
                    $plan->setStatus(Plan::STATUS_SUSPEND);
                }
                if ($GLOBALS['SS']->update($plan, array('PID', 'UID'))) {
                    $updatedPlan = $GLOBALS['SS']->get($plan);
                    if ($updatedPlan) {
                        $result['plan'] = $updatedPlan[0];
                        $result['result'] = 1;
                    } else {
                        $result['result'] = -4;
                    }
                } else {
                    $result['result'] = -1;
                }
            } else {
                $result['result'] = -3;
            }
            echo json_encode($result);
            break;
        case 'paging':
        case 'load_menu_plans':
            $plan = new Plan();
            $plan->setUID($_SESSION['member']['UID']);
            $plan->setRecycle(FALSE);
            $nonCategory = false;
            if ($_REQUEST['name'] == 'undone' || $_REQUEST['name'] == 'done' || $_REQUEST['name'] == 'none') {
                $nonCategory = true;
                if ($_REQUEST['name'] == 'undone') {
                    $plan->setDone(FALSE);
                } elseif ($_REQUEST['name'] == 'done') {
                    $plan->setDone(TRUE);
                }
                if ($_REQUEST['name'] == 'none') {
                    $plan->setRecycle(TRUE);
                }
            }
            if (!$nonCategory) {
                $category = new Category();
                $category->setUID($_SESSION['member']['UID']);
                $category->setName($_REQUEST['name']);
                $_category = $GLOBALS['SS']->get($category);
                if ($_category) {
                    $plan->setCID($_category[0]['CID']);
                } else {
                    $result['result'] = -3;
                    echo json_encode($result);
                    die();
                }
            }
            $result['total_pages'] = ceil($GLOBALS['SS']->count($plan) / Plan::QUANTITY_PER_PAGE);
            $page = isset($_REQUEST['page']) && is_numeric($_REQUEST['page']) && $_REQUEST['page'] > 0 ? $_REQUEST['page'] : 1;
            $start = ($page - 1) * Plan::QUANTITY_PER_PAGE;
            $plan->setLimit($start, Plan::QUANTITY_PER_PAGE);
            $plan->addSort('done');
            $plan->addSort('order');
            $plan->setBy(Plan::SORT_TYPE_ASC);
            $_plans = $GLOBALS['SS']->get($plan);
            if ($_plans) {
                $result['result'] = 1;
                $result['plans'] = $_plans;
                $result['page'] = $page;
            } else {
                $result['result'] = -1;
            }
            echo json_encode($result);
            break;
        case 'update_order':
        case 'sort':
            $plan = new Plan();
            $plan->setUID($_SESSION['member']['UID']);
            $plan->setOrder($_REQUEST['order']);
            $_plan = $GLOBALS['SS']->get($plan);
            if ($_plan) { // check if there is a same order plan exsit
                if ($_plan[0]['PID'] == $_REQUEST['PID']) {  // return true if get the same plan
                    $result['Order'] = $plan->getOrder();
                    $result['result'] = 2;
                    echo json_encode($result);
                    die();
                }
//                $order = $_plan[0]['Order'] - 1; // raise the order if the same order plan exsit
//                if ($order > 0) {
//                    $plan->setOrder($order); // reset order
//                } else {
//                    $tempPlan = new Plan();
//                    $tempPlan->setPID($_plan[0]['PID']);
//                    $tempPlan->setOrder($_REQUEST['order'] + 1);  // reduce the order of exsit one
//                    if (!$GLOBALS['SS']->update($tempPlan, array('PID', 'UID'))) {
//                        $result['result'] = -3;
//                        echo json_encode($result);
//                        die();
//                    }
//                }
            }
            $plan->setPID($_REQUEST['PID']);
            if ($GLOBALS['SS']->update($plan, array('PID', 'UID'))) {
                $updatedPlan = new Plan();
                $updatedPlan->setUID($_SESSION['member']['UID']);
                $updatedPlan->setPID($_REQUEST['PID']);
                $successfulPlan = $GLOBALS['SS']->get($updatedPlan);
                $result['Order'] = $successfulPlan[0]['Order'];
                $result['result'] = 1;
            } else {
                $originalPlan = new Plan();
                $originalPlan->setUID($_SESSION['member']['UID']);
                $originalPlan->setPID($_REQUEST['PID']);
                $failedPlan = $GLOBALS['SS']->get($originalPlan);
                $result['Order'] = $failedPlan[0]['Order'];
                $result['result'] = -1;
            }
            echo json_encode($result);
            break;
        case 'recycle':
            $plan = new Plan();
            $plan->setPID($_REQUEST['PID']);
            $plan->setUID($_SESSION['member']['UID']);
            $_plan = $GLOBALS['SS']->get($plan);
            if ($_plan) {
                $recycle = $_plan[0]['Recycle'] > 0 ? FALSE : TRUE;
                $plan->setRecycle($recycle);
                if ($GLOBALS['SS']->update($plan, array('PID', 'UID'))) {
                    $result['result'] = 1;
                } else {
                    $result['result'] = -1;
                }
            } else {
                $result['result'] = -3;
            }
            echo json_encode($result);
            break;
        case 'load_recycles':
            $plan = new Plan();
            $plan->setUID($_SESSION['member']['UID']);
            $plan->setRecycle(TRUE);
            $result['total_pages'] = ceil($GLOBALS['SS']->count($plan) / Plan::QUANTITY_PER_PAGE);
            $plan->setLimit(0, Plan::QUANTITY_PER_PAGE);
            $plan->addSort('done');
            $plan->addSort('order');
            $plan->setBy(Plan::SORT_TYPE_ASC);
            $_plans = $GLOBALS['SS']->get($plan);
            if ($_plans) {
                $result['result'] = 1;
                $result['plans'] = $_plans;
                $result['page'] = 1;
            } else {
                $result['result'] = -1;
            }
            echo json_encode($result);
            break;
        case 'delete':
            $plan = new Plan();
            $plan->setPID($_REQUEST['PID']);
            $plan->setUID($_SESSION['member']['UID']);
            if ($GLOBALS['SS']->delete($plan)) {
                $result['result'] = 1;
            } else {
                $result['result'] = -1;
            }
            echo json_encode($result);
            break;
        case 'update_note':
            $plan = new Plan();
            $plan->setPID($_REQUEST['PID']);
            $plan->setUID($_SESSION['member']['UID']);
            $plan->setNote($_REQUEST['note']);
            if ($GLOBALS['SS']->update($plan, array('PID', 'UID'))) {
                $result['result'] = 1;
            } else {
                $result['result'] = -1;
            }
            echo json_encode($result);
            break;
        case 'eta':
            $plan = new Plan();
            $plan->setPID($_REQUEST['PID']);
            $plan->setUID($_SESSION['member']['UID']);
            $_plan = $GLOBALS['SS']->get($plan);
            if ($_plan) {
                switch ($_plan[0]['Status']) {
                    case Plan::STATUS_ONGOING:
                        $nextStatus = Plan::STATUS_SUSPEND;
                        $plan->setStatus($nextStatus); //sets status suspend
                        $spend = $_plan[0]['Spend'];
                        $spend += time() - $_plan[0]['Start'];
                        $plan->setSpend($spend);
                        $result['Spend'] = $spend;
                        break;
                    case Plan::STATUS_SUSPEND:
                        $nextStatus = Plan::STATUS_ONGOING;
                        $plan->setStatus($nextStatus); // sets status ongoing
                        $plan->setStart(time());
                        break;
                    default:
                        $nextStatus = -1; // sets status wrong
                        break;
                }
                if ($GLOBALS['SS']->update($plan, array('PID', 'UID'))) {
                    $result['result'] = 1;
                    $result['Status'] = $nextStatus;
                } else {
                    $result['result'] = -1;
                }
            } else {
                $result['result'] = -3;
            }
            echo json_encode($result);
            break;
        default:
            $result['result'] = -10;
            echo json_encode($result);
            break;
    }
} else {
    $result['result'] = -2;
    echo json_encode($result);
}
?>
