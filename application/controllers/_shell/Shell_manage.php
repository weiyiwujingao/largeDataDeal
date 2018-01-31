<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 智能过滤系统 - 财视分析离线脚本
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-04-20
 ****************************************************************/
set_time_limit(0);
class Shell_manage extends MY_Controller
{
	private $data = array();

	public function __construct()
	{
		parent::__construct();
	}

   /**
	 * 财视文章内容分词,写入新表cs_keyword
	 * 
	 * @return void
	 */
	public function cwsForCs()
	{
		runTime('begin');

		$this->load->model('Cs_manage_mdl');

		$expert = $this->Cs_manage_mdl->getExpert();

		if(empty($expert)) exit(logs('获取专家信息为空', 'cws'));

		$where['userid'] = array_keys($expert);

		$data = $this->Cs_manage_mdl->getAllMsg($where);

		foreach($data as $rs)
		{
			$rs['Content'] = preg_replace('#[^\x{4e00}-\x{9fa5}]#u', '', $rs['Content']);
			$rs['Content'] = trim($rs['Content']);

			if(empty($rs['Content'])) continue;

			$cwsurl = sprintf(CWSAPI, urlencode($rs['Content']));

			$res = curl_get($cwsurl, 5);
			
			if($res['code'] == 200)
			{
				$keyword = array();

				$data = json_decode($res['data'], TRUE);
				
				foreach($data['List'] as $val)
					$keyword[] = $val['word'];
				
				if(empty($keyword)) continue;

				$insert['userid']  = $rs['UserID'];
				$insert['msgid']   = $rs['LiveMsgID'];
				$insert['keyword'] = join(',', $keyword);

				$insertid = $this->Cs_manage_mdl->insertKeyWord($insert);
				
				echo ($insertid > 0) ? '.' : '?';
			}
		}
	}

   /**
	 * CMS文章内容分词,写入新表cms_keyword
	 * 
	 * @return void
	 */
	public function cwsForCms()
	{
		runTime('begin');
		
		$this->load->model('Cms_manage_mdl');
		
		$where['offset'] = 0;
		$where['limit']  = 5000;
        
		$data = $this->Cms_manage_mdl->getArticle($where);

		if(empty($data)) exit(logs('获取文章数据为空', 'cwsforcms'));
        $ii = '1';
		foreach($data as $rs)
		{
			$rs['Content'] = preg_replace('#[^\x{4e00}-\x{9fa5}]#u', '', $rs['Title'] .' '. $rs['Source'] .' '. $rs['Content']);

			if(empty($rs['Content'])) continue;

			$cwsurl = sprintf(CWSAPI, urlencode($rs['Content']));

			$res = curl_get($cwsurl, 5);
			
			if($res['code'] == 200)
			{
				$keyword = array();

				$data = json_decode($res['data'], TRUE);
				
				foreach($data['List'] as $val)
					$keyword[] = $val['word'];

				$insert['msgid']   = $rs['ContId'];
				$insert['keyword'] = join(',', $keyword);

				$insertid = $this->Cms_manage_mdl->insertKeyWord($insert);
				
				echo ($insertid > 0) ? '.' : '?';

			}else{
				echo $ii.PHP_EOL;$ii++;
			}
		}
	}

   /**
	 * 关联相似cms文章
	 * 
	 * @return void
	 */
	public function relationArticle()
	{
		runTime('begin');
		
		$this->load->model('Cms_manage_mdl');
		
		$where['offset'] = 0;
		$where['limit']  = 5000;
        
		$data = $this->Cms_manage_mdl->getArticle($where);

		if(empty($data)) exit(logs('获取文章数据为空', 'cwsforcms'));
        $ii = '1';
		foreach($data as $rs)
		{
			$rs['Content'] = preg_replace('#[^\x{4e00}-\x{9fa5}]#u', '', $rs['Title'] .' '. $rs['Source'] .' '. $rs['Content']);

			if(empty($rs['Content'])) continue;

			$cwsurl = sprintf(CWSAPI, urlencode($rs['Content']));

			$res = curl_get($cwsurl, 5);
			
			if($res['code'] == 200)
			{
				$keyword = array();

				$data = json_decode($res['data'], TRUE);
				
				foreach($data['List'] as $val)
					$keyword[] = $val['word'];

				$insert['msgid']   = $rs['ContId'];
				$insert['keyword'] = join(',', $keyword);

				$insertid = $this->Cms_manage_mdl->insertKeyWord($insert);
				
				echo ($insertid > 0) ? '.' : '?';

			}else{
				echo $ii.PHP_EOL;$ii++;
			}
		}
	}

