<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 智能过滤系统 - 举报内容
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-07-03
 ****************************************************************/

class Monitor_statistics_manage extends MY_Controller
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
		$where['enddate']   = date('Y-m-d',strtotime($this->input->get('enddate', TRUE))+86400);//查询结束的时间点
		$where['startdate'] = filter_slashes($this->input->get('startdate', TRUE));//查询开始的时间点
		$where['ord']       = intval($this->input->get('ord', TRUE));//查询开始的时间点
        
        $wheretime = " and (c.uptime >='{$where['startdate']}') and (c.uptime <='{$where['enddate']}') ";

		$this->load->model('Monitor_statistics_mdl');
        
        $data = $this->Monitor_statistics_mdl->getLineList($where);
        
        $totalword = $this->Monitor_statistics_mdl->getWordList($where);//获取屏蔽词数量
        $totaluser = $this->Monitor_statistics_mdl->getUserList($where);//获取用户黑名单数量
        $totalsour = $this->Monitor_statistics_mdl->getContentSource($where);//获取屏蔽词来源平台
        $sourcelis = sourcelist($totalsour['list'],$totalsour['total']);//获取屏蔽词来源平台
        $linedata  = getLineData($data['list'],$where['startdate'],$where['enddate']);
        
        $newtopword = $this->Monitor_statistics_mdl->getNewTopWord($where);//获取最新的屏蔽词
        $alltopword = $this->Monitor_statistics_mdl->getNewTopWord();//获取最新的屏蔽词
        $ipstopword = $this->Monitor_statistics_mdl->getNewTopIp($wheretime);//获取前10名ip屏蔽信息
        $aretopword = $this->Monitor_statistics_mdl->getNewTopArea($wheretime);//获取地区屏蔽信息
        $arelist    =  arelist($aretopword);
        
		$this->data = $where;
		$this->data['data']  = $linedata;
        $this->data['total']  =  $data['total'];
        $this->data['totaluser']  = $totaluser;
		$this->data['totalword']  = $totalsour['total'];
		$this->data['sourcelis']  = $sourcelis;
		$this->data['newtopword'] = $newtopword;
		$this->data['alltopword'] = $alltopword;
		$this->data['arelist']    = $arelist;
		$this->data['ipstopword'] = $ipstopword;

		$this->load->view('monitor.html',$this->data);
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