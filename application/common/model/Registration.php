<?php
/**
 * @author 覃小珍
 * 活动报名模型
 */
  namespace app\common\model;
  use think\Db;
  use think\Model;
  class Registration extends Model{
  	//定义关联方法，通过activityid关联到Activity模型
    public function registeToAct(){
 	  return $this->hasOne('Activity','activityid','activityid');
 	}
 	//定义关联方法
 	public function registeToStu(){
 		return $this->hasOne('Stu','userid','applicantid')->field('userid,username');
 	}
  }
?>
