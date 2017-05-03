<?php
/**
 * @author 覃小珍
 * 1.管理员审核活动，管理员审核用户发起的活动，审核通过则改变activity表中的status字段的值为1
 * 2.管理员修改活动
 * 3.管理员删除单个活动
 */
  namespace app\admin\controller;
  use app\common\controller\Admin;
  
  class Activity EXTENDS Admin{
  	function __construct(){
 		parent::__construct();
    $this->check_login();
    $this->view->location = '活动管理';
    $this->view->title = '活动管理';
 		//$activity=new Activity();
 	  }
 	/*
 	 * 直接运行后台主界面
 	 */
 	 public function admin(){
  	 	return $this->fetch('activitycheck');
  	 }
 	/*
 	 * 管理员点击活动审核时，只显示出待审核的活动列表,即status为0
 	 */
 	 public function showAct(){
 	 	$param=$this->GET;
 	 	$activity=model('activity');
 	 	if(!empty($param)){
 	 		$list=$activity->with('stus')->where(['status'=>0,'activityid'=>$param['activityid']])->find();
// 	 		var_dump($param);
// 	 		var_dump($list);
// 	 		exit;
			if($list){
				$this->assign('list',$list);
 	 			return $this->fetch('activitydetail');
			}
 	 		
 	 	}else{
 	 		$list=$activity->with('stus')->where('status',0)->paginate(5);
 	 		if($list){
 	 			$this->assign('list',$list);
 	 			return $this->fetch('activitycheck');
 	 		}
 	 		
 	 	}
 	 }
  	/*
  	 * 通过审核的方法
  	 * 更新activity表中的status字段的值为1
  	 */
  	public function pass(){
  		$activity=model('activity');
  		//接收点击’通过审核‘时传递的活动ID
  		$activityid=$this->GET;
//  		var_dump($activityid);
//  		exit;
  		//$activityId=16;
  		//更新status字段
  		$result=$activity->where(['activityid'=>$activityid['activityid']])->update(['status'=>1]);
  		if($result !=0){
  			$this->success('该活动已通过审核！','admin');
  		}else{
  			$this->error('审核失败！',$activity->getError());
  		}
  	}
  	/*
  	 * 未通过审核的活动
  	 */
  	 public function passnot(){
  	 	//跳出未通过审核的原因
  	 	return $this->fetch('passnot');
  	 }
  	/*
  	 * 管理员删除单个活动
  	 */
  	 public function del(){
  	 	$activity=model('activity');
  	 	//获取要删除活动的活动id
  	 	$activityId=$this->Get;
  		$activityId=15;
  	 	//删除动作
  	 	$result=$activity->where('activityid',$activityId)->delete();
  	 	if($result !=0){
  			$this->success('删除活动成功','admin');
  		}else{
  			$this->error('删除活动失败！',$activity->getError());
  		}
  	 }
  	 /*
  	  * 管理员
  	  */
  	 
  }
?>
