<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 大数据分析专用 - 用户画像 v1.0
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-07-28
 ****************************************************************/
set_time_limit(0);
class Userportrait_manage extends MY_Controller
{
	private $data = array();

	public function __construct()
	{
		parent::__construct();
	}

   /**
	 * 用户基础数据生成脚本(用于生成原始数据)
	 * 执行 sudo /usr/local/php/bin/php index.php _shell Userportrait_manage baseUserInfo [起始条数] [获取条数]
	 * 
	 * @return void
	 */
	public function baseUserInfo($start = 0, $endlimit = 10)
	{
		$this->load->library(array('Cnfol_file','Cnfol_iplocation'));

		$this->load->model('Passport_manage_mdl');

		runTime('begin');

		$where['offset']  = $start;
		$where['limit']   = $endlimit;
		$where['iscount'] = 1;

		$data = $this->Passport_manage_mdl->getUserList($where);
$t1 = microtime(true);
		$userid = array();

		foreach($data['list'] as $rs)
		{
			$user = array();

			$user['userid']   = $rs['UserID'];
			/* 注册时间 */
			$user['regtime']  = $rs['RegTime'];
			/* 用户名称 */
			$user['username'] = $rs['UserName'];
			/* 用户呢称 */
			$user['usernick'] = $rs['NickName'];
			/* 更新时间 */
			$user['uptime']   = date('Y-m-d H:i:s');
			/* 默认活跃等级 */
			$user['activedegree'] = 5;

			if(!empty($rs['Mobile']) && preg_match('#^1[34578]{1}\d{9}$#', $rs['Mobile']))
			{
				$mobilekeys = substr($rs['Mobile'], 0, 7);

				$mobileinfo = $this->cnfol_file->get($mobilekeys, 'phone');

				if(!empty($mobileinfo))
					$user['address'] = ($mobileinfo['province'] == $mobileinfo['city']) ? $mobileinfo['province'] : $mobileinfo['province'] . $mobileinfo['city'];
			}
			$date = $iptmp = array();
			/* 
				取最近一年登录记录按以下规则进行处理
				1.最近一月每天都有登录的用户为1级
				2.最近3个月登录天数超过30天的为2级
				3.最近半年登录天数超过30天的为3级
				4.最近一年登录天数超过30天的为4级
				5.一年内登陆天数低于30天的为5级
			 */
			$where['days']   = 360;
			$where['userid'] = $rs['UserID'];

			$userLogIn = $this->Passport_manage_mdl->getUserLoginLog($where);
			
			if($userLogIn['total'] > 0)
			{
				$lately30day  = date('Ymd', strtotime('-30 day'));
				$lately90day  = date('Ymd', strtotime('-90 day'));
				$lately180day = date('Ymd', strtotime('-180 day'));
				$lately360day = date('Ymd', strtotime('-360 day'));

				$lately30daycount = $lately90daycount = $lately180daycount = $lately360daycount = 0;

				foreach($userLogIn['list'] as $rs)
				{
					/* 汇总IP地址做为归属地判断 */
					if(isset($iptmp[$rs['LoginIP']]))
						$iptmp[$rs['LoginIP']] += 1;
					else
						$iptmp[$rs['LoginIP']]  = 1;

					if($lately30day < $rs['LoginTime'])
					{
						$lately30daycount++;
					}
					if($lately90day < $rs['LoginTime'])
					{
						$lately90daycount++;
					}
					if($lately180day < $rs['LoginTime'])
					{
						$lately180daycount++;
					}
					if($lately360day < $rs['LoginTime'])
					{
						$lately360daycount++;
					}
				}
					
				if($lately30daycount == 30)
				{
					$user['activedegree'] = 1;
				}
				elseif($lately90daycount > 30)
				{
					$user['activedegree'] = 2;
				}
				elseif($lately180daycount > 30)
				{
					$user['activedegree'] = 3;
				}
				elseif($lately360daycount > 30)
				{
					$user['activedegree'] = 4;
				}
			}
			$ips = array();
			/* 获取IP所属地区 */
			if(empty($user['address']))
			{
				!empty($iptmp) ? rsort($iptmp) : '';
				$ips = array_keys($iptmp);
				/* 没有登录IP的话先获取注册IP */
				$ips = !empty($ips) ? $ips[0] : $rs['RegIP'];

				$ipinfo = $this->cnfol_iplocation->getAddRess($ips);
				$user['address'] = ($ipinfo['province'] == $ipinfo['city']) ? $ipinfo['province'] : $ipinfo['province'] . $ipinfo['city'];
			}
			/* 将用户属性添加进缓存列表中 */
			//$userid[] = $rs['UserID'];
			$lastid = $this->Passport_manage_mdl->insertUserInfo($user);
		}
		unset($user, $data);
$t2 = microtime(true);
echo 'runtime:' . round($t2 - $t1, 3). PHP_EOL;
	}

