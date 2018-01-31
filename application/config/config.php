<?php defined('BASEPATH') OR exit('No direct script access allowed');
/****************************************************************
 * 系统全局配置
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-04-07
 ****************************************************************/
$config = array
(
	'base_url'    => 'http://databank.test.cloud.cnfol.com',
	'index_page'  => 'index.php',
	'uri_protocol'=> 'REQUEST_URI',
	'url_suffix'  => '',
	'language'	  => 'english',
	'charset'	  => 'UTF-8',
	'enable_hooks'         => FALSE,
	'subclass_prefix'      => 'MY_',
	'allow_get_array'	   => TRUE,
	'composer_autoload'    => FALSE,
	'permitted_uri_chars'  => 'a-z 0-9~%.:_\-',
	'enable_query_strings' => FALSE,
	'controller_trigger'   => 'c',
	'function_trigger'     => 'm',
	'directory_trigger'    => 'd',
	'log_threshold'		   => 0,
	'log_path'			   => '',
	'log_file_extension'   => '',
	'log_file_permissions' => 0644,
	'log_date_format'	   => 'Y-m-d H:i:s',
	'error_views_path'     => '',
	'cache_path'		   => '',
	'cache_query_string'   => FALSE,
	'encryption_key'   => '',
	'sess_driver'	   => 'files',
	'sess_cookie_name' => 'ci_session',
	'sess_expiration'  => 7200,
	'sess_save_path'   => NULL,
	'sess_match_ip'    => FALSE,
	'sess_time_to_update'	  => 300,
	'sess_regenerate_destroy' => FALSE,
	'cookie_prefix'		=> '',
	'cookie_domain'		=> '',
	'cookie_path'		=> '/',
	'cookie_secure'		=> FALSE,
	'cookie_httponly' 	=> FALSE,
	'csrf_protection'   => FALSE,
	'csrf_token_name'   => 'csrf_test_name',
	'csrf_cookie_name'  => 'csrf_cookie_name',
	'csrf_expire'	    => 7200,
	'csrf_regenerate'   => TRUE,
	'csrf_exclude_uris' => array(),
	'proxy_ips'		    => '',
	'time_reference'    => 'local',
	'compress_output'   => FALSE,
	'rewrite_short_tags'   => FALSE,
	'global_xss_filtering' => FALSE

);

/* End of file config.php */
/* Location: ./application/config/config.php */