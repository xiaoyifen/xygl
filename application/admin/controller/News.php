<?php
namespace app\admin\controller;
use app\common\controller\Admin;
use think\File;

class News EXTENDS Admin
{
    function __construct(){
		parent::__construct();
        $this->view->location = '新闻管理';
        $this->view->title = '新闻管理';
        $this->model = model('article');    
	}

    public function index(){
        $map = [];
        //查询
        if(!empty($this->kw)){
            if ($this->search == 1) {
                $map['title'] = ['like',"%{$this->kw}%"];//标题
            }elseif ($this->search == 2) {
                $createtime = strtotime($this->kw);
                $map['createtime'] = ['like',"%{$createtime}%"];//发布时间
            }elseif ($this->search == 3) {
                $updatetime = strtotime($this->kw);
                $map['updatetime'] = ['like',"%{$updatetime}%"];//修改时间
            }
        }
        $items = $this->model->where($map)->order('id asc')->paginate(50);
        $this->view->items = $items;
    	return $this->fetch();
    }

    // // 上传文件
    // public function upload()
    // {
    //     // 获取表单上传文件
    //     $file = request()->file('file');
    //     // 上传文件验证
    //     $result = $this->validate(['file' => $file],'Article.fileinfo');
    //     if(true !== $result){
    //         // 验证失败 输出错误信息
    //         $this->error($result);
    //     }
    //     // 移动到目录/static/uploads/ 目录下
    //     $info = $file->move(ROOT_PATH . 'static' . DS . 'uploads');
    //     $filein = $info->getInfo();
    //     $file_info['filename'] = $filein['name'];//获取原文件名
    //     $file_info['filepath'] = $info->getSaveName();//获取文件路径
    //     if ($info) {
    //         return $file_info;
    //     } else {
    //         // 上传失败获取错误信息
    //         $this->error($file->getError());
    //     }
    // }

    // 上传文件
    public function upload($name,$fileinfo)
    {
        // 获取表单上传文件
        $file = request()->file($name);
        // 上传文件验证
        $result = $this->validate([$name => $file],$fileinfo);
        if(true !== $result){
            // 验证失败 输出错误信息
            $this->error($result);
        }
        // 移动到目录/static/uploads/ 目录下
        $info = $file->move(ROOT_PATH . 'static' . DS . 'uploads');
        if ($info) {
            return $info;
        } else {
            // 上传失败获取错误信息
            $this->error($file->getError());
        }
    }

    //添加
    public function add()
    {
        if(IS_POST){
            $post = $this->GET;
            $post['createtime'] = time();
            $post['updatetime'] = time();
            $img_info = $this->upload('image','Article.img');
            $image = $img_info->getSaveName();//获取文件路径
            $file_info = $this->upload('file','Article.fileinfo');
            $filein = $file_info->getInfo();
            $filename = $filein['name'];//获取原文件名
            $filepath = $file_info->getSaveName();//获取文件路径
            $post['filepath'] = $filepath;
            $post['filename'] = $filename;
            $post['image'] = $image;
            var_dump($post);
            // $result = $this->model->allowField(true)->save($post);
            // if(!$result){
            //     $this->error($this->model->getError());
            // }
            // $this->success('添加成功...','news/index');
        }
    }


    //添加新闻
    public function addp(){
        return $this->fetch();
    }

    //查看/修改
    public function show($id){
        $item = $this->model->where(['id'=>$id])->find() or $this->error('数据不存在...');
        $info=$item->toArray($item);
        $this->view->info = $info;
        return $this->fetch('index');
    }

    //置顶
    public function top($id){
        $item = $this->model->where(['id'=>$id])->find() or $this->error('数据不存在...');
        $info=$item->toArray($item);
        $this->view->info = $info;
        return $this->fetch('index');
    }

    //删除
    public function del($id){
        $item = $this->model->where(['id'=>$id])->find() or $this->error('数据不存在...');
        $info=$item->toArray($item);
        $this->view->info = $info;
        return $this->fetch('index');
    }

}
