<?php
namespace app\common\validate;
use think\Validate;

class Topic extends Validate
{
    protected $rule = [
        'image'  => 'image',
        'title'  => 'require',
        'content'=> 'require',
    ];

    protected $message  =  [       
        'image.image'    => '非法图像文件',
        'title.require'  => '请输入话题',
        'content.require'=> '请输入内容',
    ];

    protected $scene = [
        'image'   =>  ['image'],
        'info'  =>  ['title','content'],
    ];   

}
?>