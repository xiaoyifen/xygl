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

    // 活动报名
    public function stuToRegister(){
        return $this->hasOne('Registration','applicantid','userid');
    }
    public function getSexAttr($value){
        $status = [0=>'男',1=>'女'];
        return $status[$value];
    }
    // 就业方向
    public function getEmploymentAttr($value){
        $status = [1=>'IT',2=>'管理类',3=>'营销类',4=>'工程类',5=>'金融类'];
        return $status[$value];
    }
    // 毕业去向
    public function getDirectionAttr($value){
        $status = [1=>'就业',2=>'继续深造',3=>'创业'];
        return $status[$value];
    }
}
?>

