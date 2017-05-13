<?php
/**
 * 新闻后台控制器
 * @author lfn
 */
namespace app\admin\controller;
use app\common\controller\Admin;
use think\File;

class News extends Admin
{
    function __construct(){
		parent::__construct();
        $this->check_login();
        $this->view->location = '首页管理';
        $this->view->title = '首页管理';
        $this->model = model('article');    
	}

    public function index(){
        $map = [];
        //查询
        if(!empty($this->kw)){
            if ($this->search == 1) {
                if ($this->kw == '普通新闻') {
                    $menuid = 1;
                }elseif ($this->kw == '捐赠新闻') {
                    $menuid = 2;
                }
                $map['menuid'] = ['like',"{$menuid}"];//新闻类别
            }elseif ($this->search == 2) {
                $map['title'] = ['like',"%{$this->kw}%"];//标题
            }elseif ($this->search == 3) {
                $map['author'] = ['like',"%{$this->kw}%"];//作者
            }elseif ($this->search == 4) {
                $map['abstract'] = ['like',"%{$this->kw}%"];//摘要
            }elseif ($this->search == 5) {
                $map['content'] = ['like',"%{$this->kw}%"];//文章内容
            }
        }
        $map['categoryid'] = '1';
        $items = $this->model->where($map)->order('articleid desc')->paginate(10);
        $this->view->items = $items;
        $this->view->location = '新闻公告管理';
    	return $this->fetch();
    }

    // 上传图片
    public function upload_img(){
        // 获取表单上传图片
        $file = request()->file('image');
        // 判断是否有上传图片
        if (!empty($file)) {
            // 上传图片验证
            $result = $this->validate(['image' => $file],'Article');
            if(true !== $result){
                // 验证失败 输出错误信息
                $this->error($result);
            }
            // 移动到目录/static/uploads/ 目录下
            $info = $file->move(ROOT_PATH . 'static' . DS . 'uploads');
            if ($info) {
                $filein = $info->getInfo();
                $file_info['imagename'] = $filein['name'];//获取原图片名
                $file_info['imagepath'] = $info->getSaveName();//获取图片路径
                return $file_info;
            } else {
                // 上传失败获取错误信息
                $this->error($file->getError());
            }
        }      
    }

    // 上传文件
    public function upload_file(){
        // 获取表单上传文件
        $file = request()->file('file');
        // 判断是否有上传文件
        if (!empty($file)) {
            // 上传文件验证
            // $result = $this->validate(['file' => $file],'');
            // if(true !== $result){
            //     // 验证失败 输出错误信息
            //     $this->error($result);
            // }
            // 移动到目录/static/uploads/ 目录下
            $info = $file->move(ROOT_PATH.'static'.DS.'uploads');
            if ($info) {
                $filein = $info->getInfo();
                $file_info['filename'] = $filein['name'];//获取原文件名
                $file_info['filepath'] = $info->getSaveName();//获取文件路径
                return $file_info;
            } else {
                // 上传失败获取错误信息
                $this->error($file->getError());
            }
        }      
    }

    // 添加
    public function add(){
        $this->check_auth();
        // 功能
        if(request()->isPost()){
            $post = $this->GET;
            $post['createtime'] = time();//发布时间
            $post['updatetime'] = time();//修改时间 
            $post['status'] = 1;
            $post['categoryid'] = 1;             
            // var_dump($post);
            $result = $this->model->allowField(true)->save($post);
            if(!$result){
                $this->error($this->model->getError());
            }
            $this->success('添加成功...','news/index');
            exit;
        }
        // 页面
        $this->view->location = '新闻公告管理';
        return $this->fetch('show');
    }

    // 查看/修改
    public function show($id){
        // 修改
        if(request()->isPost()){
            $this->check_auth();            
            $post = $this->GET;
            $post['updatetime'] = time();
            $result = $this->model->allowField(true)->isUpdate(true)->save($post);
            if(!$result){
                $this->error($this->model->getError());
            }
            $this->success('修改成功...','news/index');
            exit;
        }
        // 查看
        $item = $this->model->where(['articleid'=>$id])->find() or $this->error('数据不存在...');
        $info = $item->toArray($item);
        $this->view->info = $info;
        $this->view->location = '新闻公告管理';
        return $this->fetch();
    }

    //置顶
    public function top($id){
        //判断是否通过审核，未过审核无法置顶
        //判断是否有图片，有图片，可置顶，无图片，不可
        //top置1，表示置顶
        $this->check_auth();
        $item = $this->model->has('category',['article.articleid'=>$id])->field('category.*,article.articleid')->find() or $this->error('数据不存在...');
        $status = $item->getData('status');
        $info = $item->toArray($item);
        if($status == 1) {
            if (!empty($info['imagepath'])) {
                $post = $this->GET;
                $post['top'] = '1';
                $post['updatetime'] = time();
                $post['articleid'] = $id;
                $result = $this->model->allowField(true)->isUpdate(true)->save($post);
                if(!$result){
                    $this->error($this->model->getError());
                }
                $this->success('置顶成功...');
            }else{
                $this->error('图片不存在，无法置顶');
            }
        }else{
            $this->error('未通过审核，无法置顶');
        }
               
    }

