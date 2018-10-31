<?php
//注册模块
class NetworkAction extends CommonAction{
	
	public function _initialize() {
		header("Content-Type:text/html; charset=utf-8");
		$this->_inject_check(0);//调用过滤函数
		$this->_Config_name();//调用参数
 		$this->_checkUser();
		//$this->_inject_check(1);//调用过滤函数
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
				$_SESSION['Urlszpass'] = 'MyssNetworktoB';
				$bUrl = __URL__.'/NetworktoB';//进入B网
                $this->_boxx($bUrl);
				break;
			case 2;
				$_SESSION['Urlszpass'] = 'MyssBrelations';
				$bUrl = __URL__.'/Brelations';//分红点
                $this->_boxx($bUrl);
				break;
			case 3;
				$_SESSION['UrlPTPass'] = 'MyssBrelations';
				$bUrl = __URL__.'/adminTransferMoney';//货币转账
				$this->_boxx($bUrl);
				break;
			default;
			$this->error('二级密码错误!');
			exit;
		}
	}
	
	public function Brelations($Urlsz=0){
		//推荐关系
		if ($_SESSION['Urlszpass'] == 'MyssBrelations'){
			$fck = M('fck');
			$fck2 = M('fck2');
			$UserID = $_REQUEST['UserID'];
			if (!empty($UserID)){
				$map['user_id'] = array('like',"%".$UserID."%");
			}

			$map['re_num'] = $_SESSION[C('USER_AUTH_KEY')];//自身推荐
			$map['is_pay'] = 1;
			$map['id'] =  array('gt',1);

            $field  = '*';
            //=====================分页开始==============================================
            import ( "@.ORG.ZQPage" );  //导入分页类
            $count = $fck2->where($map)->count();//总页数
    	    $listrows = C('ONE_PAGE_RE');//每页显示的记录数
			$page_where = 'UserID='.$UserID;//分页条件
//            $page_where ='';
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            //===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show();//分页变量
            $this->assign('page',$show);//分页变量输出到模板
            $list = $fck2->where($map)->field($field)->order('id asc')->page($Page->getPage().','.$listrows)->select();
            $HYJJ = '';
            $this->_levelConfirm($HYJJ,1);
            $this->assign('voo',$HYJJ);//会员级别
            $this->assign('list',$list);//数据输出到模板
            //=================================================

			$this->display ('Brelations');
			return;
		}else{
			$this->error('数据错误2!');
			exit;
		}
	}

	
	public function getUserName(){
		$user_id = $_GET['userid'];
		$fck = M('fck');
		$rs = $fck->where("user_id='$user_id'")->field("user_name")->find();
	
		
		echo  $rs['user_name'];
		
	}
	
	//=============================================注册币转帐(会员之间的注册币转帐)
	public function NetworktoB($Urlsz=0){
		if ($_SESSION['Urlszpass'] == 'MyssNetworktoB'){
			$zhuanj = M('zhuanj');
			$map['in_uid'] = $_SESSION[C('USER_AUTH_KEY')];
			$map['out_uid'] = $_SESSION[C('USER_AUTH_KEY')];
			$map['_logic'] = 'or';
	
	
	
			//			$id = $_SESSION[C('USER_AUTH_KEY')];
			//			$sql = "in_uid =".$id ." or out_uid = ".$id;
			$field  = '*';
			//=====================分页开始==============================================
			import ( "@.ORG.ZQPage" );  //导入分页类
			$count = $zhuanj->where($map)->count();//总页数
			$listrows = C('ONE_PAGE_RE');//每页显示的记录数
			$Page = new ZQPage($count,$listrows,1);
			//===============(总页数,每页显示记录数,css样式 0-9)
			$show = $Page->show();//分页变量
			$this->assign('page',$show);//分页变量输出到模板
			$list = $zhuanj->where($map)->field($field)->order('id desc')->page($Page->getPage().','.$listrows)->select();
			$this->assign('list',$list);//数据输出到模板
			//=================================================
	
			$fck = M ('fck');
			$where = array();
			$where['id'] = $_SESSION[C('USER_AUTH_KEY')];
			$field = '*';
			$rs = $fck->where($where)->field($field)->find();
			$this->assign('rs',$rs);
			$this->display('NetworktoB');
			return;
		}else{
			$this->error ('错误!');
			exit;
		}
	}
	
	
	public function NetworktoBAC(){
		$UserID = $_POST['UserID'];    //转入会员帐号(进帐的用户帐号)
		//	$ePoints = (int) $_POST['ePoints'];
		$ePoints = $_POST['ePoints'];  //转入金额
		$select = $_POST['select'];  //转帐类型
	
		$fck = M ('fck');
		$where = array();
		$where['id']= $_SESSION[C('USER_AUTH_KEY')];
	
		$f = $fck->where($where )->field('user_id')->find();
		
		$fck = M ('fck');
		if (!$fck->autoCheckToken($_POST)){
			$this->error('页面过期，请刷新页面！');
			exit;
		}
		if (empty($ePoints) || !is_numeric($ePoints) || empty($UserID)){
			$this->error('输入不能为空!');
			exit;
		}
		if($ePoints <= 0){
			$this->error('输入的金额有误!');
			exit;
		}
		$this->_NetworktoBConfirm($UserID,$ePoints,$select);
	}
	
	private function _NetworktoBConfirm($UserID='0',$ePoints=0,$select=0){
		if ($_SESSION['Urlszpass'] == 'MyssNetworktoB'){  //转帐权限session认证
			$fck = M ('fck');
			$where = array();
			$ID = $_SESSION[C('USER_AUTH_KEY')];     //登录会员AutoId
			$inUserID =  $_SESSION['loginUseracc'];  //登录的会员帐号 user_id
			//转出
	
			$myww = array();
			$myww['id'] = array('eq',$ID);
			$mmrs = $fck->where($myww)->find();
			$mmid = $mmrs['id'];
			$mmtxnum = $mmrs['tx_num'];
			$mmisagent = $mmrs['is_agent'];
			if($mmid==1){
				$mmisagent = 0;
			}

			$fee_rs = M ('fee') -> find();
			$b_money = $fee_rs['b_money'];

			$hB = 10;//倍数
			$mmB = $b_money;//最低额
			if($select==1||$select==2){
				if($ePoints!=$mmB){
					$this->error ('自我消费金额必须为 '.$mmB.' ！');
					exit;
				}
			}
//			if ($ePoints % $hB){
//				$this->error ('额度必须为 '.$hB.' 的整数倍!');
//				exit;
//			}
			if($mmtxnum==0){
				$this->error('分红点有出局时才可以使用自我消费!');
				exit;
			}
			if($select==1){
				$AgentUse = $mmrs['agent_cash'];
				if ($AgentUse < $ePoints){            //判断K币余额
					$this->error('K币余额不足!');
					exit;
				}
			}
			if($select==2){
				$AgentUseTwo = $mmrs['agent_xf'];
				if ($AgentUseTwo < $ePoints){            //判断重复消费余额
					$this->error('重消币账户余额不足!');
					exit;
				}
			}
			
			if($select==1){
				$fck->execute("update `xt_fck` Set `agent_cash`=agent_cash-".$ePoints." where `id`=".$ID);
			}
			if($select==2){
				$fck->execute("update `xt_fck` Set `agent_xf`=agent_xf-".$ePoints." where `id`=".$ID);
			}
			
			if($mmtxnum>0){
				for ($io = 0; $io < 4; $io++){
				$new_io=$io+1;
				$this->pd_into_websiteb($ID,$new_io) ;
				}
				$fck->execute("UPDATE __TABLE__ SET tx_num=tx_num-1  WHERE tx_num>0 and id=".$ID);
			}
			else{
				$this->pd_into_websiteb($ID) ;
			}
			
			$bUrl = __URL__.'/NetworktoB';
			$this->_box(1,'操作成功！',$bUrl,1);
			exit;
		}else{
			$this->error('错误！');
			exit;
		}
	}
	
	 //判断进入B网
    public function pd_into_websiteb($uid,$newfg=0){
		//$fck = D ('fck');
		$fck=new FckModel('fck');
    	$fck2 = M ('fck2');
    	$where = "is_pay>0 and is_lock=0 and is_bb>=0 and id=".$uid;
    	$lrs = $fck->where($where)->field('id,user_id,user_name,nickname,u_level')->find();
    	if($lrs){
    		$myid = $lrs['id'];
    		$result = $fck->execute("update __TABLE__ set is_bb=is_bb+1 where id=".$myid." and is_bb>=0");
    		if($result){
    			$data=array();
    			$data['fck_id'] = $lrs['id'];
    			$data['re_num'] = $lrs['id'];
    			$data['user_id'] = $lrs['user_id']."_0".$newfg;
    			$data['user_name'] = $lrs['user_name'];
    			$data['nickname'] = $lrs['nickname'];
    			$data['u_level'] = $lrs['u_level'];
    			$data['ceng'] = 0;
    
    			$farr = $this->gongpaixt_Two_big_B();
    			$data['father_id']		= $farr['father_id'];
    			$data['father_name']	= $farr['father_name'];
    			$data['treeplace']		= $farr['treeplace'];
    			$data['p_level']		= $farr['p_level'];
    			$data['p_path']			= $farr['p_path'];
    			$data['u_pai']			= $farr['u_pai'];
    			$data['is_pay']			= 1;
    			$data['pdt']			= time();
    			$ress = $fck2->add($data);  // 添加
    			$ppath = $data['p_path'];
    			$inUserID = $data['user_id'];
    			$ulevel = $data['u_level'];
    			unset($data,$farr);
    			if($ress){
    				//b网见点
    				$fck->jiandianjiang_bb($ppath,$inUserID,$ulevel);
    			}
    		}
    	}
    	unset($fck2,$lrs,$where,$fck);
    }
    
	//B网公排
    public function gongpaixt_Two_big_B(){
    	$b_fck2 = M ('fck2');
    	$field = 'id,user_id,p_level,p_path,u_pai';
    	$re_rs = $b_fck2 ->where('is_pay>0')->order('p_level asc,u_pai+0 asc')->field($field)->select();
    	foreach($re_rs as $vo){
    		$faid=$vo['id'];
    		$count = $b_fck2->where("is_pay>0 and father_id=".$faid)->count();
    		if ( is_numeric($count) == false){
    			$count = 0;
    		}
    		if ($count<2){
    			$father_id=$vo['id'];
    			$father_name=$vo['user_id'];
    			$TreePlace=$count;
    			$p_level=$vo['p_level']+1;
    			$p_path=$vo['p_path'].$vo['id'].',';
    			$u_pai=$vo['u_pai']*2+$TreePlace;
    
    			$arry=array();
    			$arry['father_id']=$father_id;
    			$arry['father_name']=$father_name;
    			$arry['treeplace']=$TreePlace;
    			$arry['p_level']=$p_level;
    			$arry['p_path']=$p_path;
    			$arry['u_pai']=$u_pai;
    			return $arry;
    			break;
    		}
    	}
    }
	
	public function adminTransferMoney(){
		$this->_Admin_checkUser();
		if ($_SESSION['UrlPTPass'] == 'MyssTransfer'){
			$zhuanj = M('zhuanj');
				
			$UserID = $_REQUEST['UserID'];
			
			if (!empty($UserID)){
				import ( "@.ORG.KuoZhan" );  //导入扩展类
				$KuoZhan = new KuoZhan();
				if ($KuoZhan->is_utf8($UserID) == false){
					$UserID = iconv('GB2312','UTF-8',$UserID);
				}
				unset($KuoZhan);
				//     		$map['in_userid'] = array('like',"%".$UserID."%");
				$map = "(in_userid like '%".$UserID."%') or (out_userid  like '%".$UserID."%') ";
				$UserID = urlencode($UserID);
			}
				
			import ( "@.ORG.ZQPage" );  //导入分页类
			$count = $zhuanj->where($map)->count();//总页数
			$listrows = C('ONE_PAGE_RE');//每页显示的记录数
			$page_where = 'UserID=' . $UserID;//分页条件
			$Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
			//===============(总页数,每页显示记录数,css样式 0-9)
			$show = $Page->show();//分页变量
			$this->assign('page',$show);//分页变量输出到模板
			$list = $zhuanj->where($map)->field('*')->order('rdt desc,id desc')->page($Page->getPage().','.$listrows)->select();
			$this->assign('list',$list);
				
			$this->display('adminTransferMoney');
		}else{
			$this->error('数据错误！');
			exit;
		}
		 
	}
	
	
}
?>