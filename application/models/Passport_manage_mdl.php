<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 智能过滤系统 - 用户中心
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-07-31
 ****************************************************************/
class Passport_manage_mdl extends CI_Model
{
	/* 用户表 */
	const TBL_PASSPORTUSER = 'tbPassportUser';
	/* 用户登录日志表 */
	const TBL_PASSPORTUSERLOGINLOG = 'tbPassportUserLoginLog';
	/* 存放用户分析好的基础信息 */
	const TBL_PASSPORTUSERINFO     = 'userbaseinfo';


	public function __construct()
	{
		parent::__construct();

		$this->load->database('passport');
	}

   /**
	 * 用户列表
	 * 
	 * @param integer $userid   用户ID
	 * @param string  $username 用户名称
	 * @param integer $iscount  是否统计总条数 0:否 1:是
	 * @param integer $offset   起始条数
	 * @param integer $limit    获取条数
	 * @return array
	 */
	public function getUserList($where = array())
	{
		if(isset($where['userid']) && $where['userid'] > 0)
			$this->db->where('userid', $where['userid']);

		if(isset($where['username']) && !empty($where['username']))
			$this->db->where('UserName', $where['username']);

		$total = 0;
//$this->db->where_in('userid', array(7725711));
		if(isset($where['iscount']) && $where['iscount'] > 0)
		{
			$db = clone($this->db);
			$total = $this->db->count_all_results(self::TBL_PASSPORTUSER);
			$this->db = $db;
		}

		$this->db->query('set names latin1');

		if(!isset($where['offset'])) $where['offset'] = 0;
		if(!isset($where['limit'])) $where['limit'] = LIMIT_NUM;

		$query = $this->db->select('UserID,UserName,NickName,Mobile,RegTime,RegIP')
						  ->from(self::TBL_PASSPORTUSER)
						  ->limit($where['limit'], $where['offset'])
						  ->get();

		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();

		$query->free_result();

		return array('total' => $total, 'list' => $data);
	}

   /**
	 * 用户登录日志
	 * 
	 * @param integer $userid  用户ID
	 * @param integer $days    最近days天
	 * @param integer $offset  起始条数
	 * @param integer $limit   获取条数
	 * @return array
	 */
	public function getUserLoginLog($where = array())
	{
		if(isset($where['userid']) && $where['userid'] > 0)
		{
			$whereuserid = " where UserID = {$where['userid']} ";
		}
		if(isset($where['days']) && $where['days'] > 0)
		{
			$wheredays = " and date_sub(curdate(), INTERVAL {$where['days']} DAY) <= date(`LoginTime`)";
		}

		$query = $this->db->query('select UserID,LoginIP,date_format(LoginTime,\'%Y%m%d\') as LoginTime from ' . self::TBL_PASSPORTUSERLOGINLOG . $whereuserid . $wheredays);

		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();

		$query->free_result();

		return array('total' => $query->num_rows(), 'list' => $data);
	}

   /**
	 * 插入用户基础信息
	 * 
	 * @param integer $data['userid']       用户ID
	 * @param integer $data['username']     用户名称
	 * @param integer $data['nickname']     用户呢称
	 * @param integer $data['activedegree'] 活跃等级
	 * @param integer $data['address']      更新时间
	 * @return array
	 */
	public function insertUserInfo($data = array())
	{
		$this->db2 = $this->load->database('default', TRUE);

        $this->db2->insert(self::TBL_PASSPORTUSERINFO, $data);

		return $this->db2->insert_id();
	}

   /**
	 * 插入用户基础信息,加强版
	 * 
	 * @param integer $data['userid']       用户ID
	 * @param integer $data['username']     用户名称
	 * @param integer $data['nickname']     用户呢称
	 * @param integer $data['activedegree'] 活跃等级
	 * @param integer $data['address']      更新时间
	 * @return array
	 */
	public function insertUserInfoPlus($data = array())
	{
		$this->db2 = $this->load->database('default', TRUE);

		$this->db2->replace(self::TBL_PASSPORTUSERINFO, $data);

		return $this->db2->insert_id();
	}
}

/* End of file Passport_manage_mdl.php */
/* Location: ./application/controllers/Passport_manage_mdl.php */