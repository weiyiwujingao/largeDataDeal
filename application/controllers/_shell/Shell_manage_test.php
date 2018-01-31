<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 智能过滤系统 - 财视分析离线脚本
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-04-20
 ****************************************************************/
set_time_limit(0);
class Shell_manage_test extends MY_Controller
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

		$this->load->model('Cms_manage_mdl_test');
		
		$where['offset'] = 0;
		$where['limit']  = 100;
		$where['contid'] = $this->Cms_manage_mdl_test->getMaxID();
		// var_dump($where['contid']);exit;
		// $where['contid'] = $data[0]['msgid'];
		// $data = $this->Cms_manage_mdl_test->getArticle($where);
		$data = $this->Cms_manage_mdl_test->getMinID($where);
// var_dump($data);exit;
		if(empty($data)) exit(logs('获取文章数据为空', 'cwsforcms'));
        // $ii = '1';
		foreach($data as $rs)
		{
			$rs['Content'] = preg_replace('#[^\x{4e00}-\x{9fa5}]#u', '',  $rs['Content']);
			$res['Title'] = preg_replace('#[^\x{4e00}-\x{9fa5}]#u', '', $rs['Title'] );
			if(empty($rs['Content']) && empty($rs['Title'])) continue;
// var_dump($rs);
			$cwsurl = sprintf(CWSAPI, urlencode($rs['Content']));
			$cwsurls = sprintf(CWSAPI, urlencode($res['Title']));
			$res = curl_get($cwsurl, 5);
			$rest = curl_get($cwsurls, 5);
			if($res['code'] == 200 )
			{
				$keywordc = array();
				$keywordt = array();
				$data = json_decode($res['data'], TRUE);
				$datat = json_decode($rest['data'], TRUE);
				// var_dump($data);exit;
				foreach($data['List'] as $val)
					$keywordc[] = $val['word'];
				foreach($datat['List'] as $val)
					$keywordt[] = $val['word'];
				// var_dump($rs['Title']);exit;
				$insert['msgid']   = $rs['ContId'];
				$insert['c_keyword'] = join(',', $keywordc);
				$insert['t_keyword'] = join(',', $keywordt);
				$insert['Title'] = $rs['Title'];
				$insert['CreatedTime'] = $rs['CreatedTime'];
				$insert['Url'] = $rs['Url'];
				$insert['Source'] = $rs['Source'];
				// var_dump($insert);exit;
				$insertid = $this->Cms_manage_mdl_test->insertKeyWord($insert);
				
				// echo ($insertid > 0) ? '.' : '?';

			}
			// else{
			// 	echo $ii.PHP_EOL;$ii++;
			// }
		}
	}
 	/**
	 * CMS文章内容分词,写入新表cms_keyword
	 * 
	 * @return void
	 */
	public function cwsForCmsTwo()
	{
		$log_path = APPPATH .'cache/participle/keyword_'.date('Ymd').'.log';
		$website  = APPPATH .'cache/participle/website/';  //避免重复的日志信息
		$logFile = $website.$iniName.'.log';
		if (!file_exists($logFile)) {
				fclose(fopen($logFile, "wb"));
			}
		runTime('begin');

		$this->load->model('Cms_manage_mdl_test');
		
		$where['offset'] = 0;
		$where['limit']  = 100;
		$where['contid'] = $this->Cms_manage_mdl_test->getMaxIDTwo('');
		/* 初始化分词类及配置 */
		$this->load->library('pscws');
		$this->pscws->set_dict(APPPATH . 'cache/pscws/dict.utf8.xdb');
		$this->pscws->set_rule(APPPATH . 'cache/pscws/rules.utf8.ini');
// echo $where['contid'];exit;
		$data = $this->Cms_manage_mdl_test->getArticleTwo($where);
		// $data = $this->Cms_manage_mdl_test->getMinID($where);
// var_dump($data);exit;
		if(empty($data)){
			error_log(date('Y-m-d H:i:s').'|获取文章数据为空'.PHP_EOL,3,$log_path);
			 exit(logs('', 'cwsforcms'));
		}
        // $ii = '1';
		foreach($data as $rs)
		{
			$res['Content'] = preg_replace('#[^\x{4e00}-\x{9fa5}]#u', '',  $rs['Content']);
			$res['Title'] = preg_replace('#[^\x{4e00}-\x{9fa5}]#u', '', $rs['Title'] );
			if(empty($rs['Content']) && empty($rs['Title'])) continue;
			$this->pscws->send_text($res['Content']);
			/* 根据权重倒序获取 */
			$resc = $this->pscws->get_tops(6, true);
			$this->pscws->send_text($rs['Title']);
			$rest = $this->pscws->get_tops(6, true);
			if(!empty($resc['List']) && !empty($rest['List']) )
			{
				$keywordc = array();
				$keywordt = array();
				foreach($resc['List'] as $val)
					$keywordc[] = $val['word'];
				foreach($rest['List'] as $val)
					$keywordt[] = $val['word'];
				// var_dump($rs['Title']);exit;
				$insert['msgid']   = $rs['ContId'];
				$insert['c_keyword'] = join(',', $keywordc);
				$insert['t_keyword'] = join(',', $keywordt);
				$insert['Title'] = $rs['Title'];
				$insert['CreatedTime'] = $rs['CreatedTime'];
				$insert['Url'] = $rs['Url'];
				$insert['Source'] = $rs['Source'];
				$insert['Content'] = $rs['Content'];
				// var_dump($insert);exit;
				$insertid = $this->Cms_manage_mdl_test->insertKeyWordTwo($insert);
				
				// echo ($insertid > 0) ? '.' : '?';

			}
			else{
				
				error_log(date('Y-m-d H:i:s').'|'.$rs['ContId'].'|'.$rs['Title'].'|分词失败'.PHP_EOL,3,$log_path);
				// echo $ii.PHP_EOL;$ii++;
			}
		}
	}
	/**
	 * CMS文章内容分词,写入新表cms_keyword
	 * 
	 * @return void
	 */
	public function blogForCms()
	{
		$log_path = APPPATH .'cache/participle/blogkeyword_'.date('Ymd').'.log';
		$website  = APPPATH .'cache/participle/website/';  //避免重复的日志信息
		$logFile = $website.'.log';
		if (!file_exists($logFile)) {
				fclose(fopen($logFile, "wb"));
			}
		runTime('begin');

		$this->load->model('Cms_manage_mdl_test');
		
		$where['offset'] = 0;
		$where['limit']  = 1;
		$data = $this->Cms_manage_mdl_test->getBlogArticleTitle('');	
		var_dump($data);	
		/* 初始化分词类及配置 */
		$this->load->library('pscws');
		$this->pscws->set_dict(APPPATH . 'cache/pscws/dict.utf8.xdb');
		$this->pscws->set_rule(APPPATH . 'cache/pscws/rules.utf8.ini');
		foreach($data as $rs){
			$where['num'] = $rs['MemberID']%40 ;
			$where['ArticleID'] = $rs['ArticleID'] ;
			$data[]['Content'] = $this->Cms_manage_mdl_test->getBlogArticleCon($where);
		}
echo $data;exit;
		$data = $this->Cms_manage_mdl_test->getArticleTwo($where);
		// $data = $this->Cms_manage_mdl_test->getMinID($where);
// var_dump($data);exit;
		if(empty($data)){
			error_log(date('Y-m-d H:i:s').'|获取文章数据为空'.PHP_EOL,3,$log_path);
			 exit(logs('', 'cwsforcms'));
		}
        // $ii = '1';
		foreach($data as $rs)
		{
			$res['Content'] = preg_replace('#[^\x{4e00}-\x{9fa5}]#u', '',  $rs['Content']);
			$res['Title'] = preg_replace('#[^\x{4e00}-\x{9fa5}]#u', '', $rs['Title'] );
			if(empty($rs['Content']) && empty($rs['Title'])) continue;
			$this->pscws->send_text($res['Content']);
			/* 根据权重倒序获取 */
			$resc = $this->pscws->get_tops(6, true);
			$this->pscws->send_text($rs['Title']);
			$rest = $this->pscws->get_tops(6, true);
			if(!empty($resc['List']) && !empty($rest['List']) )
			{
				$keywordc = array();
				$keywordt = array();
				foreach($resc['List'] as $val)
					$keywordc[] = $val['word'];
				foreach($rest['List'] as $val)
					$keywordt[] = $val['word'];
				// var_dump($rs['Title']);exit;
				$insert['msgid']   = $rs['ContId'];
				$insert['c_keyword'] = join(',', $keywordc);
				$insert['t_keyword'] = join(',', $keywordt);
				$insert['Title'] = $rs['Title'];
				$insert['CreatedTime'] = $rs['CreatedTime'];
				$insert['Url'] = $rs['Url'];
				$insert['Source'] = $rs['Source'];
				$insert['Content'] = $rs['Content'];
				// var_dump($insert);exit;
				// $insertid = $this->Cms_manage_mdl_test->insertKeyWordTwo($insert);
				
				// echo ($insertid > 0) ? '.' : '?';

			}
			else{
				
				error_log(date('Y-m-d H:i:s').'|'.$rs['ContId'].'|'.$rs['Title'].'|分词失败'.PHP_EOL,3,$log_path);
				// echo $ii.PHP_EOL;$ii++;
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
}

/* End of file Shell_manage.php */
/* Location: ./application/controllers/_shell/Shell_manage.php */