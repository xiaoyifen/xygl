<?php
/**
 * 论坛前台控制器
 * @author lfn
 */
namespace app\index\controller;
use app\common\controller\Home;

class Forum extends Home
{
    function __construct(){
		parent::__construct();
        $this->view->location = '交流专区';
        $this->view->title = '交流专区';
        $this->model = model('topic');    
	}

    // 主题帖列表界面
    public function index(){
        $this->check_login();
        $map = [];
        //查询
        if(!empty($this->kw)){
            $map['title'] = ['like',"%{$this->kw}%"];//标题
        }        
        $items = $this->model->view('Topic','*')->view('Note','time','Note.topicid = Topic.topicid','LEFT')->view('Stu','username','Stu.userid = Topic.authorid','LEFT')->field(['Topic.topicid','IFNULL(max(time),pubtime)'=>'lasttime'])->where($map)->group('Topic.topicid')->order('lasttime desc')->paginate(3);
        $num = $this->model->count();
        // var_dump($this->model->getLastSql());
        // var_dump($items);
        // exit;
        $this->view->num = $num;
        $this->view->items = $items;
        return $this->fetch();
    }

    // 帖子详细界面
    public function topic($id){
        $this->check_login();
        // 点击量+1
        $this->model->where(['topicid'=>$id])->setInc('hits');
        $item = $this->model->with(['stu','note.stu'])->where(['topicid'=>$id])->find() or $this->error('数据不存在...');
        $info = $item->toArray($item);
        // 回复贴
        $num = model('note')->where(['topicid'=>$id])->count();
        $query['id'] = $id; 
        $notes = model('note')->with(['stu'])->where(['topicid'=>$id])->order('noteid')->paginate(2,false,['query'=>$query]);
        // var_dump($this->model->getLastSql());
        // var_dump($note);
        // exit;
        $this->view->num = $num;
        $this->view->notes = $notes;
        $this->view->info = $info;
        return $this->fetch();
    }

    // 发布新帖
    public function add(){
        $this->check_login_user();
        $this->check_auth();
        // 功能
        if(request()->isPost()){
            $post = $this->GET;
            $post['pubtime'] = time();//发布时间 
            // 过滤关键词
            $keyword = model('keyword')->select();
            $post['title'] = filter($keyword,$post['title']);
            $post['content'] = filter($keyword,$post['content']);             
            // var_dump($post);
            $result = $this->model->allowField(true)->validate('Topic.info')->save($post);
            if(!$result){
                $this->error($this->model->getError());
            }
            $this->success('添加成功...','forum/index');
            exit;
        }
        // 页面
        return $this->fetch();
    }

    // 回复
    public function reply($id){
        $this->check_login_user();
        $this->check_auth();
        // 功能
        if(request()->isPost()){
            $post = $this->GET;
            // 过滤关键词
            $keyword = model('keyword')->select();
            $post['content'] = filter($keyword,$post['content']); 
            $post['time'] = time();
            $post['topicid'] = $id;
            $item = $this->model->where(['topicid'=>$id])->find() or $this->error('数据不存在...');
            $info = $item->toArray($item);
            $post['rank'] = $info['reply'] + 1;
            unset($post['id']);//
            $note = model('note');
            $result = $note->validate(true)->allowField(true)->save($post);// note插入
            if(!$result){
                $this->error($note->getError());
            }
            $topic['topicid'] = $id;
            $topic['reply'] = $post['rank'];
            $result = $this->model->allowField(true)->isUpdate(true)->save($topic);// topic更新
            if(!$result){
                $this->error($this->model->getError());
            }
            $this->success('回复成功...','forum/topic?id='.$id);
            exit;
        }
    }

    // 上传图片
    public function upload_img(){
        // 获取表单上传图片
        $file = request()->file('image');
        // 判断是否有上传图片
        if (!empty($file)) {
            // 上传图片验证
            $result = $this->validate(['image' => $file],'Topic.image');
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
}
