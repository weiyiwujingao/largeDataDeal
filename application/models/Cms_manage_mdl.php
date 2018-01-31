<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * CMS文章查询
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-04-11
 ****************************************************************/
class Cms_manage_mdl extends CI_Model
{
	/* 文章归档内容表 */
	const TBL_CONTENT  = 'cnfol_arcontent_2017';
	/* cms2017文章分词表 */
	const TBL_KEYWORD  = 'cms_keyword_2017';
	/* cms频道分类表 */
	const TBL_CATEGORY = 'cnfol_category';

	public function __construct()
	{
		parent::__construct();

		$this->load->database('cms');
	}

   /**
	 * 获取归档文章内容关键词
	 * 
	 * @param array   $aids      文章ID
	 * @param integer $offset    起始条数
	 * @param integer $limit     获取条数
	 * @param integer $iscount   是否统计总数 否:FALSE 是:TRUE
	 * @param integer $enddate   结束时间
	 * @param integer $startdate 开始时间
	 * @return array
	 *
	 */
	public function getArticleKeyWord($where = array())
	{
		$this->db2 = $this->load->database('default', TRUE);

		if(isset($where['startdate']) && !empty($where['startdate']))
			$this->db2->where("FROM_UNIXTIME(createtime,'%Y%m%d') >=", $where['startdate']);

		if(isset($where['enddate']) && !empty($where['enddate']))
			$this->db2->where("FROM_UNIXTIME(createtime,'%Y%m%d') <=", $where['enddate']);

		if(isset($where['aids']) && !empty($where['aids']))
			is_array($where['aids']) ?
					$this->db2->where_in('contid', $where['aids']) : 
							$this->db2->where('contid', $where['aids']);
		$total = 0;
		if(isset($where['iscount']) && (TRUE === $where['iscount']))
		{
			$db = clone($this->db2);
			$total = $this->db2->count_all_results(self::TBL_KEYWORD);
			$this->db2 = $db;
		}

		$this->db2->query('set names utf8');

		if(!isset($where['offset'])) $where['offset'] = 0;
		if(!isset($where['limit'])) $where['limit'] = LIMIT_NUM;

		$query = $this->db2->select('contid,keyword,stockids,createtime')
						   ->from(self::TBL_KEYWORD)
						   ->limit($where['limit'], $where['offset'])
						   ->get();

		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();

		$query->free_result();

		return array('Total' => $total, 'List' => $data);
	}

   /**
	 * 获取归档文章内容
	 * 
	 * @param array   $aids      文章ID
	 * @param integer $offset    起始条数
	 * @param integer $limit     获取条数
	 * @param integer $enddate   结束时间
	 * @param integer $startdate 开始时间
	 * @return array
	 *
	 */
	public function getArticle($where = array())
	{
		if(isset($where['startdate']) && !empty($where['startdate']))
			$this->db->where("FROM_UNIXTIME(CreatedTime,'%Y%m%d') >=", $where['startdate']);

		if(isset($where['enddate']) && !empty($where['enddate']))
			$this->db->where("FROM_UNIXTIME(CreatedTime,'%Y%m%d') <=", $where['enddate']);

		if(isset($where['aids']) && !empty($where['aids']))
			is_array($where['aids']) ?
					$this->db->where_in('c.ContId', $where['aids']) : 
							$this->db->where('c.ContId', $where['aids']);

        $db = clone($this->db);
        $total = $this->db->count_all_results(self::TBL_CONTENT);

		$this->db = $db;
		$this->db->query('set names utf8');

		if(!isset($where['offset'])) $where['offset'] = 0;
		if(!isset($where['limit'])) $where['limit'] = LIMIT_NUM;

		$query = $this->db->select('ContId,CatId,Title,Content,Source,Author,CreatedTime')
						  ->from(self::TBL_CONTENT)
						  //->order_by('CreatedTime', 'desc')
						  ->limit($where['limit'], $where['offset'])
						  ->get();

		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();

		$query->free_result();

		return array('Total' => $total, 'List' => $data);
	}

