<?php
/**
 * 审核日志模型
 * @author lfn
 */
namespace app\common\model;
use think\Db;
use think\Model;
class Log extends Model{

	//自定义初始化
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();
        //TODO:自定义的初始化
    }
	
	public function article()
    {
        return $this->hasOne('Article','id','articleid');// id为article id
    }
}
?>