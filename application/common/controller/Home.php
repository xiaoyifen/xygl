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
//		$this->check_login();
	}

	/** 
	* 检测是否登陆 没有登录，跳转回登录错误页面
	* @access protected 
	* @param
	* @return  
	*/
	protected function check_login_user(){
		$_auth = session('member_auth');
		if (!$_auth) {
			$_auth = cookie('member_info');
		}
		if(!$_auth){
			$this->redirect('index/errorLogin');
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
				$user = model('stu')->where(['status'=>1,'userid'=>$userid])->find();
				$userid = $user['userid'];
				$username = $user['username'];
			}
			if($user['password'] != $passwd){
				$this->redirect('index/errorLogin');
			}
			$this->view->userid = $userid;
			$this->view->username = $username;
			$this->view->role = $role;
		}else{
			$this->redirect('index/errorLogin');
		}
	}

	/** 
	* 检测是否登陆 没有登录，也不跳转
	* @access protected 
	* @param
	* @return  
	*/
	protected function check_login(){
		$_auth = session('member_auth');
		if (!$_auth) {
			$_auth = cookie('member_info');
		}
		if($_auth){
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
					$user = model('stu')->where(['status'=>1,'userid'=>$userid])->find();
					$userid = $user['userid'];
					$username = $user['username'];
				}				
				if($user['password'] != $passwd){
					$this->redirect('index/errorLogin');
				}				
				$this->view->userid = $userid;
				$this->view->username = $username;
				$this->view->role = $role;
			}else{
				$this->redirect('index/errorLogin');
			}
		}		
	}
	
	/** 
	* 检测身份
	* @access protected 
	* @param
	* @return  
	*/
	protected function check_auth(){
		$_auth = session('member_auth');
		if (!$_auth) {
			$_auth = cookie('member_info');
		}
		if($_auth){
			$auth = decrypt($_auth,'USER'.$_SERVER['HTTP_USER_AGENT']);
			if($auth){
				$auth = explode('|', $auth);
				$role = $auth[0];
				$userid = $auth[1];
				$passwd = $auth[2];
				if ($role == 2) { //管理员
					$this->redirect('index/errorLogin');
				}else{
					$user = model('stu')->where(['status'=>1,'userid'=>$userid])->find();
					$userid = $user['userid'];
					$username = $user['username'];
				}				
				if($user['password'] != $passwd){
					$this->redirect('index/errorLogin');
				}								
				$this->view->userid = $userid;
				$this->view->username = $username;
				$this->view->role = $role;
			}else{
				$this->redirect('index/errorLogin');
			}
		}		
	}
}
?>