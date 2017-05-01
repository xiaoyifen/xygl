<?php
/**
 * @author 覃小珍
 * 查询校友信息，根据专业和姓名进行查询
 */
 
 namespace app\index\controller;
 use app\common\controller\Home;
 
 class Stu EXTENDS Home{
 	 function __construct(){
 		parent::__construct();
 	}
 	/*
 	 * 渲染个人中心界面
 	 */
 	public function selfCenter(){
   	return $this->fetch('selfCenter');
  }
 /*
  * 渲染查找校友界面
  */
  	  public function findAlumni(){
  	  	//查找全部校友
  	  	$stu =model('stu');
  	  	$list=$stu->paginate(50);
  	  	$this->assign('list',$list);
  	 	return $this->fetch('findAlumni');
  	 }
 	/*
 	 * 查找校友的方法，首先获取用户选择的查找校友的方式，是通过专业还是通过同学来查询
 	 */
 	public function findStu(){
 		//$where=[];
 		$stu =model('stu'); //实例化Stu模型
 		//获取查找方式，1代表按专业查询，2代表按同学查询,获取入学年份和关键字
 		$findParam=$this->GET;
 		$findStyle=$findParam['findstyle'];
 		//将获取的入学年份转换为时间戳
// 		$start_year=time($findParam['enrollmentdate']);
// 		$enrollmentdate=['like',"%{$findParam['enrollmentdate']}%"];
 		$keyWord=$findParam['keyWord'];
 		//选择按专业查询
 		if($findStyle==1){
 			$findParam['major']=['like',"%{$keyWord}%"];
 			$list=$stu->where(['major'=>$findParam['major']])->whereTime('enrollmentdate','=',$findParam['enrollmentdate'])->select();//分页	
 		}else{
 			//选择按同学查询
 			$findParam['username']=['like',"%{$keyWord}%"];
 			$list=$stu->where(['username'=>$findParam['username']])->whereTime('enrollmentdate','=',$findParam['enrollmentdate'])->select();//分页	
 		}
// 		echo $list;
// 		exit;
 		$this->assign('list',$list);
 		return $this->fetch('alumniDetail');
 	}
 /**
 * 个人中心
 * 1.已收留言
 * 2.个人信息：修改个人信息；修改密码
 * 3.活动：已参加活动；已发起活动
 */
 	/*
 	 * 渲染修改个人信息界面
 	 */
 	 public function changeSelf($userid){
 	 	//获取参数
 	 	$userid=$this->GET;
	 	// var_dump($userid);
	 	// exit;
 	 	//查询账户的个人信息
 	 	$stu=model('stu');
 	 	$list=$stu->where('userid',$userid['userid'])->find();
 	 	$this->assign('list',$list);
 		return $this->fetch('changeSelf');
 	 }
 	/*
 	 * 修改个人信息的方法
 	 */
 	 public function modifyInfo(){
 	 	//获取参数
 	 	$param=$this->GET;
// 	 	var_dump($param);
// 	 	exit;
 	 	$stu=model('stu');
 	 	$param['birthday']=strtotime($param['birthday']);
 	 	$result=$stu->where('userid',$param['userid'])->update($param);
 	 	if($result !=0){
 	 		$this->success('信息修改成功');
 	 	}else{
 	 		$this->error('信息修改操作失败'); 
 	 	}
 	 }
 	 /*
 	 * 渲染修改密码页面
 	 */
 	public function changePwd(){
  	 	return $this->fetch('changePwd');
  	 }
 	 /*
 	 * 修改密码
 	 */
 	 public function updatePwd(){
 	 	//获取参数
 	 	$stu=model('stu');
 	 		$data=$this->GET;
// 	 		var_dump($data);
// 	 		exit;
 	 		$data['studentid']=2013190412;
 	 		$user=$stu->where('studentid',$data['studentid'])->find();
 	 		if(!empty($user)){
 	 			if(!empty($data['oldpwd']) && !empty($data['newpwd']) && !empty($data['confirmpwd'])){
 	 				if($data['oldpwd'] == $user['password']){
 	 					if($data['newpwd']==$data['confirmpwd']){
 	 						$result=$stu->where('studentid',$data['studentid'])->update(['password'=>$data['newpwd']]);
			 	 			if($result){
			 	 				$this->success('密码修改成功');
			 	 			}else{
			 	 				$this->error('密码修改失败');
			 	 			}
 	 					}else{
 	 						$this->error('新密码与确认密码不一致，请重新输入');
 	 					}
 	 				}else{
 	 					$this->error('登录密码错误，请重新输入');
 	 				}
 	 		}else{
 	 			$this->error('输入框输入不能为空，请按要求输入');
 	 	 	}
 	 	}else{
 	 		$this->error('该用户不存在，请注册');
 	 	}
 	 }
 }
?>
