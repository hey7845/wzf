<?php
class FckAction extends CommonAction {

	public function _initialize() {
		header("Content-Type:text/html; charset=utf-8");
		$this->_Config_name();//调用参数
		$this->_checkUser();
//		$this->_inject_check(1);//调用过滤函数


	}

	//检测报单中心是否存在
	public function check_shopid() {
		$fck = M('fck');
        $mapp               = array();
		$mapp['user_id']	= trim($_POST['shopid']);
		$rs  = $fck->where($mapp)  -> field('id')->find();
		if($rs) {
			$this->success(' ');
			exit;
		}else{
			$this->error('没有此报单中心！');
			exit;
		}

    }
	//检测推荐人是否存在
	public function check_reid() {
		$fck = M('fck');
        $mapp               = array();
		$mapp['user_id']	= trim($_POST['reid']);
		$rs  = $fck->where($mapp)  -> field('id,user_name')->find();
		if($rs) {
			$this->success('姓名：'.$rs['user_name']);
			exit;
		}else{
			$this->error('没有此推荐人！');
			exit;
		}

    }
	//检测接点人是否存在
	public function check_fid() {
		$fck = M('fck');
        $mapp               = array();
		$mapp['user_id']	= trim($_POST['fid']);
		$rs  = $fck->where($mapp)  -> field('id,user_name')->find();
		if($rs) {
			$this->success('姓名：'.$rs['user_name']);
			exit;
		}else{
			$this->error('没有此接点人！');
			exit;
		}

    }

	//检测用户名(会员名)是否已经存在
	public function check_userid() {
		$fck = M('fck');
        $mapp               = array();
		$mapp['user_id']	= trim($_POST['userid']);
		$rs  = $fck->where($mapp)  -> field('id')->find();
		if($rs) {
			$this->error('会员编号已被使用！');
			exit;
		}else{
			// if(!preg_match("/^1[34578]\d{9}$/",$_POST['userid'])){
			// 	$this->error('请输入11位手机号码！');
			// 	exit;
			// }else{
				$this->success('会员编号可使用！');
				exit;
			// }
		}

    }

