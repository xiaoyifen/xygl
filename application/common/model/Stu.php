<?php

 /**
  * 学生模型
  * @author 覃小珍
  */
  
  namespace app\common\model;
  use think\Db;
  use think\Model;
  class Stu extends Model{
  	//定义关联方法
 	public function stuToRegister(){
 		return $this->hasOne('Registration','applicantid','userid');
 	}
  }
?>