   /**
	 * 统计财视直播、动态内容中的黄金及股票关键词
	 * crontab 每天22:00生成一次缓存 目录在application/cache/keyword
	 * 
	 * @return void
	 */
	public function analysisCS()
	{
		runTime('begin');

		$this->load->model('Cs_manage_mdl');

		$this->load->library('cnfol_file');

		$expert = $this->Cs_manage_mdl->getExpert();

		if(empty($expert)) exit(logs('获取专家信息为空', 'analysisCS'));

		$offset = 0;
		$keyword = $users = array();
		$type = array('1'=>'stock','6'=>'gold');
echo 'UserTotal:'.count($expert).PHP_EOL;
		//$where['offset'] = $offset;
		//$where['limit']  = 50000;
		$where['userid'] = array_keys($expert);
		//$where['startdate'] = date('Y-m-d', strtotime('-7 days'));
		/* 目前暂时只根据所有专家的直播内容查询 */
		//$function = array('getDynamic', 'getLiveMsg');
		/* 遍历私信、直播、动态数据 */
		//foreach($function as $fun)
		//{
			$data = $this->Cs_manage_mdl->getAllMsg($where);
echo 'DataTotal:'.count($data).PHP_EOL;
			/* 统计所有关键词出现次数 */
			foreach($data as $rs)
			{
				$rs['Content'] = preg_replace('#[^\x{4e00}-\x{9fa5}]#u', '', urldecode($rs['Content']));
				$rs['Content'] = trim($rs['Content']);

				if(empty($rs['Content'])) continue;
				/* 获取内容分词 */
				$cwsurl = sprintf(CWSAPI, $rs['Content']);

				$res = curl_get($cwsurl, 5);

				if($res['code'] == 200)
				{
					$data = json_decode($res['data'], TRUE);

					if($data['Code'] == 200 && !empty($data['List']))
					{
						$class = '';
						/* 遍历文章关键词及累加统计 */
						foreach($data['List'] as $rs2)
						{
							$word  = trim($rs2['word']);
							$class = $type[$expert[$rs['UserID']]['labelid']];
							isset($users[$class][$word]) ? 
									$users[$class][$word]++ :
										$users[$class][$word] = 1;
						}
					}
					else
						logs('分词数据返回为空', 'analysisCS');
				}
				else
					logs($cwsurl.'|请求失败,httpcode='.$res['code'], 'analysisCS');
			}
		//}
		
		/* 去除交集部分 */
		$intersect = array_intersect_key($users['gold'], $users['stock']);

		if(!empty($intersect))
		{
			foreach($intersect as $key => $rs)
			{
				if(isset($users['gold'][$key])) unset($users['gold'][$key]);
				if(isset($users['stock'][$key])) unset($users['stock'][$key]);
			}
		}

		!empty($users['gold']) ? arsort($users['gold']) : '';
		!empty($users['stock']) ? arsort($users['stock']) : '';

		echo runTime('begin', 'end', 2);

		$this->cnfol_file->set('keyword', $users, 'keyword', 0);
		
		logs('直播关键词统计完成', 'analysisCS');
	}

