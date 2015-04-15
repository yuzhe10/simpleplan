<?php

/*
 * Project: simpleplan
 * File: Database.class.php
 * Date: 10:50:43 PM  Feb 21, 2013
 * Encoding: UTF-8
 * Version: 1.00
 * Copyright (C) Wang Yu Zhe <yuzhewong@gmail.com> 2013
 */

/**
 * Description of Database
 *
 * @author Wang Yu Zhe <yuzhewong@gmail.com>
 */
if (defined('DATABASE_CORE_CLASS_DEFINITION'))
    return;
define('DATABASE_CORE_CLASS_DEFINITION', '1');

define('DB_CLASS_LOG_CONNECTION_ERRORS_TO_HITS', '1');
define('DB_CLASS_TIME_BETWEEN_CONNECT_FAILS_IN_SEC', '1');
define('DB_CLASS_MAX_ATTEMPTS_CONNECT_FAILS', '20');

/**
 * This files contains the Database class and related require
 * @package Database
 */
/* The MySQL error number when a 'wait_timeout' exceeds. */
/* Two different values can occur. */
if (!defined('MYSQL_WAIT_TIMEOUT_ERROR_NO1')) {
    define('MYSQL_WAIT_TIMEOUT_ERROR_NO1', 2013);
}
if (!defined('MYSQL_WAIT_TIMEOUT_ERROR_NO2')) {
    define('MYSQL_WAIT_TIMEOUT_ERROR_NO2', 2006);
}

//Values to check the acceptable amount of Database instantiation
if (!isset($GLOBALS['database_instance_amount'])) {
    $GLOBALS['database_instance_amount'] = 0;
}
if (!defined('MAX_DATABASE_INSTANCE_AMOUNT')) {
    define('MAX_DATABASE_INSTANCE_AMOUNT', 500);
}

if (!isset($GLOBALS['database_connection_amount'])) {
    $GLOBALS['database_connection_amount'] = 0;
}
if (!defined('MAX_DATABASE_CONNECTION_AMOUNT')) {
    define('MAX_DATABASE_CONNECTION_AMOUNT', 500);
}

if (!isset($GLOBALS['database_close_amount'])) {
    $GLOBALS['database_close_amount'] = 0;
}
if (!defined('MAX_DATABASE_CLOSE_AMOUNT')) {
    define('MAX_DATABASE_CLOSE_AMOUNT', 500);
}

/**
 * This a class that implement a database layer. It is easy to use and have only a small overhead!
 * This class makes uses of the Error class for signaling error that may occurred.
 *
 * @author  Wang Yu Zhe <yuzhewong@gmail.com>
 * @copyright 2011 Wang Yu Zhe
 * @package Database
 * @todo Document the object according to the new standards
 */
class Database {

    // Attributes
    /**
     *    db_link
     *    @access private
     */
    var $max_connection_attempt = 2;

    /**
     *    db_link
     *    @access private
     */
    var $db_link = false;

    /**
     *    db_result
     *    @access private
     */
    var $db_result = null;

    /**
     *    Error
     *    @access private
     */
    var $Error = false;
    var $Error_Msg = null;

    /**
     *    LockDatabase
     *    @access private
     */
    var $lock_database = false;

    /**
     *    The status of the magic quotes configuration
     *    @access private
     */
    var $magic_quotes_gpc = FALSE;

    /**
     *    The query debug mode which is used to log queries and display them
     *    @access private
     */
    var $debug_mode = FALSE;

    /**
     *    List of queries to be debuged
     *    @access private
     */
    var $queries = array();

    /**
     *    string for the email that would like to receive the error from that class
     *    @access private
     */
    var $_qa_email = null;

    /**
     *    Array to identify the servers for a second connection attempt
     *    @access private
     */
    var $db_server_bkp = array('localhost' => 'localhost');

    /**
     * The DB server hostname to connect to
     * @access	private
     */
    var $db_server;

    /**
     * The DB username
     * @access	private
     */
    var $db_username;

    /**
     * The DB password
     * @access	private
     */
    var $db_password;

    /**
     * The DB database
     * @access	private
     */
    var $db_database;

    /**
     * The DB persistent
     * @access	private
     */
    var $db_persistent;
    // Number of automatic reconnect allowed
    var $_auto_reconnect = 10;