    //删除
    public function del($id){
        $this->check_auth();
        $item = $this->model->where(['articleid'=>$id])->find() or $this->error('数据不存在...');
        $info = $item->toArray($item);       
        $this->model->destroy(['articleid'=>$id]) or $this->error('删除失败');
        $preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';// 匹配新闻内容中的图片
        preg_match_all($preg, $info['content'], $imgArr);
        // 删除新闻内容中的图片
        if (!empty($imgArr[1])) {
            foreach ($imgArr[1] as $k => $v) {
                $path = ROOT_PATH.$v;
                is_file($path) && unlink($path);
            }          
        }
        // 删除上传图片
        if (!empty($info['imagepath'])) {
            $path = ROOT_PATH.'static'.DS.'uploads'.DS.$info['imagepath'];
            is_file($path) && unlink($path);
        }
        // 删除上传文件
        if (!empty($info['filepath'])) {
            $path = ROOT_PATH.'static'.DS.'uploads'.DS.$info['filepath'];
            is_file($path) && unlink($path);
        }
        $this->success('删除成功...');
    }

    // 审核界面
    public function check(){
        $map = [];
        //查询
        if(!empty($this->kw)){
            if ($this->search == 1) {
                $map['article.title'] = ['like',"%{$this->kw}%"];//标题
            }elseif ($this->search == 2) {
                $map['article.author'] = ['like',"%{$this->kw}%"];//作者
            }elseif ($this->search == 3) {
                $map['article.abstract'] = ['like',"%{$this->kw}%"];//摘要
            }elseif ($this->search == 4) {
                $map['article.content'] = ['like',"%{$this->kw}%"];//文章内容
            }elseif ($this->search == 5) {
                if ($this->kw == '待审核') {
                    $search = 0;
                }elseif ($this->kw == '已通过') {
                    $search = 1;
                }elseif ($this->kw == '未通过') {
                    $search = 2;
                }
                $map['article.status'] = ['like',"{$search}"];//审核状态
            }elseif ($this->search == 6) {
                $map['categoryname'] = ['like',"%{$this->kw}%"];//类别
            }
        }
        $map['categoryid'] = ['>',1];
        $items = $this->model->has('category',$map)->with('stu')->field('category.*,article.articleid')->order('status asc,article.articleid desc')->paginate(10);
        // var_dump($items);
        // exit;
        $this->view->items = $items;
        $this->view->location = '招聘/资料审核';
        return $this->fetch();
    }

    // 审核功能
    public function audit($id){
        // 审核
        if(request()->isPost()){
            $this->check_auth();            
            $post = $this->GET;
            // 写入审核未通过理由（日志表）
            if ($post['status'] == 2) {
                $info['articleid'] = $post['articleid'];
                $info['reason'] = $post['reason'];
                $info['time'] = time();
                $info['adminid'] = $post['adminid'];
                $log = model('log');
                $result = $log->validate(true)->allowField(true)->save($info);
                if(!$result){
                    $this->error($log->getError());
                }
            }
            $result = $this->model->allowField(true)->isUpdate(true)->save($post);
            if(!$result){
                $this->error($this->model->getError());
            }
            $this->success('审核成功...','news/check');
            exit;
        }
        // 一对一关联
        // $user = model('log');
        // var_dump($user->where('id=2')->find());
        // var_dump($user->where('id=2')->find()->article);
        // var_dump($this->model->where('id=5')->find());//id为article id
        // var_dump($this->model->where('id=5')->find()->log);//id为log id
        // exit;
        // 查看
        // 一对多关联
        // $item = model('category')->has('article',['article.id'=>14])->field('article.*')->select() or $this->error('数据不存在...');
        $item = $this->model->has('category',['article.articleid'=>$id])->with('stu')->field('category.*,article.articleid')->find() or $this->error('数据不存在...');
        $status = $item->getData('status');
        $info = $item->toArray($item);
        $info['status'] = $status;
        // 导出审核未通过理由（日志表中）
        if ($status == 2) {
            $item = $this->model->has('log',['article.articleid'=>$id])->field('log.*,article.articleid')->find() or $this->error('数据不存在...');
            $info_log = $item->toArray($item); 
            $info['reason'] = $info_log['reason'];
        }
        $this->view->info = $info;
        $this->view->location = '招聘/资料审核';
        return $this->fetch();
    }
}
