<?php
class UserAction extends CommonAction{
	
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
			$_SESSION['Urlszpass'] = 'MyssHuoLongGuo';
			$bUrl = __URL__.'/relations';
			$this->_boxx($bUrl);
			break;
			case 2;
			$_SESSION['Urlszpass'] = 'Myssmemberx';
			$bUrl = __URL__.'/member_x';
			$this->_boxx($bUrl);
			break;
			case 3;
			$_SESSION['Urlszpass'] = 'Myssmemberz';
			$bUrl = __URL__.'/member_z';
			$this->_boxx($bUrl);
			break;
			case 10;
			$_SESSION['UrlPTPass'] = 'admintongji';
			$bUrl = __URL__.'/adminalltongji';
			$this->_boxx($bUrl);
			break;
			case 4;
			$_SESSION['UrlPTPass'] = 'MyssShuiPuTao';
			$bUrl = __URL__.'/menber';
			$this->_boxx($bUrl);
			break;
			
			default;
			$this->error('二级密码错误!');
			exit;
		}
	}
	
	//推荐表
	public function relations($Urlsz=0){
		//推荐关系
		if ($_SESSION['Urlszpass'] == 'MyssHuoLongGuo'){
			$fck = M('fck');
			$UserID = $_REQUEST['UserID'];
			if (!empty($UserID)){
				$map['user_id'] = array('like',"%".$UserID."%");
			}
			$map['re_id'] = $_SESSION[C('USER_AUTH_KEY')];
			$map['is_pay'] = 1;

            $field  = '*';
            //=====================分页开始==============================================
            import ( "@.ORG.ZQPage" );  //导入分页类
            $count = $fck->where($map)->count();//总页数
    	    $listrows = C('ONE_PAGE_RE');//每页显示的记录数
			$page_where = 'UserID='.$UserID;//分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            //===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show();//分页变量
            $this->assign('page',$show);//分页变量输出到模板
            $list = $fck->where($map)->field($field)->order('pdt desc')->page($Page->getPage().','.$listrows)->select();
            $HYJJ = '';
            $this->_levelConfirm($HYJJ,1);
            $this->assign('voo',$HYJJ);//会员级别
            $this->assign('list',$list);//数据输出到模板
            //=================================================

			$this->display ('relations');
			return;
		}else{
			$this->error('数据错误2!');
			exit;
		}
	}
	
	//前后5人
	public function member_x(){
		if ($_SESSION['Urlszpass'] == 'Myssmemberx'){
			$fck = M('fck');
			$id = $_SESSION[C('USER_AUTH_KEY')];
			$myrs = $fck->where('id='.$id)->field('id,user_id,n_pai')->find();
			$n_pai = $myrs['n_pai'];
			
			$field  = 'id,user_id,n_pai,pdt,user_tel,qq';
			//前面5个
    		$wherea = "is_pay>0 and n_pai<".$n_pai;
            $alist = $fck->where($wherea)->field($field)->order('n_pai desc')->limit(5)->select();
            $this->assign('alist',$alist);
            
            //后5个
    		$whereb = "is_pay>0 and n_pai>".$n_pai;
            $blist = $fck->where($whereb)->field($field)->order('n_pai asc')->limit(5)->select();
            $this->assign('blist',$blist);
//            dump($blist);exit;

			$this->display ('member_x');
			return;
		}else{
			$this->error('数据错误!');
			exit;
		}
	}
	
	//一线排网
	public function member_z(){
		if ($_SESSION['Urlszpass'] == 'Myssmemberz'){
			$fck = M('fck');
			$id = $_SESSION[C('USER_AUTH_KEY')];
			$myrs = $fck->where('id='.$id)->field('id,user_id,x_pai')->find();
			$x_pai = $myrs['x_pai'];
			
			$field  = 'id,user_id,x_pai,pdt,user_tel,qq,x_num,x_out';

    		$wherea = "is_pay>0 and x_pai>=".$x_pai;
    		//=====================分页开始==============================================
            import ( "@.ORG.ZQPage" );  //导入分页类
            $count = $fck->where($wherea)->count();//总页数
       		$listrows = 20;//每页显示的记录数
            $page_where = '';//分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            //===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show();//分页变量
            $this->assign('page',$show);//分页变量输出到模板
            $list = $fck->where($wherea)->field($field)->order('x_pai asc,id asc')->page($Page->getPage().','.$listrows)->select();
            $this->assign('list',$list);//数据输出到模板
            //=================================================
            
            $nn = $fck->where("is_pay>0 and x_pai<".$x_pai." and x_out=0")->count();
            $this->assign('nn',$nn);

			$this->display ('member_z');
			return;
		}else{
			$this->error('数据错误!');
			exit;
		}
	}
	
	//统计
	public function adminalltongji(){
		$this->_Admin_checkUser();
		$fck = M ('fck');
		$msg = M ('msg');
		$chongzhi = M ('chongzhi');
		$tiqu = M ('tiqu');
		$tiqu = M ('tiqu');
		
		$now_day = strtotime(date("Y-m-d"));
		$end_day = $now_day+3600*24;
		
		$yes_day = $now_day-3600*24;
		
		$yes_c = $fck->where('is_pay>0 and pdt>='.$yes_day.' and pdt<'.$now_day)->count();//昨日新进
		$day_c = $fck->where('is_pay>0 and pdt>='.$now_day.' and pdt<'.$end_day)->count();//今日新进
		$not_c = $fck->where('is_pay=0')->count();//未开通
		$msg_c = $msg->where("s_uid=1 and s_read=0")->count(); //总记录数
		$chz_c = $chongzhi->where("is_pay=0")->count(); //充值
		$tix_c = $tiqu->where("is_pay=0")->count(); //提现
		$bad_c = $fck->where("is_agent=1 and is_pay>0")->count(); //报单中心
		
		$this->assign('yes_c',$yes_c);
		$this->assign('day_c',$day_c);
		$this->assign('not_c',$not_c);
		$this->assign('msg_c',$msg_c);
		$this->assign('chz_c',$chz_c);
		$this->assign('tix_c',$tix_c);
		$this->assign('upl_c',0);
		$this->assign('bad_c',$bad_c);
		$this->assign('did_c',0);
		
		$this->display();
	}
	
	
	//未开通会员
	public function menber($Urlsz=0){
		//列表过滤器，生成查询Map对象
		if ($_SESSION['UrlPTPass'] == 'MyssShuiPuTao'){
			$fck = M('fck');
			$map = array();
			$id = $_SESSION[C('USER_AUTH_KEY')];
			
			$rsss = $fck->where('id='.$id)->field('is_zy')->find();
// 			$gid = (int) $_GET['bj_id'];
			// 			$map['shop_id'] = $id;
// 			$UserID = $_POST['UserID'];
// 			if (!empty($UserID)){
// 				import ( "@.ORG.KuoZhan" );  //导入扩展类
// 				$KuoZhan = new KuoZhan();
// 				if ($KuoZhan->is_utf8($UserID) == false){
// 					$UserID = iconv('GB2312','UTF-8',$UserID);
// 				}
// 				unset($KuoZhan);
// 				$where['nickname'] = array('like',"%".$UserID."%");
// 				$where['user_id'] = array('like',"%".$UserID."%");
// 				$where['_logic']    = 'or';
// 				$map['_complex']    = $where;
// 				$UserID = urlencode($UserID);
// 			}
			$map['is_pay'] = array('eq',1);
			$map['_string'] = "is_zy=".$id." or is_zy=".$rsss['is_zy'];
	
			//查询字段
			$field  = '*';
			//=====================分页开始==============================================
			import ( "@.ORG.ZQPage" );  //导入分页类
			$count = $fck->where($map)->count();//总页数
			$listrows = C('ONE_PAGE_RE');//每页显示的记录数
			$page_where = '';//分页条件
			$Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
			//===============(总页数,每页显示记录数,css样式 0-9)
			$show = $Page->show();//分页变量
			$this->assign('page',$show);//分页变量输出到模板
			$list = $fck->where($map)->field($field)->order('is_pay asc,pdt desc')->page($Page->getPage().','.$listrows)->select();
			$this->assign('list',$list);//数据输出到模板
			//=================================================
	
			$HYJJ = '';
			$this->_levelConfirm($HYJJ,1);
			$this->assign('voo',$HYJJ);//会员级别
			$where = array();
			$where['id'] = $id;
			$fck_rs = $fck->where($where)->field('*')->find();
			$this->assign('frs',$fck_rs);//注册币
			$this->display ('menber');
			exit;
		}else{
			$this->error('数据错误!');
			exit;
		}
	}
	

}
?>