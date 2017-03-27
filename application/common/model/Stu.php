<?php
/**
 * 学生模型
 * @author lfn
 */
namespace app\common\model;
use think\Db;
use think\Model;
class Stu extends Model{

	public function donation()
    {
        return $this->hasMany('Donation','donorid','id');
    }
}
?>