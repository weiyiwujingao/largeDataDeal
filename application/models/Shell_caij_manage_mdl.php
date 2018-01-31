<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 通用定时数据分析脚本
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-06-21
 ****************************************************************/
class Shell_caij_manage_mdl extends CI_Model
{
	/* 用户浏览记录 */
	const TBL_USERLOG = 'userlog_';
	/* 用户标签表 */
	const TBL_USERTAG = 'usertag';
    const TB_CAIJ_KEYWORD_2017 = 'caij_keyword_2017'; //文章信息表
    const TB_WM_ARTICLE = 'wm_article'; //财经号文章
    const TB_WM_ARTICLE_CONTENT = 'wm_article_content'; //财经号文章内容
    const TB_WM_ARTICLE_CATEGORY = 'wm_article_category'; //财经号标签
    const TB_WM_USER = 'wm_user'; //用户表 

	public function __construct()
	{
		parent::__construct();
	}
    /**
     * 关键词表最大的ArticleID
     *
     * @param int $Type 类型 1为cms文章 2为博客 3为财经号
     * @param int $end 结束时间
     * 
     *
     */
    function getBlogMaxid($limitid='',$mintime='') {
        $this->db = $this->load->database('default', TRUE);
        $this->db->query('SET NAMES utf8');//SET NAMES latin1
        $sqlwhere = $limitid?"createtime < '{$limitid}' AND  createtime > '{$mintime}'":'createtime>0';
        
        $res = $this->db->select('contid')
                        ->from(self::TB_CAIJ_KEYWORD_2017);
        
        $result = $res->where('id >', 0)
                      ->where($sqlwhere, NULL, FALSE)
                      ->order_by('contid', 'DESC')
                      ->limit(1)
                      ->get()->result_array();
        if(isset($result['0']['contid']))
            return $result['0']['contid'];
        else 
            return '0';
    }
    /**
     * 查财经号文章
     *
     * @param int $ArticleID 最新入库的最大文章id
     * 
     *
     */
    function getZiArticle($create_time='0',$limit=100,$addTime='2017-01-01') {
        
        $this->db_blg = $this->load->database('caijing', true);
        $this->db_blg->query('SET NAMES utf8');//SET NAMES latin1
        
        
      //  $res = $this->db_blg->select('id,title,cover,category_id,user_id,create_time')
        $res = $this->db_blg->select('*')
                            ->from(self::TB_WM_ARTICLE);
        
        $ressult = $res->where('create_time >', $create_time)
                       ->where('create_time <', $addTime)
                       ->where('status', 1)             
                       ->order_by('id', 'desc')
                       ->limit($limit)
                       ->get()->result_array();
        //echo '<br>'.$this->db_blg->last_query().'<br>';
        return $ressult;
    }
    /**
     * 查财经号内容
     *
     * @param int $ArticleID 文章id
     * @param int $MemberID 成员id
     * @param int $time 文章发布时间
     * 
     *
     */
    function getZiContent($ArticleID='') {
        
        if(!$ArticleID) return '';
        
        $this->db_blg = $this->load->database('caijing', true);
        $this->db_blg->query('SET NAMES utf8');
        
        $res = $this->db_blg->select('content')->from(self::TB_WM_ARTICLE_CONTENT);
        
        $result = $res->where(array('article_id '=> $ArticleID))
                      ->order_by('article_id', 'desc')
                      ->limit(1)
                      ->get()->result_array();
        if(isset($result['0']['content']))
            return $result['0']['content'];
        else return '';   
    }
     /**
     * 查财经号标签名称
     *
     * @param int $ArticleID 最新入库的最大文章id
     * 
     *
     */
    function getMediaName($userid,$setype='nickname',$limit=1) {
        
        $this->db_blg = $this->load->database('caijing', true);
        $this->db_blg->query('SET NAMES utf8');//SET NAMES latin1
                   
        $res = $this->db_blg->select($setype)
                            ->from(self::TB_WM_USER);
        if($userid)
            $res = $res->where('id',$userid);
        
        $ressult = $res->where('id >', 0)             
                       ->order_by('id', 'asc')
                       ->limit($limit)
                       ->get()->result_array();
        if($limit==1 && isset($ressult['0']['nickname'])){
            $ressult = $ressult['0']['nickname'];
        }
        return $ressult;
    }
    /**
     * 插入数据
     *
     * @param array $data 批量数组数据
     *
     */
    function insertBlogKeyWord($data) {
        if(!$data) return;
        $sql = 'INSERT INTO `'.self::TB_CAIJ_KEYWORD_2017.'` (`contid`,`keyword`,`stockids`,`source`,`author`,`channelid`,`channelname`,`createtime`) VALUES ';
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