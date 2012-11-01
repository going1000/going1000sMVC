<?php 
//require_once 'GTable.php';
//require_once 'GTemplate.php';

//include 所有 dao????
abstract class GBaseCtrl {
	
	protected $tpl = '';  //模板类
	protected $dao = '';  //M类
	
	public function __construct($tableName) {
		$this->tpl = new GTemplate();
//		$this->dao = new GTable($tableName);   //$this->dao = new GBaseDao($tableName);
	}
	
	public function beforeAction($action) {}
	public function afterAction($action, $result) {}
	
	protected function returnMsg() {}                 //format return msg
}

?>