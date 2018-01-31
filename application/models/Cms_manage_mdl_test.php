<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * CMS文章查询
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-04-11
 ****************************************************************/
class Cms_manage_mdl_test extends CI_Model
{
	/* 文章归档内容表 */
	const TBL_CONTENT = 'cnfol_arcontent_2017';
	/* cms关键词表 */
	const TBL_KEYWORD = 'cms_participle';
	/* cms相似文章表 */
	const TBL_SIMILAR = 'similararticle';
	/* CMS最新文章表 */
	const TBL_CONTENTS = 'cnfol_content';

	public function __construct()
	{
		parent::__construct();

		$this->load->database('cms');
	}

   /**
	 * 获取相似文章表内容
	 * 
	 * @param integer $classid   栏目ID
	 * @param integer $offset    起始条数
	 * @param integer $limit     获取条数
	 * @param integer $enddate   结束时间
	 * @param integer $startdate 开始时间
	 * @return array
	 *
	 */
	public function getSimilarArticle($where = array())
	{
		$this->db->query('set names utf8');

		if(!isset($where['offset'])) $where['offset'] = 0;
		if(!isset($where['limit'])) $where['limit'] = LIMIT_NUM;

		$query = $this->db->select('ContId,Title,Content,Source')
						  ->from(self::TBL_CONTENT)
						  ->order_by('CreatedTime', 'desc')
						  ->limit($where['limit'], $where['offset'])
						  ->get();

		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();

		$query->free_result();

		return $data;
	}

