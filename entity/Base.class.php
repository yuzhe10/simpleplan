<?php

/*
 * Project: simpleplan
 * File: Base.class.php
 * Date: 16:19:55  2013-3-3
 * Encoding: UTF-8
 * Version: 1.00
 * Copyright (C) Wang Yu Zhe <yuzhewong@gmail.com> 2013
 */

/**
 *  Base of  entity
 *
 * @author Wang Yu Zhe <yuzhewong@gmail.com>
 */
class Base {
    // constant
    const SORT_TYPE_ASC = 'ASC';
    const SORT_TYPE_DESC = 'DESC';

    // member
    protected $sort; // fields which need to sort, must be array
    protected $limit; // specified the limitation of query resut, separated by comma, like 1,10
    protected $by; // indicates ASC or DESC


    public function getSort() {
        return $this->sort;
    }

    public function setSort(Array $sort) {
        $this->sort = $sort;
    }

    public function addSort($sort) {
        $this->sort[] = $sort; // there is no overhead of calling a function
    }

    public function getLimit() {
        return $this->limit;
    }

    public function setLimit($start = 0, $quantity = 1) {
        if (!empty($this->limit)) {
            $this->limit = array();
        }
        $this->limit[] = $start;
        $this->limit[] = $quantity;
    }

    public function getBy() {
        return $this->by;
    }

    public function setBy($by) {
        $this->by = $by;
    }
}
?>