<?php
class MsgAction extends CommonAction {
	public function _initialize() {
		header("Content-Type:text/html; charset=utf-8");
		$this->_inject_check(0);//调用过滤函数
		$this->_checkUser();
	}
	
	//二级密码验证
	public function cody(){
		$UrlID = (int)$_GET['c_id'];
		if (empty($UrlID)){
			$this->error('二级密码错误!');
			exit;
		}
		if(!empty($_SESSION['user_pwd2'])){
			$url = __URL__."/codys/Urlsz/$UrlID";
			$this->_boxx($url);
			exit;
		}
		$fck   =  M ('cody');
        $list	=  $fck->where("c_id=$UrlID")->getField('c_id');
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
	public function codys(){
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
			case 1:
				$_SESSION['DLTZURL02'] = 'changedata';
				$bUrl = __URL__.'/changedata';//修改资料
				$this->_boxx($bUrl);
				break;
			case 2:
				$_SESSION['DLTZURL01'] = 'changepassword';
				$bUrl = __URL__.'/changepassword';//修改密码
				$this->_boxx($bUrl);
				break;
			case 3:
				$_SESSION['DLTZURL01'] = 'pprofile';
				$bUrl = __URL__.'/pprofile';//修改密码
				$this->_boxx($bUrl);
				break;
			default;
				$this->error('二级密码错误!');
				break;
		}
	}

	/*
	 * 发邮件功能
	*/
	public function writemsg(){
		$ID = $_SESSION[C('USER_AUTH_KEY')];
		$fck = M('fck');
		$mrs = $fck->where('id='.$ID)->find();
		$this->assign('mrs',$mrs);
		$this->display('writemsg');
	}
	
	/*
	 * 发邮件处理功能
	 * @UserID 接收人
	 * @Title 标题
	 * @Msg 信息内容
	 * @level 发送类型 1为公司，2为会员
	*/
	public function writeSave(){
		$UserID   = trim($_POST['UserID']);
		$Title    = trim($_POST['Title']);
		$Msg      = trim($_POST['Msg']);
		$level      = (int)$_POST['level'];
		if($level==1){
			$gsrs = M('fck')->where('id=1')->field('user_id')->find();
			$UserID = $gsrs['user_id'];
		}
		$fck = M ('fck');
		if (empty($UserID)){
			$this->error('数据错误!');
			exit;
		}
		if (strlen($Title) > 200){
			$this->error ('主题太长!');
			exit;
		}
		$this->_messagesAdd($UserID,$Title,$Msg);
	}
	private function _messagesAdd($UserID='0',$Title='',$Msg=''){
		$fck = M ('fck');
		$Users = M ('Msg');
		$where = array();
		$ID = $_SESSION[C('USER_AUTH_KEY')];
		//收件人
		$where1 = array();
		if($UserID == '公司'){
		    $where1['user_id'] = $UserID;
			$gsrs = M('fck')->where('id=1')->field('user_id')->find();
			$where1['user_id'] = $gsrs['user_id'];
		} else if($UserID == '所有人' && $ID == 1){
		    $where1['id'] != 1;
		} else {
		    $where1['user_id'] = $UserID;
		}

		$field = 'id,user_id';
		$vo = $fck->where($where1)->field($field)->select();
		if (!$vo){
			$this->error('收件人不存在！');
			exit;
		}
// 		if($ID>1){
// 			if($ID == $vo['id']){
// 				$this->error('不能给自已发邮件！');
// 				exit;
// 			}
// 		}

		//发件人
		$where['id'] = $ID;
		$vo2 = $fck->where($where)->field($field)->find();
		if (!$vo2){
			$this->error('没有该记录!');
			exit;
		}
		//开始事务处理
		$Users->startTrans();
        $nowdate = strtotime(date('c'));
        foreach ($vo as $key=>$value){
            //留言表
            $data = array();
            $data['f_uid']		= $vo2['id'];
            $data['f_user_id']	= $vo2['user_id'];
            $data['s_uid']		= $value['id'];
            $data['s_user_id']	= $value['user_id'];
            $data['title']		= $Title;
            $data['content']	= $Msg;
            $data['f_time']		= time();
		    $rs1 = $Users->add($data);
		    if (!$rs1) {
		        break;
		    }
        }
		unset($data);
		if ($rs1){
			//提交事务
			$Users->commit();
			$bUrl = __URL__.'/writemsg';
			$this->_box(1,'留言成功！',$bUrl,1);
			exit;
		}else{
			//事务回滚：
			$Users->rollback();
			$this->error('操作失败');
			exit;
		}
	}
	
	/*
	 * 收件箱
	 * */
	public function inmsg(){
		$msg = M('msg');
		$map = array();
		$map['s_uid']   = $_SESSION[C('USER_AUTH_KEY')];
		$map['s_del']   = 0;
        $field  = '*';
        //=====================分页开始==============================================
        import ( "@.ORG.ZQPage" );  //导入分页类
        $count = $msg->where($map)->count();//总页数
        $listrows = C('ONE_PAGE_RE');//每页显示的记录数
        $Page = new ZQPage($count,$listrows,1);
        //===============(总页数,每页显示记录数,css样式 0-9)
        $show = $Page->show();//分页变量
        $this->assign('page',$show);//分页变量输出到模板
        $list = $msg->where($map)->field($field)->order('id desc')->page($Page->getPage().','.$listrows)->select();
        $this->assign('list',$list);//数据输出到模板
        //=================================================
		$this->display();
	}
	
