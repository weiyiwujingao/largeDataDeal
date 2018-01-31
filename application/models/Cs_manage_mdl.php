<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 财视专家标签分析脚本
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-04-20
 ****************************************************************/
class Cs_manage_mdl extends CI_Model
{
	/* 财视私信表 */
	const TBL_CHAT    = 'fpChat';
	/* 财视直播表 */
	const TBL_LIVEMSG = 'fpLiveMsg';
	/* 财视动态表 */
	const TBL_DYNAMIC = 'fpDynamic';
	/* 财视关键词表 */
	const TBL_KEYWORD = 'cs_keyword';

	public function __construct()
	{
		parent::__construct();

		$this->load->database('cs');
	}

   /**
	 * 获取专家信息
	 * 
	 * @return view
	 */
	public function getExpert($where = array())
	{
		//if(!isset($where['offset'])) $where['offset'] = 0;
		//if(!isset($where['limit'])) $where['limit'] = LIMIT_NUM;
		
		$this->db->query('set names utf8');
		/* 
			将股票、黄金专家取出来 
			ul.`Status` = 1 为标签是否有效
			u.`Status` = 2  为查询专家用户
			ul.LabelID in(1,6) 只查询股票、黄金用户
		*/
		$query = $this->db->query('select u.UserID,u.NickName,ul.LabelID from fpUserLabelRelation ul,fpUser u where ul.UserID = u.UserID and ul.LabelID in(1,6) and ul.`Status` = 1 and u.`Status` = 2');

		$users = $data = array();

		if($query->num_rows() > 0)
		{
			foreach($query->result_array() as $rs)
			{
				$data[$rs['UserID']] = array(
							'nickname' => $rs['NickName'],
							'labelid' => $rs['LabelID']);
			}
		}

		/* 查询7天内动态、直播条数大于100的专家 */
		$query = $this->db->query('select UserID,count(UserID) as c from (select ExpertID as UserID from fpLiveMsg union all select ExpertID as UserID from fpLiveMsgBackup where date_sub(curdate(), INTERVAL 7 DAY) <= date(`AddTime`) union all select UserID from fpDynamic where date_sub(curdate(), INTERVAL 7 DAY) <= date(`AddTime`)) t1 group by UserID having c > 500');

		if($query->num_rows() > 0)
		{
			foreach($query->result_array() as $rs)
			{
				if(isset($data[$rs['UserID']]))
					$users[$rs['UserID']] = $data[$rs['UserID']];
			}
		}

		$query->free_result();

		return $users;
	}

   /**
	 * 获取合并的信息
	 * 
	 * @param integer $userid    用户ID
	 * @param integer $offset    起始条数
	 * @param integer $limit     获取条数
	 * @param string  $enddate   结束添加时间
	 * @param string  $startdate 开始添加时间
	 * @return view
	 */
	public function getAllMsg($where = array())
	{
		$users = array();

		if(isset($where['userid']) && !empty($where['userid']))
			$users = $where['userid'];

		$this->db->query('set names utf8');

		//if(!isset($where['offset'])) $where['offset'] = 0;
		//if(!isset($where['limit'])) $where['limit'] = LIMIT_NUM;

		//$query = $this->db->query('select UserID,Content from (select ExpertID as UserID,Content from fpLiveMsg union all select ExpertID as UserID,Content from fpLiveMsgBackup where date_sub(curdate(), INTERVAL 7 DAY) <= date(`AddTime`) union all select UserID,Content from fpDynamic where date_sub(curdate(), INTERVAL 7 DAY) <= date(`AddTime`)) t1 where UserID in('.join(',', $users).')');

		$query = $this->db->query('select LiveMsgID,UserID,Content from (select LiveMsgID,ExpertID as UserID,Content from fpLiveMsg union all select LiveMsgID,ExpertID as UserID,Content from fpLiveMsgBackup where date_sub(curdate(), INTERVAL 7 DAY) <= date(`AddTime`)) t1 where UserID in('.join(',', $users).')');

		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();

		$query->free_result();

		return $data;
	}

