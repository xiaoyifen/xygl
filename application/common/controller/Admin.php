<?php
/**
*后台基类
*
*所有后台控制器的父类
* @author      lfn
*/
namespace app\common\controller;
use app\common\controller\Base;
class Admin EXTENDS Base{

	function __construct(){
		parent::__construct();
		$this->check_login();
	}

	/** 
	* 检测是否登陆
	* @access private 
	* @param
	* @return  
	*/
	private function check_login(){
		$_auth = session('member_auth');
		if (!$_auth) {
			$_auth = cookie('member_info');
		}
		if(!$_auth){
			$this->redirect('index/index/index');
		}
		if($_auth){
			$auth = decrypt($_auth,'USER'.$_SERVER['HTTP_USER_AGENT']);
			if($auth){
				$auth = explode('|', $auth);
				$role = $auth[0];
				$userid = $auth[1];
				$passwd = $auth[2];
				if ($role == 1) {
					$this->redirect('index/index/index');
				}
				$user = model('admin')->where('adminid',$userid)->find();
				if($user['password'] != $passwd){
					$this->redirect('index/index/index');
				}
				$this->view->username = $user['adminname'];
				$this->view->userid = $user['adminid'];
			}else{
				$this->redirect('index/index/index');
			}
		}	
	}
	
}
?>