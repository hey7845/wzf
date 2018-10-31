<?php
//注册模块
class TransferAction extends CommonAction{
	
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
				$_SESSION['Urlszpass'] = 'MyssFenYingTao';
				$bUrl = __URL__.'/transferMoney';//货币转账
                $this->_boxx($bUrl);
				break;
			case 2;
				$_SESSION['UrlPTPass'] = 'MyssTransfer';
				$bUrl = __URL__.'/adminTransferMoney';//货币转账
				$this->_boxx($bUrl);
				break;
			default;
			$this->error('二级密码错误!');
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
	public function transferMoney($Urlsz=0){
		if ($_SESSION['Urlszpass'] == 'MyssFenYingTao'){
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
			$this->display('transferMoney');
			return;
		}else{
			$this->error ('错误!');
			exit;
		}
	}
	
	
	public function transferMoneyAC(){
	    
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
	        $this->error('只能在8时至21时转账，节假日及休息时间不能转账！');
	        exit;
	    }
		$UserID = $_POST['UserID'];    //转入会员帐号(进帐的用户帐号)
		//	$ePoints = (int) $_POST['ePoints'];
		$ePoints = $_POST['ePoints'];  //转入金额
		$content = $_POST['content'];  //转帐说明
		$select = $_POST['select'];  //转帐类型
	
		$fck = M ('fck');
		$where = array();
		$where['id']= $_SESSION[C('USER_AUTH_KEY')];
	
		$f = $fck->where($where )->field('user_id')->find();
	
		if ($select==6) $UserID = $_SESSION['loginUseracc'];
	
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
		if($select==2 || $select==3){
			if($UserID != $f['user_id']){
				$this->error('请输入自己的会员编号!');
				exit;
			}
		}


