<?php
/**
 * 类别模型
 * @author lfn
 */
namespace app\common\model;
use think\Db;
use think\Model;
class Category extends Model{
	
	public function getStatusAttr($value){
	    $status = [0=>'待审核',1=>'已通过',2=>'未通过'];
	    return $status[$value];
	}

	public function article()
    {
        return $this->hasMany('Article','categoryid','categoryid');
    }
}
?>