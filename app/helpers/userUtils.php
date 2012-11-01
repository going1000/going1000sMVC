<?php
function curlPost($url, $data, $otherOpt=array()) {
	$ch = curl_init();
	
	$options = array(
		CURLOPT_URL => $url,
		CURLOPT_HEADER => 0,
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => $data,
		CURLOPT_RETURNTRANSFER=>1,
// 		CURLOPT_COOKIEJAR=>CURL_COOKIE_FILE, //保存cookie
// 	    CURLOPT_COOKIEFILE=>CURL_COOKIE_FILE, //发送cookie 
		CURLOPT_USERAGENT=>'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; msn OptimizedIE8;ZHCN; SV1; BTRS28496; .NET CLR 2.0.50727; .NET CLR 3.0.04506.648)'
	);
	

	if (!isset($otherOpt[CURLOPT_TIMEOUT])) $otherOpt[CURLOPT_TIMEOUT] = 30;
	if (!empty($otherOpt)) {
		foreach ($otherOpt as $key =>$opt) {
			$options[$key] = $opt;
		}
	}
	
	curl_setopt_array($ch, $options);
	
	$result = curl_exec($ch);
	
	if (curl_errno($ch)) {
		$result = array('success'=>false, 'result'=>curl_error($ch));
	} else {
		$result = array('success'=>true, 'result'=>$result);
	}
	
	curl_close($ch);
	return $result;
}
function curlGet($url,$otherOpt=array()) {
	$ch = curl_init();
	$cookie = LD_UPLOAD_PATH.'/cookie.txt';
	
	$options = array(
		CURLOPT_URL => $url,
		CURLOPT_HEADER => 0,
		CURLOPT_RETURNTRANSFER=>1,
// 		CURLOPT_COOKIEJAR=>CURL_COOKIE_FILE, //保存cookie
// 	    CURLOPT_COOKIEFILE=>CURL_COOKIE_FILE, //发送cookie 
		CURLOPT_USERAGENT=>'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; msn OptimizedIE8;ZHCN; SV1; BTRS28496; .NET CLR 2.0.50727; .NET CLR 3.0.04506.648)'
	);
	if (!empty($otherOpt)) {
		foreach ($otherOpt as $key =>$opt) {
			$options[$key] = $opt;
		}
	}
	
	curl_setopt_array($ch, $options);
	$result = curl_exec($ch);
	if (curl_errno($ch)) {
		$result = array('success'=>false, 'result'=>curl_error($ch));
	} else {
		$result = array('success'=>true, 'result'=>$result);
	}
	
	curl_close($ch);
	return $result;
}
/**
 * 处理html
 * @param string $html
 * @return string 处理过的html代码
 */
function repair($html) {
	$html = utf($html);//转换为utf8编码
	if (class_exists('tidy',false)) {//tidy检查整理标准化html
		$tidy = new tidy();
		$tidy->parseString($html, array('output-encoding'=>'raw'),'utf8');
		$tidy->cleanRepair();
		$html = tidy_get_output($tidy);
	}
	$utf8Header = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	$reg = '/<meta[^>]*(gbk|gb2312|utf-8)[^\/>|>]*(\/>|>)/isU';
	$html = preg_replace($reg, '', $html);
	$start = strpos($html, '<head>');
	$html = substr_replace($html, '<head>'.$utf8Header, $start, 6);
	return $html;
}
/**
 * splite header and body
 * @param string $html
 * @param boolen $append: 
 * 	1、可能会有跳转的情况，如果需要获取多个http头，将其设为true,将返回个header Array
 * 	2、默认false则取得有效的第一个header头
 * @return array contain header and body
 * 
 *     思路：
 *     1、用"\r\n\r\n"分割header和body
 *     2、使用代理服务器，可能会返回连续的http状态头(其中一个可能是代理服务器加上的)，这时候，
 *     判断标准为取得的header头中是否有换行符（默认单状态只有一行）
 */
