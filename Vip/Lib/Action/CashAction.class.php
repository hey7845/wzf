<?php
class CashAction extends CommonAction {

	public function _initialize() {
		header("Content-Type:text/html; charset=utf-8");
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
				$_SESSION['Urlszpass'] = 'Mysseb_sell';
				$bUrl = __URL__.'/eb_sell';
				$this->_boxx($bUrl);
				break;
			case 2;
				$_SESSION['Urlszpass'] = 'Mysseb_buy';
				$bUrl = __URL__.'/eb_buy';
				$this->_boxx($bUrl);
				break;
			case 3;
				$_SESSION['Urlszpass'] = 'Mysseb_list_b';
				$bUrl = __URL__.'/eb_list_b';
				$this->_boxx($bUrl);
				break;
			case 4;
				$_SESSION['Urlszpass'] = 'Mysseb_list';
				$bUrl = __URL__.'/eb_list';
				$this->_boxx($bUrl);
				break;
			case 5;
				$_SESSION['Urlszpass'] = 'Mysseb_history';
				$bUrl = __URL__.'/eb_history';
                $this->_boxx($bUrl);
				break;
			case 6;
                $_SESSION['UrlPTPass'] = 'MyssEBlist';
                $bUrl = __URL__.'/admin_eblist';//列表
                $this->_boxx($bUrl);
                break;
			default;
				$this->error('二级密码错误!');
				exit;
		}
	}

	//===================================================K币
	public function eb_sell($Urlsz=0){
		$fck = M('fck');
		$plan = M('plan');

		$plancontent = $plan->where('id=3')->find();
		$this->assign('eb_tcp',$plancontent['content']);

		$where = array();
		$ID = $_SESSION[C('USER_AUTH_KEY')];
		$where['id'] = $ID;
		$field = '*';
		$rs = $fck ->where($where)->field($field)->find();

		$this->assign('type',$ID);
		$this->assign('rs',$rs);
		
		$fee = M('fee');
	    $fee_rs = $fee->field('str2')->find();
		$str2 = 0;//$fee_rs['str2']/100;
		$this->assign('str2',$str2);
		$this->assign('ks',$str2);

		unset($fck,$where,$ID,$field,$rs);
		$this->display ();
	}

	//=================================================处理
	public function eb_sell_AC(){
		$ePoints = (int) trim($_POST['ePoints']);
		$bzcontent = trim($_POST['bzcontent']);
		$bzcontent = strip_tags($bzcontent);//去掉HTML
		$bzcontent = substr($bzcontent,0,200);//200字符


		$s_type = (int)$_POST['s_type'];
		if($s_type>1||$s_type<0){
			$s_type = 0;
		}
		$pass2 = trim($_POST['pass2']);

		$fck = M ('fck');
		$cash = M ('cash');
		$history = M ('xfhistory');

		if (empty($ePoints) || !is_numeric($ePoints)){
			$this->error('金额不能为空!');
			exit;
		}
		if (strlen($ePoints) > 12){
			$this->error ('金额太大!');
			exit;
		}
		if ($ePoints <= 0){
			$this->error ('金额输入不正确!');
			exit;
		}

		if(empty($pass2)){
			$this->error ('请输入二级密码验证！');
			exit;
		}

		$mdpass = md5($pass2);

		$where = array();
		$ID = $_SESSION[C('USER_AUTH_KEY')];
		//查询条件
		$where['id'] = $ID;
		$where['passopen'] = array('eq',$mdpass);
		$field ='*';
		$fck_rs = $fck ->where($where)->field($field)->find();
		if (!$fck_rs){
			$this->error('二级密码错误！');
			exit;
		}
		
		$fee = M('fee');
	    $fee_rs = $fee->field('str2')->find();
		$str2 = $fee_rs['str2']/100;
		
		$minB = 50;
		$hB = 50;//最低倍数
		if ($ePoints<$minB){
			$this->error ('最低额度为 '.$minB.' ！');
			exit;
		}
		if ($ePoints % $hB >0){
			$this->error ('金额必须是'.$hB.'的整数倍！');
			exit;
		}

		$inUserID = $fck_rs['user_id'];
		if($s_type==1){
			$AgentUse = $fck_rs['agent_kt'];
		}else{
			$AgentUse = $fck_rs['agent_use'];
		}
		$ks_money = 0;//bcmul($ePoints,$str2,2);//扣税
		$us_money = $ePoints+$ks_money;
		
		if ($AgentUse < $us_money){
			$this->error('账户余额不足!');
			exit;
		}
		$n_mob = $AgentUse-$ePoints;


		$money_two = $ePoints*0.95;

//		if ($ePoints % $hB||$ePoints<$s12){
//			$this->error ('提现最低额度为 '.$s12.' 且金额必须为 '.$hB.' 的倍数!');
//			exit;
//		}

		$bank_address = $fck_rs['bank_address'];

		$nowdate = strtotime(date('c'));
		//开始事务处理
		$cash->startTrans();

		//插入提现表
		$data					= array();
		$data['uid']			= $fck_rs['id'];
		$data['user_id']		= $inUserID;
		$data['rdt']			= $nowdate;
		$data['money']			= $ePoints;
		$data['money_two']		= $money_two;
		$data['epoint']			= 0;//存储国家，查询币值
		$data['is_pay']			= 0;
		$data['bank_name']		= $fck_rs['bank_name'];  //银行名称
		$data['bank_card']		= $fck_rs['bank_card'];  //银行卡
		$data['user_name']		= $fck_rs['user_name'];  //开户名称
		$data['x1']				= $bank_address;  //银行地址
		$data['x2']				= $fck_rs['qq'];  //财付通号
		$data['sellbz']			= $bzcontent;  //备注
		$data['s_type']			= $s_type;  //类型
		$data['is_sh']			= 1;  //备注
		$rs2 = $cash->add($data);
		unset($data);
		if ($rs2){
			//提交事务
			if($s_type==1){
				$fck->execute("UPDATE __TABLE__ SET agent_kt=agent_kt-".$us_money." WHERE id=".$fck_rs['id']."");
			}else{
				$fck->execute("UPDATE __TABLE__ SET agent_use=agent_use-".$us_money." WHERE id=".$fck_rs['id']."");
			}
			$cash->commit();
			$bUrl = __URL__.'/eb_sell';
			$this->_box(1,'K币挂出成功！',$bUrl,1);
			exit;
		}else{
			//事务回滚：
			$cash->rollback();
			$this->error('K币挂出失败！');
			exit;
		}
	}
	
	//===================================================担保交易
	public function eb_sell_db($Urlsz=0){
		$fck = M('fck');
		$plan = M('plan');

		$plancontent = $plan->where('id=3')->find();
		$this->assign('eb_tcp',$plancontent['content']);

		$where = array();
		$ID = $_SESSION[C('USER_AUTH_KEY')];
		$where['id'] = $ID;
		$field = '*';
		$rs = $fck ->where($where)->field($field)->find();

		$this->assign('type',$ID);
		$this->assign('rs',$rs);
		
		$fee = M('fee');
	    $fee_rs = $fee->field('str2')->find();
		$str2 = $fee_rs['str2']/100;
		$this->assign('str2',$fee_rs['str2']);
		$this->assign('ks',$str2);

		unset($fck,$where,$ID,$field,$rs);
		$this->display ();
	}
	
	//=================================================担保处理
	public function eb_sell_dbAC(){
		$Userid = trim($_POST['Userid']);
		$ePoints = (int) trim($_POST['ePoints']);
		$bzcontent = trim($_POST['bzcontent']);
		$bzcontent = strip_tags($bzcontent);//去掉HTML
		$bzcontent = substr($bzcontent,0,200);//200字符


		$s_type = (int)$_POST['s_type'];
		if($s_type>1||$s_type<0){
			$s_type = 0;
		}
		$pass2 = trim($_POST['pass2']);

		$fck = M ('fck');
		$cash = M ('cash');
		$history = M ('xfhistory');
		
		if (empty($Userid)){
			$this->error('请输入会员编号!');
			exit;
		}
		if (empty($ePoints) || !is_numeric($ePoints)){
			$this->error('金额不能为空!');
			exit;
		}
		if (strlen($ePoints) > 12){
			$this->error ('金额太大!');
			exit;
		}
		if ($ePoints <= 0){
			$this->error ('金额输入不正确!');
			exit;
		}

		if(empty($pass2)){
			$this->error ('请输入二级密码验证！');
			exit;
		}

		$mdpass = md5($pass2);
		
		$fck_buy=$fck->where("is_pay>0 and user_id='".$Userid."'")->find();
		if (!$fck_buy){
			$this->error('会员不存在！');
			exit;
		}
		
		$where = array();
		$ID = $_SESSION[C('USER_AUTH_KEY')];
		//查询条件
		$where['id'] = $ID;
		$where['passopen'] = array('eq',$mdpass);
		$field ='*';
		$fck_rs = $fck ->where($where)->field($field)->find();
		if (!$fck_rs){
			$this->error('二级密码错误！');
			exit;
		}
		
		$fee = M('fee');
	    $fee_rs = $fee->field('str2')->find();
		$str2 = $fee_rs['str2']/100;

		$minB = 50;
		$hB = 10;//最低倍数
		if ($ePoints<$minB){
			$this->error ('最低额度为 '.$minB.' ！');
			exit;
		}

		$inUserID = $fck_rs['user_id'];
		if($s_type==1){
			$AgentUse = $fck_rs['agent_kt'];
		}else{
			$AgentUse = $fck_rs['agent_use'];
		}
		$ks_money = bcmul($ePoints,$str2,2);//扣税
		$us_money = $ePoints+$ks_money;
		
		if ($AgentUse < $us_money){
			$this->error('账户余额不足!');
			exit;
		}
		$n_mob = $AgentUse-$ePoints;


		$money_two = $ePoints;

//		if ($ePoints % $hB||$ePoints<$s12){
//			$this->error ('提现最低额度为 '.$s12.' 且金额必须为 '.$hB.' 的倍数!');
//			exit;
//		}

		$bank_address = $fck_rs['bank_address'];

		$nowdate = strtotime(date('c'));
		//开始事务处理
		$cash->startTrans();

		//插入提现表
		$data					= array();
		$data['uid']			= $fck_rs['id'];
		$data['user_id']		= $inUserID;
		$data['rdt']			= $nowdate;
		$data['money']			= $ePoints;
		$data['money_two']		= $money_two;
		$data['epoint']			= 0;//存储国家，查询币值
		$data['is_pay']			= 0;
		$data['bank_name']		= $fck_rs['bank_name'];  //银行名称
		$data['bank_card']		= $fck_rs['bank_card'];  //银行卡
		$data['user_name']		= $fck_rs['user_name'];  //开户名称
		$data['x1']				= $bank_address;  //银行地址
		$data['x2']				= $fck_rs['qq'];  //财付通号
		$data['sellbz']			= $bzcontent;  //备注
		$data['s_type']			= $s_type;  //类型
		$data['is_sh']			= 1;  //备注
		
		$data['bid']			= $fck_buy['id'];  //买入会员
		$data['b_user_id']		= $fck_buy['user_id'];  //买入会员编号
		$data['bdt']			= mktime();  //时间
		$data['is_buy']			= 1;  
		
		$rs2 = $cash->add($data);
		unset($data);
		if ($rs2){
			//提交事务
			if($s_type==1){
				$fck->execute("UPDATE __TABLE__ SET agent_kt=agent_kt-".$us_money." WHERE id=".$fck_rs['id']."");
			}else{
				$fck->execute("UPDATE __TABLE__ SET agent_use=agent_use-".$us_money." WHERE id=".$fck_rs['id']."");
			}
			$cash->commit();
			$bUrl = __URL__.'/eb_sell_db';
			$this->_box(1,'担保交易成功,等待买家汇款！',$bUrl,1);
			exit;
		}else{
			//事务回滚：
			$cash->rollback();
			$this->error('K币挂出失败！');
			exit;
		}
	}

	public function eb_buy(){
		$cash = M('cash');
		$fck = M('fck');

		$id = $_SESSION[C('USER_AUTH_KEY')];

		$tt = (int)$_GET['tt'];

		$map['is_pay'] = array('eq',0);
		$map['is_buy'] = array('eq',0);
		$map['is_sh'] = array('eq',1);

		if(empty($tt)){
			$tt = 0;
		}
		if($tt==0){
			$map['money'] = array('egt',0);
		}elseif($tt==1){
			$map['money'] = array(array('egt',50),array('lt',251),'and');
		}elseif($tt==2){
			$map['money'] = array(array('egt',251),array('lt',501),'and');
		}elseif($tt==3){
			$map['money'] = array(array('egt',501),array('lt',1001),'and');
		}elseif($tt==4){
			$map['money'] = array(array('egt',1001),array('lt',2001),'and');
		}elseif($tt==5){
			$map['money'] = array(array('egt',2001),array('lt',5001),'and');
		}

		$field  = '*';
        //=====================分页开始==============================================
        import ( "@.ORG.ZQPage" );  //导入分页类
        $count = $cash->where($map)->count();//总页数
	    $listrows = C('ONE_PAGE_RE');//每页显示的记录数
	    $page_where = 'tt=' . $tt ;//分页条件
        $Page = new ZQPage($count,$listrows, 1, 0, 3, $page_where);
        //===============(总页数,每页显示记录数,css样式 0-9)
        $show = $Page->show();//分页变量
        $this->assign('page',$show);//分页变量输出到模板
        $list = $cash->where($map)->field($field)->order('id asc')->page($Page->getPage().','.$listrows)->select();
        $this->assign('list',$list);//数据输出到模板
        //=================================================
		unset($cash,$fck,$field);
		$this->display ();
	}

	public function eb_buyAC(){

		$cash = M('cash');
		$fck = M('fck');
		$plan = M('plan');

		$plancontent = $plan->where('id=3')->find();
		$this->assign('eb_tcp',$plancontent['content']);

		$id = $_SESSION[C('USER_AUTH_KEY')];
		$mrs = $fck->where('id='.$id)->field('id,user_id')->find();
		if(!$mrs){
			$this->error('参数错误！');
			exit;
		}

		$cid = (int)$_GET['cid'];
		if(empty($cid)){
			$this->error('参数错误！');
			exit;
		}

		$map = array();
		$map['id'] = array('eq',$cid);
		$map['is_pay'] = array('eq',0);
		$map['is_buy'] = array('eq',0);
		$map['is_sh'] = array('eq',1);
//		$map['uid'] = array('neq',$id);//不能购买自己的

		$rs = $cash->where($map)->find();
		$this->assign('rs',$rs);

		$buy_money = $rs['money'];

		$suid = $rs['uid'];
        $sers = $fck->where('id='.$suid)->field('bank_name,bank_card,bank_address,qq,user_tel')->find();
        $bn['bank_name'] = $sers['bank_name'];
		$bn['bank_card'] = $sers['bank_card'];
		$bn['bank_address'] = $sers['bank_address'];
		$bn['qq'] = $sers['qq'];
		$bn['user_tel'] = $sers['user_tel'];
		unset($sers);
		$this->assign('bn',$bn);

		$this->display();
	}

	public function eb_buy_AC(){

		$cash = M('cash');
		$fck = M('fck');

		$id = $_SESSION[C('USER_AUTH_KEY')];
		$mrs = $fck->where('id='.$id)->field('id,user_id,passopen')->find();
		if(!$mrs){
			$this->error('参数错误！');
			exit;
		}

		$cid = (int)$_POST['cid'];
		$uspass = $_POST['uspass'];
		if(empty($cid)){
			$this->error('参数错误！');
			exit;
		}

		if(md5($_POST['verify']) != $_SESSION['verify']) {
			$this->error('验证码错误！');
			exit;
		}

		$pass3 = $mrs['passopen'];
		$passMD = md5($uspass);
		if($pass3!=$passMD){
			$this->error('二级密码错误！');
			exit;
		}

		$map = array();
		$map['id'] = array('eq',$cid);
		$map['is_pay'] = array('eq',0);
		$map['is_buy'] = array('eq',0);
		$map['is_sh'] = array('eq',1);
//		$map['uid'] = array('neq',$id);//不能购买自己的

		$rs = $cash->where($map)->find();
		if($rs){

			$buy_money = $rs['money'];

			$dsql = "bid=".$id.",b_user_id='".$mrs['user_id']."',bdt=".mktime().",is_buy=1";
			$wsql = "id=".$cid." and is_pay=0 and is_buy=0";

			$resute = $cash->execute("update __TABLE__ set ".$dsql." where ".$wsql);
			if($resute!=false){

				$se_uid = $rs['uid'];
				$sers = $fck->where('id='.$se_uid)->field('id,user_tel')->find();

				$bUrl = __URL__.'/eb_list_b/';
				$this->_box(1,'下订单购买K币成功，请根据汇款信息尽快汇款并锁定购买！',$bUrl,1);
				exit;
			}else{
				$this->error('买入失败！');
				exit;
			}

		}else{
			$this->error('参数错误！');
			exit;
		}
	}

	public function eb_list_b(){
		$tiqu = M('cash');
		$fck = M('fck');

		$id = $_SESSION[C('USER_AUTH_KEY')];

		//买
		$map = array();
		$map['bid'] = array('eq',$id);
		$map['is_pay'] = array('eq',0);

		$field  = '*';
        //=====================分页开始==============================================
        import ( "@.ORG.ZQPage" );  //导入分页类
        $count2 = $tiqu->where($map)->count();//总页数
	    $listrows = C('ONE_PAGE_RE');//每页显示的记录数
        $Page2 = new ZQPage($count2,$listrows,1);
        //===============(总页数,每页显示记录数,css样式 0-9)
        $show2 = $Page2->show();//分页变量
        $this->assign('page',$show2);//分页变量输出到模板
        $list = $tiqu->where($map)->field($field)->order('id asc')->page($Page2->getPage().','.$listrows)->select();
        $this->assign('list',$list);//数据输出到模板
        //=================================================
        foreach($list as $trs){
        	$suid = $trs['uid'];
        	$tid = $trs['id'];
        	$sers = $fck->where('id='.$suid)->field('bank_name,bank_card,bank_address,qq')->find();
        	$bn[$tid]['bank_name'] = $sers['bank_name'];
			$bn[$tid]['bank_card'] = $sers['bank_card'];
			$bn[$tid]['bank_address'] = $sers['bank_address'];
			$bn[$tid]['qq'] = $sers['qq'];
			unset($sers);
        }
		$this->assign('bn',$bn);

		unset($tiqu,$fck,$field,$trs);
		$this->display ();
	}

	public function eb_list(){
		$tiqu = M('cash');
		$fck = M('fck');

		$id = $_SESSION[C('USER_AUTH_KEY')];


		//卖
		$map = array();
		$map['uid'] = array('eq',$id);
		$map['is_pay'] = array('eq',0);

		$field  = '*';
        //=====================分页开始==============================================
        import ( "@.ORG.ZQPage" );  //导入分页类
        $count = $tiqu->where($map)->count();//总页数
	    $listrows = C('ONE_PAGE_RE');//每页显示的记录数
        $Page = new ZQPage($count,$listrows,1);
        //===============(总页数,每页显示记录数,css样式 0-9)
        $show = $Page->show();//分页变量
        $this->assign('page2',$show);//分页变量输出到模板
        $list2 = $tiqu->where($map)->field($field)->order('id asc')->page($Page->getPage().','.$listrows)->select();
        $this->assign('list2',$list2);//数据输出到模板
        //=================================================

        //---------


		//买
		$map = array();
		$map['bid'] = array('eq',$id);
		$map['is_pay'] = array('eq',0);

		$field  = '*';
        //=====================分页开始==============================================
        import ( "@.ORG.ZQPage" );  //导入分页类
        $count2 = $tiqu->where($map)->count();//总页数
	    $listrows = C('ONE_PAGE_RE');//每页显示的记录数
        $Page2 = new ZQPage($count2,$listrows,1);
        //===============(总页数,每页显示记录数,css样式 0-9)
        $show2 = $Page2->show();//分页变量
        $this->assign('page',$show2);//分页变量输出到模板
        $list = $tiqu->where($map)->field($field)->order('id desc')->page($Page2->getPage().','.$listrows)->select();
        $this->assign('list',$list);//数据输出到模板
        //=================================================
        foreach($list2 as $rs){
        	$myid = $rs['bid'];
        	$tid = $rs['id'];
        	$mrs = $fck->where('id='.$myid)->field('id,seller_rate')->find();
        	$mrate = (int)$mrs['seller_rate'];
        	$s_img = "";
        	if($mrate>0){
        		for($i=1;$i<=$mrate;$i++){
					$s_img .='<img src="__PUBLIC__/Images/star.gif" />';
				}
        	}
        	$voo[$tid] = $s_img;
        }

        foreach($list as $trs){
        	$suid = $trs['uid'];
        	$tid = $trs['id'];
        	$sers = $fck->where('id='.$suid)->field('bank_name,bank_card,bank_address,qq')->find();
        	$bn[$tid]['bank_name'] = $sers['bank_name'];
			$bn[$tid]['bank_card'] = $sers['bank_card'];
			$bn[$tid]['bank_address'] = $sers['bank_address'];
			$bn[$tid]['qq'] = $sers['qq'];
			unset($sers);
        }
		$this->assign('bn',$bn);
		$this->assign('voo',$voo);

        //---------



		unset($tiqu,$fck,$field,$rs,$trs);
		$this->display ();
	}

	public function eb_listAC(){

		$cash = M('cash');
		$fck = M('fck');

		$id = $_SESSION[C('USER_AUTH_KEY')];
		$mrs = $fck->where('id='.$id)->field('id,user_id')->find();
		if(!$mrs){
			$this->error('参数错误！');
			exit;
		}

		$cid = (int)$_GET['cid'];
		if(empty($cid)){
			$this->error('参数错误！');
			exit;
		}

		$map = array();
		$map['id'] = array('eq',$cid);
		$map['is_pay'] = array('eq',0);
		$map['is_buy'] = array('eq',1);
		$map['bid'] = array('eq',$id);

		$rs = $cash->where($map)->find();
		$this->assign('rs',$rs);
		$suid = $rs['uid'];
        $sers = $fck->where('id='.$suid)->field('bank_name,bank_card,bank_address,qq,user_tel')->find();
        $bn['bank_name'] = $sers['bank_name'];
		$bn['bank_card'] = $sers['bank_card'];
		$bn['bank_address'] = $sers['bank_address'];
		$bn['qq'] = $sers['qq'];
		$bn['user_tel'] = $sers['user_tel'];
		unset($sers);
		$this->assign('bn',$bn);

		$this->display();
	}

	//锁定
	public function eb_list_AC(){

		$cash = M('cash');
		$fck = M('fck');

		$id = $_SESSION[C('USER_AUTH_KEY')];
		$mrs = $fck->where('id='.$id)->field('id,user_id,passopen')->find();
		if(!$mrs){
			$this->error('参数错误！');
			exit;
		}

		$cid = (int)$_POST['cid'];
		$usimg = trim($_POST['image']);
		if(empty($cid)){
			$this->error('参数错误！');
			exit;
		}
		// if(empty($usimg)){
		// 	$this->error('请上传凭证！');
		// 	exit;
		// }

		if(md5($_POST['verify']) != $_SESSION['verify']) {
			$this->error('验证码错误！');
			exit;
		}

		$uspass = $_POST['uspass'];
		$pass3 = $mrs['passopen'];
		$passMD = md5($uspass);
		if($pass3!=$passMD){
			$this->error('二级密码错误！');
			exit;
		}

		$map = array();
		$map['id'] = array('eq',$cid);
		$map['is_pay'] = array('eq',0);
		$map['is_buy'] = array('eq',1);
		$map['bid'] = array('eq',$id);

		$rs = $cash->where($map)->find();
		if($rs){

			$se_uid = $rs['uid'];
			$sers = $fck->where('id='.$se_uid)->field('id,user_tel,user_name,bank_name,bank_card,bank_address,qq')->find();

			$dsql = "ldt=".mktime().",is_buy=2," .
					"bank_name='".$sers['bank_name']."',bank_card='".$sers['bank_card']."'," .
					"user_name='".$sers['user_name']."',x1='".$sers['bank_address']."',x2='".$sers['qq']."',sellbz='".$usimg."'";//锁定交易时连同银行信息一同锁定
			$wsql = "id=".$cid." and is_pay=0 and is_buy=1 and bid=".$id;

			$resute = $cash->execute("update __TABLE__ set ".$dsql." where ".$wsql);
			if($resute!=false){
				$bUrl = __URL__.'/eb_list_b';
				$this->_box(1,'锁定购买成功，请耐心等待卖家确认！',$bUrl,1);
				exit;
			}else{
				$this->error('锁定失败！');
				exit;
			}

		}else{
			$this->error('参数错误！');
			exit;
		}
	}

	//撤销确认
	public function eb_list_CAC(){

		$cash = M('cash');
		$fck = M('fck');

		$id = $_SESSION[C('USER_AUTH_KEY')];
		$mrs = $fck->where('id='.$id)->field('id,user_id')->find();
		if(!$mrs){
			$this->error('参数错误！');
			exit;
		}

		$cid = (int)$_GET['cid'];
		if(empty($cid)){
			$this->error('参数错误！');
			exit;
		}

		$map = array();
		$map['id'] = array('eq',$cid);
		$map['is_pay'] = array('eq',0);
		$map['is_buy'] = array('eq',1);
		$map['bid'] = array('eq',$id);

		$rs = $cash->where($map)->find();
		$this->assign('rs',$rs);
		$this->display();
	}

	//买家撤销
	public function eb_list_cancel(){

		$cash = M('cash');
		$fck = M('fck');
		$xfhistory = M('xfhistory');


		$id = $_SESSION[C('USER_AUTH_KEY')];
		$mrs = $fck->where('id='.$id)->field('id,user_id,passopen')->find();
		if(!$mrs){
			$this->error('参数错误！');
			exit;
		}

		$cid = (int)$_POST['cid'];
		if(empty($cid)){
			$this->error('参数错误！');
			exit;
		}
		$cbz = trim($_POST['cbz']);
		if(empty($cbz)){
			$this->error('请填写撤销原因！');
			exit;
		}

		if(md5($_POST['verify']) != $_SESSION['verify']) {
			$this->error('验证码错误！');
			exit;
		}

		$uspass = $_POST['uspass'];
		$pass3 = $mrs['passopen'];
		$passMD = md5($uspass);
		if($pass3!=$passMD){
			$this->error('二级密码错误！');
			exit;
		}

		$map = array();
		$map['id'] = array('eq',$cid);
		$map['is_pay'] = array('eq',0);
		$map['is_buy'] = array('eq',1);
		$map['bid'] = array('eq',$id);

		$rs = $cash->where($map)->find();
		if($rs){

			$dsql = "bid=0,b_user_id='',bdt=0,is_buy=0";
			$wsql = "id=".$cid." and is_pay=0 and is_buy=1 and bid=".$id;

			$resute = $cash->execute("update __TABLE__ set ".$dsql." where ".$wsql);
			if($resute){

				$data = array();
				$data['uid'] = $rs['uid'];
				$data['user_id'] = $rs['user_id'];
				$data['did'] = $rs['bid'];
				$data['d_user_id'] = $rs['b_user_id'];
				$data['action_type'] = 1;//1买家撤销 2卖家撤销 3交易完成
				$data['pdt'] = mktime();
				$data['epoints'] = $rs['money'];
				$data['allp'] = $rs['money'];
				$data['bz'] = '买家撤销，原因：'.$cbz;
				$xfhistory->add($data);

				$bUrl = __URL__.'/eb_list_b';
				$this->_box(1,'撤销成功！',$bUrl,1);
				exit;
			}else{
				$this->error('撤销失败！');
				exit;
			}

		}else{
			$this->error('参数错误！');
			exit;
		}
	}

	//卖家撤销确认
	public function eb_listDelAC(){

		$cash = M('cash');
		$fck = M('fck');

		$id = $_SESSION[C('USER_AUTH_KEY')];
		$mrs = $fck->where('id='.$id)->field('id,user_id')->find();
		if(!$mrs){
			$this->error('参数错误！');
			exit;
		}

		$cid = (int)$_GET['cid'];
		if(empty($cid)){
			$this->error('参数错误！');
			exit;
		}

		$map = array();
		$map['id'] = array('eq',$cid);
		$map['is_pay'] = array('eq',0);
		$map['is_buy'] = array('lt',2);
		$map['uid'] = array('eq',$id);

		$rs = $cash->where($map)->find();
		$this->assign('rs',$rs);
		$this->display();
	}

	//卖家撤销
	public function eb_list_del(){

		$cash = M('cash');
		$fck = M('fck');
		$xfhistory = M('xfhistory');


		$id = $_SESSION[C('USER_AUTH_KEY')];
		$mrs = $fck->where('id='.$id)->field('id,user_id,passopen')->find();
		if(!$mrs){
			$this->error('参数错误！');
			exit;
		}

		$cid = (int)$_POST['cid'];
		if(empty($cid)){
			$this->error('参数错误！');
			exit;
		}
		$cbz = trim($_POST['cbz']);
		if(empty($cbz)){
			$this->error('请填写撤销原因！');
			exit;
		}

		if(md5($_POST['verify']) != $_SESSION['verify']) {
			$this->error('验证码错误！');
			exit;
		}

		$uspass = $_POST['uspass'];
		$pass3 = $mrs['passopen'];
		$passMD = md5($uspass);
		if($pass3!=$passMD){
			$this->error('二级密码错误！');
			exit;
		}

		$map = array();
		$map['id'] = array('eq',$cid);
		$map['is_pay'] = array('eq',0);
		$map['is_buy'] = array('lt',2);
		$map['uid'] = array('eq',$id);

		$rs = $cash->where($map)->find();
		if($rs){

			$data = array();
			$data['uid'] = $rs['uid'];
			$data['user_id'] = $rs['user_id'];
			$data['did'] = $rs['bid'];
			$data['d_user_id'] = $rs['b_user_id'];
			$data['action_type'] = 2;//1买家撤销 2卖家撤销 3交易完成
			$data['pdt'] = mktime();
			$data['epoints'] = $rs['money'];
			$data['allp'] = $rs['money'];
			$data['bz'] = '卖家撤销，原因：'.$cbz;
			$result = $xfhistory->add($data);

			if($result){

				$s_type = $rs['s_type'];
				if($s_type==1){
					$fck->query('update __TABLE__ set agent_kt=agent_kt+'.$rs['money'].' where id='.$rs['uid']);
				}else{
					$fck->query('update __TABLE__ set agent_use=agent_use+'.$rs['money'].' where id='.$rs['uid']);
				}

				$cash->where($map)->delete();

				$bUrl = __URL__.'/eb_list';
				$this->_box(1,'撤销成功！',$bUrl,1);
				exit;
			}else{
				$this->error('撤销失败！');
				exit;
			}

		}else{
			$this->error('参数错误！');
			exit;
		}
	}

	public function eb_list_DAC(){

		$cash = M('cash');
		$fck = M('fck');

		$id = $_SESSION[C('USER_AUTH_KEY')];
		$mrs = $fck->where('id='.$id)->field('id,user_id')->find();
		if(!$mrs){
			$this->error('参数错误！');
			exit;
		}

		$cid = (int)$_GET['cid'];
		if(empty($cid)){
			$this->error('参数错误！');
			exit;
		}

		$map = array();
		$map['id'] = array('eq',$cid);
		$map['is_pay'] = array('eq',0);
		$map['is_buy'] = array('eq',2);
		$map['uid'] = array('eq',$id);

		$rs = $cash->where($map)->find();
		$this->assign('rs',$rs);
		$this->display();
	}

	//确认交易
	public function eb_list_done(){

		$cash = M('cash');
		$fck = M('fck');
		$xfhistory = M('xfhistory');


		$id = $_SESSION[C('USER_AUTH_KEY')];
		$mrs = $fck->where('id='.$id)->field('id,user_id,passopen')->find();
		if(!$mrs){
			$this->error('参数错误！');
			exit;
		}

		$cid = (int)$_POST['cid'];
		if(empty($cid)){
			$this->error('参数错误！');
			exit;
		}

		if(md5($_POST['verify']) != $_SESSION['verify']) {
			$this->error('验证码错误！');
			exit;
		}

		$uspass = $_POST['uspass'];
		$pass3 = $mrs['passopen'];
		$passMD = md5($uspass);
		if($pass3!=$passMD){
			$this->error('二级密码错误！');
			exit;
		}

		$map = array();
		$map['id'] = array('eq',$cid);
		$map['is_pay'] = array('eq',0);
		$map['is_buy'] = array('eq',2);
		$map['uid'] = array('eq',$id);

		$rs = $cash->where($map)->find();
		if($rs){
			//启动事务
			$fck->startTrans();

			$buyuid = $rs['bid'];
			$cmoney = $rs['money'];
			$s_type = $rs['s_type'];

			$dsql = "is_pay=1,okdt=".mktime()."";
			$wsql = "id=".$cid." and is_pay=0 and is_buy=2 and uid=".$id;

			$result = $cash->execute("update __TABLE__ set ".$dsql." where ".$wsql);

			if($s_type==1){
				$result2 = $fck->execute("update __TABLE__ set agent_kt=agent_kt+".$cmoney." where id=".$buyuid);
			}else{
				$result2 = $fck->execute("update __TABLE__ set agent_use=agent_use+".$cmoney." where id=".$buyuid);
			}

			if($result&&$result2){

				$se_uid = $buyuid;
				$sers = $fck->where('id='.$se_uid)->field('id,user_tel')->find();

				$data = array();
				$data['uid'] = $rs['uid'];
				$data['user_id'] = $rs['user_id'];
				$data['did'] = $rs['bid'];
				$data['d_user_id'] = $rs['b_user_id'];
				$data['action_type'] = 3;//1买家撤销 2卖家撤销 3交易完成
				$data['pdt'] = mktime();
				$data['epoints'] = $rs['money'];
				$data['allp'] = $rs['money'];
				$data['bz'] = '交易完成';
				$xfhistory->add($data);
				//执行
				$fck->commit();

				$bUrl = __URL__.'/eb_list';
				$this->_box(1,'确认交易成功！',$bUrl,1);
				exit;
			}else{
				//事务回滚：
				$fck->rollback();
				$this->error('确认交易失败！');
				exit;
			}

		}else{
			$this->error('参数错误！');
			exit;
		}
	}

	//买家未收到款项撤销确认
	public function eb_list_NODAC(){

		$cash = M('cash');
		$fck = M('fck');
		$fee = M('fee');

//		$fee_rs = $fee->field('str40')->find();
//		$str40 = $fee_rs['str40'];
		$str40 = 6;

		$id = $_SESSION[C('USER_AUTH_KEY')];
		$mrs = $fck->where('id='.$id)->field('id,user_id')->find();
		if(!$mrs){
			$this->error('参数错误！');
			exit;
		}

		$cid = (int)$_GET['cid'];
		if(empty($cid)){
			$this->error('参数错误！');
			exit;
		}

		$map = array();
		$map['id'] = array('eq',$cid);
		$map['is_pay'] = array('eq',0);
		$map['is_buy'] = array('eq',2);
		$map['uid'] = array('eq',$id);

		$rs = $cash->where($map)->find();
		$this->assign('rs',$rs);
		$nnt = $rs['ldt'];
		$voo = $nnt+3600*$str40;
		$this->assign('voo',$voo);
		$this->display();
	}

	//买家未收到款项撤销
	public function eb_list_Nook(){

		$cash = M('cash');
		$fck = M('fck');
		$xfhistory = M('xfhistory');


		$id = $_SESSION[C('USER_AUTH_KEY')];
		$mrs = $fck->where('id='.$id)->field('id,user_id,passopen')->find();
		if(!$mrs){
			$this->error('参数错误！');
			exit;
		}

		$cid = (int)$_POST['cid'];
		if(empty($cid)){
			$this->error('参数错误！');
			exit;
		}
		$cbz = "未收到款，等待后台处理。";

		if(md5($_POST['verify']) != $_SESSION['verify']) {
			$this->error('验证码错误！');
			exit;
		}

		$uspass = $_POST['uspass'];
		$pass3 = $mrs['passopen'];
		$passMD = md5($uspass);
		if($pass3!=$passMD){
			$this->error('二级密码错误！');
			exit;
		}

		$map = array();
		$map['id'] = array('eq',$cid);
		$map['is_pay'] = array('eq',0);
		$map['is_buy'] = array('eq',2);
		$map['uid'] = array('eq',$id);

		$rs = $cash->where($map)->find();
		if($rs){

			$dsql = "is_pay=2,okdt=".mktime()."";//2为交由后台处理，3为已经处理
			$wsql = "id=".$cid." and is_pay=0 and is_buy=2 and uid=".$id;

			$resute = $cash->execute("update __TABLE__ set ".$dsql." where ".$wsql);

			$data = array();
			$data['uid'] = $rs['uid'];
			$data['user_id'] = $rs['user_id'];
			$data['did'] = $rs['bid'];
			$data['d_user_id'] = $rs['b_user_id'];
			$data['action_type'] = 2;//1买家撤销 2卖家撤销 3交易完成
			$data['pdt'] = mktime();
			$data['epoints'] = $rs['money'];
			$data['allp'] = $rs['money'];
			$data['bz'] = '卖家撤销，原因：'.$cbz;
			$xxid = $xfhistory->add($data);

			if($resute&&$xxid){

				$cash->execute("update __TABLE__ set x4='".$xxid."' where id=".$cid);//更新历史记录ID

				//执行
				$fck->commit();

				$bUrl = __URL__.'/eb_list';
				$this->_box(1,'撤销成功，请等待后台审核处理！',$bUrl,1);
				exit;
			}else{
				//事务回滚：
				$fck->rollback();
				$this->error('撤销失败！');
				exit;
			}
		}else{
			$this->error('参数错误！');
			exit;
		}
	}

	public function eb_history(){
		$xfhistory = M('xfhistory');
		$fck = M('fck');

		$id = $_SESSION[C('USER_AUTH_KEY')];

		$map = array();
		$where['uid'] = array('eq',$id);
		$where['did'] = array('eq',$id);
		$where['_logic']    = 'or';
		$map['_complex']    = $where;

		$sdate = $_REQUEST['Sdate'];
		$edate = $_REQUEST['Edate'];
		if(!empty($sdate)){
			$ss_d = strtotime($sdate);
			$map['pdt'] = array('egt',$ss_d);
		}
		if(!empty($edate)){
			$ee_d = strtotime($edate)+3600*24-1;
			$map['_string'] = 'pdt<='.$ee_d;
		}

		$field  = '*';
        //=====================分页开始==============================================
        import ( "@.ORG.ZQPage" );  //导入分页类
        $count = $xfhistory->where($map)->count();//总页数
	    $listrows = C('ONE_PAGE_RE');//每页显示的记录数
         $page_where = 'Sdate=' . $sdate . '&Edate=' . $edate;//分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
        //===============(总页数,每页显示记录数,css样式 0-9)
        $show = $Page->show();//分页变量
        $this->assign('page',$show);//分页变量输出到模板
        $list = $xfhistory->where($map)->field($field)->order('id desc')->page($Page->getPage().','.$listrows)->select();
        $this->assign('list',$list);//数据输出到模板
        //=================================================

		unset($xfhistory,$fck,$field);
		$this->display ();
	}
	
	public function lookpingzheng(){
			$fck = M('fck');
			$cash = M('cash');
			$id   = $_SESSION[C('USER_AUTH_KEY')];
			//输出登录用户资料记录
			$cid = (int)$_GET['cid'];
			$where = array();
			$where['id'] = array('eq',$cid);
			
			$crs = $cash ->where($where)->find();
			$this->assign('crs',$crs);

			$this->display('lookpingzheng');
	}
	
	//后台管理EB交易
	public function admin_eblist(){
		//列表过滤器，生成查询Map对象
		if ($_SESSION['UrlPTPass'] == 'MyssEBlist'){

			$cash = M('cash');
			$UserID = $_REQUEST['UserID'];
			$ss_type = (int) $_REQUEST['ry'];
			if (!empty($UserID)){
				$UserID = strtoupper($UserID);
				import ( "@.ORG.KuoZhan" );  //导入扩展类
                $KuoZhan = new KuoZhan();
                if ($KuoZhan->is_utf8($UserID) == false){
                    $UserID = iconv('GB2312','UTF-8',$UserID);
                }
                unset($KuoZhan);

				$where['user_id'] = array('like',"%".$UserID."%");
				$where['b_user_id'] = array('like',"%".$UserID."%");
				$where['_logic']    = 'or';
				$map['_complex']    = $where;
				$UserID = urlencode($UserID);
			}

			if($ss_type==1){
				$map['is_pay'] = array('eq',0);
				$map['is_buy'] = array('eq',0);
			}elseif($ss_type==2){
				$map['is_pay'] = array('eq',0);
				$map['is_buy'] = array('eq',1);
			}elseif($ss_type==3){
				$map['is_pay'] = array('eq',0);
				$map['is_buy'] = array('eq',2);
			}elseif($ss_type==4){
				$map['is_pay'] = array('eq',1);
			}elseif($ss_type==5){
				$map['is_pay'] = array('eq',2);
			}elseif($ss_type==6){
				$map['is_pay'] = array('gt',2);
			}

			$ft_date = strtotime(date("Y-m-d"))-3600*24*30;//30天内
			$map['_string'] = "rdt>".$ft_date;
			$this->assign('UserID',$UserID);
			$this->assign('ry',$ss_type);
//			$map['is_pay'] = array('gt',1);
            //查询字段
            $field  = '*';
            //=====================分页开始==============================================
            import ( "@.ORG.ZQPage" );  //导入分页类
            $count = $cash->where($map)->count();//总页数
       		$listrows = C('ONE_PAGE_RE');//每页显示的记录数
            $page_where = 'UserID=' . $UserID . '&ry=' . $ss_type;//分页条件
            $Page = new ZQPage($count, $listrows, 2, 0, 3, $page_where);
            //===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show();//分页变量
            $this->assign('page',$show);//分页变量输出到模板
            $list = $cash->where($map)->field($field)->order('id desc')->page($Page->getPage().','.$listrows)->select();
            $this->assign('list',$list);//数据输出到模板
            //=================================================

			$this->display ();
			return;
		}else{
			$this->error('数据错误!');
			exit;
		}
	}

	public function admin_eblistAC(){
		//列表过滤器，生成查询Map对象
		if ($_SESSION['UrlPTPass'] == 'MyssEBlist'){

			$cash = M('cash');
			$xfhistory = M('xfhistory');
			$fck = M('fck');

			$cid = $_GET['cid'];
			$stype= (int)$_GET['stype'];
			if($stype<1){
				$stype = 1;
			}
			if($stype>2){
				$stype = 2;
			}

			$map = array();
			$where['is_pay'] = array('eq',2);
			$where['is_buy'] = array('eq',2);
			$where['_logic']    = 'or';
			$map['_complex']    = $where;
//			$map['is_pay'] = array('eq',2);
			$map['id'] = array('eq',$cid);
			$rs = $cash->where($map)->find();
			if($rs){
				$tid = $rs['id'];
				$result = 0;
				$cmoney = $rs['money'];
				$s_type = $rs['s_type'];
				$selluid = $rs['uid'];
				$buyuid = $rs['bid'];
				$xxid = (int)$rs['x4'];
				$xh = $xfhistory->where('id='.$xxid)->find();
				if($xh){
					$xhtxt = $xh['bz'];
				}else{
					$xhtxt = "后台处理交易";
				}
				if($stype==1){

					$zbz = "金额返还给卖家";

					$txtcontent = $xhtxt."<br><font color=red>处理：金额返还给卖家</font>";

					if($s_type==1){
						$result = $fck->execute("update __TABLE__ set agent_kt=agent_kt+".$cmoney." where id=".$selluid);
					}else{
						$result = $fck->execute("update __TABLE__ set agent_use=agent_use+".$cmoney." where id=".$selluid);
					}

				}else{

					$zbz = "金额交易给买家";

					$txtcontent = $xhtxt."<br><font color=red>处理：金额交易给买家</font>";

					if($s_type==1){
						$result = $fck->execute("update __TABLE__ set agent_kt=agent_kt+".$cmoney." where id=".$buyuid);
					}else{
						$result = $fck->execute("update __TABLE__ set agent_use=agent_use+".$cmoney." where id=".$buyuid);
					}

				}
				if($result){

					if($xxid){
						$data = array();
						$data['bz'] = $txtcontent;
						$xfhistory->where('id='.$xxid)->save($data);
					}else{
						$data = array();
						$data['uid'] = $rs['uid'];
						$data['user_id'] = $rs['user_id'];
						$data['did'] = $rs['bid'];
						$data['d_user_id'] = $rs['b_user_id'];
						$data['action_type'] = 2;//1买家撤销 2卖家撤销 3交易完成
						$data['pdt'] = mktime();
						$data['epoints'] = $rs['money'];
						$data['allp'] = $rs['money'];
						$data['bz'] = $txtcontent;
						$xfhistory->add($data);
					}

					$cash ->query("update __TABLE__ set is_pay=3,bz='".$zbz."' where id=".$tid);

					$bUrl = __URL__.'/admin_eblist';
					$this->_box(1,'交易处理完成！',$bUrl,1);
					exit;
				}else{
					$this->error('处理失败！');
					exit;
				}
			}else{
				$this->error('数据错误!');
				exit;
			}


			$this->display ();
			return;
		}else{
			$this->error('数据错误!');
			exit;
		}
	}

	//卖家撤销
	public function admin_eblist_del(){
		if ($_SESSION['UrlPTPass'] == 'MyssEBlist'||$_SESSION['UrlPTPass'] == 'Myssusereblist'){
			$cash = M('cash');
			$fck = M('fck');
			$xfhistory = M('xfhistory');

			$cid = (int)$_REQUEST['cid'];
			$dp = (int)$_REQUEST['dp'];
			if(empty($cid)){
				$this->error('参数错误！');
				exit;
			}

			$map = array();
			$map['id'] = array('eq',$cid);
			$map['is_pay'] = array('eq',0);
			$map['is_buy'] = array('lt',2);

			$rs = $cash->where($map)->find();
			if($rs){

				$data = array();
				$data['uid'] = $rs['uid'];
				$data['user_id'] = $rs['user_id'];
				$data['did'] = $rs['bid'];
				$data['d_user_id'] = $rs['b_user_id'];
				$data['action_type'] = 2;//1买家撤销 2卖家撤销 3交易完成
				$data['pdt'] = mktime();
				$data['epoints'] = $rs['money'];
				$data['allp'] = $rs['money'];
				$data['bz'] = '<font color=red>后台提交撤销操作</font>';
				$result = $xfhistory->add($data);

				if($result){

					if($rs['s_type']==1){
						$fck->query('update __TABLE__ set agent_kt=agent_kt+'.$rs['money'].' where id='.$rs['uid']);
					}else{
						$fck->query('update __TABLE__ set agent_use=agent_use+'.$rs['money'].' where id='.$rs['uid']);
					}

					$cash->where($map)->delete();

					if($dp==1){
						$bUrl = __URL__.'/user_eb_list';
					}else{
						$bUrl = __URL__.'/admin_eblist';
					}
					$this->_box(1,'撤销成功！',$bUrl,1);
					exit;
				}else{
					$this->error('撤销失败！');
					exit;
				}

			}else{
				$this->error('参数错误！');
				exit;
			}
		}else{
			$this->error('数据错误!');
			exit;
		}
	}
	
	/**
     * 上传图片
     * **/
	public function upload_fengcai_pp() {
        if(!empty($_FILES)) {
            //如果有文件上传 上传附件
            $this->_upload_fengcai_pp();
        }
    }

    protected function _upload_fengcai_pp()
    {
        header("content-type:text/html;charset=utf-8");
        // 文件上传处理函数

        //载入文件上传类
        import("@.ORG.UploadFile");
        $upload = new UploadFile();

        //设置上传文件大小
        $upload->maxSize  = 1048576 * 2 ;// TODO 50M   3M 3292200 1M 1048576

        //设置上传文件类型
        $upload->allowExts  = explode(',','jpg,gif,png,jpeg');

        //设置附件上传目录
        $upload->savePath =  './Public/Uploads/image/';

        //设置需要生成缩略图，仅对图像文件有效
       $upload->thumb =  false;

       //设置需要生成缩略图的文件前缀
        $upload->thumbPrefix   =  'm_';  //生产2张缩略图

       //设置缩略图最大宽度
        $upload->thumbMaxWidth =  '800';

       //设置缩略图最大高度
        $upload->thumbMaxHeight = '600';

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
            $U_inpath=(str_replace('./Public/','__PUBLIC__/',$U_path)).$U_nname;

            echo "<script>window.parent.form1.image.value='".$U_inpath."';</script>";
            echo "<span style='font-size:12px;'>上传完成！</span>";
            exit;

        }
    }

}
?>