   /**
	 * 根据关键词归类财视专家标签
	 * 每天早上09:30生成人工审核文本文件 晚上23:30通知财视更新
	 * 
	 * @return void
	 */
	public function analysisCS2()
	{
		runTime('begin');

		$this->load->model('Cs_manage_mdl');

		$this->load->library('cnfol_file');

		$expert = $this->Cs_manage_mdl->getExpert();

		if(empty($expert)) exit(logs('获取专家信息为空', 'analysisCS'));

		$offset = 0;
		$type   = array('1'=>'stock','6'=>'gold');
		$users  = $users1 = array();

		$keyword = $this->cnfol_file->get('keyword', 'keyword');

		//$where['offset'] = $offset;
		//$where['limit']  = 50000;
		$where['userid'] = array_keys($expert);
		//$where['startdate'] = date('Y-m-d', strtotime('-7 days'));
		
		//$function = array('getChat', 'getLiveMsg');
		/* 遍历直播数据 */
		//foreach($function as $fun)
		//{
			$data = $this->Cs_manage_mdl->getAllMsg($where);

			if(!empty($data))
			{
				/* 遍历所有关键词与专家文章做匹配 */
				foreach($data as $val)
				{
					//if($fun == 'getLiveMsg') $val['UserID'] = $val['ExpertID'];

					$users[$val['UserID']]['oldtag'] = $type[$expert[$val['UserID']]['labelid']];

					foreach($keyword['stock'] as $word => $rs)
					{
						if(FALSE === strpos(urldecode($val['Content']), $word))
							continue;
			
						if(isset($users[$val['UserID']]['stock']['score']))
							$users[$val['UserID']]['stock']['score'] += $rs;
						else
							$users[$val['UserID']]['stock']['score']  = $rs;
					}

					foreach($keyword['gold'] as $word => $rs)
					{
						if(FALSE === strpos(urldecode($val['Content']), $word))
							continue;

						if(isset($users[$val['UserID']]['gold']['score']))
							$users[$val['UserID']]['gold']['score'] += $rs;
						else
							$users[$val['UserID']]['gold']['score']  = $rs;
					}
				}
			//}

			if(empty($users)) exit(logs('分析数据为空', 'analysisCS2'));
			$str = '';
			$string = array();
			$rate = $rate2 = 0;
			foreach($users as $userid => $rs)
			{
				if(!isset($rs['gold']['score'])) $rs['gold']['score'] = 0;
				if(!isset($rs['stock']['score'])) $rs['stock']['score'] = 0;
				
				
				if($rs['gold']['score'] > $rs['stock']['score'])
				{
					$rate = round(($rs['gold']['score'] - $rs['stock']['score'])/$rs['stock']['score'] *10, 2);

					$tmp1 = $rs['gold']['score'] - $rs['stock']['score'];
					$tmp2 = $rs['gold']['score'] + $rs['stock']['score'];

					$rate2 = round(($tmp1 / $tmp2) * 100, 2);
					$tagname = 'gold';
				}
				else if($rs['gold']['score'] < $rs['stock']['score'])
				{
					$rate = round(($rs['stock']['score'] - $rs['gold']['score'])/$rs['stock']['score'] *10, 2);

					$tmp1 = $rs['stock']['score'] - $rs['gold']['score'];
					$tmp2 = $rs['stock']['score'] + $rs['gold']['score'];

					$rate2 = round(($tmp1 / $tmp2) * 100, 2);
					$tagname = 'stock';
				}
				else
					continue;
				/* 如果旧标签没有被更改 或者股票和黄金命中率小于10% 就不做通知 */
				if(($type[$expert[$userid]['labelid']] == $tagname) || $rate < 10)
					continue;

				/* 生成文本格式给人工审核用的 */
				$str .= '专家:'.$expert[$userid]['nickname'].',标签:'.$type[$expert[$userid]['labelid']].',新标签:'.$tagname.',黄金命中:'.$rs['gold']['score'].',股票命中:'.$rs['stock']['score'].',差距比A:'.$rate.',差距比B:'.$rate2.PHP_EOL;
				
				$string[] = $userid.'_'.(($tagname == 'stock') ? 1 : 6);
			}
			
			/* 23:30才更新财视接口 */
			$time = date('Hi');
			if($time >= '2300' && $time <= '2359')
			{
				$string = join('~', $string);
	//echo $string;
				$param['UserLables'] = $string;
	//exit('no....');
				$request = curl_post(CSTAGAPI, $param);
				
				if($request['code'] != 200)
					logs('请求财视接口失败,httpcode:'.$request['code'].',postdata:'.$param['UserLables'], 'analysisCS2');
				else
					logs('请求财视接口成功,耗时:'.runTime('begin','end',6).',postdata:'.$string.'return:'.$request['data'], 'analysisCS2');
			}
			else
			{
				$issucc = file_put_contents('./'.date('Ymd').'.txt', $str);

				logs('人工标签审核已生成,大小:'.$issucc, 'analysisCS2');
			}
		}
	}
	/**
	 * CMS文章内容分词,写入新表cms_keyword
	 * 
	 * @return void
	 */
	public function cwsForCmsall()
	{
		runTime('begin');

		$this->load->model('Cms_manage_mdl');
		
		$where['offset'] = 0;
		$where['limit']  = 50;
        
		$data = $this->Cms_manage_mdl->getArticle($where);
		t($data);
		if(empty($data)) exit(logs('获取文章数据为空', 'cwsforcms'));
        $ii = '1';
		foreach($data as $rs)
		{
			$rs['Content'] = preg_replace('#[^\x{4e00}-\x{9fa5}]#u', '', $rs['Title'] .' '. $rs['Source'] .' '. $rs['Content']);

			if(empty($rs['Content'])) continue;

			$cwsurl = sprintf(CWSAPI, urlencode($rs['Content']));

			$res = curl_get($cwsurl, 5);
			
			if($res['code'] == 200)
			{
				$keyword = array();

				$data = json_decode($res['data'], TRUE);
				
				foreach($data['List'] as $val)
					$keyword[] = $val['word'];

				$insert['msgid']   = $rs['ContId'];
				$insert['keyword'] = join(',', $keyword);

				$insertid = $this->Cms_manage_mdl->insertKeyWord($insert);
				
				echo ($insertid > 0) ? '.' : '?';

			}else{
				echo $ii.PHP_EOL;$ii++;
			}
		}
	}

