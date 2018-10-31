<?php
class ImagAction extends CommonAction{
	
	public function _initialize() {
		header("Content-Type:text/html; charset=utf-8");
		$this->_inject_check(0);//调用过滤函数
		$this->_Config_name();//调用参数
		$this->_checkUser();
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
				$_SESSION['UrlPTPass'] = 'MyssGuanMangGuo';
				$bUrl = __URL__.'/adminimages';
				$this->_boxx($bUrl);
				break;
			default;
				$this->error('二级密码错误!');
				exit;
		}
	}
	
	//图片管理
	public function adminimages(){
		$this->_Admin_checkUser();
		if ($_SESSION['UrlPTPass'] == 'MyssGuanMangGuo'){
			$img = M ('img');
			$field  = '*';
			//=====================分页开始==============================================
			import ( "@.ORG.ZQPage" );  //导入分页类
			$count = $img->where($map)->count();//总页数
			$listrows = C('ONE_PAGE_RE');//每页显示的记录数
			$page_where = "";//分页条件
			$Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
			//===============(总页数,每页显示记录数,css样式 0-9)
			$show = $Page->show();//分页变量
			$this->assign('page',$show);//分页变量输出到模板
			$list = $img->where($map)->field($field)->order('id desc')->page($Page->getPage().','.$listrows)->select();
			$this->assign('list',$list);//数据输出到模板
			//=================================================

			$this->display();
			exit();
		}else{
			$this->error('错误!');
			exit;
		}
	}
	
	public function adminimagesAC(){
		//处理提交按钮
		$action = $_POST['action'];
		//获取复选框的值
		$PTid = $_POST['tabledb'];
		$fck = M ('fck');
		if (!$fck->autoCheckToken($_POST)){
			$this->error('页面过期，请刷新页面！');
			exit;
		}
		if ($action == '添加'){
			$this->adminimages_add();
			exit;
		}
		if (!isset($PTid) || empty($PTid)){
			$bUrl = __URL__.'/adminimages';
			$this->_box(1,'请选择！',$bUrl,1);
			exit;
		}
		switch ($action){
			case '删除';
				$this->_adminimagesDel($PTid);
				break;
			default;
				$bUrl = __URL__.'/adminimages';
				$this->_box(0,'没有该记录！',$bUrl,1);
				break;
		}
	}
	
	public function adminimages_add(){
		
		$this->display('adminimages_add');
	}
	
	//添加处理
	public function adminimages_addAC(){
		$this->_Admin_checkUser();
		$img = M ('img');
		$data = array();
		$title = $_POST['title'];
		$small_url = $_POST['small_url'];
		$img_url = $_POST['img_url'];
		if(empty($title)){
			$this->error('请输入完整的信息！');
		}
		if(empty($small_url)||empty($img_url)){
			$this->error('图片地址为空！');
		}
		$data['title'] = $title;
		$data['small_url'] = $small_url;
		$data['img_url'] = $img_url;
		$data['c_time'] = time();
		$rs = $img->add($data);
		if ($rs){
			$bUrl = __URL__.'/adminimages';
			$this->_box(0,'添加成功！',$bUrl,1);
			exit;
		}else{
			$this->error('添加失败！');
			exit;
		}
	}
	
	//编辑
	public function adminimages_edit(){
		$this->_Admin_checkUser();//后台权限检测
		$EDid = (int)$_GET['id'];
		$img = M ('img');
		$where = array();
		$where['id'] = $EDid;
		$rs = $img->where($where)->find();
		if ($rs){
			$this->assign('vo',$rs);
			$this->display('adminimages_edit');
		}else{
			$this->error('没有该信息！');
			exit;
		}
	}
	public function adminimages_editAc(){
		$this->_Admin_checkUser();//后台权限检测
		$img = M ('img');
		$data = array();
		$title = $_POST['title'];
		$small_url = $_POST['small_url'];
		$img_url = $_POST['img_url'];
		if(empty($title)){
			$this->error('请输入完整的信息！');
		}
		if(empty($small_url)||empty($img_url)){
			$this->error('图片地址为空！');
		}
		$data['title'] = $title;
		$data['small_url'] = $small_url;
		$data['img_url'] = $img_url;
		$data['id'] = $_POST['ID'];
		$rs = $img->save($data);
		if ($rs){
			$bUrl = __URL__.'/adminimages';
			$this->_box(1,'编辑成功！',$bUrl,1);
			exit;
		}else{
			$this->error('编辑失败！');
			exit;
		}
	}
	
	//删除
	private function _adminimagesDel($PTid){
		if ($_SESSION['UrlPTPass'] == 'MyssGuanMangGuo'){
			$img = M ('img');
			$where = array();
			$where['id'] = array ('in',$PTid);
			$rs = $img->where($where)->delete();
			if ($rs){
				$bUrl = __URL__.'/adminimages';
				$this->_box(1,'删除成功！',$bUrl,1);
				exit;
			}else{
				$bUrl = __URL__.'/adminimages';
				$this->_box(0,'删除失败！',$bUrl,1);
				exit;
			}
		}else{
			$this->error('错误!');
			exit;
		}
	}
	
	/**
     * 上传图片
     * **/
	public function upload_fengcai_img() {
		$this->_Admin_checkUser();//后台权限检测
        if(!empty($_FILES)) {
            //如果有文件上传 上传附件
            $this->_upload_fengcai_img();
        }
    }

    protected function _upload_fengcai_img()
    {
        header("content-type:text/html;charset=utf-8");
        $this->_Admin_checkUser();//后台权限检测
        // 文件上传处理函数

        //载入文件上传类
        import("@.ORG.UploadFile");
        $upload = new UploadFile();

        //设置上传文件大小
        $upload->maxSize  = 1048576 * 2 ;// TODO 50M   3M 3292200 1M 1048576

        //设置上传文件类型
        $upload->allowExts  = explode(',','jpg,gif,png,jpeg');

        //设置附件上传目录
        $upload->savePath =  './Public/Uploads/img/';

        //设置需要生成缩略图，仅对图像文件有效
       $upload->thumb =  true;

       //设置需要生成缩略图的文件前缀
        $upload->thumbPrefix   =  'b_,m_';  //生产2张缩略图

       //设置缩略图最大宽度
		$upload->thumbMaxWidth =  '10000,10000';

       //设置缩略图最大高度
        $upload->thumbMaxHeight = '2000,280';

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
            $U_inpath=(str_replace('./Public/','__PUBLIC__/',$U_path));
            $big_inpath=$U_inpath."b_".$U_nname;
            $small_inpath=$U_inpath."m_".$U_nname;

            echo "<script>window.parent.form1.img_url.value='".$big_inpath."';</script>";
            echo "<script>window.parent.form1.small_url.value='".$small_inpath."';</script>";
            echo "<span style='font-size:12px;'>上传完成！</span>";
            exit;

        }
    }
	

}
?>