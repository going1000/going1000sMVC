<?php
/**
 * @author     going1000 <miaorenjin@gmail.com>
 * @copyright  Copyright (c) 2012
 * @version    $Id$
 */

class GTable{
	/*
	 * sql 各部分
	 */
	private $field = '';   //表属性
	private $hfield = '';  //联合表如果有值，则代替$field
	private $where = '';   //WHERE
	private $groupby = '';
	private $join = '';  //表联合操作
	private $limit = '';   //LIMIT
	private $orderby = ''; 
	
	/*
	 * tablename:操作的表名 
	 */
	private $tableName = '';
	
	/*
	 * pdo 连接 (obj)
	 */
	private $con = '';
	
	/*
	 * PDOstmt (obj)
	 */
	private $stmt = '';
	/**
	 * @var GDatabase
	 */
	private $db = '';   //GDatabase 实例
	
	public function __construct($dbObj, $tableName) {
		$this->tableName = $tableName;
		$this->db = $dbObj;
	}
	
	/**
	 * 获取SELECT数据
	 */
	public function fetch($sqlstmt = Null,$fetchMode = Null) {
		if(empty($sqlstmt)) {
			$sql = $this->constructsql();
			return $this->db->query($sql)->fetch($fetchMode);
		}
	}
	
	public function fetchAll($sqlstmt = Null,$fetchMode = Null) {
		if(empty($sqlstmt)) {		
			$sql = $this->constructsql();			
			return $this->db->query($sql)->fetchAll($fetchMode);			 
		}
	}
	
	private  function constructsql() {
		if(!empty($this->field) || !empty($this->hfield)) {
			$field = empty($this->hfield)? $this->field : $this->hfield;
		} else {
			$field = '*';
		}
		if(!empty($this->where)) {
			$where = 'WHERE '.$this->where;  //要有空格
		} else {
			$where = '';
		}
		if(!empty($this->groupby)) {
			$groupby = 'GROUP BY '.$this->groupby;
		} else {
			$groupby = '';
		}
		if(!empty($this->join)) {
			$join = $this->join;
		} else {
			$join = '';
		}
		if(!empty($this->limit)) {
			$limit = $this->limit;
		} else {
			$limit = '';
		}
		if(!empty($this->orderby)) {
			$orderby = $this->orderby;
		} else {
			$orderby = '';
		}
		$sql = "SELECT $field FROM $this->tableName $join $where $groupby $orderby $limit";
		return $sql;	
	}
	
	/*
	 * set field,where,groupby,condition etc.
	 */
	
	/**
	 * $field:string 'a,b,c' 为要去的列
	 */
	public function field($field) {
		if(!empty($field)) {
			$this->field = $field;
		}
		return $this;
	}
	public function where($where) {
		if(!empty($this->where)) {
			$this->where .= ' AND '.$where;
		} else {
			$this->where .= $where;
		}		
		return $this;
	}
	public function limit($start = 0, $length = 0) {
		if($length === 0) {
			$this->limit = '';
		} else {
			$this->limit = 'LIMIT '.$start.','.$length;
		}
		return $this;
	}
	/**
	 * $orderby: string like 'id DESC','datetime ASC' or more complex--'id DESC,datetime ASC' 
	 */
	public function orderby($orderby) {
		if(!empty($orderby)) {
			$this->orderby = "ORDER BY $orderby";
		}
	}
	public function groupby($groupby) {
		$this->groupby = $groupby;
		return $this;
	}
	public function condition($condition) {
		$this->condition = $condition;
		return $this;
	}
	
	/**
	 * $OtableName:string
	 * $hfield : string 关联表取得的元素 not null!!!
	 * 两个表
	 */
	public function hasA($OtableName,$hfield = '') {
		if(!empty($hfield)) {
			$this->hfield = $this->tableName.'.*,';
			$arr = explode(',',$hfield);
			foreach ($arr as &$v) {
				if ($v == 'id') {		
					$v = $OtableName.'.'.$v.' AS idof'.$OtableName;
				} else {
					$v = $OtableName.'.'.$v;
				}		
			}
			$this->hfield .= implode(',',$arr);		
		}
		$this->join = 'LEFT JOIN '.$OtableName.' ON '.$this->tableName.'.'.$OtableName.'_id'.'='.$OtableName.'.id';
		return $this;
	}
	
	
	/**
	 * 插入数据库
	 * @param 2D array $arr: array('field1'=>'value1', 'field2'=>'value2')
	 * @return false when failure
	 */
	public function insert($arr) {
		if(empty($arr)) return false;
		$comma = '';
		$ques = '?';
		$fieldName = '';
		foreach($arr as $field=>$value) {			
			$fieldName .= $comma.$field;
			$params[] = $value;
			@$v .= $comma.$ques;
			$comma = ',';
		}
		$sql = "INSERT INTO $this->tableName ($fieldName) VALUES ($v)";
		$this->db->execute($sql,$params);
		return $this->db->lastInsertId();
	}
	
	/**
	 * 批量插入数据库
	 * @param array $arr: 2D array (first key must be number)
	 */
	public function batchinsert($arr) {
		for($i=0;$i<50;$i++){
			if(array_key_exists($i,$arr)) {
				$k = $i;
				break;
			}
		}
		$comma = '';
		$ques = '?';
		$fieldName = '';
		foreach ($arr[$k] as $field=>$value) {
			$fieldName .= $comma.$field;
			@$v .= $comma.$ques;
			$comma = ',';
		}
		$sql = "INSERT INTO $this->tableName ($fieldName) VALUES ($v)";
		foreach ($arr as $key=>$value) {
			foreach ($value as $v) {
				$params[$key][] = $v;
			}
		}
		return $this->db->batchexe($sql, $params);
	}
	
	/**
	 * 修改
	 * $arr:array(
 	 * 		    'field1'=>'value1',
     * 			'field2'=>'value2',
	 * 			)
	 */
	public function update($arr) {
		if(empty($arr)) return false;
		$comma = '';
		$fieldName = '';
		foreach($arr as $field=>$value) {
			$fieldName .= $comma.$field.'= ?';
			$params[] = $value;
			$comma = ',';
		}
		if(!empty($this->where)) {
			$sql = "UPDATE $this->tableName SET $fieldName WHERE $this->where";
		} else {
			echo '请设置where';
		}
		return $this->db->execute($sql,$params);
	}
	
	/**
	 * 删除
	 */
	public function delete() {
		if(!empty($this->where)) {
			$params = '';
			$sql = "DELETE FROM $this->tableName WHERE $this->where";
			return $this->db->execute($sql, NULL);
		}		
	}
	public function batchdelete($arr) {
		if(!empty($this->where)) {
			$params = $arr;
			$sql = "DELETE FROM $this->tableName WHERE $this->where";
			return $this->db->batchexe($sql, $params);
		}
	}
	
	/**
	 * 确定用户权限 未添加
	 */
	public function user() {
		session_start();
		if($_SESSION['user'] != 'admin') {
			$this->con = null;
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * Transaction
	 */
	public function beginTransaction() {
		return $this->db->beginTransaction();
	}
	public function commit() {
		return $this->db->commit();
	}
	public function rollback() {
		return $this->db->rollback();
	}
}
