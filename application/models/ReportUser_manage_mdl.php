<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 智能过滤系统 - 举报内容
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-07-03
 ****************************************************************/
class ReportUser_manage_mdl extends MY_Controller
{
	/* 举报内容表 */
	const TBL_REPORTUSER = 'reportuser';

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
	}

   /**
	 * 举报内容列表
	 * 
	 * @param string  $where['content']   内容
	 * @param string  $where['username']  举报人
	 * @param string  $where['username2'] 被举报人
	 * @param string  $where['userip']    被举报人IP
	 * @param integer $where['appid']     来源ID
	 * @param integer $where['state']     状态 0:正常 1:审核通过 2:审核不通过
	 * @param integer $where['offset']    起始条数
	 * @param integer $where['limit']     获取条数
	 * @param integer $where['datatype']  内容类型 0:未知 1:文章 2:评论 3:私信
	 * @param integer $where['type']      举报类别 0:其他 1:广告 2:辱骂 3:色情 4:政治
	 * @param integer $where['enddate']   举报结束时间
	 * @param integer $where['startdate'] 举报开始时间
	 * @return array
	 *
	 */
	public function getShieldContentList($where = array())
	{
		if(isset($where['content']) && !empty($where['content']))
			$this->db->like('content', trim($where['content']), 'both');

		if(isset($where['type']) && !empty($where['type']))
			$this->db->like('hitword', trim($where['hitword']), 'both');

		if(isset($where['userip']) && !empty($where['userip']))
			$this->db->where('userip', trim($where['userip']));

		if(isset($where['type']) && array_key_exists($where['type'], config_item('type')))
			$this->db->where('type', $where['type']);

		if(isset($where['datatype']) && array_key_exists($where['datatype'], config_item('datatype')))
			$this->db->where('datatype', $where['datatype']);

		if(isset($where['appid']) && array_key_exists($where['appid'], config_item('appid')))
			$this->db->where('appid', $where['appid']);

		if(isset($where['state']) && array_key_exists($where['state'], config_item('state')))
			$this->db->where('state', $where['state']);

		if(isset($where['startdate']) && !empty($where['startdate']))
			$this->db->where('uptime >=', $where['startdate']);

		if(isset($where['enddate']) && !empty($where['enddate']))
			$this->db->where('uptime <=', $where['enddate']);

        $db = clone($this->db);
        $total = $this->db->count_all_results(self::TBL_REPORTUSER);

		$this->db = $db;
		$this->db->query('set names utf8');

		if(!isset($where['offset'])) $where['offset'] = 0;
		if(!isset($where['limit'])) $where['limit'] = LIMIT_NUM;

		$query = $this->db->select('id,content,datatype,appid,type,username,username2,userip,state,uptime')
						  ->from(self::TBL_REPORTUSER)
						  ->order_by('uptime desc')
						  ->limit($where['limit'], $where['offset'])
						  ->get();

		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();

		$query->free_result();

		return array('total' => $total, 'list' => $data);
	}

   /**
     * 修改屏蔽内容
     * 
	 * @param mixed   $id            记录ID
	 * @param integer $data['state'] 内容状态
     * @return integer
     */
	public function editContent($id, $data)
	{
		$this->db->where_in('id', $id);
        $this->db->update(self::TBL_SHIELDCONTENT, $data);

		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
	}

   /**
     * 删除屏蔽内容
     * 
	 * @param integer $id 记录ID
     * @return boolean
     */
	public function delContent($id)
	{
        $this->db->delete(self::TBL_SHIELDCONTENT, array('id' => $id));

		return ($this->db->affected_rows() == 1) ? TRUE : FALSE;
	}
}

/* End of file ReportUser_manage_mdl.php */
/* Location: ./application/controllers/ReportUser_manage_mdl.php */