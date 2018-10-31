<?php
class PhonecardAction extends CommonAction{

    function _initialize() {
		$this->_inject_check(0); //调用过滤函数
		$this->_checkUser();
		header("Content-Type:text/html; charset=utf-8");
	}

    //二级验证
    public function Cody(){
        //$this->_checkUser();
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
		$list   =  $cody->where("c_id=$UrlID")->field('c_id')->find();
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
	public function Codys() {
		$Urlsz = $_POST['Urlsz'];
		if(empty($_SESSION['user_pwd2'])){
			$pass = $_POST['oldpassword'];
			$fck = M('fck');
			if (!$fck->autoCheckToken($_POST)) {
				$this->error('页面过期请刷新页面!');
				exit ();
			}
			if (empty ($pass)) {
				$this->error('二级密码错误!');
				exit ();
			}
			$where = array ();
			$where['id'] = $_SESSION[C('USER_AUTH_KEY')];
			$where['passopen'] = md5($pass);
			$list = $fck->where($where)->field('id')->find();
			if ($list == false) {
				$this->error('二级密码错误!');
				exit ();
			}
			$_SESSION['user_pwd2'] = 1;
		}else{
			$Urlsz = $_GET['Urlsz'];
		}
		switch ($Urlsz) {
			case 1;
				$_SESSION['UrlszUserpass'] = 'MyssGuanChanPin';
				$bUrl = __URL__ . '/card_index';
				$this->_boxx($bUrl);
				break;
			case 2;
				$_SESSION['UrlszUserpass'] = 'manlian';//求购股票
				$bUrl = __URL__ . '/card_list';
				$this->_boxx($bUrl);
				break;
			default;
				$this->error('二级密码错误!');
				break;
		}
	}

	//赠送信息
	public function card_list() {
		$card = M('card');
		$fck = M('fck');
		
		$id = $_SESSION[C('USER_AUTH_KEY')];
		$map = array();
		$map['bid'] = array('eq',$id);
		$map['is_sell'] = array('eq',1);
		
		//=====================分页开始==============================================
		import ( "@.ORG.ZQPage" );  //导入分页类
		$count = $card->where($map)->count();//总页数
		$listrows = C('ONE_PAGE_RE');//每页显示的记录数
		$Page = new ZQPage($count, $listrows, 1, 0, 3);
		//===============(总页数,每页显示记录数,css样式 0-9)
		$show = $Page->show();//分页变量
		$this->assign('page',$show);//分页变量输出到模板
		$field = '*';
		$list = $card->where($map)->field($field)->order('b_time desc')->page($Page->getPage().','.$listrows)->select();
		$this->assign('list', $list);
		
		$this->display();
	}
	
	//表查询
	public function card_index(){
		$this->_Admin_checkUser();
		if ($_SESSION['UrlszUserpass'] == 'MyssGuanChanPin'){
			$card = M('card');
			$title = $_REQUEST['stitle'];
			$map = array();
			if(strlen($title)>0){
				$map['card_no'] = array('like','%'. $title .'%');
			}
			$orderBy = 'id desc';
			$field  = '*';
	        //=====================分页开始==============================================
	        import ( "@.ORG.ZQPage" );  //导入分页类
	        $count = $card->where($map)->count();//总页数
	   		$listrows = C('ONE_PAGE_RE');//每页显示的记录数
	   		$listrows = 20;//每页显示的记录数
	        $page_where = 'stitle=' . $title ;//分页条件
	        $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
	        //===============(总页数,每页显示记录数,css样式 0-9)
	        $show = $Page->show();//分页变量
	        $this->assign('page',$show);//分页变量输出到模板
	        $list = $card->where($map)->field($field)->order($orderBy)->page($Page->getPage().','.$listrows)->select();
	        $this->assign('list',$list);//数据输出到模板
	        //=================================================

	        $this->display();
		}else{
            $this->error('错误!');
        }
	}

	//表显示修改
	public function card_edit(){
		$this->_Admin_checkUser();
		$EDid = $_GET['EDid'];
		$field = '*';
		$card = M ('card');
		$where = array();
		$where['id'] = $EDid;
		$rs = $card->where($where)->field($field)->find();
		if ($rs){
			$this->assign('rs',$rs);
//			$this->us_fckeditor('content',$rs['content'],400,"96%");
			$this->display();
		}else{
			$this->error('没有该信息！');
			exit;
		}
	}

	//表修改保存
	public function card_edit_save(){
		$this->_Admin_checkUser();
		$card = M ('card');
		$data = array();
		$card_no = trim($_POST['card_no']);
		$card_pw = trim($_POST['card_pw']);
		$cid = (int)$_POST['id'];
		if (empty($card_no)){
			$this->error('电话卡号不能为空!');
			exit;
		}
		if (empty($card_pw)){
			$this->error('电话卡密码不能为空!');
			exit;
		}
		$cff = $this->check_cf_no($card_no,$cid);
		if($cff==1){
			$this->error('电话卡号重复!');
			exit;
		}
		$data['card_no'] = $card_no;
		$data['card_pw'] = $card_pw;
		$data['id'] = $cid;
		$rs = $card->save($data);
		if (!$rs){
			$this->error('编辑失败！');
			exit;
		}
		$bUrl = __URL__.'/card_index';
		$this->_box(1,'操作成功',$bUrl,1);
		exit;
	}

	//产品表操作（启用禁用删除）
	public function card_zz(){
		$this->_Admin_checkUser();
		//处理提交按钮
		$action = $_POST['action'];
		//获取复选框的值
		$PTid = $_POST["checkbox"];
		if ($action=='添加'){
//			$this->us_fckeditor('content',"",400,"96%");
			$this->display('card_add');
			exit;
		}
		if ($action=='补发给会员'){
			$this->bf_card();
			exit;
		}
		$card = M ('card');
		switch ($action){
			case '删除';
				$wherea=array();
				$wherea['id'] = array('in',$PTid);
				$rs = $card->where($wherea)->delete();
				if ($rs){
					$bUrl = __URL__.'/card_index';
					$this->_box(1,'操作成功',$bUrl,1);
					exit;
				}else{
					$bUrl = __URL__.'/card_index';
					$this->_box(0,'操作失败',$bUrl,1);
				}
				break;
			default;
				$bUrl = __URL__.'/card_index';
				$this->_box(0,'操作失败',$bUrl,1);
				break;
		}
	}

	//表添加保存
	public function card_inserts(){
		$this->_Admin_checkUser();
		$card = M('card');
		$data = array();
		$card_no = trim($_POST['card_no']);
		$card_pw = trim($_POST['card_pw']);
		$money = trim($_POST['money']);
		$is_use = (int)(trim($_POST['is_use']));
		if (empty($card_no)){
			$this->error('电话卡号不能为空!');
			exit;
		}
		if (empty($card_pw)){
			$this->error('电话卡密码不能为空!');
			exit;
		}
		if (empty($money) || !is_numeric($money)){
			$this->error('金额不能为空!');
			exit;
		}
		if (strlen($money) > 12){
			$this->error ('金额太大!');
			exit;
		}
		if ($money <= 0){
			$this->error ('金额输入不正确!');
			exit;
		}
		if ($is_use < 0){
			$this->error ('有效期输入不正确!');
			exit;
		}
		$cff = $this->check_cf_no($card_no,0);
		if($cff==1){
			$this->error('电话卡号重复!');
			exit;
		}
		
		$data['card_no'] = $card_no;
		$data['card_pw'] = $card_pw;
		$data['money'] = $money;
		$data['is_use'] = $is_use;
		$data['c_time'] = time();
		$data['f_time']=time();
		$form_rs = $card->add($data);
		if (!$form_rs){
			$this->error('添加失败');
			exit;
		}
		$bUrl = __URL__.'/card_index';
		$this->_box(1,'操作成功',$bUrl,1);
		exit;
	}
	
	//补发给会员
	public function bf_card(){
		if ($_SESSION['UrlszUserpass'] == 'MyssGuanChanPin'){
			$card = M('card');
			$map = array();
			$map['is_sell'] = array('eq',0);
	        $count = $card->where($map)->count();//总页数
			$this->assign('cardc',$count);
	        $this->display('bf_card');
		}else{
            $this->error('错误!');
        }
	}
	
	//补发给会员处理
	public function bf_cardAC(){
		if ($_SESSION['UrlszUserpass'] == 'MyssGuanChanPin'){
			$fck = M ('fck');
			$user_id = trim($_POST['userid']);
			$numb = trim($_POST['numb']);
			if (empty($user_id)){
				$this->error('会员编号不能为空!');
				exit;
			}
			if (empty($numb) || !is_numeric($numb)){
				$this->error('数量不能为空!');
				exit;
			}
			if ($numb<=0){
				$this->error ('数量不对!');
				exit;
			}
			
			$where = array();
			$where['user_id'] = array('eq',$user_id);
			$where['is_pay'] = array('gt',0);
			$rs = $fck->where($where)->field('id,user_id')->find();
			if(!$rs){
				$this->error('会员不存在，或未开通！');
				exit;
			}
			$myid = $rs['id'];
			$inUserID = $rs['user_id'];
			
			//发放卡号
			$res = $this->fafang_card($myid,$inUserID,$numb);
			
			unset($fck,$where,$rs);
			$bUrl = __URL__.'/card_index';
			$this->_box(1,'成功补发'.$res.'张电话卡！',$bUrl,3);
		}else{
            $this->error('错误!');
        }
	}
	
	public function check_cf_no($cno,$cid=0){
		$card = M('card');
		$where = array();
		$where['card_no'] = array('eq',$cno);
		$where['id'] = array('neq',$cid);
		$crs = $card->where($where)->find();
		if($crs){
			$res = 1;
		}else{
			$res = 0;
		}
		unset($card,$where,$crs);
		return $res;
	}
	
	//发放卡号
	public function fafang_card($fid,$fuserid,$cx_n=0,$cid=0){
		$card = M('card');
		$result = 0;
		$i=0;
		if($cid==0){
			if($cx_n>0){
				$lirs = $card->where('is_sell=0')->order("RAND()")->limit($cx_n)->select();
				foreach($lirs as $lrs){
					$lid = $lrs['id'];
					$data = array();
					$data['id'] = $lid;
					$data['bid'] = $fid;
					$data['buser_id'] = $fuserid;
					$data['is_sell'] = 1;
					$data['b_time'] = time();
					$card->save($data);
					unset($data);
					$i++;
				}
				unset($lirs,$lrs);
			}
		}else{
			$where = array();
			$where['id'] = array('eq',$cid);
			$where['is_sell'] = array('eq',0);
			$frs = $card->where($where)->find();
			if($frs){
				$lid = $frs['id'];
				$data = array();
				$data['id'] = $lid;
				$data['bid'] = $fid;
				$data['buser_id'] = $fuserid;
				$data['is_sell'] = 1;
				$data['b_time'] = time();
				$card->save($data);
				unset($data);
				$i=1;
			}
			unset($frs,$where);
		}
		unset($card);
		$result = $i;
		return $result;
	}
	
	public function upload_excel(){
		header("content-type:text/html;charset=utf-8");
			//载入文件上传类
			import("@.ORG.UploadFile");
			$upload = new UploadFile();
		
			//设置上传文件大小
			$upload->maxSize  = 1048576 * 2 ;// TODO 50M   3M 3292200 1M 1048576
		
			//设置上传文件类型
			$upload->allowExts  = explode(',','xls');
		
			//设置附件上传目录
			$upload->savePath =  './Public/Uploads/excel/';
		
		
			//设置上传文件规则
			//		$upload->saveRule = uniqid;
			$upload->saveRule = 'filename';
		
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
				$U_inpath=(str_replace('./Public/','__PUBLIC__/',$U_path)).$U_nname;
		
//				echo "<script>window.parent.form1.image.value='".$U_inpath."';</script>";
// 				echo "<span style='font-size:12px;'>上传完成！</span>";
				$this->importExcel();
				exit;
		
			}
		
		
	}
	
