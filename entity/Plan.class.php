<?php

/*
 * Project: simpleplan
 * File: Plan.class.php
 * Date: 11:10:02 PM  Feb 19, 2013
 * Encoding: UTF-8
 * Version: 1.00
 * Copyright (C) Wang Yu Zhe <yuzhewong@gmail.com> 2013
 */

/**
 * Description of Plan
 *
 * @author Wang Yu Zhe <yuzhewong@gmail.com>
 */
class Plan extends Base {
    // constant
    const STATUS_ONGOING = 0;
    const STATUS_SUSPEND = 1;
    const STATUS_COMPLETE = 10;
    const QUANTITY_PER_PAGE = 10;
    const DEFAULT_ORDER = 9999;

    private $PID;
    private $UID;
    private $CID;
    private $order;
    private $start;
    private $finish;
    private $done;
    private $recycle;
    private $title;
    private $note;
    private $ETA; // added from 2013-03-30
    private $Status; //  added from 2013-03-30
    private $Spend; // added from 2013-03-31

    /**
     * @int
     */
    public function getETA() {
        return $this->ETA;
    }

    public function setETA($ETA) {
        $this->ETA = $ETA;
    }

        /**
     * @ID
     */
    public function getPID() {
        return $this->PID;
    }

    public function setPID($PID) {
        $this->PID = $PID;
    }

    /**
     * @int
     */
    public function getUID() {
        return $this->UID;
    }

    public function setUID($UID) {
        $this->UID = $UID;
    }

     /**
     * @int
     */
    public function getCID() {
        return $this->CID;
    }

    public function setCID($CID) {
        $this->CID = $CID;
    }

     /**
     * @int
     */
    public function getOrder() {
        return $this->order;
    }

    public function setOrder($order) {
        $this->order = $order;
    }

     /**
     * @int
     */
    public function getStart() {
        return $this->start;
    }

    public function setStart($start) {
        $this->start = $start;
    }

     /**
     * @int
     */
    public function getFinish() {
        return $this->finish;
    }

    public function setFinish($finish) {
        $this->finish = $finish;
    }

     /**
     * @int
     */
    public function getDone() {
        return $this->done;
    }

    public function setDone($done) {
        $this->done = $done;
    }

    /**
     * @int
     */
    public function getRecycle() {
        return $this->recycle;
    }

    public function setRecycle($recycle) {
        $this->recycle = $recycle;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getNote() {
        return $this->note;
    }

    public function setNote($note) {
        $this->note = $note;
    }

     /**
     * @int
     */
    public function getStatus() {
        return $this->Status;
    }

    public function setStatus($Status) {
        $this->Status = $Status;
    }

    /**
     * @int
     */
    public function getSpend() {
        return $this->Spend;
    }

    public function setSpend($Spend) {
        $this->Spend = $Spend;
    }



}

?>
