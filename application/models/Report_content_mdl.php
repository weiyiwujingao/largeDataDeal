<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 智能过滤系统 - 被屏蔽内容
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-04-11
 ****************************************************************/
class Report_content_mdl extends MY_Controller
{
	/* 被屏蔽内容表 */
	const TBL_REPORT_CONTENT = 'report_content';

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
	}

   /**
	 * 被屏蔽内容列表
	 * 
	 * @param integer $appid     来源ID
	 * @param integer $status     状态 0:正常 1:疑似 2:屏蔽
	 * @param integer $offset    起始条数
	 * @param integer $limit     获取条数
	 * @param integer $datatype  内容类型
	 * @param string  $hitword   命中敏感词
	 * @param string  $enddate   结束时间
	 * @param string  $startdate 开始时间
	 * @param string  $content   屏蔽内容
	 * @return image
	 *
	 */
	public function getReportContentList($where = array())
	{
		if(isset($where['content']) && !empty($where['content']))
			$this->db->like('content', trim($where['content']), 'both');

		if(isset($where['user']) && !empty($where['user']))
			$this->db->like('user', trim($where['user']), 'both');
        
        if(isset($where['reportuser']) && !empty($where['reportuser']))
			$this->db->like('reportuser', trim($where['reportuser']), 'both');

		if(isset($where['reportip']) && !empty($where['reportip']))
			$this->db->where('reportip', trim($where['reportip']));

		if(isset($where['datatype']) && array_key_exists($where['datatype'], config_item('datatype')))
			$this->db->where('datatype', $where['datatype']);
        
        if(isset($where['reportype']) && array_key_exists($where['reportype'], config_item('type')))
			$this->db->where('reporttype', $where['reportype']);//reporttype

		if(isset($where['appid']) && array_key_exists($where['appid'], config_item('appid')))
			$this->db->where('appid', $where['appid']);

		if(isset($where['status']) && array_key_exists($where['status'], config_item('status')))
			$this->db->where('status', $where['status']);

		if(isset($where['startdate']) && !empty($where['startdate']))
			$this->db->where('uptime >=', $where['startdate']);

		if(isset($where['enddate']) && !empty($where['enddate']))
			$this->db->where('uptime <=', $where['enddate']);

        $db = clone($this->db);
        $total = $this->db->count_all_results(self::TBL_REPORT_CONTENT);

		$this->db = $db;
		$this->db->query('set names utf8');

		if(!isset($where['offset'])) $where['offset'] = 0;
		if(!isset($where['limit'])) $where['limit'] = LIMIT_NUM;

		$query = $this->db->select('id,content,datatype,appid,status,reporttype,user,reportuser,reportip,uptime,filterword,filteruser')
						  ->from(self::TBL_REPORT_CONTENT)
						  ->order_by('id desc')
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
	 * @param integer $data['status'] 内容状态
     * @return integer
     */
	public function editContent($id, $data)
	{
		$this->db->where_in('id', $id);
        $this->db->update(self::TBL_REPORT_CONTENT, $data);

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
        $this->db->delete(self::TBL_REPORT_CONTENT, array('id' => $id));

		return ($this->db->affected_rows() == 1) ? TRUE : FALSE;
	}
}

/* End of file Shieldcontent_manage_mdl.php */
/* Location: ./application/controllers/Shieldcontent_manage_mdl.php */