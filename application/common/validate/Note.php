<?php
namespace app\common\validate;
use think\Validate;

class Note extends Validate
{
    protected $rule = [
        'content'=> 'require',
    ];

    protected $message  =  [       
        'content.require'=> '请输入内容',
    ]; 

}
?>