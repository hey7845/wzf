<?php
class BonusAction extends CommonAction{
	
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
			$_SESSION['Urlszpass'] = 'MyssfinanceTable';
			$bUrl = __URL__.'/financeTable';//
			$this->_boxx($bUrl);
			break;

			case 3;
			$_SESSION['UrlPTPass'] = 'MyssMiHouTao';
			$bUrl = __URL__.'/adminFinance';//拨出比例
			$this->_boxx($bUrl);
			break;
			
			case 4;
			$_SESSION['UrlPTPass'] = 'MyssPiPa';
			$bUrl = __URL__.'/adminFinanceTable';//奖金查询
			$this->_boxx($bUrl);
			break;
			
			case 5;
			$_SESSION['UrlPTPass'] = 'Mysswallet';
			$bUrl = __URL__.'/wallet';//奖金查询
			$this->_boxx($bUrl);
			break;
			
		


			default;
			$this->error('二级密码错误!');
			exit;
		}
	}
	
	//会员资金查询(显示会员每一期的各奖奖金)
	public function financeTable($cs=0){
		$fck = M('fck');
		$bonus = M ('bonus');  //奖金表
		$where = array();
		$ID = $_SESSION[C('USER_AUTH_KEY')];
		
		$user_id = trim($_REQUEST['UserID']);
		if(!empty($user_id) && $ID==1){
			$fck_rs = $fck->where("user_id='$user_id'")->field('id')->find();
			if(!$fck_rs){
				$this->error("该会员不存在");
				exit;
			}else{
				$this->assign('user_id',$user_id);
				$where['uid'] = $fck_rs['id'];
			}
		}else{
			$where['uid'] = $ID; //登录AutoId
		}
	
		
		if(!empty($_REQUEST['FanNowDate'])){  //日期查询
			$time1 = strtotime($_REQUEST['FanNowDate']);                // 这天 00:00:00
			$time2 = strtotime($_REQUEST['FanNowDate']) + 3600*24 -1;   // 这天 23:59:59
			$where['e_date'] = array(array('egt',$time1),array('elt',$time2));
			//$where['e_date'] = array('eq',$time1);
		}

        $field  = '*';
        //=====================分页开始==============================================
        import ( "@.ORG.ZQPage" );  //导入分页类
        $count = $bonus->where($where)->count();//总页数
        $listrows = 5;//每页显示的记录数
        $page_where = 'FanNowDate=' . $_REQUEST['FanNowDate'].'&UserID='.$user_id;//分页条件
        $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
        //===============(总页数,每页显示记录数,css样式 0-9)
        $show = $Page->show();//分页变量
        $this->assign('page', $show);//分页变量输出到模板
        $list = $bonus->where($where)->field($field)->order('id desc')->page($Page->getPage().','.$listrows)->select();
        $this->assign('list',$list);//数据输出到模板
        //=================================================

        //各项奖每页汇总
		$count = array();
		foreach($list as $vo){
			for($b=0;$b<=100;$b++){
				$count[$b] += $vo['b'.$b];
				$count[$b] = $this->_2Mal($count[$b],2);
			}
		}

		//奖项名称与显示
		$b_b = array();
		$c_b = array();
		$b_b[1]  = C('Bonus_B1');
		$c_b[1]  = C('Bonus_B1c');
		$b_b[2]  = C('Bonus_B2');
		$c_b[2]  = C('Bonus_B2c');
		$b_b[3]  = C('Bonus_B3');
		$c_b[3]  = C('Bonus_B3c');
		$b_b[4]  = C('Bonus_B4');
		$c_b[4]  = C('Bonus_B4c');
		$b_b[5]  = C('Bonus_B5');
		$c_b[5]  = C('Bonus_B5c');
		$b_b[6]  = C('Bonus_B6');
		$c_b[6]  = C('Bonus_B6c');
		$b_b[7]  = C('Bonus_B7');
		$c_b[7]  = C('Bonus_B7c');
		$b_b[8]  = C('Bonus_B8');
		$c_b[8]  = C('Bonus_B8c');
		$b_b[9]  = C('Bonus_B9');
		$c_b[9]  = C('Bonus_B9c');
		$b_b[10] = C('Bonus_B10');
		$c_b[10] = C('Bonus_B10c');
		$b_b[11] = C('Bonus_HJ');   //合计
		$c_b[11] = C('Bonus_HJc');
		$b_b[13] = C('Bonus_Bb0');   //合计
		$c_b[13] = C('Bonus_Bb0c');
		$b_b[0]  = C('Bonus_B0');   //实发
		$c_b[0]  = C('Bonus_B0c');
		$b_b[12] = C('Bonus_XX');   //详细
		$c_b[12] = C('Bonus_XXc');

		$fee   = M ('fee');    //参数表
		$fee_rs = $fee->field('s18')->find();
		$fee_s7 = explode('|',$fee_rs['s18']);
		
		$this->assign('fee_s7',$fee_s7);        //输出奖项名称数组

		$this -> assign('b_b',$b_b);
		$this -> assign('c_b',$c_b);
		$this->assign('count',$count);
		$this->display('financeTable');
	}
	
	
	public function financeShow(){
		//奖金明细
		$history = M('history');
		$fck = M ('fck');
		$fee = M ('fee');
		$fee_rs = $fee->field('s13')->find();
		$date = $fee_rs['s13'];
		$UID = $_SESSION[C('USER_AUTH_KEY')];
		
		$RDT = (int) $_REQUEST['RDT'];
		$PDT = (int)$_REQUEST['PDT'];
		$cPDT = $PDT + 24 * 3600 - 1;
		
		$lastdate = mktime(0, 0, 0, date("m"), date("d")-$date,   date("Y"));
		$map['pdt'] = array(array('egt',$PDT),array('elt',$cPDT));
		
		$map['uid'] = $UID;
		$map['allp'] = 0;
		
		$user_id = trim($_REQUEST['UserID']);
		if(!empty($user_id) && $UID==1){
			$fck_rs = $fck->where("user_id='$user_id'")->field('id')->find();
			if(!$fck_rs){
				$this->error("该会员不存在");
				exit;
			}else{
				$UID = $fck_rs['id'];
			}
		}
		$map = "pdt >={$RDT} and pdt <={$PDT} and uid={$UID} ";
		// print_r($map);die;
		$field  = '*';
		//=====================分页开始==============================================
		import ( "@.ORG.ZQPage" );  //导入分页类
		$count = $history->where($map)->count();//总页数
		$listrows = C('PAGE_LISTROWS')  ;//每页显示的记录数
		$page_where = 'PDT/' . $PDT;//分页条件
		$Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
		//===============(总页数,每页显示记录数,css样式 0-9)
		$show = $Page->show();//分页变量
		$this->assign('page',$show);//分页变量输出到模板
		$list = $history->where($map)->field($field)->order('id desc')->page($Page->getPage().','.$listrows)->select();
		// print_r($list);die;
		$this->assign('list',$list);//数据输出到模板
		//=================================================

		$fee   = M ('fee');    //参数表
		$fee_rs = $fee->field('s18')->find();
		$fee_s7 = explode('|',$fee_rs['s18']);
		$this->assign('fee_s7',$fee_s7);        //输出奖项名称数组

		$this->display ('financeShow');
	}
	


