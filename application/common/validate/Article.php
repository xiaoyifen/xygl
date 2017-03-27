<?php
namespace app\common\validate;
use think\Validate;

class Article extends Validate
{
    protected $rule = [
        'image'  => 'image',
    ];

    protected $message  =  [       
        'image.image'    => '非法图像文件',
    ];

    // protected $scene = [
    //     'image'   =>  ['image'],
    //     'info'  =>  ['imagename','imagepath'],
    // ];   

}
?>