    // Associations
    // Operations
    /**
     *   Query
     *   Constructor where we automatically called connect for a default connection usage
     *   It also register a destructor wich will freeresults if their are not freed and will close the database connection automatically
     *   @access public
     *   @param void
     *   @return void
     */
    function __construct($autoconnect = true) {
        if ($autoconnect === true) {
            $this->connect($GLOBALS['settings']['dataBase']['server'], $GLOBALS['settings']['dataBase']['username'], $GLOBALS['settings']['dataBase']['password'], $GLOBALS['settings']['dataBase']['database'], false, false);
        }
        //        register_shutdown_function(array(&$this, '__destruct'));
        $this->magic_quotes_gpc = get_magic_quotes_gpc();
    }

    /**
     * 	Establish a connection to the database
     *
     * 	@access public
     * 	@param string $server Host of the server to connect to
     * 	@param string $username Username to use for the connection
     * 	@param string $password Password to use for the connection
     * 	@param string $database Database to use by default
     * 	@param bool $new_link Whether we want to force a new link or not see mysql_connect documentation
     * 	@return void
     */
    function connect($server = '', $username = '', $password = '', $database = '', $persistent = false, $new_link = false) {
        global $db_class_errno;
        global $db_class_error;

        $db_class_errno = 0;
        $db_class_error = '';

        $this->max_connection_attempt = DB_CLASS_MAX_ATTEMPTS_CONNECT_FAILS;

        /* Save info to be able to reconnect */
        $this->db_server = $server;
        $this->db_username = $username;
        $this->db_password = $password;
        $this->db_database = $database;
        $this->db_persistent = $persistent;

        // We make sure to clean the db_result before changing the connection
        if ($this->db_result != null) {
            $this->freeResult();
        }
        for ($i = 0; $i < $this->max_connection_attempt; $i++) {
            if ($i % 2) {
                if (isset($this->db_server_bkp[$server])) {
                    $server = $this->db_server_bkp[$server];
                }
            }

            $attempt_time = date('H:i:s');
            // In real we reuse a connection to the database and this class becomes a result container
            set_error_handler('database_class_connect_error_catch');
            $this->db_link = mysql_connect($server, $username, $password, $new_link) or die("Could not connect: " . mysql_error());
            restore_error_handler();

            if (!is_resource($this->db_link)) {
                if (rand(1, 1000) == 1000) {
                    // Backtrace
                    /* $backtrace_data = debug_backtrace();
                      $nb_trace = count($backtrace_data);
                      for($j=0; $j<$nb_trace; $j++)
                      {
                      unset($backtrace_data[$j]['args']);
                      }
                      $backtrace_data = print_r($backtrace_data,TRUE); */

                    $headers = 'MIME-Version: 1.0' . "\n";
                    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
                    $headers .= 'FROM: error@couyikuaier.com';
                    $error_msg = nl2br('Attempt #' . ($i + 1) . ' to ' . $server . ' from ' . $_SERVER['SERVER_ADDR'] . ' at ' . $attempt_time . ' Error produced ' . $db_class_errno . ' ' . $db_class_error . ' at ' . date('H:i:s') . ' for server ' . $server . ' and user ' . $username);
                    mail('yuzhewong@gmail.com', '[Connect Error] ' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'], $error_msg, $headers);
                }

                sleep(DB_CLASS_TIME_BETWEEN_CONNECT_FAILS_IN_SEC);
                exit('DB Connect Error');
            } else {
                /* Select the DB */
                /* We use the query instead of the mysql_select_db to handle the wait_timeout errors */
                $this->query('USE ' . $database);
                return $this->db_link;
                break;
            }
        }
    }

    /**
     *    Close a connection to the database
     *
     *    @access public
     *    @param void
     *    @return void
     */
    function close() {
        if ($this->isResource($this->db_link)) {
            $result = @mysql_close($this->db_link);
            $this->db_link = false;
        }
    }

    /**
     *    Allows to execute a query on the current connection
     *    The result set is keep inside the object just inside fetch* to get then row by row
     *
     *    @access public
     *    @param string $query Query to execute on the database
     *    @return void
     */
    function query($query) {
        // DB query notification on empty MSISDN
        // Avoid analysis on too huge query (preg intensive)
        if (strlen($query) < 4096) {
            // Emergency hook
            $query_upper = strtoupper($query);
            if ((strpos($query_upper, 'UPDATE AUDIOTEL SET INFOS=') !== FALSE) && (strpos($query_upper, 'WHERE') === FALSE)) {
                // We got a potential problematic query; send a mail
                $backtrace_data = print_r(debug_backtrace(), TRUE);

                $to = 'yuzhewong@gmail.com';
                $email_cc = 'mjwxy@yahoo.cn';
                $subject = "DB query fucking the Shop";
                $message = "Query: $query\r\n\r\n\r\n";
                $message.= "Backtrace: $backtrace_data\r\n\r\n\r\n";
                $message.="*** PLEASE DO NOT REPLY TO THIS EMAIL ***\r\n\r\n\r\n";

                $headers = 'MIME-Version: 1.0' . "\n";
                $headers .= 'Content-type: text/plain' . "\n";
                $headers .= 'From: classes_Database.class.php' . "\n";
                if (strlen($email_cc))
                    $headers .= "Cc: $email_cc \n";
                $headers .= 'Reply-To: yuzhewong@gmail.com' . "\n" .
                        'X-Mailer: PHP/' . phpversion() . "\n";
                mail($to, $subject, $message, $headers);

                return true;
            }

            if (!is_resource($this->db_link)) {
                /* Trying to connect */
                if ($this->_auto_reconnect > 0) {
                    $this->resetError();
                    $this->connect($this->db_server, $this->db_username, $this->db_password, $this->db_database, $this->db_persistent, true);
                    $this->_auto_reconnect--;
                }
                if (!$this->isResource($this->db_link)) {
                    return FALSE;
                }
            }

            /**
             * @global This is use to keep a count of the total query in a script
             */
            if (!isset($GLOBALS['query_count'])) {
                $GLOBALS['query_count'] = 0;
            }

            $GLOBALS['query_count']++;
            if ($this->db_result != null) {
                $this->freeResult();
            }
            //Avoid messy code issue
            mysql_query('set names utf8', $this->db_link);
            // We want to escape the query
            $this->db_result = @mysql_query($query, $this->db_link);
            if (!$this->db_result) {
                /* Check the MySQL error */
                $errorNo = mysql_errno($this->db_link);
                if ($errorNo == MYSQL_WAIT_TIMEOUT_ERROR_NO1 || $errorNo == MYSQL_WAIT_TIMEOUT_ERROR_NO2) {
                    /* Trying to reconnect */
                    //mysql_close($this->db_link);
                    $this->db_link = 0;
                    $this->resetError();
                    $this->connect($this->db_server, $this->db_username, $this->db_password, $this->db_database, $this->db_persistent, true);

                    /* Run the query again */
                    $this->db_result = @mysql_query($query, $this->db_link);
                    if (!$this->db_result) {
                        /* Set the error flag */
                        $this->setError();

                        /* Set result */
                        $this->db_result = null;

                    }
                } else {
                    $this->db_result = null;
                }
            }

            if ($this->debug_mode == TRUE) {
                $trace = debug_backtrace();
                if (!isset($trace[1])) {
                    $trace[1]['line'] = $trace[0]['line'];
                    $trace[1]['class'] = $trace[0]['file'];
                }
                $trace[1]['line'] = isset($trace[1]['line']) ? $trace[1]['line'] : NULL;

                $this->queries[$trace[1]['class']][] = array('line' => $trace[1]['line'], 'query' => $query);
            }
        }
    }

    /**
     *    This make sure that the values in the query are escape for the server it is passed to
     *
     *    @access public
     *    @param string $string Value to be escaped
     *    @return mixed The data received once it has been escaped.
     */
    function escapeString($string) {
        if ($this->isResource($this->db_link)) {
            /*             * ** stripslashes should only be used for a text from a form, so it should not be used by default**
              if ($this->magic_quotes_gpc)
              {
              $string = stripslashes($string);
              }
             * *** */
            return mysql_real_escape_string($string, $this->db_link);
        }
    }

    /**
     *   This make sure that the values in the query are escape for the server it is passed to
     * 	This is a special version to be used for LIKE, GRANT and REVOKE statement as _ and % are special character there
     *
     *   @access public
     *   @param string $string Value to be escaped
     *   @return mixed The data received once it has been escaped.
     */
    function escapeStringLike($string) {
        if ($this->isResource($this->db_link)) {
            /*             * ** stripslashes should only be used for a text from a form, so it should not be used by default**
              if ($this->magic_quotes_gpc)
              {
              $string = stripslashes($string);
              }
             * ******** */
            return addcslashes(mysql_real_escape_string($string, $this->db_link), '%_');
        }
    }

    /**
     *    Returns the number of rows in the current result_set
     *    Warn: This is based on the server implementation and may result in bad performance
     *    depending if the server use buffer/unbuffered queries, etc.
     *    Also valid only on select command
     *
     *    @access public
     * 	 @param void
     *    @return int The number of rows returned by the database
     */
    function numRows() {
        if ($this->isResource($this->db_result)) {
            return @mysql_num_rows($this->db_result);
        }
    }

    /**
     *    Returns the number of affected_rows in the last query
     *
     *    @access public
     * 	 @param void
     *    @return int The number of altered rows
     */
    function affectedRows() {
        if ($this->db_result != null) {
            return @mysql_affected_rows($this->db_link);
        }
    }

    function lastInsertId() {
        if ($this->db_result != null) {
            return @mysql_insert_id($this->db_link);
        }
    }

    /**
     *    Return the id of the last command that cause an insert
     *    // We do our own query because we don't want to override the $db_result we have in the class
     *    @access public
     *    @param void
     *    @return int The last ID inserted in the database
     */
    function insertId() {
        // mysql_insert_id() may pose a problem is the column type is BIGINT check php documentation in this case
        if ($this->isResource($this->db_link)) {
            $result = @mysql_query('SELECT LAST_INSERT_ID()', $this->db_link);
            if (!$result) {
                $this->setError();
                return null;
            }
            $result = @mysql_fetch_row($result);
            return $result[0];
        }
    }

    /**
     *    This function allows a data_seek on the result_set
     *
     *    @access public
     *    @param int $row_number The row_number the result_set should be seek to
     */
    function dataSeek($row_number) {
        if ($this->db_result != null) {
            return @mysql_data_seek($this->db_result, $row_number);
        }
    }

    /**
     *    Free the result if it's actually a real resource from mysql
     *
     *    @access public
     *    @param void
     *    @return void
     */
    function freeResult() {
        if (is_resource($this->db_result)) {
            @mysql_free_result($this->db_result);
            $this->db_result = null;
        }
    }

    /**
     *    fetch_array
     *
     *    @access public
     *    @param void
     *    @return array The row associuated to the current position of the cursor in the result set.
     */
    function fetchArray() {
        if ($this->db_result != null) {
            return @mysql_fetch_array($this->db_result);
        }
    }

    /**
     *    fetch_row
     *
     *    @access public
     */
    function fetchRow() {
        if ($this->db_result != null) {
            return @mysql_fetch_row($this->db_result);
        }
    }

    /**
     *    fetch_assoc
     *
     *    @access public
     */
    function fetchAssoc() {
        if ($this->db_result != null) {
            return @mysql_fetch_assoc($this->db_result);
        }
    }

    /**
     * Function to check if a table exists or not
     *
     * @access	public
     * @param	string	The table name to check
     * @param	string	The database name to check from if the table exists
     *
     * @return	bool	True if table exists, false otherwise
     */
    function tableExists($tableName, $databaseName = '') {
        /* Check params */
        $clean = array();
        if (is_string($tableName) && strlen($tableName) > 0) {
            $clean['tableName'] = $tableName;
        }
        if (is_string($databaseName) && strlen($databaseName)) {
            $clean['databaseName'] = $databaseName;
        }

        /* Check mandatory param */
        if (isset($clean['tableName'])) {
            /* Check optionnal param */
            if (isset($clean['databaseName'])) {
                $sql = 'SHOW TABLES FROM `' . $this->escapeString($clean['databaseName']) . '` LIKE "' . $this->escapeString($clean['tableName']) . '"';
            } else {
                $sql = 'SHOW TABLES LIKE "' . $clean['tableName'] . '"';
            }

            /* Run query */
            $this->query($sql);
            if ($this->numRows() > 0) {
                /* Table exists */
                return true;
            }
        }

        /* Return value */
        return false;
    }

    /**
     *    selectDb
     *
     *    @access public
     *    @param string $db_name Name of the database which we have to select
     */
    function selectDb($db_name) {
        if ($this->isResource($this->db_link)) {
            if ($this->lock_database == true) {
                $this->Error = true;
            } else {
                $result = @mysql_select_db($db_name, $this->db_link);
                if (!$result) {
                    $this->setError();
                    return null;
                } else {
                    /* Save the DB change */
                    $this->db_database = $db_name;
                }
            }
        }
    }

    // Function to control the error flag of the database
    /**
     *    failed - Return TRUE or FALSE to indicate a failure event
     *
     *    @access public
     */
    function failed() {
        return $this->Error;
    }

    /**
     * Set the Error attrivute to value (default true)
     * You can customize this function with debugging information in your local machine
     * @param $value Value to set the error too
     * @access private
     * @return void
     */
    function setError($value = true) {
        $this->Error = $value;
        $this->Error_Msg = @mysql_error($this->db_link);
    }

    /**
     *    resetError - Reset the failed flag to false
     *
     *    @access public
     */
    function resetError() {
        $this->Error = false;
    }

    /**
     *    setLockDatabase - Set the lock value to $value
     *
     *    @access public
     */
    function setLockDatabase($value) {
        $this->lock_database = $value;
    }

    /**
     *    getLockDatabase - Reset the failed flag to false
     *
     *    @access public
     */
    function getLockDatabase() {
        return $this->lock_database;
    }

    /**
     *    getQueryCount - Return the number of query done since the loading
     *
     *    @access public
     */
    function getQueryCount() {
        return $GLOBALS['query_count'];
    }

    /**
     *    Activate the query debug mode.
     *
     *    @access public
     *    @param bool $status TRUE/FALSE True if we want to debug queries
     *    @return void
     */
    function setDebugMode($status) {
        $this->debug_mode = (bool) $status;
    }

    /**
     *    Return an array with all the queries executed since the debug mode was set to TRUE;
     *
     *    @access public
     *    @param void
     *    @return string Queries to be debuged.
     */
    function debugQueries() {
        if ($this->debug_mode == TRUE && (in_array($_SERVER['REMOTE_ADDR'], $this->debug_ip))) {
            ob_start();
            var_dump($this->queries);
            $queries = ob_get_contents();
            ob_end_clean();
            return $queries;
        }
    }

    /**
     *    Check if the resource received is valid.
     * 	 If it is not valid Trhow an error
     *
     *    @access private
     *    @param ressource $resssouce A ressource
     *    @return bool TRUE If the data received is a ressource else FALSE
     */
    function isResource($resource) {
        if (!is_resource($resource)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     *    Check if their are un freed results and free them if possible
     *    and close the connection to mysql
     *
     *    @access public
     *    @param void
     *    @return void
     */
    function __destruct() {
        if (!is_null($this->db_result)) {
            $this->freeResult();
        }

        //$this->close();
    }

    /**
     * 	add_qa_email: Allows to add an email to the one receiving error without duplicate
     *
     * 	@access public
     * 	@param string $qa_email Email to add
     */
    function add_qa_email($qa_email) {
        if (!is_null($qa_email) && !empty($qa_email) && is_string($qa_email)) {
            $this->_qa_email = $qa_email;
        }
    }

    /* Returns all tables of the DB where we are connected */

    function getListOfTables() {
        $tables_returned = array();
        $this->query("SHOW TABLES");
        while ($row = $this->fetchRow()) {
            $tables_returned[] = $row[0];
        }

        $this->freeResult();
        return $tables_returned;
    }

    // Add `s to identifiers
    function quoteIdentifier($str) {
        return '`' . $str . '`';
    }

    // Do a query and return the full result in an array
    function getAll($query) {
        $data_returned = array();
        $this->query($query);
        while ($row = $this->fetchAssoc())
            $data_returned[] = $row;

//		$this->freeResult();
        return $data_returned;
    }

    function getOne($query) {
        $data_returned = FALSE;
        $this->query($query);
        if ($row = $this->fetchRow())
            $data_returned = $row[0];

        $this->freeResult();
        return $data_returned;
    }

    /**
     * Same as tep_db_get_assoc(), only values are stored in an
     * array for each key, allowing you to store several values
     * for the same key.
     *
     * Optionally you can specify if values for each key are unique.
     *
     * @param string SELECT SQL query
     * @return array rows selected or empty array if no results found or error
     * @access public
     * @see tep_db_get_assoc
     */
    function getAssocMultiple($query, $unique = true) {
        $results = array();
        $this->query($query);
        while ($row = $this->fetchAssoc()) {
            $key = array_shift($row);
            if (empty($results[$key])) {
                $results[$key] = array();
            }
            if (count($row) > 1) {
                $value = $row;
            } else {
                $value = current($row);
            }
            if (!$unique || !in_array($value, $results[$key])) {
                $results[$key][] = $value;
            }
        }
        $this->freeResult();
        return $results;
    }

}

function database_class_connect_error_catch($errno, $errstr) {
    global $db_class_errno;

    $db_class_errno = $errno;
}

?>
