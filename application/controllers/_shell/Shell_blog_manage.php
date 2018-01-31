<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 智能过滤系统 - 财视分析离线脚本
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-04-20
 ****************************************************************/
set_time_limit(0);
class Shell_blog_manage extends MY_Controller
{
	private $data = array();
    const LIMITNUM = 50000; //更新文章数据量

	public function __construct()
	{
		parent::__construct();
	}
   /**
	 * 每天00:05将昨天文章分词整合入blog_keyword_2017表中
	 * 
	 * @return void
	 */
	public function articleMergeForBlog($limitid = 1)
	{       
        //更新最新的数据
        if($limitid==1) $limitid = date('Y-m-d H:i:s');
        $limit = self::LIMITNUM; 
        $mintime = date('Y-m-d H:i:s',strtotime($limitid)-(86400*1));
        if($mintime<'1971')exit('错误传参');
        runTime('begin');
        $this->load->model('Shell_blog_manage_mdl');
        $list  = $this->Shell_blog_manage_mdl->getBlogArticle($mintime,$limit,$limitid);
        if(!$list) exit('改时间段已经更新完毕！');
        logs(date('Y-m-d H:i:s').'|记录数:'.count($list), 'articleMergeForblog');
        echo date('Y-m-d H:i:s').'|记录数:'.count($list).'<br>';
        $blogname = config_item('blogname');
        $insert = array();
        foreach($list as $k=>$rs)
        {
            if(!$rs['ArticleID']) continue;
            $rs['Content']  = $this->Shell_blog_manage_mdl->getBlogContent($rs['ArticleID'], $rs['MemberID'], strtotime($rs['AppearTime']));
            $rs['Content'] = preg_replace('#[^\x{4e00}-\x{9fa5}]#u', '', $rs['Content']);

            if(empty($rs['Content'])) continue;
            /* 关键词提取 */
            $url = sprintf(WORDAPI2, $rs['Content']);
        
            $rsp = curl_get($url);
            
            if($rsp['code'] != 200 || empty($rsp['data']))
            {
                logs('分词接口请求失败,rsp:'.$rsp['data'].',httpcode:'.$rsp['code'], 'articleMergeForblog');
                continue;
            }
            $insert[$k]['keyword'] = trim($rsp['data']);

            /* 股票代码提取 */
            $url = sprintf(WORDAPI3, $rs['Content']);
        
            $rsp = curl_get($url);

            if($rsp['code'] != 200)
            {
                logs('分词接口请求失败,rsp:'.$rsp['data'].',httpcode:'.$rsp['code'], 'articleMergeForblog');
                continue;
            }
            $insert[$k]['contid']   = $rs['ArticleID'];
            $insert[$k]['stockids'] = trim($rsp['data']);
            $insert[$k]['source']   = $rs['Blogname'];
            $insert[$k]['author']   = $rs['NickName'];
            $insert[$k]['channelid']   = $rs['SysTagID'];
            $insert[$k]['channelname'] = isset($blogname[$rs['SysTagID']])?$blogname[$rs['SysTagID']]:'其它';
            $insert[$k]['createtime']  = $rs['AppearTime'];

            $this->Shell_blog_manage_mdl->insertBlogKeyWord($insert);
            unset($insert);
        }
		logs("处理完毕".count($list)."篇文章,耗时:".runTime('begin', 'end', 4), 'articleMergeForblog');
        pre("处理完毕".count($list)."篇文章,耗时:".runTime('begin', 'end', 4));
        unset($insert,$list);
	}	
}

/* End of file Shell_manage.php */
/* Location: ./application/controllers/_shell/Shell_manage.php */