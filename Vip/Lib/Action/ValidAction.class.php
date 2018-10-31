<?php 
/**
 * 有效期控制器-续费
 * **/
class ValidAction extends CommonAction {
	
	function _initialize() {
		$this->_inject_check(0);//调用过滤函数
		$this->_checkUser();
		$this->_Config_name();//调用参数
		header("Content-Type:text/html; charset=utf-8");
		ob_clean();
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
				$_SESSION['Urlszpass'] = 'Myssvalid';
				$bUrl = __URL__.'/valid';//
				$this->_boxx($bUrl);
				break;
			default;
				$this->error('二级密码错误!');
				exit;
		}
	}

	//续费
	public function valid($Urlsz=0){
		if ($_SESSION['Urlszpass'] == 'Myssvalid'){
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
			$rs = $fck ->where($where)->find();
			$this->assign('rs',$rs);

			$fee = M('fee');
	    	$fee_rs = $fee->field('s7,s11')->find();
	    	$this->assign('s11',$fee_rs['s11']);

			unset($tiqu,$fck,$where,$ID,$field,$rs);
			$this->display ();
			return;
		}else{
			$this->error ('错误1!');
			exit;
		}
	}

	//消费处理
	public function validAC(){
		if ($_SESSION['Urlszpass'] == 'Myssvalid'){
			$uplevel = (int)$_POST['uplevel'];
			$fck = D('Fck');
			$xiaof = M('xiaof');
			if($uplevel<=0){
				$this->error ('申请参数错误！');
				exit;
			}
			if($uplevel>300){
				$this->error ('数量太大！');
				exit;
			}
			$where = array();
			$ID = $_SESSION[C('USER_AUTH_KEY')];
			//查询条件
			$where['id'] = $ID;
			$fck_rs = $fck ->where($where)->find();
			if (!$fck_rs){
				$this->error('没有该会员!');
				exit;
			}
			$inUserID = $fck_rs['user_id'];
			
			$fee = M('fee');
	    	$fee_rs = $fee->field('s11')->find();
	    	$s11 = $fee_rs['s11'];
	    	$ePoints = $uplevel*$s11;
			if ($ePoints > $fck_rs['agent_cash']){
				$this->error('账户余额不足！');
				exit;
			}
			
			$nowday = strtotime(date('Y-m-d'));
			$result = $fck->execute("update __TABLE__ set agent_cash=agent_cash-".$ePoints.",is_lockqd=0 where id=".$ID);
			if($result){
				$data				= array();
				$data['uid']		= $fck_rs['id'];
				$data['user_id']	= $inUserID;
				$data['money']		= $ePoints;
				$data['money_two']	= $uplevel;
				$data['epoint']		= $ePoints;
				$data['is_pay']		= 1;
				$data['rdt']		= time();
				$data['pdt']		= time();
				$xiaof->add($data);
				
				//见点奖
				$fck->jiandianjiang($fck_rs['p_path'],$inUserID);
				
				unset($xiaof,$fck,$data);
				
				$bUrl = __URL__.'/valid';
				$this->_box(1,'续费成功！',$bUrl,1);
				exit;
			}else{
				unset($xiaof,$fck);
				$this->error('续费失败！');
				exit;
			}
		}else{
			$this->error('错误1!');
			exit;
		}
	}

}
?>