   /**
	 * 分析alog用户行为数据,为用户打标签
	 * 
	 * @param integer $date 日期
	 * @return void
	 */
	public function analysisUserLog($date = '')
	{
		if(empty($date)) exit('no date');

		runTime('begin');
		
		$this->load->model('Shell_manage_mdl');
		$this->load->library('cnfol_file');
		
		$keys = $usercount = 0;
		$uids = $userinfo = array();
		/* 生成日期范围列表 */
		//$begintime = strtotime("-{$days} days");
		//$endtime   = strtotime("-1 days");
runTime('begin');
		/* 遍历日期列表 */
		//for($start = $begintime; $start <= $endtime; $start += 24 * 3600)
		//{
			$where['date'] = $date;
			/* 第一步获取需要分析用户的总记录数用于下面分批读取 */
			$total = $this->Shell_manage_mdl->getUserCount($where);

			if($total < 1)
			{
				logs('userlog_'.$where['date'].'用户为空', 'analysisUserLog');
				continue;
			}
			
			$where['offset'] = 0;
			$where['limit']  = 20000;
			$forcount = ceil($total / $where['limit']);
echo 'table:' . 'userlog_'.$where['date'] . ',total:' . $total . ',while:' . $forcount .PHP_EOL;
			/* 每轮获取limit条处理 */
			while($forcount > 0)
			{
echo 'limit ' . $where['offset'] . ',' . $where['limit'] . PHP_EOL;
				/* 第二步分批获取需要分析用户 */
				$data = $this->Shell_manage_mdl->getUser($where);
				/* 第三步返回第一步中的用户文章数据 */
				foreach($data as $rs)
				{
					if(empty($rs['url']) || empty($rs['global_unique']))
						continue;
					
					$userinfo = $this->cnfol_file->get($rs['global_unique'], 'userinfo');
					$param = explode('/', $rs['url']);
					/* 判断是博客还是cms */
					if(FALSE === strpos($rs['url'], 'blog.cnfol.com'))
					{	/* 排重文章 */
						if(isset($userinfo[$rs['global_unique']]['cmslist']) && in_array(rtrim($param[5], '.shtml'), $userinfo[$rs['global_unique']]['cmslist']))
							continue;
						/* 根据上个键值分配新的可连续的键值 */
						$keys = isset($userinfo[$rs['global_unique']]['cms']) ? count($userinfo[$rs['global_unique']]['cms']) : 0;

						$userinfo[$rs['global_unique']]['cms'][$keys]['date'] = $param[4];
						$userinfo[$rs['global_unique']]['cms'][$keys]['aids'] = rtrim($param[5], '.shtml');

						if(!empty($rs['username']))
							$userinfo[$rs['global_unique']]['username'] = $rs['username'];

						$userinfo[$rs['global_unique']]['cmslist'][] =  rtrim($param[5], '.shtml');
					}
					else
					{	/* 排重文章 */
						if(isset($userinfo[$rs['global_unique']]['bloglist']) && in_array($blog[1], $userinfo[$rs['global_unique']]['bloglist']))
							continue;
						/* 根据上个键值分配新的可连续的键值 */
						$keys = isset($userinfo[$rs['global_unique']]['blog']) ? count($userinfo[$rs['global_unique']]['blog']) : 0;

						$blog = explode('-', rtrim($param[5], '.html'));
						$userinfo[$rs['global_unique']]['blog'][$keys]['date'] = date('Ymd', $blog[0]);
						$userinfo[$rs['global_unique']]['blog'][$keys]['aids'] = $blog[1];
						$userinfo[$rs['global_unique']]['blog'][$keys]['member'] = $param[3];
						$userinfo[$rs['global_unique']]['bloglist'][] =  $blog[1];
					}
					$usercount++;
					$uids[$rs['global_unique']] = '';
					/* 临时存储文件缓存,已免运行内存溢出 */
					$this->cnfol_file->set($rs['global_unique'], $userinfo, 'userinfo', 0);
					$userinfo = array();
				}
				$where['offset'] += $where['limit'];
				$forcount--;
echo 'lastfor:' . $forcount . PHP_EOL;
			}
			$this->cnfol_file->set('userlist_' . $where['date'], $uids, 'userlist', 0);
			//$uids = array();
		//}
		
echo 'usercount:' . $usercount . ',runtime:' . runTime('begin', 'end', 4) . PHP_EOL;
	}

