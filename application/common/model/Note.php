<?php
/**
 * 回复贴模型
 * @author lfn
 */
namespace app\common\model;
use think\Db;
use think\Model;
class Note extends Model{

    public function topic(){
        return $this->belongsTo('Topic','topicid','topicid');
    }

    public function stu()
    {
        return $this->belongsTo('Stu','authorid','userid')->field('userid,username,enrollmentdate,department,major');
    }
}
?>