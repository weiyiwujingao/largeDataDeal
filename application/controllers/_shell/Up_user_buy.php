<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 大数据分析专用 - 用户画像 v1.0
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-07-28
 ****************************************************************/
set_time_limit(0);
class Up_user_buy extends MY_Controller
{
	private $data = array();

	public function __construct()
	{
		parent::__construct();
	}

	//检查更新圈子 
	public function upQZ(){
		runTime('begin');
		$log_path = APPPATH .'cache/userbuy/upQZ.log';
		if (!file_exists($log_path)) {
				fclose(fopen($log_path, "wb"));
			}
		//获取上次更新的交易ID
		$orderID = file_get_contents($log_path);
		//$t2 = microtime(true);
		$orderID = $orderID?$orderID:'5420728';
		$this->load->model('Read_buy_mdl');
		$List = $this->Read_buy_mdl->getOrderQ($orderID);
		if(empty($List['list'])) exit;			
		file_put_contents($log_path,$List['list'][0]['OrderID']);
		// var_dump($List);exit;
		foreach($List['list'] as $rs ){
				$user = array();
				$user['userid'] = $rs['UserID'];
				/*鲜花数量跟平均数*/
				$user['avgflowe'] = $this->Read_buy_mdl->getAvg($user['userid']);
				// var_dump($user);exit;
				/*获取充值总金额*/
				$user['oldmoney'] = $this->Read_buy_mdl->getQSum($user['userid']);
				$orderSryle = $this->Read_buy_mdl->getQStyle($user['userid']);
				// var_dump($user);exit;
				$user['order'] = '';
				if(!empty($orderSryle)){
					foreach($orderSryle as $rs ){
					$user['order'] = $user['order'].'name:'.trim($rs['gName']).'style:'.trim($rs['gStyle']).'type:'.trim($rs['gType']).',';
					}
				}	
				// echo $user['userid'];exit;
				$Tw = $this->Read_buy_mdl->getTW($user['userid']);
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
					$interval = $this->Read_buy_mdl->getQInterval($user['userid']);
					// var_dump($interval);exit;
					$user['maxprice'] = $interval['Max'];
					$user['minprice'] = $interval['Min'];
					/*支付成功率*/
					$user['success'] = $this->Read_buy_mdl->getQSuccess($user['userid']);				
				}else{
					$user['oldmoney'] = 0;
					$user['maxprice'] = 0;
					$user['minprice'] = 0;
					$user['success'] = 0;
				}
				$user['type'] = 2;
			$flag = $this->Read_buy_mdl->checkQ($user['userid']);
			// var_dump($user);exit;
			if(!empty($flag)){
				$user['id'] = $flag[0]['id'];
				// var_dump($user);exit;
				$this->Read_buy_mdl->upLoadQZ($user);
				echo '更新成功'.PHP_EOL;
			}else{
				// var_dump($user);exit;
				$this->Read_buy_mdl->insertUserBuy($user);
				echo '添加成功'.PHP_EOL;
			}		
		}		
		
	}
	//更新财视 充值总金额 价格区间
	public function upCS(){
		// echo APPPATH;exit;
		runTime('begin');
// $t1 = microtime(true);
		$log_path = APPPATH .'cache/userbuy/upCS.log';
		if (!file_exists($log_path)) {
				fclose(fopen($log_path, "wb"));
			}		
		$orderID = file_get_contents($log_path);
		//$t2 = microtime(true);
		$orderID = $orderID?$orderID:'37928';
		$this->load->model('Read_buy_mdl');
		$List = $this->Read_buy_mdl->getOrderC($orderID);
		if(empty($List['list'])) exit;	
		file_put_contents($log_path,$List['list'][0]['OrderID']);
		$wordapi2 = WORDAPI2;
		// var_dump($List);exit;
		foreach($List['list'] as $rs ){
			// var_dump($rs);exit;
			$user = array();
			$user['userid'] = $rs['UserID'];
			/*鲜花数量跟平均数*/
			$user['avgflowe'] = $this->Read_buy_mdl->getAvg($user['userid']);
			/*获取充值总金额*/
			$user['oldmoney'] = $this->Read_buy_mdl->getSum($user['userid']);
			$order = $this->Read_buy_mdl->getOrder($user['userid']);
			$user['order']='';
			// var_dump($user);exit;
			if(!empty($order)){
				foreach($order as $res){

				$user['order'] = $user['order'].$res['order'].',';
				}
			}			
			$Dt = $this->Read_buy_mdl->getDT($user['userid']);
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
				$interval = $this->Read_buy_mdl->getInterval($user['userid']);
				// var_dump($interval);exit;
				$user['maxprice'] = $interval['Max'];
				$interval['Min'] > 0 ? $user['minprice'] = $interval['Min'] : $user['minprice'] = 0;
				/*支付成功率*/
				$user['success'] = $this->Read_buy_mdl->getSuccess($user['userid']);				
			}else{
				$user['oldmoney'] = 0;
				$user['maxprice'] = 0;
				$user['minprice'] = 0;
				$user['success'] = 0;
			}
			$user['type'] = 1;
			$flag = $this->Read_buy_mdl->checkC($user['userid']);
			// var_dump($user);exit;
			if(!empty($flag)){
				$user['id'] = $flag[0]['id'];
				$this->Read_buy_mdl->upLoadCS($user);
				echo '更新成功'.PHP_EOL;
			}else{
				$this->Read_buy_mdl->insertUserBuy($user);
				echo '添加成功'.PHP_EOL;
			}		
			// var_dump($user);exit;
		}
	}

	//获取埋点个人喜好股票
	public function upStock(){
		// echo APPPATH;exit;
		runTime('begin');
// $t1 = microtime(true);
		$log_path = APPPATH .'cache/userbuy/upStock.log';
		if (!file_exists($log_path)) {
				fclose(fopen($log_path, "wb"));
			}		
		$orderID = file_get_contents($log_path);
		//$t2 = microtime(true);
		$orderID = $orderID?$orderID:'732';
		$this->load->model('Read_buy_mdl');
		$List = $this->Read_buy_mdl->getStockUserList($orderID);
		if(empty($List)) exit;	
		// var_dump($List);exit;
		file_put_contents($log_path,$List[0]['RecordId']);	
		foreach($List as $rs ){
			// t($rs);
			$user = array();
			$user['stock'] = '';
			$user['userid'] = $rs['UserId'];
			$data = $this->Read_buy_mdl->getUserStock($rs['UserId']);
			if(!empty($data[0]['OutUrl'])){
				foreach($data as $res){
					$num = preg_match('/002[\d]{3}|000[\d]{3}|300[\d]{3}|600[\d]{3}|60[\d]{4}/', $res['OutUrl'], $matches);
					if($num > 0){
						$user['stock'] = $user['stock'].$matches[0].',';
					}
				}
			}
			if(!empty($user['stock'])){
				$flag = $this->Read_buy_mdl->checkStock($user['userid']);
				// var_dump($flag);exit;
				if(!empty($flag)){
					// $user['id'] = $flag[0]['id'];
					$this->Read_buy_mdl->upLoad($user);
					echo '更新成功'.PHP_EOL;
				}else{
					$this->Read_buy_mdl->insertUserBuy($user);
					echo '添加成功'.PHP_EOL;
				}		
				// echo $user['userid'].PHP_EOL.'股票代码'.$user['stock'].PHP_EOL;
			}
			echo '没有股票信息'.PHP_EOL;
			// var_dump($user);exit;
		}
		// if(empty($List)) exit;	
		// file_put_contents($log_path,$List['list'][0]['OrderID']);
		// var_dump($List);exit;
		
	}
	
}

/* End of file Userportrait_manage.php */
/* Location: ./application/controllers/_shell/Userportrait_manage.php */