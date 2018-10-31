<?php
class TreeAction extends CommonAction {
	public function _initialize() {
		header("Content-Type:text/html; charset=utf-8");
		$this->_inject_check(0);//调用过滤函数
		//$this->_checkUser();
		$this->_Config_name();//调用参数
	}

	//会员级别颜色
	function ji_Color(){
		$Color = array();
		$Color['1'] = "#E6E8FA";
		$Color['2'] = "#5CACEE";//"#E6E6FA";//"#DDA0DD";
		$Color['3'] = "#D9D919";
 		$Color['4'] = "#FF5555";//"#FFFF00";
 		$Color['5'] = "#9BCD9B";
 		$Color['6'] = "#FFFF00";
		return $Color;
	}
	
	//会员级别颜色
	function ji_Color_B(){
		$Color = array();
		$Color['1'] = "#D9D919";
		$Color['2'] = "#5CACEE";//"#E6E6FA";//"#DDA0DD";
		$Color['3'] = "#D9D919";
 		$Color['4'] = "#FF5555";//"#FFFF00";
		$Color['5'] = "#9BCD9B";
//		$Color['6'] = "#7F7F7F";
//		$Color['7'] = "#FFFF00";
		return $Color;
	}

	function AC_Color(){
		$HYJJ="";
		$this->_levelConfirm($HYJJ);
		$Color = array();
		$Color['1'] = $HYJJ[1];
		$Color['2'] = $HYJJ[2];
		$Color['3'] = $HYJJ[3];
		$Color['4'] = $HYJJ[4];
		$Color['5'] = $HYJJ[5];
 		$Color['6'] = $HYJJ[6];
//		$Color['7'] = "#0066FF";
		return $Color;
	}

	//开通 未开通 报单中心
	function Mi_Cheng(){
		$Color['0']  = '临时会员';
		$Color['1']  = '正式会员';
		$Color['2']  = '报单中心';//'报单中心';
		return $Color;
	}

