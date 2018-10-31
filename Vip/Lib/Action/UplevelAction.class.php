<?php
class UplevelAction extends CommonAction{
	
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
			$_SESSION['Urlszpass'] = 'Myssjinji';
			$bUrl = __URL__.'/MenberJinji';//会员晋级
			$this->_boxx($bUrl);
			break;
			case 2;
			$_SESSION['Urlszpass'] = 'Myssadminjinji';
			$bUrl = __URL__.'/adminmemberJJ';//后台充值管理
			$this->_boxx($bUrl);
			break;
			default;
			$this->error('二级密码错误!');
			exit;
		}
	}
	
	//前台会员晋级
	public function MenberJinji(){
		if ($_SESSION['Urlszpass'] == 'Myssjinji'){
			$where = array();
			$fck = M('fck');

	    	$uid = $_SESSION[C('USER_AUTH_KEY')];
			$frs = $fck->find($uid);
			$voo = 0;
			$this->_levelConfirm($voo);

			$level = array();
			for($i=1;$i<=count($voo) ;$i++){
				$level[$i] = $voo[$i];
			}
			$this->assign('level',$level);

			$fee = M ('fee');
			$fee_rs =$fee->field('s1,s2,s3,s4,s5,s9')->find();
			$s1 =explode('|',$fee_rs['s1']);
			$s2 =explode('|',$fee_rs['s2']);
			$s3 =explode('|',$fee_rs['s3']);
			// 投资金额基数
			$s9 =explode('|',$fee_rs['s9']);
			// 领导奖比例
			$s4 =$fee_rs['s4'];
// 			$s9=array('普通会员','铜级','银级','金级','蓝宝石','绿宝石','红宝石','珍珠','钻石','双钻石','黑钻石','皇冠');
			$s9=array('800','1600','2400','3200','4000','4800','5600','6400','7200','8000'
			    ,'8800','9600','10400','11200','12000','12800','13600','14400','15200','16000'
			    ,'16800','17600','18400','19200','20000','20800','21600','22400','23200','24000'
			    ,'24800','25600','26400','27200','28000','28800','29600','30400','31200','32000',
			    '32800','33600','34400','35200','36000','36800','37600','38400' ,'39200','40000');
			$this->assign('sx1',$s9);

			$promo = M('promo');
			$field  = '*';
			$map['uid'] = $uid;
            $list = $promo->where($map)->field($field)->order('id desc')->select();
            $this->assign('list',$list);//数据输出到模板
            //=================================================

			$this->assign('s4',$s4);
			// $this->assign('le',$voo);
			// $this->assign('level',$level);
			$this->assign('frs',$frs);//数据输出到模板
			$this->display();
		}else{
			$this->error('错误！');
			exit;
		}
	}

	//前台晋级处理
	public function MenberJinjiConfirm(){
		if ($_SESSION['Urlszpass'] == 'Myssjinji'){
		    
		    date_default_timezone_set('asia/shanghai');
		    $week = date('w');
		    $day = date('md');
		    $time = date('G');
		    if($week==0 || $week==6) {
		        $status = 2;
		    } else if($time>=8 && $time <= 21) {
		        $status = 0;
		    } else {
		        $status = 1;
		    }
		    if($status!=0){
		        $this->error('只能在8时至21时升级会员，节假日及休息时间不能升级会员！');
		        exit;
		    }
			// 加单金额
			$jiadanMoney = $_REQUEST['cpzj'];
            // 当前用户ID
			$uid = $_SESSION[C('USER_AUTH_KEY')];
			$where['id'] = $uid;
			$promo = M('promo');
			$promo_rs = $promo->where("uid={$uid} and  is_pay=0")->find();
			if ($promo_rs) {
			    $this->error('您有还未确认的升级请求，请等待确认之后再行申请！');
			    exit;
			}
			$fck = D('Fck');
			// 根据会员ID查询会员表数据
			$fck_rs = $fck->where($where)->find();
            // 临时会员不能升级
			if($fck_rs['is_pay'] == 0){
				$this->error('您是临时会员不能申请晋级，请先开通！');
				exit;
			}
			// 查询参数设置数据
			$fee = M ('fee');
			$fee_rs =$fee->field('s1,s2,s3,s4,s5,s9,s13')->find();
			// 每单注册金额
			$s9 =explode('|',$fee_rs['s9']);
			//单量
			$danshu = bcdiv($jiadanMoney ,$s9[0]);
 			if($fck_rs['cpzj'] >= 40000){
				$this->error('已经是最高级，无法再升级！');
			}
	        // 写入帐号数据
	        $data = array();
	        $data['uid'] = $uid;
	        $data['user_id'] = $fck_rs['user_id'];
	        $data['money'] = $jiadanMoney;//补差额
	        $data['u_level'] = $fck_rs['cpzj'];//旧的
	        // 升级后金额
	        $up_level = $jiadanMoney + $fck_rs['cpzj'];
	        $data['up_level'] = $up_level;//新的
	        $data['create_time'] = time();
	        $data['pdt'] = time();
	        $data['danshu'] = $danshu;
	        $data['is_pay']	= 0;
	        $data['user_name'] = "<font color=red>前台晋级</font>";;
	        $data['u_bank_name'] = $fck_rs['bank_name'];
	        $data['type'] = 0;
	        
	        $result = $promo->add($data);
	        unset($data);
	        if($result) {
	           $bUrl = __URL__.'/MenberJinji';
	           $this->_box(1,'您申请成功，请耐心等待管理员审核！',$bUrl,3);
	        } else {
	            $this->error('申请晋级失败！');
	            exit;
	        }
	        
		}else{
			$this->error('错误！');
			exit;
		}
	}

	public function MenberJinjishow(){
		//查看详细信息
		$promo = M('promo');
		$ID = (int) $_GET['Sid'];
		$where = array();
		$where['id'] = $ID;
		$srs = $promo->where($where)->field('user_name')->find();
		$this->assign('srs',$srs);
		unset($promo,$where,$srs);
		$this->display ('MenberJinjishow');
	}
	
	//会员晋级管理
	public function adminmemberJJ($GPid=0){
		$this->_Admin_checkUser();
		if ($_SESSION['Urlszpass'] == 'Myssadminjinji'){
			$fck = M('fck');
			$UserID = $_REQUEST['UserID'];
			$u_sd = $_REQUEST['u_sd'];
			$uulv = (int)$_REQUEST['ulevel'];
			$ss_type = (int) $_REQUEST['type'];
			if (!empty($UserID)){
				import ( "@.ORG.KuoZhan" );  //导入扩展类
                $KuoZhan = new KuoZhan();
                if ($KuoZhan->is_utf8($UserID) == false){
                    $UserID = iconv('GB2312','UTF-8',$UserID);
                }
                unset($KuoZhan);

				$where['nickname'] = array('like',"%".$UserID."%");
				$where['user_id'] = array('like',"%".$UserID."%");
				$where['_logic']    = 'or';
				$map['_complex']    = $where;
				$UserID = urlencode($UserID);
			}
			if(!empty($u_sd)){
				$map['is_lock'] =1;
            }
            if(!empty($uulv)){
            	$map['u_level'] =$uulv;
            }
			$map['is_pay'] = array('egt',1);
			$renshu = $fck->where($map)->count();//总人数
            //查询字段
            $field  = '*';
            //=====================分页开始==============================================
            import ( "@.ORG.ZQPage" );  //导入分页类
            $count = $fck->where($map)->count();//总页数
       		$listrows = C('ONE_PAGE_RE');//每页显示的记录数
            $page_where = 'UserID=' . $UserID . '&type=' . $ss_type. '&ulevel=' . $uulv;//分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            //===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show();//分页变量
            $this->assign('page',$show);//分页变量输出到模板
            $list = $fck->where($map)->field($field)->order('pdt desc,id desc')->page($Page->getPage().','.$listrows)->select();
            //=====================分页结束==============================================
            $HYJJ = '';
            $this->_levelConfirm($HYJJ,1);
            $this->assign('voo',$HYJJ);//会员级别
            $level = array();
			for($i=0;$i<count($HYJJ) ;$i++){
				$level[$i] = $HYJJ[$i+1];
			}
			$this->assign('level',$level);
            $this->assign('count',$renshu);
            $this->assign('list',$list);//数据输出到模板
            
            //=====================分页开始==============================================
            // 待晋级数据
            $promo = M('promo');
            $mapPro['is_pay'] = '0';
            $countPro = $promo->where($mapPro)->count();//总页数
            $listrowsPro = C('ONE_PAGE_RE');//每页显示的记录数
            $page_wherePro = 'user_id='. $UserID;//分页条件
            $PagePro = new ZQPage($countPro, $listrowsPro, 1, 0, 3, $page_wherePro);
            $showPro = $PagePro->show();//分页变量
            $this->assign('pagePro',$showPro);//分页变量输出到模板
            $field  = '*';
            $list1 = $promo->where($mapPro)->field($field)->order('pdt desc,id desc')->page($PagePro->getPage().','.$listrowsPro)->select();
           
            $list1 = $promo->where($mapPro)->field($field)->order('id desc')->select();
            $this->assign('list1',$list1);//数据输出到模板
            //=====================分页结束==============================================
            //=================================================
			$this->display ();
		}else{
			$this->error('数据错误!');
			exit;
		}
	}
	
	//后台会员晋级
	public function adminMenberJinji(){
		if ($_SESSION['Urlszpass'] == 'Myssadminjinji'){
			$where = array();
			$fck = M('fck');
	    	$uid = $_GET['uid'];
	    	// 查询会员表数据
 	    	$frs = $fck->find($uid);
//  	    	$where['user_id'] = $uid;
//  	    	$frs = $fck->where($where)->field('*')->find();
// 			$voo = 0;
// 			$this->_levelConfirm($voo);

// 			$level = array();
// 			for($i=1;$i<=count($voo) ;$i++){
// 			    $level[$i] = $voo[$i];
// 			}
// 			$this->assign('level',$level);
			
			$fee = M ('fee');
			$fee_rs =$fee->field('s1,s2,s3,s4,s5,s9')->find();
			// 领导奖比例
			$s9=array('800','1600','2400','3200','4000','4800','5600','6400','7200','8000'
			    ,'8800','9600','10400','11200','12000','12800','13600','14400','15200','16000'
			    ,'16800','17600','18400','19200','20000','20800','21600','22400','23200','24000'
			    ,'24800','25600','26400','27200','28000','28800','29600','30400','31200','32000',
			    '32800','33600','34400','35200','36000','36800','37600','38400' ,'39200','40000');
			$this->assign('sx1',$s9);
            // 会员升级申请表
			$promo = M('promo');
			$field  = '*';
			$map['uid'] = $uid;
            $list = $promo->where($map)->field($field)->order('id desc')->select();
            $this->assign('list',$list);//数据输出到模板
            //=================================================

// 			$this->assign('uid',$uid);
// 			$this->assign('le',$voo);
// 			$this->assign('level',$level);
			$this->assign('frs',$frs);//数据输出到模板
			$this->display();
		}else{
			$this->error('错误！');
			exit;
		}
	}

	//后台晋级处理
	public function adminMenberJinjiConfirm(){
		$this->_Admin_checkUser();
		if ($_SESSION['Urlszpass'] == 'Myssadminjinji'){
			$ulevel = $_REQUEST['cpzj'];
			$uid = $_POST['uid'];
			
			$promo = M('promo');
			$fck = D('Fck');
			$fee = M ('fee');
			
			$where['id'] = $uid;
			$fck_rs = $fck->where($where)->find();
			if(!$fck_rs){
				$this->error('会员错误！');
				exit;
			}
			$fee_rs =$fee->field('s1,s2,s9,s4,s5')->find();
			
			$s2 =explode('|',$fee_rs['s2']);//单量
			$s3 =explode('|',$fee_rs['s9']);//每单金额
			
			$newlv = $ulevel;
			$oldlv  = $fck_rs['cpzj'];
			//差额
			$need_m = $newlv-$oldlv;
			//单量
			$need_dl = bcdiv($need_m, $s3[0]);
			if($fck_rs['cpzj'] >=$newlv){
				$this->error('只能向上升级');
			}
 			if($fck_rs['cpzj'] >=40000){
				$this->error('已经是最高级，无法再升级！');
			}

			$content = $_POST['content'];		//备注
			if (empty($content)){
// 				$this->error('备注不能为空!');
// 				exit;
			}

			// 写入帐号数据
			$data['uid']				= $uid;
			$data['user_id']			= $fck_rs['user_id'];
			$data['money']				= $need_m;//补差额
			$data['u_level']			= $oldlv;//旧的
			$data['up_level']			= $newlv;//新的
			$data['create_time']		= time();
			$data['pdt']				= time();
			$data['danshu']				= 0;
			$data['is_pay']				= 1;
			$data['user_name']			= " <font color=red>后台晋级</font>";
			$data['u_bank_name']		= $fck_rs['bank_name'];
			$data['type']				= 0;
            $result = $promo->add($data);
			unset($data);
			if($result) {
				//统计单数
				$fck->xiangJiao($uid, $need_dl);
				$fck->tz($fck_rs['p_path'],$need_m);
				//各种奖项
				$fck->tuijj($fck_rs['re_path'],$fck_rs['user_id'],$need_m);
				$fck->lingdao22($fck_rs['p_path'],$fck_rs['user_id'],$need_m);
				$fck->sh_level($fck_rs['p_path']);
				$fck->baodanfei($fck_rs['shop_id'],$fck_rs['user_id'],$need_m,$fck_rs['is_agent']);
				$fck->dsfenhong($fck_rs['p_path'],$fck_rs['user_id'],$need_m);
				$fck->query("update __TABLE__ set is_xf=0,u_level=1".",cpzj=".$ulevel.",f4=f4+".$need_dl." where `id`=".$uid);
				// 分红包记录表
				$nowdate = strtotime(date('c'));
				$fck->jiaDan($uid, $fck_rs['user_id'], $nowdate, 0, 0, $need_dl, 0, 1);
				
				unset($fck,$fee,$promo);
				$bUrl = __URL__.'/adminMenberJinji/uid/'.$uid;
				$this->_box(1,'晋级成功！',$bUrl,3);
			}else{
				$this->error('晋级失败！');
				exit;
			}
		}else{
			$this->error('错误！');
			exit;
		}
	}

}
?>