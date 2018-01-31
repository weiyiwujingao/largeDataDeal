<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 纯真IP库读取类,Codeigniter专用 v1.0
 * 使用方法
 * 1.将文件放入application/libraries/
 * 2.程序中载入$this->load->library('Cnfol_iplocation',配置参数));
 * 3.$this->Cnfol_iplocation->getaddress('220.181.108.183');
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-07-31
 ****************************************************************/
class Cnfol_iplocation
{
	var $fp;
	var $firstip;  //第一条ip索引的偏移地址
	var $lastip;   //最后一条ip索引的偏移地址
	var $totalip;  //总ip数

	/**
	  * 构造函数,初始化一些变量
	  *
	  * @param file $datafile 的值为纯真IP数据库的文件地址,可自行修改.
	  * @return void
	  */
	function __construct($datafile = 'cache/qqwry.dat')
	{
		if(FALSE === file_exists(APPPATH . $datafile))
			exit("The file $datafile does not exist");
		/* 二制方式打开 */
		$this->fp = fopen(APPPATH . $datafile, 'rb');
		/* 第一条ip索引的绝对偏移地址 */
		$this->firstip = $this->get4b();
		/* 最后一条ip索引的绝对偏移地址 */
		$this->lastip  = $this->get4b();
		/* ip总数 索引区是定长的7个字节,在此要除以7 */
		$this->totalip = ($this->lastip - $this->firstip) / 7 ;
		/* 为了兼容php5以下版本,本类没有用析构函数,自动关闭ip库 */
		register_shutdown_function(array($this, 'closefp'));
	}

	/**
	  * 构造函数,初始化一些变量
	  *
	  * @return void
	  */
	function closefp()
	{
		fclose($this->fp);
	}

	/**
	  * 读取4个字节并将解压成long的长模式
	  *
	  * @return integer
	  */
	function get4b()
	{
	  $str = unpack('V', fread($this->fp, 4));

	  return $str[1];
	}

	/**
	  * 读取重定向了的偏移地址
	  *
	  * @return string
	  */
	function getoffset()
	{
		$str = unpack('V', fread($this->fp, 3).chr(0));

		return $str[1];
	}

	/**
	  * 读取ip的详细地址信息
	  *
	  * @return string
	  */
	function getStr()
	{
		$split = fread($this->fp,1);
		$str = '';
		while(ord($split)!=0)
		{
			$str  .= $split;
			$split = fread($this->fp,1);
		}
		return iconv('gbk', 'utf-8//ignore', $str);
	}

	/**
	  * 将ip通过ip2long转成ipv4的互联网地址,再将他压缩成big-endian字节序,用来和索引区内的ip地址做比较
	  *
	  * @return string
	  */
	function iptoint($ip)
	{
		return pack('N', intval(ip2long($ip)));
	}

