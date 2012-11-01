<?php
// http://localhost/myApp/index.php/myTest/test
class MyTest extends GBaseCtrl {
	
	public function __construct() {
		parent::__construct('MyTest');
	}
	
	function test() {
		debug(json_decode(json_encode(array('s' => true)), true), false);
	}
}