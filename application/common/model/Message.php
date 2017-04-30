<?php
/**
 * 留言模型
 * @author lfn
 */
namespace app\common\model;
use think\Db;
use think\Model;
class Message extends Model{

	public function sender()
    {
        return $this->belongsTo('Stu','senderid','userid')->field('userid,username');
    }

    public function receiver()
    {
        return $this->belongsTo('Stu','receiverid','userid')->field('userid,username');
    }
}
?>