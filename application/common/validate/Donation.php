<?php
namespace app\common\validate;
use think\Validate;

class Donation extends Validate
{
    protected $rule = [
        'donorname'  => 'require',
        'topic'  => 'require',
        'money'  => 'require',
    ];

    protected $message  =  [       
        'donorname.require'    => '请填写捐赠人',
        'topic.require'    => '请填写捐赠项目',
        'money.require'    => '请填写捐赠实物或金额',
    ];  

}
?>