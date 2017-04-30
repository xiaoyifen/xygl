<?php
/**
 * 论坛后台控制器
 * @author lfn
 */
namespace app\admin\controller;
use app\common\controller\Admin;

class Forum extends Admin
{
    function __construct(){
		parent::__construct();
        $this->view->location = '交流专区';
        $this->view->title = '交流专区';
        $this->model = model('topic');    
	}

    // 主题帖列表界面
    public function index(){
        $map = [];
        //查询
        if(!empty($this->kw)){
            $map['title'] = ['like',"%{$this->kw}%"];//标题
        }        
        $items = $this->model->view('Topic','*')->view('Note','time','Note.topicid = Topic.topicid','LEFT')->view('Stu','username','Stu.userid = Topic.authorid','LEFT')->field(['Topic.topicid','IFNULL(max(time),pubtime)'=>'lasttime'])->where($map)->group('Topic.topicid')->order('lasttime desc')->paginate(50);
        // var_dump($this->model->getLastSql());
        // var_dump($items);
        // exit;
        $this->view->items = $items;
        return $this->fetch();
    }

    // 帖子详细界面
    public function topic($id){
        $item = $this->model->with(['stu','note.stu'])->where(['topicid'=>$id])->find() or $this->error('数据不存在...');
        $info = $item->toArray($item);
        // var_dump($this->model->getLastSql());
        // var_dump($info);
        // exit;
        $this->view->info = $info;
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
    public function del($id){
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

    //删除回复帖
    public function del_reply($id){
        // 删除回复
        $item = model('note')->where(['noteid'=>$id])->find() or $this->error('数据不存在...'); 
        $info = $item->toArray($item);       
        model('note')->destroy(['noteid'=>$id]) or $this->error('删除失败');
        $this->del_img($info['content']);    
        $this->success('删除成功...');
    }

}
