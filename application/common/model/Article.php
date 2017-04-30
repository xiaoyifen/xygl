<?php
/**
 * 新闻模型
 * @author lfn
 */
namespace app\common\model;
use think\Db;
use think\Model;
class Article extends Model{

	public function getStatusAttr($value){
        $status = [0=>'待审核',1=>'已通过',2=>'未通过'];
        return $status[$value];
    }

    public function getMenuidAttr($value){
        $status = [1=>'普通新闻',2=>'捐赠新闻'];
        return $status[$value];
    }

    public function log(){
        return $this->belongsTo('Log','articleid','articleid');// id为article id
    }

    public function category(){
        return $this->belongsTo('Category','categoryid','categoryid');
    }
}
?>