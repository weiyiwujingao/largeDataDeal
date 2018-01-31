<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 智能过滤系统 - 被屏蔽内容
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-04-11
 ****************************************************************/
class Monitor_statistics_mdl extends MY_Controller
{
	/* 被屏蔽内容表 */
	const TBL_SHIELDCONTENT = 'shieldcontent';
    /* 用户黑名单表 */
	const TBL_USERBLACKLIST = 'userblacklist';
    /* 屏蔽词列表 */
	const TBL_BADWORD = 'badword';
    /* 关键词屏蔽排行条数 */
	const KEY_LIMIT = 10;

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
	}

   /**
	 * 被屏蔽内容列表
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
	public function getShieldContentList($where = array())
	{
		if(isset($where['content']) && !empty($where['content']))
			$this->db->like('content', trim($where['content']), 'both');

		if(isset($where['hitword']) && !empty($where['hitword']))
			$this->db->like('hitword', trim($where['hitword']), 'both');

		if(isset($where['userip']) && !empty($where['userip']))
			$this->db->where('userip', trim($where['userip']));

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
        $total = $this->db->count_all_results(self::TBL_SHIELDCONTENT);

		$this->db = $db;
		$this->db->query('set names utf8');

		if(!isset($where['offset'])) $where['offset'] = 0;
		if(!isset($where['limit'])) $where['limit'] = LIMIT_NUM;

		$query = $this->db->select(' uptime,COUNT(*) as num  ')
						  ->from(self::TBL_SHIELDCONTENT)
						  ->group_by("DATE_FORMAT(uptime,'%Y-%m-%d %H')")
						  ->order_by('id desc')
						  ->limit($where['limit'], $where['offset'])
						  ->get();

		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();
        echo $this->db->last_query();
		$query->free_result();

		return array('total' => $total, 'list' => $data);
	}
    /**
	 * 被屏蔽内容列表
	 * 
	 * @param integer $appid     来源ID
	 * @param integer $state     状态 0:正常 1:疑似 2:屏蔽
	 * @param string  $enddate   结束时间
	 * @param string  $startdate 开始时间
	 * @param string  $content   屏蔽内容
	 * @return image
	 *
	 */
	public function getContentSource($where = array())
	{
		if(isset($where['startdate']) && !empty($where['startdate']))
			$this->db->where('uptime >=', $where['startdate']);

		if(isset($where['enddate']) && !empty($where['enddate']))
			$this->db->where('uptime <=', $where['enddate']);
        $db = clone($this->db);
        $total = $this->db->count_all_results(self::TBL_SHIELDCONTENT);

		$this->db = $db;
		$this->db->query('set names utf8');

		$query = $this->db->select(' appid,COUNT(*) as num   ')
						  ->from(self::TBL_SHIELDCONTENT)
						  ->group_by("appid")
						  ->order_by('appid desc')
						  ->get();

		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();
		$query->free_result();

		return array('total' => $total, 'list' => $data);
	}
     /**
	 * 获取屏蔽词最多的关键词排行
	 * 
	 * @param integer $appid     来源ID
	 * @param integer $state     状态 0:正常 1:疑似 2:屏蔽
	 * @param string  $enddate   结束时间
	 * @param string  $startdate 开始时间
	 * @param string  $content   屏蔽内容
	 * @return image
	 *
	 */
	public function getNewTopWord($where = array())
	{
		if(isset($where['startdate']) && !empty($where['startdate']))
			$this->db->where('uptime >=', $where['startdate']);

		if(isset($where['enddate']) && !empty($where['enddate']))
			$this->db->where('uptime <=', $where['enddate']);
        $db = clone($this->db);
        $total = $this->db->count_all_results(self::TBL_SHIELDCONTENT);

		$this->db = $db;
		$this->db->query('set names utf8');

		$query = $this->db->select(' hitword,COUNT(*) as num   ')
						  ->from(self::TBL_SHIELDCONTENT)
                          ->where('hitword is not NULL',NULL,'FALSE')
						  ->group_by("hitword")
						  ->order_by('num desc')
						  ->limit(self::KEY_LIMIT)
						  ->get();

		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();
		$query->free_result();

		return array('total' => $total, 'list' => $data);
	}
     /**
	 * 获取屏蔽词最多的用户ip地址排行
	 * 
	 * @param integer $appid     来源ID
	 * @param integer $state     状态 0:正常 1:疑似 2:屏蔽
	 * @param string  $enddate   结束时间
	 * @param string  $startdate 开始时间
	 * @param string  $content   屏蔽内容
	 * @return image
	 *
	 */
	public function getNewTopIp($where = '')
	{
        $this->db->query('SET NAMES utf8');//SET NAMES latin1
        $sql = "SELECT c.userip,i.area,COUNT(*) as num FROM shieldcontent as c LEFT JOIN ipblacklist as i ON (c.userip = i.userip) WHERE c.userip!='' and i.userip!='' {$where} GROUP BY i.area ORDER BY num DESC LIMIT ".self::KEY_LIMIT.";";
        $res = $this->db->query($sql);
        $result = $res->result_array();
        return $result;
    }
    /**
	 * 获取地区屏蔽词情况
	 * 
	 * @param integer $appid     来源ID
	 * @param integer $state     状态 0:正常 1:疑似 2:屏蔽
	 * @param string  $enddate   结束时间
	 * @param string  $startdate 开始时间
	 * @param string  $content   屏蔽内容
	 * @return image
	 *
	 */
	public function getNewTopArea($where = '')
	{
        $this->db->query('SET NAMES utf8');
        $sql = "SELECT i.area,COUNT(*) as num FROM shieldcontent as c LEFT JOIN ipblacklist as i ON (c.userip = i.userip) WHERE c.userip!='' and i.userip!='' {$where} GROUP BY i.area ORDER BY num DESC LIMIT 33;";
        $res = $this->db->query($sql);
        $result = $res->result_array();
        return $result;
    }
    /**
	 * 被屏蔽内容列表
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
	public function getLineList($where = array())
	{
		if(isset($where['content']) && !empty($where['content']))
			$this->db->like('content', trim($where['content']), 'both');

		if(isset($where['hitword']) && !empty($where['hitword']))
			$this->db->like('hitword', trim($where['hitword']), 'both');

		if(isset($where['userip']) && !empty($where['userip']))
			$this->db->where('userip', trim($where['userip']));

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
        $total = $this->db->count_all_results(self::TBL_SHIELDCONTENT);

		$this->db = $db;
		$this->db->query('set names utf8');

		if(!isset($where['offset'])) $where['offset'] = 0;
		if(!isset($where['limit'])) $where['limit'] = LIMIT_NUM;

		$query = $this->db->select(' COUNT(*) as num,UNIX_TIMESTAMP(uptime) as time  ')
						  ->from(self::TBL_SHIELDCONTENT)
						  ->group_by("DATE_FORMAT(uptime,'%Y-%m-%d %H')")
						  ->order_by('id desc')
						  ->limit($where['limit'], $where['offset'])
						  ->get();

		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();
        //echo $this->db->last_query();
		$query->free_result();

		return array('total' => $total, 'list' => $data);
	}
     /**
	 * 被屏蔽内容列表
	 * 
	 * @param string  $enddate   结束时间
	 * @param string  $startdate 开始时间
	 * @param string  $content   屏蔽内容
	 * @return image
	 *
	 */
	public function getWordList($where = array())
	{
		if(isset($where['startdate']) && !empty($where['startdate']))
			$this->db->where('uptime >=', $where['startdate']);

		if(isset($where['enddate']) && !empty($where['enddate']))
			$this->db->where('uptime <=', $where['enddate']);

        $total = $this->db->count_all_results(self::TBL_BADWORD);
        return  $total;
		
	}
     /**
	 * 被屏蔽内容列表
	 * 
	 * @param string  $enddate   结束时间
	 * @param string  $startdate 开始时间
	 * @param string  $content   屏蔽内容
	 * @return image
	 *
	 */
	public function getUserList($where = array())
	{
		if(isset($where['startdate']) && !empty($where['startdate']))
			$this->db->where('shieldtime >=', $where['startdate']);

		if(isset($where['enddate']) && !empty($where['enddate']))
			$this->db->where('shieldtime <=', $where['enddate']);

        $total = $this->db->count_all_results(self::TBL_USERBLACKLIST);
        return  $total;
		
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

/* End of file Shieldcontent_manage_mdl.php */
/* Location: ./application/controllers/Shieldcontent_manage_mdl.php */