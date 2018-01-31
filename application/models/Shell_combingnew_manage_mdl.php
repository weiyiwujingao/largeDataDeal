<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 通用定时数据分析脚本
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-06-21
 ****************************************************************/
class Shell_combingnew_manage_mdl extends CI_Model
{
	/* 用户浏览记录 */
	const TBL_USERLOG = 'userlog_';
	/* 用户标签表 */
	const TBL_USERTAG = 'usertag';
    /* 用户基础信息表格 */
    const TB_BEHAVIOR_USERID_BASEDATA = 'behavior_userid_basedata'; 
    /* 用户行为-数据表 */
	const TBL_DATA_USERID_NEW = 'data_userid_new';
    
	public function __construct()
	{
		parent::__construct();
	}
    /**
	 * 获取行为数据中有登录且有浏览文章记录的用户
	 * 
	 * @param integer $where['ofsset'] 起始条数
	 * @param integer $where['limit']  获取条数
	 * @return array
	 */
	public function getBehaviorUserid($where = array(),$time)
	{
		$this->load->database('default');

		$this->db->query('set names utf8');

		$query = $this->db->select('userid,data')
						  ->from(self::TB_BEHAVIOR_USERID_BASEDATA)
						  ->where('userid >',0)
                          ->where('date >',$time)
                          ->order_by('id','asc')
						  ->limit($where['limit'], $where['offset'])
						  ->get();

		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();
         

		$query->free_result();

		return $data;
	}
    /**
	 * 获取行为数据中有登录且有浏览文章记录的用户
	 * 
	 * @param integer $where['date']   分表日期
	 * @param integer $where['ofsset'] 起始条数
	 * @param integer $where['limit']  获取条数
	 * @return array
	 */
	public function getBehaviorUserid2($where = array())
	{
		$this->load->database('default');

		$this->db->query('set names utf8');

		$query = $this->db->select('gloab_id')
						  ->from(self::TBL_BEHAVIOR_USERID)
						  ->where('userid',1)
                          ->order_by('id','asc')
						  ->limit($where['limit'], $where['offset'])
						  ->get();

		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();

		$query->free_result();

		return $data;
	}
    /**
     * 插入数据
     *
     * @param array $data 批量数组数据
     *
     */
    function insertBehaviorUserid($data) {
        if(!$data) return;
        $sql = 'INSERT INTO `'.self::TBL_BEHAVIOR_USERID.'` (`userid`,`type`) VALUES ';
        foreach($data as $v){
            $sql .= '(\''.$v['userid'].'\',\''.$v['type'].'\'),';
        }
        unset($data);
        $sql = trim($sql,',').'  ON DUPLICATE KEY UPDATE userid=VALUES(userid);';
        //echo $sql;exit;
        $this->db = $this->load->database('default', true);
        $insertid = $this->db->query($sql);
        return $insertid ;   
    }
   /**
	 * 插入用户的最终行为数据
	 * 
	 * @return array
	 */
	public function insertBehaviorUseridData($data = array())
	{
		$this->db2 = $this->load->database('default', TRUE);

        $this->db2->replace(self::TBL_DATA_USERID_NEW, $data);

		return $this->db2->insert_id();
	}
}

/* End of file Shell_manage_mdl.php */
/* Location: ./application/models/Shell_manage_mdl.php */