<?php 

class GuzhiAction extends CommonAction {
	
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
				$_SESSION['Urlszpass'] = 'MyssbuyGuZhi';
				$bUrl = __URL__.'/buyGuZhi';//
				$this->_boxx($bUrl);
				break;
		
			case 2;
				$_SESSION['Urlszpass'] = 'MyssbuyGuZhiList';
				$bUrl = __URL__.'/buyGuZhiList';//
				$this->_boxx($bUrl);
				break;
				
		  case 3;
				$_SESSION['Urlszpass'] = 'MyssadminBuyGuZhiList';
				$bUrl = __URL__.'/adminBuyGuZhiList';//
				$this->_boxx($bUrl);
				break;
			
			default;
				$this->error('二级密码错误!');
				exit;
		}
	}

	//购买
	public function buyGuZhi($Urlsz=0){
		if ($_SESSION['Urlszpass'] == 'MyssbuyGuZhi'){
			$blist = M('blist');
			$fck = M('fck');
			$fee = M('fee');
			
			$uid = $_SESSION[C('USER_AUTH_KEY')];
			
			$fee_rs = $fee->field('str9,str2')->find();
			$per = $fee_rs['str2'];
			$str9 = explode("|",$fee_rs['str9']);
			
			$rs = $fck->where('id='.$uid)->field('agent_gp,u_level')->find();
			$feng_buy = $str9[$rs['u_level']-1];
			
			$map = array();
			$map['uid'] = $uid;
			$t_totle = (int)$blist->where($map)->sum('nums');
			$cj_totle = (int)$blist->where($map)->sum('cj_nums');
			
			$buy_totle = (int)$blist->where("uid=".$uid." and type=0")->sum('nums');
			$cj_totle_ss = (int)$blist->where("uid=".$uid." and type=0")->sum('cj_nums');
			$have_buy = $feng_buy - ($buy_totle-$cj_totle_ss);
			if($have_buy < 0){
				$have_buy = 0;
			}
			
			$tt = mktime();
				
			$this->assign('rs',$rs);
			$this->assign('per',$per);
			$this->assign('t_totle',$t_totle);
// 			$this->assign('cj_totle',$cj_totle);
			$this->assign('tt',$tt);
			$this->assign('have_buy',$have_buy);

			unset($tiqu,$fck,$where,$ID,$field,$rs);
			$this->display ();
			return;
		}else{
			$this->error ('错误1!');
			exit;
		}
	}
	
	
	public function buyGuZhiAc(){
		$blist = M('blist');
		$fck = D('Fck');
		$fee = M('fee');
			
		$uid = $_SESSION[C('USER_AUTH_KEY')];
			
		$fee_rs = $fee->field('str9,str2')->find();
		$per = $fee_rs['str2'];
		$str9 = explode("|",$fee_rs['str9']);
		
		$rs = $fck->where('id='.$uid)->field('agent_gp,u_level,is_aa')->find();
		$feng_buy = $str9[$rs['u_level']-1];
		
		if($rs['is_aa'] == 1){
			$this->error("您的分红已出局，请用您的复投账户来购买股指手");
			exit;
		}
		
		
		$buy_totle = (int)$blist->where("uid=".$uid." and type=0")->sum('nums');
		$cj_totle = (int)$blist->where("uid=".$uid." and type=0")->sum('cj_nums');
		
		$have_buy = $feng_buy - ($buy_totle-$cj_totle);
		if($have_buy < 0){
			$have_buy = 0;
		}
		
		$nums = $_POST['nums'];
		$prices = $_POST['prices'];
		$bz      = $_POST['bz'];
		
		if($nums <= 0 || $nums == ""){
			$this->error("请输入购买数额");
			exit;
		}
		
	   if($prices <= 0 || $prices == ""){
			$this->error("参数错误，请刷新页面");
			exit;
		}
		
		if($nums > $have_buy ){
			$this->error("您当前只能在购买".$have_buy."(手)股指");
			exit;
		}
		
		
		if($rs['agent_gp'] < $prices ){
			$this->error("您的回购账户不足！");
			exit;
		}
		
		$res = $fck->execute("update __TABLE__ set agent_gp=agent_gp-".$prices.",agent_lock=agent_lock+".$nums.",gp_num=gp_num+".$nums." where id=".$uid); //agent_lock,gp_num我的股手
		if($res){
			$data = array();
			$data['uid'] = $uid;
			$data['rdt'] = time();
			$data['nums'] = $nums;
			$data['type'] = 0;
			$data['prices'] = $prices;
			$data['bz'] = $bz;
			
			$ss = $blist->add($data);
			
			if($ss){
			
				$this->addSellLian($nums);
				
				$fck->hgj($uid,$prices);  // 回购奖
				
				$_SESSION['Urlszpass'] = 'MyssbuyGuZhiList';
				$bUrl = __URL__.'/buyGuZhiList';
			    $this->_box(1,'购买成功！',$bUrl,1);
			    exit;
			}else{
				$this->error("购买失败");
				exit;
			}
			
		}else{
			$this->error("购买失败");
			exit;
			
		}
		
	}
	
	
	//股指明细
	public function buyGuZhiList(){
		if ($_SESSION['Urlszpass'] == 'MyssbuyGuZhiList'){
			$blist = M('blist');
			$fck = M('fck');
			
			$uid = $_SESSION[C('USER_AUTH_KEY')];
			$map['uid'] = $uid;
			
			$field  = '*';
			//=====================分页开始==============================================
			import ( "@.ORG.ZQPage" );  //导入分页类
			$count = $blist->where($map)->count();//总页数
			$listrows = C('ONE_PAGE_RE');//每页显示的记录数
			$Page = new ZQPage($count,$listrows,1);
			//===============(总页数,每页显示记录数,css样式 0-9)
			$show = $Page->show();//分页变量
			$this->assign('page',$show);//分页变量输出到模板
			$list = $blist->where($map)->field($field)->order('id desc')->page($Page->getPage().','.$listrows)->select();
			$this->assign('list',$list);//数据输出到模板
			//=================================================
			
			$t_totle = (int)$blist->where($map)->sum('nums');
			$cj_totle = (int)$blist->where($map)->sum('cj_nums');
			
			$rs = $fck->where('id='.$uid)->field('agent_gp')->find();
			
			$this->assign('rs',$rs);
			$this->assign('t_totle',$t_totle);
			$this->assign('cj_totle',$cj_totle);
			
			unset($tiqu,$fck,$where,$ID,$field,$rs);
			$this->display ();

		}else{
			$this->error ('错误!');
			exit;
		}
		
		
		
	}
	
	
	//股指明细
	public function adminBuyGuZhiList(){
		if ($_SESSION['Urlszpass'] == 'MyssadminBuyGuZhiList'){
			$blist = M('blist');
			$fck = M('fck');
			$map = "";
			$where = "";
			$UserID = $_REQUEST['UserID'];
			if (!empty($UserID)){
				import ( "@.ORG.KuoZhan" );  //导入扩展类
				$KuoZhan = new KuoZhan();
				if ($KuoZhan->is_utf8($UserID) == false){
					$UserID = iconv('GB2312','UTF-8',$UserID);
				}
				unset($KuoZhan);

				$map['user_id'] = array('eq',$UserID);
				
				$ress =  $fck->where($map)->field('id')->find();
				$UserID = urlencode($UserID);

			}

			if($ress){
				$where['uid'] = $ress['id'];
			}
			
				
			$field  = '*';
			//=====================分页开始==============================================
			import ( "@.ORG.ZQPage" );  //导入分页类
			$count = $blist->where($where)->count();//总页数
			$listrows = C('ONE_PAGE_RE');//每页显示的记录数
			$Page = new ZQPage($count,$listrows,1);
			//===============(总页数,每页显示记录数,css样式 0-9)
			$show = $Page->show();//分页变量
			$this->assign('page',$show);//分页变量输出到模板
			$list = $blist->where($where)->field($field)->order('id desc')->page($Page->getPage().','.$listrows)->select();
			
			$this->assign('list',$list);//数据输出到模板
			//=================================================
				
// 			$t_totle = (int)$blist->where($where)->sum('nums');
// 			$cj_totle = (int)$blist->where($where)->sum('cj_nums');
				
// 			$rs = $fck->where('id='.$uid)->field('agent_gp')->find();
				
// 			$this->assign('rs',$rs);
// 			$this->assign('t_totle',$t_totle);
// 			$this->assign('cj_totle',$cj_totle);
				
			unset($tiqu,$fck,$where,$ID,$field,$rs);
			$this->display ();
	
		}else{
			$this->error ('错误!');
			exit;
		}
	
	
	
	}
	
	public function addSellLian($nums=0){
		$gp = M('gp');
		
		$gp ->query("update __TABLE__ set gp_quantity=gp_quantity+".$nums.",turnover=turnover+".$nums." where id=1");

	}
	
	
	//销售量 
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
	
	
	/*走势图部分【开始】*/
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
		if($now_num!=$bj_n || $now_t!=$bj_d){
			$this->stock_past_due();
		}
		
		
		
		$id = $_SESSION[C('USER_AUTH_KEY')];
	

		$this->display ();
	}
	
	public function ChartsPrice() {
		$xml = M ('xml');
		$fengD = strtotime("2014-07-31");
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
	



}
?>