	/*
	 * 删除收件箱记录
	 * */
	public function s_del(){
		$boxID = $_POST['tabledb'];
		$msg = M('msg');
		$map = array();
		$map['id']  = array('in',$boxID);
		$map['s_uid'] = $_SESSION[C('USER_AUTH_KEY')];
		$lirs = $msg->where($map)->select();
		foreach($lirs as $rs){
			$where = "id=".$rs['id'];
			$f_del = $rs['f_del'];
			if($f_del==1){
				$delre = $msg->where($where)->delete();
			}else{
				$data = array();
				$data['s_del'] = 1;
				$delre = $msg->where($where)->save($data);
			}
		}
		unset($msg,$map,$boxID);
		$bUrl = __URL__.'/inmsg';
		$this->_box(1,'删除成功！',$bUrl,1);
		exit;
	}
	
	/*
	 * 查看收件箱记录
	 * */
	public function s_view(){
		$msg = M('msg');
		$did = (int)$_GET['did'];
		$map = array();
		$map['id']  = $did;
		$map['s_uid'] = $_SESSION[C('USER_AUTH_KEY')];
		$mrs = $msg->where($map)->find();
		if($mrs){
			$read = $mrs['s_read'];
			if($read==0){
				$msg->where($map)->setField('s_read',1);
			}
			$this->assign('vo',$mrs);
			$this->display();
		}else{
			$this->error('操作失败');
			exit;
		}
	}
	
	/*
	 * 回复邮件
	 * */
	public function replyAC(){
		$Pid = (int) $_POST['Pid'];
		$Msg = $_POST['Msg'];
		if ($Pid == 0){
			$this->error('数据错误!');
			exit;
		}
		if (strlen($Pid) > 12){
			$this->error ('参数错误!');
			exit;
		}
		$this->_messagesShowReply($Pid,$Msg);
	}
	private function _messagesShowReply($Pid=0,$Msg=''){
		$ID = $_SESSION[C('USER_AUTH_KEY')];
		$msg = M ('msg');
		$fck = M ('fck');
		$where = array();//
		$where['s_uid'] = $ID;
		$where['id'] = $Pid;
		$field = '*';
		$vo = $msg ->where($where)->field($field)->find();
		if (!$vo){
			$this->error ('参数错误!');
			exit;
		}
		//发件人
		$where = array();
		$where['id'] = $ID;
		$vo2 = $fck->where($where)->field('id,user_id')->find();
		if (!$vo2){
			$this->error('没有该记录!');
			exit;
		}
		$Title = '回复：'. $vo['title'];
		
		$data = array();
		$data['f_uid']		= $vo2['id'];
		$data['f_user_id']	= $vo2['user_id'];
		$data['s_uid']		= $vo['f_uid'];
		$data['s_user_id']	= $vo['f_user_id'];
		$data['title']		= $Title;
		$data['content']	= $Msg;
		$data['f_time']		= time();
		$rs1 = $msg->add($data);
		unset($msg,$data);
		if ($rs1){
			$bUrl = __URL__.'/inmsg';
			$this->_box(1,'回复成功！',$bUrl,1);
			exit;
		}else{
			$this->error('回复失败');
			exit;
		}
	}
	
	/*
	 * 发件箱
	 * */
	public function outmsg(){
		$msg = M('msg');
		$map = array();
		$map['f_uid']   = $_SESSION[C('USER_AUTH_KEY')];
		$map['f_del']   = 0;
        $field  = '*';
        //=====================分页开始==============================================
        import ( "@.ORG.ZQPage" );  //导入分页类
        $count = $msg->where($map)->count();//总页数
        $listrows = C('ONE_PAGE_RE');//每页显示的记录数
        $Page = new ZQPage($count,$listrows,1);
        //===============(总页数,每页显示记录数,css样式 0-9)
        $show = $Page->show();//分页变量
        $this->assign('page',$show);//分页变量输出到模板
        $list = $msg->where($map)->field($field)->order('id desc')->page($Page->getPage().','.$listrows)->select();
        $this->assign('list',$list);//数据输出到模板
        //=================================================
		$this->display();
	}
	
	/*
	 * 删除收件箱记录
	 * */
	public function f_del(){
		$boxID = $_POST['tabledb'];
		$msg = M('msg');
		$map = array();
		$map['id']  = array('in',$boxID);
		$map['f_uid'] = $_SESSION[C('USER_AUTH_KEY')];
		$lirs = $msg->where($map)->select();
		foreach($lirs as $rs){
			$where = "id=".$rs['id'];
			$f_del = $rs['s_del'];
			if($f_del==1){
				$delre = $msg->where($where)->delete();
			}else{
				$data = array();
				$data['f_del'] = 1;
				$delre = $msg->where($where)->save($data);
			}
		}
		unset($msg,$map,$boxID);
		$bUrl = __URL__.'/outmsg';
		$this->_box(1,'删除成功！',$bUrl,1);
		exit;
	}
	
	/*
	 * 查看收件箱记录
	 * */
	public function f_view(){
		$msg = M('msg');
		$did = (int)$_GET['did'];
		$map = array();
		$map['id']  = $did;
		$map['f_uid'] = $_SESSION[C('USER_AUTH_KEY')];
		$mrs = $msg->where($map)->find();
		if($mrs){
			$read = $mrs['f_read'];
			if($read==0){
				$msg->where($map)->setField('f_read',1);
			}
			$this->assign('vo',$mrs);
			$this->display();
		}else{
			$this->error('操作失败');
			exit;
		}
	}

}
?>