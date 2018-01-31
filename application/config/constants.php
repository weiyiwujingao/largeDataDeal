<?php defined('BASEPATH') OR exit('No direct script access allowed');
/****************************************************************
 * 全局常量配置
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-04-07
 ****************************************************************/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0755);

define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb');
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b');
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

define('SHOW_DEBUG_BACKTRACE', TRUE);

define('EXIT_SUCCESS', 0);
define('EXIT_ERROR', 1);
define('EXIT_CONFIG', 3);
define('EXIT_UNKNOWN_FILE', 4);
define('EXIT_UNKNOWN_CLASS', 5);
define('EXIT_UNKNOWN_METHOD', 6);
define('EXIT_USER_INPUT', 7);
define('EXIT_DATABASE', 8);
define('EXIT__AUTO_MIN', 9);
define('EXIT__AUTO_MAX', 125);

/*
|--------------------------------------------------------------------------
| user-defined
|--------------------------------------------------------------------------
|
| 自定义配置
|
*/

/* 当前环境 */
define('ENV', 'test_');
/* 分页条数 */
define('LIMIT_NUM', 20);
defined('MEMORY_LIMIT_ON') or define('MEMORY_LIMIT_ON', function_exists('memory_get_usage'));
defined('MAGIC_QUOTES_GPC') or define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());

define('WORDAPI', 'http://bigdata.cnfol.com:5002/get_keywordtags?contid=%s');
define('WORDAPI2', 'http://bigdata.cnfol.com:5003/get_articletags?article=%s');
define('WORDAPI3', 'http://bigdata.cnfol.com:5003/get_stocktags?type=1&article=%s');
define('CWSAPI', 'http://databank.test.api.cnfol.com/v1/cws.html?text=%s&type=~nr,ns,n,a,f,p,v&limit=10');
define('CSTAGAPI', 'http://financial.test.cnfol.com/index.php?r=replenish/ChangeUserLabel');
/* 用户中心API之用户信息 */
define('PASSPORTAPI2', 'https://passport.cnfol.com/api/userinfo/userbasicinfo');
/* 手机归属地API */
define('MOBILE_API', 'http://sj.apidata.cn/?mobile=%s');

define('LOG_PATH','/var/tmp/artrecommend/');			//定义日志路径

define('MEMFILE',APPPATH.'cache/clumname/category.txt');			//定义财经号频道名称对应路径

/* End of file constants.php */
/* Location: ./application/config/constants.php */