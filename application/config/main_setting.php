<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 智能过滤系统 - 主配置文件
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-04-11
 ****************************************************************/
/*
	文件缓存配置

	@param prefix   缓存文件名前缀
	@param type     缓存存储类型 serialize:序列化保存 array:PHP数组方式保存
	@param expire   缓存文件过期时间,0:永不过期
	@param compress 是否需要将内容做zip压缩 0:否 1:是
	@param savepath	缓存路径,默认为CI框架application/cache/
	@param filelog  缓存日志文件名
*/
$config['dbcache'] = array
(
	'prefix'   => '',
	'type'     => 'array',
	'expire'   => 10,
	'compress' => 0,
	'savepath' => APPPATH . 'cache/',
	'filelog'  => 'cnfol_file'
);

/* 
	默认缓存配置,可自定义在CI配置文件中
	
	@param server  服务IP,请使用数组配置
	@param expire  缓存过期时间 0:永不过期,单位秒
	@param prefix  缓存keys前缀
	@param filelog 日志文件名称
*/
$config['memcache'] = array
(
	'server'  => array(
		array('host'=>'memcache4.cache.cnfol.com','port'=>11211),
		array('host'=>'memcache9.cache.cnfol.com','port'=>11211)
	),
	'prefix'  => '',
	'expire'  => 10,
	'filelog' => 'cnfol_mem'
);

/* 敏感词类型配置 */
$config['type']  = array('0'=>'其他', '1'=>'广告', '2'=>'辱骂', '3'=>'色情', '4'=>'政治');
/* 敏感词状态配置 */
$config['state'] = array('0'=>'正常', '1'=>'屏蔽', '2'=>'疑似');
/* 来源配置 */
$config['appid'] = array('0'=>'未分配', '1'=>'财视', '2'=>'博客');
/* 内容类型配置 */
$config['datatype']  = array('0'=>'未知', '1'=>'文章', '2'=>'评论', '3'=>'私信');
/* 认证配置 */
$config['authtype']  = array('0'=>'未认证', '1'=>'已认证');
/* 举报审核状态配置 */
$config['status'] = array('0'=>'未屏蔽', '1'=>'已屏蔽', '-3'=>'删除');
/* 上传配置 */
$config['imgupload'] = array(
			'file_name'     => time().rand(10000,99999),
			'max_size'      => '1000',
			//'max_width'     => '1024',
			//'max_height'    => '768',
			'upload_path'   => './upload/',
			'allowed_types' => 'gif|jpg|png');
            
/* 博客频道对应名称 */
$config['blogname'] = array(                    
                        1461    =>  '股市天地', 
                        1445    =>  '基金', 
                        1463    =>  '财经杂谈',
                        1465    =>  '外汇', 
                        1433    =>  '期指期货', 
                        1447    =>  '理财消费', 
                        1453    =>  '黄金', 
                        1462    =>  '白银', 
                        1476    =>  '原油', 
                        1459    =>  '休闲'
                    );

/* End of file main_setting.php */
/* Location: ./application/controllers/config/main_setting.php */