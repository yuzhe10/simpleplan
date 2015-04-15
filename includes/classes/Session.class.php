<?php

/*
 * Project: simpleplan
 * File: Session.class.php
 * Date: 5:19:10 PM  Feb 23, 2013
 * Encoding: UTF-8
 * Version: 1.00
 * Copyright (C) Wang Yu Zhe <yuzhewong@gmail.com> 2013
 */

/**
 * Description of Session
 *
 * @author Wang Yu Zhe <yuzhewong@gmail.com>
 */
class Session {

    private $refObj = '';
    private $table = '';
    private $values = array();

    public function save($obj) {
        $this->prepare($obj);
        $getter = '';
        $prop = '';
        $props = '';
        $values = '';
        $docs = '';
        $query = '';
        foreach ($this->values as $propRef) {
            $prop = $propRef->getName();
            $getter = 'get' . ucfirst($prop); // actually PHP will ignore case sensitive
            if ($this->refObj->hasMethod($getter)) {
                $docs = $this->refObj->getMethod($getter)->getDocComment();
                if (stripos($docs, '@ID') !== FALSE) {
                    continue;
                }
                $value = $this->refObj->getMethod($getter)->invoke($obj);
                if (!is_null($value)) {
                    $prop = strtolower($prop);
                    $props .= '`' . $prop . '`,';
                    if (!empty($docs) && (stripos($docs, '@int') !== FALSE || stripos($docs, '@ID') !== FALSE)) {
                        $values .= $value . ',';
                    } elseif (is_bool($value)) {
                        $values .= $value ? 1 : 0;
                    } else {
                        $values .= '"' . $GLOBALS['DB']->escapeString($value) . '",';
                    }
                }
            } else {
                throw new Exception(__METHOD__ . ' can not foun ' . $getter . ' method!');
            }
        }
        $props = trim($props, ',');
        $values = trim($values, ',');
        $query = 'insert into ' . $this->table . '(' . $props . ') values(' . $values . ')';
//        echo $query;
        $GLOBALS['DB']->query($query);
        return $GLOBALS['DB']->lastInsertId();
    }

    /**
     * Gets entity from DB by specific props
     * @param object $obj model
     * @param array $order Order by specified properties, the last one indicates ASC or DESC, A is ASC, D is DESC
     * @param array $limit Limit query result,the first one is start,the last one is end
     */
    public function get($obj) {
        $this->prepare($obj);
        $query = '';
        $getter = '';
        $criteria = '';
        $_order = '';
        $_limit = '';
        foreach ($this->values as $propRef) {
            $prop = $propRef->getName();
            $getter = 'get' . ucfirst($prop);
            if ($this->refObj->hasMethod($getter)) {
                $value = $this->refObj->getMethod($getter)->invoke($obj);
                $docs = $this->refObj->getMethod($getter)->getDocComment();
                if (!is_null($value)) {
                    $prop = strtolower($prop);
                    if ($prop == 'sort') {
                        $_vRes = array();
                        if (is_array($value)) {
                            foreach ($value as $_v) {
                                if (strpos($_v, '`') === FALSE) {
                                    $_vRes[] = '`' . $_v . '`';
                                } else {
                                    $_vRes[] = $_v;
                                }
                            }
                            $value = implode(',', $_vRes);
                        } else {
                            throw new Exception(__METHOD__ . ' at ' . __LINE__ . ': data type is wrong');
                        }
                        $_order = ' ORDER BY ' . $value;
                        continue;
                    } elseif ($prop == 'by') {
                        $_order .= ' ' . $value;
                        continue;
                    } elseif ($prop == 'limit') {
                        $_limit = ' LIMIT ' . implode(',', $value);
                        continue;
                    }
                    if (is_bool($value)) {
                        $criteria .= $value ? '`' . $prop . '` > 0 and ' : '`' . $prop . '` <= 0 and ';
                    } elseif (!empty($docs) && (stripos($docs, '@int') !== FALSE || stripos($docs, '@ID') !== FALSE)) {
                        $criteria .= '`' . $prop . '` = ' . $value . ' and ';
                    } else {
                        $criteria .= '`' . $prop . '` = "' . $GLOBALS['DB']->escapeString($value) . '" and ';
                    }
                }
            } else {
                throw new Exception(__METHOD__ . ' can not found ' . $getter . ' method!');
            }
        }
        if (!empty($criteria)) {
            $criteria = 'and ' . trim($criteria, ' and ');
        }
        $query = 'select * from ' . $this->table . ' where 1 ' . $criteria . $_order . $_limit;
//        echo $query;
        $GLOBALS['DB']->query($query);
        if ($GLOBALS['DB']->numRows() > 0) {
            $data_returned = array();
            while ($row = $GLOBALS['DB']->fetchAssoc())
                $data_returned[] = $row;
        } else {
            $data_returned = FALSE;
        }
        return $data_returned;
    }

