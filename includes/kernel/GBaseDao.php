<?php
/**
 * @author     going1000 <miaorenjin@gmail.com>
 * @copyright  Copyright (c) 2012
 * @version    $Id$
 */
//require 'GTable.php';
//require 'GDatabase.php';

class GBaseDao{
	protected $dbh;   //dbHandler
	/**
	 * Instance of GTable
	 *
	 * @var GTable
	 */
	protected $tbl = '';  //GTable Instance
	//数据表
	private $tableName = '';
	/**
	 * Instance of GKernel
	 *
	 * @var GKernel
	 */
	private $kernel;
	
	public function __construct($tableName = '') {
		$this->tableName = ucfirst($tableName);
		$this->kernel = $GLOBALS['GKernel'];
		$this->dbh = $this->kernel->getMDBHandler();
		$this->tbl = new GTable($this->dbh, $this->tableName);
	}
	
	/**
	 * $id: number
	 * $fetchMode: PDO::FETCH_ASSOC,PDO::FETCH_BOTH,PDO::FETCH_NUM etc.
	 */
	public function fetch($id = NULL ,$fetchMode = PDO::FETCH_BOTH) {
		if(is_null($id)) {
			return $this->tbl->fetch();
		} else {
			return $this->tbl->where('id='.$id)->fetch(NULL, $fetchMode);
		}
	}
	
	/**
	 * $condition: string not null
	 */
	public function find($condition, $fetchMode = PDO::FETCH_BOTH) {
		return $this->tbl->where($condition)->limit(0,1)->fetch(NULL, $fetchMode);
	}
	
	public function fetchAll($fetchMode = PDO::FETCH_BOTH) {
		return $this->tbl->fetchAll(NULL ,$fetchMode);
	}
	
	public function findAll($condition, $fetchMode = PDO::FETCH_BOTH) {
		return $this->tbl->where($condition)->fetchAll(NULL ,$fetchMode);
	}
	
	/**
	 * add,update,delete
	 */
	public function add($arr) {
		return $this->tbl->insert($arr);
	}
	public function insert($arr) {
		return $this->tbl->insert($arr);
	}
	/**
	 * $arr: 2D array like array(
	 * 						array('a'=>1,'b'=>'kkkk'),
	 * 						array('a'=>2,'b'=>'ffff'),
	 * 						);
	 */
	public function batchinsert($arr) {
//		if(!empty($arr)) {
//			$this->beginTransaction();
//			foreach ($arr as $v) {
//				$result = $this->insert($arr);
//			}
//			$rs = true;
//			foreach ($result as $v) {
//				$rs = $v !== false && $rs;
//			}
//			if($rs !== false) {
//				$this->commit();
//				return $rs;
//			} else {
//				$this->rollback();
//			}
//		}
		return $this->tbl->batchinsert($arr);
	}
	public function batchadd($arr) {
		$this->batchinset($arr);
	}
	public function update($id, $arr) {
		return $this->tbl->where('id='.$id)->update($arr);
	}
	public function updatewhere($condition, $arr) {
		return $this->tbl->where($condition)->update($arr);
	}
	public function delete($id) {
		return $this->tbl->where('id='.$id)->delete();
	}
	/**
	 * $arr: array of id array(1,2,3)
	 */
	public function batchdelete($arr) {
		for($i=0;$i<sizeof($arr);$i++) {
			$arr1[$i] = array('id'=>$arr[$i]);
		}
		return $this->tbl->where('id=:id')->batchdelete($arr1);
	}
	public function deletewhere($condition) {
		return $this->tbl->where($condition)->delete();
	}
	
	/**
	 * PDO 事务
	 */
	public function beginTransaction() {
		return $this->tbl->beginTransaction();
	}
	public function commit() {
		return $this->tbl->commit();
	}
	public function rollback() {
		return $this->tbl->rollback();
	}
}
?>