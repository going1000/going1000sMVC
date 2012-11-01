<?php
class GoogleAccount extends GBaseCtrl {
	const CHANNEL = 'main_channel'; 
	const RETRY_CHANNEL = 'retry_channel';
	const SIGN    = 'googleAccountFilter';
	const RUN_URL = 'http://192.168.1.105/myApp/index.php/googleAccount/';
	
	private $queue;
	
	public function __construct() {
		parent::__construct('googleAccount');
		
		if(empty($this->queue)) {
			$this->queue = GQueue::getInstance();
		}
	}
	
	public function init() {
		$letterArr = array('g', 'e', 'n', 'i', 'u', 's', 'y');
		$nextStep = 'filterAccountName';
		
		for($i=1000000; $i<=1999999; $i++) {
			$i = carry($i, 7);
			for($n=0; $n<7; $n++) {
				
			}
		}
//		$letterArr[rand(0, 5)]
//		for($i =4010; $i <= 4040; $i++) {
//			$name = 'tomy'.$i;
//			$info = array('name' => $name);
//			$this->queue->push(($this->jsonFormat($nextStep, $info)), self::CHANNEL);
//		}
		debug('end');
	}
	
	public function filterAccountName($info) {
		if(!isset($info['name'])) return;
		$otherOpt[CURLOPT_PROXY]	 = '127.0.0.1';
    	$otherOpt[CURLOPT_PROXYPORT] = 8087;
    	$otherOpt[CURLOPT_HTTPHEADER] = array('Content-Type: application/json; charset=UTF-8');
		$data = array(
			'input01' => array(
				'Input' => 'GmailAddress',
				'GmailAddress' => $info['name'],
				'FirstName' => '',
				'LastName'  => ''
			),	
			'Locale' => 'zh-CN'
		);
    	
		$result = curlPost("https://accounts.google.com/InputValidator?resource=signup", json_encode($data), $otherOpt);
		if(!$result['success']) {
			if(DEBUG) debug(__LINE__.'|'.$result['result']);
			return false;
		}
		$jsonArr = json_decode($result['result'], true);
		if(trim($jsonArr['input01']['Valid']) == 'true') {
			debug($this->logName($info['name']), false);
		}
	}
	
	public function run() {
		$json = $this->queue->pop(self::CHANNEL);
		if(empty($json)) return;   //全部进行完毕
		$jsonArr = json_decode($json, true);
		
		if(md5(json_encode($jsonArr['info']).self::SIGN) != $jsonArr['sign']) {
			if(DEBUG) debug('签名错误');
			return;
		}
//		$url = self::RUN_URL.$jsonArr['nextStep'];
		$fun = $jsonArr['nextStep'];
		if($this->$fun($jsonArr['info']) === false) {
			//发生异常，需要重试
			$this->queue->push($json, self::RETRY_CHANNEL);
		}
	}
	
	private function jsonFormat($nextStep, $info) {
		return json_encode(array(
			'nextStep' => $nextStep,
			'info'     => $info,
			'sign'     => md5(json_encode($info).self::SIGN)
		));
	}
	/**
	 * 测试名字是否可用
	 * @param string $name
	 * @return boolen true while successd/false if failure
	 */
	private function accountTest($name) {
//		$otherOpt[CURLOPT_PROXY]	 = '127.0.0.1';
//    	$otherOpt[CURLOPT_PROXYPORT] = 8087;
    	$otherOpt[CURLOPT_HTTPHEADER] = array('Content-Type: application/json; charset=UTF-8');
		$data = array(
			'input01' => array(
				'Input' => 'GmailAddress',
				'GmailAddress' => $name,
				'FirstName' => '',
				'LastName'  => ''
			),	
			'Locale' => 'zh-CN'
		);
    	
		$result = curlPost("https://accounts.google.com/InputValidator?resource=signup", json_encode($data), $otherOpt);
		if(!$result['success']) {
			if(DEBUG) debug(__LINE__.'|'.$result['result']);
			return false;
		}
		
		$jsonArr = json_decode($result['result'], true);
		if($jsonArr['input01']['Valid']) {
			return $this->logName($name);
		}
	}
	/**
	 * log name into local file
	 * @param string $name
	 */
	private function logName($name) {
		return GFactory::dao('name')->insert(array('name' => $name, 'insertTime' => date(TIME_FORMAT)));
	}
}
