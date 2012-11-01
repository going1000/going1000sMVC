<?php
class test extends GBaseCtrl {
	const CHANNEL = 'test_channel';
	
	public function __construct() {
		parent::__construct('test');
	}
	
	public function index() {
	}
	public function test() {
		debug($_SERVER);
		debug($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	}
	public function cry() {
		$dao = new TestDao();
		$rs = $dao->insert(array('info' => '1111222'));
		if($rs === false) {
			echo '出错';
		} else {
			debug($rs);
		}
	}
	
	public function queueTest() {
		$queue = GQueue::getInstance();
		$queue->push('111', self::CHANNEL);
	}
	
	
	public function sphinxTest() {
		Load::helper('sphinxapi');
		
		$keyword = 'Kingston西东部';
		$cl = new SphinxClient();
		$cl->SetServer('127.0.0.1', 9312);
		$index1 = 'categoryId2936';
		$index2 = 'categoryId2937';
//		$cl->SetLimits(($cur-1)*$size, $size);
		$cl->SetMatchMode(SPH_MATCH_EXTENDED2);
// 		$cl->SetSortMode(SPH_SORT_RELEVANCE);
		$cl->SetFieldWeights(array('keyword'=>30,'productStandard'=>5,'productName'=>10));
//		$cl->SetSortMode(SPH_SORT_EXTENDED, 'canCompare DESC, @relevance DESC, times DESC');
//		$cl->AddQuery($keyword, $index1);
//		$cl->AddQuery($keyword, $index2);
//		$res = $cl->RunQueries ();
		$cl->SetIndexWeights(array(
			'categoryId2936' => 10,
			'categoryId2937' => 20
		));
		debug($cl->BuildKeywords($keyword, 'categoryId2937', true));
		$res = $cl->Query($keyword, 'categoryId2936, categoryId2938');
debug($res);
		if (false === $res) return -2;//sphinx错误	
	}
}
function setSign($param) {
        ksort($param);
        $str = '';
        foreach($param as $k=>$v){
                if(is_array($v)){
                        $str .= json_encode($v);
                }else {
                        $str .= $v;
                }
        }
        $sign = md5($str.'Iclouds.is.very_G00d');
        $param['sign'] = $sign;
        return $param;
}
