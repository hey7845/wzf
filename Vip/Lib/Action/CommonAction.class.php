<?php
class CommonAction extends CheFieldAction {

	public function _initialize() {
		$this->_inject_check(1);//调用过滤函数

		// 用户权限检查
		if (C ( 'USER_AUTH_ON' ) && !in_array(MODULE_NAME,explode(',',C('NOT_AUTH_MODULE')))) {
			import ( '@.ORG.RBAC' );
			if (! RBAC::AccessDecision ()) {
				//检查认证识别号
				if (! $_SESSION [C ( 'USER_AUTH_KEY' )]) {
					//跳转到认证网关
					redirect ( PHP_FILE . C ( 'USER_AUTH_GATEWAY' ) );
				}
				// 没有权限 抛出错误
				if (C ( 'RBAC_ERROR_PAGE' )) {
					// 定义权限错误页面
					redirect ( C ( 'RBAC_ERROR_PAGE' ) );
				} else {
					if (C ( 'GUEST_AUTH_ON' )) {
						$this->assign ( 'jumpUrl', PHP_FILE . C ( 'USER_AUTH_GATEWAY' ) );
					}
					// 提示错误信息
					$this->error ( L ( '_VALID_ACCESS_' ) );
				}
			}
		}
	}
	    public function check_prem($prem){
        $prem_array=C('prem_array');
        $number=0;
        foreach ($prem_array as $key=>$value){
            if($value==$prem){
                $number=$key;break;
            }
        }
        if($number>0){
            $id=$_SESSION[C('USER_AUTH_KEY')];
            $prem=M("fck")->getFieldbyid($id,"prem");
            if(strpos($prem, ",{$number},")===false){
                $this->error("权限不足!");exit;
            }
        }
        return true;
    }

	protected function _Gly_checkUser(){
		//后台权限
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$_SESSION = array();
			$bUrl = __APP__.'/Public/login';
			$this->_boxx($bUrl);
			exit;
		}
		$fck = M('fck');
		$mapp				=   array();
		$mapp['id']			= $_SESSION[C('USER_AUTH_KEY')];
		$mapp['is_jb']	= array('gt',0);
		$field = 'id,user_id';
        $rs = $fck->where($mapp)->field($field)->find();
        if(!$rs){
        	$_SESSION = array();
			$bUrl = __APP__.'/Public/login';
			$this->_boxx($bUrl);
			exit;
        }
		unset($fck,$mapp,$rs);
	}

	protected function _Admin_checkUser(){
		//后台权限
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$_SESSION = array();
			$bUrl = __APP__.'/Public/login';
			$this->_boxx($bUrl);
			exit;
		}
		$fck = M('fck');
		$admid			= $_SESSION[C('USER_AUTH_KEY')];
//		$mapp				=   array();
//		$mapp['id']			= $_SESSION[C('USER_AUTH_KEY')];
//		$mapp['is_boss']	= array('gt',0);
		$field = 'id,user_id';
        $rs = $fck->where('id='.$admid.' and (is_boss>0 or (is_boss=0 ))')->field($field)->find();
        if(!$rs){
        	$_SESSION = array();
			$bUrl = __APP__.'/Public/login';
			$this->_boxx($bUrl);
			exit;
        }
		unset($fck,$mapp,$rs);
	}
	// 检查用户是否登录
	protected function _checkUser() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$this->LinkOut();
			exit;
		}
