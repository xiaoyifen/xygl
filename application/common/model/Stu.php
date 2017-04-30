<?php
/**
 * 学生模型
 * @author lfn
 */
namespace app\common\model;
use think\Db;
use think\Model;
class Stu extends Model{

	// 捐赠一对多
	public function donation()
    {
        return $this->hasMany('Donation','donorid','userid');
    }

    // 主题帖一对多
	public function topic()
    {
        return $this->hasMany('Topic','authorid','userid');
    }

    // 回复帖一对多
	public function note()
    {
        return $this->hasMany('Note','authorid','userid');
    }

    // 留言发送者
    public function send()
    {
        return $this->hasMany('Message','senderid','userid');
    }

    // 留言接收者
    public function receive()
    {
        return $this->hasMany('Message','receiverid','userid');
    }
}
?>