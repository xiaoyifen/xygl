<?php
/**
 * 管理员管理后台控制器
 * @author lfn
 */
namespace app\admin\controller;
use app\common\controller\Admin;
use think\File;

class Adminer extends Admin
{
    function __construct(){
		parent::__construct();
        $this->view->location = '管理员管理';
        $this->view->title = '管理员管理';
        $this->model = model('admin');    
	}

	public function index(){
        $items = $this->model->order('adminid desc')->paginate(10);
        $this->view->items = $items;
    	return $this->fetch();
    }

    // 添加
    public function add(){
        // 功能
        if(request()->isPost()){
            $post = $this->GET;
            $post['createtime'] = time();//添加时间
            $post['authority'] = 1;//权限设置
            $result = $this->model->validate(true)->allowField(true)->save($post);
            if(!$result){
                $this->error($this->model->getError());
            }
            $this->success('添加成功...','adminer/index');
            exit;
        }
        // 页面
        return $this->fetch('show');
    }

    // 查看/修改
    public function show($id){
        // 修改
        if(request()->isPost()){            
            $post = $this->GET;
            $result = $this->model->validate(true)->allowField(true)->isUpdate(true)->save($post);
            if(!$result){
                $this->error($this->model->getError());
            }
            $this->success('修改成功...','adminer/index');
            exit;
        }
        // 查看
        $item = $this->model->where(['adminid'=>$id])->find() or $this->error('数据不存在...');
        $info = $item->toArray($item);
        $this->view->info = $info;
        return $this->fetch();
    }

    //删除
    public function del($id){
        $item = $this->model->where(['adminid'=>$id])->find() or $this->error('数据不存在...');
        $info = $item->toArray($item);       
        $this->model->destroy(['adminid'=>$id]) or $this->error('删除失败');       
        $this->success('删除成功...');
    }
}
?>