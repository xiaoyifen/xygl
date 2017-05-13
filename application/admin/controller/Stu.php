<?php
/**
 * @author 覃小珍
 * 信息管理模块，管理员可对账户进行增删改查
 */
 namespace app\admin\controller;
 use app\common\controller\Admin;
 
 class Stu EXTENDS Admin{
 	function __construct(){
 		parent::__construct();
 		$this->view->location = '校友信息管理';
    	$this->view->title = '校友信息管理';
    	$this->check_login();
// 		$stu=new Stu();
 	}
  	 /*
  	  * 查找学生列表（默认是查找所有学生）
  	  */
  	  public function findStuList(){
  	  	//实例化模型
  	  	$stu=model('stu');
  	  	//获取参数，获取查找方式，1代表按院系查询，2代表按专业查询,3代表按同学，获取入学年份和关键字
  	  	$param=$this->GET;
  	  	if(!empty($param)){
  	  		if($param['searchtype']==1){
  	  			//按院系查询
	 			$department=['like',"%{$param['keyword']}%"];
	 			if($param['enrollmentdate']==0){
	 				$list=$stu->where(['department'=>$department,'status'=>1])->paginate(15);//分页	
	 			}else{
	 				$list=$stu->where(['department'=>$department,'status'=>1,'enrollmentdate'=>$param['enrollmentdate']])->paginate(15);//分页	
	 			}
 			}else{
 				if($param['searchtype']==2){
 					//按专业查询
 					$major=['like',"%{$param['keyword']}%"];
 					if($param['enrollmentdate']==0){
 						$list=$stu->where(['major'=>$major,'status'=>1])->paginate(15);//分页
 					}else{
 						$list=$stu->where(['major'=>$major,'status'=>1,'enrollmentdate'=>$param['enrollmentdate']])->paginate(15);//分页
 					}
 				}else{
 					//选择按同学查询
		 			$username=['like',"%{$param['keyword']}%"];
		 			if($param['enrollmentdate']==0){
		 				$list=$stu->where(['username'=>$username,'status'=>1])->paginate(15);//分页
		 			}else{
		 				$list=$stu->where(['username'=>$username,'status'=>1,'enrollmentdate'=>$param['enrollmentdate']])->paginate(15);//分页
		 			}
 				}	
 			}
  	  	}else{
  	  		$list=$stu->where(['status' => 1])->paginate(15);
  	  	}
  	  	$this->assign('list',$list);
  	  	return $this->fetch('stuList');
  	  }
  	  /*
  	   * 
  	   */
  	  /*
  	   * 管理员查看或者修改学生信息
  	   */
  	  public function showOrChange($userid){
  	  	//获取参数
  	  	$userid=$this->GET;
  	  	//实例化模型
  	  	$stu=model('stu');
  	  	$list=$stu->where('userid',$userid['userid'])->find();
  	  	$list=$list->getData();
  	  	$this->assign('list',$list);
  	  	return $this->fetch('showOrChange');
  	  } 
  	  /*
 	 * 修改个人信息的方法
 	 */
 	 public function modifyInfo(){
 	 	$this->check_auth();
 	 	//获取参数
 	 	$param=$this->GET;
// 	 	var_dump($param);
// 	 	exit;
 	 	$stu=model('stu');
 	 	$param['birthday']=strtotime($param['birthday']);
 	 	$result=$stu->where('userid',$param['userid'])->update($param);
 	 	if($result !=0){
 	 		$this->success('信息修改成功');
 	 		$this->showOrChange($param['userif']);
 	 	}else{
 	 		$this->error('信息修改操作失败'); 
 	 	}
 	 }
 	/*
 	 * 管理员删除学生信息,删除单个或者批量删除
 	 */
 	 public function del($userid){
 	 	$this->check_auth();
 	 	//首先获取用户账号
 	 	$userid=$this->GET;
 	 	$stu=model('stu');
 	 	//删除该用户对于的数据
 	 	if(count($userid['userid'])>1){
			$result=$stu->where('userid','in',implode(',',$userid['userid']))->delete();
 	 	}else{
 	 		 $result=$stu->where('userid',$userid['userid'])->delete();
 	 	}
 	 	if($result !=0){
 	 		$this->success('成功删除'.$result.'条数据');
 	 	}else{
 	 		$this->error('删除失败');
 	 	}
 	 }
 	 /*
 	  * 渲染添加学生信息页面
 	  */
 	  public function addStu(){
  	 	return $this->fetch();
  	 }
 	 /*
 	  * 管理员添加账户
 	  */
 	  public function add(){
 	  	$this->check_auth();
 	  	//获取添加账户的参数
 	  	$param=$this->GET;
 	  	$stu=model('stu');
 	  	$param['birthday']=strtotime($param['birthday']);
 	  	$param['status']=1;
      $param['createtime']=time();
 	  	//添加数据
 	  	$result=$stu->save($param);
 	  	if($result !=0){
 	  		$this->success('成功添加该用户');
 	  	}else{
 	  		$this->error('添加该用户失败');
 	  	}
 	  }
 	  /*
 	  * 渲染批量上传信息页面
 	  */
 	  public function importStu(){
  	 	return $this->fetch();
  	 }
 	  /*
 	   * 批量添加学生信息
 	   */
 	   public function addBatch(){
 	   	$this->check_auth();
 	   	 //获取表单上传文件
 	   	 $file=request()->file('file');
// 	   	 var_dump($file);
 	   	 if(!empty($file)){	
//			import("@.ORG.UploadFile");
		   	$config=array(
			    'exts' => array('xlsx','xls'),
                'maxSize' => 3145728,
                'rootPath' =>"./uploads/",
                'saveName' => array('date','Ymd'),
			);
		    $info=$file->move(ROOT_PATH.'static'.DS.'uploads');
//		    var_dump($info);
//		    exit;
//		    if($info){
//		    	echo 'true';
//		    }
//		    exit;
// 			$upload = new \Think\Upload($config);
// 			$file_name=$upload->rootPath.$info['excel']['savepath'].$info['excel']['savename'];
// 			var_dump($file_name) ;
// 			 exit;
		    if($info){
		    	//引入类库
		    	vendor("PHPExcel.PHPExcel");
//		    	import("vendor.PHPExcel.Reader.Excel5");
		    	//初始化引入的方法
//		    	$objPHPExcel=new \PHPExcel();
//		    	$PHPReader=new \PHPExcel_Reader_Excel5();
		    	$filein=$file->getInfo();
		    	$file_info['filename']=$filein['name'];//文件原来的名字
		    	$file_info['filepath']=$info->getSaveName();//文件存储的文件名
//		    	var_dump($file_info['filename']);
////		    	echo '<br>';
//		    	echo $file_info['filepath'];
////		    	echo '<br>';
//		    	echo $file_info['savepath'];
//		    	exit;
		    	//导入表格后缀格式
		    	$extension=$info->getExtension();
//		    	var_dump($extension);
//		    	exit;
		    	if(($extension)=='xlsx'){
//		    		var_dump($extension);
//		    		echo '<br>';
		    		$objReader =\PHPExcel_IOFactory::createReader('Excel2007');
		    		$objPHPExcel =$objReader->load(ROOT_PATH.'static'.DS.'uploads'.DS.$file_info['filepath'], $encode = 'utf-8');
//		    		echo ROOT_PATH.'static'.DS.'uploads'.DS.$file_info['filepath'];
//		    		exit;
		    	}elseif($extension=='xls'){
		    		$objReader =\PHPExcel_IOFactory::createReader('Excel5');
		    		$objPHPExcel =$objReader->load(ROOT_PATH.'static'.DS.'uploads'.DS.$file_info['filepath'], $encode = 'utf-8');
		    	}
		    	$sheet =$objPHPExcel->getSheet(0);//获取表中第一个工作表
//				var_dump($sheet);
          		$highestRow = $sheet->getHighestRow();//取得总行数
//				var_dump($highestRow);
				echo '<br>';
          		$highestColumn =$sheet->getHighestColumn(); //取得总列数
//          		var_dump($highestColumn);
//          		exit;
				//实例化stu模型
		        $stu=model('stu');
		        $list=0;
          		//将excle中的数据取出来存在数组中
          		for($i =2; $i<= $highestRow;$i++) {
					$stu->studentid =$objPHPExcel->getActiveSheet()->getCell("A" . $i)->getValue();
//		            var_dump($data['studentid']);
//		            exit;
		            $stu->username=$objPHPExcel->getActiveSheet()->getCell("B" .$i)->getValue();
		            $stu->password=$objPHPExcel->getActiveSheet()->getCell("C" .$i)->getValue();
//		            $stu->password=$data['password']=md5($passwd);
				    $sex =$objPHPExcel->getActiveSheet()->getCell("D" .$i)->getValue();
			        if($sex=='男'){
			        	$stu->sex=0;
			        }else{
			        	$stu->sex=1;
			        }
			        $birthday = $objPHPExcel->getActiveSheet()->getCell("E". $i)->getValue();
//			        var_dump($birthday);
			        $stu->birthday=strtotime($birthday);
//			        var_dump($stu->birthday);
//			        echo '<br>';
			        $stu->hometown=$objPHPExcel->getActiveSheet()->getCell("F" .$i)->getValue();
			        $stu->enrollmentdate=$objPHPExcel->getActiveSheet()->getCell("G" . $i)->getValue();
			        $stu->graduationdate=$objPHPExcel->getActiveSheet()->getCell("H" . $i)->getValue();
			        $stu->department=$objPHPExcel->getActiveSheet()->getCell("I" . $i)->getValue();
		          	$stu->major=$objPHPExcel->getActiveSheet()->getCell("J" . $i)->getValue();
		          	$stu->class=$objPHPExcel->getActiveSheet()->getCell("K" . $i)->getValue();
		          	$stu->cellphone=$objPHPExcel->getActiveSheet()->getCell("L" . $i)->getValue();
//		          	$stu->direction=$data['dire    ction'] =$objPHPExcel->getActiveSheet()->getCell("L" . $i)->getValue();
//		          	$stu->employment=$data['employment'] =$objPHPExcel->getActiveSheet()->getCell("M" . $i)->getValue();
//		          	$stu->workplace=$data['workplace'] =$objPHPExcel->getActiveSheet()->getCell("N" . $i)->getValue();
//		          	$stu->workpost=$data['workpost'] =$objPHPExcel->getActiveSheet()->getCell("O" . $i)->getValue();
		          	$stu->cellphone=$objPHPExcel->getActiveSheet()->getCell("L" . $i)->getValue();
		          	$stu->status=1;
                $stu->createtime=time();
//		          	$stu->qq=$data['qq'] =$objPHPExcel->getActiveSheet()->getCell("Q" . $i)->getValue();
//		          	$stu->weixin=$data['weixin'] =$objPHPExcel->getActiveSheet()->getCell("R" . $i)->getValue();
//		          	$stu->address=$data['address'] =$objPHPExcel->getActiveSheet()->getCell("S" . $i)->getValue();
//		          	$stu->hobby=$data['hobby'] =$objPHPExcel->getActiveSheet()->getCell("T" . $i)->getValue();
//		          	$stu->sign=$data['sign'] =$objPHPExcel->getActiveSheet()->getCell("U" . $i)->getValue();
//		          	$stu->introduction=$data['introduction'] =$objPHPExcel->getActiveSheet()->getCell("V" . $i)->getValue();
		          	//将数据添加到数据库中
//		          	var_dump($stu);
		          	if($i==2){
		          		$result=$stu->save();
		          		$list=$list+1;
		          	}else{
//		          		var_dump($stu);
		          		$result=$stu->setAttr('userid',null)->isUpdate(false)->save();
		          		$list=$list+1;
		          	}
          		}
//          		exit;
//          		var_dump($list);
//		          	exit;
          		if(($list+1)==$highestRow){
          			$this->success('导入成功!');
          		}else{
          			$this->error('导入出错!');
          		}
		    }else{
		    	$this->error($file->getError());
		    }
		 }else{
		 		$this->error('请选择文件');
		 }
 	   }

 	// 审核界面
    public function check(){
    	$stu = model('stu');
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
        $map['status'] = 0;
        $items = $stu->where($map)->order('createtime desc')->paginate(10);
        // var_dump($items);
        // exit;
        $this->view->items = $items;
        $this->view->location = '注册信息审核';
        return $this->fetch();
    }

 	// 审核
    public function audit($id){
    	$stu = model('stu');
        $item = $stu->where(['userid'=>$id])->find() or $this->error('数据不存在...');
        $info = $item->toArray($item);
        $this->view->info = $info;
        $this->view->location = '注册信息审核';
        return $this->fetch();
    }

    // 身份验证
    public function prove($id){
    	$stu = model('stu');
    	$item = $stu->where(['userid'=>$id])->find() or $this->error('数据不存在...');
    	$info = $item->toArray($item);
    	$stu_all = model('stu_all');
    	$items = $stu_all->order('id asc')->select();
    	foreach ($items as $k => $v) {
    		if ($v['studentname'] == $info['username'] && $v['enrollmentdate'] == $info['enrollmentdate'] && $v['department'] == $info['department'] && $v['major'] == $info['major']) {
    			$flag = 1;
    			$student = $v;
    			break;
    		}else{
    			$flag = 0;
    		}
    	}
    	if ($flag) {
    		$result['studentid'] = $student['studentid'];
    		$result['userid'] = $id;
    		$result['status'] = 1;
    		$stu->allowField(true)->isUpdate(true)->save($result);
    		$to = $info['email'];
    		$from = "mianyang1994123@163.com";
    		$password = "1994123lfn";
    		$subject = '湘大校友之窗注册成功！';
    		$body = '<p>亲爱的'.$student['studentname'].'校友：</p><p>&nbsp;&nbsp;&nbsp;&nbsp;您好，欢迎加入湘大校友之窗！非常感谢您的支持，您的登录账号为'.$student['studentid'].'。</p>';
    		$student['send'] = send_mail($to,$from,$password,$subject,$body);
    		$student['res'] = 1;
    		return $student;
    	}else{
    		$to = $info['email'];
    		$from = "mianyang1994123@163.com";
    		$password = "1994123lfn";
    		$subject = '湘大校友之窗注册失败！';
    		$body = '<p>很抱歉，暂时没有找到您的档案数据，可能是我们的档案数据尚不完整，如果您确实为湘大校友，请与管理员联系</p>';
    		$student['send'] = send_mail($to,$from,$password,$subject,$body);
    		$student['res'] = 0;
    		return $student;
    	}
    }
		 
 }
?>