	//检测用户名(会员名)是否已经存在
	public function check_CCuser() {
		$fck = M('fck');
        $mapp               = array();
		$mapp['user_id']	= trim($_POST['userid']);
		$rs  = $fck->where($mapp)  -> field('id,user_name')->find();
		if($rs) {
			$this->success('姓名：'.$rs['user_name']);
			exit;
		}else{
			$this->error('会员编号输入错误！');
			exit;
		}

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
				$_SESSION['Urlszpass'] = 'MyssShuiPuTao';
				$bUrl = __URL__.'/menber';//未开通会员
				$this->_boxx($bUrl);
				break;
			case 2;
				$this->users(2);//会员注册
				$_SESSION['Urlszpass'] = 'MyssBoLuo';
				break;
			case 3;
				$_SESSION['Urlszpass'] = 'MyssHuoLongGuo';
				$bUrl = __URL__.'/relations';//直接推荐
				$this->_boxx($bUrl);
				break;
			case 4;
				if($list['is_agent'] >=2){
					$this->error('您已经是报单中心!');
            		exit();
				}
				$_SESSION['Urlszpass'] = 'MyssXiGua';
				$bUrl = __URL__.'/agents';//申请代理
                $this->_boxx($bUrl);
				break;
			case 5;
				$_SESSION['Urlszpass'] = 'MyssShiLiu';
				$bUrl = __URL__.'/finance';//财务明细表
				$this->_boxx($bUrl);
				break;
			case 6;
				$_SESSION['Urlszpass'] = 'MyssFenYingTao';
				$bUrl = __URL__.'/transferMoney';//K币账户转账
				$this->_boxx($bUrl);
				break;
			case 7;

				$_SESSION['Urlszpass'] = 'MyssPaoYingTao';
				$bUrl = __URL__.'/frontCurrency';//K币账户提现
				$this->_boxx($bUrl);
				break;
			case 8;
				$_SESSION['Urlszpass'] = 'MyssDaShuiPuTao';
				$bUrl = __URL__.'/frontMenber';//已开通会员
				$this->_boxx($bUrl);
				break;
			case 9;
				$_SESSION['Urlszpass'] = 'MyssShiLiu';
				$bUrl = __URL__.'/financeTable';//会员财务表
				$this->_boxx($bUrl);
				break;
			case 10;
				$_SESSION['Urlszpass'] = 'MyssMangGuo';
				$bUrl = __URL__.'/currencyRecharge';//充值
				$this->_boxx($bUrl);
				break;
			case 11;
                $_SESSION['Urlszpass'] = 'Mysssingle';
                $bUrl = __URL__.'/single';//申请加单
                $this->_boxx($bUrl);
                break;
            case 12;
                $_SESSION['Urlszpass'] = 'Myssbusiness';
                $bUrl = __URL__.'/business';//业务总统计
                $this->_boxx($bUrl);
                break;
            case 13;
				$_SESSION['Urlszpass'] = 'MyssXiGuaJb';
				$bUrl = __URL__.'/sq_jb';//申请金币中心
                $this->_boxx($bUrl);
				break;
			case 15;
				$_SESSION['Urlszpass'] = 'MyssXiGuaMsg';
				$bUrl = __URL__.'/messages';//短信留言
                $this->_boxx($bUrl);
				break;
			case 17;
                $_SESSION['Urlszpass'] = 'Myssxiaofei';
                $bUrl = __URL__.'/frontxiaofei';//消费申请
                $this->_boxx($bUrl);
                break;
            case 18;
                $_SESSION['Urlszpass'] = 'Myssjinji';
                $bUrl = __URL__.'/MenberJinji';//升级
                $this->_boxx($bUrl);
                break;
			case 19;
				$_SESSION['Urlszpass'] = 'Mysssqzy';
				$bUrl = __URL__.'/sq_zy';//短信留言
                $this->_boxx($bUrl);
				break;
			case 16;
				$_SESSION['Urlszpass'] = 'Mysskai';
				$bUrl = __URL__.'/user_level';//会员升级
				$this->_boxx($bUrl);
				break;
			case 20;
				$_SESSION['Urlszpass'] = 'MyGive';
				$bUrl = __URL__.'/transferMoney2';
				$this->_boxx($bUrl);
				break;
			default;
				$this->error('二级密码错误!');
				exit;
		}
	}

	


	public function transferMoney2($Urlsz=0){
		if ($_SESSION['Urlszpass'] == 'MyGive'){
			$zhuanj = M('zhuanj');
	
			$map['out_uid'] = $_SESSION[C('USER_AUTH_KEY')];
			$map['is_fh']=1;
			// $map['_logic'] = 'or';
		$fee=M('fee');
		$fee_rs=$fee->find();
		$s6=$fee_rs['s6'];
		$str11=$fee_rs['str11'];
		$this->assign('str11',$str11);
	
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

			// if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
			// 	$user_id=$_POST['user_id'];
			// 	$sk=json_encode($user_id);
			// 	echo $sk;
			// 	// $dizhi=$fck->where('user_id='.$user_id)->find();
       			 
   //  //    			 echo $sk;
       
   //    die;
			

			// }
			$this->display('transferMoney2');
		
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
		$fee= M('fee');
		$fee_rs=$fee->find();
		$s6=$fee_rs['s6'];
		$str11=$fee_rs['str11'];
		$this->assign('str11',$str11);
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
	$res=$fck->where('user_id='.$UserID)->find();
		$agent_cash=$res['agent_cash'];
		$sum=$s6*$ePoints;
		if($agent_cash < $sum){
			$this->error('注册币不足!');
			exit;
		}


		if($ePoints <= 0){
			$this->error('输入的单数有误!');
			exit;
		}

		if($ePoints>$str11){
					$this->error('当日加单不能超过'.$str11.'股！');
					exit;
				}

				$aa=$res['jia_nums']+$ePoints;
				
				if($aa>$str11){
					$re=$str11-$res['jia_nums'];
				}
				if($re){
					$this->error('当天还可以加'.$re.'单！');
					exit;
				}

				if($fck_rs['加_nums']>$str11){
					$this->error('当天加单已达到'.$str11.'单！');
					exit;
				}
			// if($UserID == $f['user_id']){
			// 	$this->error('不能加给自己!');
			// 	exit;
			// }
		
		$this->_transferMoneyConfirm($UserID,$ePoints,$content,$select);
	}



private function _transferMoneyConfirm($UserID='0',$ePoints=0,$content=null,$select=0){
		
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
			
				$zz_content = "给其他会员加单";
				
				$fck->execute("update `xt_fck` Set `is_cc`=is_cc+".$ePoints." where `id`=".$vo['id']);
			
			$nowdate = time();
			$data = array();
			// $data['uid']           = $ID;          //转出会员ID
			// $data['user_id']       = $UserID;
			// $data['did']           = $vo['id'];    //转入会员ID
			// $data['user_did']      = $vo['user_id'];
			// $data['action_type']   = 28;    //转入还是转出
			// $data['pdt']           = $nowdate;     //转帐时间
			// $data['epoints']       = $ePoints;     //进出金额
			// $data['allp']          = 0;
			// $data['bz']            = $zz_content;     //备注
			// $data['type']          = 1;   		   //1转帐
			// $history->create();
			// $rs2=$history->add($data);
			// unset($data);
			//转账表
			$data = array();
			$data['in_uid']        = $vo['id'];           //转入会员ID
			$data['out_uid']       = $ID;
			$data['in_userid']     = $vo['user_id'];      //转入会员的登录帐号
			$data['out_userid']    = $inUserID;
			$data['epoint']        = $ePoints;            //进出金额
			$data['rdt']           = $nowdate;            //转帐时间
// 			$data['sm']            = $content;            //转帐说明
			$data['is_fh']       = 1;                 
			$data['type']   = 1;
			$zhuanj->create();
			$rs4=$zhuanj->add($data);
			unset($data);
	
			//无错误提交数据
			if ($rs4){
				$history->commit();//提交事务

				$fck->_givegp($vo['id'],$user_id,$ePoints);

				$bUrl = __URL__.'/transferMoney2';
				$this->_box(1,'操作成功！',$bUrl,1);
				exit;
			}else{
				$history->rollback();//事务回滚：
				$this->error('输入数据错误！');
			}



		
	}

 //会员资金查询(显示会员每一期的各奖奖金)
    public function financeTable($cs = 0) {
        $bonus = M('bonus1');  //奖金表
        $fck=M('fck');
      
        $where = array();
        $myid = $_SESSION[C('USER_AUTH_KEY')];
        $fck_rs=$fck->where('id='.$myid)->field('*')->find();
        $this->assign('fck_rs',$fck_rs);
        $ckid = (int) $_REQUEST['cid'];
        if ($myid == 1) {
            if (empty($ckid)) {
                $where['uid'] = $myid;
            } else {
                $where['uid'] = $ckid;
            }
        } else {
            $where['uid'] = $myid;
        }
        $this->assign('cid', $ckid);

        if (!empty($_REQUEST['FanNowDate'])) {  //日期查询
            $time1 = strtotime($_REQUEST['FanNowDate']);                // 这天 00:00:00
            $time2 = strtotime($_REQUEST['FanNowDate']) + 3600 * 24 - 1;   // 这天 23:59:59
            // $where['e_date'] = array(array('egt', $time1), array('elt', $time2));
            //$where['e_date'] = array('eq',$time1);
        }

        $field = '*';
        //=====================分页开始==============================================
        import("@.ORG.ZQPage");  //导入分页类
        $count = $bonus->where($where)->count(); //总页数
        $listrows = C('ONE_PAGE_RE'); //每页显示的记录数
        if (!empty($ckid)) {
            $page_where = 'cid=' . $ckid . '&FanNowDate=' . $_REQUEST['FanNowDate']; //分页条件
        } else {
            $page_where = 'FanNowDate=' . $_REQUEST['FanNowDate']; //分页条件
        }
        $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
        //===============(总页数,每页显示记录数,css样式 0-9)
        $show = $Page->show(); //分页变量
        $this->assign('page', $show); //分页变量输出到模板
        $list = $bonus->where($where)->field($field)->order('id desc')->page($Page->getPage() . ',' . $listrows)->select();

        foreach ($list as $key => $value) {
        	for($i=8;$i<50;$i++){
        		if($value['b'.$i]!=0){
        			$this->assign('b'.$i,$value['b'.$i]);
        		}
        	}
        }
        $this->assign('list', $list); //数据输出到模板
        //=================================================
        //各项奖每页汇总
        $count = array();
        foreach ($list as $vo) {
            for ($b = 0; $b <= 100; $b++) {
                $count[$b] += $vo['b' . $b];
                $count[$b] = $this->_2Mal($count[$b], 2);
            }
        }



        $fee = M('fee');    //参数表
        $fee_rs = $fee->field('s18,s13,s14')->find();
        $fee_s7 = explode('|', $fee_rs['s13']);
        $s14 = explode('|', $fee_rs['s14']);

        $one=$s14[0]+$s14[1]+$s14[2]+$s14[3]+$s14[4]+$s14[5]+$s14[6]+$s14[7]+$s14[8]+$s14[9]+$s14[10]+$s14[11];
        $two=$fck_rs['is_cc']-1;
        
        if($two>0){
        	$count_fh=$one*$two;
        	$this->assign('two',$two);
        }
        $this->assign('s14',$s14);
        $this->assign('count_fh',$count_fh);
        $this->assign('fee_s7', $fee_s7);        //输出奖项名称数组

        $this->assign('b_b', $b_b);
        $this->assign('c_b', $c_b);
        $this->assign('count', $count);
        $this->display('financeTable');
    }


    public function financeShow() {
        //奖金明细
        //if ($_SESSION['Urlszpass'] == 'MyssShiLiu'){
        $history = M('history');
        $fee = M('fee');
        $fee_rs = $fee->field('s13')->find();
        $date = $fee_rs['s13'];
        $myid = $_SESSION[C('USER_AUTH_KEY')];
        $ckid = (int) $_REQUEST['cid'];
        if ($myid == 1) {
            if (empty($ckid)) {
                $UID = $myid;
            } else {
                $UID = $ckid;
            }
        } else {
            $UID = $myid;
        }

        $RDT = (int) $_REQUEST['RDT'];
        $PDT = (int) $_REQUEST['PDT'];
        $cPDT = $PDT + 24 * 3600 - 1;
        $lastdate = mktime(0, 0, 0, date("m"), date("d") - $date, date("Y"));
        //$map['pdt'] = array(array('egt',$PDT),array('elt',$cPDT));
        $map['uid'] = $UID;
        //$map['allp'] = 0;

        $map = "uid={$UID} and action_type+0>7 and action_type+0<100";
        $field = '*';
        //=====================分页开始==============================================
        import("@.ORG.ZQPage");  //导入分页类
        $count = $history->where($map)->count(); //总页数
        $listrows = C('PAGE_LISTROWS'); //每页显示的记录数
        if (!empty($ckid)) {
            $page_where = 'cid=' . $ckid . '&PDT=' . $PDT; //分页条件
        } else {
            $page_where = 'PDT=' . $PDT; //分页条件
        }
        $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
        //===============(总页数,每页显示记录数,css样式 0-9)
        $show = $Page->show(); //分页变量
        $this->assign('page', $show); //分页变量输出到模板
        $list = $history->where($map)->field($field)->order('id desc')->page($Page->getPage() . ',' . $listrows)->select();

        $this->assign('list', $list); //数据输出到模板
        //=================================================
//            $fee = M ('fee');
//            $fee_rs = $fee->field('s18')->find();
//            $s18 = explode('|',$fee_rs['s18']);
//            $this->assign('s18',$s18);

        $fee = M('fee');    //参数表
        $fee_rs = $fee->field('s18,s13')->find();
        $fee_s7 = explode('|', $fee_rs['s13']);
        $this->assign('fee_s7', $fee_s7);        //输出奖项名称数组

        $this->display('financeShow');
//			}else{
//			$this->error ('错误!');
//			exit;
//		}
    }



	//推荐列表
	public function frontMenber($Urlsz=0){
		//列表过滤器，生成查询Map对象
		if ($_SESSION['Urlszpass'] == 'MyssDaShuiPuTao'){
			$fck = M('fck');
			$id = $_SESSION[C('USER_AUTH_KEY')];
			$map = array();
			$map['re_id'] = $id;
			//$map['is_pay'] = array('egt',0);
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

			//if (! empty ( $fck )) {
			//	$this->_list ( $fck, $map,'id',0 );
			//}

            //查询字段
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

			$this->display ('frontMenber');
			exit;
		}else{
			$this->error('数据错误2!');
			exit;
		}
	}

	

	public function xxxx() {
        $fck = D('Fck');
        // $fck->rifenhong();
        $fck->fenhong();
     
        echo "<p style='text-align:center;color:blue;margin-top:100px'>分红成功</p>";
        
    }

	public function xxx() {
        $fck = D('Fck');
        // $fck->rifenhong();
        $fck->fenhong();
        $bUrl = __APP__ . '/Index/backmanagerIndex';
        $this->_box(1, '分红成功！', $bUrl, 1);
//         echo "<p style='text-align:center;color:blue;margin-top:100px'>分红成功</p>";
        
    }


	public function relations($Urlsz=0){
		//推荐关系
		if ($_SESSION['Urlszpass'] == 'MyssHuoLongGuo'){
			$fck = M('fck');
			$UserID = $_REQUEST['UserID'];
			if (!empty($UserID)){
				$map['user_id'] = array('like',"%".$UserID."%");
			}

//			if (!empty($_GET['bj_id'])){
//				$map['re_id'] = (int) $_GET['bj_id'];
//			}else{
//				$map['re_id'] = $_SESSION[C('USER_AUTH_KEY')];//自身推荐
//			}
			
				$map['re_id'] = $_SESSION[C('USER_AUTH_KEY')];//自身推荐
				$map['is_pay'] = 1;
				 $field  = '*';
	            //=====================分页开始==============================================
	            import ( "@.ORG.ZQPage" );  //导入分页类
	            $count = $fck->where($map)->count();//总页数
	    	    $listrows = C('ONE_PAGE_RE');//每页显示的记录数
				$page_where = 'UserID='.$UserID;//分页条件
	//            $page_where ='';
	            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
	            //===============(总页数,每页显示记录数,css样式 0-9)
	            $show = $Page->show();//分页变量
	            $this->assign('page',$show);//分页变量输出到模板
	            $list = $fck->where($map)->field($field)->order('pdt desc')->page($Page->getPage().','.$listrows)->select();
	            $HYJJ = '';
	            $this->_levelConfirm($HYJJ,1);
	            $this->assign('voo',$HYJJ);//会员级别
	            $this->assign('list',$list);//数据输出到模板
	            unset($list);
	            //=================================================
			if($_GET['q']==2){
				$map['re_id'] = $_SESSION[C('USER_AUTH_KEY')];//自身推荐
				$map['is_pay'] = 1;
				$field  = '*';
				$rew=$fck->where($map)->field($field)->order('pdt desc')->select();
				
				foreach ($rew as $key => $value) {
					$one=$fck->where('re_id='.$value['id'])->field('*')->order('pdt desc')->select();
					if($one){
					foreach ($one as $k => $v) {
						$list[]=$v;
					}
						
						}
				}
				$this->assign('list',$list);
				// print_r($list);die;
			}
			
			

           

			$this->display ('relations');
			return;
		}else{
			$this->error('数据错误2!');
			exit;
		}
	}

	//=============撤销提现
	public function revocAtionMoney(){
	    if ($_SESSION['Urlszpass'] == 'MyssPaoYingTao'){  //提现权限session认证
			$tiqu = M ('tiqu');
			$uid = $_SESSION[C('USER_AUTH_KEY')];
			$id = (int) $_GET['id'];
	    	$where = array();
	    	$where['id']  = $id;
	        $where['uid'] = $uid;   //申请提现会员ID
	        $where['is_pay'] = 0;            //申请提现是否通过
	        $field = 'id,money,uid';
	        $trs = $tiqu ->where($where)->field($field)->find();
	        if ($trs){
	            $fck = M ('fck');
	            $fck->execute("UPDATE __TABLE__ SET agent_use=agent_use+{$trs['money']} WHERE id={$trs['uid']}");
	            $tiqu->where($where)->delete();
	            $bUrl = __URL__.'/frontCurrency';
                $this->_box(1,'撤销提现！',$bUrl,1);
                exit;
	        }else{
	        	$this->error('没有该记录!');
                exit;
	        }
	    }else{
            $this->error('错误!');
            exit;
        }
	}

	public function financeInfo(){
		$fck = M('fck');
		$id = (int) $_GET['id'];
		$rs = $fck -> find($id);
		if(!$rs){
			$id = $_SESSION[C('USER_AUTH_KEY')];
			$rs = $fck -> find($id);
		}

		$bid = $_SESSION[C('USER_AUTH_KEY')];

		$k = 0;
		if($id != $bid){
			$arr = explode(',',$rs['p_path']);
			foreach($arr as $voo){
				if($voo == $bid){
					$k = 1;
					break;
				}
			}
			if($k == 0){
				$this->error ('错误!');
				exit;
			}
		}

		$this -> assign('rs',$rs);
		$this->display('financeInfo');
	}

	//===================================================货币消费
	public function frontxiaofei($Urlsz=0){
		if ($_SESSION['Urlszpass'] == 'Myssxiaofei'){
			$tiqu = M('xiaof');
			$fck = M('fck');
			$map['uid'] = $_SESSION[C('USER_AUTH_KEY')];

			$field  = '*';
            //=====================分页开始==============================================
            import ( "@.ORG.ZQPage" );  //导入分页类
            $count = $tiqu->where($map)->count();//总页数
    	    $listrows = C('ONE_PAGE_RE');//每页显示的记录数
            $Page = new ZQPage($count,$listrows,1);
            //===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show();//分页变量
            $this->assign('page',$show);//分页变量输出到模板
            $list = $tiqu->where($map)->field($field)->order('id desc')->page($Page->getPage().','.$listrows)->select();
            $this->assign('list',$list);//数据输出到模板
            //=================================================

			$where = array();
			$ID = $_SESSION[C('USER_AUTH_KEY')];
			$where['id'] = $ID;
			$field = '*';
			$rs = $fck ->where($where)->field($field)->find();
			$this->assign('type',$ID);
			$this->assign('rs',$rs);

			$fee = M('fee');
			$fee_rs = $fee->find();
			$this->assign('fee_rs',$fee_rs);

			unset($tiqu,$fck,$where,$ID,$field,$rs);
			$this->display ();
			return;
		}else{
			$this->error ('错误!');
			exit;
		}
	}

	//=================================================消费处理
	public function frontxiaofeiAC(){
		if ($_SESSION['Urlszpass'] == 'Myssxiaofei'){  //提现权限session认证
			$ePoints = trim($_POST['ePoints']);
			$fck = D ('Fck');
			$history = M ('history');
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

			$fee = M('fee');
			$fee_rs = $fee->field('s11')->find();
			$one_m = $fee_rs['s11'];
			if($ePoints<$one_m){
				$this->error ('投资额度为 '.$one_m.' 的整数倍！');
				exit;
			}
			if($ePoints%$one_m>0){
				$this->error ('投资额度为 '.$one_m.' 的整数倍！');
				exit;
			}

			$where = array();
			$ID = $_SESSION[C('USER_AUTH_KEY')];
			$tiqu = M ('xiaof');                      //消费表
			//查询条件
			$where['id'] = $ID;
			$field ='*';
			$fck_rs = $fck ->where($where)->field($field)->find();
			if (!$fck_rs){
				$this->error('没有该会员!');
				exit;
			}

			$inUserID = $fck_rs['user_id'];
			$AgentUse = $fck_rs['agent_cash'];
			if ($AgentUse < $ePoints){
				$this->error('您的报单账户余额不足!');
				exit;
			}

			$ePoints_two = (int)($ePoints/$one_m);

			$nowdate = strtotime(date('c'));

			$data				= array();
			$data['uid']		= $fck_rs['id'];
			$data['user_id']	= $inUserID;
			$data['rdt']		= $nowdate;
			$data['money']		= $ePoints;
			$data['money_two']	= $ePoints_two;
			$data['epoint']	= $ePoints;
			$data['is_pay']	= 0;
			$rs2 = $tiqu->add($data);
			unset($data);
			if ($rs2){

				$fck->execute("UPDATE __TABLE__ SET agent_cash=agent_cash-".$ePoints.",gp_num=gp_num+".$ePoints_two." WHERE id={$fck_rs['id']}");

				$bUrl = __URL__.'/frontxiaofei';
				$this->_box(1,'投资成功！',$bUrl,1);
				exit;
			}else{
				$this->error('投资失败！');
				exit;
			}
		}else{
			$this->error('错误！');
			exit;
		}
	}

	//=============撤销消费
	public function frontxiaofeiDEL(){
	    if ($_SESSION['Urlszpass'] == 'Myssxiaofei'){  //提现权限session认证
			$tiqu = M ('xiaof');
			$uid = $_SESSION[C('USER_AUTH_KEY')];
			$id = (int) $_GET['id'];
	    	$where = array();
	    	$where['id']  = $id;
	        $where['uid'] = $uid;   //申请提现会员ID
	        $where['is_pay'] = 0;            //申请提现是否通过
	        $field = 'id,money,uid';
	        $trs = $tiqu ->where($where)->field($field)->find();
	        if ($trs){
	            $fck = M ('fck');
	            $fck->execute("UPDATE __TABLE__ SET agent_cash=agent_cash+{$trs['money']} WHERE id={$trs['uid']}");
	            $tiqu->where($where)->delete();
	            $bUrl = __URL__.'/frontxiaofei';
                $this->_box(1,'撤销消费成功！',$bUrl,1);
                exit;
	        }else{
	        	$this->error('没有该记录!');
                exit;
	        }
	    }else{
            $this->error('错误!');
            exit;
        }
	}


		//==========================货币充值
	public function currencyRecharge(){
		if ($_SESSION['Urlszpass'] == 'MyssMangGuo'){
			$chongzhi = M('chongzhi');
			$fck = M('fck');
			$map['uid'] = $_SESSION[C('USER_AUTH_KEY')];

			$field  = '*';
            //=====================分页开始==============================================
            import ( "@.ORG.ZQPage" );  //导入分页类
            $count = $chongzhi->where($map)->count();//总页数
            $listrows = C('ONE_PAGE_RE');//每页显示的记录数
            $Page = new ZQPage($count,$listrows,1);
            //===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show();//分页变量
            $this->assign('page',$show);//分页变量输出到模板
            $list = $chongzhi->where($map)->field($field)->order('id desc')->page($Page->getPage().','.$listrows)->select();
            $this->assign('list',$list);//数据输出到模板
            //=================================================

			$where = array();
			$fwhere = array();
			$where['id'] = 1;
			$fwhere['id'] = $_SESSION[C('USER_AUTH_KEY')];
			$field = '*';
			$rs = $fck ->where($where)->field($field)->find();
			$frs = $fck ->where($fwhere)->field($field)->find();
			$this->assign('rs',$rs);
			$this->assign('frs',$frs);

			$nowdate[] =array();
			$nowdate[0] = date('Y');
			$nowdate[1] =date('m');
			$nowdate[2] =date('d');

			$this->assign('nowdate',$nowdate);

			$fee_rs = M ('fee') -> find();
			$this -> assign('s8',$fee_rs['s8']);
			$this -> assign('s9',$fee_rs['s9']);
			$this -> assign('s17',$fee_rs['s17']);
			$this->display ('currencyRecharge');
			return;
		}else{
			$this->error ('错误!');
			exit;
		}
	}
	public function currencyRechargeAC(){
		if ($_SESSION['Urlszpass'] == 'MyssMangGuo'){
			$fck = M ('fck');
			$ID = $_SESSION[C('USER_AUTH_KEY')];
			$rs = $fck -> field('is_pay,user_id') -> find($ID);
			if($rs['is_pay'] == 0){
				$this->error('临时会员不能充值！');
                exit;
			}
			$inUserID=$rs['user_id'];

			$ePoints = trim($_POST['ePoints']);
			$stype = (int) trim($_POST['stype']);
			$chongzhi = M('chongzhi');
			if (!$chongzhi->autoCheckToken($_POST)){
				$this->error('页面过期，请刷新页面！');
                exit;
			}
			if (empty($ePoints) || !is_numeric($ePoints)){
				$this->error('金额不能为空!');
				exit;
			}
			if (strlen($ePoints)>9){
				$this->error ('金额太大!');
				exit;
			}
			if ($ePoints<=0){
				$this->error ('金额格式不对!');
				exit;
			}
			if($stype>1){
				$stype=1;
			}

			$id =  $_SESSION[C('USER_AUTH_KEY')];
			$where = array();
			$where['uid'] = $id;
			$where['is_pay'] = 0;
			$field1 = 'id';
			$vo3 = $chongzhi ->where($where)->field($field1)->find();
			if ($vo3){
				$this->error('上次充值还没通过审核!');
				exit;
			}
			//开始事务处理
			$chongzhi->startTrans();

			//充值表
			$_money = trim($_POST['_money']);  //已汇款数额
			$_num = trim($_POST['_num']);  // 汇款到账号
			$_year = trim($_POST['_year']); // 年
			$_month = trim($_POST['_month']);  //月
			$_date = trim($_POST['_date']);  //日
			$_hour = trim($_POST['_hour']);  //小时


			if (empty($_money) || !is_numeric($_money)){
				$this->error('请输入数字或金额不能为空!');
				exit;
			}
			if (empty($_num) || !is_numeric($_num)){
				$this->error('请输入数字或账号不能为空!');
				exit;
			}
			if (empty($_year) || !is_numeric($_year)){
				$this->error('请输入数字或年不能为空!');
				exit;
			}
			if (empty($_month) || !is_numeric($_month)){
				$this->error('请输入数字或月不能为空!');
				exit;
			}
			if (empty($_date) || !is_numeric($_date)){
				$this->error('请输入数字或日不能为空!');
				exit;
			}
			if (empty($_hour) || !is_numeric($_hour)){
				$this->error('请输入数字或小时不能为空!');
				exit;
			}


			//$nowdate = strtotime(date('c'));
			$nowdate = strtotime(date($_year.'-'. $_month.'-'.$_date.' '. $_hour.':00:00'));

			$data = array();
			$data['uid']     = $id;
			$data['user_id'] = $inUserID;
			$data['huikuan'] = $_money;
			$data['zhuanghao'] = $_num;
			$data['rdt']     = $nowdate;
			$data['epoint']  = $ePoints;
			$data['is_pay']  = 0;
			$data['stype']  = $stype;

			$rs2 = $chongzhi->add($data);
			unset($data,$id);
			if ($rs2){
				//提交事务
				$chongzhi->commit();
				$bUrl = __URL__.'/currencyRecharge';
				$this->_box(1,'注册币充值成功，请等待后台审核！',$bUrl,1);
				exit;
			}else{
				//事务回滚：
				$chongzhi->rollback();
				$this->error('货币充值失败');
				exit;
			}
		}else{
			$this->error ('错误!');
			exit;
		}
	}

	public function currencyRechargeAC2(){/////////////////////////////////报单中心申请金额
		if ($_SESSION['Urlszpass'] == 'MyssXiGua'){
			$fck = M ('fck');
			$ID = $_SESSION[C('USER_AUTH_KEY')];
			$rs = $fck -> field('is_pay') -> find($ID);
			if($rs['is_pay'] == 0){
				$this->error('临时会员不能充值！');
                exit;
			}


			$ePoints = 3000;
			$chongzhi = M('chongzhi');
			if (!$chongzhi->autoCheckToken($_POST)){
				$this->error('页面过期，请刷新页面！');
                exit;
			}
			if (empty($ePoints) || !is_numeric($ePoints)){
				$this->error('金额不能为空!');
				exit;
			}
			if (strlen($ePoints)>9){
				$this->error ('金额太大!');
				exit;
			}
			if ($ePoints<=0){
				$this->error ('金额格式不对!');
				exit;
			}
			$id =  $_SESSION[C('USER_AUTH_KEY')];
			$inUserID =  $_SESSION['loginUseracc'];
			$where = array();
			$where['uid'] = $id;
			$where['is_pay'] = 0;
			$field1 = 'id';
			$vo3 = $chongzhi ->where($where)->field($field1)->find();
			if ($vo3){
				$this->error('上次加单还没通过审核!');
				exit;
			}
			//开始事务处理
			$chongzhi->startTrans();
			//充值表
			$nowdate = strtotime(date('c'));
			$data = array();
			$data['uid'] = $id;
			$data['user_id'] = $inUserID;
			$data['rdt'] = $nowdate;
			$data['epoint'] = $ePoints;
			$data['is_pay'] = 0;
			$rs2 = $chongzhi->add($data);
			unset($data,$id);
			if ($rs2){
				//提交事务
				$chongzhi->commit();
				$bUrl = __URL__.'/currencyRecharge';
				$this->_box(1,'加单申请操作成功！',$bUrl,1);
				exit;
			}else{
				//事务回滚：
				$chongzhi->rollback();
				$this->error('申请失败');
				exit;
			}
		}else{
		}
	}

    public function business(){
    	//业务总统计
    	if ($_SESSION['Urlszpass'] == 'Myssbusiness'){
			$id =  $_SESSION[C('USER_AUTH_KEY')];
    		$fck = M ('fck');
			$where = array();
			$where['id'] = $id;
			$field1 = 'user_id,zjj,agent_use,agent_cash';
			$rs = $fck ->where($where)->field($field1)->find();
			if($rs){

				//=========奖金提现
				$tiqu = M ('tiqu');
				$t_where = array();
				$t_where['uid'] = $id;
				$t_where['is_pay'] = 1;
				$rs1 = $tiqu ->where($t_where)->sum('money');
				$rs2 = $tiqu ->where($t_where)->sum('money_two');
				$rs2 = $rs1-$rs2;

				//=========注册币充值
				$chongzhi = M ('chongzhi');
				$c_where = array();
				$c_where['uid'] = $id;
				$c_where['is_pay'] = 1;
				$rs3 = $chongzhi ->where($c_where)->sum('epoint');

				//=========注册划出
				$z_where = array();
				$z_where['shop_id'] = $id;
				$z_where['is_pay'] = 1;
				$rs4 = $fck ->where($z_where)->sum('cpzj');

				//=========货币借出
				$zhuanj = M ('zhuanj');
				$z_where = array();
				$z_where['out_uid'] = $id;
				$rs5 = $zhuanj ->where($z_where)->sum('epoint');

				//=========货币借入
				$z_where = array();
				$z_where['in_uid'] = $id;
				$rs6 = $zhuanj ->where($z_where)->sum('epoint');

				$Zong = array();
				$Zong[0] = $rs['user_id'];  				  //会员名
				$Zong[1] = $this->_2Mal($rs['zjj'],2);        //总奖金
				$Zong[2] = $this->_2Mal($rs1,2);      		  //总提现
				$Zong[3] = $this->_2Mal($rs2,2);     		  //提现手续
				$Zong[4] = $this->_2Mal($rs3,2);     		  //注册币充值
				$Zong[5] = $this->_2Mal($rs4,2);     		  //注册划出
				$Zong[6] = $this->_2Mal($rs5,2);     		  //货币借出
				$Zong[7] = $this->_2Mal($rs6,2);     		  //货币借入
				$Zong[8] = $this->_2Mal($rs['agent_use'],2);  //剩余奖金
				$Zong[9] = $this->_2Mal($rs['agent_cash'],2); //剩余注册币

				$history = M('history');
				$Userid=$_REQUEST['UserID'];
				$type=$_REQUEST['type'];
				if(empty($Userid)){
					$Userid=$_SESSION['loginUseracc'];
				}

				$where = "(user_id='{$Userid}' or user_did='{$Userid}')";
				if ($type > 0 ) {
					$where .= " and type=".$type;
				}
				$field = '*';
				//=====================分页开始==============================================
				import ( "@.ORG.ZQPage" );  //导入分页类
				$count = $history->where($where)->count();//总页数
				$listrows = C('ONE_PAGE_RE');//每页显示的记录数
				$page_where = 'UserID='.$Userid.'&type='.$type;//分页条件
				$Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
				//===============(总页数,每页显示记录数,css样式 0-9)
				$show = $Page->show();//分页变量
				$this->assign('page',$show);//分页变量输出到模板
				$list = $history->where($where)->field($field)->order('id desc')->page($Page->getPage().','.$listrows)->select();

				$this->assign('list',$list);//数据输出到模板
				//=================================================

				$this->assign('Zong',$Zong);
    			$this->display();
			}else{
				$this->error ('错误!');
				exit;
			}
    	}else{
            $this->error ('错误!');
            exit;
        }
    }

	public function level_menber(){      //查看下级会员
		$level = 1;

		if($level <=0 or $level >=7 ){
			$this -> error ('级别错误!');
			exit;
		}

		$fck = M ('fck');
		$ID = $_SESSION[C('USER_AUTH_KEY')];  //登录会员ID(自动编号)
		$where = array();

		$where['p_path'] = array('like','%,'. $ID .',%');
		$rs = $fck -> where($where) -> field('p_path,id,is_pay') -> select();  //找出该会员的所有下级会员
		$id_str = ',';
		foreach($rs as $rss){
			$p_path = trim($rss['p_path'],',');  //去除路径两边的逗号
			$p_path = explode(',',$p_path);  //分解为数组
			rsort($p_path);  //数组按倒序排序

			$l = $level - 1;
			if($p_path[$l] == $ID){
				$id_str .=  $rss['id'] .',';
			}
		}

		$id_str = trim($id_str,',');  //去除路径两边的逗号
		//$list = $fck -> select($id_str);

		$map = array();
		$field = '*';
		$map['id'] = array('in',$id_str);
        //=====================分页开始==============================================
        import ( "@.ORG.ZQPage" );  //导入分页类
        $count = $fck->where($map)->count();//总页数
        $listrows = 10 ;//每页显示的记录数
        $Page = new ZQPage($count,$listrows,1);
        //===============(总页数,每页显示记录数,css样式 0-9)
        $show = $Page->show();//分页变量
        $this->assign('page',$show);//分页变量输出到模板
        $list = $fck->where($map)->field($field)->order('id desc')->page($Page->getPage().','.$listrows)->select();
        $this->assign('list',$list);//数据输出到模板
        //=================================================

		$rs = $fck -> field('user_id') -> find($ID);
		$this -> assign('rs',$rs);

		$this -> display('level_menber');
	}

	//会员升级
	public function user_level(){
		if ($_SESSION['Urlszpass'] == 'Mysskai' ){
			$fck = M("fck");
			$fee = M ('fee');
			$ul = M('ulevel');

			$id = $_SESSION[C('USER_AUTH_KEY')];
			$fee_rs = $fee->find();
			$s10 = explode('|',$fee_rs['s10']);
			$s9 = explode('|',$fee_rs['s9']);//金额

			$wehre = array();
			$f_where = array();
			$f_rs = $fck -> field("p_path,u_level,p_level")->find($id);

			$this->assign('f',$f_rs);//会员级别
			$this->assign('s10',$s10);//输级会员级别
			$this->assign('s9',$s9);


			$map['uid'] = $id;
			//查询条件
			$where['id'] = $id;
			$field ='user_id,user_name,agent_max,agent_use,is_agent,agent_cash,u_level,nickname,cpzj,re_nums';
			$fck_rs = $fck ->where($where)->field($field)->find();
			$field  = '*';
			//=====================分页开始==============================================
			import ( "@.ORG.ZQPage" );  //导入分页类
			$count = $ul->where($map)->count();//总页数
			$listrows = C('ONE_PAGE_RE');//每页显示的记录数
			$Page = new ZQPage($count,$listrows,1);
			//===============(总页数,每页显示记录数,css样式 0-9)
			$show = $Page->show();//分页变量
			$this->assign('page',$show);//分页变量输出到模板
			$list = $ul->where($map)->field($field)->order('id desc')->page($Page->getPage().','.$listrows)->select();
			$HYJJ = '';
			$this->_levelConfirm($HYJJ,1);
			$this->assign('HYJJ',$HYJJ);//会员级别
			$this->assign('list',$list);


			$this->display();
		}else{
			$this->error('二级密码错误!');
		}
	}
	//处理申请提交
	public function user_levelAC(){
		if ($_SESSION['Urlszpass'] == 'Mysskai' ){
			$fee_rs = M ('fee') -> find();
			$fck = M('Fck');
			$ulevel= M('ulevel');
			$id=$_SESSION[C('USER_AUTH_KEY')];
			$ulev=$_REQUEST['uLevel'];  //会员申请的级别
			//$zf_type = $_REQUEST['zftype'];
			$fee_rs = M('fee')->find();
			$s10 = explode('|',$fee_rs['s10']);
			$s2 = explode('|',$fee_rs['s2']);
			switch($ulev){
				case $s10[0];
					$level = 1;
					$ds = $s2[0];
					break;
				case $s10[1];
					$level = 2;
					$ds = $s2[1];
					break;
				case $s10[2];
					$level = 3;
					$ds = $s2[2];
					break;
				case $s10[3];
					$level = 4;
					$ds = $s2[3];
					break;
				case $s10[4];
					$level = 5;
					$ds = $s2[4];
					break;
				case $s10[5];
					$level = 6;
					$ds = $s2[5];
					break;
				case $s10[6];
					$level = 7;
					$ds = $s2[6];
					break;
			}
			if(empty($ulev)){
				$this->error('请选择级别！');
				exit;
			}
			$where = array();
			$where['id'] = $id;
			$field ='*';
			$fck_rs = $fck ->where($where)->field($field)->find();
			if($fck_rs){
				if($fck_rs['is_pay'] == 0){
					$this->error('临时会员不能升级!');
					exit;
				}

				$u_nn = $ulevel->where('uid='.$id.' and is_pay=0')->count();
				if($u_nn>0){
					$this->error('您之前还有升级申请尚未确认，请不要重复申请！');
					exit;
				}


				$mlevel=$fck_rs['u_level'];
				$re_id=$fck_rs['re_id'];
				if($level<=$mlevel){
					$this->error('升级级别必须大于当前级别!');
					exit;
				}else{
					$s9= explode('|',$fee_rs['s9']);
					$money = $s9[$level-1] - $s9[$mlevel-1];
					$ds = $ds - $fck_rs['f4'];
					$nowdate = strtotime(date('c'));
					$data               = array();
					$data['is_pay']     = 0;
					$data['uid']        = $id;
					$data['u_level']   = $mlevel;
					$data['up_level']  = $level;
					$data['money']      = $money;
					$data['create_time']  = $nowdate;
					$data['danshu']     = $ds;
					$data['user_id']    = $fck_rs['user_id'];

					$result = $ulevel->add($data);
//					unset($ulevel,$data);
					if($result){
						$bUrl = __URL__.'/user_level';
						$this->_box(1,'升级申请成功！',$bUrl,1);
					}else{
						dump($ulevel);
						$bUrl = __URL__.'/user_level';
						$this->_box(0,'升级申请失败，请重新再试！',$bUrl,1);
					}
				}
			}else{
				$this->error ('操作失败!');
				exit;
			}
		}else{
			$this->error('错误');
		}

	}

public function gTotle(){
		$pora = M('product');

		$gid = $_GET['GID'];
		$bnum = (int)$_GET['bnum'];
		$p_rs = $pora->where('id ='.$gid)->find();

		if($bnum<1){
			$num = 1;
		}else{
			$num = $bnum;
		}
		$shopping_id ='';
		if(empty($_SESSION["shopping"])){
			$_SESSION["shopping"] = $gid.",".$num;
		}else{
			$arr = $_SESSION["shopping"];
			$rs = explode('|',$arr);
			$tong = 0;
			foreach ($rs as $key=>$vo){
				$str = explode(',',$vo);
				if($str[0] == $gid){
					$str[1] = $str[1]+$num;
					if(empty($shopping_id)){
						$shopping_id = $str[0].",".$str[1];
					}else{
						$shopping_id .= '|'.$str[0].",".$str[1];
					}
					$tong = 1;
				}else{
					if(empty($shopping_id)){
						$shopping_id = $vo;
					}else{
						$shopping_id .= '|'.$vo;
					}
				}
			}
			if($tong==0){
				if(empty($shopping_id)){
					$shopping_id = $gid.",".$num;
				}else{
					$shopping_id .= '|'.$gid.",".$num;
				}
			}
			$_SESSION["shopping"] = $shopping_id;
		}
	}


}
?>