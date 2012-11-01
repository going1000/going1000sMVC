<?php
/**
 * @author     going1000 <miaorenjin@gmail.com>
 * @copyright  Copyright (c) 2012
 * @version    $Id$
 */

/* Error Message */
define('LG_CONNECT_DB_FAILED', 'There may be some problems when connect to the DB Server.');
define('LG_QUERY_FAILED', 'Query database failed, Pls check your SQL clause.');
define('LG_EXECUTE_FAILED', 'Failed to update the database record.');
define('LG_DBTABLE_NOT_EXIST', 'The database table doesn not exist');
define('LG_FIELD_NOT_EXIST', 'Field seems doesn not exist in the table');
define('LG_DSN_NOT_DEFINED', 'We didn\'t find the DSN which leads to connect to DB failed');

define('LG_CONFIG_FILE_NOT_EXIST', 'We didn\'t find the config file: config.inc'.php);
define('LG_CTRL_NOT_FOUND', 'Sorry, the module [<strong>%s</strong>] you requested does not exist');
define('LG_ACTION_NOT_FOUND', 'Sorry, the action [<strong>%s</strong>] you requested does not exist');
define('LG_ACTION_CANNOT_BE_STATIC', 'Sorry, access denied for static action [<strong>%s</strong>]');
define('LG_TEMPLATE_FILE_NOT_FOUND', 'Sorry, Template file [<strong>%s</strong>] does not exist');
define('LG_MISSING_ID', 'Sorry, the page you request does not exist or have been removed.');

define('LG_CONNECT2_QUEUE_FAILED', 'There may be some problems when connect to the Message Queue Server.');
define('LG_CONNECT2_KVDB_FAILED', 'There may be some problems when connect to the Key-Value Databse Server.');

class GException extends Exception {
    /** comprehensive error message for the end user */
    protected $vividMsg;
    //separate the real error messge with vivid message (which is comprehensive for the end user)
    static $err = array(
		0 => '',
		1 => '',
		2 => 'Connect to DB failed: %s',
		3 => 'Ctrl file: [%s] does not exist',
		4 => 'Action [%s] does not exist',
		5 => 'static method [%s] cannot be accessed by User directly',
		6 => 'Tpl file: [%s] does not exist',
	);
    
    function __construct($vividMsg, $errMsg=null, $errNo=0) {
//         parent::__construct($errMsg, $errNo);
		$this->code = $errNo;
		
        $this->vividMsg = $vividMsg;
        $this->message = $errMsg;
    }
    function vividMsg() {
    	return $this->vividMsg;
    }
    function __toString() {
		return 'ERROR #'.$this->code.': '.$this->message;
    }
}

class SqlException extends GException {
    private $sql;
	private $params;
    function __construct($vividMsg, $errMsg, $sql, $params=null, $errNo=0) {
        parent::__construct($vividMsg, $errMsg, $errNo);
		
        $this->sql = $sql;
        $this->params = $params;
    }
    
    function __toString() {
    	$err = parent::__toString()."\n\n";
	    $err .= 'sql error: '.print_r($this->message, true)."\n";
	    $err .= 'sql clause: '.$this->sql."\n";
	    $err .= 'sql params:'.print_r($this->params, true)."\n";
    	return $err;
    }

    function getSql() {
        return $this->sql;
    }
}