   /**
	 * 获取文章内容
	 * 
	 * @param integer $classid   栏目ID
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
			$this->db->where('CreatedTime >=', $where['startdate']);

		if(isset($where['enddate']) && !empty($where['enddate']))
			$this->db->where('CreatedTime <=', $where['enddate']);

		$this->db->query('set names utf8');

		if(!isset($where['offset'])) $where['offset'] = 0;
		if(!isset($where['limit'])) $where['limit'] = LIMIT_NUM;

		$query = $this->db->select('a.ContId,a.Title,b.Content,a.Source,a.Url,a.CreatedTime')
						  ->from('cnfol_content as a')
						  ->join('cnfol_article as b','a.ContId = b.ContId','left')
						  ->order_by('CreatedTime', 'desc')
						  ->limit($where['limit'], $where['offset'])
						  ->get();
						  // var_dump($this->db->last_query());
		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();

		$query->free_result();

		return $data;
	}
	 /**
	 * 获取最大ContentID
	 * 
	 * @param integer $msgid   记录ID
	 * @param string  $keyword 关键词
	 * @return integer
	 */
	public function getMaxID($where)
	{
		$this->db2 = $this->load->database('default', TRUE);
		$this->db2->query('set names utf8');

		if(!isset($where['offset'])) $where['offset'] = 0;
		if(!isset($where['limit'])) $where['limit'] = 1;
        $query = $this->db2->select('msgid')
        				  ->from(self::TBL_KEYWORD)
						  ->order_by('msgid', 'desc')
						  ->limit($where['limit'], $where['offset'])
						  ->get();
// var_dump($this->db2->last_query());
		$data  = ($query->num_rows() > 0) ? 
				$query->result_array() : array();

		$query->free_result();

		return $data[0]['msgid'];
	}
	 /**
	 * 获取线上CMS文章
	 * 
	 * @param integer $msgid   记录ID
	 * @param string  $keyword 关键词
	 * @return integer
	 */
	public function getMinID($where)
	{
		$this->db2 = $this->load->database('newCms', TRUE);
		$this->db2->query('set names utf8');

		if(!isset($where['offset'])) $where['offset'] = 0;
		if(!isset($where['limit'])) $where['limit'] = 1000;
		if(!isset($where['contid'])) $where['contid'] = 24925636;
        $query = $this->db2->select('a.ContId,a.Title,b.Content,a.Source,a.Url,a.CreatedTime')
						  ->from('cnfol_content as a')
						  ->join('cnfol_article as b','a.ContId = b.ContId','left')
						  ->where('a.ContId >',$where['contid'])
						  ->order_by('CreatedTime', 'asc')
						  ->limit($where['limit'], $where['offset'])
						  ->get();
						  // var_dump($this->db2->last_query());
		$data  = ($query->num_rows() > 0) ? 
				$query->result_array() : array();

		$query->free_result();

		return $data;
	}
	public function getArticleTwo($where = array())
	{
		if(isset($where['startdate']) && !empty($where['startdate']))
			$this->db->where('CreatedTime >=', $where['startdate']);

		if(isset($where['enddate']) && !empty($where['enddate']))
			$this->db->where('CreatedTime <=', $where['enddate']);

		$this->db->query('set names utf8');

		if(!isset($where['offset'])) $where['offset'] = 0;
		if(!isset($where['limit'])) $where['limit'] = LIMIT_NUM;
		 $query = $this->db->select('a.ContId,a.Title,b.Content,a.Source,a.Url,a.CreatedTime')
						  ->from('cnfol_content as a')
						  ->join('cnfol_article as b','a.ContId = b.ContId','left')
						  ->where(' a.CreatedTime>1496621402 and a.ContId >',$where['contid'])
						  ->order_by('CreatedTime', 'asc')
						  ->limit($where['limit'], $where['offset'])
						  ->get();
						  // var_dump($this->db->last_query());
		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();

		$query->free_result();

		return $data;
	}
	public function getMaxIDTwo($where)
	{
		$this->db2 = $this->load->database('default', TRUE);
		$this->db2->query('set names utf8');

		if(!isset($where['offset'])) $where['offset'] = 0;
		if(!isset($where['limit'])) $where['limit'] = 1;
        $query = $this->db2->select('msgid')
        				  ->from('cms_participle_2017')
						  ->order_by('msgid', 'desc')
						  ->limit($where['limit'], $where['offset'])
						  ->get();
// var_dump($this->db2->last_query());
		$data  = ($query->num_rows() > 0) ? 
				$query->result_array() : array();

		$query->free_result();

		return $data[0]['msgid'];
	}
	//获得博客标题信息
	public function getBlogArticleTitle($where)
	{
		$this->db = $this->load->database('blog', TRUE);
		// $this->db->query('set names utf8');

		if(isset($where['startdate']) && !empty($where['startdate']))
			$this->db->where('CreatedTime >=', $where['startdate']);

		if(isset($where['enddate']) && !empty($where['enddate']))
			$this->db->where('CreatedTime <=', $where['enddate']);

		$this->db->query('set names latin1');

		if(!isset($where['offset'])) $where['offset'] = 0;
		if(!isset($where['limit'])) $where['limit'] = 10;
		 $query = $this->db->select('MemberID,ArticleID,Title,UNIX_TIMESTAMP(AppearTime)AS AppearTimes')
						  ->from('tbBlogArticleChart')
						  ->where(' IsDel=0 AND AppearTime > "2017-06-01 00:00:00" And ArticleID >',0)
						  ->order_by('ArticleID', 'desc')
						  ->limit($where['limit'], $where['offset'])
						  ->get();
						  // var_dump($this->db->last_query());
		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();

		$query->free_result();

		return $data;
	}
	//获得博客内容
	public function getBlogArticleCon($where)
	{
		$this->db = $this->load->database('blog', TRUE);
		// $this->db->query('set names utf8');

		if(isset($where['startdate']) && !empty($where['startdate']))
			$this->db->where('CreatedTime >=', $where['startdate']);

		if(isset($where['enddate']) && !empty($where['enddate']))
			$this->db->where('CreatedTime <=', $where['enddate']);

		$this->db->query('set names latin1');

		if(!isset($where['offset'])) $where['offset'] = 0;
		if(!isset($where['limit'])) $where['limit'] = 1;
		 $query = $this->db->select('*')
						  ->from('tbBlogArticle201700'.$where['num'])
						  ->where(' ArticleID = ',$where['ArticleID'])
						  ->order_by('ArticleID', 'desc')
						  ->limit($where['limit'], $where['offset'])
						  ->get();
						  // var_dump($this->db->last_query());
		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();

		$query->free_result();

		return $data;
	}
	/**
	 * 插入CMS关键词
	 * 
	 * @param integer $msgid   记录ID
	 * @param string  $keyword 关键词
	 * @return integer
	 */
	public function insertKeyWordTwo($data = array())
	{
		$this->db2 = $this->load->database('default', TRUE);

        $this->db2->insert('cms_participle_2017', $data);

		return $this->db2->insert_id();
	}
   /**
	 * 插入CMS关键词
	 * 
	 * @param integer $msgid   记录ID
	 * @param string  $keyword 关键词
	 * @return integer
	 */
	public function insertKeyWord($data = array())
	{
		$this->db2 = $this->load->database('default', TRUE);

        $this->db2->insert(self::TBL_KEYWORD, $data);

		return $this->db2->insert_id();
	}
}

/* End of file Cms_manage_mdl.php */
/* Location: ./application/controllers/Cms_manage_mdl.php */