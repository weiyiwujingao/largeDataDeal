<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 通用定时数据分析脚本
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-06-21
 ****************************************************************/
class Shell_blog_manage_mdl extends CI_Model
{
	/* 用户浏览记录 */
	const TBL_USERLOG = 'userlog_';
	/* 用户标签表 */
	const TBL_USERTAG = 'usertag';
    const TB_BLOG_KEYWORD_2017 = 'blog_keyword_2017'; //文章信息表
    const TB_TBBLOGARTICLECHART = 'tbBlogArticleChart'; //博客最新发布文章
    const TB_TBBLOGARTICLE = 'tbBlogArticle'; //博客文章内容表--规则tbBlogArticle年份00+memberid取模40
    const TB_TBBLOGARTICLESTAT = 'tbBlogArticleStat'; //博客文章头图表--规则tbBlogArticleStat年份00+memberid取模40

	public function __construct()
	{
		parent::__construct();
	}
    /**
     * 查博客文章
     *
     * @param int $ArticleID 最新入库的最大文章id
     * 
     *
     */
    function getBlogArticle($AppearTime='0',$limit=100,$maxtime='2017-01-01') {
        
        $this->db_blg = $this->load->database('blog', true);
        $this->db_blg->query('SET NAMES latin1');//SET NAMES latin1
        
        
        $res = $this->db_blg->select('ArticleID,Blogname,AppearTime,Title,SysTagID,MemberID,NickName')
                            ->from(self::TB_TBBLOGARTICLECHART);
        
        $ressult = $res->where('AppearTime >', $AppearTime)
                       ->where('AppearTime <', $maxtime)
                       ->order_by('AppearTime', 'desc')
                       ->limit($limit)
                       ->get()->result_array();
        return $ressult;
    }
    /**
     * 查博客内容
     *
     * @param int $ArticleID 文章id
     * @param int $MemberID 成员id
     * @param int $time 文章发布时间
     * 
     *
     */
    function getBlogContent($ArticleID='', $MemberID='', $time='') {
        
        if(!$ArticleID || !$MemberID || !$time) return '';
        $mod = sprintf("%02d", $MemberID%40);//生成2位数，不足前面补0
        $table = self::TB_TBBLOGARTICLE.date('Y').'00'.$mod;
        
        $this->db_blg = $this->load->database('blog', true);
        $this->db_blg->query('SET NAMES latin1');
        
        
        $res = $this->db_blg->select('Content')->from($table);
        
        $result = $res->where(array('ArticleID '=> $ArticleID))
                      ->order_by('ArticleID', 'desc')
                      ->limit(1)
                      ->get()->result_array();
        if(isset($result['0']['Content']))
            return $result['0']['Content'];
        else return '';
    }
    /**
     * 批量数组数据插入
     *
     * @param array $data 批量数组数据
     *
     */
    function insertBlogKeyWord($data) {
        if(!$data) return;
        $sql = 'INSERT INTO `'.self::TB_BLOG_KEYWORD_2017.'` (`contid`,`keyword`,`stockids`,`source`,`author`,`channelid`,`channelname`,`createtime`) VALUES ';
        foreach($data as $v){
            $sql .= '(\''.$v['contid'].'\',\''.$v['keyword'].'\',\''.$v['stockids'].'\',\''.$v['source'].'\',\''.$v['author'].'\',\''.$v['channelid'].'\',\''.$v['channelname'].'\',\''.$v['createtime'].'\'),';
        }
        unset($data);
        $sql = trim($sql,',').'  ON DUPLICATE KEY UPDATE contid=VALUES(contid);';
        $this->db = $this->load->database('default', true);
        $insertid = $this->db->query($sql);
        return $insertid ;
    
    }


}

/* End of file Shell_manage_mdl.php */
/* Location: ./application/models/Shell_manage_mdl.php */