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
		
	}
	
}
?>