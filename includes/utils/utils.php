<?php
/**
 * 
 * Enter description here ...
 */
/**
 * 页面header路径
 */
function tplheader() {
	return G_TEMPLATE_PATH.'header.inc.php';
}
/**
 * 页面footer路径
 */
function tplfooter() {
	return G_TEMPLATE_PATH.'footer.inc.php';
}
//function mylink($ctrl,$act) {
//	return SITE_ROOT.'/index.php/'.$ctrl.'/'.$act;
//}
function url($url) {
	return MYLINK.$url;
}

function jsurl($url) {
	return JS_ROOT.$url;
}

function redirect($url) {
	if($url) header("Location:$url");
}
	
/**
 * 
 */
function debug($content, $var_dump = true) {
	if(!$var_dump) {
		echo '<pre>';
		var_dump($content);
		echo '</pre>';
	} else {
		echo '<pre>';
		print_r($content);
		echo '</pre>';
	}
}
