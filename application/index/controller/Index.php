<?php
/**
 * 新闻前台控制器
 * @author lfn
 */
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

    // 首页
    public function index(){
        $map['categoryid'] = '1';
        $items = $this->model->where($map)->order('articleid desc')->limit(16)->select();
        $this->view->items_news = $items;
        return $this->fetch();
    }

    // 新闻专区
    public function news(){
        $map['menuid'] = '1';
        $map['categoryid'] = '1';
        $news = $this->model->where($map)->order('updatetime desc')->paginate(2);
        $preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';// 匹配新闻内容中的图片
        foreach ($news as $k => $v) {
            preg_match_all($preg, $v['content'], $imgArr);
            if (!empty($imgArr[1])) {   
                $news[$k]['img'] = 1;      
            }else{
                $news[$k]['img'] = 0;
            }
        }
        $num = $this->model->where($map)->count();
        $hits = $this->model->where($map)->order('hits desc')->limit(9)->select();
        // var_dump($items);
        // exit;
        $this->view->num = $num;
        $this->view->news = $news;
        $this->view->hits = $hits;
        $this->view->category = '新闻专区';
        $this->view->categoryid = '1';
        return $this->fetch();
    }

    // 资料专区
    public function info(){
        $map['status'] = '1';
        $map['categoryid'] = '3';
        $news = $this->model->where($map)->order('updatetime desc')->paginate(2);
        $preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';// 匹配新闻内容中的图片
        foreach ($news as $k => $v) {
            preg_match_all($preg, $v['content'], $imgArr);
            if (!empty($imgArr[1])) {   
                $news[$k]['img'] = 1;      
            }else{
                $news[$k]['img'] = 0;
            }
        }
        $num = $this->model->where($map)->count();
        $hits = $this->model->where($map)->order('hits desc')->limit(9)->select();
        // var_dump($items);
        // exit;
        $this->view->num = $num;
        $this->view->news = $news;
        $this->view->hits = $hits;
        $this->view->category = '资料专区';
        $this->view->categoryid = '3';
        return $this->fetch('news');
    }

    // 招聘专区
    public function invite(){
        $map['status'] = '1';
        $map['categoryid'] = '2';
        $news = $this->model->where($map)->order('updatetime desc')->paginate(2);
        $preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';// 匹配新闻内容中的图片
        foreach ($news as $k => $v) {
            preg_match_all($preg, $v['content'], $imgArr);
            if (!empty($imgArr[1])) {   
                $news[$k]['img'] = 1;      
            }else{
                $news[$k]['img'] = 0;
            }
        }
        $num = $this->model->where($map)->count();
        $hits = $this->model->where($map)->order('hits desc')->limit(9)->select();
        // var_dump($items);
        // exit;
        $this->view->num = $num;
        $this->view->news = $news;
        $this->view->hits = $hits;
        $this->view->category = '招聘专区';
        $this->view->categoryid = '2';
        return $this->fetch('news');
    }

    // 校友捐赠
    public function donate(){
        $map['menuid'] = '2';
        $map['categoryid'] = '1';
        $news = $this->model->where($map)->order('updatetime desc')->paginate(2);
        $preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';// 匹配新闻内容中的图片
        foreach ($news as $k => $v) {
            preg_match_all($preg, $v['content'], $imgArr);
            if (!empty($imgArr[1])) {   
                $news[$k]['img'] = 1;      
            }else{
                $news[$k]['img'] = 0;
            }
        }
        $num = $this->model->where($map)->count();
        $hits = $this->model->where($map)->order('hits desc')->limit(9)->select();
        // var_dump($items);
        // exit;
        $this->view->num = $num;
        $this->view->news = $news;
        $this->view->hits = $hits;
        $this->view->category = '校友捐赠';
        $this->view->categoryid = '1';
        return $this->fetch('news');
    }

    // 新闻详细内容
    public function detail($id){
        // 点击量+1
        $this->model->where(['articleid'=>$id])->setInc('hits');
        $item = $this->model->where(['articleid'=>$id])->find() or $this->error('数据不存在...');
        $info = $item->toArray($item);
        $hit = $this->model->order('hits desc')->limit(9)->select();
        $this->view->hit = $hit;
        $this->view->info = $info;
        return $this->fetch();
    }

    // 添加
    public function add(){
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
}
