<?php
/**
 * @author     going1000 <miaorenjin@gmail.com>
 * @copyright  Copyright (c) 2012
 * @version    $Id$
 * 
 * PDO layer
 */

class GDatabase {
//	$GLOBALS['log'] = array(); //log
	public $log = array();  //log
	public $pool = array();  //数据库连接池
	/**
	 * PDO singleton instance
	 * @var PDO
	 */
	private $dbh = ''; 
	/**
     * @var PDOStatement
     */	
	private $stmt = ''; //pdo stmt
	private static $instance = null; //GDatabase instance
	
	/**
     * @param $driver database platform (e.g. mysql, sqlserver, oracle)
     * @param $username database user name
     * @param $password database user password
     * @param $hostname database host
     * @param $database database name
     * @param $pconnect whether to use pconnect or connect
     */
	function __construct($host, $user, $pwd, $dbName, $port=3306, $dbms ='mysql', $pconnect = false) {
		$dsn = "$dbms:host=$host;dbname=$dbName";
		try {
		    $this->dbh = new PDO($dsn, $user, $pwd); //初始化一个PDO对象，就是创建了数据库连接对象$dbh
		    $this->pool[] = $this;
		} catch (PDOException $e) {
		    die ("Error!: " . $e->getMessage() . "<br/>");
		}
	}
	/**
	 * @see GDatabase::__construct()
     */
	public static function getInstance($host, $user, $pwd, $dbName, $port=3306, $dbms ='mysql', $pconnect = false) {
		if(self::$instance == null) {
			self::$instance = new GDatabase($host, $user, $pwd, $dbName, $port, $dbms, $pconnect);
		}
		return self::$instance;
	}
	/**
	 * @param string $sql
	 * @param array $params
	 */
	public function execute($sql = '',$params = Null) {
		if(!empty($sql)) {
//			$this->logstart($sql = '',$params);
			$this->stmt = $this->dbh->prepare($sql);
			$result = is_null($params) ? $this->stmt->execute() : $this->stmt->execute($params);
//			$this->logend();
     		if($result !== false) {
				return $this->stmt->rowCount();
			} else {
				throw new SqlException(LG_EXECUTE_FAILED, $this->err(), $sql, $params);
			}
		}	
	}
	
	/**
	 * db type must be InnoDB but My...
	 * $params: not null array(1 => array('id'=>1, 'value'=>one),
	 * 						   2 => array('id'=>2, 'value'=>two),
	 * 						)
	 */
	public function batchexe($sql = '',$params) {
		echo $sql;
		echo '<br />';
		var_dump($params);
		$this->stmt = $this->dbh->prepare($sql);
		if(!empty($params)) {
			$this->beginTransaction();
			foreach ($params as $v) {
				$result[] = $this->stmt->execute($v);
			}
			
			$rs = true;
			foreach ($result as $v) {
				$rs = $v !== false && $rs;
			}

			if($rs !== false) {
				$this->commit();
			    return $this->stmt->rowCount();   //return后结束（跳出函数）
			} else {
				$this->rollback();
			}
		}
	}
	
	public function query($sql, $params=NULL) {
		if(!empty($sql)) {
//			$this->logstart($sql = '',Null);
			$this->stmt = $this->dbh->query($sql);
			 $result = is_null($params) ? $this->stmt->execute() : $this->stmt->execute($params);
//			$this->logend();
			if ( false !== $result ) {
	            return $this->stmt;
	        } else {
	            $this->log[$this->current]['err'] = $this->err();
	            throw new SqlException(LG_QUERY_FAILED, $this->err(), $sql, $params);
	        }
		}	
	}
	
	/**
	 * log(记录)
	 * 用prepare每次操作数据库后自动执行，将记录插入表log中
	 */
	private function logstart($sql,$params) {
		$GLOBALS['log']['sql'] = $sql;
		$GLOBALS['log']['params'] = $params;
		$GLOBALS['log']['processtime'] = microtime(true);
	}
	private function logend() {
		$GLOBALS['log']['processtime'] = microtime(true) - $GLOBALS['log']['processtime'];
	}
	
	private function err() {
		$err = $this->stmt->errorInfo();
		return $err[0] .' : '. $err[1] .' : '. $err[2];
	}	
	
	private function resetstmt() {
		$this->stmt = '';
	}
	
	/**
	 * PDO Transaction
	 */
	public function beginTransaction() {
		return $this->dbh->beginTransaction();
	}
	public function commit() {
		return $this->dbh->commit();
	}
	public function rollback() {
		return $this->dbh->rollback();
	}
	
	/**
	 * close cursor for stmt
	 */
//	public function closecursor() {
////		return $this->
//	}
	/**
     * get last insert id
     * 
     * @return last insert id
     */    
    function lastInsertId() {
        return $this->dbh->lastInsertId();
    }
}
?>