		//if($select==1){
			//if($UserID == $f['user_id']){
				//$this->error('不能转给自己!');
				//exit;
			//}
		//}
		$this->_transferMoneyConfirm($UserID,$ePoints,$content,$select);
	}
	
	private function _transferMoneyConfirm($UserID='0',$ePoints=0,$content=null,$select=0){
		if ($_SESSION['Urlszpass'] == 'MyssFenYingTao'){  //转帐权限session认证
			$fck = M ('fck');
			$where = array();
			$ID = $_SESSION[C('USER_AUTH_KEY')];     //登录会员AutoId
			$inUserID =  $_SESSION['loginUseracc'];  //登录的会员帐号 user_id
			//转出
			$history = M ('history');  //明细表
			$zhuanj  = M ('zhuanj');   //转帐表
	
			$myww = array();
			$myww['id'] = array('eq',$ID);
			$mmrs = $fck->where($myww)->find();
			$mmid = $mmrs['id'];
			$mmisagent = $mmrs['is_agent'];
			$p_path1=$mmrs['p_path'];
			// if($mmid==1){
			// 	$mmisagent = 0;
			// }
			
			//转入会员
			$fck_where = array();
			$fck_where['user_id'] =$UserID;// strtolower($UserID);
			$field = "id,user_id,is_agent,re_path,p_path,is_treasure_manager";
			$vo = $fck ->where($fck_where)->field($field)->find();  //找出转入会员记录
			$void=$vo['id'];
			$p_path2=$vo['p_path'];

			if (!$vo){
				$this->error('转入会员不存在!');
				exit;
			}
			
			if ($ID != 1 && $vo['is_treasure_manager'] == 1){
			    $this->error('不允许回转！');
			    exit;
			}

			$pos1 = strpos($p_path1, $void);
			$pos2 = strpos($p_path2, $mmid);
			if(($select==1 || $select==4 || $select==5) && $mmrs['is_treasure_manager'] != 1){
			if($pos1 === false && $pos2 === false){
				$this->error('只能同一条线才可以上下互转!');
				exit;
			}
			}
			$fee_rs = M ('fee') -> find();
			$str3 = $fee_rs['str3'];
			$str18 = $fee_rs['str18'];
			$str19 = $fee_rs['str19'];
			$str7 =$fee_rs['str7'];
			$kB = $str18;//倍数
			$hB = $str19;//倍数
			$mmB = $str3;//最低额
			
			if($select==1 || $select==2 || $select==3 || $select==4){
				if($ePoints<$mmB){
					$this->error ('转账最低额度必须为 '.$mmB.' ！');
					exit;
				}
			}
			// 	if ($ePoints % $hB){
			// 		$this->error ('额度必须为 '.$hB.' 的整数倍!');
			// 		exit;
			// 	}
			// }
			// if($select==3){
			// 	if($ePoints<$kB){
			// 		$this->error ('转账最低额度必须为 '.$kB.' ！');
			// 		exit;
			// 	}
			// 	if ($ePoints % $kB){
			// 		$this->error ('额度必须为 '.$kB.' 的整数倍!');
			// 		exit;
			// 	}
			// }
			
	
			if($select==1){
				$Agent_cash = $mmrs['agent_cash'];
				if ($Agent_cash < $ePoints){            //判断电子积分
					$this->error('电子积分余额不足!');
					exit;
				}
			}
			if($select==3 || $select==2){
				$AgentUse = $mmrs['agent_use'];
				if ($AgentUse < $ePoints){            //判断消费积分
					$this->error('消费积分余额不足!');
					exit;
				} else if ($mmrs['is_lock_use'] == 1){
			        $this->error('消费积分已锁定，请联系管理员解除锁定！');
			        exit();
				}
			}
			if($select==4){
			    $AgentUse2 = $mmrs['agent_cash'];
    			if ($AgentUse2 < $ePoints){            //判断电子积分
    				$this->error('电子积分余额不足!');
    				exit;
    			}
			}
			
			if($select==5){
			    $AgentActive = $mmrs['agent_active'];
			    if ($AgentActive < $ePoints){            //判断激活积分
    				$this->error('激活积分余额不足!');
    				exit;
			    }
			}
				
			$history->startTrans();//开始事物处理
			$zz_content = "转帐";
			if($select==1){
				//$zz_content = "现金币 转给 其他会员";
				//$fck->execute("update `xt_fck` Set `agent_use`=agent_use-".$ePoints." where `id`=".$ID);
				//$fck->execute("update `xt_fck` Set `agent_use`=agent_use+".$ePoints." where `id`=".$vo['id']);
				$zz_content = "电子币 转 现金币";
				
				$fck->execute("update `xt_fck` Set `agent_cash`=agent_cash-".$ePoints." where `id`=".$ID);
				$fck->execute("update `xt_fck` Set `agent_use`=agent_use+".$ePoints." where `id`=".$vo['id']);
			}
			if($select==2){
				$zz_content = "现金币 转 电子币";
				$fck->execute("update `xt_fck` Set `agent_use`=agent_use-".$ePoints." where `id`=".$ID);
				// 转账手续费5%
				$tmp = $ePoints * 6;
				$tmp = bcdiv($tmp, 100,2);
				$ePoints = $ePoints - $tmp;
				$fck->execute("update `xt_fck` Set `agent_cash`=agent_cash+".$ePoints." where `id`=".$vo['id']);
			}
			if($select==3){
				$zz_content = "现金币 转 复投币";
				$fck->execute("update `xt_fck` Set `agent_use`=agent_use-".$ePoints." where `id`=".$ID);
				if ($mmrs['net_status'] == 'b'){
				    $fck->execute("update `xt_netb` Set `agent_futou`=agent_futou+".$ePoints." where `id`=".$vo['id']);
				} else {
				    $fck->execute("update `xt_fck` Set `agent_xf`=agent_xf+".$ePoints." where `id`=".$vo['id']);
				}
			}

			if($select==4){
				$zz_content = "电子币 转给 其他会员";
				$fck->execute("update `xt_fck` Set `agent_cash`=agent_cash-".$ePoints." where `id`=".$ID);
				$fck->execute("update `xt_fck` Set `agent_cash`=agent_cash+".$ePoints." where `id`=".$vo['id']);
			}
			if($select==5){
				$zz_content = "激活币 转给 其他会员";
				$fck->execute("update `xt_fck` Set `agent_active`=agent_active-".$ePoints." where `id`=".$ID);
				$fck->execute("update `xt_fck` Set `agent_active`=agent_active+".$ePoints." where `id`=".$vo['id']);
			}
			// if($select==4){
			// 	$zz_content = "种子币 转 注册币 ";
			// 	$fck->execute("update `xt_fck` Set `agent_kt`=agent_kt-".$ePoints." where `id`=".$ID);
			// 	$fck->execute("update `xt_fck` Set `agent_cash`=agent_cash+".$ePoints." where `id`=".$vo['id']);
			// }
			// if($select==5){
			// 	$zz_content = "保管币 转 注册币";
			// 	$fck->execute("update `xt_fck` Set `agent_xf`=agent_xf-".$ePoints." where `id`=".$ID);
			// 	$fck->execute("update `xt_fck` Set `agent_cash`=agent_cash+".$ePoints." where `id`=".$vo['id']);
			// }
			// if($select==6){
			// 	$zz_content = "注册币 转 一进一出币";
			// 	$fck->execute("update `xt_fck` Set `agent_cash`=agent_cash-".$ePoints." where `id`=".$ID);
			// 	$fck->execute("update `xt_fck` Set `agent_xf`=agent_xf+".$ePoints." where `id`=".$vo['id']);
			// }
	
			$nowdate = time();
			$data = array();
			$data['uid']           = $ID;          //转出会员ID
			$data['user_id']       = $UserID;
			$data['did']           = $vo['id'];    //转入会员ID
			$data['user_did']      = $vo['user_id'];
			$data['action_type']   = 27;    //转入还是转出
			$data['pdt']           = $nowdate;     //转帐时间
			$data['epoints']       = $ePoints;     //进出金额
			$data['allp']          = 0;
			$data['bz']            = $zz_content;     //备注
			$data['type']          = 1;   		   //1转帐
			$history->create();
			$rs2=$history->add($data);
			unset($data);
			//转账表
			$data = array();
			$data['in_uid']        = $vo['id'];           //转入会员ID
			$data['out_uid']       = $ID;
			$data['in_userid']     = $vo['user_id'];      //转入会员的登录帐号
			$data['out_userid']    = $inUserID;
			$data['epoint']        = $ePoints;            //进出金额
			$data['rdt']           = $nowdate;            //转帐时间
// 			$data['sm']            = $content;            //转帐说明
			//$data['type']          = 0;                 // 3为货币转为货币
			$data['type']   = $select;
			$zhuanj->create();
			$rs4=$zhuanj->add($data);
			unset($data);
	
			//无错误提交数据
			if ($rs2 && $rs4){
				$history->commit();//提交事务
				$bUrl = __URL__.'/transferMoney';
				$this->_box(1,'操作成功！',$bUrl,1);
				exit;
			}else{
				$history->rollback();//事务回滚：
				$this->error('输入数据错误！');
			}
		}else{
			$this->error('错误！');
			exit;
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