<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 智能过滤系统 - 被屏蔽的图片
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-04-13
 ****************************************************************/
class Shieldimage_manage_mdl extends MY_Controller
{
	/* 被屏蔽内容表 */
	const TBL_SHIELDIMAGE = 'shieldimage';

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
	}

   /**
	 * 被屏蔽图片列表
	 * 
	 * @param integer $appid     来源ID
	 * @param integer $state     状态 0:正常 1:疑似 2:屏蔽
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
	public function getShieldImageList($where = array())
	{
		if(isset($where['userip']) && !empty($where['userip']))
			$this->db->where('userip', trim($where['userip']));

		if(isset($where['appid']) && array_key_exists($where['appid'], config_item('appid')))
			$this->db->where('appid', $where['appid']);

		if(isset($where['state']) && array_key_exists($where['state'], config_item('state')))
			$this->db->where('state', $where['state']);

		if(isset($where['startdate']) && !empty($where['startdate']))
			$this->db->where('uptime >=', $where['startdate']);

		if(isset($where['enddate']) && !empty($where['enddate']))
			$this->db->where('uptime <=', $where['enddate']);

        $db = clone($this->db);
        $total = $this->db->count_all_results(self::TBL_SHIELDIMAGE);

		$this->db = $db;
		$this->db->query('set names utf8');

		if(!isset($where['offset'])) $where['offset'] = 0;
		if(!isset($where['limit'])) $where['limit'] = LIMIT_NUM;

		$query = $this->db->select('id,imageurl,similarimageurl,appid,state,userip,uptime')
						  ->from(self::TBL_SHIELDIMAGE)
						  ->order_by('id desc')
						  ->limit($where['limit'], $where['offset'])
						  ->get();

		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();

		$query->free_result();

		return array('total' => $total, 'list' => $data);
	}

   /**
     * 修改屏蔽图片
     * 
	 * @param mixed   $id            记录ID
	 * @param integer $data['state'] 内容状态
     * @return integer
     */
	public function editImage($id, $data)
	{
		$this->db->where_in('id', $id);
        $this->db->update(self::TBL_SHIELDIMAGE, $data);

		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
	}

   /**
     * 删除屏蔽图片
     * 
	 * @param integer $id 记录ID
     * @return boolean
     */
	public function delImage($id)
	{
        $this->db->delete(self::TBL_SHIELDIMAGE, array('id' => $id));

		return ($this->db->affected_rows() == 1) ? TRUE : FALSE;
	}
}

/* End of file Shieldimage_manage_mdl.php */
/* Location: ./application/controllers/Shieldimage_manage_mdl.php */