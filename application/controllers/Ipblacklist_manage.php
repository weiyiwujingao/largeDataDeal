<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 智能过滤系统 - IP黑名单
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-05-08
 ****************************************************************/
class Ipblacklist_manage extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

   /**
	 * IP黑名单列表
	 * 
	 * @param integer $page		页码
	 * @param integer $appid	来源ID
	 * @param integer $state	状态 0:正常 1:屏蔽 2:疑似
	 * @param string  $area		所属地区
	 * @param string  $userip	用户IP
	 * @param integer $minshieldtotal 最小屏蔽次数
	 * @param integer $maxshieldtotal 最大屏蔽次数
	 * @param string  $endregtime     结束注册时间
	 * @param string  $startregtime   开始注册时间
	 * @param string  $endshieldexpiretime   结束屏蔽到期时间
	 * @param string  $startshieldexpiretime 开始屏蔽到期时间
	 * @return view
	 */
	public function ipList()
	{
		//$this->checkPurview(cur_page_url(),'');
		
		$where['page']  = intval($this->input->get('page', TRUE));
		$where['appid'] = $this->input->get('appid', TRUE);
		$where['state'] = $this->input->get('state', TRUE);
		$where['offset']    = (($where['page'] > 0 ? $where['page'] : 1) - 1) * LIMIT_NUM;
		$where['limit']     = LIMIT_NUM;
		$where['area']      = filter_slashes($this->input->get('area', TRUE));
		$where['userip']    = filter_slashes($this->input->get('userip', TRUE));
		$where['minshieldtotal']  = filter_slashes($this->input->get('minshieldtotal', TRUE));
		$where['maxshieldtotal']  = filter_slashes($this->input->get('maxshieldtotal', TRUE));
		$where['endshieldtime']   = filter_slashes($this->input->get('endshieldtime', TRUE));
		$where['startshieldtime'] = filter_slashes($this->input->get('startshieldtime', TRUE));
		$where['endshieldexpiretime'] = filter_slashes($this->input->get('endshieldexpiretime', TRUE));
		$where['startshieldexpiretime'] = filter_slashes($this->input->get('startshieldexpiretime', TRUE));

		$this->load->model('Ipblacklist_manage_mdl');

		$data = $this->Ipblacklist_manage_mdl->getIpList($where);

		$this->data = $where;
		$this->data['page']  = ($where['page'] > 0) ? $where['page'] : 1;
		$this->data['data']  = $data;
		$this->data['pagelink'] = $this->getPage('ipblacklist.html', $data['total'], $where);

		$this->load->view('ipblacklist.html', $this->data);
	}

   /**
     * ajax 操作
	 *
	 * @param string $oper 操作类型
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
						$this->load->model('Ipblacklist_manage_mdl');

						$data['state'] = $state;

						$issucc = $this->Ipblacklist_manage_mdl->editIpblack($id, $data);

						if(TRUE === $issucc) $errInfo = array('flag' => '00');
					}
				}
			break;
		}
		exit(json_encode($errInfo));
	}
}

/* End of file Ipblacklist_manage.php */
/* Location: ./application/controllers/Ipblacklist_manage.php */