function httpHeader($html, $append = false) {
	$headerArr = array();
	$body = $html;
	if($append) {
		do {
			list($header, $body) = explode("\r\n\r\n", $body, 2);
			$headerArr[] = $header;
		} while(preg_match("/HTTP\/1.[01]{1}/isU", $body));
		return array($headerArr, $body);
	} else {
		do {
			list($header, $body) = explode("\r\n\r\n", $body, 2);
		} while(!preg_match("/[\r\n]/is", $header, $matches));
		return array($header, $body);
	}
}
/**
 * Enter description here ...
 * @param array $arr
 * @param string $commaX
 * @return string
 */
function arr2str($arr, $commaX = '&') {
	$str = '';
	$comma = '';
	foreach($arr as $k => $v) {
		$str .= $comma.$k.'='.$v;
		$comma = $commaX;
	}
	return $str;
}
/**
 * manage cookie info in http header
 * @param string $header
 * @param array $noCheckArr： 对应服务器时间与本地时间不符情况，这个数组里面的项目不检测expire时间
 * @return array $cookieArr
 */
function headerManage($header, $noCheckArr = array()) {
	preg_match_all("/\r\nSet-Cookie:[\s]*([^=]*)=([^;]*);[^\r\n]*/is", $header, $matches);
	$yesterday = strtotime('-1 day');
	$cookieArr = array();
	foreach ($matches[0] as $k => $v) {
		if(preg_match("/[\d]+-[a-zA-Z]+-[\d]+/is", $v, $match)) {
			$expireTime = $match[0]; 
			if(in_array($matches[1][$k], $noCheckArr)) {
				$cookieArr[$matches[1][$k]] = $matches[2][$k];
			} else {
				if(strtotime($expireTime) >= $yesterday) {
					$cookieArr[$matches[1][$k]] = $matches[2][$k];
				}
			}
		} else { //没有expire 时间
			$cookieArr[$matches[1][$k]] = $matches[2][$k];
		}
	}
	return $cookieArr;
}
/**
 * 模拟自动跳转
 * @param string $header
 * @param array $otherOpt
 * @return boolen/string: 没有找到跳转链接的话返回false,
 */
function followLocation($header, $otherOpt) {
	$pattern = '/Location: ([^\\r\\n]*)/is';
	if(preg_match($pattern, $header, $match)) {
		return curlGet($match[1], $otherOpt);  //TODO如果使用代理ip，这里学要修改
	} else {
		return false;
	}
}
/**
 * manage a http header & return cookie array
 * @param string $header
 * @param string $cookie
 * @param array $noCheckArr:  对应服务器时间与本地时间不符情况，这个数组里面的项目不检测expire时间
 * @return array $cookieArr
 */
function manageCookie($header, $cookie = '', $noCheckArr = array()) {
	$cookieArr = array();
	$cookieArr2 = array();
	if(is_array($header)) {
		$cookieArrX = array();
		foreach ($header as $header1) {
			$cookieArrX[] = headerManage($header1, $noCheckArr);
		}
		
		foreach($cookieArrX as $cookieArr1) {
			$cookieArr = array_merge($cookieArr, $cookieArr1);
		}
	} else { 
		$cookieArr = headerManage($header, $noCheckArr);
	}
	if($cookie != '') {
		preg_match_all("/([\S][^=]*)=([^;]*);?/is", $cookie, $matches1);
		foreach($matches1[1] as $k => $v) {
			$cookieArr2[trim($v)] = $matches1[2][$k];
		}
	}
	$cookieArr = array_merge($cookieArr2, $cookieArr);
	return $cookieArr;
}
/**
 * 自加n进位（n<10）
 * @param int $num: 输入数字
 * @param int $n: n进位
 * @return int 运算完成的输出值
 */
function carry($num, $n) {
	$returnString = '';
	$isCarry = false;
	$strArr = str_split((string)$num);
	$len = count($strArr);
	for($i=$len-1; $i>=0; $i--) {
		if($isCarry) $strArr[$i]++;
		if($strArr[$i] >= $n) {
			$strArr[$i] = 0;
			$isCarry = true;
		} else {
			$isCarry = false;
		}
		$returnString = $strArr[$i].$returnString;
	}
	if($isCarry) $returnString = '1'.$returnString;
	return (int)$returnString;
}
