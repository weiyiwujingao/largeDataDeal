<?php defined('BASEPATH') OR exit('No direct script access allowed');
/****************************************************************
 * 数据库配置
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-04-07
 ****************************************************************/
$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
	'dsn'	=> '',
	'hostname' => '172.20.1.234',
	'username' => 'app_databank',
	'password' => 'Op6d5zfc13&h',
	'database' => 'databank',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
/* 话务系统主库 */
$db['phoneservice'] = array(
	'dsn'	=> '',
	'hostname' => '10.1.4.7',
	'username' => 'dbphoneservice',
	'password' => 'oE*79%VE6jEF)V|4@',
	'database' => 'phoneservice',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);

$db['cms'] = array(
	'dsn'	=> '',
	'hostname' => '10.1.4.211',
	'username' => 'cnfolCMS',
	'password' => 'Op6d5zfc13&h',
	'database' => 'cnfolCMS',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);

$db['cmskeyword'] = array(
	'dsn'	=> '',
	'hostname' => '10.1.3.86',
	'username' => 'app_databank',
	'password' => 'Op6d5zfc13&h',
	'database' => 'cnfol_ArtcleRec',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);

$db['cs'] = array(
	'dsn'	=> '',
	'hostname' => '10.1.4.13',
	'username' => 'caishi',
	'password' => 'oE*79%VE6jEF)V|4@',
	'database' => 'cnfol_cfp_pro',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);

$db['alog'] = array(
	'dsn'	=> '',
	'hostname' => '172.30.2.145',//172.20.1.31
	'username' => 'dbalog',
	'password' => 'oE*79%VE6jEF)V|4@',
	'database' => 'alog',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);

$db['blog'] = array(
	'dsn'	=> '',
	'hostname' => '10.1.3.178',
	'username' => 'test_ArticleRec',
	'password' => '4543klh54a5)*(_kkj',
	'database' => 'blog',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
$db['caijing'] = array(
	'dsn'	=> '',
	'hostname' => '172.16.2.17',
	'username' => 'cnfol_artrec',
	'password' => 'pzc6Q4LnNl9lmLWO',
	'database' => 'we_media',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
//财视从库
$db['caishi'] = array(
	'dsn'	=> '',
	'hostname' => '10.1.1.156',
    'username' => 'cnfol_caishi',
    'password' => 'ZJwhXom16iURO8!T',
    'database' => 'cnfol_cfp_pro',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
//CMS正式库
$db['newCms'] = array(
	'dsn'	=> '',
	'hostname' => '172.20.1.123',
	'username' => 'keyword',
	'password' => 'keyword',
	'database' => 'cnfolCMS',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
// 正式库新增自媒体
$db['media'] = array(
	'dsn'	=> '',
	'hostname' => 'zimeiti-slave02.casetrvucwnv.rds.cn-north-1.amazonaws.com.cn',
	'username' => 'dbuser_liaozz',
	'password' => 'ZJwhXom16iURO8!T',
	'database' => 'alog',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
// 正式从库博客
$db['newBlog'] = array(
	'dsn'	=> '',
	'hostname' => '10.1.3.178',
	'username' => 'dbuser_liaozz',
	'password' => 'ZJwhXom16iURO8!T',
	'database' => 'blog',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
/* 用户中心从库 */
$db['passport'] = array(
	'dsn'	=> '',
	'hostname' => '172.30.2.143',
	'username' => 'dbpassport',
	'password' => 'oE*79%VE6jEF)V|4@',
	'database' => 'passport',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
/* 财视财务后台单独从库 */
$db['caishik'] = array(
	'dsn'	=> '',
	'hostname' => '10.1.4.139',
	'username' => 'infocaishi',
	'password' => 'oE*79%VE6jEF)V|4@',
	'database' => 'FinancialAccount',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
//用户中心鲜花库
$db['userxh'] = array(
	'dsn'	=> '',
	'hostname' => '172.30.2.143',
	'username' => 'dbpassport',
	'password' => 'oE*79%VE6jEF)V|4@',
	'database' => 'gift',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
//鲜花圈子提问
$db['dbflower'] = array(
	'dsn'	=> '',
	'hostname' => '172.20.6.205',
	'username' => 'dbflower',
	'password' => 'oE*79%VE6jEF)V|4@',
	'database' => 'flower',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
//埋点系统数据统计库
$db['dbtracker'] = array(
	'dsn'	=> '',
	'hostname' => '172.30.2.145',
	'username' => 'foltracker',
	'password' => 'oE*79%VE6jEF)V|4@',
	'database' => 'usertracker',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
/* End of file database.php */
/* Location: ./application/config/database.php */