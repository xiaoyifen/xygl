<?php
/**
 * 新闻前台控制器
 * @author lfn
 */
namespace app\index\controller;
use app\common\controller\Home;

class Index extends Home
{
    function __construct(){
		parent::__construct();
        $this->view->location = '首页';
        $this->view->title = '首页';
        $this->model = model('article');    
	}

    // 判断是否含有图片
    public function img($items){
        $preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';// 匹配新闻内容中的图片
        foreach ($items as $k => $v) {
            preg_match_all($preg, $v['content'], $imgArr);
            if (!empty($imgArr[1])) {   
                $items[$k]['img'] = 1;      
            }else{
                $items[$k]['img'] = 0;
            }
        }
        return $items;
    }

    // 首页
    public function index(){
        $this->check_login();
        $map['categoryid'] = '1';
        $items = $this->model->where($map)->order('updatetime desc')->limit(16)->select();
        $items = $this->img($items);
        $this->view->items_news = $items;

        $map['categoryid'] = '2';
        $map['status'] = '1';
        $items = $this->model->where($map)->order('updatetime desc')->limit(12)->select();
        $this->view->items_employment = $items;

        $map['categoryid'] = '3';
        $map['status'] = '1';
        $items = $this->model->where($map)->order('updatetime desc')->limit(12)->select();
        $this->view->items_info = $items;

        $map['categoryid'] = '1';
        $map['top'] = '1';
        $items = $this->model->where($map)->order('updatetime desc')->limit(3)->select();
        $this->view->items_pic = $items;

        $items = model('activity')->where('status',1)->order('updatetime desc')->limit(14)->select();
        $this->view->items_activity = $items;

        $items = model('topic')->with('stu')->order('hits desc')->limit(11)->select();
        $this->view->items_topic = $items;

        return $this->fetch();
    }

    // 新闻专区
    public function news(){
        $this->check_login();
        $map['menuid'] = '1';
        $map['categoryid'] = '1';
        $news = $this->model->where($map)->order('updatetime desc')->paginate(10);
        $news = $this->img($news);
        $num = $this->model->where($map)->count();
        $hits = $this->model->where($map)->order('hits desc')->limit(9)->select();
        // var_dump($items);
        // exit;
        $this->view->num = $num;
        $this->view->news = $news;
        $this->view->hits = $hits;
        $this->view->category = '校友新闻';
        $this->view->categoryid = '1';
        $this->view->title = '校友新闻';
        return $this->fetch();
    }

    // 资料专区
    public function info(){
        $this->check_login();
        $map['status'] = '1';
        $map['categoryid'] = '3';
        $news = $this->model->where($map)->order('updatetime desc')->paginate(10);
        $news = $this->img($news);
        $num = $this->model->where($map)->count();
        $hits = $this->model->where($map)->order('hits desc')->limit(9)->select();
        // var_dump($items);
        // exit;
        $this->view->num = $num;
        $this->view->news = $news;
        $this->view->hits = $hits;
        $this->view->category = '学习共享';
        $this->view->categoryid = '3';
        $this->view->title = '学习共享';
        return $this->fetch('news');
    }

    // 招聘专区
    public function invite(){
        $this->check_login();
        $map['status'] = '1';
        $map['categoryid'] = '2';
        $news = $this->model->where($map)->order('updatetime desc')->paginate(10);
        $news = $this->img($news);
        $num = $this->model->where($map)->count();
        $hits = $this->model->where($map)->order('hits desc')->limit(9)->select();
        // var_dump($items);
        // exit;
        $this->view->num = $num;
        $this->view->news = $news;
        $this->view->hits = $hits;
        $this->view->category = '校友招聘';
        $this->view->categoryid = '2';
        $this->view->title = '校友招聘';
        return $this->fetch('news');
    }

    // 校友捐赠
    public function donate(){
        $this->check_login();
        $map['menuid'] = '2';
        $map['categoryid'] = '1';
        $news = $this->model->where($map)->order('updatetime desc')->paginate(10);
        $news = $this->img($news);
        $num = $this->model->where($map)->count();
        $hits = $this->model->where($map)->order('hits desc')->limit(9)->select();
        // var_dump($items);
        // exit;
        $this->view->num = $num;
        $this->view->news = $news;
        $this->view->hits = $hits;
        $this->view->category = '校友捐赠';
        $this->view->categoryid = '1';
        $this->view->title = '校友捐赠';
        return $this->fetch();
    }

    // 新闻详细内容
    public function detail($id){
        $this->check_login();
        // 点击量+1
        $this->model->where(['articleid'=>$id])->setInc('hits');
        $item = $this->model->with('stu')->where(['articleid'=>$id])->find() or $this->error('数据不存在...');
        $info = $item->toArray($item);
        $hit = $this->model->where(['status'=>1])->order('hits desc')->limit(9)->select();
        $this->view->hit = $hit;
        $this->view->info = $info;
        $this->view->title = $info['title'];
        return $this->fetch();
    }

    // 添加
    public function add(){
        $this->check_login_user();
        $this->check_auth();
        // 功能
        if(request()->isPost()){
            $post = $this->GET;
            $post['createtime'] = time();//发布时间
            $post['updatetime'] = time();//修改时间              
            // var_dump($post);
            $result = $this->model->allowField(true)->save($post);
            if(!$result){
                $this->error($this->model->getError());
            }
            $this->success('添加成功...','index/index');
            exit;
        }
        // 页面
        return $this->fetch();
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

    // 查询
    public function search(){
        $this->check_login();
        $map['status'] = '1';
        $hits = $this->model->where($map)->order('hits desc')->limit(9)->select();
        if(!empty($this->kw)){          
            $map['title'] = ['like',"%{$this->kw}%"];//标题
        }
        $news = $this->model->where($map)->order('updatetime desc')->paginate(2);
        $num = $this->model->where($map)->count();        
        // var_dump($items);
        // exit;
        $this->view->num = $num;
        $this->view->news = $news;
        $this->view->hits = $hits;
        return $this->fetch();
    }

    // 登录错误跳转页面
    public function errorLogin(){
        $this->view->title = '登录跳转';
        return $this->fetch('common/error');
    }

}
