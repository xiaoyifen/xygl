<?php
/**
 * 个人中心前台控制器
 * @author lfn
 */
namespace app\index\controller;
use app\common\controller\Base;

class Selfcenter extends Base
{
    function __construct(){
		parent::__construct();
        $this->view->location = '个人中心';
        $this->view->title = '个人中心';    
	}

    // 发布的招聘
    public function employment($id){
        $map['categoryid'] = '2';
        $map['authorid'] = $id;
        $this->model = model('article');
        $items = $this->model->where($map)->order('articleid desc')->paginate(10);
        $this->view->location = '招聘资料';
        $this->view->locationNext = '已发布招聘信息';
        $this->view->items = $items;
        return $this->fetch();
    }

    // 发布的资料
    public function data($id){
        $map['categoryid'] = '3';
        $map['authorid'] = $id;
        $this->model = model('article');
        $items = $this->model->where($map)->order('articleid desc')->paginate(10);
        $this->view->location = '招聘资料';
        $this->view->locationNext = '已发布资料';
        $this->view->items = $items;
        return $this->fetch('employment');
    }

    // 审核未通过原因
    public function reason($id){
        $map['articleid'] = $id;
        $this->model = model('log');
        $items = $this->model->where($map)->order('time desc')->paginate(10);
        $this->view->location = '招聘资料';
        $this->view->locationNext = '审核未通过原因';
        $this->view->items = $items;
        return $this->fetch();
    }
}
?>