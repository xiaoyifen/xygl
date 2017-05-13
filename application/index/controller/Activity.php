<?php
/**
 * @author 覃小珍
 * 1.根据活动类别查找活动
 * 2.快速查找活动
 */
 
  namespace app\index\controller;
  use app\common\controller\Home;
  use think\Request;
  class Activity EXTENDS Home{
  	//构造方法
  	function __construct(){
  		parent::__construct();
  		$this->view->title = '校友活动';
//  		$activity=new Activity;//实例化Activity模型
  		//$activity=model('activity');
  	}
  	
  	/*
  	 * 渲染活动主页面
  	 */
  	 public function event(){
  	 	$this->check_login();
  	 	return $this->fetch();
  	 }
  	 /*
  	  * 活动报名页面
  	  */
  	 public function setup(){
  	 	$this->check_login();
  	 	return $this->fetch('setupAct');
  	 }
  	 /*
  	  * 活动详情页面
  	  */
  	  public function actDetail(){
  	  	$this->check_login();
  	 	return $this->fetch('event1');
  	 }
  	 /*
  	  * 更新数据表activity的字段remainday
  	  */
  	  public function updateAct($starttime,$endtime){
//  	  	$starttime=1461945600;
//  	  	$endtime=1492444800;
  	  	$sysTime=time();
//  	  	echo $sysTime;
  	  	$remaintime=$starttime-$sysTime;
  	  	if($remaintime>0){
	  			$d=floor($remaintime/3600/24);
	  			$h = floor(($remaintime%(3600*24))/3600);
	  			$m = floor(($remaintime%(3600*24))%3600/60);
	  			if($d>0){
	  				if($d>=7){
	  					$w=round($d/7);
	  					return $actParam['remainday']=$w."周后";
	  				}else{
	  					return $actParam['remainday']=$d."天后";
	  				}
	  			}else{
	  				if($h>0){
	  					return $actParam['remainday']=$h."小时后";
	  				}else{
	  					return $actParam['remainday']=$m."分钟后";
	  				}
	  			}
	  		}else{
	  			if($sysTime<=$endtime){
	  				return $actParam['remainday']='进行中';
	  			}else{
	  				return $actParam['remainday']='结束';
	  			}
	  		}
  	  }
  	 /*
  	  * 渲染我要发起活动的方法
  	  */
  	  public function promotionAct(){
  	  	$this->check_login();
  	  	//判断用户是否登录
  	  	$this->check_login_user();
  	 	return $this->fetch('setupAct');
  	 }
  	 /*
  	 * 发起活动的方法
  	 * 1.获取用户提交的表单参数
  	 * 2.将参数保存到数据库对应的活动表中，此时的审核状态为0表示未审核
  	 */
  	public function setupact(){
  		$this->check_login();
  		if(IS_POST){
  			$actParam=$this->GET;
  			//$actParam=[];
  			//首先要判断用户是否已经登录
	  		//获取发起人ID，用户的登陆信息放在session中，所以通过session获取发起人id
	  		//$actParam['plannerid']=$_SESSION['studentid'];
	  		//$actParam['plannerid']=Session::get('studentid');
	  		//获取表单参数
	  		//测试，plannerid直接给值
//	  		$actParam['userid']=1;
//	  		$actParam['type']=$request->post('type');
//	  		$actParam['title']=$request->post('title');
//	  		$actParam['activitytime']=$request->post('activitytime');
//	  		$actParam['address']=$request->post('address');
//	  		$actParam['content']=$request->post('content');
//	  		$actParam['contact']=$request->post('contact');
	  		/*
	  		$actParam['type']=$_post['type'];
	  		$actParam['title']=$_post['title'];
	  		$actParam['activitytime']=$_post['activitytime'];
	  		$actParam['address']=$_post['address'];
	  		$actParam['content']=$_post['content'];
	  		$actParam['contact']=$_post['contact'];
	  		*/
	  		//将用户输入的字符串类型的时间转换为时间戳
	  		$actParam['activitytime']=strtotime($actParam['activitytime']);
	  	    $actParam['activityendtime']=strtotime($actParam['activityendtime']);//活动结束时间
	  	    $actParam['deadline']=strtotime($actParam['deadline']);//活动结束时间
//	  		echo $actParam['activitytime'];
//	  		echo echo $actParam['activitytime'];
	  		//活动日期为星期几
	  		$actParam['weekday']=$this->turn($actParam['activitytime']);
	  		//距离活动还剩多少天即活动状态
//	  		$sysTime=time();
//	  		$remaintime=$actParam['activitytime']-$sysTime;
//	  		//echo $remaintime.'<br>';
//	  		if($remaintime>0){
//	  			$d=floor($remaintime/3600/24);
//	  			$h = floor(($remaintime%(3600*24))/3600);
//	  			$m = floor(($cle%(3600*24))%3600/60);
//	  			if($d>0){
//	  				if($d>=7){
//	  					$w=round($d/7);
//	  					$actParam['remainday']=$w."周后";
//	  				}else{
//	  					$actParam['remainday']=$d."天后";
//	  				}
//	  			}else{
//	  				if($h>0){
//	  					$actParam['remainday']=$h."小时后";
//	  				}else{
//	  					$actParam['remainday']=$m."分钟后";
//	  				}
//	  			}
//	  		}else{
//	  			if($sysTime<=$actParam['activityendtime']){
//	  				$actParam['remainday']='进行中';
//	  			}else{
//	  				$actParam['remainday']='结束';
//	  			}
//	  		}
//	  		echo $actParam['remainday'];
	  		
	  		$actParam['createtime']=time();
	  		$actParam['updatetime']=time();
	  		$actParam['status']=0;
//	  		var_dump($actParam['type']);
	  		//将获取到的参数保存到数据表activity中
	  		//$activity=new Activity();//实例化Activity模型
	  		$activity=model('activity');
	  		$result=$activity->save($actParam);
	  		if($result){
	  			$this->success('提交成功，正在等待管理员审核');
	  		}else{
	  			$this->error('提交失败');
	  		}
  		}
  		
  	}
	/*
  	  * 将时间转化为星期几的函数
  	  */
  	 public function turn($time){
  	 	if (is_numeric($time)) {
 			$weekday=array('sunday','Monday','tuesday','wednesday','thursday','friday','saturday');
 			return $weekday[date('w',$time)];
 	}
 		return false;
  	 }
  	 /*
  	  * 点开导航栏上的校友活动执行的方法
  	  */
  	  public function allAct(){
  	  	$this->check_login();
  	  	$activity=model('activity');
  	  	$list=$activity->where('status',1)->order('activitytime','desc')->paginate(10);
  	 	if($list){
  		  	foreach($list as $k=>$v){
			//调用updateAct方法计算当下活动的剩余时间
  			$list[$k]['remainday']=$this->updateAct($v->activitytime,$v->activityendtime); 				
  		}
  		$this->assign('list',$list);
  		return $this->fetch('event');
  	  }
  	  }
  	/*
  	 * 根据活动类别查询获得的方法，默认是全部活动
  	 */
  	public function findActByType(){
  		$this->check_login();
  		//获取用户选择的活动类别
  		$type=$this->GET;
//  		var_dump($type);
//  		exit;
  		//数据库中通过审核的活动，即status为1
  		$type['status']=1;
  		//活动时间不超过系统当前时间 
  		$sysTime=time();
  		$activity=model('activity');
  		$query['type'] = $type['type'];
  		//$activity=new Activity();
  		if($type['type'] == '全部活动'){
  			
  			$list=$activity->where('status',$type['status'])->order('activitytime',' desc')->paginate(10,false,['query'=>$query]);
  			if($list){
  		  		foreach($list as $k=>$v){
				//调用updateAct方法计算当下活动的剩余时间
  				$list[$k]['remainday']=$this->updateAct($v->activitytime,$v->activityendtime); 				
  			}
  			$this->assign('list',$list);
  		    return $this->fetch('event');
  		}else{
  			return $this->fetch('event');
  		}
  		}else{
  			$list=$activity->where(['type'=>$type['type'],'status'=>$type['status']])->order('activitytime',' desc')->paginate(10,false,['query'=>$query]);
  			if($list){
  		  		foreach($list as $k=>$v){
				//调用updateAct方法计算当下活动的剩余时间
  				$list[$k]['remainday']=$this->updateAct($v->activitytime,$v->activityendtime);  				
  			}
  			$this->assign('list',$list);
  		    return $this->fetch('event');
  		}else{
  			return $this->fetch('event');
  		}
  		}
  	}
  	/*
  	 * 快速查询
  	 */
  	 //7天后
  	 public function findActBySeven(){
  	 	$this->check_login();
  	 	$activity=model('activity');
  	 	//获取当前系统日期对应的7天后的时间
  	 	$time=strtotime('7 days');
  	 	$list=$activity->whereTime('activitytime','>',$time)->where('status',1)->paginate(10);
  	 	if($list){
  	 		foreach($list as $k=>$v){
  				$list[$k]['remainday']=$this->updateAct($v->activitytime,$v->activityendtime);
  			}
  	 		$this->assign('list',$list);
  		    return $this->fetch('event');	
  	 	}else{
  	 		return $this->fetch('event');
  	 	}
  	 } 
  	 //今天
  	 public function findActBytoday(){
  	 	$this->check_login();
  	 	$activity=model('activity');
  	 	$list=$activity->whereTime('activitytime','d')->where('status',1)->paginate(10);
//  	 	var_dump($list);
//  	 	exit;
  	 	if($list){
  	 		foreach($list as $k=>$v){
  				$list[$k]['remainday']=$this->updateAct($v->activitytime,$v->activityendtime);
  			}
			$this->assign('list',$list);
  		    return $this->fetch('event');	
  	 	}else{
  	 		$list[$k]['remainday']=null;
  	 		return $this->fetch('event');
  	 	}
  	 }
  	 
  	 /*
  	  * 查询出在周末举行的活动
  	  */
  	 public function findActByWeeken(){
  	 	$this->check_login();
  	 	$activity=model('activity');
  	 	//首先查询出所有活动的的时间
  	 	$list=$activity->where('weekday','in',['saturday','sunday'])->paginate(10);
  	 	if($list){
  	 		foreach($list as $k=>$v){
  				$list[$k]['remainday']=$this->updateAct($v->activitytime,$v->activityendtime);
  			}
  	 		$this->assign('list',$list);
  		    return $this->fetch('event');	
  	 	}else{
  	 		$list[$k]['remainday']=null;
  	 		return $this->fetch('event');
  	 	}
  	 }
  	 
  	 /*
  	  * 点击活动标题的链接时根据传递过来的活动ID查询获得详情
  	  */
  	  public function findActDetail($activityid){
  	  		$this->check_login();
  	  		$param=$this->GET;
  	  		//获取活动详细信息
  	  		$activity=model('activity');
  	  		$list=$activity->with('stus')->where(['activityid'=>$param['activityid']])->find();
//			var_dump($list->stus->userid);
//			exit;
			//获取该活动已报名的人员名单
			$registration=model('registration');
			$registerlist=$registration->with('registeToStu')->where(['activityid'=>$param['activityid']])->select();
//			var_dump(registe_to_stu['username']);
//			exit;
//			var_dump($registerlist);
//			exit;
//  	  		if(empty($registerlist)){
//  	  			return "false";
//  	  		}else{
//  	  			return "true";
//  	  		}
  	  		//echo $registerlist['username'].'<br>';
//  	  		echo $list;
//				foreach($registerlist as $k){
//					echo $k->registe_to_stu->username;
//					echo '<br>'.$k->registe_to_stu->userid;
//				}
//  	  		exit;
  				$list['remainday']=$this->updateAct($list['activitytime'],$list['activityendtime']);
  	  			$this->assign('list',$list);
  	  			$this->assign('registerlist',$registerlist);
  		    	return $this->fetch('event1');
  	  }
  	  /*
 	 * 活动报名的方法
 	 */
 	 public function joinActivity(){
 	 	$this->check_login();
 	 	$this->check_login_user();
 	 	//判断用户是否登录
 	 		//获取参数
 	 		$joinParam=$this->GET;
// 	 		$joinParam['activityid']=20;
// 	 		$joinParam['applicantid']=1;
 	 		//实例化模型Registration
 	 		$registration=model('registration');
 	 		$activity=model('activity');
 	 		//报名时间
 	 		$registration->registrationtime=time();
 	 		//报名者ID
 	 		$registration->applicantid=$joinParam['userid'];
 	 		//报名的活动id
 	 		$registration->activityid=$joinParam['activityid'];
 	 		$result=$registration->save();
 	 		if($result !=0){
 	 			//查询出现已报名的人数
 	 			$list=$activity->where(['activityid'=>$joinParam['activityid']])->find();
 	 			$number=$list['registerNumber']+1;
 	 			$updateResult1=$registration->save();
 	 			//更新活动表activity,报名人数registerNumber字段加1
 	 			$updateResult=$activity->where(['activityid'=>$joinParam['activityid']])->update(['registerNumber'=>$number]);
 	 			//$updateResult=$registration->with('registeToAct')->save(['registerNumber'=>registerNumber+1]);
 	 			if($updateResult !=0){
 	 				$this->success('恭喜您成功报名该活动!'); 	
 	 			}else{
 	 				$this->error('很抱歉报名失败!');
 	 			}				
 	 		}else{
 	 			$this->error('很抱歉报名失败!');
 	 		}
 	 		
 	 	
 	 }
 	 
  	 /*
 	 * 个人中心：查找本账户已报名的活动
 	 */
 	 public function hasJoinAct($applicantid){
 	 	$this->check_login();
 	 	//首先获取本账户的账号
 	 	$applicantid=$this->GET;
 	 	//var_dump($applicantid);
// 	 	exit;
 	 	//这里会运用到关联模型
 	 	$activity=model('activity');
 	 	$registration=model('registration');
 	    //查找活动信息
  	  	$activities=$registration->with(['registeToAct.stus'])->where(['applicantid'=>$applicantid['applicantid']])->select();
		//var_dump($activities);
// 	 	exit;
 	 	
 		$this->assign('activities',$activities);
 		return $this->fetch('signupAct');
 	 	
 	 }
 	 /*
 	  * 个人中心：查找已发起的活动
 	  */
 	  public function hasSetupAct(){
 	  	$this->check_login();
 	  	//首先获取本账户的账号
 	 	$param=$this->GET;
 	 	$activity=model('activity');
 	 	$list=$activity->where('userid',$param['userid'])->paginate(10);
// 	 	var_dump($list);
// 	 	exit;
 	 	if($list){
 	 		$this->assign('list',$list);
 	 		return $this->fetch('launchedAct');
 	 	}else{
 	 		$this->error('很抱歉，您还没有发起活动');
 	 	}
 	  }

    // 审核未通过原因
  public function reason($id){
      $map['articleid'] = $id;
      $this->model = model('log');
      $items = $this->model->where($map)->order('time desc')->paginate(10);
      $this->view->location = '活动信息';
      $this->view->locationNext = '审核未通过原因';
      $this->view->items = $items;
      return $this->fetch();
  }


}

  
?>
