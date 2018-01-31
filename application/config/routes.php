<?php defined('BASEPATH') OR exit('No direct script access allowed');
/****************************************************************
 * 全站路由配置
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-04-07
 ****************************************************************/
$route = array
(
	'404_override' => '',
	'default_controller' => 'Badword_manage/wordlist',
	'translate_uri_dashes' => FALSE,
	'badword.html' => 'Badword_manage/wordlist',
	'badword/action.html' => 'Badword_manage/ajaxAction',
	'badimage.html' => 'Badimage_manage/imagelist',
	'badimage/action.html' => 'Badimage_manage/ajaxAction',
	'shieldcontent.html' => 'Shieldcontent_manage/shieldContentList',
	'shieldcontent/action.html' => 'Shieldcontent_manage/ajaxAction',
	'shieldimage.html' => 'Shieldimage_manage/shieldImageList',
	'shieldimage/action.html' => 'Shieldimage_manage/ajaxAction',
    'reportcontent/(\w+).html' => 'Report_content_manage/$1',
    'monitor/(\w+).html' => 'Monitor_statistics_manage/$1',
	'ipblacklist.html' => 'Ipblacklist_manage/ipList',
	'ipblacklist/action.html' => 'Ipblacklist_manage/ajaxAction',
	'userblacklist.html' => 'Userblacklist_manage/userList',
	'userblacklist/action.html' => 'Userblacklist_manage/ajaxAction',
	'cms/shell/(\w+).html' => '_shell/Shell_manage/$1',
	'blog/shell/(\w+).html' => '_shell/Shell_blog_manage/$1',
	'caij/shell/(\w+).html' => '_shell/Shell_caij_manage/$1',
	'alog/shell/(\w+).html' => '_shell/Shell_alog_manage/$1',
	//测试试用
	'cms/shelltest/(\w+).html' => '_shell/Shell_manage_test/$1',
	'cms/shell.html' => '_shell/Shell_manage/analysisKeyWord',
	'cs/shell.html'  => '_shell/Shell_manage/analysisCS',
	'cs2/shell.html' => '_shell/Shell_manage/analysisCS2',
	'up/shell.html'  => '_shell/Userportrait_manage/test',
	'up/shell/(\w+).html' => '_shell/Userportrait_buy/$1',
	'up/shell/load/(\w+).html' => '_shell/Up_user_buy/$1'
);

/* End of file routes.php */
/* Location: ./application/config/routes.php */