   /**
	 * 分析alog用户行为数据,为用户打标签
	 * 
	 * @param integer $date 自定义日期
	 * @return void
	 */
	public function articleForCms($date = '')
	{
		if(empty($date)) exit('no date');
		runTime('begin');
		
		//$this->load->model('Shell_manage_mdl');
		$this->load->library('cnfol_file');

		$aids = $userlist = $userinfo = array();
		/* 生成日期范围列表 */
		//$begintime = strtotime("-{$days} days");
		//$endtime   = strtotime("-1 days");
		/* 遍历日期列表 */
		//for($start = $begintime; $start <= $endtime; $start += 24 * 3600)
		//{
runTime('begin');
			//$date = date('Ymd', $start);
			/* 第一步遍历缓存获取用户列表 */
			$userlist = $this->cnfol_file->get('userlist_' . $date, 'userlist');
echo 'cache:userlist_' . $date . ',userlist:' . count($userlist) . PHP_EOL;
			/* 第二步遍历缓存获取用户信息 */
			foreach($userlist as $uid => $rs)
			{
//$t1 = microtime(true);
				$userinfo = array();
				$userinfo = $this->cnfol_file->get($uid, 'userinfo');

				if(empty($userinfo)) continue;
//echo 'user:' . $uid . PHP_EOL;
				if(isset($userinfo[$uid]['cmslist']) && !empty($userinfo[$uid]['cmslist']))
				{
					$where['offset'] = 0;
					$where['limit']  = 100;
					$where['aids']   = (count($userinfo[$uid]['cmslist']) > 1) ? $userinfo[$uid]['cmslist'] : $userinfo[$uid]['cmslist'][0];
//echo 'aids:' . join(',', $where['aids']) . PHP_EOL;
					$this->load->model('Cms_manage_mdl');
					$data = $this->Cms_manage_mdl->getArticleKeyWord($where);
//echo 'articletotal:' . count($data) . PHP_EOL
					if(isset($data['List']) && !empty($data['List']))
					{
						/* 遍历文章 */
						foreach($data['List'] as $rs2)
						{
							if(empty($rs2['keyword'])) continue;
//echo 'cws:' . $rsp['data'] . PHP_EOL;
							$words = explode(',', $rs2['keyword']);

							foreach($words as $keys)
							{
								if(!isset($userinfo[$uid]['words'][$keys]))
									$userinfo[$uid]['words'][$keys] = 1;
								else
									$userinfo[$uid]['words'][$keys] += 1;
							}
						}
						/*
						$insert = array();
						$insert['unique'] = $uid;
						$insert['uptime'] = date('Y-m-d H:i:s');

						if(isset($userinfo[$uid]['username']) && !empty($userinfo[$uid]['username']))
							$insert['username'] = $userinfo[$uid]['username'];

						if(isset($userinfo[$uid]['words']) && !empty($userinfo[$uid]['words']))
						{
							arsort($userinfo[$uid]['words']);

							$insert['tagname'] = serialize($userinfo[$uid]['words']);
						}
						
						$this->load->model('Shell_manage_mdl');
						$insertid = $this->Shell_manage_mdl->insertAlogUser($insert);
						*/
						$this->cnfol_file->set($uid, $userinfo, 'userinfo', 0);
					}
//$t2 = microtime(true);
//echo round($t2-$t1,3). PHP_EOL;
				}
//$t2 = microtime(true);
//echo 'uid:' . $uid . ',runtime:' . round($t2-$t1,3). PHP_EOL;
				/*
				if(isset($userinfo[$uid]['blog']) && !empty($userinfo[$uid]['blog']))
				{
					foreach($userinfo[$uid]['blog'] as $rs)
					{
						if($rs['date'] < 20170101) continue;

						$aids[] = $rs['aids'];
					}
				}
				*/
			}
echo 'runtime:' . runTime('begin', 'end', 4) . PHP_EOL;
		//}
	}

