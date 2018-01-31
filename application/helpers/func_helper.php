<?php defined('BASEPATH') OR exit('No direct script access allowed');
/****************************************************************
 * 全局公共函数
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-04-07
 ****************************************************************/

/**
  * 输出友好的调试信息
  *
  * @param mixed $vars 需要判断的日期
  * @return mixed
  */
function t($vars)
{
	if(is_array($vars))
		exit("<pre><br>" . print_r($vars, TRUE) . "<br></pre>".rand(1000,9999));
	else
		exit($vars);
}
function pre($param='') {
    echo '<br/><pre>';
    var_dump($param);
    echo '</pre>';
}
/**
  * 处理缓存键名称
  *
  * @return string
  */
function get_keys()
{
    $argList = func_get_args();

	return join('_', $argList);
}

/**
  * 返回json结构,并支持ajax跨域
  *
  * @param array  $data 数组
  * @param string $call 匿名函数
  * @return json
  */
function returnJson($data = array(), $message = NULL, $call = 'call')
{
	$data = is_array($data) ? array_merge($message, $data) : $message;

	exit(empty($call) ? json_encode($data, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE) : $call.'('.json_encode($data, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE).')');
}

/**
  * utf-8字符串截取
  *
  * @param string  $datastr 要截取的字符串
  * @param integer $width   要求长度
  * @param boolean $point   是否添加缩略字符
  * @return string
  */
function utf8_cutstr($datastr, $width = 20, $point = FALSE)
{
    $start    = 0;
    $encoding = 'UTF-8';
	$datastr  = trim($datastr);
    
	$trimmarker = $point ? '...' : '';
    
    if($width == '')
        $width = mb_strwidth($str, $encoding);

    return htmlspecialchars(mb_strimwidth($datastr, $start, $width, $trimmarker, $encoding));
}

/**
  * CURL请求
  *
  * @param string  $url     请求地址
  * @param array   $data    请求数据 key=>value 键值对
  * @param integer $timeout 超时时间,单位秒
  * @param integer $ishttp  是否使用https连接 0:否 1:是
  * @return array
  */
function curl_get($url, $timeout = 5)
{
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

	$result['data'] = curl_exec($ch);
	$result['code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	 
	curl_close($ch);

	return $result;
}
/**
  * CURL请求
  *
  * @param string  $url     请求地址
  * @param array   $data    请求数据 key=>value 键值对
  * @param integer $timeout 超时时间,单位秒
  * @return array
  */
function curl_post($url, $data, $timeout = 5)
{
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

	$result['data'] = curl_exec($ch);
	$result['code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	curl_close($ch);

	return $result;
}


/**
  * 获取后台用户cookie信息
  *
  * @return array
  */
function getUserCookie()
{
	$userinfo['uid']   = isset($_COOKIE['Usr_ID_test']) ? intval($_COOKIE['Usr_ID_test']) : 0;
	$userinfo['rid']   = isset($_COOKIE['Usr_RoleID_test']) ? intval($_COOKIE['Usr_RoleID_test']) : '';
	$userinfo['uname'] = isset($_COOKIE['RealName_test']) ? trim($_COOKIE['RealName_test']) : '';

	return $userinfo;
}

/**
  * 数据格式化
  *
  * @param mixed   $value 数据
  * @param integer $limit 保留位数
  * @param string  $mark  替代符号,一般用于数据为null或空字符串情况
  * @return mixed
  */
function formats($value, $limit = 2, $mark = '')
{
	$value = number_format($value, $limit, '.', '');

	return $value . $mark;
}

/**
  * 记录和统计时间(微秒)和内存使用情况
  * 使用方法:
  * <code>
  * 记录开始标记位 runTime('begin');
  * ... 区间运行代码
  * 记录结束标签位runTime('end');
  * 统计区间运行时间 精确到小数后6位 echo runTime('begin','end',6);
  * 统计区间内存使用情况echo runTime('begin','end','m');
  * 如果end标记位没有定义,则会自动以当前作为标记位
  * 其中统计内存使用需要 MEMORY_LIMIT_ON 常量为true才有效
  * </code>
  * @param string $start 开始标签
  * @param string $end 结束标签
  * @param integer|string $dec 小数位或者m
  * @return mixed
  */
function runTime($start, $end = '', $dec = 4)
{
    static $_mem  = array();
    static $_info = array();

    if(is_float($end))
	{ 
		/* 记录时间 */
        $_info[$start] = $end;
    }
	else if(!empty($end))
	{ 
		/* 统计时间和内存使用 */
        if(!isset($_info[$end]))
			$_info[$end] = microtime(TRUE);

        if(MEMORY_LIMIT_ON && $dec=='m')
		{
            if(!isset($_mem[$end])) $_mem[$end] = memory_get_usage();
				
            return number_format(($_mem[$end] - $_mem[$start])/1024);
        }
		else
		{
            return number_format(($_info[$end] - $_info[$start]),$dec);
        }
    }
	else
	{	/* 记录时间和内存使用 */
        $_info[$start] = microtime(TRUE);

        if(MEMORY_LIMIT_ON) $_mem[$start] = memory_get_usage();
    }
    return NULL;
}
function getLineData($data,$starttime,$endtime){
    $starttime = strtotime($starttime);
    $endtime = strtotime($endtime);
    $daynum = ($endtime-$starttime)/86400;
    $time = 3600; $result=array();
    if($daynum>7){
       $time = 86400;
       $daynum = ($endtime-$starttime)/3600; 
    }
    for($i=0;$i<$daynum;$i++){
        $result[] = '0';
    }
    foreach($data as $k=>$v){
        $key = ($v['time']-$starttime)/$time;
        $result[$key] = $v['num']; 
    }
    $str = "[";
    foreach($result as $value){
        $str .= $value.',';
    }
    unset($result);
    $str = trim($str,',').']';
    return $str;
}
/**
  * 来源平台
  *
  * @param string  $msg  日志信息
  * @param string  $file 日志文件名
  * @return boolean
  */
function sourcelist($data,$num)
{
    $test = "[{name: '未分配',
                  y: 100.00,
                  color:'#fd8024'},{name: '财视',
                  y: 0,
                  color:'#31da64'},{name: '博客',
                  y: 0,
                  color:'#30b9e2'}]";
    if(!$data)return $test;
    $arrsource = config_item('appid');
    $arrcolors = array('#fd8024','#31da64','#30b9e2');
    $str = '[';
    foreach($arrsource as $k=>$v){
        if(!isset($data[$k])){
           $name = $v;
           $color= isset($arrcolors[$k])?$arrcolors[$k]:'#fd8024';
           $prece= 0;
        }else{
            $name = $v;
            $color= isset($arrcolors[$k])?$arrcolors[$k]:'#fd8024';
            $prece= sprintf("%.2f", ($data[$k]['num']/$num)*100);
        }
        $str .= "{name: '{$name}',
                  y: {$prece},
                  color:'{$color}'},";
    }
    $str = rtrim($str,',').']';
    return $str;
}
/**
  * 地区
  *
  * @param string  $msg  日志信息
  * @param string  $file 日志文件名
  * @return boolean
  */
