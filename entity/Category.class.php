<?php

/*
 * Project: simpleplan
 * File: Category.class.php
 * Date: 10:18:15 PM  Feb 28, 2013
 * Encoding: UTF-8
 * Version: 1.00
 * Copyright (C) Wang Yu Zhe <yuzhewong@gmail.com> 2013
 */

/**
 * Description of Plan
 *
 * @author Wang Yu Zhe <yuzhewong@gmail.com>
 */
class Category extends Base {
    private $CID;
    private $UID;
    private $name;

    /**
     * @ID
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
    public function getUID() {
        return $this->UID;
    }

    public function setUID($UID) {
        $this->UID = $UID;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

}

?>