//会员资金查询(显示会员每一期的各奖奖金)
	public function financeTable1($cs=0){
		$fck = M('fck');
		$bonus = M ('bonus');  //奖金表
		$where = array();
		$ID = $_SESSION[C('USER_AUTH_KEY')];
		
		$user_id = trim($_REQUEST['UserID']);
		if(!empty($user_id) && $ID==1){
			$fck_rs = $fck->where("user_id='$user_id'")->field('id')->find();
			if(!$fck_rs){
				$this->error("该会员不存在");
				exit;
			}else{
				$this->assign('user_id',$user_id);
				$where['uid'] = $fck_rs['id'];
			}
		}else{
			$where['uid'] = $ID; //登录AutoId
		}
	
		
		if(!empty($_REQUEST['FanNowDate'])){  //日期查询
			$time1 = strtotime($_REQUEST['FanNowDate']);                // 这天 00:00:00
			$time2 = strtotime($_REQUEST['FanNowDate']) + 3600*24 -1;   // 这天 23:59:59
			$where['e_date'] = array(array('egt',$time1),array('elt',$time2));
			//$where['e_date'] = array('eq',$time1);
		}

        $field  = '*';
        //=====================分页开始==============================================
        import ( "@.ORG.ZQPage" );  //导入分页类
        $count = $bonus->where($where)->count();//总页数
        $listrows = 5;//每页显示的记录数
        $page_where = 'FanNowDate=' . $_REQUEST['FanNowDate'].'&UserID='.$user_id;//分页条件
        $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
        //===============(总页数,每页显示记录数,css样式 0-9)
        $show = $Page->show();//分页变量
        $this->assign('page', $show);//分页变量输出到模板
        $list = $bonus->where($where)->field($field)->order('id desc')->page($Page->getPage().','.$listrows)->select();
        $this->assign('list',$list);//数据输出到模板
        //=================================================

        //各项奖每页汇总
		$count = array();
		foreach($list as $vo){
			for($b=0;$b<=100;$b++){
				$count[$b] += $vo['b'.$b];
				$count[$b] = $this->_2Mal($count[$b],2);
			}
		}

		//奖项名称与显示
		$b_b = array();
		$c_b = array();
		$b_b[1]  = C('Bonus_B1');
		$c_b[1]  = C('Bonus_B1c');
		$b_b[2]  = C('Bonus_B2');
		$c_b[2]  = C('Bonus_B2c');
		$b_b[3]  = C('Bonus_B3');
		$c_b[3]  = C('Bonus_B3c');
		$b_b[4]  = C('Bonus_B4');
		$c_b[4]  = C('Bonus_B4c');
		$b_b[5]  = C('Bonus_B5');
		$c_b[5]  = C('Bonus_B5c');
		$b_b[6]  = C('Bonus_B6');
		$c_b[6]  = C('Bonus_B6c');
		$b_b[7]  = C('Bonus_B7');
		$c_b[7]  = C('Bonus_B7c');
		$b_b[8]  = C('Bonus_B8');
		$c_b[8]  = C('Bonus_B8c');
		$b_b[9]  = C('Bonus_B9');
		$c_b[9]  = C('Bonus_B9c');
		$b_b[10] = C('Bonus_B10');
		$c_b[10] = C('Bonus_B10c');
		$b_b[11] = C('Bonus_HJ');   //合计
		$c_b[11] = C('Bonus_HJc');
		$b_b[13] = C('Bonus_Bb0');   //合计
		$c_b[13] = C('Bonus_Bb0c');
		$b_b[0]  = C('Bonus_B0');   //实发
		$c_b[0]  = C('Bonus_B0c');
		$b_b[12] = C('Bonus_XX');   //详细
		$c_b[12] = C('Bonus_XXc');

		$fee   = M ('fee');    //参数表
		$fee_rs = $fee->field('s18')->find();
		$fee_s7 = explode('|',$fee_rs['s18']);
		
		$this->assign('fee_s7',$fee_s7);        //输出奖项名称数组

		$this -> assign('b_b',$b_b);
		$this -> assign('c_b',$c_b);
		$this->assign('count',$count);
		$this->display('financeTable1');
	}
	
	
	public function financeShow1(){
		//奖金明细
		$history = M('history');
		$fck = M ('fck');
		$fee = M ('fee');
		$fee_rs = $fee->field('s13')->find();
		$date = $fee_rs['s13'];
		$UID = $_SESSION[C('USER_AUTH_KEY')];
		
		$RDT = (int) $_REQUEST['RDT'];
		$PDT = (int)$_REQUEST['PDT'];
		$cPDT = $PDT + 24 * 3600 - 1;
		$lastdate = mktime(0, 0, 0, date("m"), date("d")-$date,   date("Y"));
		//$map['pdt'] = array(array('egt',$PDT),array('elt',$cPDT));
		//$map['uid'] = $UID;
		//$map['allp'] = 0;
		
		$user_id = trim($_REQUEST['UserID']);
		if(!empty($user_id) && $UID==1){
			$fck_rs = $fck->where("user_id='$user_id'")->field('id')->find();
			if(!$fck_rs){
				$this->error("该会员不存在");
				exit;
			}else{
				$UID = $fck_rs['id'];
			}
		}
		$map = "pdt >={$RDT} and pdt <={$PDT} and uid={$UID} and action_type+0>0 and action_type+0<7 and action_type+0>1";

		$field  = '*';
		//=====================分页开始==============================================
		import ( "@.ORG.ZQPage" );  //导入分页类
		$count = $history->where($map)->count();//总页数
		$listrows = C('PAGE_LISTROWS')  ;//每页显示的记录数
		$page_where = 'PDT/' . $PDT;//分页条件
		$Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
		//===============(总页数,每页显示记录数,css样式 0-9)
		$show = $Page->show();//分页变量
		$this->assign('page',$show);//分页变量输出到模板
		$list = $history->where($map)->field($field)->order('id desc')->page($Page->getPage().','.$listrows)->select();
	
		$this->assign('list',$list);//数据输出到模板
		//=================================================

		$fee   = M ('fee');    //参数表
		$fee_rs = $fee->field('s18')->find();
		$fee_s7 = explode('|',$fee_rs['s18']);
		$this->assign('fee_s7',$fee_s7);        //输出奖项名称数组

		$this->display ('financeShow1');
	}
	




	
	//出纳管理
	public function adminFinance(){
		$this->_Admin_checkUser();
		if ($_SESSION['UrlPTPass'] == 'MyssMiHouTao'){
			$times = M ('times');
			$field = '*';
			$where = 'is_count=0';
			$Numso = array();
			$Numss = array();

			$rs = $times->where($where)->field($field)->order(' id desc')->find();
			$Numso['0'] = 0;
			$Numso['1'] = 0;
			$Numso['2'] = 0;
			if ($rs){
				$eDate = strtotime(date('c'));  //time()
				$sDate = $rs['benqi'] ;//时间

				$this->MiHouTaoBenQi($eDate, $sDate, $Numso, 0);
				$this->assign('list3', $Numso);   //本期收入
				$this->assign('list4', $sDate);   //本期时间截
			}else{
				$this->assign('list3', $Numso);
			}

			$fee = M('fee');
			$fee_rs = $fee->field('s18')->find();
			$fee_s7 = explode('|',$fee_rs['s18']);
			$this->assign('fee_s7',$fee_s7);        //输出奖项名称数组

            //=====================分页开始==============================================
            import ( "@.ORG.ZQPage" );  //导入分页类
            $count = $times->where($where)->count();//总页数
            $listrows = C('PAGE_LISTROWS')  ;//每页显示的记录数
            $Page = new ZQPage($count, $listrows, 1);
            //===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show();//分页变量
            $this->assign('page',$show);//分页变量输出到模板
            $rs = $times->where($where)->field($field)->order('id desc')->page($Page->getPage().','.$listrows)->select();
            $this->assign('list',$rs);//数据输出到模板

			if ($rs){
				$occ = 1;
				$Numso['1'] = $Numso['1']+$Numso['0'];
				$Numso['3'] = $Numso['3']+$Numso['0'];
				foreach ($rs as $Roo){
					$eDate          = $Roo['benqi'];//本期时间
                    $sDate          = $Roo['shangqi'];//上期时间
					$Numsd          = array();
					$Numsd[$occ][0] = $eDate;
					$Numsd[$occ][1] = $sDate;

					$this->MiHouTaoBenQi($eDate,$sDate,$Numss,1);
					//$Numoo = $Numss['0'];   //当期收入
					$Numss[$occ]['0'] = $Numss['0'];
					$Dopp  = M ('bonus');
					$field = '*';
					$where = " s_date>= '".$sDate."' And e_date<= '".$eDate."' ";
					$rsc   = $Dopp->where($where)->field($field)->select();
					$Numss[$occ]['1'] = 0;

					foreach ($rsc as $Roc){
						$Numss[$occ]['1'] += $Roc['b0'] ;  //当期支出
						$Numb2[$occ]['1'] += $Roc['b1'];
						$Numb3[$occ]['1'] += $Roc['b2'];
						$Numb4[$occ]['1'] += $Roc['b3'];
						//$Numoo          += $Roc['b9'];//当期收入
					}
					$Numoo              = $Numss['0'];//当期收入
					$Numss[$occ]['2']   = $Numoo - $Numss[$occ]['1'];   //本期赢利
					$Numss[$occ]['3']   = substr( floor(($Numss[$occ]['1'] / $Numoo) * 100) , 0 ,3);  //本期拔比
					$Numso['1']        += $Numoo;  //收入合计
					$Numso['2']        += $Numss[$occ]['1'];           //支出合计
					$Numso['3']        += $Numss[$occ]['2'];           //赢利合计
					$Numso['4']         = substr( floor(($Numso['2'] / $Numso['1']) * 100) , 0 ,3);  //总拔比
					$Numss[$occ]['4']   = substr( ($Numb2[$occ]['1'] / $Numoo) * 100 , 0 ,4);  //小区奖金拔比
					$Numss[$occ]['5']   = substr( ($Numb3[$occ]['1'] / $Numoo) * 100 , 0 ,4);  //互助基金拔比
					$Numss[$occ]['6']   = substr( ($Numb4[$occ]['1'] / $Numoo) * 100 , 0 ,4); //管理基金拔比
					$Numss[$occ]['7']	= $Numb2[$occ]['1'];//小区奖金
					$Numss[$occ]['8'] 	= $Numb3[$occ]['1'] ;  //互助基金
					$Numss[$occ]['9'] 	= $Numb4[$occ]['1'];//管理基金
					$Numso['5']        += $Numb2[$occ]['1'];  //小区奖金合计
					$Numso['6']        += $Numb3[$occ]['1'];  //互助基金合计
					$Numso['7']        += $Numb4[$occ]['1'];  //管理基金合计
					$Numso['8']         = substr( ($Numso['5'] / $Numso['1']) * 100 , 0 ,4);  //小区奖金总拔比
					$Numso['9']         = substr( ($Numso['6'] / $Numso['1']) * 100 , 0 ,4);  //互助基金总拔比
					$Numso['10']         = substr( ($Numso['7'] / $Numso['1']) * 100 , 0 ,4);  //管理基金总拔比
					$occ++;
				}
			}

			$PP = $_GET['p'];
			$this->assign('PP',$PP);
			$this->assign('list1',$Numss);
			$this->assign('list2',$Numso);
			$this->assign('list5',$Numsd);
			$this->display('adminFinance');
		}else{
			$this->error('错误!');
			exit;
		}
	}
	
	//当期收入会员列表
    public function adminFinanceList(){
    	$this->_Admin_checkUser();
        if ($_SESSION['UrlPTPass'] == 'MyssMiHouTao'){
            $shouru = M('shouru');
            $eDate  = $_REQUEST['eDate'];
            $sDate  = $_REQUEST['sDate'];
            $UserID = $_REQUEST['UserID'];
            if (!empty($UserID)){
            	import ( "@.ORG.KuoZhan" );  //导入扩展类
                $KuoZhan = new KuoZhan();
                if ($KuoZhan->is_utf8($UserID) == false){
                    $UserID = iconv('GB2312','UTF-8',$UserID);
                }
				unset($KuoZhan);
				$map['user_id'] = array('like',"%".$UserID."%");
				$UserID = urlencode($UserID);
			}
            $map['in_time'] = array(array('gt',$sDate),array('elt',$eDate));
            //查询字段
            $field  = '*';
            //=====================分页开始==============================================
            import ( "@.ORG.ZQPage" );  //导入分页类
            $count = $shouru->where($map)->count();//总页数
            $listrows = C('PAGE_LISTROWS')  ;//每页显示的记录数
            $page_where = 'UserID=' . $UserID . '&eDate='. $eDate .'&sDate='. $sDate ;//分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            //===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show();//分页变量
            $this->assign('page',$show);//分页变量输出到模板
            $list = $shouru->where($map)->field($field)->order('in_time desc')->page($Page->getPage().','.$listrows)->select();

            $this->assign('list',$list);//数据输出到模板
            //=================================================

			$this->assign('sDate',$sDate);
			$this->assign('eDate',$eDate);
            $this->display ('adminFinanceList');
        }else{
            $this->error('数据错误!');
            exit;
        }
    }
    
	//奖金查询
	public function adminFinanceTable(){
		$this->_Admin_checkUser();
// 		if ($_SESSION['UrlPTPass'] == 'MyssPiPa'){
			$bonus = M ('bonus');  //奖金表
			$fee   = M ('fee');    //参数表
			$times = M ('times');  //结算时间表

			$fee_rs = $fee->field('s18')->find();
			$fee_s7 = explode('|',$fee_rs['s18']);
			$this->assign('fee_s7',$fee_s7);        //输出奖项名称数组

			$where = array();
			$sql = '';
			if(isset($_REQUEST['FanNowDate'])){  //日期查询
				if(!empty($_REQUEST['FanNowDate'])){
					$time1 = strtotime($_REQUEST['FanNowDate']);                // 这天 00:00:00
					$time2 = strtotime($_REQUEST['FanNowDate']) + 3600*24 -1;   // 这天 23:59:59
					$sql = "where e_date >= $time1 and e_date <= $time2";
				}
			}


			$field  = '*';
			//=====================分页开始==============================================
			import ( "@.ORG.ZQPage" );  //导入分页类
			$count = count($bonus -> query("select id from __TABLE__ ". $sql ." group by did")); //总记录数
       		$listrows = C('ONE_PAGE_RE');//每页显示的记录数
			$page_where = 'FanNowDate=' . $_REQUEST['FanNowDate'];//分页条件
			if(!empty($page_where)){
				$Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
			}else{
				$Page = new ZQPage($count, $listrows, 1, 0, 3);
			}
			//===============(总页数,每页显示记录数,css样式 0-9)
			$show = $Page->show();//分页变量
			$this->assign('page', $show);//分页变量输出到模板
			$status_rs = ($Page->getPage()-1)*$listrows;
			$list = $bonus -> query("select e_date,did,sum(b0) as b0,sum(b1) as b1,sum(b2) as b2,sum(b3) as b3,sum(b4) as b4,sum(b5) as b5,sum(b6) as b6,sum(b7) as b7,sum(b8) as b8,sum(b9) as b9,sum(b10) as b10 from __TABLE__ ". $sql ." group by did  order by did desc limit ". $status_rs .",". $listrows);
			$this->assign('list',$list);//数据输出到模板
			//=================================================

			//各项奖每页汇总
			$count = array();
			foreach($list as $vo){
				for($b=0;$b<=50;$b++){
					$count[$b] += $vo['b'.$b];
					$count[$b] = $this->_2Mal($count[$b],2);
				}
			}
	
			//奖项名称与显示
			$b_b = array();
			$c_b = array();
			$b_b[1]  = C('Bonus_B1');
			$c_b[1]  = C('Bonus_B1c');
			$b_b[2]  = C('Bonus_B2');
			$c_b[2]  = C('Bonus_B2c');
			$b_b[3]  = C('Bonus_B3');
			$c_b[3]  = C('Bonus_B3c');
			$b_b[4]  = C('Bonus_B4');
			$c_b[4]  = C('Bonus_B4c');
			$b_b[5]  = C('Bonus_B5');
			$c_b[5]  = C('Bonus_B5c');
			$b_b[6]  = C('Bonus_B6');
			$c_b[6]  = C('Bonus_B6c');
			$b_b[7]  = C('Bonus_B7');
			$c_b[7]  = C('Bonus_B7c');
			$b_b[8]  = C('Bonus_B8');
			$c_b[8]  = C('Bonus_B8c');
			$b_b[9]  = C('Bonus_B9');
			$c_b[9]  = C('Bonus_B9c');
			$b_b[10] = C('Bonus_B10');
			$c_b[10] = C('Bonus_B10c');
			$b_b[11] = C('Bonus_HJ');   //合计
			$c_b[11] = C('Bonus_HJc');
			$b_b[13] = C('Bonus_Bb0');   //合计
			$c_b[13] = C('Bonus_Bb0c');
			$b_b[0]  = C('Bonus_B0');   //实发
			$c_b[0]  = C('Bonus_B0c');
			$b_b[12] = C('Bonus_XX');   //详细
			$c_b[12] = C('Bonus_XXc');
			$this -> assign('b_b',$b_b);
			$this -> assign('c_b',$c_b);
			$this->assign('count',$count);

			//输出扣费奖索引
			$this->assign('ind',7);  //数组索引 +1

			$this->display('adminFinanceTable');
// 		}else{
// 			$this->error('错误');
// 			exit;
// 		}
	}
	
	//奖金明细
	public function adminFinanceTableList(){
		if ($_SESSION['UrlPTPass'] == 'MyssPiPa'|| $_SESSION['UrlPTPass'] == 'MyssMiHouTao'){  //MyssShiLiu
			$times   = M('times');
			$history = M('history');

			$UID = (int) $_GET['uid'];
			$did = (int) $_REQUEST['did'];

			$where = array();
			if (!empty($did)){
				$rs = $times -> find($did);
				if($rs){
					$rs_day = $rs['benqi'];
					$where['pdt'] = array(array('gt',$rs['shangqi']),array('elt',$rs_day));  //大于上期,小于等于本期
				}else{
					$this->error('错误!');
					exit;
				}
			}
			$where['uid'] = $UID;
			$where['type'] = 1;
			$where['action_type']=7;
			$where['action_type']=1;
			// action_type+0=7 or action_type+0=1
			$map = "uid={$UID} and type=1 and action_type>0 and  action_type<20";
            $field  = '*';
            //=====================分页开始==============================================
            import ( "@.ORG.ZQPage" );  //导入分页类
            $count = $history->where($map)->count();//总页数
//            dump($history);exit;
       		$listrows = C('ONE_PAGE_RE');//每页显示的记录数
            $page_where = 'did=' . (int) $_REQUEST['did'];//分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            //===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show();//分页变量
            $this->assign('page',$show);//分页变量输出到模板
            $list = $history->where($map)->field($field)->order('id desc')->page($Page->getPage().','.$listrows)->select();
            $this->assign('list',$list);//数据输出到模板
            //=================================================

            $fee   = M ('fee');    //参数表
			$fee_rs = $fee->field('s18')->find();
			$fee_s7 = explode('|',$fee_rs['s18']);
			$this->assign('fee_s7',$fee_s7);        //输出奖项名称数组

			$this->display ('adminFinanceTableList');
			}else{
			$this->error ('错误!');
			exit;
		}
	}

	//查询这一期得奖会员资金
	public function adminFinanceTableShow(){
		$this->_Admin_checkUser();
		if ($_SESSION['UrlPTPass'] == 'MyssPiPa' || $_SESSION['UrlPTPass'] == 'MyssMiHouTao'){
			$bonus = M ('bonus');  //奖金表
			$fee   = M ('fee');    //参数表
			$times = M ('times');  //结算时间表

			$fee_rs = $fee->field('s18')->find();
			$fee_s7 = explode('|',$fee_rs['s18']);
			$this->assign('fee_s7',$fee_s7);        //输出奖项名称数组
			$UserID = $_REQUEST['UserID'];
			$where = array();
			$sql = '';
			$did = (int) $_REQUEST['did'];
			$field  = '*';

			if($UserID !=""){
				$sql =" and user_id like '%".$UserID."%'";
			}
			//=====================分页开始==============================================92607291105
			import ( "@.ORG.ZQPage" );  //导入分页类
			$count = count($bonus -> query("select id from __TABLE__ where did= ". $did .$sql)); //总记录数
       		$listrows = C('ONE_PAGE_RE');//每页显示的记录数
			$page_where = 'did/' . $_REQUEST['did'];//分页条件
			if(!empty($page_where)){
				$Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
			}else{
				$Page = new ZQPage($count, $listrows, 1, 0, 3);
			}
			//===============(总页数,每页显示记录数,css样式 0-9)
			$show = $Page->show();//分页变量
			$this->assign('page', $show);//分页变量输出到模板
			$status_rs = ($Page->getPage()-1)*$listrows;
			$list = $bonus -> query("select * from __TABLE__ where did =". $did . $sql ."  order by did desc limit ". $status_rs .",". $listrows);
			$this->assign('list',$list);//数据输出到模板
			//=================================================
			$this->assign('did',$did);
			//查看的这期的结算时间
			$this -> assign('confirm',$list[0]['e_date']);

			$count = array();
			foreach($list as $vo){
				for($b=0;$b<=10;$b++){
					$count[$b] += $vo['b'.$b];
					$count[$b] = $this->_2Mal($count[$b],2);
				}
			}

			//奖项名称与显示
			$b_b = array();
			$c_b = array();
			$b_b[1]  = C('Bonus_B1');
			$c_b[1]  = C('Bonus_B1c');
			$b_b[2]  = C('Bonus_B2');
			$c_b[2]  = C('Bonus_B2c');
			$b_b[3]  = C('Bonus_B3');
			$c_b[3]  = C('Bonus_B3c');
			$b_b[4]  = C('Bonus_B4');
			$c_b[4]  = C('Bonus_B4c');
			$b_b[5]  = C('Bonus_B5');
			$c_b[5]  = C('Bonus_B5c');
			$b_b[6]  = C('Bonus_B6');
			$c_b[6]  = C('Bonus_B6c');
			$b_b[7]  = C('Bonus_B7');
			$c_b[7]  = C('Bonus_B7c');
			$b_b[8]  = C('Bonus_B8');
			$c_b[8]  = C('Bonus_B8c');
			$b_b[9]  = C('Bonus_B9');
			$c_b[9]  = C('Bonus_B9c');
			$b_b[10] = C('Bonus_B10');
			$c_b[10] = C('Bonus_B10c');
			$b_b[11] = C('Bonus_HJ');   //合计
			$c_b[11] = C('Bonus_HJc');
			$b_b[13] = C('Bonus_Bb0');   //合计
			$c_b[13] = C('Bonus_Bb0c');
			$b_b[0]  = C('Bonus_B0');   //实发
			$c_b[0]  = C('Bonus_B0c');
			$b_b[12] = C('Bonus_XX');   //详细
			$c_b[12] = C('Bonus_XXc');
	
			$this -> assign('b_b',$b_b);
			$this -> assign('c_b',$c_b);
			$this->assign('count',$count);

			$this->assign('int',5);

			$this->display('adminFinanceTableShow');
		}else{
			$this->error('错误');
			exit;
		}
	}
	

	//奖金查询
	public function adminFinanceTable1(){
		$this->_Admin_checkUser();
		if ($_SESSION['UrlPTPass'] == 'MyssPiPa'){
			$bonus = M ('bonus');  //奖金表
			$fee   = M ('fee');    //参数表
			$times = M ('times');  //结算时间表

			$fee_rs = $fee->field('s18')->find();
			$fee_s7 = explode('|',$fee_rs['s18']);
			$this->assign('fee_s7',$fee_s7);        //输出奖项名称数组

			$where = array();
			$sql = '';
			if(isset($_REQUEST['FanNowDate'])){  //日期查询
				if(!empty($_REQUEST['FanNowDate'])){
					$time1 = strtotime($_REQUEST['FanNowDate']);                // 这天 00:00:00
					$time2 = strtotime($_REQUEST['FanNowDate']) + 3600*24 -1;   // 这天 23:59:59
					$sql = "where e_date >= $time1 and e_date <= $time2";
				}
			}


			$field  = '*';
			//=====================分页开始==============================================
			import ( "@.ORG.ZQPage" );  //导入分页类
			$count = count($bonus -> query("select id from __TABLE__ ". $sql ." group by did")); //总记录数
       		$listrows = C('ONE_PAGE_RE');//每页显示的记录数
			$page_where = 'FanNowDate=' . $_REQUEST['FanNowDate'];//分页条件
			if(!empty($page_where)){
				$Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
			}else{
				$Page = new ZQPage($count, $listrows, 1, 0, 3);
			}
			//===============(总页数,每页显示记录数,css样式 0-9)
			$show = $Page->show();//分页变量
			$this->assign('page', $show);//分页变量输出到模板
			$status_rs = ($Page->getPage()-1)*$listrows;
			$list = $bonus -> query("select e_date,did,sum(b0) as b0,sum(b1) as b1,sum(b2) as b2,sum(b3) as b3,sum(b4) as b4,sum(b5) as b5,sum(b6) as b6,sum(b7) as b7,sum(b8) as b8,sum(b9) as b9 from __TABLE__ ". $sql ." group by did  order by did desc limit ". $status_rs .",". $listrows);
			$this->assign('list',$list);//数据输出到模板
			//=================================================

			//各项奖每页汇总
			$count = array();
			foreach($list as $vo){
				for($b=0;$b<=9;$b++){
					$count[$b] += $vo['b'.$b];
					$count[$b] = $this->_2Mal($count[$b],2);
				}
			}
	
			//奖项名称与显示
			$b_b = array();
			$c_b = array();
			$b_b[1]  = C('Bonus_B1');
			$c_b[1]  = C('Bonus_B1c');
			$b_b[2]  = C('Bonus_B2');
			$c_b[2]  = C('Bonus_B2c');
			$b_b[3]  = C('Bonus_B3');
			$c_b[3]  = C('Bonus_B3c');
			$b_b[4]  = C('Bonus_B4');
			$c_b[4]  = C('Bonus_B4c');
			$b_b[5]  = C('Bonus_B5');
			$c_b[5]  = C('Bonus_B5c');
			$b_b[6]  = C('Bonus_B6');
			$c_b[6]  = C('Bonus_B6c');
			$b_b[7]  = C('Bonus_B7');
			$c_b[7]  = C('Bonus_B7c');
			$b_b[8]  = C('Bonus_B8');
			$c_b[8]  = C('Bonus_B8c');
			$b_b[9]  = C('Bonus_B9');
			$c_b[9]  = C('Bonus_B9c');
			$b_b[10] = C('Bonus_B10');
			$c_b[10] = C('Bonus_B10c');
			$b_b[11] = C('Bonus_HJ');   //合计
			$c_b[11] = C('Bonus_HJc');
			$b_b[13] = C('Bonus_Bb0');   //合计
			$c_b[13] = C('Bonus_Bb0c');
			$b_b[0]  = C('Bonus_B0');   //实发
			$c_b[0]  = C('Bonus_B0c');
			$b_b[12] = C('Bonus_XX');   //详细
			$c_b[12] = C('Bonus_XXc');
			$this -> assign('b_b',$b_b);
			$this -> assign('c_b',$c_b);
			$this->assign('count',$count);

			//输出扣费奖索引
			$this->assign('ind',7);  //数组索引 +1

			$this->display('adminFinanceTable1');
		}else{
			$this->error('错误');
			exit;
		}
	}
	
	//奖金明细
	public function adminFinanceTableList1(){
		if ($_SESSION['UrlPTPass'] == 'MyssPiPa'|| $_SESSION['UrlPTPass'] == 'MyssMiHouTao'){  //MyssShiLiu
			$times   = M('times');
			$history = M('history');

			$UID = (int) $_GET['uid'];
			$did = (int) $_REQUEST['did'];

			$where = array();
			if (!empty($did)){
				$rs = $times -> find($did);
				if($rs){
					$rs_day = $rs['benqi'];
					$where['pdt'] = array(array('gt',$rs['shangqi']),array('elt',$rs_day));  //大于上期,小于等于本期
				}else{
					$this->error('错误!');
					exit;
				}
			}
			$where['uid'] = $UID;
			$where['type'] = 1;
			$map = "uid={$UID} and type=1 and action_type+0<7 and action_type+0>1";
            $field  = '*';
            //=====================分页开始==============================================
            import ( "@.ORG.ZQPage" );  //导入分页类
            $count = $history->where($map)->count();//总页数
//            dump($history);exit;
       		$listrows = C('ONE_PAGE_RE');//每页显示的记录数
            $page_where = 'did=' . (int) $_REQUEST['did'];//分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            //===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show();//分页变量
            $this->assign('page',$show);//分页变量输出到模板
            $list = $history->where($map)->field($field)->order('id desc')->page($Page->getPage().','.$listrows)->select();
            $this->assign('list',$list);//数据输出到模板
            //=================================================

            $fee   = M ('fee');    //参数表
			$fee_rs = $fee->field('s18')->find();
			$fee_s7 = explode('|',$fee_rs['s18']);
			$this->assign('fee_s7',$fee_s7);        //输出奖项名称数组

			$this->display ('adminFinanceTableList1');
			}else{
			$this->error ('错误!');
			exit;
		}
	}

	//查询这一期得奖会员资金
	public function adminFinanceTableShow1(){
		$this->_Admin_checkUser();
		if ($_SESSION['UrlPTPass'] == 'MyssPiPa' || $_SESSION['UrlPTPass'] == 'MyssMiHouTao'){
			$bonus = M ('bonus');  //奖金表
			$fee   = M ('fee');    //参数表
			$times = M ('times');  //结算时间表

			$fee_rs = $fee->field('s18')->find();
			$fee_s7 = explode('|',$fee_rs['s18']);
			$this->assign('fee_s7',$fee_s7);        //输出奖项名称数组
			$UserID = $_REQUEST['UserID'];
			$where = array();
			$sql = '';
			$did = (int) $_REQUEST['did'];
			$field  = '*';

			if($UserID !=""){
				$sql =" and user_id like '%".$UserID."%'";
			}
			//=====================分页开始==============================================92607291105
			import ( "@.ORG.ZQPage" );  //导入分页类
			$count = count($bonus -> query("select id from __TABLE__ where did= ". $did .$sql)); //总记录数
       		$listrows = C('ONE_PAGE_RE');//每页显示的记录数
			$page_where = 'did/' . $_REQUEST['did'];//分页条件
			if(!empty($page_where)){
				$Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
			}else{
				$Page = new ZQPage($count, $listrows, 1, 0, 3);
			}
			//===============(总页数,每页显示记录数,css样式 0-9)
			$show = $Page->show();//分页变量
			$this->assign('page', $show);//分页变量输出到模板
			$status_rs = ($Page->getPage()-1)*$listrows;
			$list = $bonus -> query("select * from __TABLE__ where did =". $did . $sql ."  order by did desc limit ". $status_rs .",". $listrows);
			$this->assign('list',$list);//数据输出到模板
			//=================================================
			$this->assign('did',$did);
			//查看的这期的结算时间
			$this -> assign('confirm',$list[0]['e_date']);

			$count = array();
			foreach($list as $vo){
				for($b=0;$b<=10;$b++){
					$count[$b] += $vo['b'.$b];
					$count[$b] = $this->_2Mal($count[$b],2);
				}
			}

			//奖项名称与显示
			$b_b = array();
			$c_b = array();
			$b_b[1]  = C('Bonus_B1');
			$c_b[1]  = C('Bonus_B1c');
			$b_b[2]  = C('Bonus_B2');
			$c_b[2]  = C('Bonus_B2c');
			$b_b[3]  = C('Bonus_B3');
			$c_b[3]  = C('Bonus_B3c');
			$b_b[4]  = C('Bonus_B4');
			$c_b[4]  = C('Bonus_B4c');
			$b_b[5]  = C('Bonus_B5');
			$c_b[5]  = C('Bonus_B5c');
			$b_b[6]  = C('Bonus_B6');
			$c_b[6]  = C('Bonus_B6c');
			$b_b[7]  = C('Bonus_B7');
			$c_b[7]  = C('Bonus_B7c');
			$b_b[8]  = C('Bonus_B8');
			$c_b[8]  = C('Bonus_B8c');
			$b_b[9]  = C('Bonus_B9');
			$c_b[9]  = C('Bonus_B9c');
			$b_b[10] = C('Bonus_B10');
			$c_b[10] = C('Bonus_B10c');
			$b_b[11] = C('Bonus_HJ');   //合计
			$c_b[11] = C('Bonus_HJc');
			$b_b[13] = C('Bonus_Bb0');   //合计
			$c_b[13] = C('Bonus_Bb0c');
			$b_b[0]  = C('Bonus_B0');   //实发
			$c_b[0]  = C('Bonus_B0c');
			$b_b[12] = C('Bonus_XX');   //详细
			$c_b[12] = C('Bonus_XXc');
	
			$this -> assign('b_b',$b_b);
			$this -> assign('c_b',$c_b);
			$this->assign('count',$count);

			$this->assign('int',5);

			$this->display('adminFinanceTableShow1');
		}else{
			$this->error('错误');
			exit;
		}
	}
	







	private function MiHouTaoBenQi($eDate,$sDate,&$Numss,$ppo){
		if ($_SESSION['UrlPTPass'] == 'MyssMiHouTao'){
			$shouru = M('shouru');
			$fwhere = "in_time>".$sDate." and in_time<=".$eDate;
			$Numss['0'] = $shouru->where($fwhere)->sum('in_money');
			if (is_numeric($Numss['0']) == false){
				$Numss['0'] = 0;
			}
			unset($shouru,$fwhere);
		}else{
			$this->error('错误');
			exit;
		}
	}
	
	//导出excel
	public function financeDaoChu(){
        $this->_Admin_checkUser();
		$title   =   "数据库名:test,   数据表:test,   备份日期:"   .   date("Y-m-d   H:i:s");
		header("Content-Type:   application/vnd.ms-excel");
		header("Content-Disposition:   attachment;   filename=Cash-xls.xls");
		header("Pragma:   no-cache");
		header("Content-Type:text/html; charset=utf-8");
		header("Expires:   0");
		echo   '<table   border="1"   cellspacing="2"   cellpadding="2"   width="50%"   align="center">';
		//   输出标题
		echo   '<tr   bgcolor="#cccccc"><td   colspan="3"   align="center">'   .   $title   .   '</td></tr>';
		//   输出字段名
		echo   '<tr  align=center>';
		echo   "<td>银行卡号</td>";
		echo   "<td>姓名</td>";
		echo   "<td>银行名称</td>";
		echo   "<td>省份</td>";
		echo   "<td>城市</td>";
		echo   "<td>金额</td>";
		echo   "<td>所有人的排序</td>";
		echo   '</tr>';
		//   输出内容
		$did = (int) $_GET['did'];
		$bonus = M ('bonus');
		$map = 'xt_bonus.b0>0 and xt_bonus.did='.$did;
		 //查询字段
		$field   = 'xt_bonus.id,xt_bonus.uid,xt_bonus.did,s_date,e_date,xt_bonus.b0,xt_bonus.b1,xt_bonus.b2,xt_bonus.b3';
		$field  .= ',xt_bonus.b4,xt_bonus.b5,xt_bonus.b6,xt_bonus.b7,xt_bonus.b8,xt_bonus.b9,xt_bonus.b10';
		$field  .= ',xt_fck.user_id,xt_fck.user_tel,xt_fck.bank_card';
		$field  .= ',xt_fck.user_name,xt_fck.user_address,xt_fck.nickname,xt_fck.user_phone,xt_fck.bank_province,xt_fck.user_tel';
		$field  .= ',xt_fck.user_code,xt_fck.bank_city,xt_fck.bank_name,xt_fck.bank_address';
		import ( "@.ORG.ZQPage" );  //导入分页类
		$count = $bonus->where($map)->count();//总页数
		$listrows = 1000000  ;//每页显示的记录数
		$page_where = '';//分页条件
		$Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
		//===============(总页数,每页显示记录数,css样式 0-9)
		$show = $Page->show();//分页变量
		$this->assign('page',$show);//分页变量输出到模板
		$join = 'left join xt_fck ON xt_bonus.uid=xt_fck.id';//连表查询
		$list = $bonus ->where($map)->field($field)->join($join)->Distinct(true)->order('id asc')->page($Page->getPage().','.$listrows)->select();
		$i = 0;
		foreach($list as $row)   {
			$i++;
			$num = strlen($i);
			if ($num == 1){
				$num = '000'.$i;
			}elseif ($num == 2){
				$num = '00'.$i;
			}elseif ($num == 3){
				$num = '0'.$i;
			}
			echo   '<tr align=center>';
			echo   '<td>'   .   sprintf('%s',(string)chr(28).$row['bank_card'].chr(28)).      '</td>';
			echo   '<td>'   .   $row['user_name']   .   '</td>';
			echo   "<td>"   .   $row['bank_name'] .  "</td>";
			echo   '<td>'   .   $row['bank_province']   .   '</td>';
			echo   '<td>'   .   $row['bank_city']   .   '</td>';
			echo   '<td>'   .   $row['b0']   .   '</td>';
			echo   '<td>'   .   chr(28).$num    .   '</td>';
			echo   '</tr>';
        }
        echo   '</table>';
        unset($bonus,$list);
    }
    
    //导出WPS
	public function financeDaoChuTwo(){
        $this->_Admin_checkUser();
		$title   =   "数据库名:test,   数据表:test,   备份日期:"   .   date("Y-m-d   H:i:s");
		header("Content-Type:   application/vnd.ms-excel");
		header("Content-Disposition:   attachment;   filename=Cash-wps.xls");
		header("Pragma:   no-cache");
		header("Content-Type:text/html; charset=utf-8");
		header("Expires:   0");
		echo   '<table   border="1"   cellspacing="2"   cellpadding="2"   width="50%"   align="center">';
		//   输出标题
		echo   '<tr   bgcolor="#cccccc"><td   colspan="3"   align="center">'   .   $title   .   '</td></tr>';
		//   输出字段名
        echo   '<tr  align=center>';
		echo   "<td>银行卡号</td>";
		echo   "<td>姓名</td>";
		echo   "<td>银行名称</td>";
		echo   "<td>省份</td>";
		echo   "<td>城市</td>";
		echo   "<td>金额</td>";
		echo   "<td>所有人的排序</td>";
		echo   '</tr>';
		//   输出内容
		$did = (int) $_GET['did'];
		$bonus = M ('bonus');
		$map = 'xt_bonus.b0>0 and xt_bonus.did='.$did;
		 //查询字段
		$field   = 'xt_bonus.id,xt_bonus.uid,xt_bonus.did,s_date,e_date,xt_bonus.b0,xt_bonus.b1,xt_bonus.b2,xt_bonus.b3';
		$field  .= ',xt_bonus.b4,xt_bonus.b5,xt_bonus.b6,xt_bonus.b7,xt_bonus.b8,xt_bonus.b9,xt_bonus.b10';
		$field  .= ',xt_fck.user_id,xt_fck.user_tel,xt_fck.bank_card';
		$field  .= ',xt_fck.user_name,xt_fck.user_address,xt_fck.nickname,xt_fck.user_phone,xt_fck.bank_province,xt_fck.user_tel';
		$field  .= ',xt_fck.user_code,xt_fck.bank_city,xt_fck.bank_name,xt_fck.bank_address';
		import ( "@.ORG.ZQPage" );  //导入分页类
		$count = $bonus->where($map)->count();//总页数
		$listrows = 1000000  ;//每页显示的记录数
		$page_where = '';//分页条件
		$Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
		//===============(总页数,每页显示记录数,css样式 0-9)
		$show = $Page->show();//分页变量
		$this->assign('page',$show);//分页变量输出到模板
		$join = 'left join xt_fck ON xt_bonus.uid=xt_fck.id';//连表查询
		$list = $bonus ->where($map)->field($field)->join($join)->Distinct(true)->order('id asc')->page($Page->getPage().','.$listrows)->select();
		$i = 0;
		foreach($list as $row)   {
			$i++;
			$num = strlen($i);
			if ($num == 1){
				$num = '000'.$i;
			}elseif ($num == 2){
				$num = '00'.$i;
			}elseif ($num == 3){
				$num = '0'.$i;
			}
			echo   '<tr align=center>';
			echo   "<td>'"   .   sprintf('%s',(string)chr(28).$row['bank_card'].chr(28)).      '</td>';
			echo   '<td>'   .   $row['user_name']   .   '</td>';
			echo   "<td>"   .   $row['bank_name'] .  "</td>";
			echo   '<td>'   .   $row['bank_province']   .   '</td>';
			echo   '<td>'   .   $row['bank_city']   .   '</td>';
			echo   '<td>'   .   $row['b0']   .   '</td>';
			echo   "<td>'"   .   $num    .   '</td>';
			echo   '</tr>';
		}
		echo   '</table>';
		unset($bonus,$list);
    }
    
    //导出TXT
	public function financeDaoChuTXT(){
        $this->_Admin_checkUser();
        if ($_SESSION['UrlPTPass'] =='MyssPiPa' || $_SESSION['UrlPTPass'] == 'MyssMiHouTao'){
            //   输出内容
            $did = (int) $_GET['did'];
            $bonus = M ('bonus');
            $map = 'xt_bonus.b0>0 and xt_bonus.did='.$did;
             //查询字段
            $field   = 'xt_bonus.id,xt_bonus.uid,xt_bonus.did,s_date,e_date,xt_bonus.b0,xt_bonus.b1,xt_bonus.b2,xt_bonus.b3';
            $field  .= ',xt_bonus.b4,xt_bonus.b5,xt_bonus.b6,xt_bonus.b7,xt_bonus.b8,xt_bonus.b9,xt_bonus.b10';
            $field  .= ',xt_fck.user_id,xt_fck.user_tel,xt_fck.bank_card';
            $field  .= ',xt_fck.user_name,xt_fck.user_address,xt_fck.nickname,xt_fck.user_phone,xt_fck.bank_province,xt_fck.user_tel';
            $field  .= ',xt_fck.user_code,xt_fck.bank_city,xt_fck.bank_name,xt_fck.bank_address';
            import ( "@.ORG.ZQPage" );  //导入分页类
            $count = $bonus->where($map)->count();//总页数
            $listrows = 1000000  ;//每页显示的记录数
            $page_where = '';//分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            //===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show();//分页变量
            $this->assign('page',$show);//分页变量输出到模板
            $join = 'left join xt_fck ON xt_bonus.uid=xt_fck.id';//连表查询
            $list = $bonus ->where($map)->field($field)->join($join)->Distinct(true)->order('id asc')->page($Page->getPage().','.$listrows)->select();
            $i = 0;
			$ko = "";
			$m_ko = 0;
            foreach($list as $row)   {
                $i++;
                $num = strlen($i);
                if ($num == 1){
                	$num = '000'.$i;
                }elseif ($num == 2){
                	$num = '00'.$i;
                }elseif ($num == 3){
                    $num = '0'.$i;
                }
				$ko .= $row['bank_card']."|".$row['user_name']."|".$row['bank_name']."|".$row['bank_province']."|".$row['bank_city']."|".$row['b0']."|".$num."\r\n";
				$m_ko += $row['b0'];
				$e_da = $row['e_date'];
            }
			$m_ko = $this -> _2Mal($m_ko,2);
			$content = $num."|".$m_ko."\r\n".$ko;

			header('Content-Type: text/x-delimtext;');
			header("Content-Disposition: attachment; filename=Cash_txt_".date('Y-m-d H:i:s',$e_da).".txt");
			header("Pragma: no-cache");
			header("Content-Type:text/html; charset=utf-8");
			header("Expires: 0");
			echo $content;
			exit;

        }else{
            $this->error('错误!');
            exit;
        }
    }
	
	//导出excel
	public function financeDaoChu_ChuN(){
		$this->_Admin_checkUser();
		set_time_limit(0);

		header("Content-Type:   application/vnd.ms-excel");
        header("Content-Disposition:   attachment;   filename=Cash_ier.xls");
		header("Pragma:   no-cache");
		header("Content-Type:text/html; charset=utf-8");
		header("Expires:   0");

		$m_page = (int)$_GET['p'];
		if(empty($m_page)){
			$m_page=1;
		}

        $times = M ('times');
        $Numso = array();
		$Numss = array();
        $map = 'is_count=0';
        //查询字段
        $field   = '*';
        import ( "@.ORG.ZQPage" );  //导入分页类
        $count = $times->where($map)->count();//总页数
        $listrows = C('PAGE_LISTROWS')  ;//每页显示的记录数
        $s_p = $listrows*($m_page-1)+1;
        $e_p = $listrows*($m_page);

        $title   =   "当期出纳 第".$s_p."-".$e_p."条 导出时间:".date("Y-m-d   H:i:s");



        echo   '<table   border="1"   cellspacing="2"   cellpadding="2"   width="50%"   align="center">';
        //   输出标题
        echo   '<tr   bgcolor="#cccccc"><td   colspan="6"   align="center">'   .   $title   .   '</td></tr>';
        //   输出字段名
        echo   '<tr  align=center>';
        echo   "<td>期数</td>";
        echo   "<td>结算时间</td>";
        echo   "<td>当期收入</td>";
        echo   "<td>当期支出</td>";
        echo   "<td>当期盈利</td>";
        echo   "<td>拨出比例</td>";
        echo   '</tr>';
        //   输出内容

        $rs = $times->where($map)->order(' id desc')->find();
		$Numso['0'] = 0;
		$Numso['1'] = 0;
		$Numso['2'] = 0;
		if ($rs){
			$eDate = strtotime(date('c'));  //time()
			$sDate = $rs['benqi'] ;//时间

			$this->MiHouTaoBenQi($eDate, $sDate, $Numso, 0);
		}


        $page_where = '';//分页条件
        $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
        //===============(总页数,每页显示记录数,css样式 0-9)
        $show = $Page->show();//分页变量
        $list = $times ->where($map)->field($field)->order('id desc')->page($Page->getPage().','.$listrows)->select();

//		dump($list);exit;

		$occ = 1;
		$Numso['1'] = $Numso['1']+$Numso['0'];
		$Numso['3'] = $Numso['3']+$Numso['0'];
		$maxnn=0;
		foreach ($list as $Roo){

			$eDate          = $Roo['benqi'];//本期时间
            $sDate          = $Roo['shangqi'];//上期时间
			$Numsd          = array();
			$Numsd[$occ][0] = $eDate;
			$Numsd[$occ][1] = $sDate;

			$this->MiHouTaoBenQi($eDate,$sDate,$Numss,1);
			//$Numoo = $Numss['0'];   //当期收入
			$Numss[$occ]['0'] = $Numss['0'];
			$Dopp  = M ('bonus');
			$field = '*';
			$where = " s_date>= '".$sDate."' And e_date<= '".$eDate."' ";
			$rsc   = $Dopp->where($where)->field($field)->select();
			$Numss[$occ]['1'] = 0;
			$nnn=0;
			foreach ($rsc as $Roc){
				$nnn++;
				$Numss[$occ]['1'] += $Roc['b0'] ;  //当期支出
				$Numb2[$occ]['1'] += $Roc['b1'];
				$Numb3[$occ]['1'] += $Roc['b2'];
				$Numb4[$occ]['1'] += $Roc['b3'];
				//$Numoo          += $Roc['b9'];//当期收入
			}
			$maxnn+=$nnn;
			$Numoo              = $Numss['0'];//当期收入
			$Numss[$occ]['2']   = $Numoo - $Numss[$occ]['1'];   //本期赢利
			$Numss[$occ]['3']   = substr( floor(($Numss[$occ]['1'] / $Numoo) * 100) , 0 ,3);  //本期拔比
			$Numso['1']        += $Numoo;  //收入合计
			$Numso['2']        += $Numss[$occ]['1'];           //支出合计
			$Numso['3']        += $Numss[$occ]['2'];           //赢利合计
			$Numso['4']         = substr( floor(($Numso['2'] / $Numso['1']) * 100) , 0 ,3);  //总拔比
			$Numss[$occ]['4']   = substr( ($Numb2[$occ]['1'] / $Numoo) * 100 , 0 ,4);  //小区奖金拔比
			$Numss[$occ]['5']   = substr( ($Numb3[$occ]['1'] / $Numoo) * 100 , 0 ,4);  //互助基金拔比
			$Numss[$occ]['6']   = substr( ($Numb4[$occ]['1'] / $Numoo) * 100 , 0 ,4); //管理基金拔比
			$Numss[$occ]['7']	= $Numb2[$occ]['1'];//小区奖金
			$Numss[$occ]['8'] 	= $Numb3[$occ]['1'] ;  //互助基金
			$Numss[$occ]['9'] 	= $Numb4[$occ]['1'];//管理基金
			$Numso['5']        += $Numb2[$occ]['1'];  //小区奖金合计
			$Numso['6']        += $Numb3[$occ]['1'];  //互助基金合计
			$Numso['7']        += $Numb4[$occ]['1'];  //管理基金合计
			$Numso['8']         = substr( ($Numso['5'] / $Numso['1']) * 100 , 0 ,4);  //小区奖金总拔比
			$Numso['9']         = substr( ($Numso['6'] / $Numso['1']) * 100 , 0 ,4);  //互助基金总拔比
			$Numso['10']        = substr( ($Numso['7'] / $Numso['1']) * 100 , 0 ,4);  //管理基金总拔比
			$occ++;
		}


        $i = 0;
        foreach($list as $row)   {
            $i++;
            echo   '<tr align=center>';
            echo   '<td>'   .   $row['id']   .   '</td>';
            echo   '<td>'   .   date("Y-m-d H:i:s",$row['benqi'])   .   '</td>';
            echo   '<td>'   .   $Numss[$i][0].  '</td>';
            echo   '<td>'   .   $Numss[$i][1]   .   '</td>';
            echo   '<td>'   .   $Numss[$i][2]   .   '</td>';
            echo   '<td>'   .   $Numss[$i][3]   .   ' % </td>';
            echo   '</tr>';
        }
        echo   '</table>';
    }
	
    //奖金查询导出excel
	public function financeDaoChu_JJCX(){
		$this->_Admin_checkUser();
		set_time_limit(0);

		header("Content-Type:   application/vnd.ms-excel");
		header("Content-Disposition:   attachment;   filename=Bonus-query.xls");
		header("Pragma:   no-cache");
		header("Content-Type:text/html; charset=utf-8");
		header("Expires:   0");

		$m_page = (int)$_REQUEST['p'];
		if(empty($m_page)){
			$m_page=1;
		}
		$fee   = M ('fee');    //参数表
        $times = M ('times');
        $bonus = M ('bonus');  //奖金表
        $fee_rs = $fee->field('s18')->find();
		$fee_s7 = explode('|',$fee_rs['s18']);

        $where = array();
		$sql = '';
		if(isset($_REQUEST['FanNowDate'])){  //日期查询
			if(!empty($_REQUEST['FanNowDate'])){
				$time1 = strtotime($_REQUEST['FanNowDate']);                // 这天 00:00:00
				$time2 = strtotime($_REQUEST['FanNowDate']) + 3600*24 -1;   // 这天 23:59:59
				$sql = "where e_date >= $time1 and e_date <= $time2";
			}
		}

        $field   = '*';
        import ( "@.ORG.ZQPage" );  //导入分页类
        $count = count($bonus -> query("select id from __TABLE__ ". $sql ." group by did")); //总记录数
        $listrows = C('PAGE_LISTROWS')  ;//每页显示的记录数
		$page_where = 'FanNowDate=' . $_REQUEST['FanNowDate'];//分页条件
		if(!empty($page_where)){
			$Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
		}else{
			$Page = new ZQPage($count, $listrows, 1, 0, 3);
		}
		//===============(总页数,每页显示记录数,css样式 0-9)
		$show = $Page->show();//分页变量
		$status_rs = ($Page->getPage()-1)*$listrows;
		$list = $bonus -> query("select e_date,did,sum(b0) as b0,sum(b1) as b1,sum(b2) as b2,sum(b3) as b3,sum(b4) as b4,sum(b5) as b5,sum(b6) as b6,sum(b7) as b7,sum(b8) as b8,max(type) as type from __TABLE__ ". $sql ." group by did  order by did desc limit ". $status_rs .",". $listrows);
		//=================================================


        $s_p = $listrows*($m_page-1)+1;
        $e_p = $listrows*($m_page);

        $title   =   "奖金查询 第".$s_p."-".$e_p."条 导出时间:".date("Y-m-d   H:i:s");



        echo   '<table   border="1"   cellspacing="2"   cellpadding="2"   width="50%"   align="center">';
        //   输出标题
        echo   '<tr   bgcolor="#cccccc"><td   colspan="10"   align="center">'   .   $title   .   '</td></tr>';
        //   输出字段名
        echo   '<tr  align=center>';
        echo   "<td>结算时间</td>";
        echo   "<td>".$fee_s7[0]."</td>";
        echo   "<td>".$fee_s7[1]."</td>";
        echo   "<td>".$fee_s7[2]."</td>";
        echo   "<td>".$fee_s7[3]."</td>";
        echo   "<td>".$fee_s7[4]."</td>";
        echo   "<td>".$fee_s7[5]."</td>";
        echo   "<td>".$fee_s7[6]."</td>";
        echo   "<td>合计</td>";
        echo   "<td>实发</td>";
        echo   '</tr>';
        //   输出内容

//		dump($list);exit;

        $i = 0;
        foreach($list as $row)   {
            $i++;
            $mmm = $row['b1']+$row['b2']+$row['b3']+$row['b4']+$row['b5']+$row['b6']+$row['b7'];
            echo   '<tr align=center>';
            echo   '<td>'   .   date("Y-m-d H:i:s",$row['e_date'])   .   '</td>';
            echo   "<td>"   .   $row['b1'].  "</td>";
            echo   "<td>"   .   $row['b2'].  "</td>";
            echo   "<td>"   .   $row['b3'].  "</td>";
            echo   "<td>"   .   $row['b4'].  "</td>";
            echo   "<td>"   .   $row['b5'].  "</td>";
            echo   "<td>"   .   $row['b6'].  "</td>";
            echo   "<td>"   .   $row['b7'].  "</td>";
            echo   "<td>"   .   $mmm.  "</td>";
            echo   "<td>"   .   $row['b0'].  "</td>";
            echo   '</tr>';
        }
        echo   '</table>';
        unset($bonus,$times,$fee,$list);
    }

}
?>