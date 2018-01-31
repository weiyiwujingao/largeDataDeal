<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/****************************************************************
 * 智能过滤系统 - 用户中心
 *---------------------------------------------------------------
 * Copyright (c) 2004-2017 CNFOL Inc. (http://www.cnfol.com)
 *---------------------------------------------------------------
 * $author:linfeng $addtime:2017-07-31
 ****************************************************************/
class Read_buy_mdl extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}
//圈子更新表
   public function getOrderQ($where)
	{
		$this->db2 = $this->load->database('passport', TRUE);
		$query = $this->db2->query("SELECT OrderID,UserID FROM tbPassportOrder WHERE Status=1 AND OrderID > {$where}  GROUP BY UserID ORDER BY OrderID DESC ;" );			  
		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();

		$query->free_result();

		return array('list' => $data);
	}
	//获取鲜花平均数
	public function getAvg($where){
		//用户鲜花库
		$this->db4 = $this->load->database('userxh', TRUE);
		//用户鲜花平均数	
		$query = $this->db4->query("SELECT count(GiftCnt) AS 'count',sum(GiftCnt) AS 'sum' FROM tbGiftCurrentItem WHERE FromUserID = {$where}");
		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();
		$query->free_result();
		($data[0]['count'] > 0) ? $datas['avg'] = $data[0]['sum']/$data[0]['count'] : $datas['avg'] =0
		;	
		return $datas['avg'];
	}
	//获取圈子充值总金额
	public function getQSum($where){
		//财视后台
		$this->db2 = $this->load->database('passport', TRUE);
		//所有充值记录总和
		$query = $this->db2->query("SELECT format(sum(PayCharge)/100,2) as sum FROM tbPassportPayRecord WHERE UserID = {$where}");
		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();
		$query->free_result();
		return $data[0]['sum'];
	}
	//获取充值总金额
	public function getSum($where){
		//财视后台
		$this->db2 = $this->load->database('caishik', TRUE);
		//所有充值记录总和
		$query = $this->db2->query("SELECT format(sum(PayCharge)/100,2) as sum from tbPassportPayRecord WHERE flag=1 AND UserID={$where}");
		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();
		$query->free_result();
		return $data[0]['sum'];
	}
	//圈子类型、风格、名称
	public function getQStyle($where){
		//圈子库
		$this->db2 = $this->load->database('dbflower', TRUE);
		$query = $this->db2->query("SELECT GM.ID,GM.Name,G.Name as gName, CASE G.StyleID WHEN 1 THEN '短线技术' WHEN 2 THEN '中线策略' WHEN 3 THEN '短线技术' WHEN 4 THEN '激进风格' WHEN 5 THEN '稳健达人' WHEN 6 THEN '混合型' ELSE '未知' END AS gStyle, CASE G.CategoryId WHEN 1 THEN '股票' WHEN 4 THEN '基金' WHEN 5 THEN '银行' WHEN 6 THEN '保险' WHEN 8 THEN '理财' WHEN 9 THEN '外汇' WHEN 10 THEN '期货' WHEN 18 THEN '黄金' ELSE	'未知' END AS gType FROM  GroupUserRelation AS GP,GroupMember AS GM,`GROUP` AS G WHERE  GP.UserID={$where} AND  GP.UserID=GM.ID AND GP.GroupID=G.ID;");
		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();
		$query->free_result();
		// var_dump($data);exit;
		return $data;
	}
	//圈子提问
	public function getTW($where){
		//圈子库
		$this->db2 = $this->load->database('dbflower', TRUE);
		$query = $this->db2->query("SELECT DISTINCT Content FROM GroupQuestion WHERE UserID  = {$where} ");
		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();
		$query->free_result();
		// var_dump($data);exit;
		return $data;
	}
	//购买价格区间  最大跟最小
	public function getQInterval($where){
		//财视后台
		$this->db2 = $this->load->database('passport', TRUE);
		//所有充值记录总和
		$query = $this->db2->query("SELECT format(max(Money)/100,2) as Max,format(min(Money)/100,2) as Min FROM tbPassportOrder WHERE Money > 0 and UserID = {$where}");
		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();
		$query->free_result();

		return $data[0];
	}
	//圈子支付成功率
	public function getQSuccess($where){
		//用户中心后台
		$this->db2 = $this->load->database('passport', TRUE);
		//所有充值记录总和
		$query = $this->db2->query("SELECT format(t1.succTotal/t2.Total,2) as success FROM (SELECT count(1) as succTotal FROM tbPassportPayRecord where FLag=1 AND UserID = {$where} )as t1, (SELECT count(1) as Total FROM tbPassportPayRecord WHERE UserID = {$where}) as t2;");
		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();
		$query->free_result();

		return $data[0]['success'];
	}
	public function checkQ($where){
		$this->db2 = $this->load->database('default', TRUE);
		$query = $this->db2->query("SELECT id FROM userbuy WHERE userid = {$where} and type = 2 ;" );
		// $bool=$this->db2->delete('userbuy',array('id'=>$id));  
       $data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();
		$query->free_result();

		return $data;
	}
	//录入数据库
	public function insertUserBuy($data = array())
	{
		// echo 12;exit;
		$this->db2 = $this->load->database('default', TRUE);

        $this->db2->insert('userbuy', $data);

		return $this->db2->insert_id();
	}
	//更新的圈子数据记录
	public function upLoadQZ($data = array())
	{
		$this->db2 = $this->load->database('default', TRUE);
		$query = $this->db2->query("UPDATE userbuy set maxprice = '{$data['maxprice']}', minprice = '{$data['minprice']}', oldmoney='{$data['oldmoney']}' , `order`='{$data['order']}', `success`='{$data['success']}', avgflowe ='{$data['avgflowe']}' ,content = '{$data['content']}' where id =  {$data['id']}" );
		// $bool=$this->db2->delete('userbuy',array('id'=>$id));  
        if ($query) {  
            return "影响行数".$this->db2->affected_rows();  
        } 
        // $this->db2->insert('cms_participle_2017', $data);
		// return $data;
	}
	//财视订单更新表
   public function getOrderC($where)
	{
		$this->db2 = $this->load->database('caishik', TRUE);
		$query = $this->db2->query("SELECT OrderID,UserID FROM tbPassportOrder WHERE Status=1 AND OrderID > {$where}  GROUP BY UserID ORDER BY OrderID DESC ;" );			  
		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();

		$query->free_result();

		return array('list' => $data);
	}
	//购买的产品
	public function getOrder($where){
		//财视后台
		$this->db2 = $this->load->database('caishik', TRUE);
		//所有充值记录总和
		$query = $this->db2->query("SELECT CASE tb3.ProductTypeID WHEN 1 	THEN '理财师红包' WHEN 2 	THEN '理财师直播室' WHEN 3 	THEN '理财师操盘计划' WHEN 4 	THEN '财视学院课程' WHEN 5 	THEN '财视礼物' WHEN 6 	THEN '视频直播' WHEN 7 	THEN '动态' WHEN 8 	THEN '财视插件' WHEN 8 	THEN '收益' ELSE '其它' END as 'order' FROM tbPassportOrder  tb1,tbPassportOrderItem tb2, tbShopProduct tb3 where tb1.OrderID=tb2.OrderID and tb2.ProductID=tb3.ProductID AND tb1.UserID = {$where} GROUP BY tb3.ProductTypeID;");
		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();
		$query->free_result();
		// var_dump($data);exit;
		return $data;
	}
	//财视动态
	public function getDT($where){
		//财视后台
		$this->db2 = $this->load->database('cs', TRUE);
		//所有充值记录总和
		$query = $this->db2->query("SELECT DISTINCT Content FROM fpDynamic WHERE UserID = {$where} ");
		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();
		$query->free_result();
		return $data;
	}
	public function checkC($where){
		$this->db2 = $this->load->database('default', TRUE);
		$query = $this->db2->query("SELECT id FROM userbuy WHERE userid = {$where} and type = 1 ;" );
		// $bool=$this->db2->delete('userbuy',array('id'=>$id));  
       $data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();
		$query->free_result();

		return $data;
	}
	//更新的财视数据记录
	public function upLoadCS($data = array())
	{
		$this->db2 = $this->load->database('default', TRUE);
		$query = $this->db2->query("UPDATE userbuy set maxprice = '{$data['maxprice']}', minprice = '{$data['minprice']}', oldmoney='{$data['oldmoney']}' , `order`='{$data['order']}', `success`='{$data['success']}', avgflowe ='{$data['avgflowe']}' ,content = '{$data['content']}'where id =  {$data['id']}" );
		// $bool=$this->db2->delete('userbuy',array('id'=>$id));  
        if ($query) {  
            return "影响行数".$this->db2->affected_rows();  
        } 
        // $this->db2->insert('cms_participle_2017', $data);
		// return $data;dbtracker
	}
		//购买价格区间  最大跟最小
	public function getInterval($where){
		$this->db2 = $this->load->database('caishik', TRUE);
		$query = $this->db2->query("SELECT format(max(Money)/100,2) as Max,format(min(Money)/100,2) as Min FROM tbPassportOrder WHERE Money > 0 and UserID = {$where}");
		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();
		$query->free_result();

		return $data[0];
	}
	//支付成功率
	public function getSuccess($where){
		$this->db2 = $this->load->database('caishik', TRUE);
		$query = $this->db2->query("SELECT format(t1.succTotal/t2.Total,2) as success FROM (SELECT count(1) as succTotal FROM tbPassportPayRecord where FLag=1 AND UserID = {$where} )as t1, (SELECT count(1) as Total FROM tbPassportPayRecord WHERE UserID = {$where}) as t2;");
		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();
		$query->free_result();

		return $data[0]['success'];
	}
	//埋点股票剥离 获取用户列表
	public function getStockUserList($RecordId){
		$this->db2 = $this->load->database('dbtracker', TRUE);
		$query = $this->db2->query("SELECT UserId,RecordId FROM FailUrl where RecordId > {$RecordId} ORDER BY RecordId DESC  ;");
		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();
		$query->free_result();

		return $data;
	}
	//获取埋点统计系统的用户退出连接
	public function getUserStock($where){
		$this->db2 = $this->load->database('dbtracker', TRUE);
		$query = $this->db2->query("SELECT OutUrl FROM FailUrl where  UserId = {$where};");
		$data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();
		$query->free_result();
		return $data;
	}
	//检测是否存在此用户
	public function checkStock($where){
		$this->db2 = $this->load->database('default', TRUE);
		$query = $this->db2->query("SELECT id FROM userbuy WHERE userid = {$where} ;" );
       $data  = ($query->num_rows() > 0) ? 
						$query->result_array() : array();
		$query->free_result();

		return $data;
	}
	//更新股票获取数据记录
	public function upLoad($data = array())
	{
		$this->db2 = $this->load->database('default', TRUE);
		$query = $this->db2->query("UPDATE userbuy set stock = '{$data['stock']}'where userid =  {$data['userid']}" );
		// $bool=$this->db2->delete('userbuy',array('id'=>$id));  
        if ($query) {  
            return "影响行数".$this->db2->affected_rows();  
        } 
	}

}

/* End of file Passport_manage_mdl.php */
/* Location: ./application/controllers/Passport_manage_mdl.php */