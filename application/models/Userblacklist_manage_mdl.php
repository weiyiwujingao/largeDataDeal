<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 智能过滤系统 - 用户黑名单
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-05-08
 ****************************************************************/
class Userblacklist_manage_mdl extends MY_Controller
{
	/* 用户黑名单表 */
	const TBL_USERBLACKLIST = 'userblacklist';

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
	}

   /**
	 * 用户黑名单列表
	 * 
	 * @param integer $appid            来源ID
	 * @param integer $state            状态 0:正常 1:屏蔽 2:疑似
	 * @param integer $offset           起始条数
	 * @param integer $limit            获取条数
	 * @param string  $username         用户名称
	 * @param string  $nickname         用户呢称
	 * @param integer $isnameauth       是否实名认证 0:未认证 1:已认证
	 * @param integer $ismobileauth     是否手机认证 0:未认证 1:已认证
	 * @param integer $minshieldtotal   最小被屏蔽次数
	 * @param integer $maxshieldtotal   最大被屏蔽次数
	 * @param integer $minsendfrequency 最小发送频率
	 * @param integer $maxsendfrequency 最大发送频率
	 * @param string  $endshieldtime    结束屏蔽时间
	 * @param string  $startshieldtime  开始屏蔽时间
	 * @param string  $endregtime       结束注册时间
	 * @param string  $startregtime     开始注册时间
	 * @return array
	 *
	 */
	public function getUserList($where = array())
	{
		if(isset($where['username']) && !empty($where['username']))
			$this->db->like('username', trim($where['username']), 'both');

		if(isset($where['nickname']) && !empty($where['nickname']))
			$this->db->like('nickname', trim($where['nickname']), 'both');

		if(isset($where['isnameauth']) && array_key_exists($where['isnameauth'], config_item('authtype')))
			$this->db->where('isnameauth', $where['isnameauth']);

		if(isset($where['ismobileauth']) && array_key_exists($where['ismobileauth'], config_item('authtype')))
			$this->db->where('ismobileauth', $where['ismobileauth']);

		if(isset($where['appid']) && array_key_exists($where['appid'], config_item('appid')))
			$this->db->where('appid', $where['appid']);

		if(isset($where['state']) && array_key_exists($where['state'], config_item('state')))
			$this->db->where('state', $where['state']);

		if(isset($where['minshieldtotal']) && is_numeric($where['minshieldtotal']))
			$this->db->where('shieldtotal >=', trim($where['minshieldtotal']));

		if(isset($where['maxshieldtotal']) && is_numeric($where['maxshieldtotal']))
			$this->db->where('shieldtotal <=', trim($where['maxshieldtotal']));

		if(isset($where['minsendfrequency']) && is_numeric($where['minsendfrequency']))
			$this->db->where('sendfrequency >=', trim($where['minsendfrequency']));

		if(isset($where['maxsendfrequency']) && is_numeric($where['maxsendfrequency']))
			$this->db->where('sendfrequency <=', trim($where['maxsendfrequency']));

		if(isset($where['startshieldtime']) && !empty($where['startshieldtime']))
			$this->db->where('shieldtime >=', $where['startshieldtime'] . ' 00:00:00');

		if(isset($where['endshieldtime']) && !empty($where['endshieldtime']))
			$this->db->where('shieldtime <=', $where['endshieldtime'] . ' 23:59:59');

		if(isset($where['startregtime']) && !empty($where['startregtime']))
			$this->db->where('regtime >=', $where['startregtime'] . ' 00:00:00');

		if(isset($where['endregtime']) && !empty($where['endregtime']))
			$this->db->where('regtime <=', $where['endregtime'] . ' 23:59:59');

        $db = clone($this->db);
        $total = $this->db->count_all_results(self::TBL_USERBLACKLIST);
//t($where);
		$this->db = $db;
		$this->db->query('set names utf8');

		if(!isset($where['offset'])) $where['offset'] = 0;
		if(!isset($where['limit'])) $where['limit'] = LIMIT_NUM;

		$query = $this->db->select('id,username,nickname,isnameauth,ismobileauth,sendfrequency,shieldtotal,regtime,appid,shieldtime,state')
				 ->from(self::TBL_USERBLACKLIST)
				 ->order_by('shieldtime desc')
				 ->limit($where['limit'], $where['offset'])
				 ->get();

		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();
//t($this->db->last_query());
		$query->free_result();

		return array('total' => $total, 'list' => $data);
	}

   /**
     * 修改用户黑名单记录
     * 
	 * @param array   $ids           记录ID
	 * @param integer $data['state'] 内容状态
     * @return integer
     */
	public function editUserBlack($ids, $data)
	{
		$this->db->where_in('id', $ids);
        $this->db->update(self::TBL_USERBLACKLIST, $data);

		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
	}
}

/* End of file Userblacklist_manage_mdl.php */
/* Location: ./application/controllers/Userblacklist_manage_mdl.php */