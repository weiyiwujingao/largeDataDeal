<?php defined('BASEPATH') OR exit('No direct script access allowed');
/****************************************************************
 * 扩展控制器基类,所有控制器必须继承它
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-04-07
 ****************************************************************/
class MY_Controller extends CI_Controller
{
	/* 用户信息 */
	protected $userinfo = array();

   /**
	 * 构造函数
	 *
	 * @return void
	 */
    public function __construct()
    {
        parent::__construct();

		$this->userinfo = getUserCookie();
    }

   /**
	 * 公共分页
	 *
	 * @param string  $uri   跳转的uri地址
	 * @param integer $total 总条数
	 * @param array   $where 搜索条件
	 * @return string
	 */
	protected function getPage($uri, $total, $where)
	{
		if($total > LIMIT_NUM)
		{
			$config['base_url']      = base_url() . $uri . '?' . http_build_query($where);
			$config['per_page']      = LIMIT_NUM;
			$config['total_rows']    = $total;
			$config['first_link']    = '首页';
			$config['last_link']     = '尾页';
			$config['prev_link']     = '上一页';
			$config['next_link']     = '下一页';
			$config['cur_tag_open']  = '<a class="Cur">';
			$config['cur_tag_close'] = '</a>';
			$config['uri_segment']   = 2;
			$config['use_page_numbers']  = TRUE;
			$config['page_query_string'] = TRUE;
			$config['query_string_segment'] = 'page';
			$this->pagination->initialize($config);

			return $this->pagination->create_links();
		}
		else
		{
			return '';
		}
	}
}

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */