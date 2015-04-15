<?php

class Member extends Base {

    private $UID;
    private $RID;
    private $userName;
    private $passWord;
    private $email;
    private $regDate;
    private $regIP;
    private $lastIP;
    private $lastVisit;
    private $MSISDN; // added from 2013-03-28

    public function getMSISDN() {
        return $this->MSISDN;
    }

    public function setMSISDN($MSISDN) {
        $this->MSISDN = $MSISDN;
    }

    public function setUID($UID) {
        $this->UID = $UID;
    }

    /**
     * @ID
     */
    public function getUID() {
        return $this->UID;
    }

    public function setUserName($userName) {
        $this->userName = $userName;
    }

    public function getUserName() {
        return $this->userName;
    }

    public function setPassword($password) {
        $this->passWord = $password;
    }

    public function getPassword() {
        return $this->passWord;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setRegDate($regDate) {
        $this->regDate = $regDate;
    }

    /**
     * @int
     */
    public function getRegDate() {
        return $this->regDate;
    }

    public function setRegIP($regIP) {
        $this->regIP = $regIP;
    }

    public function getRegIP() {
        return $this->regIP;
    }

    public function setLastIP($lastIP) {
        $this->lastIP = $lastIP;
    }

    public function getLastIP() {
        return $this->lastIP;
    }

    public function setLastVisit($lastVisit) {
        $this->lastVisit = $lastVisit;
    }

    /**
     * @int
     */
    public function getLastVisit() {
        return $this->lastVisit;
    }

    /**
     * @int
     */
    public function getRID() {
        return $this->RID;
    }

    public function setRID($RID) {
        $this->RID = $RID;
    }

}

?>