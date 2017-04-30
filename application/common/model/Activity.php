<?php
/**
 * @author 覃小珍
 * 活动模型
 */
 namespace app\common\model;
 use think\Db;
 use think\Model;
 class Activity extends Model{
 	//定义关联方法,通过活动发起人ID关联用户表的userid
 	public function stus(){
 		return $this->belongsTo('Stu','userid','userid')->field('userid,username');
 	}
 	public function actToregiste(){
 		return $this->hasOne('registration','activityid','acticityid');
 	}
 	//定义类型转换
// 	protected $actParam=[
// 		'activitytime'=>'timestamp',
// 		'deadline'=>'timestamp',
// 	];
// 	//定义时间戳字段名
// 	protected $createtime='createtime';
// 	protected $updatetime='updatetime';
// 	//指定自动写入时间戳类型为dateTime类型
// 	protected $autoWriteTimestamp='datetime';
 }
?>
