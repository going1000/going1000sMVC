<?php
/**
 * @author     going1000 <miaorenjin@gmail.com>
 * @copyright  Copyright (c) 2012
 * @version    $Id$
 */
define('DB_HOST',			'localhost');
define('DB_NAME',			'test');
define('DB_USER',			'root');
define('DB_PASSWORD',		'123456');

define('DB_PORT',			'3306');
define('DB_TYPE',			'mysql');
define('DB_CONNECT',		0); //0: connect; 1: pconnect


//define('SITE_ROOT', dirname($_SERVER['SCRIPT_FILENAME']));   // '/' ：反斜杠在windows和linux下通用
define('SITE_ROOT' , dirname(__FILE__));   // '\'   D:\wamp\www\my_app
define('SITE_URL'  , 'http://'.$_SERVER['SERVER_NAME'].'/myApp');   // http://localhost/my_app

define('DEBUG'     , true);      //调试开关

define('PHP'	   , '.php');
define('SUCCESS'   , 'success'); //the result type of an action.
define('ALERT'     , 'alert');
//redis's host & port
define('MQ_HOST', 'localhost');
define('MQ_PORT', 6379);

define('TIME_FORMAT', 		'Y-m-d H:i:s');
define('DATE_FORMAT', 		'Y-m-d');
define('DEFAULT_TIME_ZONE', 'Asia/Shanghai');