   /**
	 * 分析alog用户行为数据,为用户打标签
	 * 
	 * @param integer $date 自定义日期
	 * @return void
	 */
	public function articleForCms2($date = '')
	{
		if(empty($date)) exit('no date');
		runTime('begin');
		
		//$this->load->model('Shell_manage_mdl');
		$this->load->library('cnfol_file');

		$aids = $userlist = $userinfo = array();
		/* 生成日期范围列表 */
		//$begintime = strtotime("-{$days} days");
		//$endtime   = strtotime("-1 days");
		/* 遍历日期列表 */
		//for($start = $begintime; $start <= $endtime; $start += 24 * 3600)
		//{
runTime('begin');
			//$date = date('Ymd', $start);
			/* 第一步遍历缓存获取用户列表 */
			$userlist = $this->cnfol_file->get('userlist_' . $date, 'userlist');
echo 'cache:userlist_' . $date . ',userlist:' . count($userlist) . PHP_EOL;
			/* 第二步遍历缓存获取用户信息 */
			foreach($userlist as $uid => $rs)
			{
//$t1 = microtime(true);
				$userinfo = array();
				$userinfo = $this->cnfol_file->get($uid, 'userinfo');

				if(empty($userinfo)) continue;
//echo 'user:' . $uid . PHP_EOL;
				if(isset($userinfo[$uid]['cmslist']) && !empty($userinfo[$uid]['cmslist']))
				{
					$where['offset'] = 0;
					$where['limit']  = 100;
					$where['aids']   = (count($userinfo[$uid]['cmslist']) > 1) ? $userinfo[$uid]['cmslist'] : $userinfo[$uid]['cmslist'][0];
//echo 'aids:' . join(',', $where['aids']) . PHP_EOL;
					$this->load->model('Cms_manage_mdl');
					$data = $this->Cms_manage_mdl->getArticleKeyWord($where);
//echo 'articletotal:' . count($data) . PHP_EOL
					if(isset($data['List']) && !empty($data['List']))
					{
						/* 遍历文章 */
						foreach($data['List'] as $rs2)
						{
							if(empty($rs2['stockids'])) continue;
//echo 'cws:' . $rsp['data'] . PHP_EOL;
							$words = explode(',', $rs2['stockids']);

							foreach($words as $keys)
							{
								if(!is_numeric($keys)) continue;

								if(!isset($userinfo[$uid]['words'][$keys]))
									$userinfo[$uid]['words'][$keys] = 1;
								else
									$userinfo[$uid]['words'][$keys] += 1;
							}
						}
						/*
						$insert = array();
						$insert['unique'] = $uid;
						$insert['uptime'] = date('Y-m-d H:i:s');

						if(isset($userinfo[$uid]['username']) && !empty($userinfo[$uid]['username']))
							$insert['username'] = $userinfo[$uid]['username'];

						if(isset($userinfo[$uid]['words']) && !empty($userinfo[$uid]['words']))
						{
							arsort($userinfo[$uid]['words']);

							$insert['tagname'] = serialize($userinfo[$uid]['words']);
						}
						
						$this->load->model('Shell_manage_mdl');
						$insertid = $this->Shell_manage_mdl->insertAlogUser($insert);
						*/
						$this->cnfol_file->set($uid, $userinfo, 'userinfo', 0);
					}
//$t2 = microtime(true);
//echo round($t2-$t1,3). PHP_EOL;
				}
//$t2 = microtime(true);
//echo 'uid:' . $uid . ',runtime:' . round($t2-$t1,3). PHP_EOL;
				/*
				if(isset($userinfo[$uid]['blog']) && !empty($userinfo[$uid]['blog']))
				{
					foreach($userinfo[$uid]['blog'] as $rs)
					{
						if($rs['date'] < 20170101) continue;

						$aids[] = $rs['aids'];
					}
				}
				*/
			}
echo 'runtime:' . runTime('begin', 'end', 4) . PHP_EOL;
		//}
	}