	/**
	  * 获取客户端ip地址
	  * 注意:如果你想要把ip记录到服务器上,请在写库时先检查一下ip的数据是否安全
	  *
	  * @return string
	  */
	function getIp()
	{
		if(getenv('HTTP_CLIENT_IP'))
		{
			$ip = getenv('HTTP_CLIENT_IP');
		}
		elseif(getenv('HTTP_X_FORWARDED_FOR'))
		{
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		}
		elseif(getenv('HTTP_X_FORWARDED'))
		{
			$ip = getenv('HTTP_X_FORWARDED');
		}
		elseif(getenv('HTTP_FORWARDED_FOR'))
		{
			$ip = getenv('HTTP_FORWARDED_FOR');
		}
		elseif(getenv('HTTP_FORWARDED'))
		{
			$ip = getenv('HTTP_FORWARDED');
		}
		else
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

	/**
	  * 获取地址信息
	  *
	  * @return string
	  */
	function readAddRess()
	{
		/* 得到当前的指针位址 */
		$now_offset = ftell($this->fp);
		$flag = $this->getFlag();
		switch(ord($flag))
		{
			 case 0:
				 $address = '';
			 break;
			 case 1:
			 case 2:
				 fseek($this->fp, $this->getoffset());
					 $address = $this->getStr();
			 break;
			 default:
				 fseek($this->fp, $now_offset);
				 $address = $this->getStr();
			 break;
		}
		return $address;
	}

	/**
	  * 获取标志1或2,用来确定地址是否重定向了
	  *
	  * @return string
	  */
	function getFlag()
	{
		return fread($this->fp,1);
	}

	/**
	  * 用二分查找法在索引区内搜索ip
	  *
	  * @return array
	  */
	function searchIp($ip)
	{
		/* 将域名转成ip */
		$ip = gethostbyname($ip);
		$ip_offset['ip'] = $ip;
		/* 将ip转换成长整型 */
		$ip = $this->iptoint($ip);
		/* 搜索的上边界 */
		$firstip  = 0;
		/* 搜索的下边界 */
		$lastip   = $this->totalip;
		/* 初始化为最后一条ip地址的偏移地址 */
		$ipoffset = $this->lastip;

		while($firstip <= $lastip)
		{
			/* 计算近似中间记录 floor函数记算给定浮点数小的最大整数,说白了就是四舍五也舍 */
			$i = floor(($firstip + $lastip) / 2);
			/* 定位指针到中间记录 */
			fseek($this->fp, $this->firstip + $i * 7);
			/* 读取当前索引区内的开始ip地址,并将其little-endian的字节序转换成big-endian的字节序 */
			$startip = strrev(fread($this->fp, 4));
			if($ip < $startip)
			{
			   $lastip = $i - 1;
			}
			else
			{
				fseek($this->fp, $this->getoffset());
				$endip = strrev(fread($this->fp, 4));
				if($ip > $endip)
				{
					$firstip = $i + 1;
				}
				else
				{
					$ip_offset['offset'] = $this->firstip + $i * 7;
					break;
				}
			}
		}
		return $ip_offset;
	}

	/**
	  * 获取ip地址详细信息
	  *
	  * @return array
	  */
	function getAddRess($ip)
	{
		$ip_offset=$this->searchIp($ip);  //获取ip 在索引区内的绝对编移地址
		$ipoffset=$ip_offset["offset"];
		$address["ip"]=$ip_offset["ip"];
		fseek($this->fp,$ipoffset);      //定位到索引区
		$address["startip"]=long2ip($this->get4b()); //索引区内的开始ip 地址
		$address_offset=$this->getoffset();            //获取索引区内ip在ip记录区内的偏移地址
		fseek($this->fp,$address_offset);            //定位到记录区内
		$address["endip"]=long2ip($this->get4b());   //记录区内的结束ip 地址
		$flag=$this->getFlag();                      //读取标志字节
		switch(ord($flag))
		{
			case 1:  //地区1地区2都重定向
				$address_offset = $this->getoffset();   //读取重定向地址
				fseek($this->fp,$address_offset);     //定位指针到重定向的地址
				$flag = $this->getFlag();               //读取标志字节
				switch(ord($flag))
				{
					case 2:  //地区1又一次重定向,
						fseek($this->fp,$this->getoffset());
						$address["province"]=$this->getstr();
						fseek($this->fp,$address_offset+4);      //跳4个字节
						$address["city"]=$this->readAddRess();  //地区2有可能重定向,有可能没有
					break;
					default: //地区1,地区2都没有重定向
						fseek($this->fp,$address_offset);        //定位指针到重定向的地址
						$address["province"]=$this->getstr();
						$address["city"]=$this->readAddRess();
					break;
				}
			break;
			case 2: //地区1重定向 地区2没有重定向
			$address1_offset=$this->getoffset();   //读取重定向地址
			fseek($this->fp,$address1_offset);
			$address["province"]=$this->getStr();
			fseek($this->fp,$address_offset+8);
			$address["city"]=$this->readAddRess();
			break;
			default: //地区1地区2都没有重定向
			fseek($this->fp,$address_offset+4);
			$address["province"]=$this->getStr();
			$address["city"]=$this->readAddRess();
			break;
		}
	    //*过滤一些无用数据
		if(strpos($address["province"],"CZ88.NET") != false)
		{
			$address["province"]="未知";
		}
		if(strpos($address["city"],"CZ88.NET") != false)
		{
			$address["city"]=" ";
		}
		return $address;
	 }
}

/* End of file Cnfol_iplocation.php */
/* Location: ./application/libraries/Cnfol_iplocation.php */