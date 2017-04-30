<?php
/**
*基类、父类
*
*所有控制器的父类
* @author      lfn
*/
namespace app\common\controller;
use think\Controller;
use think\Db;

class Base EXTENDS Controller{
	function __construct(){
		parent::__construct();
		$this->_init_var();
		$this->_init_constant();
		$this->check_login();
	}

	/** 
	* 初始化变量
	* @access private 
	* @param
	* @return  
	*/
	private function _init_var(){
		$this->view->_m = $this->request->module();
		$this->view->_c = $this->request->controller();
		$this->view->_a = $this->request->action();
		$this->GET = input('param.');
		if(isset($this->GET['kw'])){
			$this->kw = addslashes($this->GET['kw']);
		}//关键字
		if(isset($this->GET['search'])){
			$this->search = addslashes($this->GET['search']);
		}//选择按？查询
	}

	/** 
	* 初始化常量
	* @access private 
	* @param
	* @return  
	*/
	private function _init_constant(){
		define('TIMES',$_SERVER['REQUEST_TIME']);

	}

	/** 
	* 检测是否登陆 没有登录，也不跳转
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

}
?>