   /**
	 * 用户基础数据更新脚本(用于更新数据)
	 * 执行 sudo /usr/local/php/bin/php index.php _shell Userportrait_manage baseUserInfo [起始条数] [获取条数] [最大条数]
	 * 
	 * @return void
	 */
	public function updateBaseUserInfo($start = 0, $endlimit = 50000, $maxlimit = 1000000)
	{
		$this->load->library(array('Cnfol_file','Cnfol_iplocation'));

		$this->load->model('Passport_manage_mdl');

		$where['offset']  = $start;
		$where['limit']   = $endlimit;
		$where['iscount'] = 0;

		$forcount = ceil($maxlimit / $where['limit']);
$t1 = microtime(true);
		//$userid = array();
		while($forcount > 0)
		{
			$data = $this->Passport_manage_mdl->getUserList($where);

			foreach($data['list'] as $rs)
			{
				$user = array();

				$user['userid']   = $rs['UserID'];
				/* 注册时间 */
				$user['regtime']  = $rs['RegTime'];
				/* 用户名称 */
				$user['username'] = $rs['UserName'];
				/* 用户呢称 */
				$user['usernick'] = $rs['NickName'];
				/* 更新时间 */
				$user['uptime']   = date('Y-m-d H:i:s');
				/* 默认活跃等级 */
				$user['activedegree'] = 5;

				if(!empty($rs['Mobile']) && preg_match('#^1[34578]{1}\d{9}$#', $rs['Mobile']))
				{
					$mobilekeys = substr($rs['Mobile'], 0, 7);

					$mobileinfo = $this->cnfol_file->get($mobilekeys, 'phone');

					if(!empty($mobileinfo))
						$user['address'] = ($mobileinfo['province'] == $mobileinfo['city']) ? $mobileinfo['province'] : $mobileinfo['province'] . $mobileinfo['city'];
				}
				$date = $iptmp = array();
				/* 
					取最近一年登录记录按以下规则进行处理
					1.最近一月每天都有登录的用户为1级
					2.最近3个月登录天数超过30天的为2级
					3.最近半年登录天数超过30天的为3级
					4.最近一年登录天数超过30天的为4级
					5.一年内登陆天数低于30天的为5级
				 */
				$where['days']   = 360;
				$where['userid'] = $rs['UserID'];

				$userLogIn = $this->Passport_manage_mdl->getUserLoginLog($where);
				
				if($userLogIn['total'] > 0)
				{
					$lately30day  = date('Ymd', strtotime('-30 day'));
					$lately90day  = date('Ymd', strtotime('-90 day'));
					$lately180day = date('Ymd', strtotime('-180 day'));
					$lately360day = date('Ymd', strtotime('-360 day'));

					$lately30daycount = $lately90daycount = $lately180daycount = $lately360daycount = 0;

					foreach($userLogIn['list'] as $rs)
					{
						/* 汇总IP地址做为归属地判断 */
						if(isset($iptmp[$rs['LoginIP']]))
							$iptmp[$rs['LoginIP']] += 1;
						else
							$iptmp[$rs['LoginIP']]  = 1;

						if($lately30day < $rs['LoginTime'])
						{
							$lately30daycount++;
						}
						if($lately90day < $rs['LoginTime'])
						{
							$lately90daycount++;
						}
						if($lately180day < $rs['LoginTime'])
						{
							$lately180daycount++;
						}
						if($lately360day < $rs['LoginTime'])
						{
							$lately360daycount++;
						}
					}
						
					if($lately30daycount == 30)
					{
						$user['activedegree'] = 1;
					}
					elseif($lately90daycount > 30)
					{
						$user['activedegree'] = 2;
					}
					elseif($lately180daycount > 30)
					{
						$user['activedegree'] = 3;
					}
					elseif($lately360daycount > 30)
					{
						$user['activedegree'] = 4;
					}
				}
				$ips = array();
				/* 获取IP所属地区 */
				if(empty($user['address']))
				{
					!empty($iptmp) ? rsort($iptmp) : '';
					$ips = array_keys($iptmp);
					/* 没有登录IP的话先获取注册IP */
					$ips = !empty($ips) ? $ips[0] : $rs['RegIP'];

					$ipinfo = $this->cnfol_iplocation->getAddRess($ips);
					$user['address'] = ($ipinfo['province'] == $ipinfo['city']) ? $ipinfo['province'] : $ipinfo['province'] . $ipinfo['city'];
				}
				/* 将用户属性添加进缓存列表中 */
				//$userid[] = $rs['UserID'];
				$lastid = $this->Passport_manage_mdl->insertUserInfoPlus($user);
			}
			unset($user, $data);
			$where['offset'] += $where['limit'];
			$forcount--;			
		}
$t2 = microtime(true);
echo 'runtime:' . round($t2 - $t1, 3). PHP_EOL;
	}

