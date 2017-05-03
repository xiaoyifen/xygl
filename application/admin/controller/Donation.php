<?php
/**
 * 捐赠后台控制器
 * @author lfn
 */
namespace app\admin\controller;
use app\common\controller\Admin;
use think\File;

class Donation extends Admin
{
    function __construct(){
		parent::__construct();
        $this->check_login();
        $this->view->location = '捐赠管理';
        $this->view->title = '捐赠管理';
        $this->model = model('donation');    
	}

    public function index(){
        $map = [];
        //查询
        if(!empty($this->kw)){
            if ($this->search == 1) {
                $map['topic'] = ['like',"%{$this->kw}%"];//捐赠项目
            }elseif ($this->search == 2) {
                $map['donorname'] = ['like',"%{$this->kw}%"];//捐赠人
            }elseif ($this->search == 3) {
                $map['money'] = ['like',"%{$this->kw}%"];//捐赠实物或金额
            }
        }
        // $items = $this->model->has('stu',$map)->field('stu.*,donation.id')->order('donation.id desc')->paginate(50);
        // var_dump($this->model->getLastSql());
        $items = $this->model->where($map)->order('donationid desc')->paginate(10);
        $this->view->items = $items;
    	return $this->fetch();
    }

    // 添加
    public function add(){
        $this->check_auth();
        // 功能
        if(request()->isPost()){
            $post = $this->GET;
            $post['donationtime'] = time();//捐赠时间            
            $result = $this->model->validate(true)->allowField(true)->save($post);
            if(!$result){
                $this->error($this->model->getError());
            }
            $this->success('添加成功...','donation/index');
            exit;
        }
        // 页面
        return $this->fetch('show');
    }

    // 查看/修改
    public function show($id){
        // 修改
        if(request()->isPost()){
            $this->check_auth();            
            $post = $this->GET;
            $result = $this->model->validate(true)->allowField(true)->isUpdate(true)->save($post);
            if(!$result){
                $this->error($this->model->getError());
            }
            $this->success('修改成功...','donation/index');
            exit;
        }
        // 查看
        $item = $this->model->where(['donationid'=>$id])->find() or $this->error('数据不存在...');
        $info = $item->toArray($item);
        $this->view->info = $info;
        return $this->fetch();
    }

    //删除
    public function del($id){
        $this->check_auth();
        $item = $this->model->where(['donationid'=>$id])->find() or $this->error('数据不存在...');
        $info = $item->toArray($item);       
        $this->model->destroy(['donationid'=>$id]) or $this->error('删除失败');       
        $this->success('删除成功...');
    }

}
?>