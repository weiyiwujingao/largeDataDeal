<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 大数据 - 各小系统集合
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-08-10
 ****************************************************************/
class Other_manage_mdl extends CI_Model
{
	/* 话务系统 - 用户提问表 */
	const TBL_ANSWEREDCALL = 'answeredcall';

	public function __construct()
	{
		parent::__construct();

		$this->db2 = $this->load->database('phoneservice', TRUE);
	}

   /**
	 * 用户提问列表
	 * 
	 * @param integer $iscount 是否统计总条数 0:否 1:是
	 * @param integer $offset  起始条数
	 * @param integer $limit   获取条数
	 * @return array
	 */
	public function getUserQuestion($where = array())
	{
		if(isset($where['username']) && !empty($where['username']))
			$this->db2->where('username', $where['username']);
		
		$this->db2->where('username != ', '');

		$total = 0;
		
		if(isset($where['iscount']) && $where['iscount'] > 0)
		{
			$db = clone($this->db2);
			$total = $this->db2->count_all_results(self::TBL_ANSWEREDCALL);
			$this->db2 = $db;
		}
		
		$this->db2->query('set names utf8');

		if(!isset($where['offset'])) $where['offset'] = 0;
		if(!isset($where['limit'])) $where['limit'] = LIMIT_NUM;

		$query = $this->db2->select('product,commonproblem,username')
						   ->from(self::TBL_ANSWEREDCALL)
						   ->limit($where['limit'], $where['offset'])
						   ->get();
//t($this->db->last_query());
		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();

		$query->free_result();

		return array('total' => $total, 'list' => $data);
	}
}

/* End of file Other_manage_mdl.php */
/* Location: ./application/controllers/Other_manage_mdl.php */