<?php
/**
 * 捐赠模型
 * @author lfn
 */
namespace app\common\model;
use think\Db;
use think\Model;
class Donation extends Model{

	public function stu()
    {
        return $this->belongsTo('Stu','donorid','id');
    }

}
?>