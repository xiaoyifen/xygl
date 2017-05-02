<?php
/**
 * 留言前台控制器
 * @author lfn
 */
namespace app\index\controller;
use app\common\controller\Base;

class Message extends Base
{
    function __construct(){
		parent::__construct();
        $this->view->location = '留言板';
        $this->view->title = '留言板';
        $this->model = model('message');    
	}

    // 留言板界面
    public function index($id)
    {       
        $items = $this->model->with('sender')->where(['ownerid'=>$id,'replyid'=>0])->order('time DESC')->paginate(50);
        // var_dump($this->model->getLastSql());
        // var_dump($items);
        foreach ($items as $k => $v) {
            $item[$k]['message'] = $v;
            $item[$k]['reply'] = $this->model->with(['sender','receiver'])->where(['ownerid'=>$id,'replyid'=>$v['messageid']])->order('time')->select();
            // var_dump($this->model->getLastSql());   
        }
        // var_dump($item);
        // exit;
        $this->view->item = $item;
        $this->view->items = $items;
        $this->view->ownerid = $id;
        return $this->fetch();
    }

    // 留言
    public function add(){
        // 功能
        if(request()->isPost()){
            $post = $this->GET;
            $post['replyid'] = 0;
            $post['time'] = time();//发布时间 
            // 过滤关键词
            $keyword = model('keyword')->select();
            $post['content'] = filter($keyword,$post['content']);             
            // var_dump($post);
            $result = $this->model->allowField(true)->validate('Message.info')->save($post);
            if(!$result){
                $this->error($this->model->getError());
            }
            $this->success('留言成功...','message/index?id='.$post['ownerid']);
            exit;
        }
    }

    // 回复留言
    public function reply(){
        // 功能
        if(request()->isPost()){
            $post = $this->GET;
            $post['time'] = time();//发布时间 
            // 过滤关键词
            $keyword = model('keyword')->select();
            $post['content'] = filter($keyword,$post['content']);             
            // var_dump($post);
            $result = $this->model->allowField(true)->validate('Message.info')->save($post);
            if(!$result){
                $this->error($this->model->getError());
            }
            $this->success('回复成功...','message/index?id='.$post['ownerid']);
            exit;
        }
    }

    //删除留言
    public function del($id){
        $num = $this->model->where(['replyid'=>$id])->count();
        // 删除留言
        $this->model->destroy(['messageid'=>$id]) or $this->error('删除失败');     
        // 删除该留言的回复  
        if ($num) {
            $this->model->destroy(['replyid'=>$id]) or $this->error('删除失败');
        }          
        $this->success('删除成功...');
    }

}