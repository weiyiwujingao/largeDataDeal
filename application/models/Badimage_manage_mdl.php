<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 智能过滤系统 - 敏感图库
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-04-12
 ****************************************************************/

class Badimage_manage_mdl extends MY_Controller
{
	/* 敏感图表 */
	const TBL_BADIMAGE = 'badimage';

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
	}

   /**
	 * 敏感图列表
	 * 
	 * @param string  $imageurl  敏感图地址
	 * @param integer $type      敏感词类型
	 * @param integer $appid     来源ID
	 * @param integer $state     状态 1:疑似 2:屏蔽
	 * @param integer $offset    起始条数
	 * @param integer $limit     获取条数
	 * @param string  $enddate   结束添加时间
	 * @param string  $startdate 开始添加时间
	 * @return image
	 *
	 */
	public function getImageList($where = array())
	{
		if(isset($where['imageurl']) && !empty($where['imageurl']))
			$this->db->where('imageurl', $where['imageurl']);

		if(isset($where['type']) && array_key_exists($where['type'], config_item('type')))
			$this->db->where('type', $where['type']);

		if(isset($where['appid']) && array_key_exists($where['appid'], config_item('appid')))
			$this->db->where('appid', $where['appid']);

		if(isset($where['state']) && array_key_exists($where['state'], config_item('state')))
			$this->db->where('state', $where['state']);

		if(isset($where['startdate']) && !empty($where['startdate']))
			$this->db->where('uptime >=', $where['startdate']);

		if(isset($where['enddate']) && !empty($where['enddate']))
			$this->db->where('uptime <=', $where['enddate']);

        $db = clone($this->db);
        $total = $this->db->count_all_results(self::TBL_BADIMAGE);

		$this->db = $db;
		$this->db->query('set names utf8');

		if(!isset($where['offset'])) $where['offset'] = 0;
		if(!isset($where['limit'])) $where['limit'] = LIMIT_NUM;

		$query = $this->db->select('id,imageurl,type,appid,state,createuser,uptime')
						  ->from(self::TBL_BADIMAGE)
						  ->order_by('id desc')
						  ->limit($where['limit'], $where['offset'])
						  ->get();

		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();

		$query->free_result();

		return array('total' => $total, 'list' => $data);
	}

   /**
     * 添加敏感图
     * 
	 * @param string  $data['imageurl'] 敏感图
	 * @param integer $data['type']     敏感图类型
	 * @param integer $data['state']    敏感图状态
     * @return integer
     */
	public function addImage($data)
	{
        $this->db->insert(self::TBL_BADIMAGE, $data);

		return $this->db->insert_id();
	}

   /**
     * 修改字段敏感词
     * 
	 * @param integer $id               记录ID
	 * @param string  $data['imageurl'] 敏感图
	 * @param integer $data['type']     敏感图类型
	 * @param integer $data['state']    敏感图状态
     * @return integer
     */
	public function editImage($id, $data)
	{
        $this->db->update(self::TBL_BADIMAGE, $data, array('id' => $id));
//t($this->db->affected_rows());
		return ($this->db->affected_rows() == 1) ? TRUE : FALSE;
	}

   /**
     * 删除敏感图
     * 
	 * @param integer $id 记录ID
     * @return boolean
     */
	public function delImage($id)
	{
        $this->db->delete(self::TBL_BADIMAGE, array('id' => $id));

		return ($this->db->affected_rows() == 1) ? TRUE : FALSE;
	}
}

/* End of file Badimage_manage_mdl.php */
/* Location: ./application/controllers/Badimage_manage_mdl.php */