<?php
class RegAction extends CommonAction{
	function _initialize() {
// 		$this->_inject_check(0);//调用过滤函数
		$this->_Config_name();
// 		$this->_checkUser();
		header("Content-Type:text/html; charset=utf-8");
	}

	public function add(){
		
		$this->display('add');
	}
	  public function adduserdd(){
      $fck = M ('fck');
      $field = 'id,user_id,p_level,p_path';
      $re_rs = $fck ->where('is_pay>0')->order('p_level asc,id asc')->field($field)->select();
      
      foreach($re_rs as $vo){
        $faid=$vo['id'];
        $count = $fck->where("is_pay>0 and father_id=".$faid)->count();

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
         
          return $arry;
          break;
        }
      }
    }
	/**
	 * 会员注册
	 * **/
	public function users($Urlsz=0){
		$this->_checkUser();
	
		$data=$this->adduserdd();
		
		$fck = M('fck');
		$fee = M ('fee');
		$RID = (int) $_GET['RID'];
		$FID = (int) $_GET['FID'];
		$TP = (int) $_GET['TPL'];
		if (empty($TPL))$TPL = 0;
		$TPL = array();
		for($i=0;$i<5;$i++){
			$TPL[$i] = '';
		}
		$TPL[$TP] = 'selected="selected"';
		//===报单中心
		$zzc = array();
		$where = array();
		$where['id'] = $_SESSION[C('USER_AUTH_KEY')];
		$field ='user_id,is_agent,agent_cash,shop_name';
		$rs = $fck ->where($where)->field($field)->find();
		$money = $rs['agent_cash'];
		$mmuserid = $rs['user_id'];
		if ($rs['is_agent'] >= 2){
			$zzc[1] = $rs['user_id'];
		}else{
			$mrs = M('fck')->where('id=1')->field('id,user_id')->find();
			$zzc[1] = $mrs['user_id'];
		}
		$this->assign('myid',$_SESSION[C('USER_AUTH_KEY')]);

		//===招商代表
		$where['id'] = $RID;
		$field ='user_id,is_agent';
		$rs = $fck ->where($where)->field($field)->find();
		if ($rs){
			$zzc[2] = $rs['user_id'];
		}else{
			$zzc[2] = $mmuserid;
		}
		$where['id'] = $FID;
		$field ='user_id,is_agent';
		$rs = $fck ->where($where)->field($field)->find();
		if ($rs){
			$zzc[3] = $rs['user_id'];
		}else{
			$zzc[3] =$data['father_name'];
		}

		$arr = array();
		$arr['UserID'] = $this->_getUserID();
		$this->assign('flist',$arr);

		$pwhere = array();
		$product = M ('product');
		$pwhere['is_reg'] = array("eq",1);
		$prs = $product->where($pwhere)->select();
		$this->assign('plist',$prs);



		$fee_s = $fee->field('*')->find();
		$s9 = $fee_s['s9'];
		$s9 = explode('|',$s9);
		$s2 = explode('|',$fee_s['s2']);

		$i4 = $fee_s['i4'];
		if ($i4==0){
			$openm=1;
		}else{
			$openm=0;
		}
		$youka = explode('|',$fee_s['str17']);
		//输出银行
		$bank = explode('|',$fee_s['str29']);
		//输出级别名称
        $Level = explode('|',C('Member_Level'));
		//输出注册单数
		$Single = explode('|',C('Member_Single'));
		//输出一单的金额
		
		$lang= explode('|',$fee_s['str24']);
		$countrys = explode('|',$fee_s['str25']);

		$wentilist = explode('|',$fee_s['str99']);
		$region = M('region');
		$dizhi=$region->where('pid=1')->select();
		$this->assign('s9',$s9);
		$this->assign('s2',$s9);
		$this->assign('openm',$openm);
		$this->assign('youka',$youka);
		$this->assign('bank',$bank);
        $this->assign('Level',$Level);
		$this->assign('Single',$Single);
		$this->assign('Money',$fee_s['s2']);
		$this->assign('Money1',$money);
		$this->assign('wentilist',$wentilist);

		$this->assign('dizhi', $dizhi);
		
		$this->assign('lang',$lang);
		$this->assign('countrys',$countrys);

		unset($bank,$Level,$Level,$data);

	    $this->assign('TPL',$TPL);
		$this->assign('zzc',$zzc);

		unset($fck,$TPL,$where,$field,$rs,$data_temp,$temp_rs,$rs);
	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        $mm=!empty($_POST['id'])?$_POST['id']:0;
        $dizhi=$region->where('pid='.$mm)->select();
        $sk=json_encode($dizhi);
        echo $sk;
      die;
}
		$this->display('users');
	}
	
	/**
	 * 注册确认
	 * **/
	public function usersConfirm() {
		$this->_checkUser();
		$region = M('region');
		$id = $_SESSION[C('USER_AUTH_KEY')];
		$fck    = M ('fck');
		$rs = $fck -> field('is_pay,agent_cash') -> find($id);
		if($rs['is_pay'] == 0){
			$this->error('临时会员不能注册会员！');
			exit;
		}
		$this->assign('UserID',$_POST['UserID']);
		$ck1 = $_POST['ck1'];
		if (!$ck1) {
		    $this->error('请勾选我已阅读消费协议！');
		    exit;
		}
		$data = array();  //创建数据对象
		//检测招商代表
		$RID = trim($_POST['RID']);  //获取推荐会员帐号
		$mapp  = array();
		$mapp['user_id']	= $RID;
		$mapp['is_pay']	    = array('gt',0);
		$authInfoo = $fck->where($mapp)->field('id,user_id,re_level,re_path,is_agent,l,r')->find();
		if ($authInfoo){
		    $this->assign('RID',$RID);
		    $data['re_id'] = $authInfoo['id'];
		}else{
		    $this->error('推荐人不存在！');
		    exit;
		}
		unset($mapp);
		
		//检测报单中心
		$shopResult = $this->find_shopid($RID);
		// 报单中心的用户名
		$data['shop_id'] = $shopResult['id'];
		// 报单中心姓名
		$data['shop_name'] = $shopResult['user_id'];
		unset($shopResult);

 		// 根据左右区人数判断滑落地点
		$FID = $this->positionRecuision($authInfoo['id'],$authInfoo['user_id'],$authInfoo['l'],$authInfoo['r']);
 		$mappp  = array();
 		$mappp['user_id'] = $FID['user_id'];
 		$authInfoo = $fck->where($mappp)->field('id,p_path,p_level,user_id,is_pay,tp_path')->find();
 		if ($authInfoo){
 			$this->assign('FID',$FID);
 			$fatherispay = $authInfoo['is_pay'];
 			$data['father_id'] = $authInfoo['id'];                        //上节点ID
 			$tp_path = $authInfoo['tp_path'];
 		}else{
 			$this->error('上级会员不存在！');
 			exit;
 		}
 		unset($authInfoo,$mappp);
  		$TPL = $FID['treeplace'];
 		$where = array();
 		$where['father_id'] = $data['father_id'];
 		$where['treeplace'] = $TPL;
 		$rs = $fck->where($where)->field('id')->find();
 		if ($rs){
 			$this->error('该位置已经注册！');
 			exit;
 		}
 		if($TPL==0){
 			$zy_n = "1区";
 		}elseif($TPL==1){
 			$zy_n = "2区";
 		}elseif($TPL==2){
 			$zy_n = "3区";
 		}else{
 			$TPL = 0;
 			$zy_n = "1区";
 		}
 		$this->assign('zy_n',$zy_n);
 		$this->assign('TPL',$TPL);

		unset($rs,$where,$TPL);

		$fwhere = array();//检测帐号是否存在
		$fwhere['user_id'] = trim($_POST['UserID']);
		$frs = $fck->where($fwhere)->field('id')->find();
		if ($frs){
			$this->error('该会员编号已存在！');
			exit;
		}
		$kk = stripos($fwhere['user_id'],'-');
		if($kk){
			$this->error('会员编号中不能有扛(-)符号！');
			exit;
		}
		unset($fwhere,$frs);

		$errmsg="";
		
		if(empty($_POST['BankCard'])){
			$errmsg.="<li>银行卡号不能为空！</li>";
		}
		$this->assign('BankCard',$_POST['BankCard']);

		$huhu=trim($_POST['UserName']);
		if(empty($huhu)){
			$errmsg.="<li>请填写开户姓名！</li>";
		}
// 		if(!preg_match('/^[\x7f-\xff]+$/', $huhu)){
// 		    $this->error('开户姓名必须是中文！');
// 		    exit;
// 		}
		$this->assign('UserName',$_POST['UserName']);
		$this->assign('UserName',$_POST['UserName']);
		if(empty($_POST['UserCode'])){
			$errmsg.="<li>请填写身份证号码！</li>";
		}
		
		if(empty($_POST['UserTel'])){
			$errmsg.="<li>请填写电话号码！</li>";
		}
		$this->assign('UserTel',$_POST['UserTel']);
		if(empty($_POST['f4'])){
			$errmsg.="<li>请填写单数</li>";
		}
		$usercc=trim($_POST['UserTel']);	
		if(!preg_match("/^1[34578]\d{9}$/",$_POST['UserTel'])){
			$this->error('手机号码格式不正确！');
			exit;
		}	
		$usercc=trim($_POST['UserCode']);
		if(!preg_match("/\d{17}[\d|X]|\d{15}/", $_POST['UserCode'])){
			$errmsg.="<li>身份证号码格式不正确！</li>";
		}		
		$this->assign('UserCode',$_POST['UserCode']);

		if(strlen($_POST['Password']) < 1 or strlen($_POST['Password']) > 16){
			$this->error('密码应该是1-16位！');
			exit;
		}
		if($_POST['Password'] != $_POST['rePassword']){  //一级密码
			$this->error('一级密码两次输入不一致！');
			exit;
		}
		if($_POST['PassOpen'] != $_POST['rePassOpen']){  //二级密码
			$this->error('二级密码两次输入不一致！');
			exit;
		}
		if($_POST['Password'] == $_POST['PassOpen']){  //二级密码
			$this->error('一级密码与二级密码不能相同！');
			exit;
		}
		$this->assign('Password',$_POST['Password']);
		$this->assign('PassOpen',$_POST['PassOpen']);
		
		$us_name = $_POST['us_name'];
		$us_address = $_POST['us_address'];
		$us_tel = $_POST['us_tel'];
		$f4 = $_POST['f4'];
		$this->assign('us_name',$_POST['us_name']);
		$this->assign('us_address',$_POST['us_address']);
		$this->assign('us_tel',$_POST['us_tel']);

		$s_err = "<ul>";
		$e_err = "</ul>";
		if(!empty($errmsg)){
			$out_err = $s_err.$errmsg.$e_err;
			$this->error($out_err);
			exit;
		}


		$uLevel = $_POST['u_level'];

		$this->assign('u_level',$_POST['u_level']);
		$fee  = M ('fee') -> find();
		$s    = $fee['s9'];
		$s10 = explode('|',$fee['s10']);
		$this->assign('uarray',$s10);
		$s9 = explode('|',$fee['s9']);
		
		$u_money = $s9[$uLevel];
		
		$this->assign('money',$u_money);
		$this->assign('f4',$f4);
		$this->assign('s_province',$_POST['s_province']);
		$this->assign('s_city',$_POST['s_city']);
		$this->assign('s_county',$_POST['s_county']);

		$this->assign('youkakName',$_POST['youkakName']);
		$this->assign('YouCard',$_POST['YouCard']);
		
		$this->assign('nickname',$_POST['nickname']);
		$this->assign('BankName',$_POST['BankName']);
		$this->assign('BankProvince',$_POST['BankProvince']);
		$this->assign('BankCity',$_POST['BankCity']);
		$this->assign('BankAddress',$_POST['BankAddress']);
		
		$this->assign('UserAddress',$_POST['UserAddress']);
		$this->assign('qq',$_POST['qq']);
		
		$this->display();

	}
	
	/**
	 * 注册处理
	 * **/
	public function usersAdd() {
		$this->_checkUser();
		$id = $_SESSION[C('USER_AUTH_KEY')];
		$fck    = M ('fck');  //注册表
		$relation    = M ('relation');  //对应关系表

		$rs = $fck -> field('is_pay,agent_cash') -> find($id);
		$m = $rs['agent_cash'];
		if($rs['is_pay'] == 0){
			$this->error('临时会员不能注册会员！');
			exit;
		}
		if (strlen($_POST['UserID'])<1){
			$this->error('会员编号不能少！');
			exit;
		}

		$data = array();  //创建数据对象
		//检测招商代表
		$RID = trim($_POST['RID']);  //获取推荐会员帐号
		$mapp  = array();
		$mapp['user_id']	= $RID;
		$mapp['is_pay']	    = array('gt',0);
		$authInfoo = $fck->where($mapp)->field('id,user_id,re_level,re_path,is_agent,l,r')->find();
		if ($authInfoo){
		    $data['re_path'] = $authInfoo['re_path'].$authInfoo['id'].',';  //推荐路径
		    $data['re_id'] = $authInfoo['id'];                              //招商代表ID
		    $data['re_name'] = $authInfoo['user_id'];                       //招商代表帐号
		    $data['re_level'] = $authInfoo['re_level'] + 1;                 //代数(绝对层数)
		}else{
		    $this->error('招商代表不存在！');
		    exit;
		}
		unset($mapp);
		
		//检测报单中心
		$shopResult = $this->find_shopid($RID);
		// 报单中心的用户名
		$data['shop_id'] = $shopResult['id'];
		// 报单中心姓名
		$data['shop_name'] = $shopResult['user_id'];
		unset($shopResult);

        // 根据左右区人数判断滑落地点
		$FID = $this->positionRecuision($authInfoo['id'],$authInfoo['user_id'],$authInfoo['l'],$authInfoo['r']);
 		$mappp  = array();
 		$mappp['user_id'] = $FID['user_id'];
 		$authInfoo = $fck->where($mappp)->field('id,p_path,p_level,user_id,is_pay,tp_path')->find();
 		if ($authInfoo){
 			$fatherispay = $authInfoo['is_pay'];
 			$data['p_path'] = $authInfoo['p_path'].$authInfoo['id'].',';  //绝对路径
 			$data['father_id'] = $authInfoo['id'];                        //上节点ID
 			$data['father_name'] = $authInfoo['user_id'];                 //上节点帐号
 			$data['p_level'] = $authInfoo['p_level'] + 1;                 //上节点ID
 			$tp_path = $authInfoo['tp_path'];
 		}else{
 			$this->error('上级会员不存在！');
 			exit;
 		}
 		unset($authInfoo,$mappp);
  		$TPL = $FID['treeplace'];
 		$where = array();
 		$where['father_id'] = $data['father_id'];
 		$where['treeplace'] = $TPL;
 		$rs = $fck->where($where)->field('id')->find();
 		if ($rs){
 			$this->error('该位置已经注册！');
 			exit;
 		}else{
 			$data['treeplace'] = $TPL;
 			if(strlen($tp_path)==0){
 				$data['tp_path'] = $TPL;
 			}else{
 				$data['tp_path'] = $tp_path.",".$TPL;
 			}
 		}
		unset($rs,$where,$TPL);

		$fwhere = array();//检测帐号是否存在
		$fwhere['user_id'] = trim($_POST['UserID']);
		$frs = $fck->where($fwhere)->field('id')->find();
		if ($frs){
			$this->error('该会员编号已存在！');
			exit;
		}
		$kk = stripos($fwhere['user_id'],'-');
		if($kk){
			$this->error('会员编号中不能有扛(-)符号！');
			exit;
		}
		unset($fwhere,$frs);

		$errmsg="";
		if(empty($_POST['BankCard'])){
			$errmsg.="<li>银行卡号不能为空！</li>";
		}
		$huhu=trim($_POST['UserName']);
		if(empty($huhu)){
			$errmsg.="<li>请填写开户姓名！</li>";
		}
		if(empty($_POST['UserTel'])){
			$errmsg.="<li>请填写电话号码！</li>";
		}

		$usercc=trim($_POST['UserTel']);		
		if(!preg_match("/^1[34578]\d{9}$/",$_POST['UserTel'])){
			$this->error('手机号码格式不正确！');
			exit;
		}	
		if(strlen($_POST['Password']) < 1 or strlen($_POST['Password']) > 16){
			$this->error('密码应该是1-16位！');
			exit;
		}
		if($_POST['Password'] == $_POST['PassOpen']){  //二级密码
			$this->error('一级密码与二级密码不能相同！');
			exit;
		}

		$us_name = $_POST['us_name'];
		$us_address = $_POST['us_address'];
		$us_tel = $_POST['us_tel'];
		
		$this->assign('us_name',$_POST['us_name']);
		$this->assign('us_address',$_POST['us_address']);
		$this->assign('us_tel',$_POST['us_tel']);
		

		$s_err = "<ul>";
		$e_err = "</ul>";
		if(!empty($errmsg)){
			$out_err = $s_err.$errmsg.$e_err;
			$this->error($out_err);
			exit;
		}


		$uLevel = $_POST['u_level'];
		$fee  = M ('fee') -> find();
		$s    = $fee['s9'];
		$s2 = explode('|',$fee['s2']);
		$s9 = explode('|',$fee['s9']);

		$F4     = $_POST['f4'];//认购单数
		$ul     = $s9[$uLevel];
		
		$Money = explode('|',C('Member_Money'));  //注册金额数组
		//当前日期  
        $sdefaultDate = date("Y-m-d");
        //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期  
        $first=1;  
        //获取当前周的第几天 周日是 0 周一到周六是 1 - 6  
        $w=date('w',strtotime($sdefaultDate));
        //获取本周开始日期，如果$w是0，则表示周日，减去 6 天  
        // $week_start=date('Y-m-d',strtotime("$sdefaultDate -".($w ? $w - $first : 6).' days'));
        $week_strt=strtotime("$sdefaultDate -".($w ? $w - $first : 6).' days');
        $booleanID = true;
        $new_userid = rand(10000000,99999999);
        while ($booleanID) {
            $bResult=$fck->where("user_id='".$new_userid."'")->find();
            if ($bResult) {
                $new_userid = rand(10000000,99999999);
            } else {
                $booleanID = false;
            }
            
        }
		$sum=$F4*$ul;
		$data['user_id']             = trim($_POST['UserID']);
		$data['bind_account']        = '3333';
		$data['last_login_ip']       = '';                            //最后登录IP
		$data['verify']              = '0';
		$data['status']              = 1;                             //状态(?)
		$data['type_id']             = '0';
		$data['last_login_time']     = time();                        //最后登录时间
		$data['login_count']         = 0;                             //登录次数
		$data['info']                = '信息';
		$data['name']                = '名称';
		$data['password']            = md5(trim($_POST['Password']));  //一级密码加密
		$data['passopen']            = md5(trim($_POST['PassOpen']));  //二级密码加密
		$data['pwd1']                = trim($_POST['Password']);       //一级密码不加密
		$data['pwd2']                = trim($_POST['PassOpen']);       //二级密码不加密

		$data['wenti']				= trim($_POST['wenti']);  //密保问题
		$data['wenti_dan']			= trim($_POST['wenti_dan']);  //密保答案
		
		$data['lang']           = $_POST['lang'];             //语言
		$data['countrys']           = $_POST['countrys']; //国家

		$data['bank_name']           = $_POST['BankName'];             //银行名称
		$data['bank_card']           = $_POST['BankCard'];             //帐户卡号
		$data['user_name']           = $_POST['UserName'];             //姓名
		$data['nickname']			  = $_POST['nickname'];//$_POST['nickname'];  //昵称
		$data['bank_province']       = $_POST['BankProvince'];  //省份
		$data['bank_city']           = $_POST['BankCity'];      //城市
		$data['bank_address']        = $_POST['BankAddress'];          //开户地址
		//$data['user_post']           = $_POST['UserPost']; 		   //
		$data['user_code']           = $_POST['UserCode'];             //身份证号码
		$data['user_address']        = $_POST['UserAddress'];          //联系地址
		$data['email']               = $_POST['UserEmail'];            //电子邮箱
		$data['qq']              	 = $_POST['qq'];            	   //微信
		$data['user_tel']            = $_POST['UserTel'];              //联系电话
		$data['s_province']            = $_POST['s_province'];
		$data['s_city']            = $_POST['s_city'];
		$data['s_county']            = $_POST['s_county'];

		$data['youname']            = $_POST['youkakName'];
		$data['youcar']            = $_POST['YouCard'];

		$data['is_pay']              = 0;                              //是否开通
		$data['vip4']              = 1; 
		$data['rdt']                 = time();                         //注册时间
		//$data['pdt']                 = time();
		$data['u_level']             = $uLevel+1;                      //注册等级
		$data['cpzj']                = $sum;                          //注册金额
		$data['_times']                  = $sdefaultDate;							//单量
		$data['f4']                  = $F4;							//单量
		$data['wlf']                 = 0;                             
		$data['is_fh']                 = 1;                              
		$data['is_sf']                 = 1;                            
		$result = $fck->add($data);
		
		$temp_Uid=$fck->where("user_id='".$new_userid."'")->field("id")->find();
		$data1['uid']                 = $temp_Uid['id'];
		$data1['user_id']             = $_POST['UserID'];
		$data1['user_id_encrypt']     = $new_userid;
		$data1['bank_name']           = $_POST['BankName'];             //银行名称
		$data1['bank_card']           = $_POST['BankCard'];             //帐户卡号
		$data1['user_name']           = $_POST['UserName'];             //姓名
		$data1['nickname']			  = $_POST['nickname'];//$_POST['nickname'];  //昵称
		$data1['bank_province']       = $_POST['BankProvince'];  //省份
		$data1['bank_city']           = $_POST['BankCity'];      //城市
		$data1['bank_address']        = $_POST['BankAddress'];          //开户地址
		$data1['user_code']           = $_POST['UserCode'];             //身份证号码
		$data1['user_address']        = $_POST['UserAddress'];          //联系地址
		$data1['email']               = $_POST['UserEmail'];            //电子邮箱
		$data1['qq']              	 = $_POST['qq'];            	   //qq
		$data1['user_tel']            = $_POST['UserTel'];              //联系电话
		$result1 = $relation->add($data1);

		unset($data,$data1,$fck);
		if($result) {
			
			M('fee')->query("update __TABLE__ set us_num=us_num+1");

			$_SESSION['new_user_reg_id'] = $result;

			echo "<script>window.location='".__URL__."/users_ok/';</script>";
			exit;
		}else{
			$this->error('会员注册失败！');
			exit;
		}
	}
	
	/**
	 * 注册完成
	 * **/
	public function users_ok(){
		$this->_checkUser();
		$gourl = __APP__."/Reg/users/";
		if(!empty($_SESSION['new_user_reg_id'])){

			$fck = M('fck');
			$fee_rs = M ('fee') -> find();

			$this -> assign('s8',$fee_rs['s8']);
			$this -> assign('alert_msg',$fee_rs['str28']);
			$this -> assign('s17',$fee_rs['s17']);

			$myrs = $fck->where('id='.$_SESSION['new_user_reg_id'])->find();
			$this->assign('myrs',$myrs);

			$this->assign('gourl',$gourl);
			unset($fck,$fee_rs);
			$this->display();
		}else{
			echo "<script>window.location='".$gourl."';</script>";
			exit;
		}
	}
	
	
	
	//前台注册
	public function us_reg(){
		$fck = M ('fck');
		$fee = M ('fee');
		$reid = (int)$_GET['rid'];
		
		$fee_rs = $fee->field('s2,s9,str21,str27,str29,str99')->find();
		$this->assign('fflv',$fee_rs['str21']);
		$this->assign('str27',$fee_rs['str27']);
		$s9 = $fee_rs['s9'];
		$s9 = explode('|',$s9);
		$this->assign('s9',$s9);
		$s2 = explode('|',$fee_rs['s9']);
		$this->assign('s2',$s2);
		$bank = explode('|',$fee_rs['str29']);
		$this->assign('bank',$bank);
		$wentilist = explode('|',$fee_rs['str99']);
		$this->assign('wentilist',$wentilist);
		
		$arr = array();
		$arr['UserID'] = $this->_getUserID();
		$this->assign('flist',$arr);
		
		//检测招商代表
		$where = array();
		$where['id'] = $reid;
		$where['is_pay'] = array('gt',0);
		$field ='id,user_id,nickname,us_img,is_agent,shop_name';
		$rs = $fck ->where($where)->field($field)->find();
		if($rs){
			if(empty($rs['us_img'])){
				$rs['us_img'] = "__PUBLIC__/Images/tirns.jpg";
			}
			if($rs['is_agent']==2){
				$this->assign('shopname',$rs['user_id']);
			}else{
				$shopname = '100000';
				$this->assign('shopname',$shopname);
			}
			$this->assign('rs',$rs);
			$this->assign('reid',$reid);
		}else{
			$shopname = '100000';
			$this->assign('shopname',$shopname);
		}
		$plan = M ('plan');
		$prs = $plan->find(4);
		$this->assign('prs',$prs);
		$this->display();
	}
	
	//前台注册处理
	public function us_regAC() {
		$fck    = M ('fck');  //注册表
		if (strlen($_POST['UserID'])<1){
			$this->error('会员编号不能少！');
			exit;
		}
		$data = array();  //创建数据对象
		$ck1 = $_POST['ck1'];
		if (!$ck1) {
		    $this->error('请勾选我已阅读消费协议！');
		    exit;
		}
		//检测招商代表
		$RID = trim($_POST['RID']);  //获取推荐会员帐号
		$mapp  = array();
		$mapp['user_id']	= $RID;
		$mapp['is_pay']	    = array('gt',0);
		$authInfoo = $fck->where($mapp)->field('id,user_id,re_level,re_path,p_path,is_agent,l,r')->find();
		if ($authInfoo){
		    $data['re_path'] = $authInfoo['re_path'].$authInfoo['id'].',';  //推荐路径
		    $data['re_id'] = $authInfoo['id'];                              //招商代表ID
		    $data['re_name'] = $authInfoo['user_id'];                       //招商代表帐号
		    $data['re_level'] = $authInfoo['re_level'] + 1;                 //代数(绝对层数)
		}else{
		    $this->error('推荐人不存在！');
		    exit;
		}
		//检测报单中心
		$shopResult = $this->find_shopid($RID);
		// 报单中心的用户名
		$data['shop_id'] = $shopResult['id'];
		// 报单中心姓名
		$data['shop_name'] = $shopResult['user_id'];
		unset($shopResult);

        // 根据左右区人数判断滑落地点
		$FID = $this->positionRecuision($authInfoo['id'],$authInfoo['user_id'],$authInfoo['l'],$authInfoo['r']);
		$mappp  = array();
		$mappp['user_id'] = $FID['user_id'];
		$authInfoo = $fck->where($mappp)->field('id,p_path,p_level,user_id,is_pay,tp_path')->find();
		if ($authInfoo){
			$fatherispay = $authInfoo['is_pay'];
			$data['p_path'] = $authInfoo['p_path'].$authInfoo['id'].',';  //绝对路径
			$data['father_id'] = $authInfoo['id'];                        //上节点ID
			$data['father_name'] = $authInfoo['user_id'];                 //上节点帐号
			$data['p_level'] = $authInfoo['p_level'] + 1;                 //上节点ID
			$tp_path = $authInfoo['tp_path'];
		}else{
			$this->error('上级会员不存在！');
			exit;
		}
		unset($authInfoo,$mappp);
 		$TPL = $FID['treeplace'];
		$where = array();
		$where['father_id'] = $data['father_id'];
		$where['treeplace'] = $TPL;
		$rs = $fck->where($where)->field('id')->find();
		if ($rs){
			$this->error('该位置已经注册！');
			exit;
		}else{
			$data['treeplace'] = $TPL;
			if(strlen($tp_path)==0){
				$data['tp_path'] = $TPL;
			}else{
				$data['tp_path'] = $tp_path.",".$TPL;
			}
		}
		unset($rs,$where,$TPL);

		$fwhere = array();//检测帐号是否存在
		$fwhere['user_id'] = trim($_POST['UserID']);
		$frs = $fck->where($fwhere)->field('id')->find();
		if ($frs){
			$this->error('该会员编号已存在！');
			exit;
		}
		$kk = stripos($fwhere['user_id'],'-');
		if($kk){
			$this->error('会员编号中不能有扛(-)符号！');
			exit;
		}
		unset($fwhere,$frs);

		$errmsg="";
		if(empty($_POST['BankCard'])){
			$errmsg.="<li>银行卡号不能为空！</li>";
		}
		$huhu=trim($_POST['UserName']);
		if(empty($huhu)){
			$errmsg.="<li>请填写开户姓名！</li>";
		}
// 		if(!preg_match('/^[\x7f-\xff]+$/', $huhu)){
// 		    $this->error('开户姓名必须是中文！');
// 		    exit;
// 		}
		if(empty($_POST['UserCode'])){
			$errmsg.="<li>请填写身份证号码！</li>";
		}
		if(empty($_POST['UserTel'])){
			$errmsg.="<li>请填写电话号码！</li>";
		}
		if(empty($_POST['f4'])){
			$errmsg.="<li>请填写单数</li>";
		}
		$usercc=trim($_POST['UserCode']);

		if(strlen($_POST['Password']) < 1 or strlen($_POST['Password']) > 16){
			$this->error('密码应该是1-16位！');
			exit;
		}
		$usercc=trim($_POST['UserCode']);
		if(!preg_match("/\d{17}[\d|X]|\d{15}/", $_POST['UserCode'])){
			$errmsg.="<li>身份证号码格式不正确！</li>";
		}		
		$s_err = "<ul>";
		$e_err = "</ul>";
		if(!empty($errmsg)){
			$out_err = $s_err.$errmsg.$e_err;
			$this->error($out_err);
			exit;
		}

		$uLevel = $_POST['u_level'];
		$fee  = M ('fee') -> find();
		$s    = $fee['s9'];
		$s2 = explode('|',$fee['s2']);
		$s9 = explode('|',$fee['s9']);
		$s15 = explode('|',$fee['s15']);
		$F4     = $_POST['f4'];//认购单数
		$ul     = $s9[$uLevel];
		$gp     = $s9[$uLevel];

			$Money = explode('|',C('Member_Money'));  //注册金额数组
		//当前日期  
        $sdefaultDate = date("Y-m-d");
        //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期  
        $first=1;  
        //获取当前周的第几天 周日是 0 周一到周六是 1 - 6  
        $w=date('w',strtotime($sdefaultDate));
        //获取本周开始日期，如果$w是0，则表示周日，减去 6 天  
        // $week_start=date('Y-m-d',strtotime("$sdefaultDate -".($w ? $w - $first : 6).' days'));
        $week_strt=strtotime("$sdefaultDate -".($w ? $w - $first : 6).' days');
        $booleanID = true;
        $new_userid = rand(10000000,99999999);
        while ($booleanID) {
            $bResult=$fck->where("user_id='".$new_userid."'")->find();
            if ($bResult) {
                $new_userid = rand(10000000,99999999);
            } else {
                $booleanID = false;
            }
        
        }
		$sum=$F4*$ul;
		$data['user_id']             = trim($_POST['UserID']);
		$data['bind_account']        = '3333';
		$data['verify']              = '0';
		$data['status']              = 1;                             //状态(?)
		$data['type_id']             = '0';
		$data['last_login_time']     = time();                        //最后登录时间
		$data['login_count']         = 0;                             //登录次数
		$data['info']                = '信息';
		$data['name']                = '名称';
		$data['password']            = md5(trim($_POST['Password']));  //一级密码加密
		$data['passopen']            = md5(trim($_POST['PassOpen']));  //二级密码加密
		$data['pwd1']                = trim($_POST['Password']);       //一级密码不加密
		$data['pwd2']                = trim($_POST['PassOpen']);       //二级密码不加密

		$data['bank_name']           = $_POST['BankName'];             //银行名称
		$data['bank_card']           = $_POST['BankCard'];             //帐户卡号
		$data['user_name']           = $_POST['UserName'];             //姓名
		$data['nickname']			  = '';//$_POST['nickname'];  //昵称
		$data['bank_province']       = '';  //省份
		$data['bank_city']           = '';      //城市
		$data['bank_address']        = '';          //开户地址
		//$data['user_post']           = $_POST['UserPost']; 		   //
		$data['user_code']           = $_POST['UserCode'];             //身份证号码
		$data['user_address']        = '';          //联系地址
		$data['email']               = '';            //电子邮箱
		$data['qq']              	 = $_POST['qq'];            	   //微信
		$data['user_tel']            = $_POST['UserTel'];              //联系电话

		$data['is_pay']              = 0;                              //是否开通
		$data['vip4']              = 1; 
		$data['rdt']                 = time();                         //注册时间
		//$data['pdt']                 = time();
		$data['u_level']             = $uLevel+1;                      //注册等级
		$data['cpzj']                = $sum;                          //注册金额
		$data['_times']                  = $sdefaultDate;							//单量
		$data['f4']                  = $F4;							//单量
		$data['wlf']                 = 0;                             
		$data['is_fh']                 = 1;                              
		$data['is_sf']                 = 1;     
		$result = $fck->add($data);
		
		$relation    = M ('relation');  //对应关系表
		$temp_Uid=$fck->where("user_id='".$new_userid."'")->field("id")->find();
		$data1['uid']                 = $temp_Uid['id'];
		$data1['user_id']             = $_POST['UserID'];
		$data1['user_id_encrypt']     = $new_userid;
		$data1['bank_name']           = $_POST['BankName'];             //银行名称
		$data1['bank_card']           = $_POST['BankCard'];             //帐户卡号
		$data1['user_name']           = $_POST['UserName'];             //姓名
		$data1['nickname']			  = '';//$_POST['nickname'];  //昵称
		$data1['bank_province']       = $_POST['BankProvince'];  //省份
		$data1['bank_city']           = $_POST['BankCity'];      //城市
		$data1['bank_address']        = $_POST['BankAddress'];          //开户地址
		$data1['user_code']           = $_POST['UserCode'];             //身份证号码
		$data1['user_address']        = $_POST['UserAddress'];          //联系地址
		$data1['email']               = $_POST['UserEmail'];            //电子邮箱
		$data1['qq']              	 = $_POST['qq'];            	   //qq
		$data1['user_tel']            = $_POST['UserTel'];              //联系电话
		$result1 = $relation->add($data1);
		
		unset($data,$data1,$fck,$relation);
		if($result) {
			echo "<script>";
			echo "alert('恭喜您注册成功，您的账户编号：".trim($_POST['UserID'])."，请及时开通正式会员！');";
			echo "window.location='".__APP__."/Public/login/';";
			echo "</script>";
			exit;
		}else{
			$this->error('会员注册失败！');
			exit;
		}
	}
	// 递归判定位置是否有人注册
	public function positionRecuision($father_id,$father_name,$l = 0,$r = 0){
	    $data = array();
        $reWhere = array();
        $fck = M('fck');
        if ($l <= $r) {
            $reWhere['father_id']	= $father_id;
            $reWhere['treeplace']	= 0;
            $fckL = $fck->where($reWhere)->field('id,user_id,l,r')->find();
            if (!$fckL) {
                $data['user_id'] = $father_name;
                $data['treeplace'] = 0;
                return $data;
            } else {
                $data = $this->positionRecuision($fckL['id'],$fckL['user_id'],$fckL['l'],$fckL['r']);
            }
        } else {
            $reWhere['father_id']	= $father_id;
            $reWhere['treeplace']	= 1;
            $fckR = $fck->where($reWhere)->field('id,user_id,l,r')->find();
            if (!$fckR) {
                $data['user_id'] = $father_name;
                $data['treeplace'] = 1;
                return $data;
            } else {
                $data = $this->positionRecuision($fckR['id'],$fckR['user_id'],$fckR['l'],$fckR['r']);
            }
        }
        return $data;
	}
	
    // 递归检测报单中心
	public function find_shopid($user_id) {
	    $member = M('fck');
	    $mappp  = array();
	    $mappp['user_id'] = $user_id;
	    $authInfoo = $member->where($mappp)->field('id,user_id,user_name,is_agent,re_id,re_name')->find();
	    if ($authInfoo['is_agent'] == 2){
	        return $authInfoo;
	    } else {
	        $authInfoo = $this->find_shopid($authInfoo['re_name']);
	        return $authInfoo;
	    }
	}
	
	//生成会员编号
	private function _getUserID(){
		$fck = M('fck');
//		$fee = M('fee');
//		$fee_rs = $fee->field('us_num')->find(1);
//		$us_num = $fee_rs['us_num'];
//		$first_n = 800000000;
//		$mynn = $first_n+$us_num;
		
		$mynn = ''.rand(1000000,9999999);
		
//		if($us_num<10){
//			$mynn = "00000".$us_num;
//		}elseif($us_num<100){
//			$mynn = "0000".$us_num;
//		}elseif($us_num<1000){
//			$mynn = "000".$us_num;
//		}elseif($us_num<10000){
//			$mynn = "00".$us_num;
//		}elseif($us_num<100000){
//			$mynn = "0".$us_num;
//		}else{
//			$mynn = $us_num;
//		}
		$fwhere['user_id'] = $mynn;
		$frss = $fck->where($fwhere)->field('id')->find();
		if ($frss){
			return $this->_getUserID();
		}else{
			unset($fck,$fee);
			return $mynn;
		}
	}
	
	//判断最左区
	public function pd_left_us($uid,&$tp){
		$fck = M('fck');
		$c_l = $fck->where('father_id='.$uid.' and treeplace='.$tp.'')->field('id')->find();
		if($c_l){
			$n_id = $c_l['id'];
			$tp = 0;
			$ren_id = $this->pd_left_us($n_id,$tp);
		}else{
			$ren_id = $uid;
		}
		unset($fck,$c_l);
		return $ren_id;
	}
	
	//
	public function find_agent(){
		$fck = M('fck');
		$where = "is_agent=2 and is_pay>0";
		$s_echo = '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tab1"><tr><td>';
		$e_echo = '</td></tr></table>';
		$m_echo = "";
		$c_l = $fck->where($where)->field('user_id,user_name,shop_a')->select();
		foreach($c_l as $ll){
			$m_echo .= "<li><b>".$ll['user_id']."</b>(".$ll['user_name'].")<br>".$ll['shop_a']."</li>";
		}
		unset($fck,$c_l);
		echo $s_echo.$m_echo.$e_echo;
	}
	
	
	
	// 找回密码1
	public function find_pw() {
		$_SESSION['us_openemail']="";
		$this->display('find_pw');
	}

	// 找回密码2
	public function find_pw_s() {
		if(empty($_SESSION['us_openemail'])){
			if(empty($_POST['us_name'])&&empty($_POST['us_email'])) {
				$_SESSION = array();
				$this->display('Public:LinkOut');
				return;
			}
			$ptname=$_POST['us_name'];
			$us_email=$_POST['us_email'];
			$fck = M('fck');
			$rs=$fck->where("user_id='".$ptname."'")->field('id,email,user_id,user_name,pwd1,pwd2')->find();
			if ($rs==false){
				$errarry['err']='<font color=red>注：找不到此会员编号！</font>';
				$this->assign('errarry',$errarry);
				$this->display('find_pw');
			}else{
				if($us_email<>$rs['email']){
					$errarry['err']='<font color=red>注：邮箱验证失败！</font>';
					$this->assign('errarry',$errarry);
					$this->display('find_pw');
				}else{

					$passarr=array();
					$passarr[0]=$rs['pwd1'];
					$passarr[1]=$rs['pwd2'];
					
					$title = '感谢您使用密码找回';
					
					$body="<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"font-size:12px; line-height:24px;\">";
					$body=$body."<tr>";
					$body=$body."<td height=\"30\">尊敬的客户:".$rs['user_name']."</td>";
					$body=$body."</tr>";
					$body=$body."<tr>";
					$body=$body."<td height=\"30\">你的账户编号:".$rs['user_id']."</td>";
					$body=$body."</tr>";
					$body=$body."<tr>";
					$body=$body."<td height=\"30\">一级密码为:".$rs['pwd1']."</td>";
					$body=$body."</tr>";
					$body=$body."<tr>";
					$body=$body."<td height=\"30\">二级密码为:".$rs['pwd2']."</td>";
					$body=$body."</tr>";
					$body=$body."此邮件由系统发出，请勿直接回复。<br>";
					$body=$body."</td></tr>";
					$body=$body."<tr>";
					$body=$body."<td height=\"30\" align=\"right\">".date("Y-m-d H:i:s")."</td>";
					$body=$body."</tr>";
					$body=$body."</table>";

					$this->send_email($us_email,$title,$body);

					$_SESSION['us_openemail']=$us_email;
					$this->find_pw_e($us_email);
				}
			}
		}else{
			$us_email=$_SESSION['us_openemail'];
			$this->find_pw_e($us_email);
		}
	}

	// 找回密码3
	public function find_pw_e($us_email) {
		$this->assign('myask',$us_email);
		$this->display('find_pw_s');
	}
	
	public function send_email($useremail,$title='',$body='')
	{

		require_once "stemp/class.phpmailer.php";
		require_once "stemp/class.smtp.php";

		$arra=array();

		$mail = new PHPMailer();
		$mail->IsSMTP();                  // send via SMTP
		$mail->Host  = "smtp.163.com";   // SMTP servers
		$mail->SMTPAuth = true;           // turn on SMTP authentication
		$mail->Username = "yuyangtaoyecn";     // SMTP username     注意：普通邮件认证不需要加 @域名
		$mail->Password = "yuyangtaoyecn666";          // SMTP password
		$mail->From  = "yuyangtaoyecn@163.com";        // 发件人邮箱
		$mail->FromName =  "传奇梦";    // 发件人
		$mail->CharSet  = "utf-8";              // 这里指定字符集！
		$mail->Encoding = "base64";
		$mail->AddAddress("".$useremail."","".$useremail."");    // 收件人邮箱和姓名
		//$mail->AddAddress("119515301@qq.com","text");    // 收件人邮箱和姓名
		$mail->AddReplyTo("".$useremail."","163.com");
		$mail->IsHTML(true);    // send as HTML
		$mail->Subject  = $title; // 邮件主题
		$mail->Body = "".$body."";// 邮件内容
		$mail->AltBody ="text/html";
//		$mail->Send();

		if(!$mail->Send())
		{
		echo "Message could not be sent. <p>";
		echo "Mailer Error: " . $mail->ErrorInfo;
		exit;
		}
		//echo "Message has been sent";
	}
	

	/**
	 * Generates an UUID
	 *
	 * @return     string  the formatted uuid
	 */
	function uuid()
	{
	    $chars = md5(uniqid(mt_rand(), true));
	    $uuid  = substr($chars,0,8);
	    return $uuid;
	}
}
?>