<?php
/**
 * @author     going1000 <miaorenjin@gmail.com>
 * @copyright  Copyright (c) 2012
 * @version    $Id$
 */
define('JS_PATH'        	, SITE_URL.'/includes/js');
define('CSS_PATH' 		, SITE_URL.'/includes/css');

define('MYLINK'   		, SITE_URL.'/index.php');

define('G_APP_PATH'     , SITE_ROOT.'/app');
define('G_CTRL_PATH'    , G_APP_PATH.'/controllers');
define('G_DAOS_PATH'    , G_APP_PATH.'/daos');
define('G_TEMPLATE_PATH', G_APP_PATH.'/template');
define('G_HELPER_PATH', G_APP_PATH.'/helpers');

define('G_INCLUDES_PATH', SITE_ROOT.'/includes');
define('G_KERNEL_PATH'  , G_INCLUDES_PATH.'/kernel');
define('G_UTILS_PATH'	, G_INCLUDES_PATH.'/utils');
/*
 * 包含：
 * 1、固定的常量
 * 2、固定包含的文件（kernel） 
 */
require_once G_KERNEL_PATH.'/GApplication'.PHP;
require_once G_KERNEL_PATH.'/GBaseCtrl'.PHP;   //V
require_once G_KERNEL_PATH.'/GBaseDao'.PHP; 
require_once G_KERNEL_PATH.'/GDatabase'.PHP;
require_once G_KERNEL_PATH.'/GException'.PHP;
require_once G_KERNEL_PATH.'/GFactory'.PHP;
require_once G_KERNEL_PATH.'/GFliter'.PHP;
require_once G_KERNEL_PATH.'/GKernel'.PHP;
require_once G_KERNEL_PATH.'/GQueue'.PHP;
require_once G_KERNEL_PATH.'/GTable'.PHP;     //M直接操作table
require_once G_KERNEL_PATH.'/GTemplate'.PHP;  //C
require_once G_KERNEL_PATH.'/GValidator'.PHP;
require_once G_UTILS_PATH.'/utils'.PHP;
require_once G_HELPER_PATH.'/Load'.PHP;
require_once G_HELPER_PATH.'/userUtils'.PHP;\

/* ---[make all php configuration consistently]----- */
//timezone
date_default_timezone_set(DEFAULT_TIME_ZONE);

if (!get_cfg_var('short_open_tag')) {
	if (ini_get('short_open_tag')) {
		echo '<span class="warning">Warning: you\'d better turn on your short_open_tag in your PHP.ini for speed performance</span>'; //turn on in .htaccess
	} else {
		die('Pls turn on "short_open_tag" in your php.ini');
	}
}
if (!DEBUG) {
	error_reporting(0);
} else {
	ini_set('display_errors', '1');   //display errors info
	error_reporting(E_ALL ^E_NOTICE);
//	error_reporting(E_ALL);
}

// ini_set('magic_quotes_runtime', false);
// if (get_magic_quotes_gpc()) {
// 	function stripslashes_deep($value) {
// 		return is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
// 	}
// 	$_POST = stripslashes_deep($_POST);
// 	$_GET = stripslashes_deep($_GET);
// 	$_COOKIE = stripslashes_deep($_COOKIE);
// }

/* ---[auto include necessarily library]----- */
function __autoload($className) {
	if (substr($className, -3, 3) == 'Dao') {
        include G_DAOS_PATH.'/'.$className.PHP;
	} else {
        include G_CTRL_PATH.'/'.$className.PHP;
	}
}
