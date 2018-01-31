<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 智能过滤系统 - 敏感图库
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-04-12
 ****************************************************************/

class Badimage_manage extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

   /**
	 * 敏感图列表
	 * 
	 * @param integer $page      页码
	 * @param integer $type      敏感词类型
	 * @param integer $appid     来源ID
	 * @param integer $state     状态 0:正常 1:屏蔽 2:疑似
	 * @param string  $enddate   结束添加时间
	 * @param string  $startdate 开始添加时间
	 * @return view
	 */
	public function imageList()
	{
		//$this->checkPurview(cur_page_url(),'');
		
		$where['page']  = intval($this->input->get('page', TRUE));
		$where['type']  = $this->input->get('type', TRUE);
		$where['appid'] = $this->input->get('appid', TRUE);
		$where['state'] = $this->input->get('state', TRUE);
		$where['offset'] = (($where['page'] > 0 ? $where['page'] : 1) - 1) * LIMIT_NUM;
		$where['limit']  = LIMIT_NUM;
		$where['enddate']   = filter_slashes($this->input->get('enddate', TRUE));
		$where['startdate'] = filter_slashes($this->input->get('startdate', TRUE));

		$this->load->model('Badimage_manage_mdl');

		$data = $this->Badimage_manage_mdl->getImageList($where);

		$this->data = $where;
		$this->data['page']  = ($where['page'] > 0) ? $where['page'] : 1;
		$this->data['data']  = $data;
		$this->data['pagelink'] = $this->getPage('badimage.html', $data['total'], $where);

		$this->load->view('badimage.html', $this->data);
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
			case 'add':
				$type   = intval($this->input->get('type', TRUE));
				$state  = intval($this->input->get('state', TRUE));
				$imgurl = filter_slashes($this->input->get('imgurl', TRUE));
				
				if(!empty($imgurl) && array_key_exists($type, config_item('type')) && array_key_exists($state, config_item('state')))
				{
					$this->load->model('Badimage_manage_mdl');
					
					$data['type']   = $type;
					$data['state']  = $state;
					$data['uptime'] = date('Y-m-d H:i:s');
					$data['imageurl'] = $imgurl;
					$data['createuser'] = $this->userinfo['uname'];
					$insertid = $this->Badimage_manage_mdl->addImage($data);

					if($insertid > 0) $errInfo = array('flag' => '00');
				}
			break;
			case 'edit':
				$id = intval($this->input->get('id', TRUE));

				if($id > 0)
				{
					$type   = intval($this->input->get('type', TRUE));
					$state  = intval($this->input->get('state', TRUE));
					$imgurl = filter_slashes($this->input->get('imgurl', TRUE));
					
					if(!empty($imgurl) && array_key_exists($type, config_item('type')) && array_key_exists($state, config_item('state')))
					{
						$this->load->model('Badimage_manage_mdl');

						$data['type']   = $type;
						$data['state']  = $state;
						$data['uptime'] = date('Y-m-d H:i:s');
						$data['imageurl'] = $imgurl;
						$data['createuser'] = $this->userinfo['uname'];

						$issucc = $this->Badimage_manage_mdl->editImage($id, $data);

						if(TRUE === $issucc) $errInfo = array('flag' => '00');
					}
				}
			break;
			case 'del':
				$id = intval($this->input->get('id', TRUE));

				if($id > 0)
				{
					$this->load->model('Badimage_manage_mdl');

					$issucc = $this->Badimage_manage_mdl->delImage($id);

					if(TRUE === $issucc) $errInfo['flag'] = '00';
				}
			break;
			case 'upload':
				$opertype = intval($this->input->get('opertype'));

				if(isset($_FILES['imgupload'.$opertype]) && !empty($_FILES['imgupload'.$opertype]))
				{
					$this->load->library('upload', config_item('imgupload'));

					if(!$this->upload->do_upload('imgupload'.$opertype))
					{
						$errInfo['flag']  = '01';
						$errInfo['error'] = $this->upload->display_errors();
					}
					else
					{
						$image = $this->upload->data();
						$imageurl = base_url() . 'upload/' . $image['file_name'];

						$errInfo['flag'] = '00';
						$errInfo['imageurl'] = $imageurl;
					}
				}
			break;
		}
		exit(json_encode($errInfo));
	}
}

/* End of file Badimage_manage.php */
/* Location: ./application/controllers/Badimage_manage.php */