   /**
	 * 获取话务系统用户提问数据
	 * 执行 sudo /usr/local/php/bin/php index.php _shell Userportrait_manage baseUserInfo [起始条数] [获取条数]
	 * 
	 * @return void
	 */
	public function phoneService($offset = 0, $limit = 1000)
	{
		$this->load->library('Cnfol_file');

		$this->load->model(array('Other_manage_mdl', 'Passport_manage_mdl'));
		
		$where['offset']  = $offset;
		$where['limit']   = $limit;
		$where['iscount'] = 1;

		$data = $this->Other_manage_mdl->getUserQuestion($where);

		$userlist = array();
$t1 = microtime(true);
		if(isset($data['total']) && $data['total'] > 0)
		{
			foreach($data['list'] as $rs)
			{
				if(empty($rs['commonproblem'])) continue;
				
				$where['username'] = $rs['username'];
				$userinfo = $this->Passport_manage_mdl->getUserList($where);

				if($userinfo['total'] > 0)
				{
					$userid = $userinfo['list'][0]['UserID'];
					$user = $this->cnfol_file->get('passportuserinfo_' . $userid, 'passportuserinfo');

					$keys = $rs['product'] . ':' . $rs['commonproblem'];

					if(isset($user[$userid]['phoneservice_keyword'][$keys]))
						$user[$userid]['phoneservice_keyword'][$keys] += 1;
					else
						$user[$userid]['phoneservice_keyword'][$keys]  = 1;
					
					$this->cnfol_file->set('passportuserinfo_' . $userid, $user, 'passportuserinfo', 0);
				}
			}
		}
$t2 = microtime(true);
echo 'runtime:' . round($t2 - $t1, 3). PHP_EOL;
	}

	public function test2()
	{
		$this->load->library('Cnfol_file');

		$user = array('username'=>'llslovelf','usernick'=>'llslovelf');
		for($i=0;$i<100;$i++)
		{
$t1 = microtime(true);
			$this->cnfol_file->set('passportuserinfo_345147', $user, 'passportuserinfo', 0);
$t2 = microtime(true);
echo 'runtime:' . round($t2 - $t1, 4). PHP_EOL;
		}

	}
}

/* End of file Userportrait_manage.php */
/* Location: ./application/controllers/_shell/Userportrait_manage.php */