<?php
namespace app\index\controller;
use app\common\controller\Base;

class Index extends Base
{
    function __construct(){
		parent::__construct();
        $this->view->location = '首页';
        $this->view->title = '首页';
        $this->model = model('article');    
	}

    public function index()
    {
        $map['categoryid'] = '1';
        $map['top'] = '1';
        $items = $this->model->where($map)->order('id desc')->limit(3)->select();
        $this->view->items_news = $items;
        return $this->fetch();
    }
}
