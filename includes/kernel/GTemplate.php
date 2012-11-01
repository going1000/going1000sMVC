<?php
class GTemplate{
	private $assignValues = '';   //传进来的参数
	private $assignName   = '';   //传进来的名称
	
	private $templatepath = '';   //模板路径
	
	
	public function __construct() {
		
	}
	
	/*
	 * $assignValues: array('key'=>'value',)
	 * $assignName: string
	 */
	public function assign($assignName,$assignValues) {
		$this->assignValues = $assignValues;
		$this->assignName = $assignName;		
		return $this;
	}
	
	public function display() {
		${$this->assignName} = $this->assignValues;
		if(!empty($this->templatepath)) {
			include $this->templatepath;  //将模板(html代码)包含进来
		} else {
			echo '路径未设置';
		}
	}
	
	public function setfile($path) {
		if(!empty($path)){
			$this->templatepath = G_TEMPLATE_PATH.'/'.lcfirst($path).'.php';
		} 
		return $this;
	}
}
?>
