<?php
/**
 * 前台登录控制器
 * @author lfn
 */
namespace app\index\controller;
use app\common\controller\Home;

class Login extends Home
{
    function __construct(){
		parent::__construct();
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
                $userid = $user['adminid'];
                if (!$user || $user['password'] != $post['password']) {
                    $flag = 0;                    
                    return $flag;
                }
            }elseif ($post['role'] == 1) {
                $user = model('stu')->where(['status'=>1,'studentid'=>$post['studentid']])->find();
                $userid = $user['userid'];
                if(!$user || $user['password'] != $post['password']){
                    $flag = 0;                   
                    return $flag;
                }
            }

            if(!$this->check($post['yzm'])){
                $flag = 1;
                return $flag;
            }
            $auth = encrypt($post['role'].'|'.$userid.'|'.$user['password'], 'USER'.$_SERVER['HTTP_USER_AGENT']);
 
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

    // 注册
    public function sign(){
        if(request()->isPost()){
            $post = $this->GET;
            $post['status'] = 0;
            $post['graduationdate'] = $post['enrollmentdate'] + 4;
            $user = model('stu');
            $result = $user->allowField(true)->save($post);
            if(!$result){
                $this->error($user->getError());
            }
            $this->success('注册成功，待审核...','index/index');
            exit;
        }
        return $this->fetch();
    }

}