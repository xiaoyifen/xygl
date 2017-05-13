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
 		$this->view->title = '校友查询';
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
  	  	$this->check_login();
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
 		$this->check_login_user();
 		//$where=[];
 		$stu =model('stu'); //实例化Stu模型
 		//获取查找方式，1代表按专业查询，2代表按同学查询,获取入学年份和关键字
 		$findParam=$this->GET;
 		$searchtype=$findParam['searchtype'];
 		//选择按专业查询
 		if($searchtype==1){
 			$major=['like',"%{$findParam['keyword']}%"];
 			if($findParam['enrollmentdate']==0){
 				$list=$stu->where(['major'=>$major])->paginate(20);//分页	
 			}else{
 				$list=$stu->where(['major'=>$major,'enrollmentdate'=>$findParam['enrollmentdate']])->paginate(50);//分页	
 			}
 		}else{
 			//选择按同学查询
 			$username=['like',"%{$findParam['keyword']}%"];
 			if($findParam['enrollmentdate']==0){
 				$list=$stu->where(['username'=>$username])->paginate(20);//分页	
 			}else{
 				$list=$stu->where(['username'=>$username,'enrollmentdate'=>$findParam['enrollmentdate']])->paginate(50);//分页	
 			}
 		}
// 		echo $list;
// 		exit;
 		$this->assign('list',$list);
 		return $this->fetch('alumniDetail');
 	}
 	/*
  	   * 查找活动发起人信息
  	   */
  	   public function findActLeader($userid){
        $this->check_login_user();
  	   	//获取URL传递的参数
  	   	$leaderParam=$this->GET;
//  	   	echo $leaderParam['userid'].'<br>';
  	   	//实例化Stu模型
  	   	$stu=model('stu');
  	   	//查找
  	   	$list=$stu->where(['userid'=>$leaderParam['userid']])->find();
//  	   	echo $list;
//  	   	exit;
        $message = model('message');
        $items = $message->with('sender')->where(['ownerid'=>$leaderParam['userid'],'replyid'=>0])->order('time DESC')->paginate(50);
        // var_dump($this->model->getLastSql());
        // var_dump($items);
        foreach ($items as $k => $v) {
            $item[$k]['message'] = $v;
            $item[$k]['reply'] = $message->with(['sender','receiver'])->where(['ownerid'=>$leaderParam['userid'],'replyid'=>$v['messageid']])->order('time')->select();
            // var_dump($this->model->getLastSql());   
        }
        // var_dump($item);
        // exit;
        $this->assign('item',$item);
        $this->assign('items',$items);
  	   	if($list){
  	   		$this->assign('list',$list);
  	   		return $this->fetch('alumniInfo');
  	   	}else{
  	   		$this->error('错误');
  	   	}
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
 	 	$this->check_login();
    $this->check_auth();
 	 	//获取参数
 	 	$userid=$this->GET;
	 	// var_dump($userid);
	 	// exit;
 	 	//查询账户的个人信息
 	 	$stu=model('stu');
 	 	$list=$stu->where('userid',$userid['userid'])->find();
    	$list=$list->getData();
 	 	$this->assign('list',$list);
 	 	$this->view->title = '个人中心';
 		return $this->fetch('changeSelf');
 	 }
 	/*
 	 * 修改个人信息的方法
 	 */
 	 public function modifyInfo(){
 	 	$this->view->title = '个人中心';
 	 	$this->check_login();
//    $this->check_auth();
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
 		$this->check_login();
    	$this->check_auth();
 		$this->view->title = '个人中心';
 		$stu=model('stu');
 		$data=$this->GET;
 		//查找登陆者的账号
 		$list=$stu->where('userid',$data['userid'])->find();
 		$this->assign('list',$list);
  	 	return $this->fetch('changePwd');
  	 }
 	 /*
 	 * 修改密码
 	 */
 	 public function updatePwd(){
 	 	$this->check_login();
//    	$this->check_auth();
 	 	//获取参数
 	 	$stu=model('stu');
 	 	$data=$this->GET;
// 	 		var_dump($data);
// 	 		exit;
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
