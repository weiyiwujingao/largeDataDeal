<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 通用定时数据分析脚本
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-06-21
 ****************************************************************/
class Shell_alog_manage_mdl extends CI_Model
{
	/* 用户浏览记录 */
	const TBL_USERLOG = 'userlog_';
	/* 用户标签表 */
	const TBL_USERTAG = 'usertag';
    const TB_BLOG_KEYWORD_2017 = 'blog_keyword_2017'; //文章信息表
    const TB_CMS_KEYWORD_2017 = 'cms_keyword_2017'; //文章信息表
    const TB_CAIJ_KEYWORD_2017 = 'caij_keyword_2017'; //文章信息表
    

	public function __construct()
	{
		parent::__construct();
	}
    /**
     * 查博客文章
     *
     * @param int $ArticleID 最新入库的最大文章id
     * 
     *
     */
    function getAlogMsg($tablemsg, $limit, $table){
        
        if(!$tablemsg || !$limit || !$table) return '';
        $this->db_alg = $this->load->database('alog', true);
        
        $db = clone($this->db_alg);
        $total = $this->db_alg->count_all_results(self::TBL_USERLOG.$table);
        
        $this->db_alg = $db;
		$this->db_alg->query('set names utf8');
 
        $res = $this->db_alg->select('username,global_unique,starttime,url')
                            ->from(self::TBL_USERLOG.$table);
                            
        
        
        $ressult = $res->order_by('id', 'asc')
                       ->limit($limit,$tablemsg[$table]["num"])
                       ->get()->result_array();
        //echo $this->db_alg->last_query();       
        return array('total'=>$total,'list'=>$ressult);
    }
    /**
     * 查内容
     *
     * @param int $ArticleID 文章id
     * @param int $MemberID 成员id
     * @param int $time 文章发布时间
     * 
     *
     */
    function getContent($contid='', $type='') {
        
        if(!$contid || !$type) return '';
        $key = $contid.'_'.$type;
        $result = $this->cnfol_file->get($key, 'keywords_2017');
        if($result)
            return $result;
        $this->db = $this->load->database('default', true);
        $this->db->query('set names utf8');
        $table = $this->tabletype($type);
        
        $res = $this->db->select('*')
                            ->from($table);
        
        $result = $res->where(array('contid '=> $contid))
                      ->limit(1)
                      ->get()->result_array();
        if(isset($result['0']) && $result['0']){
             $this->cnfol_file->set($key, $result['0'], 'keywords_2017', 0);
            return $result['0'];
        }    
        else return '';
    }
    private function tabletype($type)
    {
        $table = self::TB_CAIJ_KEYWORD_2017;
        if($type==1)
            return self::TB_CMS_KEYWORD_2017;
        if($type==2)
            return self::TB_BLOG_KEYWORD_2017;
        return $table;
    }
    /**
     * 插入媒体来源
     *
     * @param array $data 批量数组数据
     *
     */
    function insertBlogKeyWord($data) {
        if(!$data) return;
        $sql = 'INSERT INTO `'.self::TB_BLOG_KEYWORD_2017.'` (`contid`,`keyword`,`stockids`,`source`,`author`,`channelid`,`channelname`,`createtime`) VALUES ';
        foreach($data as $v){
            $sql .= '(\''.$v['contid'].'\',\''.$v['keyword'].'\',\''.$v['stockids'].'\',\''.$v['source'].'\',\''.$v['author'].'\',\''.$v['channelid'].'\',\''.$v['channelname'].'\',\''.$v['createtime'].'\'),';
        }
        unset($data);
        $sql = trim($sql,',').'  ON DUPLICATE KEY UPDATE contid=VALUES(contid);';
        $this->db = $this->load->database('default', true);
        $insertid = $this->db->query($sql);
        return $insertid ;
    
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