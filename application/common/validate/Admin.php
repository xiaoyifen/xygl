<?php
namespace app\common\validate;
use think\Validate;

class Admin extends Validate
{
    protected $rule = [
        'adminname'  => 'require',
        'password'  => 'require',
    ];

    protected $message  =  [       
        'adminname.require'    => '请填写姓名',
        'password.require'    => '请填写密码',
    ];  

}
?>