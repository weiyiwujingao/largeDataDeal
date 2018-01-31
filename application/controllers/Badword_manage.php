<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 智能过滤系统 - 敏感词库
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-04-11
 ****************************************************************/
class Badword_manage extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

   /**
	 * 敏感词列表
	 * 
	 * @param integer $page      页码
	 * @param string  $word      敏感词
	 * @param integer $type      敏感词类型
	 * @param integer $appid     来源ID
	 * @param integer $state     状态 0:正常 1:屏蔽 2:疑似
	 * @param string  $enddate   结束添加时间
	 * @param string  $startdate 开始添加时间
	 * @return view
	 */
	public function wordList()
	{
		//$this->checkPurview(cur_page_url(),'');
		
		$where['page']  = intval($this->input->get('page', TRUE));
		$where['word']  = filter_slashes($this->input->get('word', TRUE));
		$where['type']  = $this->input->get('type', TRUE);
		$where['appid'] = $this->input->get('appid', TRUE);
		$where['state'] = $this->input->get('state', TRUE);
		$where['offset'] = (($where['page'] > 0 ? $where['page'] : 1) - 1) * LIMIT_NUM;
		$where['limit']  = LIMIT_NUM;
		$where['enddate']   = filter_slashes($this->input->get('enddate', TRUE));
		$where['startdate'] = filter_slashes($this->input->get('startdate', TRUE));

		$this->load->model('Badword_manage_mdl');

		$data = $this->Badword_manage_mdl->getWordList($where);

		$this->data = $where;
		$this->data['page']  = ($where['page'] > 0) ? $where['page'] : 1;
		$this->data['data']  = $data;
		$this->data['pagelink'] = $this->getPage('badword.html', $data['total'], $where);

		$this->load->view('badword.html', $this->data);
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
				$word  = filter_slashes($this->input->get('word', TRUE));
				$type  = intval($this->input->get('type', TRUE));
				$state = intval($this->input->get('state', TRUE));
				
				if(!empty($word) && array_key_exists($type, config_item('type')) && array_key_exists($state, config_item('state')))
				{
					$this->load->model('Badword_manage_mdl');

					$word = explode(',', $word);

					foreach($word as $rs)
					{
						$where['word'] = $rs;
						$where['type'] = $type;

						$result = $this->Badword_manage_mdl->getWordList($where);

						if($result['total'] > 0)
						{
							$errInfo = array('flag' => '01', 'word' => $where['word']);

							break;
						}
						else
						{
							$data['word'] = $rs;
							$data['type'] = $type;
							$data['state'] = $state;
							$data['uptime'] = date('Y-m-d H:i:s');
							$data['createuser'] = $this->userinfo['uname'];
							$insertid = $this->Badword_manage_mdl->addWord($data);

							if($insertid > 0) $errInfo = array('flag' => '00');
						}
					}
				}
			break;
			case 'edit':
				$id = intval($this->input->get('id', TRUE));

				if($id > 0)
				{
					$word  = filter_slashes($this->input->get('word', TRUE));
					$type  = intval($this->input->get('type', TRUE));
					$state = intval($this->input->get('state', TRUE));
					
					if(!empty($word) && array_key_exists($type, config_item('type')) && array_key_exists($state, config_item('state')))
					{
						$this->load->model('Badword_manage_mdl');

						$word = explode(',', $word);

						foreach($word as $rs)
						{
							$data['word'] = $rs;
							$data['type'] = $type;
							$data['state'] = $state;
							$data['uptime'] = date('Y-m-d H:i:s');
							$data['createuser'] = $this->userinfo['uname'];

							$issucc = $this->Badword_manage_mdl->editWord($id, $data);

							if(TRUE === $issucc) $errInfo = array('flag' => '00');
						}
					}
				}
			break;
			case 'del':
				$id = intval($this->input->get('id', TRUE));

				if($id > 0)
				{
					$this->load->model('Badword_manage_mdl');

					$issucc = $this->Badword_manage_mdl->delWord($id);

					if(TRUE === $issucc) $errInfo['flag'] = '00';
				}
			break;
		}
		exit(json_encode($errInfo));
	}
}

/* End of file Badword_manage.php */
/* Location: ./application/controllers/Badword_manage.php */