   /**
	 * 将用户标签数据入库
	 * 
	 * @param integer $date 自定义日期,格式Ymd
	 * @return void
	 */
	public function addUserTag($date = '')
	{
		if(empty($date)) exit('no date');

		runTime('begin');

		$this->load->library('cnfol_file');
		$this->load->model('Shell_manage_mdl');

		$data = $userlist = $userinfo = array();
runTime('begin');
		/* 第一步遍历缓存获取用户列表 */
		$userlist = $this->cnfol_file->get('userlist_' . $date, 'userlist');
echo 'cache:userlist_' . $date . ',userlist:' . count($userlist) . PHP_EOL;
		/* 第二步遍历缓存获取用户信息 */
		foreach($userlist as $uid => $rs)
		{
//$t1 = microtime(true);
			$userinfo = array();
			$userinfo = $this->cnfol_file->get($uid, 'userinfo');

			if(empty($userinfo) || !isset($userinfo[$uid]['words']))
				continue;

			arsort($userinfo[$uid]['words'], SORT_NUMERIC);

			$data = array();
			$data['unique']  = $uid;
			$data['type']    = 6;
			$data['tagname2'] = serialize($userinfo[$uid]['words']);
			$data['uptime']  = date('Y-m-d H:i:s');

			/* 获取用户信息 */
			if((isset($userinfo[$uid]['username']) && !empty($userinfo[$uid]['username'])))
			{
				$time = time();
				$request['tid'] = $time;
				$request['_t']  = $time;
				$request['username'] = $userinfo[$uid]['username'];
				$request['keystr']   = md5(md5($userinfo[$uid]['username']) . 'REN#23ekj$asf9344' . md5(substr($time, -6)));
				
				$info = curl_post(PASSPORTAPI2, $request);
		
				if($info['code'] != 200 || empty($info['data']))
					logs(PASSPORTAPI2.PHP_EOL.print_r($request, TRUE).PHP_EOL.print_r($info, TRUE), 'addUserTag');
				else
				{
					$info = json_decode($info['data'], TRUE);

					if($info['flag'] == '10000')
					{
						$data['userid']   = $info['info']['UserID'];
						$data['nickname'] = $info['info']['NickName'];
					}
				}
				$data['username'] = $userinfo[$uid]['username'];
			}
			$this->Shell_manage_mdl->insertAlogUser($data);
		}
		unset($data, $userlist, $userinfo);
echo 'runtime:' . runTime('begin', 'end', 4) . PHP_EOL;
		
	}

   /**
	 * 将用户标签数据入库(处理股票代码标签)
	 * 
	 * @param integer $date 自定义日期,格式Ymd
	 * @return void
	 */
	public function addUserTag2($date = '')
	{
		if(empty($date)) exit('no date');

		runTime('begin');

		$this->load->library('cnfol_file');
		$this->load->model('Shell_manage_mdl');

		$data = $userlist = $userinfo = array();
runTime('begin');
		/* 第一步遍历缓存获取用户列表 */
		$userlist = $this->cnfol_file->get('userlist_' . $date, 'userlist');
echo 'cache:userlist_' . $date . ',userlist:' . count($userlist) . PHP_EOL;
		/* 第二步遍历缓存获取用户信息 */
		foreach($userlist as $uid => $rs)
		{
//$t1 = microtime(true);
			$userinfo = array();
			$userinfo = $this->cnfol_file->get($uid, 'userinfo');

			if(empty($userinfo) || !isset($userinfo[$uid]['words']))
				continue;

			arsort($userinfo[$uid]['words'], SORT_NUMERIC);

			$data = array();
			$data['unique']  = $uid;

			foreach($userinfo[$uid]['words'] as $keys => $total)
			{
				$data['stockid'] = $keys;
				$data['total']   = $total;
				$data['uptime']  = date('Y-m-d H:i:s');
			
				/* 获取用户信息 */
				if((isset($userinfo[$uid]['username']) && !empty($userinfo[$uid]['username'])))
				{
					$time = time();
					$request['tid'] = $time;
					$request['_t']  = $time;
					$request['username'] = $userinfo[$uid]['username'];
					$request['keystr']   = md5(md5($userinfo[$uid]['username']) . 'REN#23ekj$asf9344' . md5(substr($time, -6)));
					
					$info = curl_post(PASSPORTAPI2, $request);
			
					if($info['code'] != 200 || empty($info['data']))
						logs(PASSPORTAPI2.PHP_EOL.print_r($request, TRUE).PHP_EOL.print_r($info, TRUE), 'addUserTag');
					else
					{
						$info = json_decode($info['data'], TRUE);

						if($info['flag'] == '10000')
						{
							$data['userid']   = $info['info']['UserID'];
							//$data['nickname'] = $info['info']['NickName'];
						}
					}
					//$data['username'] = $userinfo[$uid]['username'];
				}
				$this->Shell_manage_mdl->insertAlogUser2($data);
			}
		}
		unset($data, $userlist, $userinfo);
echo 'runtime:' . runTime('begin', 'end', 4) . PHP_EOL;
	}