// 		$this->check_order_isout();
		
		$this->_user_mktime($_SESSION['UserMktimes']);
		$User = M ('fck');


		//生成认证条件
        $mapp            =   array();
		// 支持使用绑定帐号登录
		//管理员编号，证明
		$mapp['id']    = $_SESSION[C('USER_AUTH_KEY')];
		$mapp['user_id']	= $_SESSION['loginUseracc'];
		$field = 'user_id,password,user_type,is_lock';
        $authInfoo = $User->where($mapp)->field($field)->find();
        if(false == $authInfoo) {
            $this->LinkOut();
			exit;
        }else {
       		if($authInfoo['is_lock'] == 1){
				echo "<script language=javascript>";
				echo 'alert("==您的账户已锁定！==");';
				echo "</script>";
				$this->LinkOut();
			}
			//是否允许一个用户同时多人在线！
        	$fee = M ('fee');
			$fee_rs = $fee->field('i3,str27')->find(1);
			$user_type = 0;
			if ($fee_rs['user_type'] == 1){
				if ($_SESSION['login_user_type'] != $authInfoo['user_type']){
					$user_type = 1;
				}
			}
			if($fee_rs['i3'] == 1){
				if ($_SESSION[C('USER_AUTH_KEY')] != 1){
					echo "<script language=javascript>";
					echo 'alert("=='.$fee_rs['str27'].'==");';
					echo "</script>";
					$this->LinkOut();
                }

			}
			unset($fee,$fee_rs);
        	$mpwd = md5($authInfoo['user_id'].'wodetp_new_1012!@#' . $authInfoo['password'] . $_SERVER['HTTP_USER_AGENT']);
			if ($mpwd != $_SESSION['login_sf_list_u'] || $user_type == 1){
//				$this->LinkOut();
//				exit;
			}
		}
	}
	//检测登录是否超时
	protected function _user_mktime($onlinetime){
		$new_time = mktime();
		if ($new_time - $onlinetime > '1200'){
			$this->LinkOut();
			exit;
		}else{
			$_SESSION['UserMktimes'] = mktime();
		}
	}
	
	//检测登录是否超时
	protected function check_order_isout(){
		$id  = $_SESSION[C('USER_AUTH_KEY')];
		$fck = M ('fck');
		$cashpp = M ('cashpp');
		$nowdate = strtotime(date('c'));
		
		$fee = M('fee');
		$fee_rs = $fee->field('s12,str9')->find();
		$fee_s12 = $fee_rs['s12'];
		$fee_str9 = $fee_rs['str9'];
		
		$buylasttime = $nowdate - $fee_s12*3600;
		$selllasttime = $nowdate - $fee_str9*3600;
		
		if($id>1){
			//未打款检查
			$mapp            =   array();
			$mapp['uid']    = $id;
			$mapp['is_buy']	= 1;
			$mapp['is_pay'] = 0;
			$mapp['bdt']	= array('lt',$buylasttime);
			$buych = $cashpp ->where($mapp)->select();
			if($buych){
				$fck->execute("UPDATE __TABLE__ SET `is_lock`=1 where id>1 and id=".$id);
				$this->LinkOut();
			}
			
			//收款未确认检查
			$map           =   array();
			$map['bid']    = $id;
			$map['is_buy'] = 2;
			$map['is_pay'] = 0;
			$map['bdt']	   = array('lt',$selllasttime);
			$sellch = $cashpp ->where($map)->select();
			if($sellch){
				$fck->execute("UPDATE __TABLE__ SET `is_lock`=1 where id>1 and id=".$id);
				$this->LinkOut();
			}
		}
	}
	
	public function LinkOut(){
		$_SESSION = array();
		$this->display('Public:LinkOut');
	}
	//处理结果函数 (结果，事件，跳转url，跳转时间单位为秒)
	protected function _box($dz=0,$list='',$url='',$ms=3,$cgs=0){
		if ($dz == 1){
			$dz = '操作成功!';
		}else{
			$dz = '操作失败!';
		}
		if ($cgs == 1){
			$cgs = 'parent.';
		}else{
			$cgs = '';
		}
		$lists = array();
		$lists['Title'] = $list;
		$lists['Url'] = $url;
		$lists['ms'] = $ms;
		$lists['dz'] = $dz;
		$lists['cgs'] = $cgs;
		$this->assign('list',$lists);
		$this->display('Public:box');
	}
	//页面跳转
	protected function _boxx($url=''){
		echo "<script> location.href='{$url}' </script>";
	}
	//过滤函数
	protected function _inject_check($sql_str=0) {
			//合并$_POST 和 $_GET
			foreach ($_GET as $get_key => $get_var){
				$get[strtolower($get_key)] = $get_var;
			}
			/* 过滤所有POST过来的变量 */
			foreach ($_POST as $post_key => $post_var){
			  $post[strtolower($post_key)] = $post_var;
			}
		//需要过滤的数据
		if ($sql_str == 0){
			$GetPost = 'select|insert|update|delete|union|into|load_file|outfile|and';
		}else{
			$GetPost = 'select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|\(|\)|\<|\>|and|chr|char';
		}
		foreach ($post as $post_key => $sql_str){
			$check = preg_match($GetPost,$sql_str);// 进行过滤
			if ($check){
				$this->error('输入内容不合法，请重新输入！');
				exit();
			}
		}
		foreach ($get as $post_key => $sql_str){
			$check = preg_match($GetPost,$sql_str);// 进行过滤
			if ($check){
				$this->error('输入内容不合法，请重新输入！');
				exit();
			}
		}

	}

	protected function _levelConfirm(&$HYJJ,$HYid=1){
		$HYJJ = array();
		$User = M ('fee');
		$fee_rs = $User->find(1);
		// $HYJJ=array('普通会员','铜级','银级','金级','蓝宝石','绿宝石','红宝石','珍珠','钻石','双钻石','黑钻石','皇冠');
		$fee_s1 = explode('|',$fee_rs['s10']);
		// $fee_s1 = array('普通会员','铜级','银级','金级','蓝宝石','绿宝石','红宝石','珍珠','钻石','双钻石','黑钻石','皇冠');
		$HYJJ[1] = $fee_s1[0];
		// $HYJJ[2] = $fee_s1[1];
		// $HYJJ[3] = $fee_s1[2];
		// $HYJJ[4] = $fee_s1[3];
		// $HYJJ[5] = $fee_s1[4];
 	// 	$HYJJ[6] = $fee_s1[5];
		// $HYJJ[7] = $fee_s1[6];
		// $HYJJ[8] = $fee_s1[7];
		// $HYJJ[9] = $fee_s1[8];
		// $HYJJ[10] = $fee_s1[9];
		// $HYJJ[11] = $fee_s1[10];
		// $HYJJ[12] = $fee_s1[11];
	}
	
	protected function _getLevelConfirm(&$HYJJ,$HYid=1){
		$HYJJ = array();
		$HYJJ[0] = "普通会员";
		$HYJJ[1] = "一星";
		$HYJJ[2] = "二星";
		$HYJJ[3] = "三星";
		$HYJJ[4] = "四星";
	}

	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;
	}
	/**
     +----------------------------------------------------------
	 * 取得操作成功后要返回的URL地址
	 * 默认返回当前模块的默认操作
	 * 可以在action控制器中重载
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	function getReturnUrl() {
		return __URL__ . '?' . C ( 'VAR_MODULE' ) . '=' . MODULE_NAME . '&' . C ( 'VAR_ACTION' ) . '=' . C ( 'DEFAULT_ACTION' );
	}

	/**
     +----------------------------------------------------------
	 * 根据表单生成查询条件
	 * 进行列表过滤
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param string $name 数据对象名称
     +----------------------------------------------------------
	 * @return HashMap
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	protected function _search($name = '') {
		//生成查询条件
		if (empty ( $name )) {
			$name = $this->getActionName();
		}
		$name=$this->getActionName();
		$model = D ( $name );
		$map = array ();
		foreach ( $model->getDbFields () as $key => $val ) {
			if (isset ( $_REQUEST [$val] ) && $_REQUEST [$val] != '') {
				$map [$val] = $_REQUEST [$val];
			}
		}
		return $map;
	}

	/**
     +----------------------------------------------------------
	 * 根据表单生成查询条件
	 * 进行列表过滤
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param Model $model 数据对象
	 * @param HashMap $map 过滤条件
	 * @param string $sortBy 排序
	 * @param boolean $asc 是否正序
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	 //==============================================分页函数
	protected function _list($model, $map, $sortBy = '', $asc = false) {
		//排序字段 默认为主键名
		if (isset ( $_REQUEST ['_order'] )) {
			$order = $_REQUEST ['_order'];
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : $model->getPk ();
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		//取得满足条件的记录数
		$count = $model->where ( $map )->count ( 'id' );
		if ($count > 0) {
			import ( "@.ORG.Page" );
			//创建分页对象
			if (! empty ( $_REQUEST['listRows'] )) {
				$listRows = $_REQUEST['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $count, 10 );
			//分页查询数据

			$voList = $model->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
			//echo $model->getlastsql();
			//分页跳转的时候保证查询条件
			foreach ( $map as $key => $val ) {
				if (! is_array ( $val )) {
					$p->parameter .= "$key=" . urlencode ( $val ) . "&";
				}
			}
			//会员等级 开始=================
			$i = 1;
			$HYJJ = array();
			$HYoo = array();
			$this->_levelConfirm($HYJJ,1);
			foreach($voList as $voo){
				$HYoo[$i][0] = $HYJJ[$voo['u_level']];
				$i++;
			}
			$this->assign('voo',$HYoo);
			//会员等级 结束=================

			//分页显示
			$page = $p->show ();
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式
			//模板赋值显示
			$this->assign ( 'list', $voList );
			$this->assign ( 'sort', $sort );
			$this->assign ( 'order', $order );
			$this->assign ( 'sortImg', $sortImg );
			$this->assign ( 'sortType', $sortAlt );
			$this->assign ( "page", $page );
		}
		Cookie::set ( '_currentUrl_', __SELF__ );
		return;
	}

	protected function _2Mal($name=0,$wei=0) {
		//格式化数字，保留小数位数
		$map = sprintf('%.'.$wei.'f', (float)$name);
		return $map;
	}
	public function _Config_name() {
		header("Content-Type:text/html; charset=utf-8");
		//调用系统参数
		$System_namex    = C('System_namex');        //系统名字
		//$System_bankx    = C('System_bankx');        //银行名字
		$User_namex      = C('User_namex');
		$Nick_namex      = C('Nick_namex');
		//$Member_Level    = C('Member_Level');       //会员级别名称
		//$Member_Money    = C('Member_Money');       //注册金额
		//$Member_Single   = C('Member_Single');      //会员级别单数



		$this->assign ('System_namex',$System_namex);
		//$this->assign ('System_bankx',$System_bankx);
		$this->assign ('User_namex',$User_namex);
		$this->assign ('Nick_namex',$Nick_namex);
		//$this->assign ('Member_Level',$Member_Level);
		//$this->assign ('Member_Money',$Member_Money);
		//$this->assign ('Member_Single',$Member_Single);
	}
	
	//双轨小公排
	public function gongpaixtsmall($uid){
		$fck = M ('fck');
		$mouid=$uid;
		$field = 'id,user_id,p_level,p_path,u_pai';
		$where = 'is_pay>0 and (p_path like "%,'.$mouid.',%" or id='.$mouid.')';
	
		$re_rs = $fck ->where($where)->order('p_level asc,u_pai asc')->field($field)->select();
		$fck_where = array();
		foreach($re_rs as $vo){
			$faid=$vo['id'];
			$fck_where['is_pay']   = array('egt',0);
			$fck_where['father_id']   = $faid;
			$count = $fck->where($fck_where)->count();
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
	

	protected function _cheakPrem()
    {
        //权限
        $fck = M ('fck');
        $id = $_SESSION[C('USER_AUTH_KEY')];
        $frs = $fck ->field('prem') ->find($id);
        $arr = explode(',',$frs['prem']);
        for ($i=1;$i<=30;$i++){
            if (in_array($i,$arr)){
                $arss[$i] = 1;
            }else{
                $arss[$i] = 0;
            }
        }
        return $arss;
    }
    
	//======================================奖金结算
    public function  _clearing(){
		//参数
		$times = M ('times');  //结算时间表
		$fck = D ('Fck');

		//以下写进资金表
		$nowdate = strtotime(date('Y-m-d'))+3600*24-1;
//		$nowdate = time();
        $settime_two['benqi'] = $nowdate;
        $settime_two['type']  = 0;
        $trs = $times->where($settime_two)->find();  //找出本期结算时间
        if (!$trs){  //不存在本期
            $rs3 = $times->where('type=0')->order('id desc')->find(); //找出上期结算时间
            if ($rs3){
				$data['shangqi']  = $rs3['benqi'];
				$data['benqi']    = $nowdate;
				$data['is_count'] = 0;
				$data['type']     = 0;
			}else{  //不存在上期,创建第一期
				$data['shangqi']  = strtotime('2010-01-01');
				$data['benqi']    = $nowdate;
				$data['is_count'] = 0;
				$data['type']     = 0;
			}
			unset($rs3);
			
			$times->add($data);  // times表 添加本期结算

		}else{
			$data['shangqi'] = $trs['shangqi'];
			$data['benqi']   = $trs['benqi'];
		}  //整理结算时间

		$times_time = $data['shangqi'];

		$fck->execute("UPDATE __TABLE__ SET `b0`=b1+b2+b3");

		//奖金汇总
		$fck->quanhuizong();

		$bonus = M ('bonus');
		$twhere = array();
		$twhere['type'] = 0;
		$trs_two = $times->where($twhere)->order('id desc')->field('id')->find();  //找出times表所有记录并倒序,只取id字段

		$where_two = array();
		$data2 = array();
		$where_two['did'] = $trs_two['id'];  //本期结算 times表id
		$fwhere = array();
		$fwhere['b0'] = array('neq',0);       //这一期总奖金大于0
		$fwhere['is_tj'] = array('eq',0);    //统计占用
		$fwhere['is_pay'] = array('gt',0);   //已开通的会员
		$rs = $fck ->where($fwhere)->field('*')->order('id asc')->select();
		foreach ($rs as $rss){
			$my_b = $rss['b0'];
			$my_id = $rss['id'];
			$myww = "id=".$my_id." and b0=".$my_b." and is_tj=0";
			$result = $fck -> execute("update __TABLE__ set zjj=zjj+b0,is_tj=1,agent_use=agent_use+".$my_b." where ".$myww);
			if($result){
				$where_two['uid'] = $rss['id'];
				$bonus_rs = $bonus->where($where_two)->find();  //查找是否存在本期结算记录
				if (!$bonus_rs){
					$data2['e_date']   = $data['benqi'];
					$data2['s_date']   = $data['shangqi'];
					$data2['user_id']  = $rss['user_id'];
					$data2['did']      = $trs_two['id'];
					$data2['uid']      = $rss['id'];
					$data2['b0']       = $rss['b0'];
					$data2['b1']       = $rss['b1'];
					$data2['b2']       = $rss['b2'];
					$data2['b3']       = $rss['b3'];
					$data2['b4']       = $rss['b4'];
					$data2['b5']       = $rss['b5'];
					$data2['b6']       = $rss['b6'];
					$data2['b7']       = $rss['b7'];
					$data2['b8']       = $rss['b8'];
					$data2['b9']       = $rss['b9'];
					$bonus->add($data2);  // bonus 奖金表新增本期记录
				}else{
					$sql  = "`b0`=b0+". $rss['b0'] .",";
					$sql .= "`b1`=b1+". $rss['b1'] .",";
					$sql .= "`b2`=b2+". $rss['b2'] .",";
					$sql .= "`b3`=b3+". $rss['b3'] .",";
					$sql .= "`b4`=b4+". $rss['b4'] .",";
					$sql .= "`b5`=b5+". $rss['b5'] .",";
					$sql .= "`b6`=b6+". $rss['b6'] .",";
					$sql .= "`b7`=b7+". $rss['b7'] .",";
					$sql .= "`b8`=b8+". $rss['b8'] .",";
					$sql .= "`b9`=b9+". $rss['b9'] ."";
					$bonus -> query("update __TABLE__ set ". $sql ." where `id`=". $bonus_rs['id']);  //bonus 奖金表本期记录++
				}
				$fck -> query("update __TABLE__ set b0=0,b1=0,b2=0,b3=0,b4=0,b5=0,b6=0,b7=0,b8=0,b9=0,is_tj=0 where id=".$my_id);
			}
        }
		$fck->_addBonus();
		unset($fck,$times,$trs,$settime_two,$bonus,$twhere,$trs_two,$data2,$fwhere,$rs,$data);
	}
    


    //引用编辑器
	public function us_fckeditor($inputid,$value,$height,$width='100%')
	{
		//引用编辑器库类
		import ( "@.ORG.FCKeditor.fckeditor" );  //导入分页类
//		vendor("FCKeditor.fckeditor");
		$editor= new FCKeditor(); //实例化FCKeditor对象
		$editor->Width=$width;//设置编辑器实际需要的宽度。此项省略的话，会使用默认的宽度。
		$editor->Height=$height;//设置编辑器实际需要的高度。此项省略的话，会使用默认的高度。
		$editor->Value=$value;//设置编辑器初始值。也可以是修改数据时的设定值。可以置空。
		$editor->InstanceName=$inputid;//设置编辑器所在表单内输入标签的id与name，即< input>标签的id与name。此处假 //设为comment.此处不可省，也要保持唯一性。表单上传到服务器处理程序后，即可通过$_POST['comment']来读取。
		$html=$editor->Createhtml();//创建在线编辑器html代码字符串,并赋值给字符串变量$html.
		$this->assign('html',$html);//将$html的值赋给模板变量$html.在模板里通过{$html}可以直 接引用。
	}

	
}
?>