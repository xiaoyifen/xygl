<?php
/**
*前台基类
*
*所有前台控制器的父类
* @author      lfn
*/
namespace app\common\controller;
use app\common\controller\Base;
class Home EXTENDS Base{
	function __construct(){
		parent::__construct();
		$this->check_login();
	}

	/** 
	* 检测是否登陆 没有登录，跳转回首页
	* @access private 
	* @param
	* @return  
	*/
	private function check_login(){
		// $_auth = cookie('member_auth');
		$_auth = session('member_auth');
		if (!$_auth) {
			$_auth = cookie('member_info');
		}
		if(!$_auth){
			$this->redirect('index/index');
		}
		$auth = decrypt($_auth,'USER'.$_SERVER['HTTP_USER_AGENT']);
		if($auth){
			$auth = explode('|', $auth);
			$role = $auth[0];
			$userid = $auth[1];
			$passwd = $auth[2];
			if ($role == 2) { //管理员
				$user = model('admin')->where('adminid',$userid)->find();
				$userid = $user['adminid'];
				$username = $user['adminname'];
			}else{
				$user = model('stu')->where('userid',$userid)->find();
				$userid = $user['userid'];
				$username = $user['username'];
			}
			if($user['password'] != $passwd){
				$this->redirect('index/index');
			}
			$this->view->userid = $userid;
			$this->view->username = $username;
			$this->view->role = $role;
		}else{
			$this->redirect('index/index');
		}
	}
	
}
?>