	public function importExcel(){
		$card=M('card');
// 		header("content-type:text/html;charset=utf-8");
		@include("phpExcelReader/Excel/reader.php");
		
		$xl_reader = new Spreadsheet_Excel_Reader ( );
		$xl_reader->setOutputEncoding('utf-8'); //GBK或者GB2312
		$xl_reader->read("./Public/Uploads/excel/filename.xls");
		
		$data=  $xl_reader->sheets[0]['cells'];
	
		if ($data){
		
			set_time_limit(0);
		$i=0;
		$ok=0;
		$nok=0;
		foreach ($data as $vo){
			if ($i>0){
				$card_data['card_no']=$vo[1];
				$cff = $this->check_cf_no($vo[1],0);
				if($cff==1){
					$nok++;
					$i++;
					continue;
				}
				$card_data['card_pw']=$vo[2];
				$card_data['money']=$vo[3];
				if ($vo[4]=='是'){
					$is_sell=1;
				}else{
					$is_sell=0;
				}
				$c_time=explode('/', $vo[5]);
				$c_time_d = $c_time[0]-1;//excel 计算方式多一天
				$c_date=strtotime(date(''.$c_time[2].'-'.$c_time[1].'-'.$c_time_d.''));
				$b_time=explode('/', $vo[6]);
				$b_time_d = $b_time[0]-1;
				$b_date=strtotime(date(''.$b_time[2].'-'.$b_time[1].'-'.$b_time_d.''));
				$card_data['is_sell']=$is_sell;
				$card_data['c_time']=time();
				$card_data['f_time']=$c_date;
				$card_data['l_time']=$b_date;
				$card_data['is_use']=$vo[7];
				
				$card->add($card_data);
				$ok++;
			}
			$i++;
		}
		$aurls='./Public/Uploads/excel/filename.xls';
		
		if(file_exists($aurls)){
			unlink($aurls);
		}
		
		$bUrl = __URL__.'/card_index';
		$this->_box(1,'成功导入'.$ok.'条记录，'.$nok.'条卡号存在重复！',$bUrl,1);
		
		}
	}

}
?>