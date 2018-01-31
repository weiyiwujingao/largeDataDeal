<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 智能过滤系统 - IP黑名单
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-05-08
 ****************************************************************/
class Ipblacklist_manage_mdl extends MY_Controller
{
	/* IP黑名单表 */
	const TBL_IPBLACKLIST = 'ipblacklist';

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
	}

   /**
	 * IP黑名单列表
	 * 
	 * @param integer $appid     来源ID
	 * @param integer $state     状态 0:正常 1:屏蔽 2:疑似
	 * @param integer $offset    起始条数
	 * @param integer $limit     获取条数
	 * @param string  $area      所属地区
	 * @param string  $userip    用户IP
	 * @param integer $minshieldtotal  最小被屏蔽次数
	 * @param integer $maxshieldtotal  最大被屏蔽次数
	 * @param string  $endshieldtime   结束屏蔽时间
	 * @param string  $startshieldtime 开始屏蔽时间
	 * @param string  $endshieldexpiretime   结束屏蔽到期时间
	 * @param string  $startshieldexpiretime 开始屏蔽到期时间
	 * @return array
	 *
	 */
	public function getIpList($where = array())
	{
		if(isset($where['userip']) && !empty($where['userip']))
			$this->db->where('userip', trim($where['userip']));

		if(isset($where['area']) && !empty($where['area']))
			$this->db->like('area', trim($where['area']), 'both');

		if(isset($where['appid']) && array_key_exists($where['appid'], config_item('appid')))
			$this->db->where('appid', $where['appid']);

		if(isset($where['state']) && array_key_exists($where['state'], config_item('state')))
			$this->db->where('state', $where['state']);

		if(isset($where['minshieldtotal']) && is_numeric($where['minshieldtotal']))
			$this->db->where('shieldtotal >=', trim($where['minshieldtotal']));

		if(isset($where['maxshieldtotal']) && is_numeric($where['maxshieldtotal']))
			$this->db->where('shieldtotal <=', trim($where['maxshieldtotal']));

		if(isset($where['startshieldtime']) && !empty($where['startshieldtime']))
			$this->db->where('shieldtime >=', $where['startshieldtime'] . ' 00:00:00');

		if(isset($where['endshieldtime']) && !empty($where['endshieldtime']))
			$this->db->where('shieldtime <=', $where['endshieldtime'] . ' 23:59:59');

		if(isset($where['startshieldexpiretime']) && !empty($where['startshieldexpiretime']))
			$this->db->where('shieldexpiretime >=', $where['startshieldexpiretime'] . ' 00:00:00');

		if(isset($where['endshieldexpiretime']) && !empty($where['endshieldexpiretime']))
			$this->db->where('shieldexpiretime <=', $where['endshieldexpiretime'] . ' 23:59:59');

        $db = clone($this->db);
        $total = $this->db->count_all_results(self::TBL_IPBLACKLIST);

		$this->db = $db;
		$this->db->query('set names utf8');

		if(!isset($where['offset'])) $where['offset'] = 0;
		if(!isset($where['limit'])) $where['limit'] = LIMIT_NUM;

		$query = $this->db->select('id,userip,area,appid,shieldtotal,shieldtime,state,shieldexpiretime')
						  ->from(self::TBL_IPBLACKLIST)
						  ->order_by('shieldtime desc')
						  ->limit($where['limit'], $where['offset'])
						  ->get();

		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();
//echo $this->db->last_query();
		$query->free_result();

		return array('total' => $total, 'list' => $data);
	}

   /**
     * 修改IP黑名单记录
     * 
	 * @param array   $ids           记录ID
	 * @param integer $data['state'] 记录状态
     * @return integer
     */
	public function editIpblack($ids, $data)
	{
		(in_array($data['state'], array(0,2))) ?
				$data['shieldexpiretime'] = date('Y-m-d H:i:s') : '';
		$this->db->where_in('id', $ids);
        $this->db->update(self::TBL_IPBLACKLIST, $data);

		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
	}
}

/* End of file Ipblacklist_manage_mdl.php */
/* Location: ./application/controllers/Ipblacklist_manage_mdl.php */