    public function update($obj, $where) {
        if (!$where) {
            return false;
        }
        $this->prepare($obj);
        $query = '';
        $getter = '';
        $criteria = '';
        $sets = '';
        $docs = '';
        foreach ($this->values as $propRef) {
            $prop = $propRef->getName();
            $getter = 'get' . ucfirst($prop);
            if ($this->refObj->hasMethod($getter)) {
                $value = $this->refObj->getMethod($getter)->invoke($obj);
                $docs = $this->refObj->getMethod($getter)->getDocComment();
                if (!is_null($value)) {
                    $prop = strtolower($prop);
                    if (is_array($where)) {
                        foreach ($where as $_w) {
                            $_w = strtolower($_w);
                            if ($_w == $prop) {
                                if (is_bool($value)) {
                                    $criteria .= $value ? '`' . $prop . '` > 0 and ' : '`' . $prop . '` <= 0 and ';
                                } elseif (!empty($docs) && (stripos($docs, '@int') !== FALSE || stripos($docs, '@ID') !== FALSE)) {
                                    $criteria .= '`' . $prop . '` = ' . $value . ' and ';
                                } else {
                                    $criteria .= '`' . $prop . '` = "' . $GLOBALS['DB']->escapeString($value) . '" and ';
                                }
                                continue 2;
                            }
                        }
                    } elseif ($prop == strtolower($where)) {
                        if (is_bool($value)) {
                            $criteria .= $value ? '`' . $prop . '` > 0' : '`' . $prop . '` <= 0';
                        } elseif (!empty($docs) && (stripos($docs, '@int') !== FALSE || stripos($docs, '@ID') !== FALSE)) {
                            $criteria .= '`' . $prop . '` = ' . $value;
                        } else {
                            $criteria .= '`' . $prop . '` = "' . $GLOBALS['DB']->escapeString($value) . '"';
                        }
                        continue;
                    }
                    $sets .= '`' . $prop . '` = ';
                    if (is_bool($value)) {
                        $sets .= $value ? '1, ' : '0, ';
                    } elseif (!empty($docs) && (stripos($docs, '@int') !== FALSE || stripos($docs, '@ID') !== FALSE)) {
                        $sets .= $value . ', ';
                    } else {
                        $sets .= '"' . $GLOBALS['DB']->escapeString($value) . '", ';
                    }
                }
            } else {
                throw new Exception(__METHOD__ . ' can not found ' . $getter . ' method!');
            }
        }
        if (!empty($sets)) {
            $sets = trim($sets, ', ');
        }
        if (!empty($criteria)) {
            $criteria = 'and ' . trim($criteria, ' and ');
        }
        $query = 'update `' . $this->table . '` set ' . $sets . ' where 1 ' . $criteria;
//        echo $query;
        $GLOBALS['DB']->query($query);
        return $GLOBALS['DB']->affectedRows();
    }

    public function delete($obj) {
        $this->prepare($obj);
        $query = '';
        $getter = '';
        $criteria = '';
        $docs = '';
        foreach ($this->values as $propRef) {
            $prop = $propRef->getName();
            $getter = 'get' . ucfirst($prop);
            if ($this->refObj->hasMethod($getter)) {
                $docs = $this->refObj->getMethod($getter)->getDocComment();
                $value = $this->refObj->getMethod($getter)->invoke($obj);
                if (!is_null($value)) {
                    $prop = strtolower($prop);
                    if (is_bool($value)) {
                        $criteria .= $value ? '`' . $prop . '` > 0 and ' : '`' . $prop . '` <= 0 and ';
                    } elseif (!empty($docs) && (stripos($docs, '@int') !== FALSE || stripos($docs, '@ID') !== FALSE)) {
                        $criteria .= '`' . $prop . '` = ' . $value . ' and ';
                    } else {
                        $criteria .= '`' . $prop . '` = "' . $GLOBALS['DB']->escapeString($value) . '" and ';
                    }
                }
            } else {
                throw new Exception(__METHOD__ . ' can not foun ' . $getter . ' method!');
            }
        }
        if (!empty($criteria)) {
            $criteria = 'and ' . trim($criteria, ' and ');
        }
        $query = 'delete from ' . $this->table . ' where 1 ' . $criteria;
//        echo $query;
        $GLOBALS['DB']->query($query);
        return $GLOBALS['DB']->affectedRows();
    }

    public function count($obj) {
        $this->prepare($obj);
        $query = '';
        $getter = '';
        $criteria = '';
        $docs = '';
        foreach ($this->values as $propRef) {
            $prop = $propRef->getName();
            $getter = 'get' . ucfirst($prop);
            if ($this->refObj->hasMethod($getter)) {
                $docs = $this->refObj->getMethod($getter)->getDocComment();
                $value = $this->refObj->getMethod($getter)->invoke($obj);
                if (!is_null($value)) {
                    $prop = strtolower($prop);
                    if (is_bool($value)) {
                        $criteria .= $value ? '`' . $prop . '` > 0 and ' : '`' . $prop . '` <= 0 and ';
                    } elseif (!empty($docs) && (stripos($docs, '@int') !== FALSE || stripos($docs, '@ID') !== FALSE)) {
                        $criteria .= '`' . $prop . '` = ' . $value . ' and ';
                    } else {
                        $criteria .= '`' . $prop . '` = "' . $GLOBALS['DB']->escapeString($value) . '" and ';
                    }
                }
            } else {
                throw new Exception(__METHOD__ . ' can not foun ' . $getter . ' method!');
            }
        }
        if (!empty($criteria)) {
            $criteria = 'and ' . trim($criteria, ' and ');
        }
        $query = 'select count(*) from ' . $this->table . ' where 1 ' . $criteria;
//        echo $query;
        return $GLOBALS['DB']->getOne($query);
    }

    public function prepare($obj) {
        $this->refObj = new ReflectionClass($obj);
        $this->table = strtolower($this->refObj->getName());
        $this->values = $this->refObj->getProperties();
    }

}

?>
