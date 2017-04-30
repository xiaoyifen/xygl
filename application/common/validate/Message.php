<?php
namespace app\common\validate;
use think\Validate;

class Message extends Validate
{
    protected $rule = [
        'image'  => 'image',
        'content'=> 'require',
    ];

    protected $message  =  [       
        'image.image'    => '非法图像文件',
        'content.require'=> '请输入内容',
    ];

    protected $scene = [
        'image'   =>  ['image'],
        'info'  =>  ['content'],
    ];   

}
?>