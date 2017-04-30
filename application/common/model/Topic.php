<?php
/**
 * 主题贴模型
 * @author lfn
 */
namespace app\common\model;
use think\Db;
use think\Model;
class Topic extends Model{

	public function note()
    {
        return $this->hasMany('Note','topicid','topicid')->order('rank');
    }

    public function stu()
    {
        return $this->belongsTo('Stu','authorid','userid')->field('userid,username,enrollmentdate,department,major');
    }
}
?>