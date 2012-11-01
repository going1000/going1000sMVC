<?php
/**
 * @author     going1000 <miaorenjin@gmail.com>
 * @copyright  Copyright (c) 2012
 * @version    $Id$
 */
class GApplication{
	private $ctrl;
	private $act;
	
	function __construct() {
		$GLOBALS['GKernel'] = new GKernel();
		$this->parsePathInfo();
		$GLOBALS['ctrl'] = $this->ctrl;
		$GLOBALS['act']  = $this->act;
	}
	/**
	 * 运行程序
	 * @throws GException
	 */
	public function run() {
		try{
			$controllerDir = G_CTRL_PATH.'/'.ucfirst($this->ctrl).PHP;
			if(file_exists($controllerDir)) {
				require_once $controllerDir;
				$controller = new $this->ctrl();
				$action = $this->act;
			
				if (!method_exists($controller, $action)) {
	       			throw new GException(sprintf(LG_ACTION_NOT_FOUND, $action), 
				  	  	sprintf(GException::$err[4], $this->ctrl.'->'.$action), 4);
				}
				
				$method = new ReflectionMethod($controller, $action);
				
				if ($method->isStatic()) {
	       			throw new GException(sprintf(LG_ACTION_CANNOT_BE_STATIC, $action), 
				  	  	sprintf(GException::$err[5], $this->ctrl.'::'.$action), 5);
				}
				
				if(method_exists($controller, 'beforeAction'))
					$controller->beforeAction($action);
				$output = $method->invoke($controller);
				if(method_exists($controller, 'afterAction'))
					$controller->afterAction($action, $output);

				//if have output, means this action is an ajax call.
				if (isset($output)) {
					if (!empty($controller->httpHeader)) {
						if (!is_array($controller->httpHeader)) {
							header($controller->httpHeader);
						} else {
							foreach ($controller->httpHeader as $header) {
								header($header);
							}
						}
					}
					echo $output;
				}
			} else {
				throw new GException(sprintf(LG_CTRL_NOT_FOUND, $this->ctrl), 
					sprintf(GException::$err[3], $controllerDir), 3);
			}
		} catch (Exception $ex) {
			if ($ex instanceof GException ) {
				echo $ex->vividMsg();
			}
			$error = '<pre>'.$ex->__toString()."\n\n".$ex->getTraceAsString().'</pre>';
			error_log($error);
			if (DEBUG) {
				echo $error;
// 				self::debug($error);
			}
		}
	}
	
	/**
	 * 分析url信息，得到ctrl和fun
	 */
	private function parsePathInfo() {
		$pathinfo = $_SERVER['PATH_INFO'];
		$arr = explode('/',trim($pathinfo,'/'));
		
		//===得到control名
		$this->ctrl = empty($arr[0]) ? 'index' : $arr[0];
		//===得到function名
		$this->act = empty($arr[1]) ? 'index' : $arr[1];
		
		$count = count($arr);
		$getNum = floor($count/2);

		for($i=1; $i<$getNum; $i++) {
			$_GET[$arr[2*$i]] = $arr[2*$i+1];
		}
	}
}
