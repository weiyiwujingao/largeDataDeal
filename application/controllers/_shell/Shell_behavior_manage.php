<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 智能过滤系统 - 财视分析离线脚本
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-04-20
 ****************************************************************/
set_time_limit(0);
class Shell_behavior_manage extends MY_Controller
{
	private $data = array();
    const LIMITNUM = 200; //更新文章数据量

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
	public function behaviorCreate($offset = 0, $limit = 5000)
	{       
        $path = APPPATH.'logs/repeat/';
        $aloguserlist = $this->cnfol_file->get('aloguserlist','aloguserlist');
        $userlistnow = unserialize(file_get_contents($path.'userlistnow.txt'));
        $aloguserlist = $list = explode(',',trim($aloguserlist,','));
        $aloguserlist = array_diff($aloguserlist,$userlistnow);
        if(!$aloguserlist)exit('暂无更新用户中心id！');
        runTime('begin');
        $this->load->model('Shell_behavior_manage_mdl');
        logs(date('Y-m-d H:i:s').'|记录数:'.count($aloguserlist), 'behaviorCreate');
        echo '更新时间'.date('Y-m-d H:i:s').'|记录数:'.count($aloguserlist).'<br>';
        $bedata = array();
        foreach($aloguserlist as $v){
            if(!$v) continue;
            //获取用户行为数据
            $behaviordata = $this->cnfol_file->get($v, 'behaviordata');         
            $behaviordata['blogname'] = $this->Shell_behavior_manage_mdl->getBlogMsg($v);//博客关注
            $caisuserid = $this->Shell_behavior_manage_mdl->getCaisUserid($v);//财视关注
            $caisuserid = $this->arrToStr($caisuserid,'ToUserID');//财视关注用户id
            if($caisuserid){
                $behaviordata['caisname'] = $this->Shell_behavior_manage_mdl->getCaisName($caisuserid);//财视专家名称
                $behaviordata['caisname'] = $this->arrToStr($behaviordata['caisname'],'UserName');//财视专家名称
                $caisdlabelid = $this->Shell_behavior_manage_mdl->getCaisLabelid($caisuserid);//财视标签id
                $caisdlabelid = $this->arrToStr($caisdlabelid,'LabelID');//财视专家名称
                if($caisdlabelid){
                    $behaviordata['caislabel'] = $this->Shell_behavior_manage_mdl->getCaisLabel($caisdlabelid);//财视标签名称
                    $behaviordata['caislabel'] = $this->arrToStr($behaviordata['caislabel'],'LabelName');//财视标签名称
                }
            }
            $caijid = $this->Shell_behavior_manage_mdl->getCaijid($v);//财经关注
            $caijid = $this->arrToStr($caijid,'user_id');//财经关注
            if($caijid){
                $behaviordata['caijname'] = $this->Shell_behavior_manage_mdl->getCaijName($caijid);//财经专家名称
                $behaviordata['caijname'] = $this->arrToStr($behaviordata['caijname'],'nickname');//财经专家名称
            }
            //获取用户行为数据
            $behaviordata = $this->cnfol_file->set($v,$behaviordata,'behaviordata', 0);
            
        }
        $userlistnow = serialize($list);
        file_put_contents($path.'userlistnow.txt',$userlistnow);
		logs("处理数量：".count($aloguserlist)."个，耗时:".runTime('begin', 'end', 4), 'behaviorCreate');
        pre("处理数量：".count($aloguserlist)."个，耗时:".runTime('begin', 'end', 4));
        unset($aloguserlist);exit;
	}
     /**
	 * 转化二维数组为一维数组
	 * 
	 * @return array
	 */
    private function arrToStr($useridarr,$key){
        if(!$useridarr || !is_array($useridarr)) return '';
        $ids = array();
        foreach($useridarr as $v){
            $ids[] = $v[$key];
        }
        return $ids;
    } 
    /**
	 * 初始化用户数据
	 * 
	 * @return array
	 */
    private function behaviorData(){
        $behaviordata = array(
                                'source'  => array(),//来源
                                'author'  => array(),//作者
                                'channel' => array(),//频道
                                'keyword' => array(),//关键词
                                'codename'=> array(),//股票名称
                                'vieleng' => array(),//浏览时长
                                'linetime'=> array(),//在线时间
                            );
        return $behaviordata;
    }   
}

/* End of file Shell_manage.php */
/* Location: ./application/controllers/_shell/Shell_manage.php */