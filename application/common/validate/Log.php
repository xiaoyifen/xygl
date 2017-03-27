<?php
namespace app\common\validate;
use think\Validate;

class Log extends Validate
{
    protected $rule = [
        'reason'  => 'require'
    ];

    protected $message  =  [       
        'reason.require'    => '请输入理由'
    ];  

}
?>