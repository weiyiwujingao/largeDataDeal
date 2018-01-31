<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 通用定时数据分析脚本
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-06-21
 ****************************************************************/
class Shell_manage_mdl extends CI_Model
{
	/* 用户浏览记录 */
	const TBL_USERLOG = 'userlog_';
	/* 用户标签表 */
	const TBL_USERTAG = 'usertag';

	public function __construct()
	{
		parent::__construct();
	}

   /**
	 * 获取用户总条数
	 * 
	 * @param integer $where['date'] 分表日期
	 * @return array
	 */
	public function getUserCount($where = array())
	{
		$this->load->database('alog');

		$this->db->where('global_unique !=', '');
		$this->db->where('url REGEXP', '^(lmths\.[0-9]+)\/([0-9]{8})\/([a-z]+)');
		//$this->db->where('(url REGEXP "^(lmths\.[0-9]+)\/([0-9]{8})\/([a-z]+)" or url REGEXP "^(lmth\.[0-9]+)\-([0-9]+)\/([a-z]+)\/([a-z]+)")');

        $total = $this->db->count_all_results(self::TBL_USERLOG . $where['date']);
echo $this->db->last_query().PHP_EOL;
		return $total;
	}
   /**
	 * 获取行为数据中有登录且有浏览文章记录的用户
	 * 
	 * @param integer $where['date']   分表日期
	 * @param integer $where['ofsset'] 起始条数
	 * @param integer $where['limit']  获取条数
	 * @return array
	 */
	public function getUser($where = array())
	{
		$this->load->database('alog');

		$this->db->where('global_unique !=', '');
		$this->db->where('url REGEXP', '^(lmths\.[0-9]+)\/([0-9]{8})\/([a-z]+)');
		//$this->db->where('(url REGEXP "^(lmths\.[0-9]+)\/([0-9]{8})\/([a-z]+)" or url REGEXP "^(lmth\.[0-9]+)\-([0-9]+)\/([a-z]+)\/([a-z]+)")');

		$this->db->query('set names utf8');

		$query = $this->db->select('global_unique,username,reverse(url) as url')
						  ->from(self::TBL_USERLOG . $where['date'])
						  ->limit($where['limit'], $where['offset'])
						  ->get();

		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();

		$query->free_result();

		return $data;
	}

   /**
	 * 插入alog用户
	 * 
	 * @param integer $data['userid']   用户ID
	 * @param string  $data['unique']   UID
	 * @param integer $data['type']     数据类别
	 * @param integer $data['username'] 用户名称
	 * @param integer $data['nickname'] 用户呢称
	 * @param integer $data['tagname']  用户标签
	 * @param integer $data['uptime']   更新时间
	 * @return array
	 */
	public function insertAlogUser($data = array())
	{
		$this->db2 = $this->load->database('default', TRUE);

        $this->db2->insert(self::TBL_USERTAG, $data);

		return $this->db2->insert_id();
	}

   /**
	 * 插入alog用户
	 * 
	 * @param integer $data['userid']   用户ID
	 * @param string  $data['unique']   UID
	 * @param integer $data['stockid']  股票代码
	 * @param integer $data['total']    重复次数
	 * @param integer $data['uptime']   更新时间
	 * @return array
	 */
	public function insertAlogUser2($data = array())
	{
		$this->db2 = $this->load->database('default', TRUE);

        $this->db2->insert(self::TBL_USERTAG, $data);

		return $this->db2->insert_id();
	}
}

/* End of file Shell_manage_mdl.php */
/* Location: ./application/models/Shell_manage_mdl.php */