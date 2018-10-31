<?php
class GupiaoAction extends CommonAction {

	function _initialize() {
		ob_clean();
		set_time_limit(0);
		$this->_inject_check(0); //调用过滤函数
		header("Content-Type:text/html; charset=utf-8");
		$this->_checkUser();
		$this->gp_up_down_pd();
		//$this->force_sell_gp();
		$this->stock_past_due(); ///查看是否過一天   CQ币
	}

    //二级验证
    public function Cody(){
        $this->_checkUser();
        $UrlID = (int) $_GET['c_id'];
  
		if(!empty($_SESSION['user_pwd2'])){
			//unset($_SESSION['user_pwd3']);//清空二级输入的密码
			$url = __URL__."/codys/Urlsz/$UrlID";
			$this->_boxx($url);
			exit;
		}
		$thisa = $this->getActionName();
		$this->assign('thisa',$thisa);
        if (empty($UrlID)){
            $this->error('二级密码错误!');
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
		$this->_checkUser();
		
		$Urlsz = (int) $_POST['Urlsz'];
		
		if(empty($_SESSION['user_pwd2'])){
			$pass  = $_POST['oldpassword'];
			$fck   =  M ('fck');
			if (!$fck->autoCheckToken($_POST)){
				//$this->error('页面过期请刷新页面!');
				//exit();
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

		switch ($Urlsz) {
			
			case 1;

				$_SESSION['UrlszUserpass'] = 'MyssQiCheng';
				$bUrl = __URL__ . '/buyGPform';
				$this->_boxx($bUrl);
				break;
			case 2;
				$_SESSION['UrlszUserpass'] = 'sellgupiao';//出卖CQ币
				$bUrl = __URL__ . '/sellGPform';
				$this->_boxx($bUrl);
				break;
			case 3;
				$_SESSION['UrlszUserpass'] = 'gpHistory';//CQ币买卖记录
				$bUrl = __URL__ . '/sellGPform_N';
				$this->_boxx($bUrl);
				break;
			case 4;
				$_SESSION['UrlszUserpass'] = 'gpHistory';//CQ币买卖记录
				$bUrl = __URL__ . '/alllistGP';
				$this->_boxx($bUrl);
				break;
			case 5;
				$_SESSION['UrlPTPass'] = 'adminsetGP';
				$bUrl = __URL__ . '/adminsetGP';
				$this->_boxx($bUrl);
				break;
			case 7;
				$_SESSION['UrlszUserpass'] = 'gpHistory';
				$bUrl = __URL__ . '/buylist';
				$this->_boxx($bUrl);
				break;
			case 8;
				$_SESSION['UrlszUserpass'] = 'gpHistory';
				$bUrl = __URL__ . '/selllist';
				$this->_boxx($bUrl);
				break;
			case 9;
				$_SESSION['UrlszUserpass'] = 'gpHistory';
				$bUrl = __URL__ . '/agents';
				$this->_boxx($bUrl);
				break;
			case 10;
				$_SESSION['UrlszUserpass'] = 'gpHistory';
				$bUrl = __URL__ . '/admin1';
				$this->_boxx($bUrl);
				break;
			case 11;
				$_SESSION['Urlszpass'] = 'Mygive';
				$bUrl = __URL__.'/transferMoney1';
				$this->_boxx($bUrl);
				break;
			case 12;
				$_SESSION['Urlszpass'] = 'gpHistory';
				$bUrl = __URL__.'/buyyuans';
				$this->_boxx($bUrl);
				break;
			case 13;
				$_SESSION['Urlszpass'] = 'Gpjiaoyi';
				$bUrl = __URL__.'/tradingfloor';
				$this->_boxx($bUrl);
				break;

				case 14;
				$_SESSION['UrlPTPass'] = 'HYguanli';
				$bUrl = __URL__.'/adminMenber';//会员管理
				$this->_boxx($bUrl);
				break;
			default;
				$this->error('二级密码错误!');
				break;
		}
	}




	public function adminMenber($GPid=0){
		//列表过滤器，生成查询Map对象
		if ($_SESSION['UrlPTPass'] == 'HYguanli'){
			$fck = M('fck');
			$UserID = $_REQUEST['UserID'];

			$ss_type = (int) $_REQUEST['type'];
			
			$map = array();
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
			$uulv = (int)$_REQUEST['ulevel'];
			if(!empty($uulv)){
            	$map['u_level'] = array('eq',$uulv);
            }
 			$map['is_pay'] = array('egt',1);
            //查询字段
            $field  = '*';
            //=====================分页开始==============================================
            import ( "@.ORG.ZQPage" );  //导入分页类
            $count = $fck->where($map)->count();//总页数
       		$listrows = C('ONE_PAGE_RE');//每页显示的记录数
       		$listrows = 20;//每页显示的记录数
            $page_where = 'UserID=' . $UserID. '&ulevel=' . $uulv ;//分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            //===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show();//分页变量
            $this->assign('page',$show);//分页变量输出到模板
            $list = $fck->where($map)->field($field)->order('pdt desc,id desc')->page($Page->getPage().','.$listrows)->select();
		
            $f4_count =  $fck->where($map)->sum('cpzj');
            $this->assign('f4_count',$f4_count);
            
            $HYJJ = '';
            $this->_levelConfirm($HYJJ,1);
            $this->assign('voo',$HYJJ);//会员级别
            
            $getLev = "";
            $this->_getLevelConfirm($getLev,1);
            $this->assign('gvoo',$getLev);//会员团队级别
            
            
            $level = array();
			for($i=0;$i<count($HYJJ) ;$i++){
				$level[$i] = $HYJJ[$i+1];
			}
			$this->assign('level',$level);
            $this->assign('list',$list);//数据输出到模板
            //=================================================


			$title = '会员管理';
			$this->assign('title',$title);
			$this->display ('adminMenber');
			return;
		}else{
			$this->error('数据错误!');
			exit;
		}
	}


	public function adminMenberAC(){
		//处理提交按钮
		$action = $_POST['action'];
		//获取复选框的值
		$PTid = $_POST['tabledb'];
		if (!isset($PTid) || empty($PTid)){
			$bUrl = __URL__.'/adminMenber';
			$this->_box(0,'请选择会员！',$bUrl,1);
			exit;
		}
		switch ($action){
			case '结算';
				$this->_adminMenberOpen($PTid);
				break;
		
		
		default;
			$bUrl = __URL__.'/adminMenber';
			$this->_box(0,'没有该会员！',$bUrl,1);
			break;
		}
	}


public function _adminMenberOpen($PTid=0){

	$fck= D ('Fck');
	$where['id'] = array ('in',$PTid);
			
	$rs = $fck->where($where)->select();
	foreach ($rs as $key => $value) {

		//本期新增积分和投资
		$shang_use = $value['agent_use']-$value['shangqi_use'];
		$shang_tz  = $value['cpzj']+$value['tz_nums']-$value['shangqi_tz'];
	
		$one=$shang_tz-$shang_use;
		
		if($one>0){
			$money=$one*0.5;
			$fck->rw_bonus($value['id'],$value['user_id'],5,$money);
			$fck->execute("update __TABLE__ set shangqi_use=".$shang_use.",shangqi_tz=".$shang_tz." where id=".$value['id']);
		}



	}
	if ($rs){
				$bUrl = __URL__.'/adminMenber';
				$this->_box(1,'结算成功！',$bUrl,1);
				exit;
			}


}











	public function transferMoney1($Urlsz=0){
		if ($_SESSION['Urlszpass'] == 'Mygive'){
			$zhuanj = M('zhuanj');
	
			$map['out_uid'] = $_SESSION[C('USER_AUTH_KEY')];
			$map['is_gupiao']=1;
			// $map['_logic'] = 'or';
	
	
	
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
			$this->display('transferMoney1');
			return;
		}else{
			$this->error ('错误!');
			exit;
		}
	}
	
	
	public function transferMoneyAC(){
		$UserID = $_POST['UserID'];    //转入会员帐号(进帐的用户帐号)
		//	$ePoints = (int) $_POST['ePoints'];
		$ePoints = $_POST['ePoints'];  //转入金额
		$content = $_POST['content'];  //转帐说明
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
			$this->error('输入的额度有误!');
			exit;
		}
	
			if($UserID == $f['user_id']){
				$this->error('不能送给自己!');
				exit;
			}
		
		$this->_transferMoneyConfirm($UserID,$ePoints,$content,$select);
	}
	
	private function _transferMoneyConfirm($UserID='0',$ePoints=0,$content=null,$select=0){
		if ($_SESSION['Urlszpass'] == 'Mygive'){  //转帐权限session认证
			$fck  = D ('Fck');
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
			$user_id=$mmrs['user_id'];
			$mmisagent = $mmrs['is_agent'];
			if($mmid==1){
				$mmisagent = 0;
			}
	
			//转入会员
			$fck_where = array();
			$fck_where['user_id'] =$UserID;// strtolower($UserID);
			$field = "id,user_id,is_agent";
			$vo = $fck ->where($fck_where)->field($field)->find();  //找出转入会员记录
			if (!$vo){
				$this->error('转入会员不存在!');
				exit;
			}



			
			$history->startTrans();//开始事物处理
			$zz_content = "赠送";
	
				$zz_content = "赠送股票给其他会员";
				
				$fck->execute("update `xt_fck` Set `give_gupiao`=give_gupiao+".$ePoints." where `id`=".$vo['id']);
		
			$nowdate = time();
			$data = array();
			$data['uid']           = $ID;          //转出会员ID
			$data['user_id']       = $UserID;
			$data['did']           = $vo['id'];    //转入会员ID
			$data['user_did']      = $vo['user_id'];
			$data['action_type']   = 20;    //转入还是转出
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
			$data['is_gupiao']       = 1;                 
			$data['type']   = 1;
			$zhuanj->create();
			$rs4=$zhuanj->add($data);
			unset($data);
	
			//无错误提交数据
			if ($rs4){
				$history->commit();//提交事务

				$fck->_givegp($vo['id'],$user_id,$ePoints);

				$bUrl = __URL__.'/transferMoney1';
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






	public function agents($Urlsz=0){
		//=====================================
		if ($_SESSION['UrlszUserpass'] == 'gpHistory'){
			$fee_rs = M ('fee') -> find();
		
			$fck = M ('fck');
			$where = array();
			//查询条件
			$where['id'] = $_SESSION[C('USER_AUTH_KEY')];
			$field ='*';
			$fck_rs = $fck ->where($where)->field($field)->find();

			if ($fck_rs){
				//会员级别
				switch($fck_rs['is_aa']){
					case 0:
						$agent_status = '未购买';
						break;
					case 1:
						$agent_status = '正在审核中!';
						break;
					case 2:
						$agent_status = '购买成功!';
						break;
				}
				$res = '已购买'.$fck_rs['is_cc'].'手!';
				$this->assign('res',$res);
				$this->assign ( 's6',$fee_rs['s6']);
				
				$this->assign ( 'agent_status',$agent_status);
				$this->assign ( 'fck_rs', $fck_rs);
				
				
				$this->display ('agents');
			}else{
				$this->error ('操作失败!');
				exit;
			}
		}else{
			$this->error ('错误!');
			exit;
		}
	}
	
	
	public function agentsAC(){
		//================================中转函数
		$nums  = $_POST['nums'];
		$select  = $_POST['select'];
	if(!$nums){
		$this->error('请填写购买数量!');
		exit;
	}
		$fee=M('fee');
		$fee_rs=$fee->where('str1,s14,s6')->find(1);
    
    	$s6=$fee_rs['s6'];
    	$str1=$fee_rs['str1'];
		$fck = M ('fck');
		$id = $_SESSION[C('USER_AUTH_KEY')];		
		$where = array();
		$where['id'] = $id;
	
		$fck_rs = $fck->where($where)->field('*')->find();
		if($fck_rs){



				if($nums>$str1){
					$this->error('单次购买不能超过'.$str1.'股！');
					exit;
				}

				$aa=$fck_rs['pg_nums']+$nums;
				
				if($aa>$str1){
					$re=$str1-$fck_rs['pg_nums'];
				}
				if($re){
					$this->error('您的本周还可以购买'.$re.'次！');
					exit;
				}

				if($fck_rs['pg_nums']>$str1){
					$this->error('您的本周购买已达到'.$str1.'次！');
					exit;
				}

			
			if($fck_rs['is_aa']  == 1){
				$this->error('上次申请还没通过审核!');
				exit;
			}
			$ePoints=$nums*$s6;
			if($select==2){

					$AgentUse = $fck_rs['agent_use'];
				if ($AgentUse < $ePoints){            //判断积分余额
					$this->error('积分余额不足!');
					exit;
				}else{
					$fck->execute("update `xt_fck` Set `agent_use`=agent_use-".$ePoints.",is_cc=is_cc+".$nums.",pg_nums=pg_nums+".$nums." ,tz_nums=tz_nums+".$ePoints." where `id`=".$fck_rs['id']);
				}

				
			$bUrl = __URL__ .'/agents';
			$this->_box(1,'购买成功！',$bUrl,2);
			}

			if($select==1){



				// $nowdate = time();
				// $result = $fck -> query("update __TABLE__ set is_agent=1,is_cc=is_cc+".$nums.",idt=$nowdate where id=".$id);
				$result = $fck -> query("update __TABLE__ set is_aa=1,is_bb=".$nums." where id=".$id);
			
	
			$bUrl = __URL__ .'/agents';
			$this->_box(1,'购买成功，请联系管理员！',$bUrl,2);
			}
	
		}else{
			$this->error('非法操作');
			exit;
		}
	}






	public function admin1(){
		//=====================================后台报单中心管理
		
	
			$fck = M('fck');
			$UserID = $_POST['UserID'];
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
			//$map['is_del'] = array('eq',0);
			$map['is_aa'] = array('gt',0);
		
            $field  = '*';
            //=====================分页开始==============================================
            import ( "@.ORG.ZQPage" );  //导入分页类
            $count = $fck->where($map)->count();//总页数
       		$listrows = C('ONE_PAGE_RE');//每页显示的记录数
            $page_where = 'UserID=' . $UserID;//分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            //===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show();//分页变量
            $this->assign('page',$show);//分页变量输出到模板
            $list = $fck->where($map)->field($field)->order('id desc')->page($Page->getPage().','.$listrows)->select();
            $this->assign('list',$list);//数据输出到模板
            //=================================================
            


            $id = $_SESSION[C('USER_AUTH_KEY')];
            $res=$fck->where('id='.$id)->find();
            $this->assign('res',$res);
			$this->display ('admin1');
			
		
	}




	public function adminAgentsAC(){  //审核配股
		$this->_Admin_checkUser();
		//处理提交按钮
		$action = $_POST['action'];
		//获取复选框的值
		$XGid = $_POST['tabledb'];
		$fck = M ('fck');
//	    if (!$fck->autoCheckToken($_POST)){
//            $this->error('页面过期，请刷新页面！');
//            exit;
//        }
        unset($fck);
		if (!isset($XGid) || empty($XGid)){
			$bUrl = __URL__.'/admin1';
			$this->_box(0,'请选择会员！',$bUrl,1);
			exit;
		}
		switch ($action){
			case '确认';
				$this->_adminAgentsConfirm($XGid);
				break;
			case '删除';
				$this->_adminAgentsDel($XGid);
				break;
		default;
			$bUrl = __URL__.'/admin1';
			$this->_box(0,'没有该会员！',$bUrl,1);
			break;
		}
	}
	


	private function _adminAgentsConfirm($XGid=0){
		//==========================================确认购买
		if ($_SESSION['UrlszUserpass'] == 'gpHistory'){
			$fee=M('fee');
			$fee_rs=$fee->where('str1,s14,s6')->find(1);
    
    		$s6=$fee_rs['s6'];
			$fck  = D ('Fck');
			$where['id'] = array ('in',$XGid);
			$where['is_aa'] = 1;
			$rs = $fck->where($where)->field('*')->select();
			


			$data = array();
			$history = M ('history');
            $rewhere = array();
//          $nowdate = strtotime(date('c'));
            $nowdate = time();
            $jiesuan = 0;
			foreach($rs as $rss){


		$nums=$rss['is_bb'];
		$ePoints=$nums*$s6;
		$id = $_SESSION[C('USER_AUTH_KEY')];
			if($id!=1){
				$res=$fck->where('id='.$id)->find();
				$agent_cash=$res['agent_cash'];
				if($agent_cash<$ePoints){
					$this->error('注册币不足，无法确认!');
					exit;
				}else{
					$fck ->query("UPDATE __TABLE__ SET agent_cash=agent_cash-{$ePoints} where id=".$res['id']);  //确认
				}

			}

				$data['user_id'] = $rss['user_id'];
				$data['uid'] = $rss['uid'];
				$data['action_type'] = '购买配股';
				$data['pdt'] = $nowdate;
				$data['epoints'] = $rss['agent_no'];
				$data['bz'] = '购买成功！';
				$data['did'] = 0;
				$data['allp'] = 0;
				$history ->add($data);

		

		$fck ->query("UPDATE __TABLE__ SET is_aa=2,is_cc=is_cc+{$nums},pg_nums=pg_nums+{$nums},tz_nums=tz_nums+{$ePoints} where id=".$rss['id']);  //确认
		if($rss['is_cc']==0){


		$fck ->query("UPDATE __TABLE__ SET is_cc=is_cc+1 where id=".$rss['re_id']);  //确认
	}
			$fee=M('fee');
			$fee_rs=$fee->where('s6')->find(1);
	    	$s6=$fee_rs['s6'];

			
			$repath=$rss['re_path'];
			$money=$s6*$nums;
			$lirs = $fck->where('id in (0'.$repath.'0)')->field('id,ach')->order('re_level desc')->select();
            foreach ($lirs as $key => $value) {
               $fck->execute("update __TABLE__ set ach=ach+".$money." where id=".$value['id']);
            }

			$fck->pgfh($rss['id'],$rss['re_id']);
			}
			unset($fck,$where,$rs,$history,$data,$rewhere);
			$bUrl = __URL__.'/admin1';
			$this->_box(1,'确认购买！',$bUrl,1);
			
			exit;
		}else{
			$this->error('错误！');
			exit;
		}
	}



	private function _adminAgentsDel($XGid=0){
		//=======================================删除购买
		if ($_SESSION['UrlPTPass'] == 'gpHistory'){
			$fck = M ('fck');
			$rewhere = array();
			$where['is_agent'] = array('gt',0);
			$where['id'] = array ('in',$XGid);
			$rs = $fck -> where($where) -> select();
			foreach ($rs as $rss){
				$fck ->query("UPDATE __TABLE__ SET is_aa=0,is_bb=0 where id>1 and id = ".$rss['id']);
			}
	
			//			$shop->where($where)->delete();
			unset($fck,$where,$rs,$rewhere);
			$bUrl = __URL__.'/admin1s';
			$this->_box('操作成功','删除购买！',$bUrl,1);
			exit;
		}else{
			$this->error('错误!');
			exit;
		}
	}

	public function tradingfloor() { //交易大厅
		$id	= $_SESSION[C('USER_AUTH_KEY')];
		$GPmj = M('gupiao');
		$fck = M('fck');
		$fee = M ('fee');

		$ttrs = $GPmj->where('id>0')->field('id,uid')->select();
		foreach($ttrs as $tors){
			$thid = $tors['id'];
			$tuid = $tors['uid'];
			$cs=$fck->where('id='.$tuid)->field('id,user_id')->find();
			if(!$cs){
				$GPmj->where('id='.$thid)->delete();
			}
		}

		$rs = $fee->find();
		$one_price = $rs['str1'];
		$dan = $rs['str2'];

		$ys_gp = $rs['str8'];
		$pt_gp = $rs['str24'];
		$gj_gp = $rs['str25'];

		$this -> assign('ys_gp',$ys_gp);
		$this -> assign('pt_gp',$pt_gp);
		$this -> assign('gj_gp',$gj_gp);

		$id = $_SESSION[C('USER_AUTH_KEY')];

		import ( "@.ORG.ZQPage" );  //导入分页类

		$where = 'xt_gupiao.uid>0 and xt_gupiao.lnum>0 and xt_gupiao.type=1 and xt_gupiao.status=0 and xt_gupiao.ispay=0'; //出售
		$map = 'xt_gupiao.uid>0 and xt_gupiao.type=0 and xt_gupiao.ispay=0';//求购

		$field = 'xt_gupiao.*';
		$field .= ',xt_fck.nickname,xt_fck.user_id';
		$join = 'left join xt_fck ON xt_fck.id=xt_gupiao.uid'; //连表查询

		$count = $GPmj->where($where)->field($field)->join($join)->Distinct(true)->count();//出售总页数
		$count1 = $GPmj->where($map)->field($field)->join($join)->Distinct(true)->count();//求购总页数

		$listrows = 15;//每页显示的记录数

		$Page = new ZQPage($count,$listrows,1);
		//===============(总页数,每页显示记录数,css样式 0-9)
		$show = $Page->show();//分页变量

		$this->assign('page',$show);//分页变量输出到模板
		$Page1 = new ZQPage($count1,$listrows,1);
		//===============(总页数,每页显示记录数,css样式 0-9)
		$show1 = $Page1->show();//分页变量
		$this->assign('page1',$show1);//分页变量输出到模板
		$list = $GPmj->where($where)->field($field)->join($join)->Distinct(true)->order('ispay asc,eDate asc,id asc')->page($Page->getPage().','.$listrows)->select();
		$list1 = $GPmj->where($map)->field($field)->join($join)->Distinct(true)->order('ispay asc,eDate asc,id asc')->page($Page1->getPage().','.$listrows)->select();
		foreach($list1 as $vov){

//			$can_b[$vov['id']] = floor($vov['buy_s']/$vov['one_price']);
			$can_b[$vov['id']] = floor($vov['buy_s']/$one_price);

		}
		$this->assign('can_b', $can_b);

		// $this->assign('one_price', $one_price);
		$this->assign('list', $list);
		// $this->assign('list1', $list1);
		$this->display('tradingfloor');
		
	}

	public function alllistGP() { //CQ币买卖界面
		$id	= $_SESSION[C('USER_AUTH_KEY')];
		$GPmj = M('gupiao');
		$fck = M('fck');
		$fee = M ('fee');

		$ttrs = $GPmj->where('id>0')->field('id,uid')->select();
		foreach($ttrs as $tors){
			$thid = $tors['id'];
			$tuid = $tors['uid'];
			$cs=$fck->where('id='.$tuid)->field('id,user_id')->find();
			if(!$cs){
				$GPmj->where('id='.$thid)->delete();
			}
		}

		$rs = $fee->find();
		//当前股价
		$one_price = $rs['gp_one'];

		$id = $_SESSION[C('USER_AUTH_KEY')];

		import ( "@.ORG.ZQPage" );  //导入分页类

		$where = 'xt_gupiao.uid>0 and xt_gupiao.type=1 and xt_gupiao.status=0'; //出售
		$map = 'xt_gupiao.uid>0 and xt_gupiao.type=0 and xt_gupiao.ispay=0';//求购

		$field = 'xt_gupiao.*';
		$field .= ',xt_fck.nickname,xt_fck.user_id';
		$join = 'left join xt_fck ON xt_fck.id=xt_gupiao.uid'; //连表查询

		$count = $GPmj->where($where)->field($field)->join($join)->Distinct(true)->count();//出售总页数
		$count1 = $GPmj->where($map)->field($field)->join($join)->Distinct(true)->count();//求购总页数

		$listrows = 15;//每页显示的记录数

		$Page = new ZQPage($count,$listrows,1);
		//===============(总页数,每页显示记录数,css样式 0-9)
		$show = $Page->show();//分页变量

		$this->assign('page',$show);//分页变量输出到模板

		$Page1 = new ZQPage($count1,$listrows,1);
		//===============(总页数,每页显示记录数,css样式 0-9)
		$show1 = $Page1->show();//分页变量
		$this->assign('page1',$show1);//分页变量输出到模板
		$list = $GPmj->where($where)->field($field)->join($join)->Distinct(true)->order('ispay asc,one_price asc,eDate asc,id asc')->page($Page->getPage().','.$listrows)->select();
		$list1 = $GPmj->where($map)->field($field)->join($join)->Distinct(true)->order('ispay asc,one_price asc,eDate asc,id asc')->page($Page1->getPage().','.$listrows)->select();
		foreach($list1 as $vov){

			$can_b[$vov['id']] = floor($vov['buy_s']/$one_price);

		}
		$this->assign('can_b', $can_b);

		$this->assign('one_price', $one_price);
		$this->assign('list', $list);
		$this->assign('list1', $list1);

		$gp = M('gp');
		$ts = $gp->where('id>0')->find();
		$all_num = $ts['turnover'];

		$all_num = number_format($all_num,0,"",",");
		$this->assign('all_num', $all_num);//总成交量

		$this->display('alllistGP');
		exit;
	}

	//购买原始股
	public function buyyuans() {
		if(!empty($_SESSION[C('USER_AUTH_KEY')])){
			$GPmj = M('gupiao');
			$fck = M('fck');
			$fee = M('fee')->field('str1,str8')->find();
			$close_gp	= $fee['str8'];//原始CQ币开关,1为关闭
			if($close_gp==1){
				$this->error("原始CQ币尚未开放交易！");
				exit;
			}

			$one_price = 0.1;
			$gp_info	= $this->gpInfo();//CQ币的信息
			$id = $_SESSION[C('USER_AUTH_KEY')];
			$user_rs = $fck->where("id=$id")->field("agent_use")->find();
			$game_m	= $user_rs['agent_use'];//剩余的注册币
			$this->assign('game_m', $game_m);
			$this->assign('live_gp', $gp_info[6]);//剩余的CQ币
			$this->assign('one_price', $one_price);
			$this->display();
		}else{
			$this->error("错误！");
		}
	}

	//购买原始股处理
	public function buyyuansAC() {
		if(!empty($_SESSION[C('USER_AUTH_KEY')])){
			$one_price = $_POST['one_price'];//表单传来的CQ币单价

			$fck = M('fck');
			$fee = M('fee');
			$frse = $fee->field('str1,str8,str9')->find();
			$close_gp	= $frse['str8'];//原始CQ币开关,1为关闭
			$yuan_num	= $frse['str9'];//原始CQ币剩余数量
			if($close_gp==1){
				$this->error("原始CQ币尚未开放交易！");
				exit;
			}

			$id = $_SESSION[C('USER_AUTH_KEY')];
			//检查交易密码
			$user_info	= $fck->where("id=$id")->field("agent_use,user_id,passopentwo")->find();
			$use	= $user_info['agent_use'];//可以的游戏币
			$gp_pwd = trim($_POST['gp_pwd']);
			if(md5($gp_pwd) !=  $user_info['passopentwo']){
				$this->error("三级密码不正确！");
				exit;
			}

			$sNun	= (int)$_POST['sNun'];//购买CQ币的数量

			if (empty ($sNun)) {
				$this->error('购买原始CQ币的数量不能为空或者小于等于0！');
				exit;
			}
			if ($sNun != floor($sNun)) {
				$this->error('温馨提示：您输入数量必须是整数。请检验后再试！');
				exit;
			}

			// if($sNun>$yuan_num){
			// 	$this->error('购买不成功，原始股剩余数量不足！');
			// 	exit;
			// }

			$buy	= $sNun * $one_price;//购买CQ币所需的金额
			$may	= (int)($use/$one_price);

			if (bccomp($buy, $use, 2)>0){
				$this->error('温馨提示：你的注册币账户余额不足 '.$buy.'。请检验后再试！');
				exit;
			}

			$fck->execute("UPDATE __TABLE__ SET yuan_gupiao=yuan_gupiao+$sNun,agent_use=agent_use-$buy WHERE `id`=$id");
			$fee->execute("UPDATE __TABLE__ SET str9=str9-$sNun");

			$bUrl = __URL__ . '/buyyuans';
			$this->_box(1, '恭喜您，原始CQ币购买成功！', $bUrl, 3);

		}else{
			$this->error("错误！");
		}
	}
	
	//检查股票开关是否开发
	private function check_gpopen($type=0){
		$fee_rs = M('fee')->field('gp_kg')->find();
		$gp_kg = $fee_rs['gp_kg'];
		if($type==1){
			if($gp_kg==1){
				$this->error("CQ币尚未开放交易！");
				exit;
			}
		}else{
			$this->assign('close_gp', $gp_kg);
		}
	}
	
	// 求购CQ币列表
	public function buyGPform(){
		if(!empty($_SESSION[C('USER_AUTH_KEY')])){
			
			$gupiao = M('gupiao');
			$fck = M('fck');
			$this->check_gpopen();
			
			$fee_rs = M('fee')->field('gp_one,gp_fxnum,gp_senum')->find();
			//当前股价
			$one_price = $fee_rs['gp_one'];
			$gp_fxnum = $fee_rs['gp_fxnum'];//涨价数量
			$gp_senum = $fee_rs['gp_senum'];//已售出
			$ca_gp_n = $gp_fxnum-$gp_senum;//差多少涨价
			$ca_gp_p = ((int)($one_price*$ca_gp_n*100))/100;//差多少钱涨价
			$this->assign('gp_upnum', $ca_gp_n);
			$this->assign('gp_uppri', $ca_gp_p);
			//CQ币的信息
			$gp_info	= $this->gpInfo();
			//正在求购的CQ币
			$gping_num	= $this->buy_and_ing(0);

			$id = $_SESSION[C('USER_AUTH_KEY')];

			import ( "@.ORG.ZQPage" );  //导入分页类
			$where = 'type=0 and id>0 and uid='.$id;
			$field = '*';

			$count = $gupiao->where($where)->field($field)->count();//总页数
			$listrows = 10;//每页显示的记录数
			$Page = new ZQPage($count,$listrows,1);
			//===============(总页数,每页显示记录数,css样式 0-9)
			$show = $Page->show();//分页变量
			$this->assign('page',$show);//分页变量输出到模板
			$list = $gupiao->where($where)->field($field)->order('eDate desc')->page($Page->getPage().','.$listrows)->select();
			foreach($list as $vvv){
				$buy_s = $vvv['buy_s'];
				$is_pay = $vvv['ispay'];
				if($is_pay==1){
					$can_b = 0;
				}else{
					$can_b = floor($buy_s/$one_price);
				}
				$tvo[$vvv['id']] = $can_b;
			}
			$this->assign('list', $list);
			$this->assign('tvo', $tvo);


			$user_rs = $fck->where("id=$id")->field("agent_gp")->find();
			$game_m	= $user_rs['agent_gp'];//剩余的CQ币交易账户余额
			$this->assign('game_m', $game_m);

			$this->assign('one_price', $one_price);
			$this->assign('live_gp', $gp_info[0]);//剩余的CQ币
			$this->assign('gping_num', $gping_num);//正在求购的CQ币

			$_SESSION['GP_Sesion_Buy'] = 'OK';

			$this->display('buyGPform');
		}else{
			$this->error("错误！");
		}
	}


	public function sellGPform() { //出售CQ币
		if(empty($_SESSION[C('USER_AUTH_KEY')])){
			$this->error("错误");
			exit;
		}
		$GPmj = M('gupiao');
		$gp_sell = M('gp_sell');
		$fck = M('fck');
		
		$fee_rs = M('fee')->field('gp_one,gp_kg')->find();
		//当前股价
		$one_price = $fee_rs['gp_one'];
		$close_gp	= $fee_rs['gp_kg'];//CQ币交易开关,1为关
		//CQ币的信息
		$gp_info	= $this->gpInfo();
		//正在出售的CQ币
		$gping_num	= $this->buy_and_ing(1);

		$id = $_SESSION[C('USER_AUTH_KEY')];

		import ( "@.ORG.ZQPage" );  //导入分页类

		$where = 'type=1 and id>0 and uid='.$id;
		$field = '*';

		$count = $GPmj->where($where)->field($field)->count();//总页数
		$listrows = 15;//每页显示的记录数
		$Page = new ZQPage($count,$listrows,1);
		//===============(总页数,每页显示记录数,css样式 0-9)
		$show = $Page->show();//分页变量
		$this->assign('page',$show);//分页变量输出到模板

		$list = $GPmj->where($where)->field($field)->order('eDate desc')->page($Page->getPage().','.$listrows)->select();
		$user_rs = $fck->where("id=$id")->field("*")->find();

		$this->assign('fck_rs',$user_rs);
		$game_m	= $user_rs['agent_gp'];//剩余的CQ币
		$this->assign('game_m', $game_m);

		$this->assign('one_price',$one_price);
		$this->assign('live_gp', $gp_info[0]);//剩余的CQ币
		$this->assign('gping_num', $gping_num);//正在出售的CQ币
		$this->assign('close_gp',$close_gp);
		$this->assign('list', $list);

		$twhere = array();
		$twhere['uid'] = array('eq',$id);
		$twhere['sNun'] = array('gt',0);
// 		$twhere['is_over'] = array('eq',0);
		$field1 = "*";

		$tcount = $gp_sell->where($twhere)->field($field1)->count();//总页数
		$Page2 = new ZQPage($tcount,$listrows,1);
		//===============(总页数,每页显示记录数,css样式 0-9)
		$show2 = $Page2->show();//分页变量
		$this->assign('page_t',$show2);//分页变量输出到模板

		$list2 = $gp_sell->where($twhere)->field($field1)->order('id desc')->page($Page2->getPage().','.$listrows)->select();
		$this->assign('list_t', $list2);

		$_SESSION['GP_Sesion_Sell'] = 'OK';
		
		$this->display('sellGPform');
		
	}

	public function sellGPform_N() { //出售CQ币
		$this->_Admin_checkUser();
		if(empty($_SESSION[C('USER_AUTH_KEY')])){
			$this->error("错误");
			exit;
		}
		$GPmj = M('gupiao');
		$fck = M('fck');

		$this->check_gpopen();
			
		$fee_rs = M('fee')->field('gp_one')->find();
		//当前股价
		$one_price = $fee_rs['gp_one'];
		//CQ币的信息
		$gp_info	= $this->gpInfo();
		//正在出售的CQ币
		$gping_num	= $this->buy_and_ing(1);

		$id = $_SESSION[C('USER_AUTH_KEY')];

		import ( "@.ORG.ZQPage" );  //导入分页类

		$where = 'type=1 and id>0 and uid='.$id;
		$field = '*';

		$count = $GPmj->where($where)->field($field)->count();//总页数
		$listrows = 15;//每页显示的记录数
		$Page = new ZQPage($count,$listrows,1);
		//===============(总页数,每页显示记录数,css样式 0-9)
		$show = $Page->show();//分页变量
		$this->assign('page',$show);//分页变量输出到模板

		$list = $GPmj->where($where)->field($field)->order('eDate desc')->page($Page->getPage().','.$listrows)->select();
		$user_rs = $fck->where("id=$id")->field("agent_gp")->find();
		$game_m	= $user_rs['agent_gp'];//剩余的CQ币
		$this->assign('game_m', $game_m);

		$this->assign('one_price',$one_price);
		$this->assign('live_gp', $gp_info[0]);//剩余的CQ币
		$this->assign('gping_num', $gping_num);//正在出售的CQ币
		$this->assign('list', $list);

		$aars = $fck->where('id>1')->sum('live_gupiao');
		if(empty($aars))$aars=0;
		$this->assign('aars', $aars);

		$_SESSION['GP_Sesion_Sell'] = 'OK';

		$this->display();
	}

	//CQ币买卖历史
	public function gpHistory(){
		if(!empty($_SESSION[C('USER_AUTH_KEY')]) && ($_SESSION['UrlszUserpass'] == 'gpHistory')){
			$id	= $_SESSION[C('USER_AUTH_KEY')];
			$GPmj = M('hgupiao');
			$fck = M('fck');
			$fee = M ('fee');

			$rs = $fee->find();
			$one_price = $rs['str1'];//当前CQ币价格
			$gp_info	= $this->gpInfo();//CQ币的信息
			$gping_num0	= $this->buy_and_ing(0);//正在求购的CQ币
			$gping_num1	= $this->buy_and_ing(1);//正在出售的CQ币

			import ( "@.ORG.ZQPage" );  //导入分页类

			$where = "uid=$id"; //买卖记录
			$field = '*';

			$count = $GPmj->where($where)->field($field)->count();//出售总页数
			$listrows = 15;//每页显示的记录数

			$Page = new ZQPage($count,$listrows,1);
			//===============(总页数,每页显示记录数,css样式 0-9)
			$show = $Page->show();//分页变量
			$this->assign('page',$show);//分页变量输出到模板

			$list = $GPmj->where($where)->field($field)->order('eDate desc')->page($Page->getPage().','.$listrows)->select();
			$this->assign('one_price', $one_price);

			$this->assign('list', $list);
			$this->assign('live_gp', $gp_info[0]);//剩余的CQ币
			$this->assign('all_in_gp', $gp_info[1]);//成功售出
			$this->assign('all_out_gp', $gp_info[2]);//成功买入
			$this->assign('gping_num0', $gping_num0);//正在求购的CQ币
			$this->assign('gping_num1', $gping_num1);//正在求购的CQ币
			$this->display();
			exit;
		}
	}
	
	public function sellGP_Next() {//出售CQ币
		if(empty($_SESSION[C('USER_AUTH_KEY')])){
			$this->error("错误！");
			exit;
		}
		if(empty($_SESSION['GP_Sesion_Sell'])){
			$this->error("刷新操作错误！");
			exit;
		}
		$id = $_SESSION[C('USER_AUTH_KEY')];
		$hgp = M('hgupiao');
		$gupiao = M('gupiao');//
		$gp_sell = M('gp_sell');//售股信息表
		$fck = D('Fck');
		$fee = M ('fee');

		$this->gpxz_sell_a();

		$fee_rs = $fee ->find();
		$one_price	= $fee_rs['str1'];
		$this->assign('one_price',$one_price);
		$close_gp	= $fee_rs['str3'];//CQ币交易开关,1为关闭
		$this->assign('close_gp',$close_gp);
		$jj_t11	= explode("|",$fee_rs['str13']);//竞价出售开始时间
		$jj_t12	= explode("|",$fee_rs['str14']);//竞价出售结束时间

		$start_t = $jj_t11[0].":".$jj_t11[1].":".$jj_t11[2];//组合分割的时间
		$end_t = $jj_t12[0].":".$jj_t12[1].":".$jj_t12[2];//组合分割的时间

		$time_ss = strtotime($start_t);//运用时间戳对比，开始时间
		$time_se = strtotime($end_t);//结束时间
		$now_time = strtotime(date("H:i:s",time()));//现在时间

		//检查交易密码及其他
		$user_info	= $fck->where("id=".$id)->field("id,live_gupiao,user_id,passopentwo,re_path,max_jy,u_level")->find();

		$my_lv = $user_info['u_level'];//级别

		$cur_one_price	= $fee_rs['str1'];//系统设置的CQ币价格

		$day_week	= $fee_rs['str20'];//运行出售日期

		$use	= $user_info['live_gupiao'];//剩余的CQ币

		if($close_gp == 1){
			$this->error("交易功能已经关闭！");
			exit;
		}

		if($now_time<$time_ss||$now_time>$time_se){
			$this->error("交易竞价出售时间已过！");
			exit;
		}

		$nowweek = date("w");
		if($nowweek==0){
			if(strpos($day_week,',7,')!==false){
			//允许运行
			}else{
				$this->error("今日不允许竞价出售GP！");
				exit;
			}
		}else{
			if(strpos($day_week,','.$nowweek.',')!==false){
				//允许运行
			}else{
				$this->error("今日不允许竞价出售GP！");
				exit;
			}
		}

		$next_min_d = 20;//20天才可再次交易
//		$next_min_d = 0;//20天才可再次交易

		$tid = (int)$_GET['tid'];

		$where = array();
		$where['id'] = array('eq',$tid);
		$where['is_over'] = array('eq',0);
		$where['uid'] = array('eq',$id);

		$trs = $gp_sell->where($where)->find();
		if($trs){
			$tgpid = $trs['id'];
			$gpuid = $trs['uid'];
			$last_d = $trs['sell_date'];
			$next_d = $last_d+3600*24*$next_min_d;
			$now_ddd = mktime();
			if($next_d>$now_ddd){
				$this->error("每笔GP出售后必须等待 ".$next_min_d." 天才能再次出售！");
				exit;
			}
			$ln_num = $trs['sell_ln'];//剩余
			$sell_m = $trs['sell_mon'];//售出总数
			$sell_n = $trs['sell_num'];//售出次数
			$mmsNun = $trs['sNun'];//总量
			$chus = 3-$sell_n;//被除数
			if($sell_n==2){
				$now_sell_num = $ln_num;
				$is_over = 1;
			}else{
				$now_sell_num = floor($ln_num/$chus);
				$is_over = 0;
			}
			$this->assign('tid',$tid);
		}else{
			$this->error("找不到此GP！");
			exit;
		}

		$sNun = $now_sell_num;
		if($sNun>0){
			$this->assign('sNun',$sNun);
		}
		$this->display();
	}

	public function A_sellGP() { //出售CQ币
		if(empty($_SESSION[C('USER_AUTH_KEY')])){
			$this->error("错误！");
			exit;
		}
		if(empty($_SESSION['GP_Sesion_Sell'])){
			$this->error("刷新操作错误！");
			exit;
		}
		$id = $_SESSION[C('USER_AUTH_KEY')];
		$hgp = M('hgupiao');
		$gupiao = M('gupiao');//
		$gp_sell = M('gp_sell');//售股信息表
		$fck = D('Fck');
		$fee = M ('fee');

		$this->gpxz_sell_a();

		

		$fee_rs = $fee ->find();
		$now_price	= $fee_rs['str1'];
//		$one_price = $_POST['one_price'];//表单传来的CQ币单价
		$one_price = $now_price;
		$close_gp	= $fee_rs['str3'];//CQ币交易开关,1为关闭
		
		$max_price = $now_price+0.01;
		$min_price = $now_price-0.01;
		if($one_price>$max_price||$one_price<$min_price){
			$this->error("价格只能在当前价幅度1美分！");
			exit;
		}
		
		$jj_t11	= explode("|",$fee_rs['str13']);//竞价出售开始时间
		$jj_t12	= explode("|",$fee_rs['str14']);//竞价出售结束时间

		$start_t = $jj_t11[0].":".$jj_t11[1].":".$jj_t11[2];//组合分割的时间
		$end_t = $jj_t12[0].":".$jj_t12[1].":".$jj_t12[2];//组合分割的时间

		$time_ss = strtotime($start_t);//运用时间戳对比，开始时间
		$time_se = strtotime($end_t);//结束时间
		$now_time = strtotime(date("H:i:s",time()));//现在时间

		//检查交易密码及其他
		$user_info	= $fck->where("id=".$id)->field("id,live_gupiao,user_id,passopentwo,re_path,max_jy,u_level")->find();

		$my_lv = $user_info['u_level'];//级别

		$cur_one_price	= $fee_rs['str1'];//系统设置的CQ币价格

		$day_week	= $fee_rs['str20'];//运行出售日期

		$use	= $user_info['live_gupiao'];//剩余的CQ币
//		$gp_pwd = trim($_POST['gp_pwd']);
//		if(md5($gp_pwd) !=  $user_info['passopentwo']){
//			$this->error("三级密码不正确！");
//			exit;
//		}

		if($close_gp == 1){
			$this->error("交易功能已经关闭！");
			exit;
		}

		if($now_time<$time_ss||$now_time>$time_se){
			$this->error("交易竞价出售时间已过！");
			exit;
		}

		$nowweek = date("w");
		if($nowweek==0){
			if(strpos($day_week,',7,')!==false){
			//允许运行
			}else{
				$this->error("今日不允许竞价出售GP！");
				exit;
			}
		}else{
			if(strpos($day_week,','.$nowweek.',')!==false){
				//允许运行
			}else{
				$this->error("今日不允许竞价出售GP！");
				exit;
			}
		}

		$next_min_d = 20;//20天才可再次交易
//		$next_min_d = 0;//20天才可再次交易

		$tid = (int)$_GET['tid'];

		$where = array();
		$where['id'] = array('eq',$tid);
		$where['is_over'] = array('eq',0);
		$where['uid'] = array('eq',$id);

		$trs = $gp_sell->where($where)->find();
		if($trs){
			$tgpid = $trs['id'];
			$gpuid = $trs['uid'];
			$last_d = $trs['sell_date'];
			$next_d = $last_d+3600*24*$next_min_d;
			$now_ddd = mktime();
			if($next_d>$now_ddd){
				$this->error("每笔GP出售后必须等待 ".$next_min_d." 天才能再次出售！");
				exit;
			}
			$ln_num = $trs['sell_ln'];//剩余
			$sell_m = $trs['sell_mon'];//售出总数
			$sell_n = $trs['sell_num'];//售出次数
			$mmsNun = $trs['sNun'];//总量
			$chus = 3-$sell_n;//被除数
			if($sell_n==2){
				$now_sell_num = $ln_num;
				$is_over = 1;
			}else{
				$now_sell_num = floor($ln_num/$chus);
				$is_over = 0;
			}

			$last_sellmon = $sell_m+$now_sell_num;
			$last_sellnum = $sell_n+1;
			$last_ln = $ln_num-$now_sell_num;

			$s_pid = $trs['id'];
			if($last_sellnum<3){
				$s_last = 0;
			}else{
				$s_last = 1;
			}

		}else{
			$this->error("找不到此GP！");
			exit;
		}

		$swh = array();
		$swh['id'] = array('eq',$tgpid);
		$swh['is_over'] = array('eq',0);
		$swh['uid'] = array('eq',$id);

		$valuearr = array(
					'sell_ln'	=> $last_ln,
					'sell_mon'	=> $last_sellmon,
					'sell_num'	=> $last_sellnum,
					'sell_date'	=> mktime(),
					'is_over'	=> $is_over
					);

		$gp_sell->where($swh)->setField($valuearr);

		$sNun = $now_sell_num;
		if($sNun>0){
			$this->sell_GPAC($id,$user_info['user_id'],$sNun,$s_pid,$s_last,$one_price);
		}

		$_SESSION['GP_Sesion_Sell'] = "";

		$bUrl = __URL__ . '/sellGPform';
		$this->_box(1, '出售GP成功！', $bUrl, 3);
		exit;
	}
	
	public function force_sell_gp() { //出售CQ币
		if(!empty($_SESSION[C('USER_AUTH_KEY')])){
			set_time_limit(0);//是页面不过期
			$fck = D('Fck');
			$fee = M ('fee');
			$gupiao = M('gupiao');
			
			$this->check_gpopen(1);
			
			//检查交易密码及其他
			$user_info	= $fck->where("live_gupiao>0 and is_pay>0 and id>1")->field("id,live_gupiao,user_id,passopen,cpzj,u_level")->order('pdt asc,id asc')->select();
			foreach($user_info as $lrs){
				
				$fee_rs = $fee ->find();
				$cur_one_price	= $fee_rs['gp_one'];//系统设置的CQ币价格
				$str12	= $fee_rs['str12'];
				$s7 = explode("|",$fee_rs['s7']);
				
				$max_num	= $str12;
				$nowdate = strtotime(date('Y-m-d'));
				
				$id = $lrs['id'];
				$cpzj = $lrs['cpzj'];
				$ulevel = $lrs['u_level'];
				$prii = $s7[$ulevel-1];
				$maxcyed = $cpzj * $prii;//最大可持有额度
								
				$use	= $lrs['live_gupiao'];//剩余的CQ币
				$gpjiazhi = $use * $cur_one_price; 
				
				$where = 'uid>0 and sNun>0 and type=1 and status=0 and is_cancel=0 and eDate>='.$nowdate; 
				$gpnum = $gupiao->where($where)->sum('sNun');
				if(empty($gpnum)){$gpnum=0;}
				// $max_num = $str12 - $gpnum;
				// if($max_num<0){
				// 	$max_num = 0;
				// }
				
				if($gpjiazhi>=$maxcyed){  //当前持有CQ币价值大于可持有额度时
					$use_yes = (int)($use * 0.2);//剩余的CQ币
					
					$sNun = $use_yes;//出售CQ币的数量
			
					if (empty ($sNun)||$sNun<=0) {
						$sNun = 0;
					}
					if ($sNun>$max_num) {
						$sNun = $max_num;
					}
					
					if ($sNun == floor($sNun) && $sNun>0) {
					
						//更新卖方的CQ币信息
						$fck->execute("UPDATE __TABLE__ SET live_gupiao=live_gupiao-".$sNun." WHERE `id`=".$id."");
						$this->sell_GPAC($id,$lrs['user_id'],$sNun);
				
				
						$_SESSION['GP_Sesion_Buy'] = "OK";
						//有人售出股票后排队电子币自动购买CQ币
						$frs = $fck->where('agent_gp>0')->order('pdt asc,id asc')->select();
						foreach ($frs as $vo){
							$fgpid = $vo['id'];
							$agent_gp = $vo['agent_gp'];
							
							$this->auto_buyGP($fgpid,$agent_gp);
						}
					}
				}
			}
			unset($lrs);
			
			$bUrl = __URL__ . '/sellGPform_N';
			$this->_box(1, '强制出售成功。', $bUrl, 3);
			exit;
		}
	}

	public function sellGP() { //出售CQ币
		if(empty($_SESSION[C('USER_AUTH_KEY')])){
			$this->error("错误！");
			exit;
		}
		if(empty($_SESSION['GP_Sesion_Sell'])){
			$this->error("刷新操作错误！");
			exit;
		}
		set_time_limit(0);//是页面不过期
		
		$id = $_SESSION[C('USER_AUTH_KEY')];
		$fck = D('Fck');
		$fee = M ('fee');
		$gupiao = M('gupiao');
		
		$this->check_gpopen(1);

	
		
		$fee_rs = $fee ->find();
		$cur_one_price	= $fee_rs['gp_one'];//系统设置的CQ币价格
		

		//检查交易密码及其他
		$user_info	= $fck->where("id=".$id)->field("*")->find();
		$use	= $user_info['in_gupiao'];//剩余的
		$out	= $user_info['out_gupiao'];//剩余的
		$buy	= $user_info['buy_gupiao'];//剩余的
		$flat	= $user_info['flat_gupiao'];//剩余的
		$give	= $user_info['give_gupiao'];//剩余的
		
		$use_yes = (int)$use;//剩余的CQ币
		
		$gp_pwd = trim($_POST['gp_pwd']);
		if(md5($gp_pwd) !=  $user_info['passopen']){
			$this->error("二级密码不正确！");
			exit;
		}
		
		$now_time = strtotime(date('Y-m-d'));
		$smap['uid'] = $id;
		$smap['type'] = 1;
		//$smap['eDate'] = array('egt',$now_time);
		$tdgrs = $gupiao->where($smap)->order('id desc')->find();


		$sNun	= trim($_POST['sNun']);//出售股票的数量
		$pay	= trim($_POST['TPL']);//出售股票的类型
		$one_price	= trim($_POST['one_price']);//卖出价格
		if (empty ($sNun)||$sNun<=0) {
			$this->error('出售的数量不能为空或者小于等于0！');
			exit;
		}
		if ($sNun != floor($sNun)) {
			$this->error('温馨提示：您输入数量必须是整数。请检验后再试！');
			exit;
		}
		
		if ($sNun > $use_yes && $pay==5) {
			$this->error('温馨提示：您目前最多可以出售 ' . $use_yes . ' 个内部认购股。请检验后再试！');
			exit;
		}

			if ($sNun > $out && $pay==1) {
			$this->error('温馨提示：您目前最多可以出售 ' . $out . ' 个投资配股。请检验后再试！');
			exit;
		}

			if ($sNun > $buy && $pay==2) {
			$this->error('温馨提示：您目前最多可以出售 ' . $buy . ' 个买入股票。请检验后再试！');
			exit;
		}

			if ($sNun > $flat && $pay==3) {
			$this->error('温馨提示：您目前最多可以出售 ' . $flat . ' 个平仓配股。请检验后再试！');
			exit;
		}

			if ($sNun > $give && $pay==4) {
			$this->error('温馨提示：您目前最多可以出售 ' . $give . ' 个赠送股票。请检验后再试！');
			exit;
		}
		
		if($pay==5){
			$fck->execute("UPDATE __TABLE__ SET in_gupiao=in_gupiao-".$sNun." WHERE `id`=".$id."");
		}

		if($pay==1){
			$fck->execute("UPDATE __TABLE__ SET out_gupiao=out_gupiao-".$sNun." WHERE `id`=".$id."");
		}

		if($pay==2){
			$fck->execute("UPDATE __TABLE__ SET buy_gupiao=buy_gupiao-".$sNun." WHERE `id`=".$id."");
		}

		if($pay==3){
			$fck->execute("UPDATE __TABLE__ SET flat_gupiao=flat_gupiao-".$sNun." WHERE `id`=".$id."");
		}

		if($pay==4){
			$fck->execute("UPDATE __TABLE__ SET give_gupiao=give_gupiao-".$sNun." WHERE `id`=".$id."");
		}



		//更新卖方的CQ币信息
		
		$this->sell_GPAC($id,$user_info['user_id'],$sNun,0,0,$one_price,$pay);


		$_SESSION['GP_Sesion_Buy'] = "OK";
		//有人售出股票后排队电子币自动购买CQ币
		// $frs = $fck->where('agent_gp>0')->order('pdt asc,id asc')->select();
		// foreach ($frs as $vo){
		// 	$agent_gp = $vo['agent_gp'];
		// 	$fgpid = $vo['id'];
			
		// 	$this->auto_buyGP($fgpid,$agent_gp);
		// }
		
		
		$_SESSION['GP_Sesion_Buy'] = "";
		$_SESSION['GP_Sesion_Sell'] = "";

		$bUrl = __URL__ . '/sellGPform';
		$this->_box(1, '股票卖出成功。', $bUrl, 3);
		exit;
	}

	public function sell_GPAC($uid,$user_id,$sNunb=0,$spid=0,$ssn=0,$o_pri=0,$pay=0){

		$fck = D('Fck');
		$gupiao = M('gupiao');
		$fee = M('fee');
		$game = D('Game');
		
		$this->check_gpopen(1);
		
		$one_price = $o_pri;
		$fee_rs = $fee ->find();
		$now_price	= $fee_rs['gp_one'];//系统设置的CQ币价格
		if(empty($one_price)){
			$one_price = $now_price;
		}

		$sNunb = (int)$sNunb;//卖出数
		$ok_sell = 0;//成功卖出数
		$ok_over = 0;//结束卖操作
		while($ok_over==0){

			$sNun = $sNunb-$ok_sell;
			if($sNun>0){
				$map = array();
				$map['type']	= array('eq',0);//求购CQ币的标识
				$map['status']	= array('eq',0);//没有作废的标识
				$map['ispay']	= array('eq',0);//没有交易完成的标识
				$map['is_en']	= array('eq',0);//标准CQ币
				$map['uid']		= array('neq',$uid);//不能交易给自己
//				$map['one_price']	= array('eq',$one_price);//价格
				$order	= "eDate asc,id asc";//时间先后顺序
				$list_gp	= $gupiao->where($map)->field("*")->order($order)->find();
				if($list_gp){
					$gpid	= $list_gp['id'];
					$gpuid  = $list_gp['uid'];
					$buy_s  = $list_gp['buy_s'];//剩余总值
					$scan_b = floor($buy_s/$one_price);//当前购买力

					if($scan_b == 0){//说明该交易完成了【再判断一次，以防程序出错】
						$gupiao->query("update __TABLE__ set ispay=1 where id=".$gpid);
						sleep(1);//休眠1秒以免程序运行过快数据未处理;
					}

					$lnum	= $scan_b;//剩余
					$i_ispay = $list_gp['ispay'];//成功标签
					$buy_a = $list_gp['buy_a'];//已购买总额
					$buy_nn = $list_gp['buy_nn'];//已购买量
					if($lnum<=$sNun){

						$us_money = $lnum*$one_price;//使用额度

						$s_buy_s = $buy_s-$us_money;//过后剩余总值
						$s_buy_a = $buy_a+$us_money;//过后已购买总额
						$s_buy_nn = $buy_nn+$lnum;//过后已购买量

						$s_ispay = 1;
						$se_numb = $lnum;
					}else{

						$us_money = $sNun*$one_price;//使用额度

						$s_buy_s = $buy_s-$us_money;//过后剩余总值
						$s_buy_a = $buy_a+$us_money;//过后已购买总额
						$s_buy_nn = $buy_nn+$sNun;//过后已购买量

						$s_ispay = 0;
						$se_numb = $sNun;
					}
					$do_where = "id=".$gpid." and buy_a=".$buy_a." and buy_nn=".$buy_nn." and buy_s=".$buy_s." and ispay=".$i_ispay."";
					$do_sql = "update __TABLE__ set buy_s=".$s_buy_s.",buy_a=".$s_buy_a.",buy_nn=".$s_buy_nn.",ispay=".$s_ispay." where ".$do_where;
					$do_relute = $gupiao->execute($do_sql);//返回影响的行数

					if($do_relute!=false){//上一个语句是否存在行数

						if($s_ispay==1){
							//自动生成卖出信息
							$this->addsell_gp($gpuid,$s_buy_nn,$one_price);
							if($s_buy_s>0){
								$fck->execute("UPDATE __TABLE__ SET agent_gp=agent_gp+".$s_buy_s.",agent_lock=agent_lock+".$s_buy_s." WHERE `id`=".$gpuid."");
							}
						}

						$ok_sell = $ok_sell+$se_numb;

						//更新对方的CQ币信息
						$fck->execute("UPDATE __TABLE__ SET live_gupiao=live_gupiao+".$se_numb.",all_in_gupiao=all_in_gupiao+".$se_numb." WHERE `id`=".$gpuid."");
						
						//记录成功交易的CQ币信息
						$this->gpSuccessed($gpuid,$se_numb,0,$fee_rs,$gpid,0,0,$uid);

					}
				}else{
					$ok_over = 1;
				}
				unset($list_gp);
			}else{
				$ok_over = 1;
			}
		}



		$id = $uid;


		$ok_sell=$sNunb;
		//更新自己的售股信息
		$data['uid'] = $uid;
		$data['one_price'] = $one_price;
		$data['price'] = $sNunb*$one_price;//总得CQ币金额
		$data['sNun'] = $sNunb;//总的CQ币数
		$data['used_num'] = $ok_sell;//成功买到的CQ币
		$data['lnum'] = $ok_sell;//还差没有售出的CQ币
		$data['ispay'] = 0;//交易是否完成
		$data['eDate'] = mktime();//售出时间
		$data['status'] = 0;//这条记录有效
		$data['type'] = 1;//标识为售出
		$data['is_en'] = 0;//标准股
		$data['spid'] = $spid;//原卖出记录ID
		$data['last_s'] = $ssn;//是否最后一次卖出
		$data['sell_g'] = $ok_sell*$one_price;//售出获得总额
		$data['tpl'] = $pay;//售出类型
		
		$resid = $gupiao->add($data);//添加记录
		//记录成功交易的CQ币信息
		if($ok_sell>0){
			$this->gpSuccessed($uid,$ok_sell,1,$fee_rs,$resid,0,1);
		}
		
		$this->sellOutGp($id,0,$ok_sell,$fee_rs,$game,$sNunb);
		
		unset($fck,$fee,$gupiao,$game,$fee_rs);
	}
	
	//购买处理
	public function buyGP(){
		if(empty($_SESSION[C('USER_AUTH_KEY')])){
			$this->error("错误！");
			exit;
		}
		if(empty($_SESSION['GP_Sesion_Buy'])){
			$this->error("刷新操作错误！");
			exit;
		}
		$id = $_SESSION[C('USER_AUTH_KEY')];
		$gupiao = M('gupiao');//购股信息表
		$fck = M('fck');
		$fee = M ('fee');
		$gp = M('gp');
		
		//$this->check_gpopen(1);

		$one_price = $_POST['one_price'];//表单传来的CQ币单价

		$fee_rs = $fee ->find();
		$cur_one_price	= $fee_rs['gp_one'];//系统设置的CQ币价格
		$gp_fxnum = $fee_rs['gp_fxnum'];//涨价数量
		$gp_senum = $fee_rs['gp_senum'];//已售出
		$ca_gp_n = $gp_fxnum-$gp_senum;//差多少涨价
		$ca_gp_p = ((int)($cur_one_price*$ca_gp_n*100))/100;
		
		//检查交易密码
		$user_info	= $fck->where("id=$id")->field("agent_gp,agent_lock,user_id,passopen")->find();
		$myuser_id = $user_info['user_id'];
		$use	= $user_info['agent_gp'];//可以的游戏币
		$gp_pwd = trim($_POST['gp_pwd']);
		if(md5($gp_pwd)!= $user_info['passopen']){
			$this->error("二级密码不正确！");
			exit;
		}
// 		if($user_info['agent_lock']<=0){
// 			$this->error("等待循环出局，不能再购买CQ币！");
// 			exit;
// 		}

		$buy_mm	= trim($_POST['sNun']);//购买CQ币总金额
		$sNun	= (int)($this->numb_duibi($buy_mm,$cur_one_price));

		if (empty ($sNun)||$sNun<=0) {
			$this->error('购买CQ币的数量不能为空或者小于等于0！');
			exit;
		}
		if($sNun>$ca_gp_n){
			$this->error('距离下次涨价只能购买'.$ca_gp_n.'CQ币（折合币:'.$ca_gp_p.'），请检验后再试！');
			exit;
		}
		if (bccomp($buy_mm, $use, 2)>0){
			$this->error('你的电子币账户余额不足 '.$buy_mm.'。请检验后再试！');
			exit;
		}
// 		if($user_info['agent_lock']<$buy_mm){
// 			$this->error("您账户级别最多只能再购买价值：".$user_info['agent_lock']."的CQ币！");
// 			exit;
// 		}

		//股票交易
		$this->buy_GPAC($id,$myuser_id,$buy_mm);

		$_SESSION['GP_Sesion_Buy'] = "";

		$bUrl = __URL__ . '/buyGPform';
		$this->_box(1, '求购提交完成！', $bUrl, 3);
	}
	
	//购买处理
	public function auto_buyGP(){
	
		set_time_limit(0);//是页面不过期

		$gupiao = M('gupiao');//购股信息表
		$fck = M('fck');
		$fee = M('fee');
		$gp  = M('gp');

		$where = 'uid<>'.$id.' and lnum>0 and type=1 and status=0 and ispay=0 and is_cancel=0'; 
		$gpnum = $gupiao->where($where)->sum('lnum');
		if(empty($gpnum)){$gpnum=0;}

		$id = (int)$_GET['id'];
		$time=(int)$_GET['time'];

		$res=$gupiao->where('uid='.$id.' and eDate='.$time)->find();
		$money=$res['sell_g'];
		$sNun=$res['sNun'];



		$fee_rs = $fee ->find();
		$cur_one_price	= $fee_rs['gp_one'];//系统设置的CQ币价格
		$gp_fxnum = $fee_rs['gp_fxnum'];//涨价数量
		$gp_senum = $fee_rs['gp_senum'];//已售出
		$ca_gp_n = $gp_fxnum-$gp_senum;//差多少涨价
		//echo "-";
		$ca_gp_p = ((int)($cur_one_price*$ca_gp_n*100))/100;
	
		//检查交易密码
		$uid	= $_SESSION[C('USER_AUTH_KEY')];

		$user_info	= $fck->where("id=$uid")->field("agent_use,agent_lock,user_id,passopen")->find();
		
		$myuser_id = $user_info['user_id'];
		$use	= $user_info['agent_use'];//可以的积分
		$gp_pwd = trim($_POST['gp_pwd']);

		$buy_mm	= $money;//购买CQ币总金额
	

		if ($buy_mm>$use){
			$this->error('可用积分不足');
			exit;
		}
		if($buy_mm>0 && $sNun>0){

			//股票交易
			$this->buy_GPAC($uid,$myuser_id,$buy_mm,$sNun,$time);
			//交易后自动更新股价
			$this->gp_up_down_pd();
		}
		
	}
	
	//购买交易算法
	public function buy_GPAC($uid,$user_id,$bmoney=0,$snun=0,$time=0){

		$fck = D('Fck');
		$gupiao = M('gupiao');
		$fee = M('fee');
		$game = D('Game');
		$myid = $_SESSION[C('USER_AUTH_KEY')];
		if($uid==$myid){
			$this->error('不能购买自己卖出的！');
			exit;
		}
		$fee_rs = $fee ->find();
		$bmoney = $bmoney;//求购总值

		$sNunb = (int)$snun;//买入数
		$ok_buy = 0;//成功买入数
		$ok_over = 0;//结束买操作
		while($ok_over==0){
			$sNun = $sNunb-$ok_buy;
			if($sNun>0){

				$map = array();
				$map['eDate']	= array('eq',$time);//售出CQ币的标识
				$map['type']	= array('eq',1);//售出CQ币的标识
				$map['status']	= array('eq',0);//没有作废的标识
				$map['ispay']	= array('eq',0);//没有交易完成的标识
				$map['uid']		= array('neq',$uid);//不能交易给自己
//				$map['one_price']	= array('eq',$one_price);//价格
				$order	= "eDate asc,id asc";//时间先后顺序
				$list_gp	= $gupiao->where($map)->field("*")->order($order)->find();
			
				if($list_gp){
					$gpid	= $list_gp['id'];
					$gpuid  = $list_gp['uid'];
					if($list_gp['lnum'] == 0){//说明该交易完成了【再判断一次，以防程序出错】
						$gupiao->query("update __TABLE__ set ispay=1 where id=".$gpid);
						usleep(500000);//休眠5000毫秒以免程序运行过快数据未处理;
					}

					$ussNun	= $list_gp['sNun'];//全部
					$used_num	= $list_gp['used_num'];//已使用
					$lnum	= $list_gp['lnum'];//剩余

					$sell_g = $list_gp['sell_g'];//已售出总额
					$i_ispay = $list_gp['ispay'];//成功标签
					if($lnum<=$sNun){
					
						$us_money = $lnum*$one_price;//使用额度

						$s_sell_g = $sell_g+$us_money;//过后已售出总额
						$s_used_n = $ussNun;//过后已使用
						$s_lnum = 0;//过后剩余
						$s_ispay = 1;
						$se_numb = $lnum;
					}else{

						
						$us_money = $sNun*$one_price;//使用额度

						$s_sell_g = $sell_g+$us_money;//过后已售出总额
						$s_used_n = $used_num+$sNun;//过后已使用
						$s_lnum = $lnum-$sNun;//过后剩余
						$s_ispay = 0;
						$se_numb = $sNun;
					}
					
					$do_where = "id=".$gpid." and eDate=".$time." and ispay=".$i_ispay."";
					$do_sql = "update __TABLE__ set used_num=".$s_used_n.",lnum=".$s_lnum.",sell_g=".$s_sell_g.",ispay=".$s_ispay.",pid=".$myid." where ".$do_where;
					$do_relute = $gupiao->execute($do_sql);//返回影响的行数
					
					if($do_relute!=false){//上一个语句是否存在行数
					
						
						$ok_buy = $ok_buy+$se_numb;

						//更新对方成功出售的CQ币信息
						$this->sellOutGp($gpuid,$gpid,$se_numb,$fee_rs,$game,$bmoney);
						//记录成功交易的CQ币信息
						$this->gpSuccessed($gpuid,$se_numb,1,$fee_rs,$gpid,0,0,$uid);

					}
				}else{
					$ok_over = 1;
				}
				unset($list_gp);
			}else{
				$ok_over = 1;
			}
		}
		$id = $uid;

		$lv_nnm = $sNunb - $ok_buy;
		$all_bm = $ok_buy*$one_price;//购买总金额
		$lv_money = $bmoney-$all_bm;//差额

		//更新自己的购股信息
		$data['uid'] = $id;
		$data['one_price'] = $one_price;
		$data['price'] = $bmoney;//总金额
		$data['sNun'] = 0;//总的CQ币数
		$data['used_num'] = 0;//成功买到的CQ币
		$data['lnum'] = 0;//还差没有买到的CQ币
		$data['ispay'] = ($lv_nnm<=0)?1:0;//交易是否完成
		$data['eDate'] = mktime();//购买时间
		$data['status'] = 0;//这条记录有效
		$data['type'] = 0;//标识为求股
		$data['is_en'] = 0;//标准股

		$data['buy_a'] = $all_bm;
		$data['buy_nn'] = $ok_buy;
		$data['buy_s'] = $lv_money;
		if($lv_nnm==0){
			if($lv_money>0){
				$fck->execute("UPDATE __TABLE__ SET agent_use=agent_use+".$lv_money." WHERE `id`=".$id."");
			}
		}
		$resid = $gupiao->add($data);//添加记录
		
		//记录成功交易的CQ币信息
		if($ok_buy>0){
			
			$this->gpSuccessed($id,$ok_buy,0,$fee_rs,$resid,0,1);
		}
		//小于零时，自动生成卖出信息
		$lv_n = $sNunb - $ok_buy;
		if($sNunb>0){
			$this->addsell_gp($id,$sNunb,$one_price);
		}
		$gm = $sNunb*$one_price;//购股所花费的金额
		$hm	= $ok_buy*$one_price;//已经用在买CQ币上的钱
		$game->updateGameCash($id,$hm);
		//更新fck表中信息
		$fck->execute("UPDATE __TABLE__ SET buy_gupiao=buy_gupiao+".$ok_buy." WHERE `id`=".$id."");
		$fck->rw_bonus($id,$gpuid,4,$ok_buy);
		// $fck->execute("UPDATE __TABLE__ SET agent_lock=0 WHERE `id`=".$id." and agent_lock<0");

		$bUrl = __URL__ . '/selllist';
		$this->_box(1, '购买成功！', $bUrl, 3);
		unset($fck,$fee,$gupiao,$game,$fee_rs);
	}






	public function delbuyGP() {
		$del = M('gupiao');
		$fck = M('fck');
		$gp_sell = M('gp_sell');
		$id	= $_SESSION[C('USER_AUTH_KEY')];

		if(empty($id)){
			$this->error("您的登录状态过期！");
			exit;
		}

		$where['id'] = $_GET['id'];
		$where['uid'] = $id;
		//选出该条记录的信息
		$del_info = $del->where($where)->field("*")->find();
		if(empty($del_info)){
			$this->error("没有找到符合条件的记录");
			exit;
		}

		$buy_s	= $del_info['buy_s'];//剩余总价

		$fck->execute("UPDATE __TABLE__ SET agent_gp=agent_gp+".$buy_s." WHERE `id`=".$del_info['uid']);
		$bUrl = __URL__ . '/buyGPform';

		//撤销的话要更新股票表
		$data['ispay']	= 1;
		$data['is_cancel']	= 1;

		$rs = $del->where($where)->save($data);
		if ($rs) {

			$sNunb = $del_info['buy_nn'];
			$wdata = array();
			$wdata['uid'] = $id;
			$wdata['sNun'] = $sNunb;
			$wdata['eDate'] = mktime();
			$wdata['sell_ln'] = $sNunb;
			$gp_sell->add($wdata);

			$this->_box(1, '撤销成功！', $bUrl, 1);
		}else{
			$this->error('撤销失败');
		}

	}

	public function delsellGP() {
		$del = M('gupiao');
		$fck = M('fck');
		$id	= $_SESSION[C('USER_AUTH_KEY')];

		if(empty($id)){
			$this->error("您的登录状态过期！");
			exit;
		}

		$where['id'] = $_GET['id'];
		$where['uid'] = $id;
		//选出该条记录的信息
		$del_info = $del->where($where)->field("*")->find();
		if(empty($del_info)){
			$this->error("没有找到符合条件的记录");
			exit;
		}

		$sNun	= $del_info['sNun'];//总得交易数
		$used_num	= $del_info['used_num'];//成功成交得数量
		$lnum	= $del_info['lnum'];//余下的数量

		if ($lnum+$used_num != 0){
			//交易成功跟余下的数量不等于0
			if($lnum+$used_num != $sNun){
				$this->error("该条信息记录有误，请和管理员联系");
				exit;
			}
		}

		//没有售出的那部分股票还给他
		$fck->execute("UPDATE __TABLE__ SET in_gupiao=in_gupiao+".$lnum." WHERE `id`=".$del_info['uid']);
		$bUrl = __URL__ . '/sellGPform_N';

		$cx_content = "撤销出售 ".$lnum." 个";

		//撤销的话要更新股票表
		$data['ispay']	= 1;
		$data['is_cancel']	= 1;
		$data['sNun']	= $del_info['used_num'];
		$data['lnum']	= 0;
		$data['bz']	= $cx_content;
		$rs = $del->where($where)->save($data);
		if($rs){
			$this->_box(1, '撤销成功！', $bUrl, 1);
		}else{
			$this->error('撤销失败');
		}

	}

	public function us_delsellgpAC() {
		$del = M('gupiao');
		$fck = M('fck');
		$gp_sell = M('gp_sell');
		$id	= $_SESSION[C('USER_AUTH_KEY')];

		if(empty($id)){
			$this->error("您的登录状态过期！");
			exit;
		}

		$where['id'] = $_GET['id'];
		$where['uid'] = $id;
		//选出该条记录的信息
		$del_info = $del->where($where)->field("*")->find();
		if(empty($del_info)){
			$this->error("没有找到符合条件的记录");
			exit;
		}

		$sNun	= $del_info['sNun'];//总得交易数
		$used_num	= $del_info['used_num'];//成功成交得数量
		$lnum	= $del_info['lnum'];//余下的数量

		if ($lnum+$used_num != 0){
			//交易成功跟余下的数量不等于0
			if($lnum+$used_num != $sNun){
				$this->error("该条信息记录有误，请和管理员联系");
				exit;
			}
		}

		$last_s = $del_info['last_s'];
		if($last_s==1){
			$this->error("该条信息为最后一次售出，不能进行撤销操作。");
			exit;
		}

		$spid = $del_info['spid'];
		$y_rd = 1;//是否原数据读出
		if($spid>0){
			$s_c = $gp_sell->where('id='.$spid.' and sell_num<3')->count();
			if($s_c==0){
				$this->error("该条信息为最后一次售出，不能进行撤销操作。");
				exit;
			}
		}else{
			$s_rs = $gp_sell->where('uid='.$id.' and sell_num<3')->field('id,uid')->order('sell_date desc')->find();
			if(!$s_rs){
				$this->error("该条信息为最后一次售出，不能进行撤销操作。");
				exit;
			}else{
				$spid = $s_rs['id'];
				$y_rd = 0;
			}
		}

		$xz_hour = 1;
		$eDate = $del_info['eDate'];
		$n_edate = $eDate+3600*$xz_hour;
		if($n_edate>mktime()){
			$this->error("交易挂出 ".$xz_hour." 小时内，不能撤销。");
			exit;
		}

		$where['sNun'] = array('eq',$sNun);
		$where['used_num'] = array('eq',$used_num);
		$where['lnum'] = array('eq',$lnum);

		$cx_content = "撤销出售 ".$lnum." 个";

		//撤销的话要更新股票表
		$data['ispay']	= 1;
		$data['is_cancel']	= 1;
		$data['sNun']	= $del_info['used_num'];
		$data['lnum']	= 0;
		$data['bz']	= $cx_content;
		$rs = $del->where($where)->save($data);
		if($rs){

			//没有售出的那部分股票还给他
			if($y_rd==1){
				$gp_sell->execute("UPDATE __TABLE__ SET sell_ln=sell_ln+".$lnum.",sell_mon=sell_mon-".$lnum." WHERE `id`=".$spid);
			}else{
				$gp_sell->execute("UPDATE __TABLE__ SET sNun=sNun+".$lnum.",sell_ln=sell_ln+".$lnum." WHERE `id`=".$spid);
			}

			$fck->execute("UPDATE __TABLE__ SET live_gupiao=live_gupiao+".$lnum." where id=".$id);

			$bUrl = __URL__ . '/sellGPform';
			$this->_box(1, '撤销成功！', $bUrl, 1);
		}else{
			$this->error('撤销失败');
		}

	}

	//卖家交易出去后处理
	public function sellOutGp($uid=0,$gpid=0,$out_n=0,$fee_rs=0,$game=0,$senum=0){
		$fck = D('Fck');
		$mrs = $fck->where('id='.$uid)->field('id,user_id')->find();
		
		$one_price = $fee_rs['gp_one'];//达人挂起的价格
		$gp_perc = $fee_rs['gp_perc']/100;//交易手续费
		$gp_inm = $fee_rs['gp_inm']/100;//进入奖金比例
		$gp_inn = $fee_rs['gp_inn']/100;//进入重复消费比例
	
		$get_money = $senum;//售出CQ币金额
		if($get_money>0){
			
			
			$game->setGameCash($uid,$get_money);
			
			$fck->addencAdd($uid,$mrs['user_id'], $get_money, 31);//添加奖金和记录
		}
		//更新账户
		$fck->query("update __TABLE__ SET " .
			"all_out_gupiao=all_out_gupiao+".$out_n.
			",agent_kt=agent_kt+".$in_cfmoney.",agent_use=agent_use+".$get_money."" .
			" WHERE `id`=$uid");
		// $fck->query("update __TABLE__ SET agent_use=agent_use+".$in_gpmoney."" ." WHERE `id`=1");
		
		unset($fck,$game,$mrs,$fee_rs);
	}

	//把交易成功的记录写入到一个表中【不能删除的】
	public function gpSuccessed($uid=0,$out_n=0,$type=0,$fee_rs=0,$gpid=0,$en=0,$ett=0,$did=0){


		$hgp=M('hgupiao');
		$gp = M('gupiao');
		$grs = $gp->where('id='.$gpid)->find();
		$cur_one_price	= $grs['one_price'];//达人挂起的价格

		$gm	= $out_n*$cur_one_price;//售出CQ币金额
		//添加记录到表
		$data['uid'] = $uid;
		$data['price'] = $gm;
		$data['one_price'] = $cur_one_price;
		$data['sNun'] = $out_n;
		$data['ispay'] = 1;
		$data['eDate'] = time();
		$data['type'] = $type;
		if($type==1){
			$fee_money	= $fee_rs['str2']/100;//CQ币的税费
			$shuis = $gm*$fee_money;//税收
			$la_sh = $gm - $shuis;//税后
			$sy_sh = $la_sh;//剩余
			//扣税后的金额
			$data['gprice']	= $la_sh;
			//更新多少进入注册币,多少进入交易币
			$stt6 = $fee_rs['str6']/100;//注册币比例
			$stt5 = $fee_rs['str5']/100;//交易币比例
			$data['gmp'] = $sy_sh*$stt6;//进入注册币
			$data['pmp'] = $sy_sh*$stt5;//进入交易币
		}else{
			D('Game')->updateGameCash($uid,$gm);
		}

		$data['is_en'] = $en;//CQ币类型
//		$data['did'] = $did;

		$hgp->add($data);
		//添加到历史记录表

		$fck = D('Fck');
		$rs	= $fck->where("id=$uid")->field("user_id")->find();

		$c_gp = M('gp');
		if($ett==1){
			$jioayi_n = 0;
		}else{
			$jioayi_n = $out_n;
		}
		$c_gp ->query("update __TABLE__ set gp_quantity=gp_quantity+".$jioayi_n.",turnover=turnover+".$jioayi_n." where id=1");

		$this->gp_jy_bs($jioayi_n,0);

		$this->gp_jy_bs($jioayi_n,1);

	}
	
	//股票买卖统计交易量
	public function gp_jy_bs($num,$type=0){
	
		if($num>0){
			$gp	= M('gp');
			if($type==0){
				$gp->query("update __TABLE__ set buy_num=buy_num+".$num." where id=1");
				//加卖出数量
				M('fee')->query("update __TABLE__ set gp_senum=gp_senum+".$num." where id=1");
			}else{
				$gp->query("update __TABLE__ set sell_num=sell_num+".$num." where id=1");
			}
			unset($gp);
		}
	}
	
	//股票升价降价
	public function gp_up_down_pd(){
		$gp		= M('gp');
		$fee	= M('fee');
		$fee_rs = $fee->field('gp_one,gp_fxnum,gp_senum,gp_cnum') ->find();
		$cf_pri = 2;
		$up_pri = 0.01;
		$one_price	= $fee_rs['gp_one'];
		$gp_fxnum	= $fee_rs['gp_fxnum'];//升价标准
		$gp_senum	= $fee_rs['gp_senum'];//销售量
		$gp_cnum	= $fee_rs['gp_cnum'];//拆股次数
		$gp_c_pri = $one_price;
		if($gp_fxnum<=$gp_senum&&$gp_fxnum>0){//升价
			$new_pri = $one_price+$up_pri;
			$gp_c_pri = $new_pri;
			$result = $fee->execute("update __TABLE__ set gp_one=".$new_pri.",gp_senum=gp_senum-".$gp_fxnum." where id=1 and gp_one=".$one_price);
			if($result){//涨价成功
				$gp->query("update __TABLE__ set opening=".$new_pri."");
				if($gp_cnum>1){
					//自动抛出股票
					//$this->auto_sell_gp($new_pri);
				}
			}
		}
		if($gp_c_pri==$cf_pri){//达到拆股
			//自动拆股
			$this->splitGP(1);
			$gp_ch_pri = $gp_c_pri/2;
			$gp->query("update __TABLE__ set opening=".$gp_ch_pri."");
			if($gp_cnum>=1){
				//自动抛出股票
				//$this->auto_sell_gp($gp_ch_pri);
			}else{
// 				$this->gx_auto_sell();
			}
		}
		unset($gp,$fee,$fee_rs);
	}
	
	//自动抛售股票
	public function auto_sell_gp($one_price){
		if($one_price>0){
			$gp_sell = M('gp_sell');
			$fck = M('fck');
			$gupiao = M('gupiao');
			$where = "is_over=0 and sell_mm=".$one_price." and sell_mon=2";
			$order = "id asc";
			$lirs = $gp_sell->where($where)->order($order)->select();
			foreach($lirs as $lrs){
				$id = $lrs['id'];
				$myid = $lrs['uid'];
				$sNunb = $lrs['sNun'];
				$bmoney = $one_price*$sNunb;
				$ok_sell = 0;
				$ok_money = $ok_sell*$one_price;
				
				//更新自己的售股信息
				$data['uid'] = $myid;
				$data['one_price'] = $one_price;
				$data['price'] = $sNunb*$one_price;//总得CQ币金额
				$data['sNun'] = $sNunb;//总的CQ币数
				$data['used_num'] = $ok_sell;//成功买到的CQ币
				$data['lnum'] = $sNunb - $ok_sell;//还差没有售出的CQ币
				$data['ispay'] = 0;//交易是否完成
				$data['eDate'] = time();//售出时间
				$data['status'] = 0;//这条记录有效
				$data['type'] = 1;//标识为售出
				$data['is_en'] = 0;//标准股
				$data['spid'] = 0;//原卖出记录ID
				$data['last_s'] = 0;//是否最后一次卖出
				$data['sell_g'] = $ok_money;//售出获得总额
				$resid = $gupiao->add($data);//添加记录
				if($resid){
					//更新账户
					$fck->query("update __TABLE__ SET " .
							"live_gupiao=live_gupiao-".$sNunb.",all_out_gupiao=all_out_gupiao+".$ok_sell.
							" WHERE `id`=".$myid);
					$gp_sell->query("update __TABLE__ set is_over=1,ispay=1,sell_date=".time()." where id=".$id);
				}
			}
			unset($gp_sell,$fck,$gupiao,$lirs,$lrs);
		}
	}

	//更新所有会员股数
	public function gx_all_gp_tj(){

// 		$fck = M('fck');
// 		$gp_sell = M('gp_sell');

// 		$lirs = $fck->where('is_pay>0 and id>1 and is_lock=0')->field('id,live_gupiao')->order('id asc')->select();
// 		foreach($lirs as $lrs){
// 			$myid = $lrs['id'];
// 			$live_gupiao  = $lrs['live_gupiao'];

// 			$all_n = $gp_sell->where('uid='.$myid.' and is_over=0 and sell_ln>0')->sum('sell_ln');
// 			$all_n = (int)$all_n;
// 			if($live_gupiao!=$all_n){
// 				$fck->query("update __TABLE__ set live_gupiao=".$all_n." where id=".$myid);
// 			}
// 		}
// 		unset($fck,$gp_sell,$lirs,$lrs);
	}

	//达人的CQ币信息
	public function gpInfo(){
		$id	= $_SESSION[C('USER_AUTH_KEY')];
		$rs	= M('fck')->where("id=$id")->field("live_gupiao")->find();

		$arr = array();
		$arr[0] = $rs['live_gupiao'];//剩余的CQ币
		$arr[1] = $rs['all_in_gupiao'];//全部买进的CQ币
		$arr[2] = $rs['all_out_gupiao'];//全部卖出的CQ币
		$arr[3] = $rs['yuan_gupiao'];//原始CQ币
		
		return $arr;
	}

	//达人正在求购或者购买的CQ币,0为求购,1为出售
	public function buy_and_ing($x=0,$en=0){
		$id	= $_SESSION[C('USER_AUTH_KEY')];
		$gp = M('gupiao');
		$gping_num = $gp->where("uid=$id and type=$x and is_en=$en")->sum("lnum");
		return empty($gping_num)?0:$gping_num;
	}

	/*CQ币走势图部分【开始】*/
	public function trade() { ///7/股權交易
		if(empty($_SESSION[C('USER_AUTH_KEY')])){
			$this->error("错误！");
			exit;
		}
		if(empty($_SESSION['first_in_trade'])){
			//第一次进来就刷新走势图
			$this->stock_past_due();
			$_SESSION['first_in_trade'] = 1;
		}
		$fck = M('fck');
		$gppp = M('gp');
		$fee	= M('fee');
		$xml = M('xml');
		$fee_rs = $fee->find();

		$fee_gp = $gppp->find();
		$laxl = $xml->order('id desc')->find();
		$now_p = $laxl['money'];
		$now_num = $laxl['amount'];
		$now_t = $laxl['x_date'];

		$bj_p = $fee_rs['gp_one'];
		$bj_n = $fee_gp['gp_quantity'];
		$bj_d = strtotime(date("Y-m-d"));
		if(bccomp($bj_p,$now_p,2)!=0||$now_num!=$bj_n||$now_t!=$bj_d){
			$this->stock_past_due();
		}

		$op_price	= $fee_rs['gp_one'];//现价和现在的购买价和销售价
		$day_price	= $fee_rs['gp_open'];//开盘价
		$close_pr	= $fee_rs['gp_close'];//昨日收盘价

		$tall_sNun	= $fee_gp['gp_quantity'];//今日成交量
		$tall_sNun	= number_format($tall_sNun,0,"",",");
		$all_sNun	= $xml->sum('amount');//XML总成交量
		$all_sNun	= number_format($all_sNun,0,"",",");
		
		$all_num	= $fee_gp['turnover'];//总成交量
		$yt_sellnum = $fee_gp['yt_sellnum'];//昨日交易量

		$all_num = number_format($all_num,0,"",",");
		$this->assign('all_num', $all_num);//总成交量
		$yt_sellnum = number_format($yt_sellnum,0,"",",");
		$this->assign('yt_sellnum', $yt_sellnum);//

		$id = $_SESSION[C('USER_AUTH_KEY')];
		$gp_info	= $this->gpInfo();//CQ币的信息
		$fck_rs		= $fck->where("id=$id")->field("agent_gp")->find();

		$this->assign('live_gp', $gp_info[0]);//剩余的
		$this->assign('game_cash', $fck_rs['agent_gp']);//当前的游戏币

		$this->assign('op_price',$op_price);
		$this->assign('day_price',$day_price);
		$this->assign('cl_price',$close_pr);
		$this->assign('tall_sNun',$tall_sNun);
		$this->assign('all_sNun',$all_sNun);

		$this->display ();
	}

	public function stock_past_due(){
		$gp = M('gp');
		$xml = M('xml');
		$rs = $gp->where("id=1")->find();
		$gp_quantity = $rs['gp_quantity'];
		$tt = $rs['f_date'];
		$newday = strtotime(date("Y-m-d"));
		$ddtt = strtotime(date("Y-m-d",$tt));

		if($ddtt==$newday){
			$mrs=$xml->where('x_date='.$newday)->find();
			if($mrs){
				$data = array();
				$data['id']=$mrs['id'];
				$data['money'] =  $rs['opening'];
				$data['amount'] = $rs['gp_quantity'];
				$xml->save($data);
			}else{
				$data = array();
				$data['money'] =  $rs['opening'];
				$data['amount'] = $rs['gp_quantity'];
				$data['x_date'] = $newday;
				$xml->add($data);
			}
		}else{
			$result = $gp->execute("update __TABLE__ set yt_sellnum=gp_quantity,gp_quantity=0,closing=opening where id=1 and gp_quantity=".$gp_quantity);
			if($result){
				$mrs=$xml->where('x_date='.$newday)->find();
				if($mrs){
					$data = array();
					$data['id']=$mrs['id'];
					$data['amount'] = 0;
					$xml->save($data);
				}else{
					$data = array();
					$data['money'] =  $rs['opening'];
					$data['amount'] = 0;
					$data['x_date'] = $newday;
					$xml->add($data);
				}
			}
		}
		if ($tt < time()) {//时时更新价格
			$f_date = time();
			$gp->query ("update __TABLE__ set today=opening,most_g=opening,most_d=opening,f_date='$f_date' where id=1");
		}
		$this->ChartsPrice ();
	}

	//股票升值判断是否还能再购买
	public function pd_buy_ok($pri=0){

		$fck = M('fck');
		$gupiao = M('gupiao');

		$rs=$gupiao->where('ispay=0 and status=0 and buy_s<'.$pri.' and type=0')->select();
		foreach($rs as $vo){

			$buy_s = $vo['buy_s'];//剩余购买总额
			$myuid = $vo['uid'];
			$tid = $vo['id'];

			$sy_pri = $buy_s;//购买剩余多少没买到
			$gupiao->where('id='.$tid)->setField('ispay',1);//完成
			$fck->query('update __TABLE__ set agent_gp=agent_gp+'.$sy_pri.' where id='.$myuid);//补回余额
		}

	}

	public function ChartsPrice() {
		$xml = M ('xml');
		$fengD = strtotime("2012-01-01");
		$rs = $xml->where('x_date>='.$fengD)->order("x_date desc")->select();
		$xx = "";
		foreach ($rs as $vo ) {
			$xx = $xx.date("Y-m-d",$vo['x_date']).",".$vo['amount'] .",".$vo['money']."\r\n";
		}
//		$filename =  "./Public/amstock/data2.csv";
		$filename =  "./Public/U/data2.csv";
		if (file_exists($filename)) {
			unlink($filename); //存在就先刪除
		}
		file_put_contents($filename,$xx);
	}

	public function ChartsVolume($date, $shu) {
		////生成xml檔
		$yy = "<graph yAxisMaxValue='3500000' yAxisMinValue='100' numdivlines='19' lineThickness='1' showValues='0' numVDivLines='0' formatNumberScale='1' rotateNames='1' decimalPrecision='2' anchorRadius='2' anchorBgAlpha='0' divLineAlpha='30' showAlternateHGridColor='1' shadowAlpha='50'>";
		$yy = $yy . "<categories>";
		$yy = $yy . $date;
		$yy = $yy . "</categories>";
		$yy = $yy . "<dataset color='A66EDD' anchorBorderColor='A66EDD' anchorRadius='2'>";
		$yy = $yy . $shu;
		$yy = $yy . "</dataset>";
		$yy = $yy . "</graph>";
		$filename = "./Public/Images/ChartsVolume.xml";
		if (file_exists ( $filename )) {
			unlink ( $filename ); //存在就先刪除
		}
		$wContent = $yy;
		$handle = fopen ( $filename, "a" );
		if (is_writable ( $filename )) {
			//fwrite($handle, $wContent);
			fwrite ( $handle, $wContent );
			if (is_readable ( $filename )) {
				$file = fopen ( $filename, "rb" );
				$contents = "";
				while ( ! feof ( $file ) ) {
					$contents = fread ( $file, 90000000 );
				}
				fclose ( $file );
			}
			fclose ( $handle );
		}
	}

    //检查代卖表
    public function addsell_gp($uid,$sNunb=0,$pri=0){
    	$gp_sell = M('gp_sell');
    	$lrs = $gp_sell->where('uid='.$uid.' and sell_mm='.$pri.' and is_over=0 and sell_mon=0')->find();
    	if($lrs){
    		$gp_sell->query("update __TABLE__ set sNun=sNun+".$sNunb.",sell_ln=sell_ln+".$sNunb." where id=".$lrs['id']);
    	}else{
    		$wdata = array();
    		$wdata['uid'] = $uid;
    		$wdata['sNun'] = $sNunb;
    		$wdata['eDate'] = time();
    		$wdata['sell_mm'] = $pri;
    		$wdata['sell_ln'] = $sNunb;
    		$gp_sell->add($wdata);
    		unset($wdata);
    	}
    	unset($gp_sell,$lrs);
    }

    //股票参数设置
    public function adminsetGP(){
    	$this->_Admin_checkUser();
    	if ($_SESSION['UrlPTPass'] == 'adminsetGP'){
			$fck = M('fck');
			$allagcash = $fck->where('is_pay>0')->sum('agent_gp');
			if(empty($allagcash)){$allagcash=0;}
			$this -> assign('allagcash',$allagcash);
			
    		$fee = M ('fee');
    		$fee_rs = $fee -> find();
    		
    		$is_sp	= $fee_rs['gp_cgbl'];
    		$is_yes	 = explode(':',$is_sp);
    
    		$btn ="<input name=\"bttn\" type=\"button\" id=\"bttn\" value=\"立刻按此设置进行拆股\" class=\"btn1\" onclick=\"if(confirm('您确定要按照 ".$fee_str10." 比例进行拆股吗？')){window.location='__URL__/set_gp_cg/f_b/".$is_yes[0]."/s_b/".$is_yes[1]."/';return true;}return false;\"/>";
    		$this -> assign('btn',$btn);
    		$this -> assign('fee_rs',$fee_rs);
    
    		$this->display('adminsetGP');
    	}else{
    		$this->error('错误!');
    		exit;
    	}
    }
    
    public function setGPSave(){
    	$this->_Admin_checkUser();
    	if ($_SESSION['UrlPTPass'] == 'adminsetGP'){
    		$fee = M ('fee');
    		$gp = M( 'gp' );
    		$rs = $fee -> find();
    		
    		$data1 = array();
    		$data1['gp_open']  = trim($_POST['gp_open']);
    		$data1['gp_close']  = trim($_POST['gp_close']);
    		$data1['gp_perc']  = trim($_POST['gp_perc']);
    		$data1['gp_inm']  = trim($_POST['gp_inm']);
    		$data1['gp_inn']  = trim($_POST['gp_inn']);
    		$data1['gp_kg']  = trim($_POST['gp_kg']);
    		$data1['str12']  = trim($_POST['str12']);
    		$is_sp	= trim($_POST['gp_cgbl']);
    		$is_yes	 = explode(':',$is_sp);
    		if(!is_numeric($is_yes[0])||!is_numeric($is_yes[1])){
    			$this->error('拆股比例不是数值!');
    			exit;
    		}
    		$data1['gp_cgbl']  = trim($_POST['gp_cgbl']);
    		$now_ppp = trim($_POST['gp_one']);
    		$gpprice = trim($_POST['gpprice']);
    		$data1['gp_one'] = $now_ppp;
    		$fee -> where("id=1") -> save($data1);
    		
    		if(bccomp($gpprice, $now_ppp, 2)!=0){
    			//更新价格
    			$gp->execute("update __TABLE__ set opening=$now_ppp,f_date=".time());
    			//更新交易信息
    			$this->pd_buy_ok($now_ppp);
    		}
    
    		$this->success('参数设置成功！');
    		exit;
    	}else{
    		$this->error('错误!');
    		exit;
    	}
    }
    
    //拆股操作
    public function set_gp_cg(){
    
    	$is_yes=array();
    	$is_yes[0]=$_GET['f_b'];
    	$is_yes[1]=$_GET['s_b'];
    	if(!is_numeric($is_yes[0])||!is_numeric($is_yes[1])){
    		$this->error('拆股比例不是数值!');
    		exit;
    	}
    	if($is_yes[0]!=$is_yes[1]){
    		$this->splitGP();
    		$bUrl = __URL__.'/adminsetGP';
    		$this->_box(1,'拆股操作完成！',$bUrl,1);
    	}else{
    		$this->error('拆股比例未有改变!');
    		exit;
    	}
    }
    
    //拆分CQ币
    public function splitGP($c_type=0){
    	if($c_type==0){
    		$this->_Admin_checkUser();
    	}
    	$fee	= M('fee');
    	$fck	= M('fck');
    	$gp		= M( 'gp' );

    	$fee_rs = $fee ->find();
    	//拆分之前CQ币的相关设置
    	$old_close_gp	= $fee_rs['gp_kg'];//CQ币交易开关,1为关闭
    	$old_one_price	= $fee_rs['gp_one'];//系统设置的CQ币价格
    	//拆分比率
    	$split_m	= explode(':',$fee_rs['gp_cgbl']);
    	$split_m1	= $split_m[0];//拆分前的CQ币比率
    	$split_m2	= $split_m[1];//拆分后的CQ币比率
    	if($c_type==1){//自动拆分固定比例1:2
    		$split_m1 = 1;
    		$split_m2 = 2;
    	}
    	//计算拆分后的价格【根据拆分前后的总价值相等算出拆分后的价格】
    	$cur_one_price	= ((int)((($old_one_price*$split_m[0])/$split_m2)*10000))/10000;
    	//拆分后达人的CQ币变动比率【根据拆分前后的比率为$split_m1/$split_m2】
    	$cur_gp	= $split_m2/$split_m1;
    
    	//拆分之前先把CQ币的交易功能关闭掉,以免出错
    	$fee->execute("update __TABLE__ set gp_kg=1 where id=1");
    	
    	//撤消所有未完成交易
    	$this->canel_jy();
    	
    	//更新达人的CQ币信息
    	$fck->execute("update __TABLE__ set live_gupiao=live_gupiao*$cur_gp where id>0 and live_gupiao>0");
    
    	M('gp_sell')->query("update __TABLE__ set sNun=sNun*$cur_gp,sell_ln=sell_ln*$cur_gp,sell_mon=sell_mon+1 where is_over=0");
    	
    	//更新会员股数
    	$this->gx_all_gp_tj();

    	//更新CQ币的当前价格,恢复1:1
    	$fee->execute("update __TABLE__ set gp_one=$cur_one_price,gp_fxnum=gp_fxnum*$cur_gp,gp_cnum=gp_cnum+1,gp_cgbl='1:1' where id=1");
    	//更新CQ币价格
    	$gp->execute("update __TABLE__ set opening=".$cur_one_price.",f_date=".time()."");
    
    	//拆分完毕，重新恢复CQ币的之前设置
    	$fee->execute("update __TABLE__ set gp_kg=$old_close_gp where id=1");
    }
    
    //公司自动抛售
    private function gx_auto_sell(){
    	$one_price = 0.1;
    	$sNunb = 6000000;
    	$ok_sell = 0;
    	$ok_money = $ok_sell*$one_price;
    	
    	$data = array();
    	$data['uid'] = 1;
    	$data['one_price'] = $one_price;
    	$data['price'] = $sNunb*$one_price;//总得CQ币金额
    	$data['sNun'] = $sNunb;//总的CQ币数
    	$data['used_num'] = $ok_sell;//成功买到的CQ币
    	$data['lnum'] = $sNunb - $ok_sell;//还差没有售出的CQ币
    	$data['ispay'] = 0;//交易是否完成
    	$data['eDate'] = time();//售出时间
    	$data['status'] = 0;//这条记录有效
    	$data['type'] = 1;//标识为售出
    	$data['is_en'] = 0;//标准股
    	$data['spid'] = 0;//原卖出记录ID
    	$data['last_s'] = 0;//是否最后一次卖出
    	$data['sell_g'] = $ok_money;//售出获得总额
    	$resid = M('gupiao')->add($data);//添加记录
    	if($resid){
    		M('fck')->query("update __TABLE__ SET all_out_gupiao=all_out_gupiao+".$sNunb." WHERE `id`=1");
    	}
    	unset($data);
    }
    
    //两个数字相除
    private function numb_duibi($a,$b){
    	$numb = 3;
    	$chub = pow(10,$numb);
    	$c_a = (int)($a*$chub);
    	$c_b = (int)($b*$chub);
    	$c_c = $c_a/$c_b;
    	return $c_c;
    }
    
    //取消未完成交易
    public function canel_jy(){
    	$fck=M('fck');
    	$gupiao = M('gupiao');
    	$where = array();
    	$where = 'uid>0 and status=0 and lnum>0 and ispay=0 and type=1';//卖
    	$mrs =$gupiao->where($where)->select();
    	foreach($mrs as $vo){
			$sNun = $vo['sNun'];//总得交易数
			$used_num = $vo['used_num'];//成功成交得数量
			$lnum = $vo['lnum'];//余下的数量
			$en=$vo['is_en'];
		    
			if ($lnum+$used_num != 0){
				//交易成功跟余下的数量不等于0,退出此次循环
				if($lnum+$used_num != $sNun){
					continue;
				}
			}
		    $resulta = $fck->execute("UPDATE __TABLE__ SET live_gupiao=live_gupiao+".$lnum.",all_out_gupiao=all_out_gupiao-".$lnum." WHERE `id`=".$vo['uid']);
			if($resulta){
				$cx_content = "撤销出售 ".$lnum." 个";
				//撤销的话要更新股票表
				$data = array();
				$data['ispay']	= 1;
				$data['is_cancel']	= 1;
				$data['sNun']	= $vo['used_num'];
				$data['lnum']	= 0;
				$data['bz']	= $cx_content;
				$gupiao->where('id='.$vo['id'])->save($data);
			}
    	}
    	unset($where,$mrs,$vo);
    	
    	$where = array();
    	$where = 'uid>0 and status=0 and buy_s>0 and ispay=0 and type=0';//买
    	$mrs =$gupiao->where($where)->select();
    	foreach($mrs as $vo){
    		$buy_s = $vo['buy_s'];//剩余额度
    		$resulta = $fck->execute("UPDATE __TABLE__ SET agent_gp=agent_gp+".$buy_s." WHERE `id`=".$vo['uid']);
    		if($resulta){
    			$data = array();
    			$data['ispay']	= 1;
				$data['is_cancel']	= 1;
    			$gupiao->where('id='.$vo['id'])->save($data);
    		}
    	}
    	unset($where,$mrs,$vo);
    	unset($fck,$gupiao);
    }
    
    //买卖列表
    public function buylist(){
    	$this->_Admin_checkUser();
		$GPmj = M('gupiao');
		$gp_sell = M('gp_sell');
		$fck = M('fck');
		
		$fee_rs = M('fee')->field('gp_one,gp_kg')->find();
		$id = $_SESSION[C('USER_AUTH_KEY')];

		import ( "@.ORG.ZQPage" );  //导入分页类

		$where = 'type=1 and id>0 and uid='.$id;
		$field = '*';

		$count = $GPmj->where($where)->field($field)->count();//总页数
		$listrows = 15;//每页显示的记录数
		$Page = new ZQPage($count,$listrows,1);
		//===============(总页数,每页显示记录数,css样式 0-9)
		$show = $Page->show();//分页变量
		$this->assign('page',$show);//分页变量输出到模板

		$list = $GPmj->where($where)->field($field)->order('eDate desc')->page($Page->getPage().','.$listrows)->select();
		$this->assign('list',$list);

		$where2= 'type=1 and id>0 and pid='.$id;
		$count2 = $GPmj->where($where2)->field($field)->count();//总页数
		$listrows2 = 15;//每页显示的记录数
		$Page2 = new ZQPage($count2,$listrows2,1);
		//===============(总页数,每页显示记录数,css样式 0-9)
		$show2 = $Page2->show();//分页变量
		$this->assign('page2',$show2);//分页变量输出到模板


		
		$list2 = $GPmj->where($where2)->field($field)->order('eDate desc')->page($Page2->getPage().','.$listrows2)->select();
		$this->assign('list2',$list2);
		

		$this->display('buylist');
    }
    
    //出售列表
    public function selllist(){
    	$this->_Admin_checkUser();
    	$fck=M('fck');
    	$id	= $_SESSION[C('USER_AUTH_KEY')];
    	$fck_rs=$fck->where('id='.$id)->field('agent_use')->find();
    	$this->assign('frs',$fck_rs);
    	$gupiao = M('gupiao');
    	import ( "@.ORG.ZQPage" );  //导入分页类
    	$where = 'type=1 and id>0 and ispay=0';
    	$field = '*';
    	$count = $gupiao->where($where)->field($field)->count();//总页数
    	$listrows = 10;//每页显示的记录数
    	$Page = new ZQPage($count,$listrows,1);
    	//===============(总页数,每页显示记录数,css样式 0-9)
    	$show = $Page->show();//分页变量
    	$this->assign('page',$show);//分页变量输出到模板
    	$list = $gupiao->where($where)->field($field)->order('eDate desc')->page($Page->getPage().','.$listrows)->select();
    	$this->assign('list', $list);
    	$this->display('selllist');
    }

}
?>