<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 智能过滤系统 - 用户黑名单
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-05-08
 ****************************************************************/
class Userblacklist_manage extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

   /**
	 * 用户黑名单列表
	 * 
	 * @param integer $page				页码
	 * @param integer $appid			来源ID
	 * @param integer $state			状态 0:正常 1:屏蔽 2:疑似
	 * @param integer $isnameauth       是否实名认证 0:未认证 1:已认证
	 * @param integer $ismobileauth     是否手机认证 0:未认证 1:已认证
	 * @param integer $minsendfrequency 最小发送频率
	 * @param integer $maxsendfrequency 最大发送频率
	 * @param integer $minshieldtotal   最小屏蔽次数
	 * @param integer $maxshieldtotal   最大屏蔽次数
	 * @param string  $username         用户名称
	 * @param string  $nickname         用户呢称
	 * @param string  $endregtime       结束注册时间
	 * @param string  $startregtime     开始注册时间
	 * @param string  $endshieldtime    结束屏蔽时间
	 * @param string  $startshieldtime  开始屏蔽时间
	 * @return view
	 */
	public function userList()
	{
		//$this->checkPurview(cur_page_url(),'');
		
		$where['page']   = intval($this->input->get('page', TRUE));
		$where['appid']  = $this->input->get('appid', TRUE);
		$where['state']  = $this->input->get('state', TRUE);
		$where['offset'] = (($where['page'] > 0 ? $where['page'] : 1) - 1) * LIMIT_NUM;
		$where['limit']  = LIMIT_NUM;
		$where['isnameauth']   = filter_slashes($this->input->get('isnameauth', TRUE));
		$where['ismobileauth'] = filter_slashes($this->input->get('ismobileauth', TRUE));
		$where['username']     = filter_slashes($this->input->get('username', TRUE));
		$where['nickname']     = filter_slashes($this->input->get('nickname', TRUE));
		$where['endregtime']   = filter_slashes($this->input->get('endregtime', TRUE));
		$where['startregtime'] = filter_slashes($this->input->get('startregtime', TRUE));
		$where['endshieldtime']    = filter_slashes($this->input->get('endshieldtime', TRUE));
		$where['startshieldtime']  = filter_slashes($this->input->get('startshieldtime', TRUE));
		$where['minshieldtotal']   = filter_slashes($this->input->get('minshieldtotal', TRUE));
		$where['maxshieldtotal']   = filter_slashes($this->input->get('maxshieldtotal', TRUE));
		$where['minsendfrequency'] = filter_slashes($this->input->get('minsendfrequency', TRUE));
		$where['maxsendfrequency'] = filter_slashes($this->input->get('maxsendfrequency', TRUE));

		$this->load->model('Userblacklist_manage_mdl');

		$data = $this->Userblacklist_manage_mdl->getUserList($where);

		$this->data = $where;
		$this->data['page']  = ($where['page'] > 0) ? $where['page'] : 1;
		$this->data['data']  = $data;
		$this->data['pagelink'] = $this->getPage('userblacklist.html', $data['total'], $where);

		$this->load->view('userblacklist.html', $this->data);
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
						$this->load->model('Userblacklist_manage_mdl');

						$data['state'] = $state;

						$issucc = $this->Userblacklist_manage_mdl->editUserBlack($id, $data);

						if(TRUE === $issucc) $errInfo = array('flag' => '00');
					}
				}
			break;
		}
		exit(json_encode($errInfo));
	}
}

/* End of file Userblacklist_manage.php */
/* Location: ./application/controllers/Userblacklist_manage.php */