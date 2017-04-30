<?php
/**
 * 前台登录控制器
 * @author lfn
 */
namespace app\index\controller;
use app\common\controller\Base;

class Login extends Base
{
    function __construct(){
		parent::__construct();
	}

    // 登录
    public function index(){
        if(request()->isPost()){
            $post = $this->GET;
            $post['studentid'] or $this->error('请填写学号');
            $post['password'] or $this->error('请填写密码');
            $user = model('stu')->where('studentid',$post['studentid'])->find();
            if(!$user || $user['password'] != $post['password']){
                $this->error('帐号或密码不正确');
            }
            $auth = encrypt($user['userid'].'|'.$user['password'], 'USER'.$_SERVER['HTTP_USER_AGENT']);
            // var_dump($user['id'].'|'.$user['password'], 'USER'.$_SERVER['HTTP_USER_AGENT']);
            // echo decrypt($auth,'USER'.$_SERVER['HTTP_USER_AGENT']);
            // exit;
            // $auth = $user['id'];
            cookie('member_auth', $auth, 86400*365);
            session('name', $auth);
            $this->redirect('index/index');
            exit;
        }
        // return $this->fetch();
    }

    // 验证码检测
    public function check($code=''){
        if (!captcha_check($code)) {
            return 0;//验证码错误
        }else{
            return 1;//验证码正确
        }
    }

    // 登录
    public function login(){
        if(request()->isPost()){
            $post = $this->GET;
            $post['studentid'] or $this->error('请填写用户名');
            $post['password'] or $this->error('请填写密码');
            // 检测管理员
            if ($post['role'] == 2) {
                $user = model('admin')->where('adminname',$post['studentid'])->find();
                if (!$user || $user['password'] != $post['password']) {
                    $flag = 0;
                    return $flag;
                }
            }elseif ($post['role'] == 1) {
                $user = model('stu')->where('studentid',$post['studentid'])->find();
                if(!$user || $user['password'] != $post['password']){
                    $flag = 0;
                    return $flag;
                }
            }

            if(!$this->check($post['yzm'])){
                $flag = 1;
                return $flag;
            }
            $auth = encrypt($post['role'].'|'.$user['userid'].'|'.$user['password'], 'USER'.$_SERVER['HTTP_USER_AGENT']);
 
            // 记住密码
            if (!empty($post['remember'])) {
                cookie('member_info', $auth, 86400*365);
            }
            session('member_auth', $auth);
            // $this->redirect('index/index');
            $flag = 2;
            return $flag;
        }
    }

    // 登出
    public function logout(){
        session('member_auth', null);
        cookie('member_info', null);
        $this->redirect('index/index');
    }

}