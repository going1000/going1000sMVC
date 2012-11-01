<?php
/**
 * @author     going1000 <miaorenjin@gmail.com>
 * @copyright  Copyright (c) 2012
 * @version    $Id$
 */

/*
 * 队列模式，从左边push进队列，从右边pop出队列
 */
class GQueue{
	/**
	 * Enter description here ...
	 * @var Redis
	 */
	private $queue = '';   
	
	private static $instance = null;
	
	const DEFAULT_CHANNEL = 'DEFAULT_CHANNEL';
	/**
	 * Enter description here ...
	 * @param unknown_type $host
	 * @param unknown_type $port
	 * @throws GException
	 * @return void
	 */
	private function __construct($host = MQ_HOST, $port = MQ_PORT) {
		$this->queue = new Redis();
		if(!$this->queue->connect($host, $port)) {
			throw new GException("cannot connect to MQ server", "cannot connect to MQ server $host:$port");
		}
	}
	/**
	 * create new instance of the class
	 * @return GQueue
	 */
	static public function getInstance($host = MQ_HOST, $port = MQ_PORT) {
		if(is_null(self::$instance)) {
			self::$instance = new GQueue($host, $port);
		}
		return self::$instance;
	}
	/**
	 * push in queue by left side
	 * @param string $item
	 * @param string $channel
	 * @return void
	 */
	public function push($item, $channel = self::DEFAULT_CHANNEL) {
		return $this->queue->lPush($channel, $item);
	}
	/**
	 * pop out from right of queue
	 * @param unknown_type $channel
	 * @return string|flase if command executed successfully BOOL FALSE in case of failure (empty list)
	 */
	public function pop($channel = self::DEFAULT_CHANNEL) {
		return $this->queue->rPop($channel);
	}
}
