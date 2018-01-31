<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 通用定时数据分析脚本
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-06-21
 ****************************************************************/
class Shell_alognew_manage_mdl extends CI_Model
{
	/* 用户浏览记录 */
	const TBL_USERLOG = 'userlog_';
	/* 用户标签表 */
	const TBL_USERTAG = 'usertag';
    const TB_BLOG_KEYWORD_2017 = 'blog_keyword_2017'; //文章信息表
    const TB_CMS_KEYWORD_2017 = 'cms_keyword_2017'; //文章信息表
    const TB_CAIJ_KEYWORD_2017 = 'caij_keyword_2017'; //文章信息表
    const TB_BEHAVIOR_USERID_BASEDATA = 'behavior_userid_basedata'; //用户基础信息表格

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
    function getAlogMsg($tablemsg, $limit, $table){
        
        if(!$tablemsg || !$limit || !$table) return '';
        $this->db_alg = $this->load->database('alog', true);
        
        $db = clone($this->db_alg);
        $total = $this->db_alg->count_all_results(self::TBL_USERLOG.$table);
        
        $this->db_alg = $db;
		$this->db_alg->query('set names utf8');
 
        $res = $this->db_alg->select('username,global_unique,starttime,url')
                            ->from(self::TBL_USERLOG.$table);
                            
        
        
        $ressult = $res->order_by('id', 'asc')
                       ->limit($limit,$tablemsg[$table]["num"])
                       ->get()->result_array();
        //echo $this->db_alg->last_query();       
        return array('total'=>$total,'list'=>$ressult);
    }
    /**
     * 查内容
     *
     * @param int $ArticleID 文章id
     * @param int $MemberID 成员id
     * @param int $time 文章发布时间
     * 
     *
     */
    function getContent($contid='', $type='') {
        
        if(!$contid || !$type) return '';
        $key = $contid.'_'.$type;
        $result = $this->cnfol_file->get($key, 'keywords_2017');
        if($result)
            return $result;
        $this->db = $this->load->database('default', true);
        $this->db->query('set names utf8');
        $table = $this->tabletype($type);
        
        $res = $this->db->select('*')
                            ->from($table);
        
        $result = $res->where(array('contid '=> $contid))
                      ->limit(1)
                      ->get()->result_array();
        if(isset($result['0']) && $result['0']){
             $this->cnfol_file->set($key, $result['0'], 'keywords_2017', 0);
            return $result['0'];
        }    
        else return '';
    }
    private function tabletype($type)
    {
        $table = self::TB_CAIJ_KEYWORD_2017;
        if($type==1)
            return self::TB_CMS_KEYWORD_2017;
        if($type==2)
            return self::TB_BLOG_KEYWORD_2017;
        return $table;
    }
    /**
     * 查内容
     *
     * @param int $ArticleID 文章id
     * @param int $MemberID 成员id
     * @param int $time 文章发布时间
     * 
     *
     */
    function getBehaviorData($global_unique='',$userid='') {
        
        if(!$global_unique) return array();
        $wheresql = '`global_uniques` like "%'.$global_unique.'%"';
        if($userid)
            $wheresql .= ' or `global_uniques` like "%'.$userid.'%"';
        $this->db = $this->load->database('default', true);
        $this->db->query('set names utf8');       
        $res = $this->db->select('userid,data,global_uniques')
                            ->from(self::TB_BEHAVIOR_USERID_BASEDATA);
        
        $result = $res->where($wheresql)
                      ->limit(1)
                      ->get()->result_array();             
        $result = isset($result['0'])?$result['0']:array();
        return $result;
    }
    /**
     * 
     * 更新表
     * 
     */
    function updateBehaviorData($param, $where){
        $this->db = $this->load->database('default', true);
        $this->db->query('set names utf8');
        $this->db->update(self::TB_BEHAVIOR_USERID_BASEDATA, $param, $where);
        
        $r = ($this->db->affected_rows() == 1) ? TRUE : FALSE;
        $this->db->close();
        return $r;
    }
    

   /**
	 * 插入基础用户数据
	 * @return array
	 */
	public function insertBehaviorData($data = array())
	{
		$this->db2 = $this->load->database('default', TRUE);

        $this->db2->insert(self::TB_BEHAVIOR_USERID_BASEDATA, $data);
        //echo $this->db2->last_query();

		return $this->db2->insert_id();
	}

}

/* End of file Shell_manage_mdl.php */
/* Location: ./application/models/Shell_manage_mdl.php */