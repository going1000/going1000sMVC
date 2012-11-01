<?php
/**
 * @author     going1000 <miaorenjin@gmail.com>
 * @copyright  Copyright (c) 2012
 * @version    $Id$
 */

/**
 * The kernel of the framework which holds all available resource
 */
class GKernel{
	private $mdb = null;//主db，用于insert update delete
//	private $sdb = null;//副db，用于select
	public function __construct() {

	}
	
	function getMDBHandler($initialize = true) {
        if (empty($this->mdb) && $initialize) {
			$this->mdb = GDatabase::getInstance(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT, DB_TYPE, DB_CONNECT);
        }
        return $this->mdb;
    }


}