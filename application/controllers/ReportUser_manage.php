<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 智能过滤系统 - 举报内容
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-07-03
 ****************************************************************/

class ReportUser_manage extends MY_Controller
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
	public function shieldContentList()
	{
		//$this->checkPurview(cur_page_url(),'');
		
		$where['page']  = intval($this->input->get('page', TRUE));
		$where['appid'] = $this->input->get('appid', TRUE);
		$where['state'] = $this->input->get('state', TRUE);
		$where['offset']    = (($where['page'] > 0 ? $where['page'] : 1) - 1) * LIMIT_NUM;
		$where['limit']     = LIMIT_NUM;
		$where['userip']    = filter_slashes($this->input->get('userip', TRUE));
		$where['hitword']   = filter_slashes($this->input->get('hitword', TRUE));
		$where['datatype']  = $this->input->get('datatype', TRUE);
		$where['content']   = filter_slashes($this->input->get('content', TRUE));
		$where['enddate']   = filter_slashes($this->input->get('enddate', TRUE));
		$where['startdate'] = filter_slashes($this->input->get('startdate', TRUE));

		$this->load->model('Shieldcontent_manage_mdl');

		$data = $this->Shieldcontent_manage_mdl->getShieldContentList($where);

		$this->data = $where;
		$this->data['page']  = ($where['page'] > 0) ? $where['page'] : 1;
		$this->data['data']  = $data;
		$this->data['pagelink'] = $this->getPage('shieldcontent.html', $data['total'], $where);

		$this->load->view('shieldcontent.html', $this->data);
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
		$op = $this->input->get('oper', TRUE);

		$errInfo = array('flag' => '99');

		switch($op)
		{
			case 'edit':
				$id = filter_slashes($this->input->get('id', TRUE));
				$id = explode(',', $id);

				if(count($id) > 0)
				{
					$state = intval($this->input->get('state', TRUE));
					
					if(array_key_exists($state, config_item('state')))
					{
						$this->load->model('Shieldcontent_manage_mdl');

						$data['state'] = $state;
						$data['uptime'] = date('Y-m-d H:i:s');

						$issucc = $this->Shieldcontent_manage_mdl->editContent($id, $data);

						if(TRUE === $issucc) $errInfo = array('flag' => '00');
					}
				}
			break;
			case 'del':
				$id = intval($this->input->get('id', TRUE));

				if($id > 0)
				{
					$this->load->model('Shieldcontent_manage_mdl');

					$issucc = $this->Shieldcontent_manage_mdl->delContent($id);

					if(TRUE === $issucc) $errInfo['flag'] = '00';
				}
			break;
		}
		exit(json_encode($errInfo));
	}
}

/* End of file ReportUser_manage.php */
/* Location: ./application/controllers/ReportUser_manage.php */