function arelist($data)
{
    $test = "[{value: 1,
                  name: '福建',
                  percentage:100}]";
    if(!$data)return $test;
    $num = '';
    foreach($data as $key=>$val){
       $num += $val['num'];
    }
    $str = '[';
    foreach($data as $k=>$v){
        $value = $v['num'];
        $name  = $v['area'];
        $prece= sprintf("%.0f", ($data[$k]['num']/$num)*100);
       
        $str .= "{value: {$value},
                  name: '{$name}',
                  percentage:{$prece}},";
    }
    $str = rtrim($str,',').']';
    return $str;
}
/**
 * 新版财经号 栏目名称对应id
 * 
 */
function catenametoid($name=''){
    $classname = unserialize(file_get_contents(MEMFILE));
    if(!$classname) return ;
    $classid   = array_search($name,$classname);
    if(!$classid) $classid = -100;
    return $classid;
}
/**
 * $Type 类型 1为cms文章 2为博客 3为财经号 4为财经号
 */
function get_host_type($url)
{
	if(!$url) return;
    $alltype = array('2'=>'blog.cnfol.com','3'=>'mp.3g.cnfol.com','4'=>'mp.cnfol.com');//
    $type=1;
    foreach($alltype as $k=>$v){
        if(stripos(' '.$url,$v)){
            $type = $k;break;
        }          
    }
    return $type;
}
/**
 * 获取文章id
 */
function get_article_id($url)
{
	if(!$url) return;
    $url = rtrim($url,'.html.shtml');
    $preg = '/\d{5,}$/i';
    preg_match($preg,$url,$idarr);
    if(isset($idarr['0']) && $idarr['0'])
        $id = $idarr['0'];
    else
        return;
    return $id;
}
/**
  * 日志记录
  *
  * @param string  $msg  日志信息
  * @param string  $file 日志文件名
  * @return boolean
  */
function logs($msg, $file = 'system')
{
	$log = '['.date('H:i:s').']['.$msg.']'.PHP_EOL;

	$filePath = APPPATH . 'logs/' . $file . '_' . date('Ymd').'.log';

	error_log($log, 3, $filePath);
}

/* End of file func_helper.php */
/* Location: ./application/helpers/func_helper.php */