   /**
	 * 获取最新文章内容
	 * 
	 * @param mixed   $aids      文章ID
	 * @param integer $offset    起始条数
	 * @param integer $limit     获取条数
	 * @param integer $enddate   结束时间 Ymd
	 * @param integer $startdate 开始时间 Ymd
	 * @return array
	 *
	 */
	public function getNewArticle($where = array())
	{
		if(isset($where['startdate']) && !empty($where['startdate']))
			$this->db->where("FROM_UNIXTIME(c.CreatedTime,'%Y%m%d') >=", $where['startdate']);

		if(isset($where['enddate']) && !empty($where['enddate']))
			$this->db->where("FROM_UNIXTIME(c.CreatedTime,'%Y%m%d') <=", $where['enddate']);

		if(isset($where['aids']) && !empty($where['aids']))
			is_array($where['aids']) ?
					$this->db->where_in('c.ContId', $where['aids']) : 
							$this->db->where('c.ContId', $where['aids']);

        $db = clone($this->db);
        $total = $this->db->from('cnfol_content as c')
						  ->join('cnfol_article as a','c.ContId = a.ContId')
						  ->count_all_results();
		$this->db = $db;
		$this->db->query('set names utf8');

		if(!isset($where['offset'])) $where['offset'] = 0;
		if(!isset($where['limit'])) $where['limit'] = LIMIT_NUM;

		$query = $this->db->select('c.ContId,c.CatId,c.Title,a.Content,c.Source,c.Author,c.CreatedTime')
						  ->from('cnfol_content as c')
						  ->join('cnfol_article as a','c.ContId = a.ContId')
						  ->limit($where['limit'], $where['offset'])
						  ->get();

		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();

		$query->free_result();

		return array('Total' => $total, 'List' => $data);
	}

   /**
	 * 获取cms全部分类
	 * 
	 * @param integer $offset  起始条数
	 * @param integer $limit   获取条数
	 * @return array
	 *
	 */
	public function getCateGoryAll($where = array())
	{
		if(isset($where['classid']) && !empty($where['classid']))
			$this->db->like('ChildIds', $where['classid'], 'both'); ;

		$this->db->query('set names utf8');

		if(!isset($where['offset'])) $where['offset'] = 0;
		if(!isset($where['limit'])) $where['limit'] = 5000;

		$query = $this->db->select('CatId,Name,ChildIds')
						  ->from(self::TBL_CATEGORY)
						  ->limit($where['limit'], $where['offset'])
						  ->get();
		$data  = array();

		if($query->num_rows() > 0)
		{
			foreach($query->result_array() as $rs)
			{
				$classid = explode(',', $rs['ChildIds']);
				foreach($classid as $id)
				{	
					$data[$id]['id']   = $rs['CatId'];
					$data[$id]['name'] = $rs['Name'];
				}
			}
		}
		$query->free_result();

		return $data;
	}

   /**
	 * 插入CMS关键词
	 * 
	 * @param integer $contid     记录ID
	 * @param string  $keyword    关键词
	 * @param string  $createtime 文章发表时间
	 * @return integer
	 */
	public function insertKeyWord($data = array())
	{
		$this->db2 = $this->load->database('default', TRUE);

        $this->db2->insert(self::TBL_KEYWORD, $data);

		return $this->db2->insert_id();
	}

   /**
	 * 更新关键词表
	 * 
	 * @param integer $contid              文章ID
	 * @param string  $data['stockids']    股票代码
	 * @param string  $data['channelid']   频道ID
	 * @param string  $data['channelname'] 频道名称
	 * @param string  $data['source']      来源
	 * @param string  $data['author']      作者
	 * @return integer
	 */
	public function updateKeyWord($contid, $data = array())
	{
		$this->db2 = $this->load->database('default', TRUE);

		$this->db2->where('contid', $contid);
		$this->db2->update(self::TBL_KEYWORD, $data);
//echo $this->db2->last_query().PHP_EOL;
		return $this->db2->affected_rows();
	}
}

/* End of file Cms_manage_mdl.php */
/* Location: ./application/controllers/Cms_manage_mdl.php */