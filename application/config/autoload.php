<?php defined('BASEPATH') OR exit('No direct script access allowed');
/****************************************************************
 * 自动加载配置
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-04-07
 ****************************************************************/
$autoload = array
(
	'model'		=> array(),
	'helper'	=> array('url', 'func', 'security'),
	'config'	=> array('main_setting'),
	'drivers'   => array(),
	'language'  => array(),
	'packages'  => array(),
	'libraries' => array('pagination','cnfol_file')
);

/* End of file autoload.php */
/* Location: ./application/config/autoload.php */