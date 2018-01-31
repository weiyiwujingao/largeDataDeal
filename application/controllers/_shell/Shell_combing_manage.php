<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 智能过滤系统 - 财视分析离线脚本
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-04-20
 ****************************************************************/
set_time_limit(0);
class Shell_combing_manage extends MY_Controller
{
	private $data = array();
    const LIMITNUM = 50000; //更新文章数据量

	public function __construct()
	{
		parent::__construct();
       
	}
   /**
	 * 更新数据用户中心用户的行为文件
     * 更新内容为用户关注详情
	 * 
	 * @return void
	 */
	public function behaviorData($offset = 0)
	{       
        $path = APPPATH.'cache/behaviordata/';
        $limit = self::LIMITNUM;
        runTime('begin');
        $where = array('offset'=>$offset,'limit'=>$limit);
        $this->load->model('Shell_combing_manage_mdl');
        $data = $this->Shell_combing_manage_mdl->getBehaviorUserid($where);
        if(!$data)exit('已完成更新！');
        foreach($data as $v){
            if(!$v['userid'])continue;
            $behaviordata = $this->cnfol_file->get($v['userid'], 'behaviordata'); 
            //pre($behaviordata);
            $behaviordata['userid'] = $v['userid'];
            $behaviordata['type']   = $v['type'];
            if(isset($behaviordata['author']))
                $behaviordata['author'] = $this->getMaxkey($behaviordata['author']);
            if(isset($behaviordata['source']))
                $behaviordata['source'] = $this->getMaxkey($behaviordata['source']);
            if(isset($behaviordata['channel']))
                $behaviordata['channel'] = $this->getMaxkey($behaviordata['channel']);
            if(isset($behaviordata['keyword']))
                $behaviordata['keyword'] = $this->getMaxkey($behaviordata['keyword']);
            if(isset($behaviordata['codename']))
                $behaviordata['codename'] = $this->getMaxkey($behaviordata['codename']);
            if(isset($behaviordata['vieleng']))
                $behaviordata['vieleng'] = $this->getVieleng($behaviordata['vieleng']);
            if(isset($behaviordata['linetime'])){
                $linetime = $behaviordata['linetime'];
                $behaviordata['linetime'] = $this->getLinetime($linetime);
                $behaviordata['linetime2'] = $this->getLinetime2($linetime);
            }
            
            if(isset($behaviordata['blogname']))
                $behaviordata['blogname'] = $this->getBlogname($behaviordata['blogname']);
            if(isset($behaviordata['caisname']))
                $behaviordata['caisname'] = $this->getstr($behaviordata['caisname']);
            if(isset($behaviordata['caislabel']))
                $behaviordata['caislabel'] = $this->getstr($behaviordata['caislabel']);
            if(isset($behaviordata['caijname']))
                $behaviordata['caijname'] = $this->getstr($behaviordata['caijname']);
            //var_dump($behaviordata);exit;
            $this->Shell_combing_manage_mdl->insertBehaviorUseridData($behaviordata);
        }
        pre("处理数量：".count($data)."个，耗时:".runTime('begin', 'end', 4));exit('end');
        
    }
     /**
	 * 更新数据用户中心用户的行为文件
     * 更新内容为用户关注详情
	 * 
	 * @return void
	 */
	public function behaviorData2($offset = 0)
	{       
        $path = APPPATH.'cache/behaviordata/';
        $limit = self::LIMITNUM;
        runTime('begin');
        $where = array('offset'=>$offset,'limit'=>$limit);
        $this->load->model('Shell_combing_manage_mdl');
        $data = $this->Shell_combing_manage_mdl->getBehaviorUserid2($where);
        t($data);
        if(!$data)exit('已完成更新！');
        foreach($data as $v){
            if(!$v['gloab_id'])continue;
            $behaviordata = $this->cnfol_file->get($v['gloab_id'], 'behaviordata'); 
            $behaviordata['userid'] = 1;
            $behaviordata['gloab_id'] = $v['gloab_id'];
            if(isset($behaviordata['author']))
                $behaviordata['author'] = $this->getMaxkey($behaviordata['author']);
            if(isset($behaviordata['source']))
                $behaviordata['source'] = $this->getMaxkey($behaviordata['source']);
            if(isset($behaviordata['channel']))
                $behaviordata['channel'] = $this->getMaxkey($behaviordata['channel']);
            if(isset($behaviordata['keyword']))
                $behaviordata['keyword'] = $this->getMaxkey($behaviordata['keyword']);
            if(isset($behaviordata['codename']))
                $behaviordata['codename'] = $this->getMaxkey($behaviordata['codename']);
            if(isset($behaviordata['vieleng']))
                $behaviordata['vieleng'] = $this->getVieleng($behaviordata['vieleng']);
            if(isset($behaviordata['linetime'])){
                $linetime = $behaviordata['linetime'];
                $behaviordata['linetime'] = $this->getLinetime($linetime);
                $behaviordata['linetime2'] = $this->getLinetime2($linetime);
            }
            
            if(isset($behaviordata['blogname']))
                $behaviordata['blogname'] = $this->getBlogname($behaviordata['blogname']);
            if(isset($behaviordata['caisname']))
                $behaviordata['caisname'] = $this->getstr($behaviordata['caisname']);
            if(isset($behaviordata['caislabel']))
                $behaviordata['caislabel'] = $this->getstr($behaviordata['caislabel']);
            if(isset($behaviordata['caijname']))
                $behaviordata['caijname'] = $this->getstr($behaviordata['caijname']);
            $this->Shell_combing_manage_mdl->insertBehaviorUseridData($behaviordata);
        }
        pre("处理数量：".count($data)."个，耗时:".runTime('begin', 'end', 4));exit('end');
        
    }
    /**
	 * 
     * 更新用户行为数据表
	 * 
	 * @return void
	 */
	public function behaviorUser($offset = 0, $limit = 5000)
	{       
        $path = APPPATH.'cache/behaviordata/';
        runTime('begin');
        $a = $this->scanfiles($path);
        pre("处理数量：".$a."个，耗时:".runTime('begin', 'end', 4));exit('end');
    }
    /**
     * 返回最大值的关联键名
     * @param array $data
     * @return string
     */
    private function getMaxkey($data) {
        if(!$data || !is_array($data)) return '';
        $num = $str = '';
        foreach($data as $k=>$v){
            if($num<$v){
                $str = $k;
                $num = $v;
            }
        }
        return $str;
    }
    /**
     * 返回最大值的关联键名
     * @param array $data
     * @return string
     */
    private function getstr($data) {
        if(!isset($data) || !$data || !is_array($data) ) return '';
        $str = implode(',',$data);
        return $str;
    }
     /**
     * 返回最近一个月平时每天访问时间长
     * @param array $data
     * @return string
     */
    private function getVieleng($data) {
        if(!$data || !is_array($data)) return '';
        $maxtime = 15*60;
        $num = $lastnum = $allnum = $now = '';
        ksort($data);
        $data = array_pop($data);
        asort($data);
        foreach($data as $k=>$v){
            foreach($v as $val){
                $num = strtotime($val);
                if($lastnum){
                    $now = abs($num - $lastnum);
                    $now = $now>$maxtime?$maxtime:$now;
                    $lastnum = $num;
                }else{
                    $lastnum = $num;
                }
                $allnum += $now;
                
            }
            
        }
        if(!$allnum) $allnum = $maxtime;
        $vieleng = sprintf('%.2f',$allnum/30);
        return $vieleng;
    }
    /**
     * 返回首次访问最多的时间点 单位为小时
     * @param array $data
     * @return string
     */
    private function getLinetime($data) {
        if(!$data || !is_array($data)) return '';
        $num = $alltime = '';
        foreach($data as $k=>$v){
            $now = date('H',strtotime($v));
            if(!isset($alltime[$now])){
                $alltime[$now] = 1;
            }else{
                $alltime[$now] += 1;
            }
        }
        $alltime = $this->getMaxkey($alltime);
        return $alltime;
    }
    /**
     * 返回首次访问第二多的时间点 单位为小时
     * @param array $data
     * @return string
     */
    private function getLinetime2($data) {
        if(!$data || !is_array($data)) return '';
        $num = $alltime = '';
        foreach($data as $k=>$v){
            $now = date('H',strtotime($v));
            if(!isset($alltime[$now])){
                $alltime[$now] = 1;
            }else{
                $alltime[$now] += 1;
            }
        }
        asort($alltime);
        array_pop($alltime);
        if(!$alltime)return '';
        $alltime = $this->getMaxkey($alltime);
        return $alltime;
    }
     /**
     * 返回最大值的关联键名
     * @param array $data
     * @return string
     */
    private function getBlogname($data) {
        if(!$data || !is_array($data)) return '';
        $num = $str = '';
        foreach($data as $k=>$v){
            $str .= $v['BlogName'].','; 
        }
        return trim($str,',');
    }
    /**
     * PHP 非递归实现查询该目录下所有文件
     * @param unknown $dir
     * @return multitype:|multitype:string
     */
    private function scanfiles($dir) {
         $i = 0;
         //$repeatpath = APPPATH.'logs/repeat/behavior_userid.txt';
         $repeatpath = APPPATH.'logs/repeat/behavior_userid_new.txt';
         if (! is_dir ( $dir ))
         return array ();
          
         // 兼容各操作系统
         $dir = rtrim ( str_replace ( '\\', '/', $dir ), '/' ) . '/';
          
         // 栈，默认值为传入的目录
         $dirs = array ( $dir );

         // 放置所有文件的容器
         $rt = array ();
         $userids = file_get_contents($repeatpath); 
         do {
             // 弹栈
             $dir = array_pop ( $dirs );

             // 扫描该目录
             $tmp = scandir ( $dir );
             foreach ( $tmp as $f ) {
                  // 过滤. ..
                  if ($f == '.' || $f == '..')
                  continue;
                   
                  // 组合当前绝对路径
                  $path = $dir . $f;
                   
                   
                  // 如果是目录，压栈。
                  if (is_dir ( $path )) {
                    array_push ( $dirs, $path . '/' );
                  } else if (is_file ( $path )){ // 如果是文件，放入容器中
                        $path = strrchr($path,'/');
                        $path = trim($path,'/_cache');
                        if(strpos(' '.$userids,$path)) continue;
                        error_log(','.$path,3,$repeatpath);
                        $strlen =  strlen($path);
                        $rt [$i]['type']   = 2;
                        if($strlen<=15)
                            $rt [$i]['type']   = 1;
                        $rt [$i]['userid']   = $path;
                        $i++;
                       /// if($i>10000)exit('暂停数据');
                        if($i%50000==0){
                            echo $i.PHP_EOL;                
                            //$userids = file_get_contents($repeatpath);
                            $this->insertuser($rt);
                            $rt = array();
                        }
                  }
             }
          
         } while ( $dirs ); // 直到栈中没有目录
         $this->insertuser($rt);
         return $i; 
    }
    /**
     * 用户入库
     * @return void
     */
    private function insertuser($data) { 
        if(!$data)return;
        $this->load->model('Shell_combing_manage_mdl');
        $this->Shell_combing_manage_mdl->insertBehaviorUserid($data);        
    }
}

/* End of file Shell_manage.php */
/* Location: ./application/controllers/_shell/Shell_manage.php */