   /**
	 * 每天00:05将昨天文章分词整合入cms_keyword_2017表中
	 * 
	 * @return void
	 */
	public function articleMergeForCms($offset = 0, $limit = 30000)
	{
		$this->load->model('Cms_manage_mdl');

		runTime('begin');

		$where['offset'] = $offset;
		$where['limit']  = $limit;
		/* 取昨天日期 */
		$where['enddate']   = date('Ymd',strtotime('-1 day'));
		$where['startdate'] = date('Ymd',strtotime('-1 day'));
		//$where['enddate']   = 20170806;
		//$where['startdate'] = 20170706;

		$data = $this->Cms_manage_mdl->getNewArticle($where);

		$forcount = ($data['Total'] > 0) ? ceil($data['Total'] / $where['limit']) : 0;

		logs("{$where['startdate']}文章,共{$data['Total']}篇", 'articleMergeForCms');
		
		$category = $this->Cms_manage_mdl->getCateGoryAll();

		//while($forcount > 0)
		//{
			foreach($data['List'] as $rs)
			{
				$rs['Content'] = preg_replace('#[^\x{4e00}-\x{9fa5}]#u', '', $rs['Content']);

				if(empty($rs['Content'])) continue;
				/* 关键词提取 */
				$url = sprintf(WORDAPI2, $rs['Content']);
			
				$rsp = curl_get($url);

				if($rsp['code'] != 200 || empty($rsp['data']))
				{
					logs('分词接口请求失败,rsp:'.$rsp['data'].',httpcode:'.$rsp['code'], 'articleMergeForCms');
					continue;
				}

				$insert['keyword'] = trim($rsp['data']);

				/* 股票代码提取 */
				$url = sprintf(WORDAPI3, $rs['Content']);
			
				$rsp = curl_get($url);

				if($rsp['code'] != 200)
				{
					logs('分词接口请求失败,rsp:'.$rsp['data'].',httpcode:'.$rsp['code'], 'articleMergeForCms');
					continue;
				}
				$insert['contid']   = $rs['ContId'];
				$insert['stockids'] = trim($rsp['data']);
				$insert['source']   = $rs['Source'];
				$insert['author']   = $rs['Author'];
				$insert['channelid']   = $category[$rs['CatId']]['id'];
				$insert['channelname'] = $category[$rs['CatId']]['name'];
				$insert['createtime']  = date('Y-m-d H:i:s', $rs['CreatedTime']);

				$insertid = $this->Cms_manage_mdl->insertKeyWord($insert);
			}
			//$where['offset'] += $where['limit'];
			//$forcount--;
			//$data = array();
			//$data = $this->Cms_manage_mdl->getNewArticle($where);
//echo 'limit ' . $where['offset'] . ',' . $where['limit'] . ',lastfor:' . $forcount . PHP_EOL;
		//}
		logs("处理完毕{$data['Total']}篇文章,耗时:".runTime('begin', 'end', 4), 'articleMergeForCms');
	}
	/* 更新表用 */
	public function upArticleMergeForCms($offset = 0, $limit = 5000)
	{
		$this->load->model('Cms_manage_mdl');

		$where['offset'] = $offset;
		$where['limit']  = $limit;
		$category = $this->Cms_manage_mdl->getCateGoryAll();
		//t($category);
		$data = $this->Cms_manage_mdl->getNewArticle($where);
		$count = 0;

		foreach($data['List'] as $rs)
		{
			//$rs['Content'] = preg_replace('#[^\d\x{4e00}-\x{9fa5}]#u', '_', $rs['Content']);

			//if(empty($rs['Content'])) continue;
//$t1 = microtime(true);

			//$url = sprintf(WORDAPI3, $rs['Content']);
		
			//$rsp = curl_get($url);
//$t2 = microtime(true);

//echo round($t2-$t1,3). PHP_EOL;
			//if($rsp['code'] != 200)
			//{
				//logs('分词接口请求失败,rsp:'.$rsp['data'].',httpcode:'.$rsp['code'], 'articleMergeForCms2');
				//continue;
			//}

			//if(empty($rsp['data'])) continue;

			//$update['stockids'] = trim($rsp['data']);
			$update['source'] = $rs['Source'];
			$update['author'] = $rs['Author'];
			$update['channelid']   = $category[$rs['CatId']]['id'];
			$update['channelname'] = $category[$rs['CatId']]['name'];

			$this->Cms_manage_mdl->updateKeyWord($rs['ContId'], $update);

			$count++;
		}
		echo 'update:'.$count.PHP_EOL;
	}
}

/* End of file Shell_manage.php */
/* Location: ./application/controllers/_shell/Shell_manage.php */