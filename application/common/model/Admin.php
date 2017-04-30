<?php
/**
 * 管理员模型
 * @author lfn
 */
namespace app\common\model;
use think\Db;
use think\Model;
class Admin extends Model{
    public function getauthorityAttr($value){
        $status = [0=>'超级管理员',1=>'管理员'];
        return $status[$value];
    }
}
?>