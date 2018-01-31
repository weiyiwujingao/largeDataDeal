<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 大数据分析专用 - 用户画像 v1.0
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-07-28
 ****************************************************************/
set_time_limit(0);
class Userportrait_buy extends MY_Controller
{
	private $data = array();

	public function __construct()
	{
		parent::__construct();
	}

   /**
	 * 分析用户登录日志,生成用户画像属性
	 * 
	 * @return void
	 */
	public function caiShi()
	{
		$this->load->library(array('Cnfol_file','Cnfol_iplocation'));

		$this->load->model('Passport_buy_mdl');
		runTime('begin');
		$log_path = APPPATH .'cache/userbuy/caishi.log';
		if (!file_exists($log_path)) {
				fclose(fopen($log_path, "wb"));
			}
		
		$userid = file_get_contents($log_path);
		$userid = $userid?$userid:'8365674';

		$id=$this->Passport_buy_mdl->getMaxID('1');		
		$data = $this->Passport_buy_mdl->getUserList($id);
		$wordapi2 = WORDAPI2;

// $t1 = microtime(true);
			foreach($data['list'] as $rs)
			{
				/* 遍历缓存获取用户列表 */
				// $user = $this->cnfol_file->get('passportuser_' . $rs['UserID'], 'passportuser');
				$user = array();
				$user['userid'] = $rs['UserID'];
				/*鲜花数量跟平均数*/
				$user['avgflowe'] = $this->Passport_buy_mdl->getAvg($rs['UserID']);
				/*获取充值总金额*/
				$user['oldmoney'] = $this->Passport_buy_mdl->getSum($rs['UserID']);
				$order = $this->Passport_buy_mdl->getOrder($rs['UserID']);
				$user['order']='';
				foreach($order as $res){

					$user['order'] = $user['order'].$res['order'].',';
				}
				$Dt = $this->Passport_buy_mdl->getDT($rs['UserID']);
				if(!empty($Dt)){

					$user['content']='';
					foreach($Dt as $rest){			
					// 过滤取关键字
					$rest['Content'] = preg_replace('#[^\x{4e00}-\x{9fa5}]#u', '',  $rest['Content']);
					$rest['Content'] = trim($rest['Content']);

					if(empty($rest['Content'])) continue;
					/* 关键词提取 */
		            $url = sprintf($wordapi2, $rest['Content']);
		        
		            $rsp = curl_get($url);
	         
		            if($rsp['code'] != 200 || empty($rsp['data']))
		            {
		                logs('分词接口请求失败,rsp:'.$rsp['data'].',httpcode:'.$rsp['code'], 'caiShi');
		                continue;
		            }


					$user['content'] = $user['content'].trim($rsp['data']).',';
					}

				}else{
					$user['content']='';
				}				
				
				if($user['oldmoney'] > 0){
					/*价格区间*/
					$interval = $this->Passport_buy_mdl->getInterval($rs['UserID']);
					// var_dump($interval);exit;
					$user['maxprice'] = $interval['Max'];
					$interval['Min'] > 0 ? $user['minprice'] = $interval['Min'] : $user['minprice'] = 0;
					/*支付成功率*/
					$user['success'] = $this->Passport_buy_mdl->getSuccess($rs['UserID']);				
				}else{
					$user['oldmoney'] = 0;
					$user['maxprice'] = 0;
					$user['minprice'] = 0;
					$user['success'] = 0;
				}
				$user['type'] = 1;
				$this->Passport_buy_mdl->insertUserBuy($user);	
				file_put_contents($log_path,$rs['UserID']);			
			}

// $t2 = microtime(true);

// echo 'runtime:' . round($t2 - $t1, 3). PHP_EOL;
	}
	//圈子
	public function quanZi()
		{
			$this->load->library(array('Cnfol_file','Cnfol_iplocation'));

			$this->load->model('Passport_buy_mdl');
			$log_path = APPPATH .'cache/userbuy/QZ'.date('Ymd').'.log';
			if (!file_exists($log_path)) {
					fclose(fopen($log_path, "wb"));
				}
			runTime('begin');
			$id=$this->Passport_buy_mdl->getMaxID('2');
			$data = $this->Passport_buy_mdl->getQzList($id);
				foreach($data['list'] as $rs)
				{
					/* 遍历缓存获取用户列表 */
				$user = array();
				$user['userid'] = $rs['UserID'];
				/*鲜花数量跟平均数*/
				$user['avgflowe'] = $this->Passport_buy_mdl->getAvg($rs['UserID']);
				/*获取充值总金额*/
				$user['oldmoney'] = $this->Passport_buy_mdl->getQSum($rs['UserID']);
				$datao = $this->Passport_buy_mdl->getQStyle($rs['UserID']);
				// var_dump($data);exit;
				$order = '';
				foreach($datao as $rs ){
					$order = $order.'name:'.trim($rs['gName']).'style:'.trim($rs['gStyle']).'type:'.trim($rs['gType']).',';
				}
				$user['order']=$order;
				// echo $user['userid'];exit;
				$Tw = $this->Passport_buy_mdl->getTW($user['userid']);
				if(!empty($Tw)){
					$user['content']='';
					foreach($Tw as $rest){
					// 过滤取关键字
					$rest['Content'] = preg_replace('#[^\x{4e00}-\x{9fa5}]#u', '',  $rest['Content']);
					$rest['Content'] = file_get_contents('http://bigdata.cnfol.com:5002/get_articletags?article='.trim($rest['Content']));
					$user['content'] = $user['content'].$rest['Content'].',';
					}
				}else{
					$user['content']='';
				}				
				
				if($user['oldmoney'] > 0){
					/*价格区间*/
					$interval = $this->Passport_buy_mdl->getQInterval($user['userid']);
					// var_dump($interval);exit;
					$user['maxprice'] = $interval['Max'];
					$user['minprice'] = $interval['Min'];
					/*支付成功率*/
					$user['success'] = $this->Passport_buy_mdl->getQSuccess($user['userid']);				
				}else{
					$user['oldmoney'] = 0;
					$user['maxprice'] = 0;
					$user['minprice'] = 0;
					$user['success'] = 0;
				}
				$user['type'] = 2;	
				$this->Passport_buy_mdl->insertUserBuy($user);
				// var_dump($user);
				}
			//logs("处理完毕{$data['Total']}篇文章,耗时:".runTime('begin', 'end', 4), 'articleMergeForCms');
		}
		public function quanZiNew()
		{
			$this->load->library(array('Cnfol_file','Cnfol_iplocation'));

			$this->load->model('Passport_buy_mdl');
			$log_path = APPPATH .'cache/userbuy/QZ'.date('Ymd').'.log';
			if (!file_exists($log_path)) {
					fclose(fopen($log_path, "wb"));
				}
			runTime('begin');
			$start  = intval($this->input->get_post('start', TRUE));
			$end = intval($this->input->get_post('end', TRUE));
			$go['start']=$start;
			$go['end']=$end;
			$data = $this->Passport_buy_mdl->getQzListNew($go);
			// var_dump($data);exit;
				foreach($data['list'] as $rs)
				{
					/* 遍历缓存获取用户列表 */
					// $user = $this->cnfol_file->get('passportuser_' . $rs['UserID'], 'passportuser');
				$user = array();
				$user['userid'] = $rs['UserID'];
				/*鲜花数量跟平均数*/
				$user['avgflowe'] = $this->Passport_buy_mdl->getAvg($rs['UserID']);
				/*获取充值总金额*/
				$user['oldmoney'] = $this->Passport_buy_mdl->getQSum($rs['UserID']);
				$datao = $this->Passport_buy_mdl->getQStyle($rs['UserID']);
				// var_dump($data);exit;
				$order = '';
				foreach($datao as $rs ){
					$order = $order.'name:'.trim($rs['gName']).'style:'.trim($rs['gStyle']).'type:'.trim($rs['gType']).',';
				}
				$user['order']=$order;
				// echo $user['userid'];exit;
				$Tw = $this->Passport_buy_mdl->getTW($user['userid']);
				if(!empty($Tw)){
					$user['content']='';
					foreach($Tw as $rest){
					// 过滤取关键字
					$rest['Content'] = preg_replace('#[^\x{4e00}-\x{9fa5}]#u', '',  $rest['Content']);
					$rest['Content'] = file_get_contents('http://bigdata.cnfol.com:5002/get_articletags?article='.trim($rest['Content']));
					$user['content'] = $user['content'].$rest['Content'].',';
					}
				}else{
					$user['content']='';
				}				
				
				if($user['oldmoney'] > 0){
					/*价格区间*/
					$interval = $this->Passport_buy_mdl->getQInterval($user['userid']);
					// var_dump($interval);exit;
					$user['maxprice'] = $interval['Max'];
					$user['minprice'] = $interval['Min'];
					/*支付成功率*/
					$user['success'] = $this->Passport_buy_mdl->getQSuccess($user['userid']);				
				}else{
					$user['oldmoney'] = 0;
					$user['maxprice'] = 0;
					$user['minprice'] = 0;
					$user['success'] = 0;
				}
				$user['type'] = 2;	
				// var_dump($user);exit;
				$this->Passport_buy_mdl->insertUserBuy($user);
				// var_dump($user);
				}
			//logs("处理完毕{$data['Total']}篇文章,耗时:".runTime('begin', 'end', 4), 'articleMergeForCms');
		}
	public function deleteRepeat(){
		$this->load->model('Passport_buy_mdl');
		$data = $this->Passport_buy_mdl->getRepeat();
		// var_dump($data);exit;
		foreach($data as $rs ){
			$this->Passport_buy_mdl->deleteRepeat($rs['id']);
		}
		exit;
	}
	//更新圈子 圈子名称 类型 所属风格
	public function uploadQZ(){
		runTime('begin');
		$log_path = APPPATH .'cache/uploadQZ.log';
		if (!file_exists($log_path)) {
				fclose(fopen($log_path, "wb"));
			}
		$upid = file_get_contents($log_path);
		//$t2 = microtime(true);
		$upid = $upid?$upid:'146';
		$this->load->model('Passport_buy_mdl');
		$UpLists = $this->Passport_buy_mdl->getUpList($upid);
		
		foreach($UpLists as $UpList ){
			$user = array();
			$data = $this->Passport_buy_mdl->getQStyle($UpList['userid']);
			// var_dump($data);exit;
			$order = '';
			foreach($data as $rs ){
				$order = $order.'name:'.trim($rs['gName']).'style:'.trim($rs['gStyle']).'type:'.trim($rs['gType']).',';
			}
			$interval = $this->Passport_buy_mdl->getUpInterval($UpList['userid']);
			$sum = $this->Passport_buy_mdl->getQSum($UpList['userid']);
			$user['success'] = $this->Passport_buy_mdl->getQSuccess($UpList['userid']);	
			$user['avgflowe'] = $this->Passport_buy_mdl->getAvg($UpList['userid']);
			$user['maxprice'] = $interval['Max'];
			$user['minprice'] = $interval['Min'];
			$user['oldmoney'] = $sum;
			$user['order'] = $order;
			$user['id'] = $UpList['id'];
			// var_dump($user);exit;
			$this->Passport_buy_mdl->upLoadQZ($user);
			// var_dump($user);exit;
			file_put_contents($log_path,$UpList['id']);
		}		
		
	}
	//更新财视 充值总金额 价格区间
	public function uploadCS(){
		// echo APPPATH;exit;
		runTime('begin');
// $t1 = microtime(true);
		$log_path = APPPATH .'cache/uploadCS.log';
		if (!file_exists($log_path)) {
				fclose(fopen($log_path, "wb"));
			}
		
		$upid = file_get_contents($log_path);
		//$t2 = microtime(true);
		$upid = $upid?$upid:'0';
		$this->load->model('Passport_buy_mdl');
		$UpLists = $this->Passport_buy_mdl->getUpCSList($upid);
		// var_dump($UpLists);exit;
		foreach($UpLists as $UpList ){
			$user = array();
			$data = $this->Passport_buy_mdl->getQStyle($UpList['userid']);
			$user['avgflowe'] = $this->Passport_buy_mdl->getAvg($UpList['userid']);
			/*获取充值总金额*/
			$user['oldmoney'] = $this->Passport_buy_mdl->getSum($UpList['userid']);
			if($user['oldmoney'] > 0){
					/*价格区间*/
					$interval = $this->Passport_buy_mdl->getInterval($UpList['userid']);
					// var_dump($interval);exit;
					$user['maxprice'] = $interval['Max'];
					$interval['Min'] > 0 ? $user['minprice'] = $interval['Min'] : $user['minprice'] = 0;
					/*支付成功率*/
					$user['success'] = $this->Passport_buy_mdl->getSuccess($UpList['userid']);				
				}else{
					$user['oldmoney'] = 0;
					$user['maxprice'] = 0;
					$user['minprice'] = 0;
					$user['success'] = 0;
				}
			$user['id'] = $UpList['id'];
			$this->Passport_buy_mdl->upLoadCS($user);
			file_put_contents($log_path,$UpList['id']);
		}
	}
	public function test2()
	{
		$this->load->library('Cnfol_file');

		$file = file(APPPATH . 'cache/mobile.txt');
		
		$data = array();

		foreach($file as $val)
		{
			$tmp = explode(',', $val);

			$data['city']  = $tmp[3];
			$data['phone'] = $tmp[1];
			$data['province'] = $tmp[2];
			$data['service']  = $tmp[4];
			$data['citycode'] = $tmp[5];
			$data['postcode'] = $tmp[6];

			$this->cnfol_file->set($tmp[1], $data, 'phone', 0);
		}
	}
}

/* End of file Userportrait_manage.php */
/* Location: ./application/controllers/_shell/Userportrait_manage.php */