   /**
	 * 获取私信信息
	 * 
	 * @param integer $userid    用户ID
	 * @param integer $offset    起始条数
	 * @param integer $limit     获取条数
	 * @param string  $enddate   结束添加时间
	 * @param string  $startdate 开始添加时间
	 * @return view
	 */
	public function getChat($where = array())
	{
		if(isset($where['startdate']) && !empty($where['startdate']))
			$this->db->where('AddTime >=', $where['startdate']);

		if(isset($where['enddate']) && !empty($where['enddate']))
			$this->db->where('AddTime <=', $where['enddate']);

		if(isset($where['userid']) && !empty($where['userid']))
			$this->db->where_in('UserID', $where['userid']);

		$this->db->query('set names utf8');

		if(!isset($where['offset'])) $where['offset'] = 0;
		if(!isset($where['limit'])) $where['limit'] = LIMIT_NUM;

		$query = $this->db->select('UserID,Content')
						  ->from(self::TBL_CHAT)
						  ->limit($where['limit'], $where['offset'])
						  ->get();

		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();

		$query->free_result();

		return $data;
	}

   /**
	 * 获取直播信息
	 * 
	 * @param integer $userid    用户ID
	 * @param integer $offset    起始条数
	 * @param integer $limit     获取条数
	 * @param string  $enddate   结束添加时间
	 * @param string  $startdate 开始添加时间
	 * @return view
	 */
	public function getLiveMsg($where = array())
	{
		if(isset($where['startdate']) && !empty($where['startdate']))
			$this->db->where('AddTime >=', $where['startdate']);

		if(isset($where['enddate']) && !empty($where['enddate']))
			$this->db->where('AddTime <=', $where['enddate']);

		if(isset($where['userid']) && !empty($where['userid']))
			$this->db->where_in('ExpertID', $where['userid']);

		$this->db->query('set names utf8');

		if(!isset($where['offset'])) $where['offset'] = 0;
		if(!isset($where['limit'])) $where['limit'] = LIMIT_NUM;

		$query = $this->db->select('ExpertID,Content')
						  ->from(self::TBL_LIVEMSG)
						  ->limit($where['limit'], $where['offset'])
						  ->get();

		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();
//t($this->db->last_query());
		$query->free_result();

		return $data;
	}

   /**
	 * 获取动态信息
	 * 
	 * @param integer $userid    用户ID
	 * @param integer $offset    起始条数
	 * @param integer $limit     获取条数
	 * @param string  $enddate   结束添加时间
	 * @param string  $startdate 开始添加时间
	 * @return view
	 */
	public function getDynamic($where = array())
	{
		if(isset($where['startdate']) && !empty($where['startdate']))
			$this->db->where('AddTime >=', $where['startdate']);

		if(isset($where['enddate']) && !empty($where['enddate']))
			$this->db->where('AddTime <=', $where['enddate']);

		if(isset($where['userid']) && !empty($where['userid']))
			$this->db->where_in('UserID', $where['userid']);

		$this->db->query('set names utf8');

		if(!isset($where['offset'])) $where['offset'] = 0;
		if(!isset($where['limit'])) $where['limit'] = LIMIT_NUM;

		$query = $this->db->select('UserID,Content')
						  ->from(self::TBL_DYNAMIC)
						  ->limit($where['limit'], $where['offset'])
						  ->get();

		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();

		$query->free_result();

		return $data;
	}

   /**
	 * 插入财视关键词
	 * 
	 * @param integer $userid    用户ID
	 * @param integer $msgid     记录ID
	 * @param string  $keyword   关键词
	 * @return integer
	 */
	public function insertKeyWord($data = array())
	{
		$this->db2 = $this->load->database('default', TRUE);

        $this->db2->insert(self::TBL_KEYWORD, $data);

		return $this->db2->insert_id();
	}
}

/* End of file Cs_manage_mdl.php */
/* Location: ./application/controllers/Cs_manage_mdl.php */