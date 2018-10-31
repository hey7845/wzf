<?php
class DownAction extends CommonAction {
	function _initialize() {
		ob_clean();
		$this->_inject_check(0);//调用过滤函数
		$this->_checkUser();
		$this->_Config_name();//调用参数
		header("Content-Type:text/html; charset=utf-8");
	}
	
	public function cody(){
		//===================================二级验证
		$UrlID = (int) $_GET['c_id'];
		if (empty($UrlID)){
			$this->error('二级密码错误!');
			exit;
		}
		if(!empty($_SESSION['user_pwd2'])){
			$url = __URL__."/codys/Urlsz/$UrlID";
			$this->_boxx($url);
			exit;
		}
		$cody   =  M ('cody');
		$list	=  $cody->where("c_id=$UrlID")->field('c_id')->find();
		if ($list){
			$this->assign('vo',$list);
			$this->display('Public:cody');
			exit;
		}else{
			$this->error('二级密码错误!');
			exit;
		}
	}
	
	
	public function codys(){
		//=============================二级验证后调转页面
		$Urlsz = (int) $_POST['Urlsz'];
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
	
			$where = array();
			$where['id'] = $_SESSION[C('USER_AUTH_KEY')];
			$where['passopen'] = md5($pass);
			$list = $fck->where($where)->field('id,is_agent')->find();
			if($list == false){
				$this->error('二级密码错误!');
				exit();
			}
			$_SESSION['user_pwd2'] = 1;
		}else{
			$Urlsz = $_GET['Urlsz'];
		}
		switch ($Urlsz){
			case 1;
			$_SESSION['Urlszpass'] = 'MyssPaoYingTao';
			$bUrl = __URL__.'/frontCurrency';//
			$this->_boxx($bUrl);
			break;
	
			case 2;
			$_SESSION['Urlszpass'] = 'MyssGuanPaoYingTao';
			$bUrl = __URL__.'/zip_index';//
			$this->_boxx($bUrl);
			break;
				
			default;
			$this->error('二级密码错误!');
			exit;
		}
	}
	
	//下载
	public function zip_index(){
		$this->_Admin_checkUser();//后台权限检测
		$form = M ('item');
		$map = array();
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
// 		$map['type'] = 0;
		$field  = '*';
		//=====================分页开始==============================================
		import ( "@.ORG.ZQPage" );  //导入分页类
		$count = $form->where($map)->count();//总页数
		$listrows = C('ONE_PAGE_RE');//每页显示的记录数
		$this_where = 'title='. $title;
		$Page = new ZQPage($count, $listrows, 1, 0, 3, $this_where);
		//===============(总页数,每页显示记录数,css样式 0-9)
		$show = $Page->show();//分页变量
		$this->assign('page',$show);//分页变量输出到模板
		$list = $form->where($map)->field($field)->order('id desc')->page($Page->getPage().','.$listrows)->select();
		$this->assign('list',$list);//数据输出到模板
		//=================================================
	
		$this->display();
	}
		
	public function ZipAC(){
		$this->_Admin_checkUser();//后台权限检测
		//处理提交按钮
		$action = trim($_POST['action']);
		//获取复选框的值
		$PTid = $_POST['tabledb'];
		if ($action == '添加'){
			$this->Zip_add();
			exit;
		}
		if (!isset($PTid) || empty($PTid)){
			$bUrl = __URL__.'/zip_index';
			$this->_box(0,'请选择资料！',$bUrl,1);
			exit;
		}
		switch ($action){
			case '启用';
			$this->Zip_A($PTid);
			break;
			case '禁用';
			$this->Zip_B($PTid);
			break;
			case '删除';
			$this->Zip_C($PTid);
			break;
			default;
			$bUrl = __URL__.'/zip_index';
			$this->_box(0,'没有该资料！',$bUrl,1);
			break;
		}
	}
	
	private function Zip_A($PTid=0){
		$this->_Admin_checkUser();//后台权限检测
		$User = M ('item');
		$where['id'] = array ('in',$PTid);
		$User->where($where)->setField('status',1);
		$bUrl = __URL__.'/zip_index';
		$this->_box(1,'启用成功！',$bUrl,1);
		exit;
	}
	//禁用
	private function Zip_B($PTid=0){
		$this->_Admin_checkUser();//后台权限检测
		$User = M ('item');
		$where['id'] = array ('in',$PTid);
		$User->where($where)->setField('status',0);
		$bUrl = __URL__.'/zip_index';
		$this->_box(1,'禁用成功！',$bUrl,1);
		exit;
	}
	private function Zip_C($PTid=0){
		$this->_Admin_checkUser();//后台权限检测
		$User = M ('item');
		$where['id'] = array ('in',$PTid);
		$rs = $User->where($where)->delete();
		if ($rs){
			$bUrl = __URL__.'/zip_index';
			$this->_box(1,'删除成功！',$bUrl,1);
			exit;
		}else{
			$bUrl = __URL__.'/zip_index';
			$this->_box(0,'删除失败！',$bUrl,1);
			exit;
		}
	}
	
	public function Zip_add(){
		$this->us_fckeditor('content',"",400,"100%");
		$this->display('Zip_add');
	}
	public function Zip_add_save(){
		$this->_Admin_checkUser();//后台权限检测
		$User = M ('item');
		$data = array();
		$content = stripslashes($_POST['content']);
		$title = $_POST['title'];
		$stype = (int)$_POST['stype'];
		$fsize = (int)$_POST['fsize'];
		$image = $_POST['image'];
		$imagetitle = $_POST['imagetitle'];
		if(empty($title)||empty($content)||empty($image)||empty($imagetitle)){
			$this->error('请输入完整的信息！');
		}
	
		$data['title'] = $title;
		$data['content'] = $content;
		$data['zip_url'] = $image;
		$data['zip_title'] = $imagetitle;
		$data['user_id'] = $_POST['user_id'];
		$data['create_time'] = time();
		$data['status'] = 1;
		$data['type'] = $stype;
		$data['fsize'] = $fsize;
	
		$rs = $User->add($data);
		if (!$rs){
			$this->error('添加失败！');
			exit;
		}
		$bUrl = __URL__.'/zip_index';
		$this->_box(1,'添加成功！',$bUrl,1);
		exit;
	}
		
	public function Zip_edit(){
		$this->_Admin_checkUser();//后台权限检测
		$EDid = $_GET['EDid'];
		$User = M ('item');
		$where = array();
		$where['id'] = $EDid;
		$rs = $User->where($where)->find();
		if ($rs){
			$this->assign('vo',$rs);
			$this->us_fckeditor('content',$rs['content'],400,"100%");
			$this->display();
		}else{
			$this->error('没有该资料！');
			exit;
		}
	}
	
	public function Zip_edit_save(){
		$this->_Admin_checkUser();//后台权限检测
		$User = M ('item');
		$data = array();
	
		$content = stripslashes($_POST['content']);
		$title = $_POST['title'];
		$stype = (int)$_POST['stype'];
		$fsize = (int)$_POST['fsize'];
		$image = $_POST['image'];
		$imagetitle = $_POST['imagetitle'];
		if(empty($title)||empty($content)||empty($image)||empty($imagetitle)){
			$this->error('请输入完整的信息！');
		}
	
		$data['title'] = $title;
		$data['content'] = $content;
		$data['zip_url'] = $image;
		$data['zip_title'] = $imagetitle;
		$data['update_time'] = mktime();
		$data['type'] = $stype;
		$data['fsize'] = $fsize;
		$data['id'] = $_POST['ID'];
	
		$rs = $User->save($data);
		if (!$rs){
			$this->error('编辑失败！');
			exit;
		}
		$bUrl = __URL__.'/zip_index';
		$this->_box(1,'编辑成功！',$bUrl,1);
		exit;
	}
	
	//上传图片
	public function upload_fengcai_zip() {
		$this->_Admin_checkUser();//后台权限检测
		if(!empty($_FILES)) {
			//如果有文件上传 上传附件
			$this->_upload_fengcai_zip();
		}
	}
	//上传图片
	protected function _upload_fengcai_zip()
	{
		$this->_Admin_checkUser();//后台权限检测
		header("content-type:text/html;charset=utf-8");
		// 文件上传处理函数
	
		//载入文件上传类
		import("@.ORG.UploadFile");
		$upload = new UploadFile();
	
		//设置上传文件大小
		$upload->maxSize  = 1048576 * 50 ;//（20M） TODO 50M   3M 3292200 1M 1048576
	
		//设置上传文件类型
		$upload->allowExts  = explode(',','zip,rar,doc');
	
		//设置附件上传目录
		$upload->savePath =  './Public/Uploads/zip/';
	
		//设置需要生成缩略图，仅对图像文件有效
		$upload->thumb =  false;
	
		//设置需要生成缩略图的文件前缀
		$upload->thumbPrefix   =  'm_';  //生产2张缩略图
	
		//设置缩略图最大宽度
		$upload->thumbMaxWidth =  '210';
	
		//设置缩略图最大高度
		$upload->thumbMaxHeight = '150';
	
		//设置上传文件规则
		//		$upload->saveRule = uniqid;
		$upload->saveRule = date("Y").date("m").date("d").date("H").date("i").date("s").rand(1,100);
	
		//删除原图
		$upload->thumbRemoveOrigin = true;
	
		if(!$upload->upload()) {
			//捕获上传异常
			$error_p=$upload->getErrorMsg();
			echo "<script>alert('".$error_p."');history.back();</script>";
		}else {
			//取得成功上传的文件信息
			$uploadList = $upload->getUploadFileInfo();
			$U_path=$uploadList[0]['savepath'];
			$U_nname=$uploadList[0]['savename'];
			$U_inpath=(str_replace('./Public/','',$U_path)).$U_nname;
	
			echo "<script>window.parent.form1.image.value='".$U_inpath."';</script>";
			echo "<script>window.parent.form1.imagetitle.value='".$U_nname."';</script>";
			echo "<span style='font-size:12px;'>上传完成！</span>";
			exit;
	
		}
	}
	
	public function down_file_url(){
		ob_end_clean();
		$cid = $_GET['cid'];
	
		$item = M ('item');
		$where = array();
		$where['id'] = array('eq',$cid);
		$rs = $item->where($where)->find();
		if(!$rs){
			$this->error('文件找不到！');
			exit;
		}
		$file_all_dir = $rs['zip_url'];
		$now_path = $_SERVER["DOCUMENT_ROOT"];
		$web_path = __ROOT__.'/Public/';
		$all_path = $now_path.$web_path.$file_all_dir;
		$file_name = $rs['zip_title'];
	
		$ppp = $this->extend($file_name);
		$houzui = $ppp[0];
	
		if(!$handle=fopen($all_path,'rw')){
			$this->error('文件找不到，或文件正在使用中，无法打开缓存文件.');
			exit;
		}else{
			// 输入文件标签
			if($houzui=="rar"){
				Header("Content-type: text/plain");
			}else{
				Header("Content-type: application/octet-stream");
			}
			//			Header("Content-type: application/octet-stream");
			Header("Accept-Ranges: bytes");
			Header("Accept-Length: ".filesize($all_path));
			Header("Content-Disposition: attachment; filename=" . $file_name);
			// 输出文件内容
			echo fread($handle,filesize($all_path));
			fclose($handle);
			exit;
		}
	}
		
	//获取文件名及后缀
	public function extend($file_name)
	{
		$narray = array();
		$fname = "";
		$fexname = "";
	
		$extend =explode("." , $file_name);
		$va=count($extend)-1;
		$allva = count($extend);
		if($allva>2){
			for($i=0;$i<$allva-1;$i++){
				$tttt = $extend[$i];
				$fname .= $tttt;
			}
		}elseif($allva<2){
			$fname = "NULL";
		}else{
			$tttt = $extend[0];
			$fname = $tttt;
		}
		$fexname = $extend[$va];
		$narray[0] = $fname;
		$narray[1] = $fexname;
		return $narray;
	}
	
	public function Down(){
		$table  = M ('item');
		$b_id = (int)$_GET['b_id'];
		$where['type'] = $b_id;
		$where['status'] = 1;
		//=====================分页开始==============================================
		import ( "@.ORG.ZQPage" );  //导入分页类
		$count = $table->where($where)->count();//总页数
		$listrows = 20;//每页显示的记录数
		$page_where = '';//分页条件
		$Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
		//===============(总页数,每页显示记录数,css样式 0-9)
		$show = $Page->show();//分页变量
		$this->assign('page',$show);//分页变量输出到模板
		$list = $table->where($where)->field($field)->order('id desc')->page($Page->getPage().','.$listrows)->select();
		$this->assign('list',$list);//数据输出到模板
		//=================================================
		$this -> display('Down');
	}
	
	public function Downfile(){
		$table  = M ('item');
		$id = (int)$_GET['id'];
		$rs = $table -> find($id);
		$this -> assign('vo',$rs);
		$this -> display('Downfile');
	}

}
?>