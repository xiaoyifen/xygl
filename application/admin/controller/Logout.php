<?php
/**
 * 登出后台控制器
 * @author lfn
 */
namespace app\admin\controller;
use app\common\controller\Admin;
use think\File;

class Logout extends Admin
{
    function __construct(){
		parent::__construct();  
		$this->check_login();
	}

	public function logout(){
        session('member_auth', null);
        cookie('member_info',null);
        $this->redirect('index/index/index');
    }
}