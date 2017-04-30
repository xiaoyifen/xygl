<?php
/**
 * 审核日志模型
 * @author lfn
 */
namespace app\common\model;
use think\Db;
use think\Model;
class Log extends Model{
	
	public function article()
    {
        return $this->hasOne('Article','articleid','articleid');// id为article id
    }
}
?>