<?php
namespace app\common\validate;
use think\Validate;

class Article extends Validate
{
    protected $rule = [
        'file'   => 'require',
        'image'  => 'require|image',
    ];

    protected $message  =   [
        'file.require'   => '请选择上传文件',
        'image.require'  => '请选择上传图片',
        'image.image'    => '非法图像文件',
     ];

    protected $scene = [
		'fileinfo' => ['file'],
		'img'      => ['image'],
	];
}
?>