<?php
class PlanAction extends CommonAction{
	function _initialize() {
		$this->_inject_check(0);//调用过滤函数
		header("Content-Type:text/html; charset=utf-8");
	}
	
	//================================================二级验证
	public function cody(){
		$UrlID  = (int) $_GET['c_id'];
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
	//====================================二级验证后调转页面
	public function codys(){
		$Urlsz	= $_POST['Urlsz'];
		if(empty($_SESSION['user_pwd2'])){
			$pass	= $_POST['oldpassword'];
			$fck   =  M ('fck');
		    if (!$fck->autoCheckToken($_POST)){
	            $this->error('页面过期请刷新页面!');
	            exit();
	        }
			if (empty($pass)){
				$this->error('二级密码错误!');
				exit();
			}
			$where =  array();
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
				$_SESSION['UrlPTPass'] = 'MyssPlanadmina';
				$bUrl = __URL__.'/admin_plan';
				$this->_boxx($bUrl);
				break;
			case 2;
				$_SESSION['UrlPTPass'] = 'MyssPlanadminb';
				$bUrl = __URL__.'/admin_planTwo';
				$this->_boxx($bUrl);
				break;
			case 3;
				$_SESSION['UrlPTPass'] = 'MyssPlanadminc';
				$bUrl = __URL__.'/admin_planThree';
				$this->_boxx($bUrl);
				break;
			case 4;
				$_SESSION['UrlPTPass'] = 'MyssPlanadmind';
				$bUrl = __URL__.'/admin_planFour';
				$this->_boxx($bUrl);
				break;
			case 5;
				$_SESSION['UrlPTPass'] = 'MyssPlanadmine';
				$bUrl = __URL__.'/admin_planFive';
				$this->_boxx($bUrl);
				break;
			default;
				$this->error('二级密码错误!');
				break;
		}
	}
	
	public function admin_plan(){
		$this->_Admin_checkUser();//后台权限检测
		$plan = M ('plan');
		$fid = 1;
		$this->plan_find($fid);
		$this->assign('fid',$fid);
		$webtitle = $this->plan_class();
		$this->assign('title',$webtitle[$fid]);
		$this->display('admin_plan');
	}
	public function admin_planTwo(){
		$this->_Admin_checkUser();//后台权限检测
		$plan = M ('plan');
		$fid = 2;
		$this->plan_find($fid);
		$this->assign('fid',$fid);
		$webtitle = $this->plan_class();
		$this->assign('title',$webtitle[$fid]);
		$this->display('admin_plan');
	}
	public function admin_planThree(){
		$this->_Admin_checkUser();//后台权限检测
		$plan = M ('plan');
		$fid = 3;
		$this->plan_find($fid);
		$this->assign('fid',$fid);
		$webtitle = $this->plan_class();
		$this->assign('title',$webtitle[$fid]);
		$this->display('admin_plan');
	}
	public function admin_planFour(){
		$this->_Admin_checkUser();//后台权限检测
		$plan = M ('plan');
		$fid = 4;
		$this->plan_find($fid);
		$this->assign('fid',$fid);
		$webtitle = $this->plan_class();
		$this->assign('title',$webtitle[$fid]);
		$this->display('admin_plan');
	}
	public function admin_planFive(){
		$this->_Admin_checkUser();//后台权限检测
		$plan = M ('plan');
		$fid = 5;
		$this->plan_find($fid);
		$this->assign('fid',$fid);
		$webtitle = $this->plan_class();
		$this->assign('title',$webtitle[$fid]);
		$this->display('admin_plan');
	}
	
	public function admin_planInsert(){
		$this->_Admin_checkUser();//后台权限检测
		$plan = M ('plan');
		$inid = (int)$_POST['id'];
		$content = stripslashes($_POST['content']);
		$data = array();
		$data['id'] = $inid;
		$data['content'] = $content;
		$rs = $plan->find($inid);
		if($rs){
			$list = $plan->save ($data);
		}else{
			$list = $plan->add ($data);
		}
		if ($list !== false) {
			$this->success ('保存成功!');
		} else {
			$this->error ('保存失败!');
		}
	}
	
	//1
	public function plan(){
		$plan = M ('plan');
		$fid = 1;
		$vo = $plan->find($fid);
		$vo['content'] = stripslashes($vo['content']);//过滤掉反斜杠
		$this->assign ( 'vo', $vo );
		$webtitle = $this->plan_class();
		$this->assign('title',$webtitle[$fid]);
		$this->display ('plan');
	}
	//2
	public function planTwo(){
		$plan = M ('plan');
		$vo = $plan->find(2);
		$vo['content'] = stripslashes($vo['content']);//过滤掉反斜杠
		$this->assign ( 'vo', $vo );
		
		$see = $_SERVER['HTTP_HOST'].__APP__;
		$see = str_replace("//","/",$see);
        $this->assign ( 'server', $see );
        
        //会员级别
        $fck = M ('fck');
        $id = $_SESSION[C('USER_AUTH_KEY')];
        $urs = $fck -> where('id='.$id)->field('*') -> find();
		$this -> assign('fck_rs',$urs);//总奖金
        
		$this->display ('planTwo');
	}

	//3
	public function planThree(){
		$plan = M ('plan');
		$vo = $plan->find(3);
		$vo['content'] = stripslashes($vo['content']);//过滤掉反斜杠
		$this->assign ( 'vo', $vo );
		$this->display ('planThree');
	}

	//4
	public function planFour($Fid=0){
		$plan = M ('plan');
		$vo = $plan->find(4);
		$vo['content'] = stripslashes($vo['content']);//过滤掉反斜杠
		$this->assign ( 'vo', $vo );
		$this->display ('planFour');
	}
	
	//5
	public function planFive($Fid=0){
		$plan = M ('plan');
		$vo = $plan->find(5);
		$vo['content'] = stripslashes($vo['content']);//过滤掉反斜杠
		$this->assign ( 'vo', $vo );
		$this->display ('planFive');
	}
	
	private function plan_find($fid){
		$plan = M ('plan');
        $list = $plan->find($fid);
		$this->assign('list',$list);
		$this->us_fckeditor('content',$list['content'],400,"100%");//FCK编辑器
	}
	
	private function plan_class(){
		$returnarray = array();
		$returnarray[1] = "市场计划";
		$returnarray[2] = "市场计划-英文";
		$returnarray[3] = "交易协议";
		$returnarray[4] = "视频宣传";
		$returnarray[5] = "常见问题";
		return $returnarray;
	}

}
?>