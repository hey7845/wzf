<?php
class NewsAction extends CommonAction{
	function _initialize() {
		$this->_inject_check(0);//调用过滤函数
		$this->_checkUser();
		header("Content-Type:text/html; charset=utf-8");
	}
	
	//二级密码验证
	public function cody(){
		$UrlID = (int)$_GET['c_id'];
		if (empty($UrlID)){
			$this->error('二级密码错误!');
			exit;
		}
		if(!empty($_SESSION['user_pwd2'])){
			$url = __URL__."/codys/Urlsz/$UrlID";
			$this->_boxx($url);
			exit;
		}
		$fck   =  M ('cody');
        $list	=  $fck->where("c_id=$UrlID")->getField('c_id');
		if (!empty($list)){
			$this->assign('vo',$list);
			$this->display('Public:cody');
			exit;
		}else{
			$this->error('二级密码错误!');
			exit;
		}
	}
	//二级验证后调转页面
	public function codys(){
		$Urlsz = $_POST['Urlsz'];
		if(empty($_SESSION['user_pwd2'])){
			$pass  = $_POST['oldpassword'];
			$fck   =  M ('fck');
		    if (!$fck->autoCheckToken($_POST)){
	            $this->error('页面过期请刷新页面!');
	            exit();
	        }
			if (empty($pass)){
				$this->error('二级密码错误!');
				exit();
			}

			$where =array();
			$where['id'] = $_SESSION[C('USER_AUTH_KEY')];
			$where['passopen'] = md5($pass);
			$list = $fck->where($where)->field('id')->find();
			if($list == false){
				$this->error('二级密码错误!');
				exit();
			}
			$_SESSION['user_pwd2'] = 1;
		}else{
			$Urlsz = $_GET['Urlsz'];
		}
		switch ($Urlsz){
			case 1:
				$_SESSION['Urlszpass'] = 'Myssadminnews';
				$bUrl = __URL__.'/adminnews';
				$this->_boxx($bUrl);
				break;
			default;
				$this->error('二级密码错误!');
				break;
		}
	}
	
	//新闻管理首页
	public function adminnews(){
		$this->_Admin_checkUser();//后台权限检测
// 		if ($_SESSION['Urlszpass'] == 'Myssadminnews'){
			$form = M ('form');
			$title = trim($_REQUEST['title']);
			if (!empty($title)){
				import ( "@.ORG.KuoZhan" );  //导入扩展类
				$KuoZhan = new KuoZhan();
				if ($KuoZhan->is_utf8($title) == false){
					$title = iconv('GB2312','UTF-8',$title);
				}
				unset($KuoZhan);
				$map['title'] = array('like',"%".$title."%");
				$title = urlencode($title);
			}
	
	        $field  = '*';
	        //=====================分页开始==============================================
	        import ( "@.ORG.ZQPage" );  //导入分页类
	        $count = $form->where($map)->count();//总页数
	   		$listrows = C('ONE_PAGE_RE');//每页显示的记录数
			$this_where = 'title='. $title;
	        $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
	        //===============(总页数,每页显示记录数,css样式 0-9)
	        $show = $Page->show();//分页变量
	        $this->assign('page',$show);//分页变量输出到模板
	        $list = $form->where($map)->field($field)->order('baile desc,create_time desc,id desc')->page($Page->getPage().','.$listrows)->select();
	        $this->assign('list',$list);//数据输出到模板
	        //=================================================
			$this->display();
// 		}else{
// 			$this->error ('错误!');
// 			exit;
// 		}
	}

