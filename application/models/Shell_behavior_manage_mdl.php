<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 通用定时数据分析脚本
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-06-21
 ****************************************************************/
class Shell_behavior_manage_mdl extends CI_Model
{
	/* 博客关注博主对应表 */
	const TBL_TBBLOGMEMBER = 'tbBlogMember';
    /* 财视关注对应表 */
	const TBL_FPEXPERTFANS = 'fpExpertFans';
    /* 财视用户id和标签对应表 */
	const TBL_FPUSERLABELRELATION = 'fpUserLabelRelation';
    /* 财视用户和用户名称对应表 */
	const TBL_FPUSER = 'fpUser';
    /* 财视用户和标签对应表 */
	const TBL_FPLABEL = 'fpLabel';
    /* 财经用户关注专家id对应表 */
	const TBL_WM_ATTENTION = 'wm_attention';
    /* 财经用户关注专家名称表 */
	const TBL_WM_USER = 'wm_user';


	public function __construct()
	{
		parent::__construct();
	}
    /**
     * 查博客用户关注的博主
     *
     * @param int $Userid 用户中心id
     * 
     *
     */
    function getBlogMsg($userid){
        
        if(!$userid) return '';
        $this->db_log = $this->load->database('blog', true);  
		$this->db_log->query('SET NAMES latin1');
        
        $data = $this->db_log->select('BlogName')
                              ->from(self::TBL_TBBLOGMEMBER)
                              ->where('UserID',$userid)
                              ->where('Status',0)//状态默认0为正常状态
                              ->limit(10)
                              ->get()->result_array();  
        ///$data = (isset($data['0']['BlogName']) && $data['0']['BlogName']) ? 
						//$data['0']['BlogName'] : '';                       
        //$query->free_result();  
        return $data;
    }
     /**
     * 查财视用户关注的博主id
     *
     * @param int $Userid 用户中心id
     * 
     *
     */
    function getCaisUserid($userid){
        
        if(!$userid) return '';
        $this->db_cais = $this->load->database('caishi', true);  
		$this->db_cais->query('SET NAMES utf8');
        
        $data = $this->db_cais->select('ToUserID')
                              ->from(self::TBL_FPEXPERTFANS)
                              ->where('UserID',$userid)
                              ->where('IsCancel',0)//是否取消：0,否;1,是
                              ->group_by('ToUserID')                              
                              ->limit(500)
                              ->get()->result_array();    
        return $data;
    }
     /**
     * 读取财视用户关注的专家名称
     *
     * @param array $Userid 用户中心id
     * 
     *
     */
    function getCaisName($userid){
        
        if(!$userid) return '';
        $this->db_cais = $this->load->database('caishi', true);  
		$this->db_cais->query('SET NAMES utf8');
        
        $data = $this->db_cais->select('UserName')
                              ->from(self::TBL_FPUSER)
                              ->where_in('UserID',$userid)
                              ->group_by('UserName')
                              ->limit(500)
                              ->get()->result_array();    
        return $data;
    }
     /**
     * 读取财视用户关注的专家标签id
     *
     * @param array $Userid 用户中心id
     * 
     *
     */
    function getCaisLabelid($userid){
        
        if(!$userid) return '';
        $this->db_cais = $this->load->database('caishi', true);  
		$this->db_cais->query('SET NAMES utf8');
        
        $data = $this->db_cais->select('LabelID,count(LabelID) as num')
                              ->from(self::TBL_FPUSERLABELRELATION)
                              ->where_in('UserID',$userid)
                              ->group_by('LabelID')
                              ->order_by('num','desc')
                              ->limit(500)
                              ->get()->result_array(); 
        return $data;
    }
     /**
     * 读取财视用户关注的专家标签id
     *
     * @param array $Userid 用户中心id
     * 
     *
     */
    function getCaisLabel($userid){
        
        if(!$userid) return '';
        $this->db_cais = $this->load->database('caishi', true);  
		$this->db_cais->query('SET NAMES utf8');
        $str = '';
        foreach($userid as $v){
            $str .= "'{$v}'," ;
        }
        $str =  trim($str,',');
        $data = $this->db_cais->select('LabelName')
                              ->from(self::TBL_FPLABEL)
                              ->where_in('LabelID',$userid)
                              ->order_by('field (LabelID,'.$str.')')
                              ->limit(500)
                              ->get()->result_array(); 
        return $data;
    }
  /**
     * 查财经号用户关注的专家id
     *
     * @param int $Userid 用户中心id
     * 
     *
     */
    function getCaijid($userid){
        
        if(!$userid) return '';
        $this->db_caij = $this->load->database('caijing', true);  
		$this->db_caij->query('SET NAMES utf8');
        
        $data = $this->db_caij->select('user_id')
                              ->from(self::TBL_WM_ATTENTION)
                              ->where('u_id',$userid)
                              ->group_by('user_id')                              
                              ->limit(500)
                              ->get()->result_array();    
        return $data;
    } 
    /**
     * 查财经号用户关注的专家名称
     *
     * @param int $Userid 用户中心id
     * 
     *
     */
    function getCaijName($userid){
        
        if(!$userid) return '';
        $this->db_caij = $this->load->database('caijing', true);  
		$this->db_caij->query('SET NAMES utf8');
        
        $data = $this->db_caij->select('nickname')
                              ->from(self::TBL_WM_USER)
                              ->where_in('id',$userid)
                              ->group_by('nickname')                              
                              ->limit(500)
                              ->get()->result_array();    
        return $data;
    }     
}

/* End of file Shell_manage_mdl.php */
/* Location: ./application/models/Shell_manage_mdl.php */