	function kd_Color(){
		$Color['0']    = '#C0C0C0';
		$Color['1']    = '#F5FFFA';
		$Color['2']    = '#DDA0DD';
		return $Color;
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
			$this->display('Public/cody');
			exit;
		}else{
			$this->error('二级密码错误!');
			exit;
		}
	}


	//二级验证后调转页面
	public function Codys(){
		$this->_checkUser();
		$Urlsz = $_POST['Urlsz'];
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
			$where =array();
			$where['id'] = $_SESSION[C('USER_AUTH_KEY')];
			$where['passopen'] = md5($pass);
			$list = $fck->where($where)->field('id')->find();
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
				$_SESSION['UrlszUserpass'] = 'MyssPuTao';
				$bUrl = __URL__.'/PuTao';
				$this->_boxx($bUrl);
				break;
			case 2;
				$_SESSION['UrlszUserpass'] = 'Myssindex';
				$bUrl = __URL__.'/index';
				$this->_boxx($bUrl);
				break;
			case 3;
				$_SESSION['UrlszUserpass'] = 'MyssQiCheng';
				$bUrl = __URL__.'/QiCheng';
				$this->_boxx($bUrl);
				break;
			case 4;
				$_SESSION['UrlszUserpass'] = 'MyssQiCheng';
				$bUrl = __URL__.'/Tree2';
				$this->_boxx($bUrl);
				break;
			case 5;
				$_SESSION['UrlszUserpass'] = 'MyssTreePass';
				$bUrl = __URL__.'/Tree3';
				$this->_boxx($bUrl);
				break;
			case 6;
				$_SESSION['UrlszUserpass'] = 'MyssTreeRe';
				$bUrl = __URL__.'/TreeAjax';
				$this->_boxx($bUrl);
				break;
			case 7;
				$_SESSION['UrlszUserpass'] = 'MyssTreeRe';
				$bUrl = __URL__.'/TreeAjaxb';
				$this->_boxx($bUrl);
				break;
			case 8;
				$_SESSION['UrlszUserpass'] = 'MyssTree_yx';
				$bUrl = __URL__.'/Tree_yx';
				$this->_boxx($bUrl);
				break;
			case 9;
				$_SESSION['UrlszUserpass'] = 'MyssTree_2B';
				$bUrl = __URL__.'/Tree2_B';
				$this->_boxx($bUrl);
				break;
			default;
				$this->error('二级密码错误1!');
				break;
		}
	}

	//跳转到注册页面
	public function KaiBoLuo(){
		$time = date('H');
		$RID = (int) $_GET['RID'];//推荐人
		$TPL = (int) $_GET['TPL'];//左区右区
		$FID = (int) $_GET['FID'];//安置人
		$_SESSION['Urlszpass'] = 'MyssBoLuo';
		$bUrl = __APP__."/Reg/users/RID/". $RID ."/TPL/". $TPL ."/FID/". $FID;
		redirect($bUrl);//URL 重定向
		exit;
	}

	//推荐图
	public function Tree() {
		$this->_checkUser();
		$fck = M("fck");
		$ID  = (int) $_GET['UID'];
		$Mmid=$_SESSION[C('USER_AUTH_KEY')];
		if (empty($ID))$ID = $_SESSION[C('USER_AUTH_KEY')];
		if (!is_numeric($ID) || strlen($ID) > 20 ) $ID = $_SESSION[C('USER_AUTH_KEY')];
		$UserID = $_POST['UserID'];

		if (strlen($UserID) > 20 ){
			$this->error( '错误操作！');
			exit;
		}
		if (!empty($UserID)){
			if (!$fck->autoCheckToken($_POST)){
				$this->error( '页面过期，请刷新页面！');
				exit;
			}
			$fwhere = "re_path like '%,". $Mmid .",%' and user_id='". $UserID ."' ";
//			$fwhere = "user_id='".$UserID."'";
			$frs = $fck->where($fwhere)->field('id')->find();
			if (!$frs){
				$this->error('没有找到该会员！');
				exit;
			}else{
				$ID = $frs['id'];
			}
		}
		$where = array();
		$where['id'] = $ID;
		$where['_string'] = "(re_path like '%,".$_SESSION[C('USER_AUTH_KEY')].",%' or id = ".$_SESSION[C('USER_AUTH_KEY')].")";
		$rs = $fck->where($where)->find();
		if (!$rs){
			$this->error('没有找到该会员！');
			exit;
		}else{
			$UID		= $rs['id'];
			$UserID		= $rs['user_id'];
			$NickName	= $rs['nickname'];
			$FatherID	= $rs['father_id'];
			$FatherName	= $rs['father_name'];
			$ReID		= $rs['re_id'];
			$ReName		= $rs['re_name'];
			$isPay		= $rs['is_pay'];
			$isAgent	= $rs['is_agent'];
			$isJB	= $rs['is_jb'];
			$isLock		= $rs['is_lock'];
			$uLevel		= $rs['u_level'];
			$NanGua		= 'aappleeva';
			$ReNUMS		= $rs['re_nums'];
			$QiCheng_l	= $rs['l'];
			$QiCheng_r  = $rs['r'];
		}
		$tree_Images = __PUBLIC__ .'/Images/tree/';//图片所在文件夹
		$rows = array();
		$rows['0'] .= "<SCRIPT LANGUAGE='JavaScript'>" . chr(13) . chr(10);
		$rows['0'] .= "var tree = new MzTreeView('tree');" . chr(13) . chr(10);
		$rows['0'] .= "tree.icons['property'] = 'property.gif';" . chr(13) . chr(10);
		$rows['0'] .= "tree.icons['Trial'] = 'trial.gif';" . chr(13) . chr(10);//试用
		$rows['0'] .= "tree.icons['Official']  = 'Official.gif';" . chr(13) . chr(10);//正试成员
		$rows['0'] .= "tree.iconsExpand['book'] = 'bookopen.gif';" . chr(13) . chr(10); //展开时对应的图片
		$rows['0'] .= "tree.icons['Center']  = 'center.gif';" . chr(13) . chr(10);//报单中心成员
		$rows['0'] .= "tree.setIconPath('". $tree_Images ."'); " . chr(13) . chr(10);//可用相对路径
		$i = -1;
		$j = 1;

		$fee = M('fee');
		$fee_rs = $fee->field('s10')->find();
		$Level = explode('|',$fee_rs['s10']);
		$uLe    = $uLevel-1;
		if ($isAgent >= 2) {
			$rows['0'] .= "tree.nodes['" . $i . "_" . $j . "'] = 'text:" . $UserID . "[". $Level[$uLe] ."];icon:Center;url:Tree/UID/" . $UID .";';" . chr(13) . chr(10) ;
		}else{
			if ($isPay == 1){
				$rows['0'] .= "tree.nodes['" . $i . "_" . $j . "'] = 'text:" . $UserID . "[". $Level[$uLe] ."];icon:Official;url:Tree/UID/". $UID . ";';" . chr(13) . chr(10) ;
			}else{
				$rows['0'] .= "tree.nodes['" . $i . "_" . $j . "'] = 'text:" . $UserID. "[". $Level[$uLe] ."];icon:Trial;url:Tree/UID/". $UID . ";';" . chr(13). chr(10);
			}
		}
		$this->_MakeTree($UID, 1, $isPay, 1, $j, $rows);
		$rows['0'] .= "tree.setTarget('_self');" . chr(13) . chr(10);
		//document.write(tree.toString());    //亦可用 obj.innerHTML = tree.toString();
		$rows['0'] .= "thisTree.innerHTML = tree.toString();" . chr(13) . chr(10);
		//$rows['0'] .= "MzTreeView.prototype.expandAll.call(tree);";
		$rows['0'] .= "</SCRIPT>";
		$this->assign('rs', $rows);
		$this->assign('ID', $ID);
		$this->display('Tree');
	}
	//推荐图_调用函数
	private function _MakeTree($ID,$FatherId,$IsZs,$N,$j,&$rows){
		$fck = M("fck");
		$fee = M('fee');

		$fee_rs = $fee->field('s10')->find();
		$Level = explode('|',$fee_rs['s10']);
		global $j;
		if ($j <= 1)$j = 1;
		$N++;
		if ($N <= 100){
			$k = 1;
			$where 			= array();
			$where['re_id']	= $ID;
			$rs = $fck->where($where)->order('is_pay desc,pdt asc,id asc')->select();
			foreach ($rs as $rss){
				$j		= $j+1;
				$uUser	= $rss['user_id'];
				$uName	= $rss['nickname'];
				$uIsPay	= $rss['is_pay'];
				$ID		= $rss['id'];
				$uLevel	= $rss['u_level'];
				$misjb	= $rss['is_jb'];
				$Agent	= $rss['is_agent'];
				$ReNUMS	= $rss['re_nums'];
				$QiCheng_l	= $rss['l'];
				$QiCheng_r  = $rss['r'];
				//级别

				$uLe    = $uLevel-1;
				if ($Agent >= 2){
					$rows['0'] .= "tree.nodes['" .  $FatherId .  "_" . $j . "'] = 'text:" . $uUser . "[". $Level[$uLe] ."];icon:Center;url:Tree/UID/" . $ID . ";';" ;
				}else{
					if ($uIsPay == 1){
						$rows['0'] .= "tree.nodes['" .  $FatherId .  "_" . $j . "'] = 'text:" . $uUser . "[". $Level[$uLe] ."];icon:Official;url:Tree/UID/" . $ID . ";';" ;
					}else{
						$rows['0'] .= "tree.nodes['" .  $FatherId .  "_" . $j . "'] = 'text:" . $uUser . "[". $Level[$uLe] ."];icon:Trial;url:Tree/UID/" . $ID . ";';" ;
					}
				}
				$k = $j;
				$this->_MakeTree($ID, $k, $uIsPay, $N, $j, $rows);
			}
		}
	}



	//一线图
	public function Tree1(){
		$this->_checkUser();
		$kd_c = $this->kd_Color();  //是否开通

		$fck = M ('fck');
		$id     = $_SESSION[C('USER_AUTH_KEY')];
		$UID    = (int) $_GET['ID'];
		if (empty($UID)) $UID = $id ;
		$UserID = $_POST['UserID'];  //跳转到 X 用户
		if (!empty($UserID)){
			if (strlen($UserID) > 20 ){
				$this->error( '错误操作！');
				exit;
			}
			$where = " user_id='". $UserID ."' ";
			$field = 'id';
			$rs = $fck ->where($where)->field($field)->find();
			if($rs == false){
				$this->error('没有该用户!');
				exit();
			}else{
				$UID = $rs['id'];
			}
		}

		$where = array();
		$where['id']     = array('gt',$UID);
		$field = '*';
		$rs = $fck->where($where)->order('rdt asc')->find();

		$out_where = array();
		$out_where['id'] = array(array('egt',$id),array('egt',$UID),'and');
		$out_rs = $fck->where($out_where)->field('id,user_id,is_pay,is_agent,u_level')->order('id asc')->limit(9)->select();
		//dump($fck->getLastSql());
		$lhe = 30;
		$tps = __PUBLIC__ .'/Images/tree1/';
		$i = 0;
		$Treex = "<table width='92' border='0' align='left' cellpadding='0' cellspacing='0'>";
		foreach ($out_rs as $vo){
			$i++;
			if ($vo['is_pay']>0){
				$is_color = 1;
			}else{
				$is_color = 0;
			}
			if ($vo['is_agent']>0){
				$is_color = 2;
			}
			$Level  = explode('|',C('Member_Level'));
			$uLe    = $vo['u_level']-1;
			$Treex .= "<tr align='center'><td width='90' bgcolor='#FFFFFF'><table width='90' border='0' cellpadding='0' cellspacing='1' bgcolor='#ADBA84'>
			<tr align='center'><td width='90' height='25' style='background:".$kd_c[$is_color]."'>
			<a href='". __URL__ ."/Tree1/ID/". $vo['id']."'>". $vo['user_id'] ."</a> [". $i ."]</td></tr>
			<tr align='center'><td height='25'> ". $Level[$uLe] ." </td></tr></table></td></tr>
			<tr align='center'><td height='25'><img src='". $tps ."bottom.gif' height='". $lhe ."'>
			</td></tr>";
		}
		for($u=$i+1;$u<=10;$u++){
			$Treex .= "<tr align='center'><td width='90' bgcolor='#FFFFFF'>
			<table width='90' border='0' cellpadding='0' cellspacing='1' bgcolor='#ADBA84'>
			<tr align='center'><td>[ ". $u ." ]</td></tr></table></td></tr>";
			if ($u<10){$Treex .= "<tr align='center'><td><img src='". $tps ."bottom.gif' height='". $lhe ."'></td></tr>";}
		}
		$Treex .= "</table>";

		$this->assign('Treex',$Treex);
		$this->display('Tree1');
	}



	//双轨图
	public function Tree2(){
		$time = date('H');
		$this->_checkUser();
		$ji_c = $this->ji_Color();  //级别颜色
		$kd_c = $this->kd_Color();  //是否开通
		$mi_c = $this->Mi_Cheng();  //级别名称
		$ac_c = $this->AC_Color();  //级别名称

		$fee = M ('fee');
		$fee_rs = $fee->field('i4')->find();

		$i4 = $fee_rs['i4'];
		if ($i4==0){
			$openm=1;
		}else{
			$openm=0;
		}

		$fck   =  M('fck');
		$fee_rs = $fck -> find();
		$id  = $_SESSION[C('USER_AUTH_KEY')];
		$myid=$_SESSION[C('USER_AUTH_KEY')];
		$UID = (int) $_GET['ID'];
		if (empty($UID)){$UID = $id;}
		$UserID = $_POST['UserID'];  //跳转到 X 用户
		if (!empty($UserID)){
			if (strlen($UserID) > 20 ){
				$this->error( '错误操作！');
				exit;
			}
			$where = "p_path like '%,". $UID .",%' and user_id='". $UserID ."' ";
//			$where = "user_id='". $UserID ."' ";
			$field = 'id,is_boss';
			$rs = $fck ->where($where)->field($field)->find();
			if($rs == false){
				$this->error('没有该用户!');
				exit();
			}else{
				$UID = $rs['id'];
			}
		}
		
		
		$where =array();
		$where['id'] = $UID;
		$where['_string'] = 'id>='.$id;
		$field = '*';
		$rs = $fck ->where($where)->field($field)->find();
		if (!$rs){
			$this->error('没有该用户!');
			exit();
		}else{
			$ID			= $rs['id'];
			$UserID		= $rs['user_id'];
			$NickName	= $rs['nickname'];
			$TreePlace	= $rs['treeplace'];   //区分左右 0为左边,1为右边
			if($ID==$id){
				$FatherID = $id;
			}else{
				$FatherID	= $rs['father_id'];    //安置人ID
			}

			$isPay		= $rs['is_pay'];		  //是否为正式(开通时为正式)
			$isLock		= $rs['is_lock'];	  //锁定(是否可以登录系统)
			$uLevel		= $rs['u_level'];      //级别
			$pPath		= $rs['p_path'];       //自已的路径
			$pLevel		= $rs['p_level'];	  //层数(数字)
			$Rid		= $rs['id'];
			$L			= $rs['l'];
			$R			= $rs['r'];
			
			$benqiL		= $rs['benqi_l'];//本期新增
			$benqiR		= $rs['benqi_r'];
			$SpareL		= $rs['shangqi_l'];//上期剩余
			$SpareR		= $rs['shangqi_r'];
		
			$zjj        = $rs['zjj'];        //总奖金
			$user_tel   = $rs['user_tel'];
			$qq   		= $rs['qq'];

			$isagent	= $rs['is_agent'];  //
			$cpzj 		= $rs['cpzj'];

//			$LL=0;
//			$RR=0;
//			$this->todayindan($ID,$LL,$RR);
		}
		if ($isPay>1) $isPay=1;


		if($rs['is_agent'] > 1){
			$isPay = 2;    //服务中心
		}

		//显示层数
		$uLev = (int) $_GET['uLev'];		//$Lev 记录显示层数
		if (is_numeric($uLev) == false) $uLev = $_SESSION['uLev2'];
		if (is_numeric($uLev) == false) $uLev = 3;
		if ($uLev < 2 || $uLev > 11)    $uLev = 3;
		$_SESSION['uLev2']=$uLev;
		for ($i=1;$i<=$uLev;$i++){
			$Nums = $Nums + pow(2,$i);		//pow(x,y) 返回x的y次方
		}
		global $TreeArray;
		$TreeArray = array();

		for ($i=1;$i<=$Nums;$i++){
			$TreeArray[$i] = "<table border='0' cellpadding='0' cellspacing='1' class='tu_box'><tr><td class='tu_ko'> 空位".$i." </td></tr></table>";
		}
		
		$bj = "style='background:". $kd_c[$isPay] ."';";  //表格背景色
		$StTab = "<table border='0' cellpadding='0' cellspacing='1' class='tu_box'><tr><td colspan='3' style='background:". $ji_c[$uLevel] .";font-weight:bold;'>";
		$MyYJ = "</td></tr>";
//		$MyYJ .= "<tr><td colspan='3' $bj><a class='title' href='#' title='会员编号：".$UserID."|手机：".$user_tel."|QQ：".$qq."'>联系方式</a></td></tr>";
//		$MyYJ .= "<tr><td colspan='3' $bj><a class='title' href='#' title='投资金额：".$cpzj."'>投资金额</a></td></tr>";
 		$MyYJ .= "<tr><td class='tu_l' $bj>$L</td><td class='tu_z' $bj>总</td><td class='tu_r' $bj>$R</td></tr>";
		$MyYJ .= "<tr><td class='tu_l' $bj>$SpareL</td><td class='tu_z' $bj>余</td><td class='tu_r' $bj>$SpareR</td></tr>";
 		$MyYJ .= "<tr><td class='tu_l' $bj>$benqiL</td><td class='tu_z' $bj>新</td><td class='tu_r' $bj>$benqiR</td></tr>";
		$MyYJ .= "</table>";

		$ZiJi   = $StTab."<a href='#'>". $UserID ."</a>". $MyYJ;
		$Str4C0 = "<table  border='0' cellpadding='0' cellspacing='1' class='tu_box'><tr><td class='tu_ko'>";
		$Str4C1 = "<a href='". __URL__ ."/KaiBoLuo/RID/". $myid ."/TPL/";
		$Str4C4 = "</td></tr></table>";
		if ($isPay > 0){
			$i = pow(2,$uLev);
			$TreeArray['1'] = $Str4C0.$Str4C1."0/FID/". $ID ."' target='_self'>点击注册</a>". $Str4C4;
			$TreeArray[$i]  = $Str4C0.$Str4C1."1/FID/". $ID ."' target='_self'>点击注册</a>". $Str4C4;
		}else{
//			$i = pow(2,$uLev);
//			$TreeArray['1']	= $Str4C0.$Str4C1."0/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
//			$TreeArray[$i]	= $Str4C0.$Str4C1."1/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
		}

		$TreeArray['0'] = $ZiJi;

		$this->Tree2_MtKass($UID, 0, $pLevel, $uLev, $Str4C0, $Str4C1, $Str4C4,  $TreeArray, $Nums);
		//会员ID,0,绝对层次,显示层高,表开始,表内链接,表结束  ,级别颜色数组,所有空位表格,显示多少会员数(包括空位数)
		$wop = '';
		$this->Tree2_showTree($uLev, $TreeArray, $wop);

		$fee = M('fee');
		$fee_rs = $fee->field('s10,s9')->find();
		$Level = explode('|',$fee_rs['s10']);
		$L_cpzj = explode('|',$fee_rs['s9']);
		
		$this->assign('Level',$Level);
		$this->assign('L_cpzj',$L_cpzj);
		
		$this->assign('openm',$openm);
		$this->assign('ColorUA',$ji_c);
		$this->assign('TU_Color',$kd_c);
		$this->assign('TU_MiCheng',$mi_c);
		$this->assign('AC_Color',$ac_c);
		$this->assign('UID',$UID);
		$this->assign('uLev',$uLev);
		$this->assign('FatherID',$FatherID);
		$this->assign('wop',$wop);
		$this->display('Tree2');

	}
	
	//双轨图---生成下层会员内容
	private function Tree2_MtKass($FatherID,$iL,$pLevel,$uLev,$Str4C0,$Str4C1,$Str4C4,&$TreeArray,$Nums){
		$ji_c = $this->ji_Color();  //级别颜色
		$kd_c = $this->kd_Color();  //是否开通
		$mi_c = $this->Mi_Cheng();  //级别名称
		if (!empty($FatherID)){
			$fck = M("fck");
			//$where = array();
			$maxLev = $pLevel+$uLev;
			$where = "father_id=". $FatherID ." and p_level<=". $maxLev ." and treeplace<2";
			$field = '*';
			$rs    = $fck->where($where)->field($field)->order('treeplace asc')->select();
			foreach($rs as $rss){
				if ($rss['treeplace'] == 0){
					$k = $iL + 1;
				}else{
					$i = ($pLevel + $uLev) - $rss['p_level'] + 1;
					$j = pow(2,$i);
					$k = $j + $iL;
				}
				$i			= ($pLevel + $uLev) - $rss['p_level'];
				$Uo			= $k + 1;
				$Yo			= $k + pow(2,($pLevel + $uLev) - $rss['p_level']);
				$Leve		= $rss['u_level'];	//用户级别
				$uisLock	= $rss['is_lock'];	//是否为正式会员
				$Lo			= $rss['l'];		//
				$Ro			= $rss['r'];		//
				
				$SpareLo	= $rss['shangqi_l'];
				$SpareRo	= $rss['shangqi_r'];
				$SpareLo		= $rss['l_nums'];//上期剩余
				$SpareRo		= $rss['r_nums'];
				$benqiLo	= $rss['benqi_l'];
				$benqiRo	= $rss['benqi_r'];
				$Rid		= $rss['id'];
				$uUserID	= $rss['user_id'];
				$uisPay		= $rss['is_pay'];
				$upLevel	= $rss['p_level'];
				$zjj        = $rss['zjj'];
				$uis_agent	= $rss['is_agent'];
				$uuser_tel	= $rss['user_tel'];
				$uqq		= $rss['qq'];
				$cpzj       = $rss['cpzj'];

//				$LL=0;
//				$RR=0;
//				$this->todayindan($rss['id'],$LL,$RR);

				if ($uisPay>1) $uisPay=1;
				if($rss['is_agent'] > 0){
					$uisPay = 2;    //服务中心
				}
				$bj = "style='background:". $kd_c[$uisPay] .";'";  //表格背景色
				$StTab = "<table border='0' cellpadding='0' cellspacing='1' class='tu_box'><tr><td colspan='3' style='background:".$ji_c[$Leve].";font-weight:bold;'>";
				$MyYJ = "</td></tr>";
//				$MyYJ .= "<tr><td colspan='3' $bj><a class='title' href='#' title='会员编号：".$uUserID."|手机：".$uuser_tel."|QQ：".$uqq."'>联系方式</a></td></tr>";
//				$MyYJ .= "<tr><td colspan='3' $bj><a class='title' href='#' title='投资金额：".$cpzj."'>投资金额</a></td></tr>";
				$MyYJ .= "<tr><td class='tu_l' $bj>$Lo</td><td class='tu_z' $bj>总</td><td class='tu_r' $bj>$Ro</td></tr>";
				$MyYJ .= "<tr><td class='tu_l' $bj>$SpareLo</td><td class='tu_z' $bj>余</td><td class='tu_r' $bj>$SpareRo</td></tr>";
 				$MyYJ .= "<tr><td class='tu_l' $bj>$benqiLo</td><td class='tu_z' $bj>新</td><td class='tu_r' $bj>$benqiRo</td></tr>";
				$MyYJ .= "</table>";

	//			$Str = $StTab."<a href='". __URL__ ."/PuTao/ID/". $Rid ."'>会员编号：". $uUserID ."</a>". $MyYJ;
				$Str = $StTab."<a href='". __URL__ ."/Tree2/ID/". $Rid ."'>". $uUserID ."</a>". $MyYJ;
				$Str4C2 = "/FID/". $Rid ."'>点击注册</a>";

				if ($uisPay > 0){
					if ($Yo <= $Nums + 1 && $i>0){
						$TreeArray[$Uo] = $Str4C0. $Str4C1 ."0". $Str4C2 . $Str4C4;
						$TreeArray[$Yo] = $Str4C0. $Str4C1 ."1". $Str4C2 . $Str4C4;
					}
				}else{
//					if ($Yo<=$Nums+1 && $i>0){
//						$TreeArray[$Uo]=$Str4C0.$Str4C1."0".$Str4C2.$Str4C4;
//						$TreeArray[$Yo]=$Str4C0.$Str4C1."1".$Str4C2.$Str4C4;
//					}
				}
				$TreeArray[$k] = $Str;
				if ($upLevel < $pLevel + $uLev){
					//查出来的下级的绝对层	 //上级的绝对层,显示层数
					$this->Tree2_MtKass($Rid, $k, $pLevel, $uLev, $Str4C0, $Str4C1, $Str4C4,  $TreeArray, $Nums, $ColorUA);
				}
			}

		}
	}
	//双轨图----生成表格内容
	private function Tree2_showTree($uLev,$TreeArray,&$wop){
					       //显示层高,所有空位表格,空
		for ($i = 1;$i <= $uLev;$i++){
			$Nums = $Nums + pow(2,$i);    //要显示用户的数量
		}
		$wid = 12;
		$arr = array();
		global $arrs;
		$arrs = array();

		for ($i = 0;$i <= $Nums;$i++){
			$arr[$i] = $TreeArray[$i];
		}

		$arrs[0][0] = $arr;

		for ($i = 1;$i <= $uLev;$i++){
			for ($u = 1 ; $u <= pow(2,($i-1)) ; $u++){
				$yyyo = $arrs[$i-1][$u-1];
				$ta = array();
				$tar = count($yyyo);
				//echo $tar."<br>";
				for ($ti = 0 ; $ti < $tar ; $ti++){
					$ta[$ti] = $yyyo[$ti+1];
				}
				$to    = floor($tar/2)-1;
				$tarr1 = array();
				$tarr2 = array();

				for ($tj = 0 ; $tj <= $to ; $tj++){
					$tarr1[$tj] = $ta[$tj];
					$tarr2[$tj] = $ta[$to+$tj+1];
				}
				$arrs[$i][($u-1)*2]   = $tarr1;
				$arrs[$i][($u-1)*2+1] = $tarr2;
			}
		}

		$lhe = 20;//行高
		$tps = __PUBLIC__ .'/Images/tree/';
		$strL = "<img src='". $tps ."t_tree_bottom_l.gif' height='". $lhe ."'><img src='". $tps ."t_tree_line.gif' width='25%' height='". $lhe ."'><img src='". $tps . "t_tree_top.gif' height='". $lhe ."' alt='顶层'><img src='". $tps ."t_tree_line.gif' width='25%' height='". $lhe ."'><img src='". $tps ."t_tree_bottom_r.gif' height='". $lhe ."'>";

		$strW = "<img width='" . $wid . "' height='0'><br />";

        $wop = '';

		for ($i = 0  ;  $i <= $uLev  ;  $i++){
			$wop .= "<table width='100%' border='0' cellpadding='1' cellspacing='1'>";
			for ($t = 0  ;  $t <= 1  ;  $t++){
				if ($t != 1 || $i != $uLev){
					$wop.="<tr align='center'>";
					$oop= pow(2,$i)-1;
					for ($j = 0  ;  $j <= $oop ;  $j++){
						$eop=100/pow(2,$i);
						if ($t==1){
							$wop.="<td class='borderno' width='". $eop ."%' valign='top'>";
							$wop.=$strL;
						}else{
							$bcxx=$arrs[$i][$j][0];
							$rp=$i+1;
							$wop.="<td class='borderlrt' width='". $eop ."%' valign='top' title='第" . $rp . "层'>";
							$wop.=$strW;
							$wop.=$bcxx;
						}
						$wop.="</td>";
					}
					$wop.="</tr>";
				}
			}
			$wop.="</table>";
		}
		$wop.="</td></tr></table>";
	}
	
	//双轨图----生成表格内容
	private function Tree2_showTree_b($uLev,$TreeArray,&$wop,$ncc=2){
					       			//显示层高,所有空位表格,空
		for ($i = 1;$i <= $uLev;$i++){
			$Nums = $Nums + pow(2,$i);    //要显示用户的数量
		}
		
		$tree_img = __PUBLIC__."/Images/tree/";
		$line_img = $tree_img."t_tree_line.gif";
		$lr_img = $tree_img."t_tree_bottom.gif";
		if($ncc%2==1){
			$mm_img = $tree_img."t_tree_mid.gif";
		}else{
			$mm_img = $tree_img."t_tree_top.gif";
		}
		
		for($i=0;$i<=$uLev;$i++){//层数
			
			
			
			
		}
		
		
		
		
		$wid = 12;
		$arr = array();
		global $arrs;
		$arrs = array();

		for ($i = 0;$i <= $Nums;$i++){
			$arr[$i] = $TreeArray[$i];
		}

		$arrs[0][0] = $arr;

		for ($i = 1;$i <= $uLev;$i++){
			for ($u = 1 ; $u <= pow(2,($i-1)) ; $u++){
				$yyyo = $arrs[$i-1][$u-1];
				$ta = array();
				$tar = count($yyyo);
				//echo $tar."<br>";
				for ($ti = 0 ; $ti < $tar ; $ti++){
					$ta[$ti] = $yyyo[$ti+1];
				}
				$to    = floor($tar/2)-1;
				$tarr1 = array();
				$tarr2 = array();

				for ($tj = 0 ; $tj <= $to ; $tj++){
					$tarr1[$tj] = $ta[$tj];
					$tarr2[$tj] = $ta[$to+$tj+1];
				}
				$arrs[$i][($u-1)*2]   = $tarr1;
				$arrs[$i][($u-1)*2+1] = $tarr2;
			}
		}

		$lhe = 20;//行高
		$tps = __PUBLIC__ .'/Images/tree/';
		$strL = "<img src='". $tps ."t_tree_bottom_l.gif' height='". $lhe ."'><img src='". $tps ."t_tree_line.gif' width='25%' height='". $lhe ."'><img src='". $tps . "t_tree_top.gif' height='". $lhe ."' alt='顶层'><img src='". $tps ."t_tree_line.gif' width='25%' height='". $lhe ."'><img src='". $tps ."t_tree_bottom_r.gif' height='". $lhe ."'>";

		$strW = "<img width='" . $wid . "' height='0'><br />";

        $wop = '';

		for ($i = 0  ;  $i <= $uLev  ;  $i++){
			$wop .= "<table width='100%' border='0' cellpadding='1' cellspacing='1'>";
			for ($t = 0  ;  $t <= 1  ;  $t++){
				if ($t != 1 || $i != $uLev){
					$wop.="<tr align='center'>";
					$oop= pow(2,$i)-1;
					for ($j = 0  ;  $j <= $oop ;  $j++){
						$eop=100/pow(2,$i);
						if ($t==1){
							$wop.="<td class='borderno' width='". $eop ."%' valign='top'>";
							$wop.=$strL;
						}else{
							$bcxx=$arrs[$i][$j][0];
							$rp=$i+1;
							$wop.="<td class='borderlrt' width='". $eop ."%' valign='top' title='第" . $rp . "层'>";
							$wop.=$strW;
							$wop.=$bcxx;
						}
						$wop.="</td>";
					}
					$wop.="</tr>";
				}
			}
			$wop.="</table>";
		}
		$wop.="</td></tr></table>";
	}

	public function todayindan($uid=0,&$danL=0,&$danR=0){
		$fck=M('fck');
		$dayt=strtotime(date('Y-m-d'));
		$tomt=strtotime(date('Y-m-d'))+3600*24;
		$insql=' and pdt>='.$dayt.' and pdt<'.$tomt;
		$where_r['id']=$uid;
		$rs=$fck->where($where_r)->find();
		if($rs){
			$rs_l=$fck->where('father_id='.$uid.' and treeplace=0')->field('id')->find();
			if($rs_l){
				$lid=$rs_l['id'];
				$suml=$fck->where('(id='.$lid.' or p_path like "%'.$lid.'%") and is_pay=1'.$insql)->sum('f4');
				if($suml!=false){
					$danL=$suml;
				}
			}else{
				$danL=0;
			}

			$rs_r=$fck->where('father_id='.$uid.' and treeplace=1')->field('id')->find();
			if($rs_r){
				$rid=$rs_r['id'];
				$sumr=$fck->where('(id='.$rid.' or p_path like "%'.$rid.'%") and is_pay=1'.$insql)->sum('f4');
				if($sumr!=false){
					$danR=$sumr;
				}
			}else{
				$danR=0;
			}
		}else{
			return;
		}
	}

	//  三轨图
	public function Tree3(){
		$this->_checkUser();
		$ji_c = $this->ji_Color();  //级别颜色
		$kd_c = $this->kd_Color();  //是否开通
		$mi_c = $this->Mi_Cheng();  //级别名称

		$fck   =  M ("fck");
		$id  = $_SESSION[C('USER_AUTH_KEY')];
		$myid = $_SESSION[C('USER_AUTH_KEY')];
		$UID = (int) $_GET['ID'];
		if (empty($UID)) $UID = $id;
			$UserID=$_POST['UserID'];
			if (!empty($UserID)){
			if (strlen($UserID)>10 ){
				$this->error( "错误操作！");
				exit;
			}
			//$where = "p_path like '%,". $UID .",%' and (user_id='". $UserID ."' or nickname='". $UserID ."') ";  //帐号的昵称都可以查询
			$where = "p_path like '%,". $UID .",%' and user_id='". $UserID ."' ";
			$field ='*';
			$rs = $fck ->where($where)->field($field)->find();
			//dump($fck->getLastSql());
			//exit;
			if($rs==false){
				$this->error('没有该用户1!');
				exit();
			}else{
				$UID = $rs['id'];
			}
		}
		$_SESSION['showUID'] = $UID;
		$where =array();
		$where['id'] = $UID;
		$field ='*';
		$rs = $fck ->where($where)->field($field)->find();
		if (!$rs){
			$this->error('没有该用户2!');
			exit();
		}else{
			$ID			= $rs['id'];
			$UserID		= $rs['user_id'];
			$NickName	= $rs['nickname'];
			$TreePlace	= $rs['treeplace'];   //区分左右 0为左边,1为右边
			$FatherID	= $rs['father_id'];    //安置人ID
			$isPay		= $rs['is_pay'];		  //是否为正式(开通时为正式)
			$uLevel		= $rs['u_level'];      //级别
			$pPath		= $rs['p_path'];       //自已的路径
			$pLevel		= $rs['p_level'];	  //层数(数字)
			$L			= $rs['l'];
			$R			= $rs['r'];
			$LR			= $rs['lr'];
			$SpareL			= $rs['shangqi_l'];
			$SpareR			= $rs['shangqi_r'];
			$SpareLR			= $rs['shangqi_lr'];
			$benqiL			= $rs['benqi_l'];
			$benqiR			= $rs['benqi_r'];
			$benqiLR			= $rs['benqi_lr'];
		}
		if($myid==$ID)$FatherID=$myid;
		if ($isPay>1) $isPay=1;
		if($rs['is_agent'] > 1){
			$isPay = 2;    //报单中心颜色
		}

		//显示层数
		$uLev = (int) $_GET['uLev'];		//$Lev 记录显示层数
		if (is_numeric($uLev) == false) $uLev = $_SESSION['uLev3'];
		if (is_numeric($uLev) == false) $uLev = 2;
		if ($uLev < 2 || $uLev > 11)    $uLev = 2;
		$_SESSION['uLev3']=$uLev;
		for ($i=1;$i<=$uLev;$i++){
			$Nums=$Nums+pow(3,$i);
		}
		global $TreeArray;
		$TreeArray=array();

		for ($i=0;$i<=$Nums;$i++){
			$TreeArray[$i]="<table border='0' cellpadding='0' cellspacing='1' class='tu_box'><tr><td class='tu_ko'> 空位 </td></tr></table>";
		}
		$bj = "style='background:". $kd_c[$isPay] ."';";  //表格背景色
		$StTab = "<table border='0' cellpadding='0' cellspacing='1' class='tu_box'><tr><td colspan='3' style='background:". $ji_c[$uLevel] .";font-weight:bold;'>";
		$MyYJ = "</td></tr>";
		$MyYJ .= "<tr><td class='tu_l' $bj>$L</td><td class='tu_z' $bj>$R</td><td class='tu_r' $bj>$LR</td></tr>";
		//$MyYJ .= "<tr><td class='tu_l' $bj>$SpareL</td><td class='tu_z' $bj>$SpareR</td><td class='tu_r' $bj>$SpareLR</td></tr>";
		//$MyYJ .= "<tr><td class='tu_l' $bj>$benqiL</td><td class='tu_z' $bj>$benqiR</td><td class='tu_r' $bj>$benqiLR</td></tr>";
		//$MyYJ .= "<tr><td class='tu_l' $bj>$SpareL</td><td class='tu_z' $bj>余</td><td class='tu_r' $bj>$SpareR</td></tr>";
		//$MyYJ .= "<tr><td class='tu_l' $bj>$benqiL</td><td class='tu_z' $bj>新</td><td class='tu_r' $bj>$benqiR</td></tr>";
		$MyYJ .= "</table>";

		$ZiJi   = $StTab."<a href='#'>". $UserID ."</a>". $MyYJ;
		$Str4C0 = "<table  border='0' cellpadding='0' cellspacing='1' class='tu_box'><tr><td class='tu_ko'>";
		$Str4C1 = "<a href='". __URL__ ."/KaiBoLuo/TPL/";
		$Str4C4 = "</td></tr></table>";

		if ($isPay>0){
			$i=pow(3,$uLev);
			$j=($i+1)/2;
 			$TreeArray['1']=$Str4C0.$Str4C1."0/RID/". $ID ."/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
 			$TreeArray[$j]=$Str4C0.$Str4C1."1/RID/". $ID ."/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
 			$TreeArray[$i]=$Str4C0.$Str4C1."2/RID/". $ID ."/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
		}else{
// 			$TreeArray['1']=$Str4C0.$Str4C1."0/RID/". $ID ."/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
			//$TreeArray[$j]=$Str4C0.$Str4C1."1/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
			//$TreeArray[$i]=$Str4C0.$Str4C1."2/FID/".$ID."' target='_self'>点击注册</a>".$Str4
		}

		$wop = '';
		$TreeArray['0']=$ZiJi;
		$this->Tree3_MtKass($UID,0,$pLevel,$uLev,$Str4C0,$Str4C1,$Str4C4,$TreeArray,$Nums);
		$this->Tree3_showTree($uLev,$TreeArray,$wop);

		$fee_rs = M('fee')->field('s10')->find();
		$Level = explode('|',$fee_rs['s10']);
		$this->assign('Level',$Level);
		$this->assign('ColorUA',$ji_c);
		$this->assign('TU_Color',$kd_c);
		$this->assign('TU_MiCheng',$mi_c);
		$this->assign('UID',$UID);
		$this->assign('uLev',$uLev);
		$this->assign('FatherID',$FatherID);
		$this->assign('wop',$wop);
		$this->display();
	}  // end function

	//  三轨图---生成下层会员内容
	private function Tree3_MtKass($FatherID,$iL,$pLevel,$uLev,$Str4C0,$Str4C1,$Str4C4,&$TreeArray,$Nums){
		$ji_c = $this->ji_Color();  //级别颜色
		$kd_c = $this->kd_Color();  //是否开通
		$mi_c = $this->Mi_Cheng();  //级别名称
		if (!empty($FatherID)){
			
			$fck = M('fck');
			$where = array();
			$where = "father_id=". $FatherID ." and p_level-". $pLevel ."<=". $uLev ." And treeplace<3";
			$field = '*';
			$rss = $fck->where($where)->field($field)->order("treeplace asc")->select();
//			dump($rss);
			foreach($rss as $rs){
				if ($rs['treeplace']==0){
					$k=$iL+1;
				}elseif($rs['treeplace']==1){
					$i=($pLevel+$uLev)-$rs['p_level']+1;
					$j=pow(3,$i);
					$k=($j+1)/2+$iL;
				}else{
					$i=($pLevel+$uLev)-$rs['p_level']+1;
					$j=pow(3,$i);
					$k=$j+$iL;
				}

				$i=($pLevel+$uLev)-$rs['p_level'];
				$Uo=$k+1;   //  1线
				$To=pow(3,$i)+$k;  //  3线
				$Yo=($Uo+$To)/2;   //  2线

				$Rid		= $rs['id'];
				$UserID		= $rs['user_id'];
				$NickName	= $rs['nickname'];
				$TreePlace	= $rs['treeplace'];   //区分左右 0为左边,1为右边
				$FatherID	= $rs['father_id'];    //安置人ID
				$isPay		= $rs['is_pay'];		  //是否为正式(开通时为正式)
				$uLevel		= $rs['u_level'];      //级别
				$upLevel	= $rs['p_level'];	  //层数(数字)
				$L			= $rs['l'];
				$R			= $rs['r'];
				$LR			= $rs['lr'];

				$L			= $rs['l'];
				$R			= $rs['r'];
				$LR			= $rs['lr'];
				$SpareL		= $rs['shangqi_l'];
				$SpareR		= $rs['shangqi_r'];
				$SpareLR	= $rs['shangqi_lr'];
				$benqiL		= $rs['benqi_l'];
				$benqiR		= $rs['benqi_r'];
				$benqiLR	= $rs['benqi_lr'];


				if ($isPay>1) $isPay=1;
				if($rs['is_agent'] > 1){
					$isPay = 2;    //报单中心颜色
				}

				$bj = "style='background:". $kd_c[$isPay] ."';";  //表格背景色
				$StTab = "<table border='0' cellpadding='0' cellspacing='1' class='tu_box'><tr><td colspan='3' style='background:". $ji_c[$uLevel] .";font-weight:bold;'>";
				$MyYJ  = "</td></tr>";
				$MyYJ .= "<tr><td class='tu_l' $bj>$L</td><td class='tu_z' $bj>$R</td><td class='tu_r' $bj>$LR</td></tr>";
				//$MyYJ .= "<tr><td class='tu_l' $bj>$SpareL</td><td class='tu_z' $bj>$SpareR</td><td class='tu_r' $bj>$SpareLR</td></tr>";
				//$MyYJ .= "<tr><td class='tu_l' $bj>$benqiL</td><td class='tu_z' $bj>$benqiR</td><td class='tu_r' $bj>$benqiLR</td></tr>";
				$MyYJ .= "</table>";

				$Str=$StTab."<a href='".__URL__."/Tree3/ID/".$Rid."'>".$UserID."</a>".$MyYJ;
				$Str4C2="/RID/". $Rid ."/FID/".$Rid."' target='_self'>点击注册</a>";

				if ($isPay > 0){
					if ($Yo<=$Nums+1 && $i>0){
 						$TreeArray[$Uo]=$Str4C0.$Str4C1."0".$Str4C2.$Str4C4;
 						$TreeArray[$Yo]=$Str4C0.$Str4C1."1".$Str4C2.$Str4C4;
 						$TreeArray[$To]=$Str4C0.$Str4C1."2".$Str4C2.$Str4C4;
					}
				}else{
					if ($Yo<=$Nums+1 && $i>0){
// 					$TreeArray[$Uo]=$Str4C0.$Str4C1."0".$Str4C2.$Str4C4;
					//$TreeArray[$Yo]=$Str4C0.$Str4C1."1".$Str4C2.$Str4C4;
					//$TreeArray[$To]=$Str4C0.$Str4C1."2".$Str4C2.$Str4C4;
					}
				}
				$TreeArray[$k]=$Str;
				if ($upLevel < $pLevel + $uLev){
					$this->Tree3_MtKass($Rid,$k,$pLevel,$uLev,$Str4C0,$Str4C1,$Str4C4,$TreeArray,$Nums);
				}
			}  //end for
		}  //end if
	}  //end function

	// 三轨图----生成表格内容
	private function Tree3_showTree($uLev,$TreeArray,&$wop){
		for ($i=1;$i<=$uLev;$i++){
			$Nums=$Nums+pow(3,$i);
		}
		$arr=array();
		global $arrs;
		$arrs=array();
		for ($i=0;$i<=$Nums;$i++){
			$arr[$i]=$TreeArray[$i];
		}
		$arrs[0][0]=$arr;
		for ($i=1;$i<=$uLev;$i++){
			for ($u = 1 ; $u <= pow(3,($i-1)) ; $u++){
				$yyyo=$arrs[$i-1][$u-1];
				$ta=array();
				$tar=count($yyyo);
				for ($ti=0 ; $ti<$tar ; $ti++){
					$ta[$ti] = $yyyo[$ti+1];
				}
				$to=floor($tar/3)-1;
				$tarr1=array();
				$tarr2=array();
				$tarr3=array();
				for ($tj=0 ; $tj<=$to ; $tj++){
					$tarr1[$tj] = $ta[$tj];
					$tarr2[$tj] = $ta[$to+$tj+1];
					$tarr3[$tj] = $ta[2*$to+$tj+2];
				}
				$sq=($u-1)*3;
				$arrs[$i][$sq] = $tarr1;
				$arrs[$i][$sq+1] = $tarr2;
				$arrs[$i][$sq+2] = $tarr3;
			}
		}
		$wid = '33%';
		$lhe = 30;
		$tps = __ROOT__.'/public/Images/tree/';
		$strL = "<img src='".$tps."t_tree_bottom_l.gif' height='".$lhe."'><img src='".$tps."t_tree_line.gif' width='".$wid."' height='".$lhe."'><img src='".$tps."t_tree_mid.gif' height='".$lhe."' alt='顶层'><img src='".$tps."t_tree_line.gif' width='".$wid."' height='".$lhe."'><img src='".$tps."t_tree_bottom_r.gif' height='".$lhe."'>";
        $wop="";
		for ($i = 0  ;  $i <= $uLev  ;  $i++){
			$wop.="<table width='100%' border='0' cellpadding='1' cellspacing='1'>";
			for ($t = 0  ;  $t <= 1  ;  $t++){
				if ($t != 1 or $i != $uLev){
					$wop.="<tr align='center'>";
					$oop= pow(3,$i)-1;
					for ($j = 0  ;  $j <= $oop ;  $j++){
						$eop=100/pow(3,$i);
						if ($t==1){
							$wop.="<td class='borderno' width='". $eop ."%' valign='top'>";
							$wop.=$strL;
						}else{
							$bcxx=$arrs[$i][$j][0];
							$rp=$i+1;
							$wop.="<td class='borderlrt' width='". $eop ."%' valign='top' title='第" . $rp . "层'>";
							$wop.=$strW;
							$wop.=$bcxx;
							$wop.="</td>";
						}
						$wop.="</td>";
					}
					$wop.="</tr>";
				}
			}
			$wop.="</table>";
		}
		$wop.="</td></tr></table>";
	}


	//  四轨图
	public function Tree4(){
		$this->_checkUser();
		$ji_c = $this->ji_Color();  //级别颜色
		$kd_c = $this->kd_Color();  //是否开通
		$mi_c = $this->Mi_Cheng();  //级别名称

		$fck   =  M ("fck");
		$id  = $_SESSION[C('USER_AUTH_KEY')];
		$UID = (int) $_GET['ID'];
		if (empty($UID)) $UID = $id;
			$UserID=$_POST['UserID'];
			if (!empty($UserID)){
			if (strlen($UserID)>10 ){
				$this->error( "错误操作！");
				exit;
			}
			//$where = "p_path like '%,". $UID .",%' and (user_id='". $UserID ."' or nickname='". $UserID ."') ";  //帐号的昵称都可以查询
			$where = "p_path like '%,". $UID .",%' and user_id='". $UserID ."' ";
			$field ='*';
			$rs = $fck ->where($where)->field($field)->find();
			//dump($fck->getLastSql());
			//exit;
			if($rs==false){
				$this->error('没有该用户1!');
				exit();
			}else{
				$UID = $rs['id'];
			}
		}
		$_SESSION['showUID'] = $UID;
		$where =array();
		$where['id'] = $UID;
		$field ='*';
		$rs = $fck ->where($where)->field($field)->find();
		if (!$rs){
			$this->error('没有该用户2!');
			exit();
		}else{
			$ID			= $rs['id'];
			$UserID		= $rs['user_id'];
			$NickName	= $rs['nickname'];
			$TreePlace	= $rs['treeplace'];   //区分左右 0为左边,1为右边
			$FatherID	= $rs['father_id'];    //安置人ID
			$isPay		= $rs['is_pay'];		  //是否为正式(开通时为正式)
			$uLevel		= $rs['u_level'];      //级别
			$pPath		= $rs['p_path'];       //自已的路径
			$pLevel		= $rs['p_level'];	  //层数(数字)
			$L			= $rs['l'];
			$R			= $rs['r'];
			$LR			= $rs['lr'];
		}
		if ($isPay>1) $isPay=1;
		if($rs['is_agent'] > 1){
			$isPay = 2;    //报单中心颜色
		}

		//显示层数
		$uLev = (int) $_GET['uLev'];		//$Lev 记录显示层数
		$uLev = 1;
		if (is_numeric($uLev) == false) $uLev = $_SESSION['uLev3'];
		if (is_numeric($uLev) == false) $uLev = 1;
		if ($uLev < 1 || $uLev > 11)    $uLev = 1;
		$_SESSION['uLev3']=$uLev;

		for ($i=1;$i<=$uLev;$i++){
			$Nums=$Nums+pow(4,$i);
		}
		global $TreeArray;
		$TreeArray=array();

		for ($i=0;$i<=$Nums;$i++){
			$TreeArray[$i]="<table border='0' cellpadding='0' cellspacing='1' class='tu_box'><tr><td class='tu_ko'> 空位 </td></tr></table>";
		}
		$bj = "style='background:". $kd_c[$isPay] ."';";  //表格背景色
		$StTab = "<table border='0' cellpadding='0' cellspacing='1' class='tu_box'><tr><td colspan='3' style='background:". $ji_c[$uLevel] .";font-weight:bold;'>";
		$MyYJ = "</td></tr>";
		$MyYJ .= "<tr><td class='tu_l' $bj>$L</td><td class='tu_z' $bj>$R</td><td class='tu_r' $bj>$LR</td></tr>";
		//$MyYJ .= "<tr><td class='tu_l' $bj>$SpareL</td><td class='tu_z' $bj>余</td><td class='tu_r' $bj>$SpareR</td></tr>";
		//$MyYJ .= "<tr><td class='tu_l' $bj>$benqiL</td><td class='tu_z' $bj>新</td><td class='tu_r' $bj>$benqiR</td></tr>";
		$MyYJ .= "</table>";

		$ZiJi   = $StTab."<a href='#'>". $UserID ."</a>". $MyYJ;
		$Str4C0 = "<table  border='0' cellpadding='0' cellspacing='1' class='tu_box'><tr><td class='tu_ko'>";
		$Str4C1 = "<a href='". __URL__ ."/KaiBoLuo/TPL/";
		$Str4C4 = "</td></tr></table>";

		if ($isPay>0){
			//$i=pow(4,$uLev);
			//$j=($i+1)/2;
			$TreeArray['1']=$Str4C0.$Str4C1."0/RID/". $ID ."/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
			$TreeArray['2']=$Str4C0.$Str4C1."1/RID/". $ID ."/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
			$TreeArray['3']=$Str4C0.$Str4C1."2/RID/". $ID ."/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
			$TreeArray['4']=$Str4C0.$Str4C1."3/RID/". $ID ."/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
		}else{
			//$TreeArray['1']=$Str4C0.$Str4C1."0/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
			//$TreeArray[$j]=$Str4C0.$Str4C1."1/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
			//$TreeArray[$i]=$Str4C0.$Str4C1."2/FID/".$ID."' target='_self'>点击注册</a>".$Str4
		}

		$wop = '';
		$TreeArray['0']=$ZiJi;
		$this->Tree4_MtKass($UID,0,$pLevel,$uLev,$Str4C0,$Str4C1,$Str4C4,$TreeArray,$Nums);
		$this->Tree4_showTree($uLev,$TreeArray,$wop);

		$Level = explode('|',C("Member_Level"));
		$this->assign('Level',$Level);
		$this->assign('ColorUA',$ji_c);
		$this->assign('TU_Color',$kd_c);
		$this->assign('TU_MiCheng',$mi_c);
		$this->assign('UID',$UID);
		$this->assign('uLev',$uLev);
		$this->assign('FatherID',$FatherID);
		$this->assign('wop',$wop);
		$this->display('Tree4');
	}  // end function

	//  四轨图---生成下层会员内容
	private function Tree4_MtKass($FatherID,$iL,$pLevel,$uLev,$Str4C0,$Str4C1,$Str4C4,&$TreeArray,$Nums){
		$ji_c = $this->ji_Color();  //级别颜色
		$kd_c = $this->kd_Color();  //是否开通
		$mi_c = $this->Mi_Cheng();  //级别名称
		if (!empty($FatherID)){
			$fck = M('fck');
			$where = array();
			$where = "father_id=". $FatherID ." and p_level-". $pLevel ."<=". $uLev ." And treeplace<4 order by treeplace asc";
			$field = '*';
			$rss = $fck->where($where)->field($field)->select();
			//dump($rss);
			foreach($rss as $rs){
				$Rid		= $rs['id'];
				$UserID		= $rs['user_id'];
				$NickName	= $rs['nickname'];
				$TreePlace	= $rs['treeplace'];   //区分左右 0为左边,1为右边
				$FatherID	= $rs['father_id'];    //安置人ID
				$isPay		= $rs['is_pay'];		  //是否为正式(开通时为正式)
				$uLevel		= $rs['u_level'];      //级别
				$upLevel	= $rs['p_level'];	  //层数(数字)
				$L			= $rs['l'];
				$R			= $rs['r'];
				$LR			= $rs['lr'];
				if ($isPay>1) $isPay=1;
				if($rs['is_agent'] > 1){
					$isPay = 2;    //报单中心颜色
				}

				if ($TreePlace == 0){
					$k = 1;
				}elseif ($TreePlace == 1){
					$k = 2;
				}elseif ($TreePlace == 2){
					$k = 3;
				}elseif ($TreePlace == 3){
					$k = 4;
				}


				$bj = "style='background:". $kd_c[$isPay] ."';";  //表格背景色
				$StTab = "<table border='0' cellpadding='0' cellspacing='1' class='tu_box'><tr><td colspan='3' style='background:". $ji_c[$uLevel] .";font-weight:bold;'>";
				$MyYJ  = "</td></tr>";
				$MyYJ .= "<tr><td class='tu_l' $bj>$L</td><td class='tu_z' $bj>$R</td><td class='tu_r' $bj>$LR</td></tr>";
				$MyYJ .= "</table>";

				$Str=$StTab."<a href='".__URL__."/Tree4/ID/".$Rid."'>".$UserID."</a>".$MyYJ;

				$TreeArray[$k]=$Str;
				if ($upLevel < $pLevel + $uLev){
					$this->Tree3_MtKass($Rid,$k,$pLevel,$uLev,$Str4C0,$Str4C1,$Str4C4,$TreeArray,$Nums);
				}
			}  //end for
		}  //end if
	}  //end function

	//  四轨图----生成表格内容
	private function Tree4_showTree($uLev,$TreeArray,&$wop){
		for ($i=1;$i<=$uLev;$i++){
			$Nums=$Nums+pow(4,$i);
		}
		$arr=array();
		global $arrs;
		$arrs=array();
		for ($i=0;$i<=$Nums;$i++){
			$arr[$i]=$TreeArray[$i];
		}
		$arrs[0][0]=$arr;
		for ($i=1;$i<=$uLev;$i++){
			for ($u = 1 ; $u <= pow(4,($i-1)) ; $u++){
				$yyyo=$arrs[$i-1][$u-1];
				$ta=array();
				$tar=count($yyyo);
				for ($ti=0 ; $ti<$tar ; $ti++){
					$ta[$ti] = $yyyo[$ti+1];
				}
				$to=floor($tar/4)-1;
				$tarr1=array();
				$tarr2=array();
				$tarr3=array();
				$tarr4=array();
				for ($tj=0 ; $tj<=$to ; $tj++){
					$tarr1[$tj] = $ta[$tj];
					$tarr2[$tj] = $ta[$to+$tj+1];
					$tarr3[$tj] = $ta[2*$to+$tj+2];
					$tarr4[$tj] = $ta[3*$to+$tj+3];
				}
				$sq=($u-1)*4;
				$arrs[$i][$sq] = $tarr1;
				$arrs[$i][$sq+1] = $tarr2;
				$arrs[$i][$sq+2] = $tarr3;
				$arrs[$i][$sq+3] = $tarr4;
			}
		}
		$wid = '25%';
		$lhe = 8;
		$tps = __ROOT__.'/public/Images/tree4/';

		$strL = "<img src='".$tps."/t_tree_bottom.gif'><img src='".$tps."t_tree_line.gif' width='".$wid."' height='".$lhe."'><img src='".$tps."t_tree_bottom.gif'><img src='".$tps."t_tree_line.gif' width='".$wid."' height='".$lhe."'><img src='".$tps."t_tree_bottom.gif'><img src='".$tps."t_tree_line.gif' width='".$wid."' height='".$lhe."'><img src='".$tps."t_tree_bottom.gif'>";


		//$strL = "<img src='".$tps."t_tree_bottom.gif' height='".$lhe."'><img src='".$tps."t_tree_line.gif' width='".$wid."' height='".$lhe."'><img src='".$tps."t_tree_bottom.gif' height='".$lhe."'><img src='".$tps."t_tree_mid.gif' width='".$wid."' height='".$lhe."'><img src='".$tps."t_tree_line.gif' height='".$lhe."'><img src='".$tps."t_tree_bottom.gif' height='".$lhe."'><img src='".$tps."t_tree_bottom.gif' height='".$lhe."'>";

        $wop="";
		for ($i = 0  ;  $i <= $uLev  ;  $i++){
			$wop.="<table width='100%' border='0' cellpadding='1' cellspacing='1'>";
			for ($t = 0  ;  $t <= 1  ;  $t++){
				if ($t != 1 or $i != $uLev){
					$wop.="<tr align='center'>";
					$oop= pow(4,$i)-1;
					for ($j = 0  ;  $j <= $oop ;  $j++){
						$eop=100/pow(4,$i);
						if ($t==1){
							$wop.="<td class='borderno' width='". $eop ."%' valign='top'>";
							$wop.=$strL;
						}else{
							$bcxx=$arrs[$i][$j][0];
							$rp=$i+1;
							$wop.="<td class='borderlrt' width='". $eop ."%' valign='top' title='第" . $rp . "层'>";
							$wop.=$strW;
							$wop.=$bcxx;
							$wop.="</td>";
						}
						$wop.="</td>";
					}
					$wop.="</tr>";
				}
			}
			$wop.="</table>";
		}
		$wop.="</td></tr></table>";
	}


	//  五轨图
	public function Tree5(){
		$this->_checkUser();
		$ji_c = $this->ji_Color();  //级别颜色
		$kd_c = $this->kd_Color();  //是否开通
		$mi_c = $this->Mi_Cheng();  //级别名称

		$fck   =  M ("fck");
		$id  = $_SESSION[C('USER_AUTH_KEY')];
		$UID = (int) $_GET['ID'];
		if (empty($UID)) $UID = $id;
			$UserID=$_POST['UserID'];
			if (!empty($UserID)){
			if (strlen($UserID)>10 ){
				$this->error( "错误操作！");
				exit;
			}
			//$where = "p_path like '%,". $UID .",%' and (user_id='". $UserID ."' or nickname='". $UserID ."') ";  //帐号的昵称都可以查询
			$where = "p_path like '%,". $UID .",%' and user_id='". $UserID ."' ";
			$field ='*';
			$rs = $fck ->where($where)->field($field)->find();
			//dump($fck->getLastSql());
			//exit;
			if($rs==false){
				$this->error('没有该用户1!');
				exit();
			}else{
				$UID = $rs['id'];
			}
		}
		$_SESSION['showUID'] = $UID;
		$where =array();
		$where['id'] = $UID;
		$field ='*';
		$rs = $fck ->where($where)->field($field)->find();
		if (!$rs){
			$this->error('没有该用户2!');
			exit();
		}else{
			$ID			= $rs['id'];
			$UserID		= $rs['user_id'];
			$NickName	= $rs['nickname'];
			$TreePlace	= $rs['treeplace'];   //区分左右 0为左边,1为右边
			$FatherID	= $rs['father_id'];    //安置人ID
			$isPay		= $rs['is_pay'];		  //是否为正式(开通时为正式)
			$uLevel		= $rs['u_level'];      //级别
			$pPath		= $rs['p_path'];       //自已的路径
			$pLevel		= $rs['p_level'];	  //层数(数字)
			$L			= $rs['l'];
			$R			= $rs['r'];
			$LR			= $rs['lr'];
		}
		if ($isPay>1) $isPay=1;
		if($rs['is_agent'] > 1){
			$isPay = 2;    //报单中心颜色
		}

		//显示层数
		$uLev = (int) $_GET['uLev'];		//$Lev 记录显示层数
		$uLev = 1;
		if (is_numeric($uLev) == false) $uLev = $_SESSION['uLev3'];
		if (is_numeric($uLev) == false) $uLev = 1;
		if ($uLev < 1 || $uLev > 11)    $uLev = 1;
		$_SESSION['uLev3']=$uLev;

		for ($i=1;$i<=$uLev;$i++){
			$Nums=$Nums+pow(5,$i);
		}
		global $TreeArray;
		$TreeArray=array();

		for ($i=0;$i<=$Nums;$i++){
			$TreeArray[$i]="<table border='0' cellpadding='0' cellspacing='1' class='tu_box'><tr><td class='tu_ko'> 空位 </td></tr></table>";
		}
		$bj = "style='background:". $kd_c[$isPay] ."';";  //表格背景色
		$StTab = "<table border='0' cellpadding='0' cellspacing='1' class='tu_box'><tr><td colspan='3' style='background:". $ji_c[$uLevel] .";font-weight:bold;'>";
		$MyYJ = "</td></tr>";
		$MyYJ .= "<tr><td class='tu_l' $bj>$L</td><td class='tu_z' $bj>$R</td><td class='tu_r' $bj>$LR</td></tr>";
		//$MyYJ .= "<tr><td class='tu_l' $bj>$SpareL</td><td class='tu_z' $bj>余</td><td class='tu_r' $bj>$SpareR</td></tr>";
		//$MyYJ .= "<tr><td class='tu_l' $bj>$benqiL</td><td class='tu_z' $bj>新</td><td class='tu_r' $bj>$benqiR</td></tr>";
		$MyYJ .= "</table>";

		$ZiJi   = $StTab."<a href='#'>". $UserID ."</a>". $MyYJ;
		$Str4C0 = "<table  border='0' cellpadding='0' cellspacing='1' class='tu_box'><tr><td class='tu_ko'>";
		$Str4C1 = "<a href='". __URL__ ."/KaiBoLuo/TPL/";
		$Str4C4 = "</td></tr></table>";

		if ($isPay>0){
			$TreeArray['1']=$Str4C0.$Str4C1."0/RID/". $ID ."/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
			$TreeArray['2']=$Str4C0.$Str4C1."1/RID/". $ID ."/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
			$TreeArray['3']=$Str4C0.$Str4C1."2/RID/". $ID ."/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
			$TreeArray['4']=$Str4C0.$Str4C1."3/RID/". $ID ."/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
			$TreeArray['5']=$Str4C0.$Str4C1."4/RID/". $ID ."/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
		}else{
			//$TreeArray['1']=$Str4C0.$Str4C1."0/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
			//$TreeArray[$j]=$Str4C0.$Str4C1."1/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
			//$TreeArray[$i]=$Str4C0.$Str4C1."2/FID/".$ID."' target='_self'>点击注册</a>".$Str4
		}

		$wop = '';
		$TreeArray['0']=$ZiJi;
		$this->Tree5_MtKass($UID,0,$pLevel,$uLev,$Str4C0,$Str4C1,$Str4C4,$TreeArray,$Nums);
		$this->Tree5_showTree($uLev,$TreeArray,$wop);

		$Level = explode('|',C("Member_Level"));
		$this->assign('Level',$Level);
		$this->assign('ColorUA',$ji_c);
		$this->assign('TU_Color',$kd_c);
		$this->assign('TU_MiCheng',$mi_c);
		$this->assign('UID',$UID);
		$this->assign('uLev',$uLev);
		$this->assign('FatherID',$FatherID);
		$this->assign('wop',$wop);
		$this->display('Tree5');
	}  // end function

	//  五轨图---生成下层会员内容
	private function Tree5_MtKass($FatherID,$iL,$pLevel,$uLev,$Str4C0,$Str4C1,$Str4C4,&$TreeArray,$Nums){
		$ji_c = $this->ji_Color();  //级别颜色
		$kd_c = $this->kd_Color();  //是否开通
		$mi_c = $this->Mi_Cheng();  //级别名称
		if (!empty($FatherID)){
			$fck = M('fck');
			$where = array();
			$where = "father_id=". $FatherID ." and p_level-". $pLevel ."<=". $uLev ."  order by treeplace asc";
			$field = '*';
			$rss = $fck->where($where)->field($field)->select();
			//dump($rss);
			foreach($rss as $rs){
				$Rid		= $rs['id'];
				$UserID		= $rs['user_id'];
				$NickName	= $rs['nickname'];
				$TreePlace	= $rs['treeplace'];   //区分左右 0为左边,1为右边
				$FatherID	= $rs['father_id'];    //安置人ID
				$isPay		= $rs['is_pay'];		  //是否为正式(开通时为正式)
				$uLevel		= $rs['u_level'];      //级别
				$upLevel	= $rs['p_level'];	  //层数(数字)
				$L			= $rs['l'];
				$R			= $rs['r'];
				$LR			= $rs['lr'];
				if ($isPay>1) $isPay=1;
				if($rs['is_agent'] > 1){
					$isPay = 2;    //报单中心颜色
				}

				$i=($pLevel+$uLev)-$upLevel;
				if ($TreePlace == 0){
					$k = $tL+1;
				}elseif ($TreePlace == 1){
					$j = 5^$i;
					$k = ($j+1)/2+$tL-1;
				}elseif ($TreePlace == 2){
					$i = $i+1;
					$j = 5^$i;
					$k = ($j+1)/2+$tL+1;
				}elseif ($TreePlace == 3){
					$i = $i+1;
					$j = 5^$i;
					$k = ($j+1)/2+$tL+2;
				}elseif ($TreePlace == 4){
					$i = $i+1;
					$j = 5^$i;
					$k = $j+$tL+1;
				}

				$bj = "style='background:". $kd_c[$isPay] ."';";  //表格背景色
				$StTab = "<table border='0' cellpadding='0' cellspacing='1' class='tu_box'><tr><td colspan='3' style='background:". $ji_c[$uLevel] .";font-weight:bold;'>";
				$MyYJ  = "</td></tr>";
				$MyYJ .= "<tr><td class='tu_l' $bj>$L</td><td class='tu_z' $bj>$R</td><td class='tu_r' $bj>$LR</td></tr>";
				$MyYJ .= "</table>";

				$Str=$StTab."<a href='".__URL__."/Tree5/ID/".$Rid."'>".$UserID."</a>".$MyYJ;

				$TreeArray[$k]=$Str;
				if ($upLevel < $pLevel + $uLev){
					$this->Tree3_MtKass($Rid,$k,$pLevel,$uLev,$Str4C0,$Str4C1,$Str4C4,$TreeArray,$Nums);
				}
			}  //end for
		}  //end if
	}  //end function

	//  五轨图----生成表格内容
	private function Tree5_showTree($uLev,$TreeArray,&$wop){
		for ($i=1;$i<=$uLev;$i++){
			$Nums=$Nums+pow(5,$i);
		}
		$arr=array();
		global $arrs;
		$arrs=array();
		for ($i=0;$i<=$Nums;$i++){
			$arr[$i]=$TreeArray[$i];
		}
		$arrs[0][0]=$arr;
		for ($i=1;$i<=$uLev;$i++){
			for ($u = 1 ; $u <= pow(5,($i-1)) ; $u++){
				$yyyo=$arrs[$i-1][$u-1];
				$ta=array();
				$tar=count($yyyo);
				for ($ti=0 ; $ti<$tar ; $ti++){
					$ta[$ti] = $yyyo[$ti+1];
				}
				$to=floor($tar/5)-1;
				$tarr1=array();
				$tarr2=array();
				$tarr3=array();
				$tarr4=array();
				$tarr5=array();
				for ($tj=0 ; $tj<=$to ; $tj++){
					$tarr1[$tj] = $ta[$tj];
					$tarr2[$tj] = $ta[$to+$tj+1];
					$tarr3[$tj] = $ta[2*$to+$tj+2];
					$tarr4[$tj] = $ta[3*$to+$tj+3];
					$tarr5[$tj] = $ta[4*$to+$tj+4];
				}
				$sq=($u-1)*5;
				$arrs[$i][$sq] = $tarr1;
				$arrs[$i][$sq+1] = $tarr2;
				$arrs[$i][$sq+2] = $tarr3;
				$arrs[$i][$sq+3] = $tarr4;
				$arrs[$i][$sq+4] = $tarr5;
			}
		}
		$wid = '20%';
		$lhe = 8;
		$tps = __ROOT__.'/public/Images/Tree4/';

		$strL = "<img src='".$tps."/t_tree_bottom.gif'><img src='".$tps."t_tree_line.gif' width='".$wid."' height='".$lhe."'><img src='".$tps."t_tree_bottom.gif'><img src='".$tps."t_tree_line.gif' width='".$wid."' height='".$lhe."'><img src='".$tps."t_tree_mid.gif'><img src='".$tps."t_tree_line.gif' width='".$wid."' height='".$lhe."'><img src='".$tps."t_tree_bottom.gif'><img src='".$tps."t_tree_line.gif' width='".$wid."' height='".$lhe."'><img src='".$tps."t_tree_bottom.gif'>";

        $wop="";
		for ($i = 0  ;  $i <= $uLev  ;  $i++){
			$wop.="<table width='100%' border='0' cellpadding='1' cellspacing='1'>";
			for ($t = 0  ;  $t <= 1  ;  $t++){
				if ($t != 1 or $i != $uLev){
					$wop.="<tr align='center'>";
					$oop= pow(5,$i)-1;
					for ($j = 0  ;  $j <= $oop ;  $j++){
						$eop=100/pow(5,$i);
						if ($t==1){
							$wop.="<td class='borderno' width='". $eop ."%' valign='top'>";
							$wop.=$strL;
						}else{
							$bcxx=$arrs[$i][$j][0];
							$rp=$i+1;
							$wop.="<td class='borderlrt' width='". $eop ."%' valign='top' title='第" . $rp . "层'>";
							$wop.=$strW;
							$wop.=$bcxx;
							$wop.="</td>";
						}
						$wop.="</td>";
					}
					$wop.="</tr>";
				}
			}
			$wop.="</table>";
		}
		$wop.="</td></tr></table>";
	}




	//  直角图
	public function Tree6(){
		$this->_checkUser();
		$ji_c = $this->ji_Color();  //级别颜色
		$kd_c = $this->kd_Color();  //是否开通
		$mi_c = $this->Mi_Cheng();  //级别名称

		$fck   =  M ("fck");
		$id  = $_SESSION[C('USER_AUTH_KEY')];
		$UID = (int) $_GET['ID'];
		if (empty($UID)) $UID = $id;
			$UserID=$_POST['UserID'];
			if (!empty($UserID)){
			if (strlen($UserID)>10 ){
				$this->error( "错误操作！");
				exit;
			}
			//$where = "p_path like '%,". $UID .",%' and (user_id='". $UserID ."' or nickname='". $UserID ."') ";  //帐号的昵称都可以查询
			$where = "p_path like '%,". $UID .",%' and user_id='". $UserID ."' ";
			$field ='*';
			$rs = $fck ->where($where)->field($field)->find();
			if($rs==false){
				$this->error('没有该用户1!');
				exit();
			}else{
				$UID = $rs['id'];
			}
		}
		$_SESSION['showUID'] = $UID;
		$where =array();
		$where['id'] = $UID;
		$field ='*';
		$rs = $fck ->where($where)->field($field)->find();
		if (!$rs){
			$this->error('没有该用户2!');
			exit();
		}else{
			$ID			= $rs['id'];
			$UserID		= $rs['user_id'];
			$NickName	= $rs['nickname'];
			$TreePlace	= $rs['treeplace'];   //区分左右 0为左边,1为右边
			$FatherID	= $rs['father_id'];    //安置人ID
			$isPay		= $rs['is_pay'];		  //是否为正式(开通时为正式)
			$uLevel		= $rs['u_level'];      //级别
			$pPath		= $rs['p_path'];       //自已的路径
			$pLevel		= $rs['p_level'];	  //层数(数字)
			$L			= $rs['l'];
			$R			= $rs['r'];
			$benqiL		= $rs['benqi_l'];//本期新增
			$benqiR		= $rs['benqi_r'];
			$SpareL		= $rs['shangqi_l'];//上期剩余
			$SpareR		= $rs['shangqi_r'];

		}
		if ($isPay>1) $isPay=1;
		if($rs['is_agent'] > 1){
			$isPay = 2;    //报单中心颜色
		}

		global $TreeArray;
		$TreeArray=array();

		for ($i=0;$i<=10;$i++){
			$TreeArray[$i]="<table border='0' cellpadding='0' cellspacing='1' class='tu_box'><tr><td class='tu_ko'> 空位 </td></tr></table>";
		}
		$bj = "style='background:". $kd_c[$isPay] ."';";  //表格背景色
		$StTab = "<table border='0' cellpadding='0' cellspacing='1' class='tu_box'><tr><td colspan='3' style='background:". $ji_c[$uLevel] .";font-weight:bold;'>";
		$MyYJ = "</td></tr>";
		$MyYJ .= "<tr><td class='tu_l' $bj>$L</td><td class='tu_z' $bj>总</td><td class='tu_r' $bj>$R</td></tr>";
		$MyYJ .= "<tr><td class='tu_l' $bj>$SpareL</td><td class='tu_z' $bj>余</td><td class='tu_r' $bj>$SpareR</td></tr>";
		$MyYJ .= "<tr><td class='tu_l' $bj>$benqiL</td><td class='tu_z' $bj>新</td><td class='tu_r' $bj>$benqiR</td></tr>";
		$MyYJ .= "</table>";

		$ZiJi   = $StTab."<a href='#'>". $UserID ."</a>". $MyYJ;
		$Str4C0 = "<table  border='0' cellpadding='0' cellspacing='1' class='tu_box'><tr><td class='tu_ko'>";
		$Str4C1 = "<a href='". __URL__ ."/KaiBoLuo/TPL/";
		$Str4C4 = "</td></tr></table>";

		$wop = array();
		if ($isPay>0){
			$wop['1']=$Str4C0.$Str4C1."0/RID/". $ID ."/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
			$wop['2']=$Str4C0.$Str4C1."1/RID/". $ID ."/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
		}else{
			//$wop['1']=$Str4C0.$Str4C1."0/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
			//$wop['2']=$Str4C0.$Str4C1."1/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
		}

		$TreeArray[0] = $ZiJi;
		$Zid = 0;
		$this->Tree6_MtKass($ID,0,$TreeArray,1,$Zid);
		if($Zid > 0){
			$Cip = $Zid;
			$Zid = 0;
			$this->Tree6_MtKass($Cip,0,$TreeArray,2,$Zid);
			if($Zid > 0){
				$Vip = $Zid;
				$Zid = 0;
				$this->Tree6_MtKass($Vip,0,$TreeArray,3,$Zid);
				$this->Tree6_MtKass($Vip,1,$TreeArray,4,$Zid);
			}
			$this->Tree6_MtKass($Cip,1,$TreeArray,5,$Zid);
			if($Zid > 0){
				$Vip = $Zid;
				$Zid = 0;
				$this->Tree6_MtKass($Vip,0,$TreeArray,6,$Zid);
				$this->Tree6_MtKass($Vip,1,$TreeArray,7,$Zid);
			}
		}

		$this->Tree6_MtKass($ID,1,$TreeArray,8,$Zid);
		if($Zid > 0){
			$Cip = $Zid;
			$this->Tree6_MtKass($Cip,0,$TreeArray,9,$Zid);
			$this->Tree6_MtKass($Cip,1,$TreeArray,10,$Zid);
		}

		$Level = explode('|',C("Member_Level"));
		$this->assign('Level',$Level);
		$this->assign('ColorUA',$ji_c);
		$this->assign('TU_Color',$kd_c);
		$this->assign('TU_MiCheng',$mi_c);
		$this->assign('UID',$UID);
		$this->assign('uLev',$uLev);
		$this->assign('FatherID',$FatherID);
		$this->assign('wop',$TreeArray);
		$this->display('Tree6');
	}  // end function

	//  直角图---生成下层会员内容
	private function Tree6_MtKass($Pid,$LR,&$TreeArray,$Trr,&$Zid){
		$ji_c = $this->ji_Color();  //级别颜色
		$kd_c = $this->kd_Color();  //是否开通
		$mi_c = $this->Mi_Cheng();  //级别名称

		$fck = M('fck');
		$where = array();
		$where = "father_id=". $Pid ." And treeplace=".$LR." and treeplace<2";
		$field = '*';
        $rsc = $fck ->where($where)->field($field)->find();
		if($rsc){
			$ID			= $rsc['id'];
			$Zid        = $rsc['id'];
			$UserID		= $rsc['user_id'];
			$NickName	= $rsc['nickname'];
			//$TreePlace	= $rsc['treeplace'];   //区分左右 0为左边,1为右边
			//$FatherID	= $rsc['father_id'];    //安置人ID
			$isPay		= $rsc['is_pay'];		  //是否为正式(开通时为正式)
			$uLevel		= $rsc['u_level'];      //级别
			//$pPath		= $rsc['p_path'];       //自已的路径
			//$pLevel		= $rsc['p_level'];	  //层数(数字)
			$L			= $rsc['l'];
			$R			= $rsc['r'];
			$benqiL		= $rsc['benqi_l'];//本期新增
			$benqiR		= $rsc['benqi_r'];
			$SpareL		= $rsc['shangqi_l'];//上期剩余
			$SpareR		= $rsc['shangqi_r'];

			$bj = "style='background:". $kd_c[$isPay] ."';";  //表格背景色
			$StTab = "<table border='0' cellpadding='0' cellspacing='1' class='tu_box'><tr><td colspan='3' style='background:". $ji_c[$uLevel] .";font-weight:bold;'>";
			$MyYJ = "</td></tr>";
			$MyYJ .= "<tr><td class='tu_l' $bj>$L</td><td class='tu_z' $bj>总</td><td class='tu_r' $bj>$R</td></tr>";
			$MyYJ .= "<tr><td class='tu_l' $bj>$SpareL</td><td class='tu_z' $bj>余</td><td class='tu_r' $bj>$SpareR</td></tr>";
			$MyYJ .= "<tr><td class='tu_l' $bj>$benqiL</td><td class='tu_z' $bj>新{$this->Trr}</td><td class='tu_r' $bj>$benqiR</td></tr>";
			$MyYJ .= "</table>";
			$Tree = $StTab."<a href='".__URL__."/Tree6/ID/".$ID."'>". $UserID ."</a>". $MyYJ;

			$TreeArray[$Trr] = $Tree;

		}else{
			$Zid=0;
			$Str4C0 = "<table  border='0' cellpadding='0' cellspacing='1' class='tu_box'><tr><td class='tu_ko'>";
			$Str4C1 = "<a href='". __URL__ ."/KaiBoLuo/TPL/";
			$Str4C4 = "</td></tr></table>";
			$Tree = $Str4C0.$Str4C1.$LR."/RID/". $Pid ."/FID/".$Pid."' target='_self'>点击注册{$this->Trr}</a>".$Str4C4;
			$TreeArray[$Trr] = $Tree;

		} //end if($rsc)
	}  //end function

	//推荐图
	public function TreeAjax() {
		$this->_checkUser();

		$fck = M("fck");
		
		$fee_s = M('fee')->field('s9,s10')->find();
		$s9 = $fee_s['s9'];
		$s9 = explode('|',$s9);
		$s10 = $fee_s['s10'];
		$s10 = explode('|',$s10);
		$this->assign('s9',$s9);
		$this->assign('s10',$s10);

		$tt = $this->pb_img();
		$treemg1 = $tt[1];
		// print_r($treemg1);die;
		$treemg2 = $tt[2];
		$treemg3 = $tt[3];

		$jieimg1 = $tt[4];
		$jieimg2 = $tt[5];
		$jieimg3 = $tt[6];
		$jieimg4 = $tt[7];

		$openimg1 = $tt[8];
		$openimg2 = $tt[9];


		$ID  = (int) $_GET['UID'];
		$Mmid=$_SESSION[C('USER_AUTH_KEY')];
		$this->assign('Mmid', $Mmid);
		if (empty($ID))$ID = $_SESSION[C('USER_AUTH_KEY')];

		if (!is_numeric($ID) || strlen($ID) > 20 ) $ID = $_SESSION[C('USER_AUTH_KEY')];

		$UserID = $_POST['UserID'];
		if (strlen($UserID) > 20 ){
			$this->error( '错误操作！');
			exit;
		}
		if (!empty($UserID)){
			$fwhere = "(re_path like '%,".$Mmid.",%' or id = ".$Mmid.") and user_id='". $UserID ."'";
			$frs = $fck->where($fwhere)->field('id')->find();

			if (!$frs){
				$this->error('没有找到该用户！');
				exit;
			}else{
				$ID = $frs['id'];
			}
		}

		$id =  $_SESSION[C('USER_AUTH_KEY')];

		$where = array();
		$where['id'] = $ID;
		$where['_string'] = "(re_path like '%,".$Mmid.",%' or id = ".$Mmid.")";
		$rs = $fck->where($where)->find();
		if (!$rs){
			$this->error('没有找到该用户！');
			exit;
		}else{
			$UID		= $rs['id'];
			$UserID		= $rs['user_id'];
			$username	= $rs['user_name'];
			$NickName	= $rs['nickname'];
			$FatherID	= $rs['father_id'];
			$FatherName	= $rs['father_name'];
			$ReID		= $rs['re_id'];
			$ReName		= $rs['re_name'];
			$isPay		= $rs['is_pay'];
			$isAgent	= $rs['is_agent'];
			$isLock		= $rs['is_lock'];
			$uLevel		= $rs['u_level'];
			$grtLevel		= $rs['get_level'];
			$NanGua		= 'aappleeva';
			$ReNUMS		= $rs['re_nums'];
			$QiCheng_l	= $rs['l'];
			$QiCheng_r  = $rs['r'];
			$to_l	= $rs['today_l'];
			$ro_r  = $rs['today_r'];

		}
		
		$all_nn = $fck->where('re_path like "%,'.$UID.',%" and is_pay=1')->count();
		$this->assign('all_nn', $all_nn);

		$fee = M ('fee');
		$fee_rs =$fee->field('s10,s3')->find();
		$Level =explode('|',$fee_rs['s10']);
		$s3 = explode('|',$fee_rs['s3']);
		$uLe    = $uLevel-1;

		$zyj = $QiCheng_l+$QiCheng_r;
		$to_zyj = $to_l + $ro_r;

		$myIMG = "";
		$myName = "";
		$myTabN = "";
		if($isAgent>=2){
			$myIMG = $treemg1;
		}else{
			$myIMG = $treemg2;
		}
		$HYJJ = '';
		$this->_levelConfirm($HYJJ,1);
		if($rs['is_pay']>0){
			$r="$HYJJ[$uLevel]";
		}else{
			$r="未激活";	
		}
		// print_r($r);die;
		//$LE = $HYJJ[$zLevel];

		$myName = $UserID."(".$username.") [".$r."]"."[直推:".$rs['re_nums']."]"."[团队:".$all_nn."]";
		$myTabN = "m".$UID;

		$myStr = '<img name="img'.$UID.'" src="'.$myIMG.'" align="absmiddle"> '.$myName;

		$this->assign('myStr', $myStr);
		$this->assign('myTabN', $myTabN);
		$this->assign('ID', $ID);

		$this->assign('zyj', $zyj);
		$this->assign('to_zyj', $to_zyj);

		$z_tree = array();

		//子网络
		$rwhere 	= array();
		$rwhere['re_id']	= $ID;

		$z_count = $fck->where($rwhere)->count();//人数
		

		$trs = $fck->where($rwhere)->order('is_pay desc,pdt asc')->select();
		$zz = 1;
		foreach($trs as $rss){
			
			$rssid = $rss['id'];
			$rsuserid = $rss['user_id'];
			$nickname = $rss['nickname'];
			$rusername = $rss['user_name'];
			$rsagent = $rss['is_agent'];
			$rslv = $rss['u_level'];
			$getrslv = $rss['get_level'];
			$z_rslv = $rslv-1;
			$rspay = $rss['is_pay'];
			$z_function = "";
			$z_myTabN = "m".$rssid;
			$oz_TabNN = "img".$rssid;
			$oz_img = "";
			$l_pp = ",";
			$zzz_count = $fck->where('re_id='.$rssid)->count();//人数
			if($zzz_count>0){
				if($zz==$z_count){
					$l_pp = $l_pp."1,";
					$z_img = $jieimg1;
					$z_function = "openmm('".$z_myTabN."','".$oz_TabNN."','".$rssid."','1','".$l_pp."')";
					$oz_img = $openimg1;
				}else{
					$z_img = $jieimg2;
					$z_function = "openmm('".$z_myTabN."','".$oz_TabNN."','".$rssid."','1','".$l_pp."')";
					$oz_img = $openimg2;
				}
			}else{
				if($zz==$z_count){
					$z_img = $jieimg3;
				}else{
					$z_img = $jieimg4;
				}
			}
			if($uLevel>=9){
				$z_us_img = $treemg1;
			}else{
				if($rspay>0){
					$z_us_img = $treemg2;
				}else{
					$z_us_img = $treemg3;
				}
			}

			$cf_mm = $this->cf_img(1);
			$all_nn2 = $fck->where('re_path like "%,'.$rssid.',%" and is_pay=1 ')->count();
			$HYJJ = '';
			$this->_levelConfirm($HYJJ,1);
			if($rss['is_pay']>0){
			$r=$HYJJ[$rslv];
			}else{
				$r="未激活";	
			}
			$z_myName = $rsuserid."(".$rusername.") [".$r."]"."[直推:".$rss['re_nums']."]"."[团队:".$all_nn2."]";

			$z_tree[$zz][0] = '<img id="'.$oz_TabNN.'" src="'.$z_img.'" align="absmiddle" onclick="'.$z_function.'">';
			$z_tree[$zz][0].= '<img id="fg'.$rssid.'" src="'.$z_us_img.'" align="absmiddle"> ';
			$z_tree[$zz][0].= $z_myName;
			if(!empty($oz_img)){
				$z_tree[$zz][0].= '<img id="o'.$oz_TabNN.'" src="'.$oz_img.'" align="absmiddle" style="display:none;">';
			}
			$z_tree[$zz][1] = $z_myTabN;
			$z_tree[$zz][2] = $cf_mm;
			$zz++;
		}
		$this->assign('z_tree', $z_tree);

		$this->display();
	}

	public function ajax_tree_m(){
		$this->_checkUser();

		$fck = M("fck");

		$tt = $this->pb_img();
		$treemg1 = $tt[1];
		$treemg2 = $tt[2];
		$treemg3 = $tt[3];

		$jieimg1 = $tt[4];
		$jieimg2 = $tt[5];
		$jieimg3 = $tt[6];
		$jieimg4 = $tt[7];

		$openimg1 = $tt[8];
		$openimg2 = $tt[9];

		$fee = M ('fee');
		$fee_rs =$fee->field('s10')->find();
		$Level =explode('|',$fee_rs['s10']);

		$reid = (int)$_GET['reid'];
		$opnum = (int)$_GET['nn'];
		$l_path = trim($_GET['pp']);
		$n_path = $l_path;
		if($opnum<1){
			$opnum = 1;
		}
		
	$ttt_mm = $opnum+1;
		$rwhere 	= array();
		$rwhere['re_id']	= $reid;
		$z_count = $fck->where($rwhere)->count();//人数
	
		$trs = $fck->where($rwhere)->order('is_pay desc,pdt asc')->select();
		$zz = 1;
		$z_tree = array();

		
		

		foreach($trs as $rss){
		
			
			$rssid = $rss['id'];
			$rsuserid = $rss['user_id'];
			$nickname = $rss['nickname'];
			$rusername = $rss['user_name'];
			$rsagent = $rss['is_agent'];
			$rslv = $rss['u_level'];
			$getrslv = $rss['get_level'];
			$z_rslv = $rslv-1;
			$rspay = $rss['is_pay'];
			$z_function = "";
			$z_myTabN = "m".$rssid;
			$oz_TabNN = "img".$rssid;
			$oz_img = "";
			$zzz_count = $fck->where('re_id='.$rssid)->count();//人数
			if($zzz_count>0){
				if($zz==$z_count){
					$n_path = $n_path.$ttt_mm.",";
					$z_img = $jieimg1;
					$z_function = "openmm('".$z_myTabN."','".$oz_TabNN."','".$rssid."','".$ttt_mm."','".$n_path."')";
					$oz_img = $openimg1;
				}else{
					$z_img = $jieimg2;
					$z_function = "openmm('".$z_myTabN."','".$oz_TabNN."','".$rssid."','".$ttt_mm."','".$n_path."')";
					$oz_img = $openimg2;
				}
			}else{
				if($zz==$z_count){
					$z_img = $jieimg3;
				}else{
					$z_img = $jieimg4;
				}
			}
			if($rsagent>=2){
				$z_us_img = $treemg1;
			}else{
				if($rspay>0){
					$z_us_img = $treemg2;
				}else{
					$z_us_img = $treemg3;
				}
			}

			$cf_mm = $this->cf_img($opnum,$n_path);

			$HYJJ = '';
			$this->_levelConfirm($HYJJ,1);
			$z_myName = $rsuserid."(".$rusername.") [".$HYJJ[$rslv]."]";


			$z_tree[$zz][0] = '<img id="'.$oz_TabNN.'" src="'.$z_img.'" align="absmiddle" onclick="'.$z_function.'">';
			$z_tree[$zz][0].= '<img id="fg'.$rssid.'" src="'.$z_us_img.'" align="absmiddle"> ';
			$z_tree[$zz][0].= $z_myName;
			if(!empty($oz_img)){
				$z_tree[$zz][0].= '<img id="o'.$oz_TabNN.'" src="'.$oz_img.'" align="absmiddle" style="display:none;">';
			}
			$z_tree[$zz][1] = $z_myTabN;
			$z_tree[$zz][2] = $cf_mm;
			$zz++;
		}
		$zzz_str = "";
		foreach($z_tree as $zzzz){

			$ttt_nnn = $this->cf_img($ttt_mm,$n_path);
			$zzz_str .= '<p>'.$zzzz[2].$zzzz[0].'</p>'.
					'<table width="100%" border="0" cellspacing="0" cellpadding="0" id="'.$zzzz[1].'" class="treep2">' .
					'<tr><td id="'.$zzzz[1].'_tree">'.$ttt_nnn.'<img src="'.__PUBLIC__.'/Images/loading2.gif" align="absmiddle"></td>' .
					'</tr></table>';

		}
		$this->assign('zzz_str',$zzz_str);
		$this->display();
		exit;

	}
	
	//图
	public function TreeAjaxb() {
		$this->_checkUser();

		$fck = M("fck");

		$tt = $this->pb_img();
		$treemg1 = $tt[1];
		$treemg2 = $tt[2];
		$treemg3 = $tt[3];

		$jieimg1 = $tt[4];
		$jieimg2 = $tt[5];
		$jieimg3 = $tt[6];
		$jieimg4 = $tt[7];

		$openimg1 = $tt[8];
		$openimg2 = $tt[9];


		$ID  = (int) $_GET['UID'];
		if (empty($ID))$ID = $_SESSION[C('USER_AUTH_KEY')];

		if (!is_numeric($ID) || strlen($ID) > 20 ) $ID = $_SESSION[C('USER_AUTH_KEY')];

		$UserID = $_POST['UserID'];
		if (strlen($UserID) > 20 ){
			$this->error( '错误操作！');
			exit;
		}
		if (!empty($UserID)){
			$fwhere = "user_id='$UserID'";
			$frs = $fck->where($fwhere)->field('id')->find();

			if (!$frs){
				$this->error('没有找到该用户！');
				exit;
			}else{
				$ID = $frs['id'];
			}
		}
		$id =  $_SESSION[C('USER_AUTH_KEY')];

		$where = array();
		$where['id'] = $ID;
		$rs = $fck->where($where)->find();
		if (!$rs){
			$this->error('没有找到该用户！');
			exit;
		}else{
			$UID		= $rs['id'];
			$UserID		= $rs['user_id'];
			$username	= $rs['user_name'];
			$NickName	= $rs['nickname'];
			$FatherID	= $rs['father_id'];
			$FatherName	= $rs['father_name'];
			$ReID		= $rs['re_id'];
			$ReName		= $rs['re_name'];
			$isPay		= $rs['is_pay'];
			$isAgent	= $rs['is_agent'];
			$isLock		= $rs['is_lock'];
			$uLevel		= $rs['u_level'];
			$treeplace	= $rs['treeplace'];
			$NanGua		= 'aappleeva';
			$ReNUMS		= $rs['re_nums'];
			$QiCheng_l	= $rs['l'];
			$QiCheng_r  = $rs['r'];
			$to_l	= $rs['today_l'];
			$ro_r  = $rs['today_r'];

		}

		$fee = M ('fee');
		$fee_rs =$fee->field('s10,s3')->find();
		$Level =explode('|',$fee_rs['s10']);
		$s3 = explode('|',$fee_rs['s3']);
		$uLe    = $uLevel-1;

		$zyj = $QiCheng_l+$QiCheng_r;
		$to_zyj = $to_l + $ro_r;

		$myIMG = "";
		$myName = "";
		$myTabN = "";
		if($isAgent>=2){
			$myIMG = $treemg1;
		}else{
			$myIMG = $treemg2;
		}
		$HYJJ = '';
		$this->_levelConfirm($HYJJ,1);
		//$LE = $HYJJ[$zLevel];
		
		//部门
		$bm_l = $this->lk_treep();

		$myName = $UserID."(".$username.") [".$bm_l[$treeplace]."]";
		$myTabN = "m".$UID;

		$myStr = '<img name="img'.$UID.'" src="'.$myIMG.'" align="absmiddle"> '.$myName;

		$this->assign('myStr', $myStr);
		$this->assign('myTabN', $myTabN);
		$this->assign('ID', $ID);

		$this->assign('zyj', $zyj);
		$this->assign('to_zyj', $to_zyj);

		$z_tree = array();

		//子网络
		$rwhere = array();
		$rwhere['father_id'] = $ID;

		$z_count = $fck->where($rwhere)->count();//人数

		$trs = $fck->where($rwhere)->order('treeplace asc,is_pay desc,pdt asc')->select();
		$zz = 1;
		foreach($trs as $rss){
			$rssid = $rss['id'];
			$rsuserid = $rss['user_id'];
			$nickname = $rss['nickname'];
			$rusername = $rss['user_name'];
			$rsagent = $rss['is_agent'];
			$rtreep = $rss['treeplace'];
			$rslv = $rss['u_level'];
			$z_rslv = $rslv-1;
			$rspay = $rss['is_pay'];
			$z_function = "";
			$z_myTabN = "m".$rssid;
			$oz_TabNN = "img".$rssid;
			$oz_img = "";
			$l_pp = ",";
			$zzz_count = $fck->where('father_id='.$rssid)->count();//人数
			if($zzz_count>0){
				if($zz==$z_count){
					$l_pp = $l_pp."1,";
					$z_img = $jieimg1;
					$z_function = "openmm('".$z_myTabN."','".$oz_TabNN."','".$rssid."','1','".$l_pp."')";
					$oz_img = $openimg1;
				}else{
					$z_img = $jieimg2;
					$z_function = "openmm('".$z_myTabN."','".$oz_TabNN."','".$rssid."','1','".$l_pp."')";
					$oz_img = $openimg2;
				}
			}else{
				if($zz==$z_count){
					$z_img = $jieimg3;
				}else{
					$z_img = $jieimg4;
				}
			}
			if($rsagent>=2){
				$z_us_img = $treemg1;
			}else{
				if($rspay>0){
					$z_us_img = $treemg2;
				}else{
					$z_us_img = $treemg3;
				}
			}

			$cf_mm = $this->cf_img(1);

			$HYJJ = '';
			$this->_levelConfirm($HYJJ,1);
			$z_myName = $rsuserid."(".$rusername.") [".$bm_l[$rtreep]."]";

			$z_tree[$zz][0] = '<img id="'.$oz_TabNN.'" src="'.$z_img.'" align="absmiddle" onclick="'.$z_function.'">';
			$z_tree[$zz][0].= '<img id="fg'.$rssid.'" src="'.$z_us_img.'" align="absmiddle"> ';
			$z_tree[$zz][0].= $z_myName;
			if(!empty($oz_img)){
				$z_tree[$zz][0].= '<img id="o'.$oz_TabNN.'" src="'.$oz_img.'" align="absmiddle" style="display:none;">';
			}
			$z_tree[$zz][1] = $z_myTabN;
			$z_tree[$zz][2] = $cf_mm;
			$zz++;
		}
		$this->assign('z_tree', $z_tree);

		$this->display();
	}

	public function ajax_tree_mb(){
		$this->_checkUser();

		$fck = M("fck");

		$tt = $this->pb_img();
		$treemg1 = $tt[1];
		$treemg2 = $tt[2];
		$treemg3 = $tt[3];

		$jieimg1 = $tt[4];
		$jieimg2 = $tt[5];
		$jieimg3 = $tt[6];
		$jieimg4 = $tt[7];

		$openimg1 = $tt[8];
		$openimg2 = $tt[9];
		
		//部门
		$bm_l = $this->lk_treep();

		$fee = M ('fee');
		$fee_rs =$fee->field('s10')->find();
		$Level =explode('|',$fee_rs['s10']);

		$reid = $_GET['reid'];
		$opnum = (int)$_GET['nn'];
		$l_path = trim($_GET['pp']);
		$n_path = $l_path;
		if($opnum<1){
			$opnum = 1;
		}
		$ttt_mm = $opnum+1;

		$rwhere 	= array();
		$rwhere['father_id']	= $reid;
		$z_count = $fck->where($rwhere)->count();//人数

		$trs = $fck->where($rwhere)->order('treeplace asc,is_pay desc,pdt asc')->select();
		$zz = 1;
		$z_tree = array();
		foreach($trs as $rss){
			$rssid = $rss['id'];
			$rsuserid = $rss['user_id'];
			$nickname = $rss['nickname'];
			$rusername = $rss['user_name'];
			$rsagent = $rss['is_agent'];
			$rtreep = $rss['treeplace'];
			$rslv = $rss['u_level'];
			$z_rslv = $rslv-1;
			$rspay = $rss['is_pay'];
			$z_function = "";
			$z_myTabN = "m".$rssid;
			$oz_TabNN = "img".$rssid;
			$oz_img = "";
			$zzz_count = $fck->where('father_id='.$rssid)->count();//人数
			if($zzz_count>0){
				if($zz==$z_count){
					$n_path = $n_path.$ttt_mm.",";
					$z_img = $jieimg1;
					$z_function = "openmm('".$z_myTabN."','".$oz_TabNN."','".$rssid."','".$ttt_mm."','".$n_path."')";
					$oz_img = $openimg1;
				}else{
					$z_img = $jieimg2;
					$z_function = "openmm('".$z_myTabN."','".$oz_TabNN."','".$rssid."','".$ttt_mm."','".$n_path."')";
					$oz_img = $openimg2;
				}
			}else{
				if($zz==$z_count){
					$z_img = $jieimg3;
				}else{
					$z_img = $jieimg4;
				}
			}
			if($rsagent>=2){
				$z_us_img = $treemg1;
			}else{
				if($rspay>0){
					$z_us_img = $treemg2;
				}else{
					$z_us_img = $treemg3;
				}
			}

			$cf_mm = $this->cf_img($opnum,$n_path);

			$HYJJ = '';
			$this->_levelConfirm($HYJJ,1);
			$z_myName = $rsuserid."(".$rusername.") [".$bm_l[$rtreep]."]";


			$z_tree[$zz][0] = '<img id="'.$oz_TabNN.'" src="'.$z_img.'" align="absmiddle" onclick="'.$z_function.'">';
			$z_tree[$zz][0].= '<img id="fg'.$rssid.'" src="'.$z_us_img.'" align="absmiddle"> ';
			$z_tree[$zz][0].= $z_myName;
			if(!empty($oz_img)){
				$z_tree[$zz][0].= '<img id="o'.$oz_TabNN.'" src="'.$oz_img.'" align="absmiddle" style="display:none;">';
			}
			$z_tree[$zz][1] = $z_myTabN;
			$z_tree[$zz][2] = $cf_mm;
			$zz++;
		}
		$zzz_str = "";
		foreach($z_tree as $zzzz){

			$ttt_nnn = $this->cf_img($ttt_mm,$n_path);
			$zzz_str .= '<p>'.$zzzz[2].$zzzz[0].'</p>'.
					'<table width="100%" border="0" cellspacing="0" cellpadding="0" id="'.$zzzz[1].'" class="treep2">' .
					'<tr><td id="'.$zzzz[1].'_tree">'.$ttt_nnn.'<img src="'.__PUBLIC__.'/Images/loading2.gif" align="absmiddle"></td>' .
					'</tr></table>';

		}
		$this->assign('zzz_str',$zzz_str);
		$this->display();
		exit;

	}

	private function pb_img(){

		$tt[1] = __PUBLIC__."/Images/tree/center.gif";
		$tt[2] = __PUBLIC__."/Images/tree/Official.gif";
		$tt[3] = __PUBLIC__."/Images/tree/trial.gif";

		$tt[4] = __PUBLIC__."/Images/tree/P2.gif";
		$tt[5] = __PUBLIC__."/Images/tree/P1.gif";
		$tt[6] = __PUBLIC__."/Images/tree/L2.gif";
		$tt[7] = __PUBLIC__."/Images/tree/L1.gif";

		$tt[8] = __PUBLIC__."/Images/tree/M2.gif";
		$tt[9] = __PUBLIC__."/Images/tree/M1.gif";

		return $tt;
	}

	private function cf_img($num=1,$array=','){
		for($i=1;$i<=$num;$i++){
			if(strpos($array,','.$i.',') !==false){
				$cf_img .= '<img src="'.__PUBLIC__.'/Images/tree/L5.gif" align="absmiddle">';
			}else{
				$cf_img .= '<img src="'.__PUBLIC__.'/Images/tree/L4.gif" align="absmiddle">';
			}
		}
		return $cf_img;
	}
	
	private function lk_treep(){
		$val = array();
		$val[0] = "A 部门";
		$val[1] = "B 部门";
		$val[2] = "C 部门";
		$val[3] = "D 部门";
		$val[4] = "E 部门";
		$val[5] = "F 部门";
		$val[6] = "G 部门";
		$val[7] = "H 部门";
		$val[8] = "I 部门";
		return $val;
	}
	
	public function Tree_yx(){
		$fck = M('fck');
		$fid = (int)$_GET['fid'];
		$myid = $_SESSION[C('USER_AUTH_KEY')];
		if(empty($fid)){
			$fid = $myid;
		}
		$where = array();
		$where['id'] = array('eq',$myid);
		$mrs = $fck->where($where)->field('n_pai')->find();
		$mypai = $mrs['n_pai'];
		unset($where,$mrs);
		
		$where = array();
		$where['id'] = array('eq',$fid);
		$fnrs = $fck->where($where)->field('n_pai')->find();
		$fnpai = $fnrs['n_pai'];
		unset($where,$fnrs);
		$map = array();
		$map['is_pay'] = array('gt',0);
		$map['n_pai'] = array('egt',$fnpai);
		$map['_string'] ="n_pai>=".$mypai;
		$field  = '*';
		
		//=====================分页开始==============================================
		import ( "@.ORG.ZQPage" );  //导入分页类
		$count = $fck->where($map)->count();//总页数
		$listrows = 20;//每页显示的记录数
		$page_where = 'fid=' . $fid;//分页条件
		$Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
		//===============(总页数,每页显示记录数,css样式 0-9)
		$show = $Page->show();//分页变量
		$this->assign('page',$show);//分页变量输出到模板
		$list = $fck->where($map)->field($field)->order('n_pai asc')->page($Page->getPage().','.$listrows)->select();
		$HYJJ = '';
		$this->_levelConfirm($HYJJ,1);
		$this->assign('voo',$HYJJ);//会员级别
		$this->assign('list',$list);//数据输出到模板
		//=================================================
		$this->display();
	}
	
	public function yidongTwo(){
		$this->_Admin_checkUser();//後台權限檢測
		if ($_SESSION[C('USER_AUTH_KEY')] != 1){
			$this->error('无权操作！');
			exit;
		}
		$fck = M('fck');
	
		$sUserID        = $_POST['sUserID'];
		$yUserID        = $_POST['yUserID'];
		//
		if ($sUserID == '' or $yUserID == ''){
			$this->error('请输入会员编号！');
			exit;
		}
		if ($sUserID == $yUserID){
			$this->error('两个编号相同，请重新输入会员编号！');
			exit;
		}
		//
	
		$field = 'id,user_id,nickname,re_id,re_path,re_level,is_pay';
		$where = array();
		$fwhere = array();
		$where['user_id'] = $sUserID;
		$fwhere['user_id'] = $yUserID;
	
	
		//$fwhere['TreePlace'] = $TreePlace;
		$fck_rs = $fck->where($where)->field($field)->find();
		if (!$fck_rs){
			$this->error('没有找到该会员编号，请重新输入会员编号！');
			exit;
		}else{
			if ($fck_rs['id']== 1 ){
				$this->error('根节点不能移动，请重新输入会员编号！');
				exit;
			}
		}
		$fck_frs = $fck->where($fwhere)->field($field)->find();
		if (!$fck_frs){
			$this->error('没有找到移至的会员编号，请重新输入会员编号！');
			exit;
		}else{
			if ($fck_frs['is_pay'] == 0){
				$this->error('移至的会员编号尚未开通，请重新输入会员编号！');
				exit;
			}
			//分割成數組進行比較
			$arr = explode(',',$fck_frs['re_path']);
			if (in_array($fck_rs['id'],$arr)){
				$this->error('移至会员编号在 '.$sUserID.' 的团队下面，请重新输入！');
				exit;
			}
		}
		$pLevel = $fck_frs['re_level'] + 1;
		$pPaht = $fck_frs['re_path'].$fck_frs['id'].',';
		$fck->execute("UPDATE __TABLE__ SET `re_id`=".$fck_frs['id'].",`re_name`='".$fck_frs['user_id']."',`re_path`='".$pPaht."',`re_level`=".$pLevel." where `id`= ".$fck_rs['id']);
	
		$vwhere['re_path'] = array('like',$fck_rs['re_path'].$fck_rs['id'].'%');
		$vo = $fck->where($vwhere)->field($field)->order('re_level asc')->select();
		$vfwhere = array();
	
		foreach ($vo as $voo){
			$vfwhere['id'] = $voo['re_id'];
			$vrs = $fck->where($vfwhere)->field($field)->find();
			$pLevel = $vrs['re_level'] + 1;
			$pPaht = $vrs['re_path'].$vrs['id'].',';
			$fck->execute("UPDATE __TABLE__ SET re_path='".$pPaht."',re_level=".$pLevel." where `id`= ".$voo['id']);
		}
		$bUrl = __URL__.'/TreeAjax/gly/1/';
		$this->_box(1,'移动会员成功！',$bUrl,3);
	}

	//双轨图B
	public function Tree2_B(){
		$time = date('H');
		$this->_checkUser();
		$ji_c = $this->ji_Color_B();  //级别颜色
		$kd_c = $this->kd_Color();  //是否开通
		$mi_c = $this->Mi_Cheng();  //级别名称
		$ac_c = $this->AC_Color();  //级别名称
	
		$fck   =  M('fck');
		$fck2   =  M('fck2');
	
		$ffrs = $fck2->where('fck_id='.$_SESSION[C('USER_AUTH_KEY')])->find();
		if($ffrs==false){
			$this->error( '您尚未进入B网！');
			exit;
		}
	
		$id  = $ffrs['id'];
		$myid = $ffrs['id'];
		$UID = (int) $_GET['ID'];
		if (empty($UID)){$UID = $id;}
		$UserID = $_POST['UserID'];  //跳转到 X 用户
		if (!empty($UserID)){
			if (strlen($UserID) > 20 ){
				$this->error( '错误操作！');
				exit;
			}
			$UserID=strtoupper($UserID);
			$where = "p_path like '%,". $UID .",%' and user_id='". $UserID ."' ";
			//			$where = "user_id='". $UserID ."' ";
			$field = 'id';
			$rs = $fck2 ->where($where)->field($field)->find();
			if($rs == false){
				$this->error('没有该用户!');
				exit();
			}else{
				$UID = $rs['id'];
			}
		}
	
		$where =array();
		$where['ID'] = $UID;
		$where['_string'] = 'id>='.$id;
		$field = '*';
		$rs = $fck2 ->where($where)->field($field)->find();
		if (!$rs){
			$this->error('没有该用户!');
			exit();
		}else{
			$ID			= $rs['id'];
			$UserID		= $rs['user_id'];
			$NickName	= $rs['nickname'];
			$TreePlace	= $rs['treeplace'];   //区分左右 0为左边,1为右边
			if($ID==$id){
				$FatherID = $id;
			}else{
				$FatherID	= $rs['father_id'];    //安置人ID
			}
	
			$isPay		= $rs['is_pay'];		  //是否为正式(开通时为正式)
			$uLevel		= $rs['u_level'];      //级别
			$pPath		= $rs['p_path'];       //自已的路径
			$pLevel		= $rs['p_level'];	  //层数(数字)
			$Rid		= $rs['id'];
		}
		if ($isPay>1) $isPay=1;
	
		//显示层数
		$uLev = (int) $_GET['uLev'];		//$Lev 记录显示层数
		if (is_numeric($uLev) == false) $uLev = $_SESSION['uLevB2'];
		if (is_numeric($uLev) == false) $uLev = 3;
		if ($uLev < 1 || $uLev > 11)    $uLev = 3;
		$_SESSION['uLevB2']=$uLev;
		for ($i=1;$i<=$uLev;$i++){
			$Nums = $Nums + pow(2,$i);		//pow(x,y) 返回x的y次方
		}
		global $TreeArray;
		$TreeArray = array();
	
		for ($i=1;$i<=$Nums;$i++){
			$TreeArray[$i] = "<table border='0' cellpadding='0' cellspacing='1' class='tu_box'><tr><td class='tu_ko'> 空位 </td></tr></table>";
		}
		$bj = "style='background:". $kd_c[$isPay] ."';";  //表格背景色
		$StTab = "<table border='0' cellpadding='0' cellspacing='1' class='tu_box'><tr><td colspan='3' style='background:". $ji_c[$uLevel] .";font-weight:bold;'>";
		$MyYJ = "</td></tr>";
		$MyYJ .= "</table>";
	
		$ZiJi   = $StTab."<a href='#'>". $UserID ."</a>". $MyYJ;
		$Str4C0 = "<table  border='0' cellpadding='0' cellspacing='1' class='tu_box'><tr><td class='tu_ko'>";
		$Str4C1 = "<a href='". __URL__ ."/KaiBoLuo/RID/". $myid ."/TPL/";
		$Str4C4 = "</td></tr></table>";
		if ($isPay > 2){
			$i = pow(2,$uLev);
			$TreeArray['1'] = $Str4C0.$Str4C1."0/FID/". $ID ."' target='_self'>点击注册</a>". $Str4C4;
			$TreeArray[$i]  = $Str4C0.$Str4C1."1/FID/". $ID ."' target='_self'>点击注册</a>". $Str4C4;
		}else{
			//			$TreeArray['1']=$Str4C0.$Str4C1."0/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
			//$TreeArray[$i] =$Str4C0.$Str4C1."1/FID/".$ID."' target='_self'>点击注册</a>".$Str4C4;
		}
	
		$TreeArray['0'] = $ZiJi;
	
		$this->Tree2_B_MtKass($UID, 0, $pLevel, $uLev, $Str4C0, $Str4C1, $Str4C4,  $TreeArray, $Nums);
		//会员ID,0,绝对层次,显示层高,表开始,表内链接,表结束  ,级别颜色数组,所有空位表格,显示多少会员数(包括空位数)
		$wop = '';
		$this->Tree2_showTree($uLev, $TreeArray, $wop);
	
		$fee = M('fee');
		$fee_rs = $fee->field('s10')->find();
		$Level = explode('|',$fee_rs['s10']);
		$this->assign('Level',$Level);
		$this->assign('ColorUA',$ji_c);
		$this->assign('TU_Color',$kd_c);
		$this->assign('TU_MiCheng',$mi_c);
		$this->assign('AC_Color',$ac_c);
		$this->assign('UID',$UID);
		$this->assign('uLev',$uLev);
		$this->assign('FatherID',$FatherID);
		$this->assign('wop',$wop);
		$this->display('Tree2_B');
	
	}
	//双轨图---生成下层会员内容
	private function Tree2_B_MtKass($FatherID,$iL,$pLevel,$uLev,$Str4C0,$Str4C1,$Str4C4,&$TreeArray,$Nums){
		$ji_c = $this->ji_Color_B();  //级别颜色
		$kd_c = $this->kd_Color();  //是否开通
		$mi_c = $this->Mi_Cheng();  //级别名称
		if (!empty($FatherID)){
			$fck = M("fck2");
			$where = array();
			$where = "father_id=". $FatherID ." And p_level-". $pLevel ."<=". $uLev ." And treeplace<2 Order By treeplace Asc";
			$field = '*';
			$rs    = $fck->where($where)->field($field)->select();
			foreach($rs as $rss){
				if ($rss['treeplace'] == 0){
					$k = $iL + 1;
				}else{
					$i = ($pLevel + $uLev) - $rss['p_level'] + 1;
					$j = pow(2,$i);
					$k = $j + $iL;
				}
				$i			= ($pLevel + $uLev) - $rss['p_level'];
				$Uo			= $k + 1;
				$Yo			= $k + pow(2,($pLevel + $uLev) - $rss['p_level']);
				$Leve		= $rss['u_level'];	//用户级别
				$Rid		= $rss['id'];
				$uUserID	= $rss['user_id'];
				$uisPay		= $rss['is_pay'];
				$upLevel	= $rss['p_level'];
	
				if ($uisPay>1) $uisPay=1;
	
				$bj = "style='background:". $kd_c[$uisPay] .";'";  //表格背景色
				$StTab = "<table border='0' cellpadding='0' cellspacing='1' class='tu_box'><tr><td colspan='3' style='background:".$ji_c[$Leve].";font-weight:bold;'>";
				$MyYJ = "</td></tr>";
				$MyYJ .= "</table>";
	
				//			$Str = $StTab."<a href='". __URL__ ."/PuTao/ID/". $Rid ."'>会员编号：". $uUserID ."</a>". $MyYJ;
				$Str = $StTab."<a href='". __URL__ ."/Tree2_B/ID/". $Rid ."'>". $uUserID ."</a>". $MyYJ;
				$Str4C2 = "/FID/". $Rid ."'>点击注册</a>";
	
				if ($uisPay > 2){
					if ($Yo <= $Nums + 1 && $i>0){
						$TreeArray[$Uo] = $Str4C0. $Str4C1 ."0". $Str4C2 . $Str4C4;
						$TreeArray[$Yo] = $Str4C0. $Str4C1 ."1". $Str4C2 . $Str4C4;
					}
				}else{
					if ($Yo<=$Nums+1 && $i>0){
						//						$TreeArray[$Uo]=$Str4C0.$Str4C1."0".$Str4C2.$Str4C4;
						//$TreeArray[$Yo]=$Str4C0.$Str4C1."1".$Str4C2.$Str4C4;
					}
				}
				$TreeArray[$k] = $Str;
				if ($upLevel < $pLevel + $uLev){
					//查出来的下级的绝对层	 //上级的绝对层,显示层数
					$this->Tree2_B_MtKass($Rid, $k, $pLevel, $uLev, $Str4C0, $Str4C1, $Str4C4,  $TreeArray, $Nums, $ColorUA);
				}
			}
	
		}
	}

}
?>