	public function NewsAC(){
		$this->_Admin_checkUser();//后台权限检测
		//处理提交按钮
		$action = trim($_POST['action']);

		//获取复选框的值
		$PTid = $_POST['tabledb'];
		if ($action == '添加新闻'){
			$nowtime = date("Y-m-d H:i:s");
			$this->assign('nowtime',$nowtime);
			$this->us_fckeditor('content',"",400,"100%");
		
			$this->News_add();
			exit;
		}
		if (!isset($PTid) || empty($PTid)){
			$bUrl = __URL__.'/adminnews';
			$this->_box(0,'请选择新闻！',$bUrl,1);
			exit;
		}
		switch ($action){
			case '启用';
				$this->News_Open($PTid);
				break;
			case '禁用';
				$this->News_Stop($PTid);
				break;
			case '删除';
				$this->News_Del($PTid);
				break;
			case '设置置顶';
				$this->News_Top($PTid);
				break;
			case '取消置顶';
				$this->News_NoTop($PTid);
				break;
		default;
			$bUrl = __URL__.'/adminnews';
			$this->_box(0,'没有该新闻！',$bUrl,1);
			break;
		}
	}
	public function News_add(){
		$this->display('News_add');
	}
	public function News_add_save(){
		$User = M ('form');
		$data = array();

		$content = stripslashes($_POST['content']);
		$title = $_POST['title'];
		$addtime = $_POST['addtime'];
		$ttime = strtotime($addtime);
		if($ttime==0){
			$ttime = mktime();
		}
		if(empty($title) or empty($content)){
			$this->error('请输入完整的信息！');
		}
		//dump($_POST['select']);exit;
		$data['title'] = $title;
		$data['content'] = $content;
		$data['user_id'] = $_POST['user_id'];
		$data['create_time'] = $ttime;
		$data['status'] = 1;
		$data['type'] = $_POST['type'];

		$rs = $User->add($data);
		if (!$rs){
			$this->error('添加新闻2');
			exit;
		}
		$bUrl = __URL__.'/adminnews';
		$this->_box(0,'添加新闻！',$bUrl,1);
		exit;
	}
	//启用
	private function News_Open($PTid=0){
		$User = M ('form');
		$where['id'] = array ('in',$PTid);
		$User->where($where)->setField('status',1);
		$bUrl = __URL__.'/adminnews';
		$this->_box(1,'启用成功！',$bUrl,1);
		exit;
	}
	//禁用
	private function News_Stop($PTid=0){
		$User = M ('form');
		$where['id'] = array ('in',$PTid);
		$User->where($where)->setField('status',0);
		$bUrl = __URL__.'/adminnews';
		$this->_box(1,'禁用成功！',$bUrl,1);
		exit;
	}
	//删除
	private function News_Del($PTid=0){
		$User = M ('form');
		$where['id'] = array ('in',$PTid);
		$rs = $User->where($where)->delete();
		if ($rs){
			$bUrl = __URL__.'/adminnews';
			$this->_box(1,'删除成功！',$bUrl,1);
			exit;
		}else{
			$bUrl = __URL__.'/adminnews';
			$this->_box(0,'删除失败！',$bUrl,1);
			exit;
		}
	}
	//置顶
	private function News_Top($PTid=0){
		$User = M ('form');
		$where['id'] = array ('in',$PTid);
		$User->where($where)->setField('baile',1);
		$bUrl = __URL__.'/adminnews';
		$this->_box(1,'置顶成功！',$bUrl,1);
		exit;
	}
	//取消置顶
	private function News_NoTop($PTid=0){
		$User = M ('form');
		$where['id'] = array ('in',$PTid);
		$User->where($where)->setField('baile',0);
		$bUrl = __URL__.'/adminnews';
		$this->_box(1,'公告取消置顶成功！',$bUrl,1);
		exit;
	}
	
	//编辑
	public function News_edit(){
		$this->_Admin_checkUser();//后台权限检测
		$EDid = $_GET['EDid'];
		$User = M ('form');
		$l_type = (int)$_GET['l_type'];
		$this->assign('l_type',$l_type);
		if($l_type==1){
			$field = 'id,e_title as title,e_content as content,user_id,create_time,update_time,status,baile,type';
		}else{
			$field = '*';
		}
		$where = array();
		$where['id'] = $EDid;
		$rs = $User->where($where)->field($field)->find();
		if ($rs){
			$this->assign('vo',$rs);
			$this->us_fckeditor('content',$rs['content'],400,"100%");
			$this->display('News_edit');
		}else{
			$this->error('没有该新闻！');
			exit;
		}
	}
	public function News_editAc(){
		$this->_Admin_checkUser();//后台权限检测
		$User = M ('form');
		$data = array();
		//h 函数转换成安全html
		$content = stripslashes($_POST['content']);
		$title = $_POST['title'];
		$type = $_POST['type'];
		$addtime = $_POST['addtime'];
		$l_type = (int)$_POST['l_type'];
		$ttime = strtotime($addtime);
		if($ttime==0){
			$ttime = mktime();
		}
		if($l_type==1){
			$data['e_title'] = $title;
			$data['e_content'] = $content;
		}else{
			$data['title'] = $title;
			$data['content'] = $content;
		}
		$data['type'] =$type;
		//$data['user_id'] = $_POST['user_id'];
		$data['create_time'] = $ttime;
		$data['update_time'] = mktime();
		$data['status'] = 1;
		$data['id'] = $_POST['ID'];

		//dump($data);
		//exit;
		$rs = $User->save($data);
		if (!$rs){
			$this->error('编辑失败！');
			exit;
		}
		$bUrl = __URL__.'/adminnews';
		$this->_box(1,'编辑新闻！',$bUrl,1);
		exit;
	}

	public function News_Class(){

	}
	
	//前台新闻
	public function News() {
		$map = array();
		$map['status'] = 1;
		$form = M ('form');
        $field  = '*';
        //=====================分页开始==============================================
        import ( "@.ORG.ZQPage" );  //导入分页类
        $count = $form->where($map)->count();//总页数
     	$listrows = C('ONE_PAGE_RE');//每页显示的记录数
     	$listrows = 20;//每页显示的记录数
        $Page = new ZQPage($count,$listrows,1);
        //===============(总页数,每页显示记录数,css样式 0-9)
        $show = $Page->show();//分页变量
        $this->assign('page',$show);//分页变量输出到模板
        $list = $form->where($map)->field($field)->order('baile desc,id desc')->page($Page->getPage().','.$listrows)->select();
        $this->assign('list',$list);//数据输出到模板
        //=================================================
        $this->display();
    }

	//查询返回一条记录
	public function News_show() {
		$model = M ('Form');
		$id = (int) $_GET['NewID'];
		$where = array();
		$where['id'] = $id;
		$where['status'] = 1;
		$vo = $model->where($where)->find();
		$vo['content'] = stripslashes($vo['content']);//去掉反斜杠
		$this->assign ( 'vo', $vo );
		
//		sleep(5);
		
		$this->display ();
	}

}
?>