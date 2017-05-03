<?php
/**
 * 个人中心前台控制器
 * @author lfn
 */
namespace app\index\controller;
use app\common\controller\Home;

class Selfcenter extends Home
{
    function __construct(){
		parent::__construct();
        $this->check_login_user();
        $this->check_auth();
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

    // 查看/修改 招聘/资料
    public function show($id){
        $this->model = model('article');
        // 修改
        if(request()->isPost()){            
            $post = $this->GET;
            // var_dump($post);
            // exit;
            $post['updatetime'] = time();
            $post['status'] = 0;
            $result = $this->model->allowField(true)->isUpdate(true)->save($post);
            if(!$result){
                $this->error($this->model->getError());
            }
            if ($post['categoryid'] == '2') {
                $this->success('修改成功...','Selfcenter/employment?id='.$post['authorid']);
            }else{
                $this->success('修改成功...','Selfcenter/data?id='.$post['authorid']);
            }            
            exit;
        }
        // 查看
        $item = $this->model->where(['articleid'=>$id])->find() or $this->error('数据不存在...');
        $info = $item->toArray($item);
        $this->view->info = $info;
        $this->view->location = '招聘资料';
        $this->view->locationNext = '查看/修改';
        return $this->fetch();
    }

    //删除
    public function del($id){
        $this->model = model('article');
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
        // 删除上传文件
        if (!empty($info['filepath'])) {
            $path = ROOT_PATH.'static'.DS.'uploads'.DS.$info['filepath'];
            is_file($path) && unlink($path);
        }
        $this->success('删除成功...');
    }

    // 主题帖列表界面
    public function topic($id){
        $this->model = model('topic');
        $map['Topic.authorid'] = $id;      
        $items = $this->model->view('Topic','*')->view('Note','time','Note.topicid = Topic.topicid','LEFT')->view('Stu','username','Stu.userid = Topic.authorid','LEFT')->field(['Topic.topicid','IFNULL(max(time),pubtime)'=>'lasttime'])->where($map)->group('Topic.topicid')->order('lasttime desc')->paginate(10);
        $num = $this->model->where(['authorid' => $id])->count();
        // var_dump($this->model->getLastSql());
        // var_dump($items);
        // exit;
        $this->view->num = $num;
        $this->view->items = $items;
        $this->view->location = '留言帖子';
        $this->view->locationNext = '已发帖子';
        return $this->fetch();
    }

    // 删除图片
    public function del_img($content){
        // 匹配图片
        $preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
        preg_match_all($preg, $content, $imgArr);
        // 删除内容中的图片
        if (!empty($imgArr[1])) {
            foreach ($imgArr[1] as $k => $v) {
                $path = ROOT_PATH.$v;
                is_file($path) && unlink($path);
            }          
        }
    }

    //删除主题帖
    public function delTopic($id){
        $this->model = model('topic');
        // 删除回复
        $num = model('note')->where(['topicid'=>$id])->count();
        if ($num) {
            $item = model('note')->where(['topicid'=>$id])->select() or $this->error('数据不存在...'); 
            model('note')->destroy(['topicid'=>$id]) or $this->error('删除失败');
            foreach ($item as $key => $value) {
                $this->del_img($value['content']);
            }   
        }          
        // 删除主题
        $item = $this->model->where(['topicid'=>$id])->find() or $this->error('数据不存在...');
        $info = $item->toArray($item);       
        $this->model->destroy(['topicid'=>$id]) or $this->error('删除失败');
        $this->del_img($info['content']);
        $this->success('删除成功...');
    }

    // 查看/修改主题帖
    public function showTopic($id){
        $this->model = model('topic');
        // 修改
        if(request()->isPost()){            
            $post = $this->GET;
            $result = $this->model->allowField(true)->isUpdate(true)->save($post);
            if(!$result){
                $this->error($this->model->getError());
            }            
            $this->success('修改成功...','Selfcenter/topic?id='.$post['authorid']);           
            exit;
        }
        // 查看
        $item = $this->model->where(['topicid'=>$id])->find() or $this->error('数据不存在...');
        $info = $item->toArray($item);
        $this->view->info = $info;
        $this->view->location = '留言帖子';
        $this->view->locationNext = '查看/修改帖子';
        return $this->fetch();
    }

    // 留言列表
    public function message($id){  
        $this->model = model('message'); 
        $num = $this->model->where(['ownerid'=>$id,'replyid'=>0])->count();    
        $items = $this->model->with('sender')->where(['ownerid'=>$id,'replyid'=>0])->order('time DESC')->paginate(10);
        $this->view->items = $items;
        $this->view->ownerid = $id;
        $this->view->num = $num;
        $this->view->location = '留言帖子';
        $this->view->locationNext = '已收留言';
        return $this->fetch();
    }

    // 回复留言
    public function showMessage($id){
        $this->model = model('message');
        // 修改
        if(request()->isPost()){            
            $post = $this->GET;
            $post['time'] = time();//发布时间 
            // 过滤关键词
            $keyword = model('keyword')->select();
            $post['content'] = filter($keyword,$post['content']);             
            $result = $this->model->allowField(true)->validate('Message.info')->save($post);
            if(!$result){
                $this->error($this->model->getError());
            }            
            $this->success('回复成功...');        
            exit;
        }
        // 查看
        $item = $this->model->with('sender')->where(['messageid'=>$id])->find() or $this->error('数据不存在...');
        $info = $item->toArray($item);
        $items = $this->model->with(['sender','receiver'])->where(['replyid'=>$id])->order('time')->select();
        $this->view->info = $info;
        $this->view->items = $items;
        $this->view->location = '留言帖子';
        $this->view->locationNext = '回复留言';
        return $this->fetch();
    }
}
?>