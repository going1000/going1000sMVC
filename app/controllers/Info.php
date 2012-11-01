<?php
class info extends GBaseCtrl {
	public function __construct() {
		parent::__construct('info');
	}
	public function index() {
		$this->infolist();
	}
	public function infolist() {
		$a = GFactory::dao('test')->fetchAll(PDO::FETCH_ASSOC); 
		$this->tpl->setfile('info/list')
				  ->assign('a',$a)
				  ->display();
	}
	public function add() {
		if(empty($_POST)) {
			$this->tpl->setfile('info/add')
					  ->display();
		} else {
			$aaaa       = $_POST['aaaa'];    //加入检测
			$class_id	= intval($_POST['class_id']);
			
			$arr = array(
						'aaaa'     => $aaaa,
						'class_id' => $class_id,
			);
			$rs = GFactory::dao('test')->add($arr);
			if($rs !== false) {
				echo SUCCESS.'|'.url('/info');
			} else {
				echo ALERT.'|插入失败';
			}
		}
	}
	
	public function modify() {
		if(empty($_GET['id'])) redirect(url('/info'));
		$id = intval($_GET['id']);
		if(empty($_POST)) {				
			$a = GFactory::dao('test')->fetch($id);
			$this->tpl->setfile('info/add')
					  ->assign('a',$a)
					  ->display();
		} else {
			$aaaa = trim($_POST['aaaa']);
			$class_id = trim($_POST['class_id']);
			$arr = array(
						'aaaa'     => $aaaa,
						'class_id' => $class_id,
			);
			$rs = GFactory::dao('test')->update($id, $arr);
			if($rs !== false) {
				$this->dao = null;
				echo SUCCESS.'|'.url('/info');
			} else {
				echo ALERT.'|修改错误';
			}
		}
	}
	public function delete() {
		if(empty($_GET['id'])) {
			$this->dao = null;
			redirect(url('/info'));
		} else {
			$id = intval($_GET['id']);
			$rs = GFactory::dao('test')->delete($id);
			if($rs !== false) {
				$this->dao = null;
				echo SUCCESS.'|'.url('/info');
			} else {
				echo ALERT.'|删除错误';
			}
		}
	}
	
	public function batchdelete() {
		var_dump($_POST);
//		$arr = array(10,11,12);
//		$rs = GFactory::dao('test')->batchdelete($arr);
//		var_dump($rs);
	}
	
	public function batchinsert() {
		
		for($i=1;$i<=5;$i++) {
			$arr[] = array(
						'aaaa'     => 'test'.$i,
						'class_id' => 5,
						);
		}
//		var_dump($arr);
		$rs = GFactory::dao('test')->batchinsert($arr);
		var_dump($rs);
	}
}
?>