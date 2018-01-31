<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 智能过滤系统 - 举报内容
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-07-03
 ****************************************************************/

class Report_content_manage extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

   /**
	 * 举报内容列表
	 * 
	 * @param integer $page          页码
	 * @param integer $appid         来源ID
	 * @param integer $state         状态 0:正常 1:屏蔽 2:疑似
	 * @param integer $datatype      内容类型
	 * @param string  $enddate       结束时间
	 * @param string  $startdate     开始时间
	 * @param string  $shieldcontent 屏蔽内容
	 * @return view
	 */
	public function index()
	{
		$where['page']  = intval($this->input->get('page', TRUE));//页码
        $where['limit'] = LIMIT_NUM;//显示条数
		
		$where['state'] = $this->input->get('state', TRUE);//状态 0:正常 1:屏蔽 2:疑似
		$where['offset']= (($where['page'] > 0 ? $where['page'] : 1) - 1) * LIMIT_NUM;
        
		$where['user']    = filter_slashes($this->input->get('user', TRUE));//举报人
        $where['reportuser'] = filter_slashes($this->input->get('reportuser', TRUE));//被举报人
		$where['reportuserip'] = filter_slashes($this->input->get('reportuserip', TRUE));//被举报人ip      
		
		$where['datatype']  = $this->input->get('datatype', TRUE);//数据类型
		$where['reportype'] = $this->input->get('type', TRUE);//举报类型
        $where['appid']     = $this->input->get('appid', TRUE);//项目来源ID
		$where['content']   = filter_slashes($this->input->get('content', TRUE));//举报内容
		$where['enddate']   = filter_slashes($this->input->get('enddate', TRUE));//查询结束的时间点
		$where['startdate'] = filter_slashes($this->input->get('startdate', TRUE));//查询开始的时间点

		$this->load->model('Report_content_mdl');

		$data = $this->Report_content_mdl->getReportContentList($where);
 
		$this->data = $where;
		$this->data['page']  = ($where['page'] > 0) ? $where['page'] : 1;
		$this->data['data']  = $data;
		$this->data['pagelink'] = $this->getPage('reportcontent/index.html', $data['total'], $where);

		$this->load->view('reportcontent.html', $this->data);
	}

   /**
     * ajax 操作
	 *
	 * @param string  $oper   操作类型
	 * @param integer $userid 用户ID
	 * @return view
     */
    public function ajaxAction() 
    {
        $data = array();
        $errInfo = array('flag' => '99');
        
        $id = intval($this->input->get('id', TRUE));//id编号
        $data['reporttype'] = intval($this->input->get('typeval', TRUE));//过滤词类型
        $data['filteruser'] = intval($this->input->get('state', TRUE));//0:被举报人不屏蔽  1:屏蔽被举报人
        $data['status'] = intval($this->input->get('status', TRUE));//举报通过为1，否为0
		$data['filterword'] = filter_slashes($this->input->get('filterword', TRUE));//过滤词语 
        
        if(!$id || !array_key_exists($data['filteruser'], config_item('state')) || !array_key_exists($data['status'], config_item('status')) || !array_key_exists($data['reporttype'], config_item('type')))
            exit(json_encode($errInfo));
        
        $this->load->model('Report_content_mdl');
		$issucc = $this->Report_content_mdl->editContent($id,$data);
		if(TRUE === $issucc) $errInfo = array('flag' => '00');
        exit(json_encode($errInfo));
	}
}

/* End of file ReportUser_manage.php */
/* Location: ./application/controllers/ReportUser_manage.php */