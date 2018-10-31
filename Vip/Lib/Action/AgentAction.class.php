<?php
// 注册模块
class AgentAction extends CommonAction
{

    public function _initialize()
    {
        header("Content-Type:text/html; charset=utf-8");
        $this->_inject_check(0); // 调用过滤函数
        $this->_Config_name(); // 调用参数
        $this->_checkUser();
    }

    public function cody()
    {
        // ===================================二级验证
        $UrlID = (int) $_GET['c_id'];
        if (empty($UrlID)) {
            $this->error('二级密码错误!');
            exit();
        }
        if (! empty($_SESSION['user_pwd2'])) {
            $url = __URL__ . "/codys/Urlsz/$UrlID";
            $this->_boxx($url);
            exit();
        }
        $cody = M('cody');
        $list = $cody->where("c_id=$UrlID")
            ->field('c_id')
            ->find();
        if ($list) {
            $this->assign('vo', $list);
            $this->display('Public:cody');
            exit();
        } else {
            $this->error('二级密码错误!');
            exit();
        }
    }

    public function codys()
    {
        // =============================二级验证后调转页面
        $Urlsz = (int) $_POST['Urlsz'];
        if (empty($_SESSION['user_pwd2'])) {
            $pass = $_POST['oldpassword'];
            $fck = M('fck');
            if (! $fck->autoCheckToken($_POST)) {
                $this->error('页面过期请刷新页面!');
                exit();
            }
            if (empty($pass)) {
                $this->error('二级密码错误!');
                exit();
            }
            
            $where = array();
            $where['id'] = $_SESSION[C('USER_AUTH_KEY')];
            $where['passopen'] = md5($pass);
            $list = $fck->where($where)
                ->field('id,is_agent')
                ->find();
            if ($list == false) {
                $this->error('二级密码错误!');
                exit();
            }
            $_SESSION['user_pwd2'] = 1;
        } else {
            $Urlsz = $_GET['Urlsz'];
        }
        switch ($Urlsz) {
            case 1:
                
                $_SESSION['Urlszpass'] = 'MyssXiGua';
                $bUrl = __URL__ . '/agents'; // 申请代理
                $this->_boxx($bUrl);
                break;
            case 9:
                if ($list['is_agent'] >= 2) {
                    $this->error('您已经是服务中心!');
                    exit();
                }
                $_SESSION['Urlszpass'] = 'MyssX';
                $bUrl = __URL__ . '/agents3'; // 申请代理
                $this->_boxx($bUrl);
                break;
            case 2:
                $_SESSION['Urlszpass'] = 'MyssShuiPuTao';
                $bUrl = __URL__ . '/menber'; // 未开通会员
                $this->_boxx($bUrl);
                break;
            
            case 3:
                $_SESSION['Urlszpass'] = 'Myssmenberok';
                $bUrl = __URL__ . '/menberok'; // 已开通会员
                $this->_boxx($bUrl);
                break;
                
            case 11:
                $_SESSION['Urlszpass'] = 'MyssmenberUpLevel';
                $bUrl = __URL__ . '/memberuplevel'; // 原点升级晋级审核
                $this->_boxx($bUrl);
                break;
                
            case 12:
                $_SESSION['Urlszpass'] = 'MyssnetAorB';
                $bUrl = __URL__ . '/netAorB'; // 开通新网络审核
                $this->_boxx($bUrl);
                break;
                
            case 13:
                $_SESSION['Urlszpass'] = 'MyssnetAorBApply';
                $bUrl = __URL__ . '/netAorBApply'; // 申请新网络
                $this->_boxx($bUrl);
                break;
            case 14:
                $_SESSION['Urlszpass'] = 'MyssproductExchange';
                $bUrl = __URL__ . '/productExchange'; // 产品兑换
                $this->_boxx($bUrl);
                break;
            
            case 4:
                $_SESSION['UrlPTPass'] = 'MyssGuanXiGua';
                $bUrl = __URL__ . '/adminAgents'; // 后台确认服务中心
                $this->_boxx($bUrl);
                break;
            
            case 5:
                
                $_SESSION['Urlszpass'] = 'MyssXiGu';
                $bUrl = __URL__ . '/agents1'; // 申请代理
                $this->_boxx($bUrl);
                break;
            
            case 6:
                $_SESSION['UrlPTPass'] = 'MyssGuanXiGu';
                $bUrl = __URL__ . '/adminAgents1'; // 后台确认
                $this->_boxx($bUrl);
                break;
            
            case 10:
                $_SESSION['UrlPTPass'] = 'MyssGuanX';
                $bUrl = __URL__ . '/adminAgents3'; // 后台确认
                $this->_boxx($bUrl);
                break;
            
            case 7:
                
                $_SESSION['Urlszpass'] = 'MyssXiG';
                $bUrl = __URL__ . '/agents2'; // 申请代理
                $this->_boxx($bUrl);
                break;
            
            case 8:
                $_SESSION['UrlPTPass'] = 'MyssGuanXiG';
                $bUrl = __URL__ . '/adminAgents2'; // 后台确认
                $this->_boxx($bUrl);
                break;
            
            default:
                $this->error('二级密码错误!');
                exit();
        }
    }

    public function agents($Urlsz = 0)
    {
        // ======================================申请会员中心/服务中心/服务中心
        if ($_SESSION['Urlszpass'] == 'MyssXiGua') {
            $fee_rs = M('fee')->find();
            
            $fck = M('fck');
            $where = array();
            // 查询条件
            $where['id'] = $_SESSION[C('USER_AUTH_KEY')];
            $field = '*';
            $fck_rs = $fck->where($where)
                ->field($field)
                ->find();
            
            if ($fck_rs) {
                // 会员级别
                switch ($fck_rs['l_nums']) {
                    case 0:
                        $agent_status = '未申请商家!';
                        break;
                    case 1:
                        $agent_status = '申请正在审核中!';
                        break;
                    case 2:
                        $agent_status = '商家已开通!';
                        break;
                }
                
                $this->assign('fee_s6', $fee_rs['i1']);
                $this->assign('agent_level', 0);
                $this->assign('agent_status', $agent_status);
                $this->assign('fck_rs', $fck_rs);
                
                $Agent_Us_Name = C('Agent_Us_Name');
                $Aname = explode("|", $Agent_Us_Name);
                $this->assign('Aname', $Aname);
                
                $this->display('agents');
            } else {
                $this->error('操作失败!');
                exit();
            }
        } else {
            $this->error('错误!');
            exit();
        }
    }

    public function agents3($Urlsz = 0)
    {
        // ======================================申请会员中心/服务中心/服务中心
        if ($_SESSION['Urlszpass'] == 'MyssX') {
            $fee_rs = M('fee')->find();
            
            $fck = M('fck');
            $where = array();
            // 查询条件
            $where['id'] = $_SESSION[C('USER_AUTH_KEY')];
            $field = '*';
            $fck_rs = $fck->where($where)
                ->field($field)
                ->find();
            
            if ($fck_rs) {
                // 会员级别
                switch ($fck_rs['is_agent']) {
                    case 0:
                        $agent_status = '未申请服务中心!';
                        break;
                    case 1:
                        $agent_status = '申请正在审核中!';
                        break;
                    case 2:
                        $agent_status = '服务中心已开通!';
                        break;
                }
                
                $this->assign('fee_s6', $fee_rs['i1']);
                $this->assign('agent_level', 0);
                $this->assign('agent_status', $agent_status);
                $this->assign('fck_rs', $fck_rs);
                
                $Agent_Us_Name = C('Agent_Us_Name');
                $Aname = explode("|", $Agent_Us_Name);
                $this->assign('Aname', $Aname);
                
                $this->display('agents3');
            } else {
                $this->error('操作失败!');
                exit();
            }
        } else {
            $this->error('错误!');
            exit();
        }
    }
    
    // 申请新网络列表
    public function netAorBApply($Urlsz = 0)
    {
        if ($_SESSION['Urlszpass'] == 'MyssnetAorBApply') {
            $where = array();
			$fck = M('fck');
	    	$uid = $_SESSION[C('USER_AUTH_KEY')];
			$frs = $fck->find($uid);
			$fee = M ('fee');
			$fee_rs =$fee->field('s1,s2,s3,s4,s5,s9')->find();
			$s1 =explode('|',$fee_rs['s1']);
			$s2 =explode('|',$fee_rs['s2']);
			$s3 =explode('|',$fee_rs['s3']);
			// 投资金额基数
			$s9 =explode('|',$fee_rs['s9']);
			// 领导奖比例
			$s4 =$fee_rs['s4'];
			$promo = M('aorb');
			$field  = '*';
			$map['uid'] = $uid;
            $list = $promo->where($map)->field($field)->order('id desc')->select();
            $this->assign('list',$list);//数据输出到模板
            //=================================================

			$this->assign('s4',$s4);
			$this->assign('frs',$frs);//数据输出到模板
			$this->display();
        } else {
            $this->error('错误!');
            exit();
        }
    }
    
    // 产品兑换列表
    public function productExchange($Urlsz = 0)
    {
        if ($_SESSION['Urlszpass'] == 'MyssproductExchange') {
            $where = array();
            // 类型
            $nettype = $_POST['net'];
            $fck = M('fck');
            $uid = $_SESSION[C('USER_AUTH_KEY')];
            $frs = $fck->find($uid);
            $this->assign('futoua',$frs['agent_xf']);
            $netb = M('netb');
            $netb_rs = $netb->where('uid='.$uid)->field('*')->find();
            $this->assign('futoub',$netb_rs['agent_futou']);
            $this->assign('frs',$frs);//数据输出到模板
            unset($where,$fck,$uid,$frs,$netb,$netb_rs);
            $this->display();
        } else {
            $this->error('错误!');
            exit();
        }
    }
    
    //前台产品兑换确认
    public function productExchangeConfirm(){
        if ($_SESSION['Urlszpass'] == 'MyssproductExchange'){
            
            date_default_timezone_set('asia/shanghai');
            $week = date('w');
            $day = date('md');
            $time = date('G');
            if($week==0 || $week==6) {
                $status = 2;
            } else if($time>=8 && $time <= 21) {
                $status = 0;
            } else {
                $status = 1;
            }
            if($status!=0){
                $this->error('只能在8时至21时兑换产品，节假日及休息时间不能兑换产品！');
                exit;
            }
            // 网络类型
            $nettype = $_POST['net'];
            // 类型
            $type = $_POST['type'];
            // 填写的数量
            $nums = $_POST['nums'];
            $money = $nums*800;
            $where = array();
            $fck = M('fck');
            $uid = $_SESSION[C('USER_AUTH_KEY')];
            $frs = $fck->find($uid);
            if ($nettype == 'a' && $frs['net_top_a'] == 1) {
                $agent_futou = $frs['agent_xf'];
                if (type == 1 && $agent_futou < $money) {
                    $this->error('可兑换余额不足！');
                    exit();
                } else {
                    $member_result = $fck->execute("update xt_fck set agent_xf=agent_xf -$money where id =".$uid);
                }
            } else if ($nettype == 'b' && $frs['net_top_b'] == 1) {
                $netb = M('netb');
                $netb_rs = $netb->where('uid='.$uid)->field('*')->find();
                $agent_futou = $netb_rs['agent_futou'];
                if (type == 1 && $agent_futou < $money) {
                    $this->error('可兑换余额不足！');
                    exit();
                } else {
                    $member_result = $netb->execute("update xt_netB set agent_futou=agent_futou -$money where uid =".$uid);
                }
            } else {
                $this->error('分红包尚未封顶，暂时不可兑换！');
                exit;
            }
            if ($member_result) {
                // 添加物流信息
                $pora = M('product');
                $gouwu = D('Gouwu');
                $gwd = array();
                $gwd['uid'] = $frs['id'];
                $gwd['user_id'] = $frs['user_id'];
                $gwd['lx'] = 1;
                $gwd['ispay'] = 0;
                $gwd['pdt'] = mktime();
                $gwd['us_name'] = $frs['name'];
                $gwd['us_address'] = $frs['user_address'];
                $gwd['us_tel'] = $frs['user_tel'];
                $where = array();
                // 查询产品信息
                $where['id'] = 22;
                $prs = $pora->where($where)->find();
                $w_money = $prs['a_money'];
                $gwd['did'] = $prs['id'];
                $gwd['money'] = $w_money;
                $gwd['shu'] = $nums;
                $gwd['cprice'] = $nums*800;
                if(!empty($prs['countid'])){
                    $gwd['countid'] = $prs['countid'];
                }
                $result = $gouwu->add($gwd);
            }
            if($result) {
                $bUrl = __URL__.'/productExchange';
                $this->_box(1,'兑换成功！',$bUrl,3);
            } else {
                $this->error('兑换失败！');
                exit;
            }
            unset($fck,$uid,$frs,$agent_futou,$money,$member_result,$netb,$netb_rs,$pora,$gouwu,$gwd,$where,$prs,$w_money,$result);
             
        }else{
            $this->error('错误！');
            exit;
        }
    }
    
    //前台新网络申请
    public function netAorBApplyConfirm(){
        if ($_SESSION['Urlszpass'] == 'MyssnetAorBApply'){
            date_default_timezone_set('asia/shanghai');
            $week = date('w');
            $day = date('md');
            $time = date('G');
            if($week==0 || $week==6) {
                $status = 2;
            } else if($time>=8 && $time <= 21) {
                $status = 0;
            } else {
                $status = 1;
            }
            if($status!=0){
                $this->error('只能在8时至21时申请新网络，节假日及休息时间不能申请新网络！');
                exit;
            }
            // 当前用户ID
            $uid = $_SESSION[C('USER_AUTH_KEY')];
            $where['id'] = $uid;
            $promo = M('aorb');
            $promo_rs = $promo->where("uid={$uid} and  is_pay=0")->find();
            if ($promo_rs) {
                $this->error('您有还未确认的申请，请等待确认之后再行申请！');
                exit;
            }
            $fck = D('Fck');
            // 根据会员ID查询会员表数据
            $fck_rs = $fck->where($where)->find();
            // 临时会员不能升级
            if($fck_rs['is_pay'] == 0){
                $this->error('您是临时会员不能申请新网络，请先开通！');
                exit;
            }
            if ($fck_rs['net_status'] == 'b' && $fck_rs['net_top_b'] == 0) {
                $this->error('当前网络分红包尚未封顶，暂时不能开通新网络！');
                exit;
            } else if ($fck_rs['net_status'] == 'a' && $fck_rs['net_top_a'] == 0) {
                $this->error('当前网络分红包尚未封顶，暂时不能开通新网络！');
                exit;
            }
            // 查询参数设置数据
            $fee = M ('fee');
            $fee_rs =$fee->field('s1,s2,s3,s4,s5,s9,s13')->find();
            // 写入帐号数据
            $data = array();
            $data['uid'] = $uid;
            $data['user_id'] = $fck_rs['user_id'];
            $data['money'] = 40000;
            $data['adt'] = time();
            $data['danshu'] = 50;
            $data['is_pay']	= 0;
            $result = $promo->add($data);
            unset($data);
            if($result) {
                $bUrl = __URL__.'/netAorBApply';
                $this->_box(1,'您申请成功，请耐心等待管理员审核！',$bUrl,3);
            } else {
                $this->error('申请失败！');
                exit;
            }
             
        }else{
            $this->error('错误！');
            exit;
        }
    }

    public function agents1($Urlsz = 0)
    {
        // ======================================申请会员中心/服务中心/服务中心
//         if ($_SESSION['Urlszpass'] == 'MyssXiGu') {
            $fee_rs = M('fee')->find();
            
            $fck = M('fck');
            $where = array();
            // 查询条件
            $where['id'] = $_SESSION[C('USER_AUTH_KEY')];
            $field = '*';
            $fck_rs = $fck->where($where)
                ->field($field)
                ->find();
            $netb = M('netb');
            $netb_rs = $netb ->where('uid = '.$_SESSION[C('USER_AUTH_KEY')])->field('*')->find();
            
            if ($fck_rs) {
                $this->assign('fee_s6', $fee_rs['i1']);
                $this->assign('str9', $fee_rs['str9']);
                $this->assign('agent_level', 0);
                $this->assign('agent_status', $agent_status);
                $this->assign('fck_rs', $fck_rs);
                $this->assign('netb_rs', $netb_rs);
                
                $Agent_Us_Name = C('Agent_Us_Name');
                $Aname = explode("|", $Agent_Us_Name);
                $this->assign('Aname', $Aname);
                
                $this->display('agents1');
            } else {
                $this->error('操作失败!');
                exit();
            }
//         } else {
//             $this->error('错误!');
//             exit();
//         }
    }

    public function agents2($Urlsz = 0)
    {
        // ======================================申请会员中心/服务中心/服务中心
        if ($_SESSION['Urlszpass'] == 'MyssXiG') {
            $fee_rs = M('fee')->find();
            
            $fck = M('fck');
            $where = array();
            // 查询条件
            $where['id'] = $_SESSION[C('USER_AUTH_KEY')];
            $field = '*';
            $fck_rs = $fck->where($where)
                ->field($field)
                ->find();
            
            if ($fck_rs) {
                // 会员级别
                switch ($fck_rs['is_p']) {
                    case 0:
                        $agent_status = '未申请省代理!';
                        break;
                    case 1:
                        $agent_status = '申请正在审核中!';
                        break;
                    case 2:
                        $agent_status = '省代理已申请成功!';
                        break;
                }
                
                switch ($fck_rs['is_c']) {
                    case 0:
                        $agent_status1 = '未申请市代理!';
                        break;
                    case 1:
                        $agent_status1 = '申请正在审核中!';
                        break;
                    case 2:
                        $agent_status1 = '市代理已申请成功!';
                        break;
                }
                switch ($fck_rs['is_cty']) {
                    case 0:
                        $agent_status2 = '未申请县!';
                        break;
                    case 1:
                        $agent_status2 = '申请正在审核中!';
                        break;
                    case 2:
                        $agent_status2 = '县代理已申请成功!';
                        break;
                }
                
                $this->assign('fee_s6', $fee_rs['i1']);
                $this->assign('str9', $fee_rs['str9']);
                $this->assign('agent_level', 0);
                $this->assign('agent_status', $agent_status);
                $this->assign('agent_status1', $agent_status1);
                $this->assign('agent_status2', $agent_status2);
                $this->assign('fck_rs', $fck_rs);
                
                $Agent_Us_Name = C('Agent_Us_Name');
                $Aname = explode("|", $Agent_Us_Name);
                $this->assign('Aname', $Aname);
                
                $this->display('agents2');
            } else {
                $this->error('操作失败!');
                exit();
            }
        } else {
            $this->error('错误!');
            exit();
        }
    }

    public function agentsAC2()
    {
        // ================================申请会员中心中转函数
        $content = $_POST['content'];
        $agentMax = $_POST['agentMax'];
        $shoplx = (int) $_POST['shoplx'];
        $shop_a = $_POST['shop_a'];
        $shop_b = $_POST['shop_b'];
        $fee = M('fee');
        $fee_rs = $fee->where('s9,s14,s11,str9')->find(1);
        $s11 = $fee_rs['s11'];
        
        $str9 = $fee_rs['str9'];
        $one_mm = 1;
        $where = array();
        if ($shoplx == 1) {
            $where['is_p'] = 0;
        }
        if ($shoplx == 2) {
            $where['is_c'] = 0;
        }
        if ($shoplx == 3) {
            $where['is_cty'] = 0;
        }
        $fck = M('fck');
        $id = $_SESSION[C('USER_AUTH_KEY')];
        
        $where['id'] = $id;
        
        $fck_rs = $fck->where($where)
            ->field('*')
            ->find();
        
        if ($fck_rs) {
            
            $agent_use = $fck_rs['agent_use'];
            
            if ($fck_rs['is_pay'] == 0) {
                $this->error('临时会员不能申请!');
                exit();
            }
            
            // if($agent_use < $str9){
            // $this->error ('奖金余额不足!');
            // exit;
            // }
            
            if ($fck_rs['is_p'] == 1) {
                $this->error('有申请还没通过审核!');
                exit();
            }
            if ($fck_rs['is_c'] == 1) {
                $this->error('有申请还没通过审核!');
                exit();
            }
            if ($fck_rs['is_cty'] == 1) {
                $this->error('有申请还没通过审核!');
                exit();
            }
            
            if (empty($content)) {
                $this->error('请输入备注!');
                exit();
            }
            
            if ($fck_rs['is_p'] == 0 && $shoplx == 1) {
                $nowdate = time();
                $result = $fck->query("update __TABLE__ set verify='" . $content . "',is_p=1,is_cha=1,jia_nums=1,idt=$nowdate where id=" . $id);
            }
            if ($fck_rs['is_c'] == 0 && $shoplx == 2) {
                $nowdate = time();
                $result = $fck->query("update __TABLE__ set verify='" . $content . "',is_c=1,is_cha=1,jia_nums=2,idt=$nowdate where id=" . $id);
            }
            if ($fck_rs['is_cty'] == 0 && $shoplx == 3) {
                $nowdate = time();
                $result = $fck->query("update __TABLE__ set verify='" . $content . "',is_cty=1,is_cha=1,jia_nums=3,idt=$nowdate where id=" . $id);
            }
            
            $bUrl = __URL__ . '/agents2';
            $this->_box(1, '申请成功！', $bUrl, 2);
        } else {
            $this->error('非法操作');
            exit();
        }
    }
    // 复投
    public function agentsAC1()
    {
        // 复投类型
        $futou = $_POST['futou'];
        if ($futou == 1) {
            $this->error('请选择复投类型！');
            exit();
        }
        $fee = M('fee');
        $fee_rs = $fee->where('s9,s14,s11,str9')->find(1);
        // 注册金额
        $s9 = $fee_rs['s9'];
        // 报单中心奖励比例
        $str9 = $fee_rs['str9'];
        $fck = D('Fck');
        $jiadan = M('jiadan');
        // 登录会员ID
        $id = $_SESSION[C('USER_AUTH_KEY')];
        $where = array();
        $where['id'] = $id;
        // 查询会员表数据
        $fck_rs = $fck->where($where)->field('*')->find();
        $ftMonth = $jiadan->where('uid ='.$id)->max('ftMonth');
        // 若存在会员表数据
        if ($fck_rs) {
            // 复投金额：注册金额的30%
            $tmpMoney = $fck_rs['cpzj'] * 0.3;
            if ($fck_rs['agent_use'] < $tmpMoney && $futou == 2) {
                $this->error('消费积分不足！');
                exit();
            }
            // 现在时间
            $nowdate = strtotime(date('c'));
            if ($futou == 2) {
                // 更新会员表数据
                $result = $fck->query("update __TABLE__ set is_cc=is_cc+1". ",agent_use=agent_use-$tmpMoney where id=" . $id);
            }
            // 分红包记录表
            // 0.注册复投返还
            $fck->jiaDan($fck_rs['id'], $fck_rs['user_id'], $nowdate, 0, 0, $tmpMoney, 0, $fck_rs['month_tag'], 0);
            // 1.分红
            $fck->jiaDan($fck_rs['id'], $fck_rs['user_id'], $nowdate, 0, 0, $fck_rs['f4'] * 10000, 0, $fck_rs['month_tag'], 1);
            if ($fck_rs['month_tag'] == 2) {
                // 2.补助
                $fck->jiaDan($fck_rs['id'], $fck_rs['user_id'], $nowdate, 0, 0, 666, 0, $fck_rs['month_tag'], 2);
            }
            $bUrl = __URL__ . '/agents1';
            $this->_box(1, '复投成功，请等待管理员审核！', $bUrl, 2);
        } else {
            $this->error('非法操作');
            exit();
        }
    }

    public function agentsAC3()
    {
        // ================================申请会员中心中转函数
        $content = $_POST['content'];
        $agentMax = $_POST['agentMax'];
        $shoplx = (int) $_POST['shoplx'];
        $shop_a = $_POST['shop_a'];
        $shop_b = $_POST['shop_b'];
        $fee = M('fee');
        $fee_rs = $fee->where('s9,s14,s11')->find(1);
        $s11 = $fee_rs['s11'];
        // $one_mm = $s9[0];
        $one_mm = 1;
        
        $fck = M('fck');
        $id = $_SESSION[C('USER_AUTH_KEY')];
        $where = array();
        $where['id'] = $id;
        $fck_rs = $fck->where($where)->field('*')->find();
        if ($fck_rs) {
            if ($fck_rs['is_pay'] == 0) {
                $this->error('临时会员不能申请!');
                exit();
            }
            if ($fck_rs['is_agent'] == 1) {
                $this->error('上次申请还没通过审核!');
                exit();
            }
            
            // $bqycount=0;
            // if($shoplx==1){
            // $bqycount = $fck->where("is_agent>0 and shop_a=".$shop_a)->count;
            // }elseif($shoplx==2){
            // $bqycount = $fck->where("is_agent>0 and shop_b=".$shop_b)->count;
            // }
            // if($bqycount>0){
            // $this->error('本区域的服务中心已经存在!');
            // exit;
            // }
            
            if (empty($content)) {
                $this->error('请输入备注!');
                exit();
            }
            
            if ($fck_rs['l_nums'] == 0) {
                $nowdate = time();
                $result = $fck->query("update __TABLE__ set verify='" . $content . "',is_agent=1,shoplx=" . $shoplx . ",shop_a='" . $shop_a . "',shop_b='" . $shop_b . "',idt=$nowdate where id=" . $id);
            }
            
            $bUrl = __URL__ . '/agents3';
            $this->_box(1, '申请成功！', $bUrl, 2);
        } else {
            $this->error('非法操作');
            exit();
        }
    }

    public function agentsAC()
    {
        // ================================申请会员中心中转函数
        $content = $_POST['content'];
        $agentMax = $_POST['agentMax'];
        $shoplx = (int) $_POST['shoplx'];
        $shop_a = $_POST['shop_a'];
        $shop_b = $_POST['shop_b'];
        $fee = M('fee');
        $fee_rs = $fee->where('s9,s14,s11')->find(1);
        $s11 = $fee_rs['s11'];
        $one_mm = 1;
        
        
        $fck = M('fck');
        $id = $_SESSION[C('USER_AUTH_KEY')];
        $where = array();
        $where['id'] = $id;
        
        $fck_rs = $fck->where($where)
            ->field('*')
            ->find();
        if ($fck_rs) {
            
            if ($fck_rs['is_pay'] == 0) {
                $this->error('临时会员不能申请!');
                exit();
            }
            if ($fck_rs['l_nums'] == 1) {
                $this->error('上次申请还没通过审核!');
                exit();
            }
            
            // $bqycount=0;
            // if($shoplx==1){
            // $bqycount = $fck->where("is_agent>0 and shop_a=".$shop_a)->count;
            // }elseif($shoplx==2){
            // $bqycount = $fck->where("is_agent>0 and shop_b=".$shop_b)->count;
            // }
            // if($bqycount>0){
            // $this->error('本区域的服务中心已经存在!');
            // exit;
            // }
            
            if (empty($content)) {
                $this->error('请输入备注!');
                exit();
            }
            
            if ($fck_rs['l_nums'] == 0) {
                $nowdate = time();
                $result = $fck->query("update __TABLE__ set verify='" . $content . "',l_nums=1,shoplx=" . $shoplx . ",shop_a='" . $shop_a . "',shop_b='" . $shop_b . "',idt=$nowdate where id=" . $id);
            }
            
            $bUrl = __URL__ . '/agents';
            $this->_box(1, '申请成功！', $bUrl, 2);
        } else {
            $this->error('非法操作');
            exit();
        }
    }
    
    // 未开通会员
    public function menber($Urlsz = 0)
    {
        // 列表过滤器，生成查询Map对象
//         if ($_SESSION['Urlszpass'] == 'MyssShuiPuTao') {
            $fck = M('fck');
            $map = array();
            $id = $_SESSION[C('USER_AUTH_KEY')];
            $gid = (int) $_GET['bj_id'];
            $map['shop_id'] = $id;
            $UserID = $_POST['UserID'];
            if (! empty($UserID)) {
                import("@.ORG.KuoZhan"); // 导入扩展类
                $KuoZhan = new KuoZhan();
                if ($KuoZhan->is_utf8($UserID) == false) {
                    $UserID = iconv('GB2312', 'UTF-8', $UserID);
                }
                unset($KuoZhan);
                $where['nickname'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
                $where['user_id'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
                $where['_logic'] = 'or';
                $map['_complex'] = $where;
                $UserID = urlencode($UserID);
            }
            $map['is_pay'] = array(
                'eq',
                0
            );
            $map['shop_id'] = array(
                'eq',
                $id
            );
            
            // 查询字段
            $field = '*';
            // =====================分页开始==============================================
            import("@.ORG.ZQPage"); // 导入分页类
            $count = $fck->where($map)->count(); // 总页数
            $listrows = C('ONE_PAGE_RE'); // 每页显示的记录数
            $page_where = 'UserID=' . $UserID; // 分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            // ===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show(); // 分页变量
            $this->assign('page', $show); // 分页变量输出到模板
            $list = $fck->where($map)
                ->field($field)
                ->order('is_pay asc,pdt desc')
                ->page($Page->getPage() . ',' . $listrows)
                ->select();
            $this->assign('list', $list); // 数据输出到模板
                                         // =================================================
            
            $HYJJ = '';
            $this->_levelConfirm($HYJJ, 1);
            $this->assign('voo', $HYJJ); // 会员级别
            $where = array();
            $where['id'] = $id;
            $fck_rs = $fck->where($where)
                ->field('*')
                ->find();
            $this->assign('frs', $fck_rs); // 注册币
            $this->display('menber');
            exit();
//         } else {
//             $this->error('数据错误!');
//             exit();
//         }
    }
    
    // 原点升级审核列表
    public function memberuplevel($Urlsz = 0)
    {
        // 列表过滤器，生成查询Map对象
        if ($_SESSION['Urlszpass'] == 'MyssmenberUpLevel') {
            $id = $_SESSION[C('USER_AUTH_KEY')];
            $fck = M('fck');
            $where = array();
            $where['p_path'] = array('like',"%" . $id . "%");
            $fck_rs = $fck->where($where)->field('*')->find();
            $promo = M('promo');
            $map = array();
            // 查询字段
            $field = '*';
            // =====================分页开始==============================================
            import("@.ORG.ZQPage"); // 导入分页类
            $count = $promo->where($map)->count(); // 总页数
            $listrows = C('ONE_PAGE_RE'); // 每页显示的记录数
            $page_where = 'user_id=' . $fck_rs['user_id']; // 分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            // ===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show(); // 分页变量
            $this->assign('page', $show); // 分页变量输出到模板
            $list = $promo->where($map)->field($field)->order('is_pay asc,pdt desc')->page($Page->getPage() . ',' . $listrows)->select();
            $this->assign('list', $list); // 数据输出到模板
            // =================================================
            $HYJJ = '';
            $this->_levelConfirm($HYJJ, 1);
            $this->assign('voo', $HYJJ); // 会员级别
            $this->assign('frs', $fck_rs); // 注册币
            $this->display('memberuplevel');
            exit();
        } else {
            $this->error('数据错误!');
            exit();
        }
    }
    
    // 开通新网络审核列表
    public function netAorB($Urlsz = 0)
    {
        // 列表过滤器，生成查询Map对象
        if ($_SESSION['Urlszpass'] == 'MyssnetAorB') {
            $id = $_SESSION[C('USER_AUTH_KEY')];
            $fck = M('fck');
            $where = array();
            $where['p_path'] = array('like',"%" . $id . "%");
            $fck_rs = $fck->where($where)->field('*')->find();
            $promo = M('aorb');
            $map = array();
            // 查询字段
            $field = '*';
            // =====================分页开始==============================================
            import("@.ORG.ZQPage"); // 导入分页类
            $count = $promo->where($map)->count(); // 总页数
            $listrows = C('ONE_PAGE_RE'); // 每页显示的记录数
            $page_where = 'user_id=' . $fck_rs['user_id']; // 分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            // ===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show(); // 分页变量
            $this->assign('page', $show); // 分页变量输出到模板
            $list = $promo->where($map)->field($field)->order('is_pay asc,pdt desc')->page($Page->getPage() . ',' . $listrows)->select();
            $this->assign('list', $list); // 数据输出到模板
            // =================================================
            $HYJJ = '';
            $this->_levelConfirm($HYJJ, 1);
            $this->assign('voo', $HYJJ); // 会员级别
            $this->assign('frs', $fck_rs); // 注册币
            $this->display('netAorB');
            exit();
        } else {
            $this->error('数据错误!');
            exit();
        }
    }
    
    // 未开通会员
    public function menberok($Urlsz = 0)
    {
        // 列表过滤器，生成查询Map对象
//         if ($_SESSION['Urlszpass'] == 'Myssmenberok') {
            $fck = M('fck');
            $map = array();
            $id = $_SESSION[C('USER_AUTH_KEY')];
            $gid = (int) $_GET['bj_id'];
            $map['shop_id'] = $id;
            $map['is_pay'] = array(
                'gt',
                0
            );
            $UserID = $_POST['UserID'];
            if (! empty($UserID)) {
                import("@.ORG.KuoZhan"); // 导入扩展类
                $KuoZhan = new KuoZhan();
                if ($KuoZhan->is_utf8($UserID) == false) {
                    $UserID = iconv('GB2312', 'UTF-8', $UserID);
                }
                unset($KuoZhan);
                $where['nickname'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
                $where['user_id'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
                $where['_logic'] = 'or';
                $map['_complex'] = $where;
                $UserID = urlencode($UserID);
            }
            
            // 查询字段
            $field = '*';
            // =====================分页开始==============================================
            import("@.ORG.ZQPage"); // 导入分页类
            $count = $fck->where($map)->count(); // 总页数
            $listrows = C('ONE_PAGE_RE'); // 每页显示的记录数
            $page_where = 'UserID=' . $UserID; // 分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            // ===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show(); // 分页变量
            $this->assign('page', $show); // 分页变量输出到模板
            $list = $fck->where($map)
                ->field($field)
                ->order('is_pay asc,pdt desc')
                ->page($Page->getPage() . ',' . $listrows)
                ->select();
            $this->assign('list', $list); // 数据输出到模板
                                         // =================================================
            
            $HYJJ = '';
            $this->_levelConfirm($HYJJ, 1);
            $this->assign('voo', $HYJJ); // 会员级别
            $where = array();
            $where['id'] = $id;
            $fck_rs = $fck->where($where)
                ->field('*')
                ->find();
            $this->assign('frs', $fck_rs); // 注册币
            $this->display('menberok');
            exit();
//         } else {
//             $this->error('数据错误!');
//             exit();
//         }
    }

    public function menberAC()
    {
        // 处理提交按钮
        $action = $_POST['action'];
        // 获取复选框的值
        $OpID = $_POST['tabledb'];
        if (! isset($OpID) || empty($OpID)) {
            $bUrl = __URL__ . '/menber';
            $this->_box(0, '没有该会员！', $bUrl, 1);
            exit();
        }
        switch ($action) {
            case '开通会员':
                $this->_menberOpenUse($OpID, 1);
                break;
            
            case '删除会员':
                $this->_menberDelUse($OpID);
                break;
            default:
                $bUrl = __URL__ . '/menber';
                $this->_box(0, '没有该会员！', $bUrl, 1);
                break;
        }
    }
    // 原点升级晋级确认
    public function agentUpLevelConfirm(){
        // 获取复选框的值
        $OpID = $_POST['tabledb'];
        if (! isset($OpID) || empty($OpID)) {
            $bUrl = __URL__ . '/memberuplevel';
            $this->_box(0, '没有该会员！', $bUrl, 1);
            exit();
        }
        if ($_SESSION['Urlszpass'] == 'MyssmenberUpLevel'){
            $i = 0;
            ini_set("max_execution_time", 0);
            foreach ($OpID as $vo){
                $i++;
                $where = array();
                $where['id'] = $vo;
                $where['is_pay'] = 0;
                $promo = M('promo');
                $fck = D('Fck');
                $fee = M ('fee');
                $fee_rs =$fee->field('s1,s2,s9,s4,s5')->find();
                $s2 =explode('|',$fee_rs['s2']);//单量
                $s3 =explode('|',$fee_rs['s9']);//每单金额
                $promo_rs = $promo->where($where)->find();
                if (!$promo_rs) {
                    $this->error('会员已经升过级,请刷新页面重试！');
                    exit;
                }
                $fck_where = array();
                $fck_where['user_id'] = $promo_rs['user_id'];
                $fck_rs = $fck->where($fck_where)->find();
                $newlv = $promo_rs['up_level'];
                $oldlv  = $promo_rs['u_level'];
                //差额
                $need_m = $newlv-$oldlv;
                //单量
                $need_dl = bcdiv($need_m, $s3[0]);
                if($oldlv >=$newlv){
                    $this->error('只能向上升级');
                    exit;
                }
                if($oldlv >=40000){
                    $this->error('已经是最高级，无法再升级！');
                    exit;
                }
                $rs = $fck->where("id=".$fck_rs['id'])->field("*")->find();
                if (! $rs) {
                    $this->error('会员错误！');
                    exit();
                }
                $agent_cash = $rs['agent_cash'];
                if ($agent_cash < $need_m) {
                    $this->error('电子余额不足！');
                    exit;
                }
                // 减去电子币
                $minusResult = $fck->execute("update __TABLE__ set `agent_cash`=agent_cash-" . $need_m . " where `id`=" . $fck_rs['id']);
                if ($minusResult) {
                    //统计单数
                    $fck->xiangJiao($fck_rs['id'], $need_dl);
                    $fck->tz($fck_rs['p_path'],$need_m);
                    //各种奖项
                    $fck->tuijj($fck_rs['re_path'],$fck_rs['user_id'],$need_m);
                    $fck->lingdao22($fck_rs['p_path'],$fck_rs['user_id'],$need_m);
                    $fck->sh_level($fck_rs['p_path']);
                    $fck->baodanfei($fck_rs['shop_id'],$fck_rs['user_id'],$need_m,$fck_rs['is_agent']);
                    $fck->dsfenhong($fck_rs['p_path'],$fck_rs['user_id'],$need_m);
                    $fck->query("update __TABLE__ set is_xf=0,u_level=1".",cpzj=".$newlv.",f4=f4+".$need_dl." where `id`=".$fck_rs['id']);
                    // 分红包记录表
                    $nowdate = strtotime ("now");
                    // $fck->jiaDan($fck_rs['id'], $fck_rs['user_id'], $nowdate, 0, 0, $need_dl, 0, 1);]
                    $jiadan = M('jiadan');
                    $jiadan->query("update xt_jiadan set danshu=danshu+" . $need_dl ." where uid=" . $fck_rs['id']);
                    
                    if ($need_dl == 50) {
                        $nowdate = strtotime(date('c'));
                        $data['idt'] = $promo_rs['create_time'];
                        $data['adt'] = $nowdate;
                        $data['is_agent'] = 1;
                        // 设置报单中心审核
                        $result = $fck->where("user_id='". $fck_rs['user_id']."'")->save($data);
                    }
                    
                    // 添加物流信息
                    $pora = M('product');
                    $gouwu = D('Gouwu');
                    $gwd = array();
                    $gwd['uid'] = $fck_rs['id'];
                    $gwd['user_id'] = $fck_rs['user_id'];
                    $gwd['lx'] = 1;
                    $gwd['ispay'] = 0;
                    $gwd['pdt'] = mktime();
                    $gwd['us_name'] = $fck_rs['name'];
                    $gwd['us_address'] = $fck_rs['user_address'];
                    $gwd['us_tel'] = $fck_rs['user_tel'];
                    $where = array();
                    // 查询产品信息
                    $where['id'] = 22;
                    $prs = $pora->where($where)->find();
                    $w_money = $prs['a_money'];
                    $gwd['did'] = $prs['id'];
                    $gwd['money'] = $w_money;
                    $gwd['shu'] = $need_m/800;
                    $gwd['cprice'] = $need_m;
                    if(!empty($prs['countid'])){
                        $gwd['countid'] = $prs['countid'];
                    }
                    $gouwu->add($gwd);
                    
                    $promo->query("update xt_promo set is_pay=1 where `id`=".$vo);
                    unset($fck,$fee,$promo);
                }
            }
            if($OpID) {
                $bUrl = __URL__.'/memberuplevel/';
                $this->_box(1,'晋级成功！',$bUrl,3);
            }else{
                $this->error('晋级失败！');
                exit;
            }
        }else{
            $this->error('错误！');
            exit;
        }
    }
    
    // 开通新网络确认
    public function agentNetAorBConfirm(){
        // 获取复选框的值
        $OpID = $_POST['tabledb'];
        if (! isset($OpID) || empty($OpID)) {
            $bUrl = __URL__ . '/netAorB';
            $this->_box(0, '没有该会员！', $bUrl, 1);
            exit();
        }
        $model = M();
        M()->startTrans();
        if ($_SESSION['Urlszpass'] == 'MyssnetAorB'){
            $i = 0;
            ini_set("max_execution_time", 0);
            foreach ($OpID as $vo){
                $i++;
                $where = array();
                $where['user_id'] = $vo;
                $where['is_pay'] = 0;
                $promo = M('aorb');
                $fck = D('Fck');
                $promo_rs = $promo->where($where)->find();
                if (!$promo_rs) {
                    $this->error('会员新网络已经开通,请刷新页面重试！');
                    exit;
                }
                $fck_where = array();
                $fck_where['user_id'] = $promo_rs['user_id'];
                $fck_rs = $fck->where($fck_where)->find();
                
                $rs = $fck->where("id=".$fck_rs['id'])->field("*")->find();
                if (! $rs) {
                    $this->error('会员错误！');
                    exit();
                }
                $nowdate = strtotime(date('c'));
                // 物流表更新
                $pora = M('product');
                $gouwu = D('Gouwu');
                $gwd = array();
                $gwd['uid'] = $fck_rs['id'];
                $gwd['user_id'] = $fck_rs['user_id'];
                $gwd['lx'] = 1;
                $gwd['ispay'] = 0;
                $gwd['pdt'] = mktime();
                $gwd['us_name'] = $fck_rs['name'];
                $gwd['us_address'] = $fck_rs['user_address'];
                $gwd['us_tel'] = $fck_rs['user_tel'];
                $where = array();
                // 查询产品信息
                $where['id'] = 22;
                $prs = $pora->where($where)->find();
                $w_money = $prs['a_money'];
                $gwd['did'] = $prs['id'];
                $gwd['money'] = $w_money;
                $gwd['shu'] = $promo_rs['danshu'];
                $gwd['cprice'] = 40000;
                if(!empty($prs['countid'])){
                    $gwd['countid'] = $prs['countid'];
                }
                $gouwuResult = $model->table('xt_gouwu')->add($gwd);
                
                // 新网络审核表更新
                $aorbData['is_pay'] = 1;
                $aorbData['pdt'] = $nowdate;
                $aorbData['confirm_userid'] = $_SESSION['loginUseracc'];
                $aorbResult = $model->table('xt_aorb')->where("user_id = '".$vo."'")->save($aorbData);
                if ($fck_rs['net_status'] == 'b') {
                    // 剩余分红包迁移，分红包表更新
                    $jiadan = M('jiadan');
                    $jiadanb = M('jiadanb');
//                     $jiadan_rs = $jiadan->where('is_pay = 0 and uid = '.$fck_rs['id'])->field("uid,user_id,adt,pdt,money,danshu,is_pay,up_level,out_level")->select();
                    // 取得A网分红金额以及单数
                    $in_money = $jiadan->where('is_pay = 0 and uid = '.$fck_rs['id'])->sum('money');
                    $in_counts = $jiadan->where('is_pay = 0 and uid = '.$fck_rs['id'])->sum('danshu');
                    // 取得B网分红金额以及单数
                    $tmpMoney = $jiadanb->where('uid = '.$fck_rs['id'])->sum('money');
                    $tmpDanshu = $jiadanb->where('uid = '.$fck_rs['id'])->sum('danshu');
                    // 从B网向A网迁移，把A网剩余未分红包迁移到B网
                    $jiadanBContent['money'] = $tmpMoney+$in_money;
                    $jiadanBContent['danshu'] = $tmpDanshu+$in_counts;
                    $jiadanResult = $model->table('xt_jiadanb')->where("uid=". $fck_rs['id'])->save($jiadanBContent);
                    $jiadandeleteResult = $model->table('xt_jiadan')->where('uid = '.$fck_rs['id'])->delete();
                    // 会员表更新
                    $net_status['f4'] = 50;
                    $net_status['is_cc'] = 0;
                    $net_status['agent_xf'] = 0;
                    $net_status['net_status'] = 'a';
                    $net_status['net_ispay_a'] = 1;
                    $net_status['net_ispay_b'] = 0;
                    $net_status['net_top_a'] = 0;
                    $net_status['idt'] = $promo_rs['pdt'];
                    $net_status['adt'] = $nowdate;
                    $net_status['is_agent'] = 2;
                    $net_status['is_fenh'] = 0;
                    $fckResult = $model->table('xt_fck')->where("user_id='". $vo."'")->save($net_status);
                    $netB = M('netb');
                    $netBData = $netB->where('uid = '.$fck_rs['id'])->find();
                    $netBData['sum_bag'] += $in_counts;
                    $netBData['futou_danshu'] += $in_counts;
                    $netBData['in_bag'] += $in_counts;
                    $netResult = $model->table('xt_netb')->where('uid='.$fck_rs['id'])->save($netBData);
                } else {
                    // B网表更新
                    $netb = M('netb');
                    $netbWhere = array();
                    $netbWhere['uid'] = $fck_rs['id'];
                    $netb_rs = $netb->where($netbWhere)->find();
                    if ($netb_rs) {
                        // 剩余分红包迁移，分红包表更新
                        $jiadan = M('jiadan');
                        $jiadanb = M('jiadanb');
//                         $jiadanb_rs = $jiadanb->where('is_pay = 0 and uid = '.$fck_rs['id'])->field("uid,user_id,adt,pdt,money,danshu,is_pay,up_level,out_level")->select();
                        // 取得B网分红金额以及单数
                        $in_money = $jiadanb->where('is_pay = 0 and uid = '.$fck_rs['id'])->sum('money');
                        $in_counts = $jiadanb->where('is_pay = 0 and uid = '.$fck_rs['id'])->sum('danshu');
                        // 取得A网分红金额以及单数
                        $tmpMoney = $jiadan->where('uid = '.$fck_rs['id'])->sum('money');
                        $tmpDanshu = $jiadan->where('uid = '.$fck_rs['id'])->sum('danshu');
                        // 从A网向B网迁移，把B网剩余未分红包迁移到A网
                        $jiadanBContent['money'] = $tmpMoney+$in_money;
                        $jiadanBContent['danshu'] = $tmpDanshu+$in_counts;
                        $jiadanResult = $model->table('xt_jiadan')->where("uid=". $fck_rs['id'])->save($jiadanBContent);
//                         $jiadanResult = $model->table('xt_jiadan')->addAll($jiadanb_rs);
                        $jiadandeleteResult = $model->table('xt_jiadanb')->where('uid = '.$fck_rs['id'])->delete();
                        $netBData['sum_bag'] = 50;
                        $netBData['agent_futou'] = 0;
                        $netBData['register_danshu'] = 50;
                        $netBData['futou_danshu'] = 0;
                        $netBData['in_bag'] = 50;
                        $netBData['out_bag'] = 0;
                        $netBData['register_time'] = $nowdate;
                        $netResult = $model->table('xt_netb')->where('uid='.$fck_rs['id'])->save($netBData);
                        // 会员表更新
                        $net_status = $fck->where('id = '.$fck_rs['id'])->find();
                        $net_status['is_cc'] += $in_counts;
                        $net_status['net_status'] = 'b';
                        $net_status['net_ispay_a'] = 0;
                        $net_status['net_ispay_b'] = 1;
                        $net_status['net_top_b'] = 0;
                        $net_status['idt'] = $promo_rs['pdt'];
                        $net_status['adt'] = $nowdate;
                        $net_status['is_agent'] = 2;
                        $net_status['is_fenh'] = 0;
                        $fckResult = $model->table('xt_fck')->where("user_id='". $vo."'")->save($net_status);
                    } else {
                        $netBData['uid'] = $fck_rs['id'];
                        $netBData['user_id'] = $fck_rs['user_id'];
                        $netBData['sum_bag'] = 50;
                        $netBData['register_danshu'] = 50;
                        $netBData['futou_danshu'] = 0;
                        $netBData['in_bag'] = 50;
                        $netBData['out_bag'] = 0;
                        $netBData['register_time'] = $nowdate;
                        $netResult = $model->table('xt_netb')->add($netBData);
                        $jiadanResult = true;
                        $jiadandeleteResult = true;
                        $net_status['net_status'] = 'b';
                        $net_status['net_ispay_a'] = 0;
                        $net_status['net_ispay_b'] = 1;
                        $net_status['net_top_b'] = 0;
                        $net_status['idt'] = $promo_rs['pdt'];
                        $net_status['adt'] = $nowdate;
                        $net_status['is_agent'] = 2;
                        $net_status['is_fenh'] = 0;
                        $fckResult = $model->table('xt_fck')->where("user_id='". $vo."'")->save($net_status);
                    }
                }
                if($netResult && $jiadanResult && $jiadandeleteResult && $gouwuResult && $aorbResult && $fckResult) {
                    // 分红包记录表更新
                    if ($fck_rs['net_status'] == 'b'){
                        $nowdate = strtotime ("now");
                        $fck->jiaDan($fck_rs['id'], $fck_rs['user_id'], $nowdate, 0, 0, $promo_rs['danshu'], 0, 0);
                    } else {
                        $nowdate = strtotime ("now");
                        $fck->jiadanb($fck_rs['id'], $fck_rs['user_id'], $nowdate, 0, 0, $promo_rs['danshu'], 0, 0);
                    }
                    // 投资业绩统计更新
                    $fck->tz($fck_rs['p_path'],$promo_rs['money']);
                    //各种奖项分配更新
                    $fck->tuijj($fck_rs['re_path'],$fck_rs['user_id'],$promo_rs['money']);
                    $fck->sh_level($fck_rs['p_path']);
                }
                unset($fck,$fee,$promo);
            }
            if($netResult && $jiadanResult && $jiadandeleteResult && $gouwuResult && $aorbResult && $fckResult) {
                $model->commit();
                $bUrl = __URL__.'/netAorB/';
                $this->_box(1,'开通新网络成功！',$bUrl,3);
            }else{
                $model->rollback();
                $this->error('开通新网络失败！');
                exit;
            }
        }else{
            $this->error('错误！');
            exit;
        }
    }

    private function _menberOpenUse($OpID = 0, $reg_money = 0)
    {
        // =============================================开通会员
//         if ($_SESSION['Urlszpass'] == 'MyssShuiPuTao') {
            
            $fck = D('Fck');
            $fee = M('fee');
            $gouwu = D('Gouwu');
            $shouru = M('shouru');
            $blist = M('blist');
            $Guzhi = A('Guzhi');
            if (! $fck->autoCheckToken($_POST)) {
                $this->error('页面过期，请刷新页面！');
                exit();
            }
            
            // 被开通会员参数
            $where = array();
            $where['id'] = array(
                'in',
                $OpID
            ); // 被开通会员id数组
            $where['is_pay'] = 0; // 未开通的
            $field = '*';
            $vo = $fck->where($where)
                ->field($field)
                ->order('id asc')
                ->select();
            $fee_rs = $fee->field('str18,str19')->find();
            
            // 服务中心参数
            $where_two = array();
            $field_two = '*';
            $ID = $_SESSION[C('USER_AUTH_KEY')];
            $where_two['id'] = $ID;
            $nowdate = strtotime(date('c'));
            $nowday = strtotime(date('Y-m-d'));
            $nowmonth = date('m');
            $fck->emptyTime();
            ini_set("max_execution_time", 0);
            foreach ($vo as $voo) {
                $rs = $fck->where($where_two)
                    ->field($field_two)
                    ->find(); // 找出登录会员(必须为服务中心并且已经登录)
                if (! $rs) {
                    $this->error('会员错误！');
                    exit();
                }
                $ppath = $voo['p_path'];
                // 上级未开通不能开通下级员工
                $frs_where['is_pay'] = array(
                    'eq',
                    0
                );
                $frs_where['id'] = $voo['father_id'];
                $frs = $fck->where($frs_where)->find();
                
                $us_money = $rs['agent_cash'];
                $money_b = $voo['cpzj'];
                
                if ($us_money < $money_b) {
                    $bUrl = __URL__ . '/menber';
                    $this->_box(0, '电子积分余额不足！', $bUrl, 1);
                    exit();
                }
                $r_id = $rs['id'];
                $is_agent = $rs['is_agent'];
                if ($reg_money == 1) {
                    $result = $fck->execute("update __TABLE__ set `agent_cash`=agent_cash-" . $money_b . " where `id`=" . $ID);
                }
                if ($result) {
                    if ($reg_money == 1) {
                        $kt_cont = "开通会员";
                    }
                    // 奖金历史记录表
                    $fck->addencAdd($rs['id'], $voo['user_id'], $money_b, 26, 0, 0, 0, $kt_cont); 
                    // 分红包记录表
                    // 0.注册
                    $fck->jiaDan($voo['id'], $voo['user_id'], $nowdate, 0, 0, $voo['f4'] * 3000, 0, $voo['month_tag'], 0,1);
                    // 1.分红
                    $fck->jiaDan($voo['id'], $voo['user_id'], $nowdate, 0, 0, $voo['f4'] * 10000, 0, $voo['month_tag'], 1,1);
                                                                                               
                    $data = array();
                    $data['is_pay'] = 1;
                    $data['pdt'] = $nowdate;
                    $data['open'] = 0;
                    $data['get_date'] = $nowday;
                    $data['fanli_time'] = $nowday - 1; // 当天没有分红奖
                    $data['is_zy'] = $voo['id'];
                    $data['kt_id'] = $r_id;
                    
                    if ($voo['f4'] == 50) {
                        $data['idt'] = $nowdate;
                        $data['adt'] = $nowdate;
                        $data['is_agent'] = 1;
                    }
                    // 开通会员
                    $result = $fck->where('id=' . $voo['id'])->save($data);
                    unset($data, $varray);
                    
                    $data = array();
                    $data['uid'] = $voo['id'];
                    $data['user_id'] = $voo['user_id'];
                    $data['in_money'] = $voo['cpzj'];
                    $data['in_time'] = time();
                    $data['in_bz'] = "新会员加入";
                    $shouru->add($data);
                    unset($data);
                    
                    //统计单数
                    $fck->xiangJiao($voo['id'], 1);
                    // 算出奖金
                    $fck->getusjj($voo['id'], $voo['cpzj']);
                }
            }
            unset($fck, $where, $where_two, $rs);
            if ($vo) {
                unset($vo);
                $bUrl = __URL__ . '/menber';
                $this->_box(1, '开通会员成功！', $bUrl, 2);
                exit();
            } else {
                unset($vo);
                $bUrl = __URL__ . '/menber';
                $this->_box(0, '开通会员失败！', $bUrl, 1);
                exit();
            }
//         } else {
//             $this->error('错误！');
//             exit();
//         }
    }

    private function _menberDelUse($OpID = 0)
    {
        // =========================================删除会员
        if ($_SESSION['Urlszpass'] == 'MyssShuiPuTao') {
            $fck = M('fck');
            $jiadan = M('jiadan');
            $where['is_pay'] = 0;
            foreach ($OpID as $voo) {
                $rs = $fck->find($voo);
                if ($rs) {
                    $whe['father_name'] = $rs['user_id'];
                    $rss = $fck->where($whe)
                        ->field('id')
                        ->find();
                    if ($rss) {
                        $bUrl = __URL__ . '/menber';
                        $this->error('该 ' . $rs['user_id'] . ' 会员有下级会员，不能删除！');
                        exit();
                    } else {
                        $where['id'] = $voo;
                        $fck->where($where)->delete();
                        $jiadan->where("uid = ".$voo)->delete();
                    }
                } else {
                    $this->error('错误!');
                }
            }
            $bUrl = __URL__ . '/menber';
            $this->_box(1, '删除会员！', $bUrl, 1);
            exit();
        } else {
            $this->error('错误!');
        }
    }
    
    // 已开通会员
    public function frontMenber($Urlsz = 0)
    {
        // 列表过滤器，生成查询Map对象
        if ($_SESSION['Urlszpass'] == 'MyssDaShuiPuTao') {
            $fck = M('fck');
            $id = $_SESSION[C('USER_AUTH_KEY')];
            $map = array();
            $map['open'] = $id;
            $map['is_pay'] = array(
                'gt',
                0
            );
            $UserID = $_POST['UserID'];
            if (! empty($UserID)) {
                import("@.ORG.KuoZhan"); // 导入扩展类
                $KuoZhan = new KuoZhan();
                if ($KuoZhan->is_utf8($UserID) == false) {
                    $UserID = iconv('GB2312', 'UTF-8', $UserID);
                }
                unset($KuoZhan);
                $where['nickname'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
                $where['user_id'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
                $where['_logic'] = 'or';
                $map['_complex'] = $where;
                $UserID = urlencode($UserID);
            }
            
            // 查询字段
            $field = "*";
            // =====================分页开始==============================================
            import("@.ORG.ZQPage"); // 导入分页类
            $count = $fck->where($map)->count(); // 总页数
            $listrows = C('ONE_PAGE_RE'); // 每页显示的记录数
            $page_where = 'UserID=' . $UserID; // 分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            // ===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show(); // 分页变量
            $this->assign('page', $show); // 分页变量输出到模板
            $list = $fck->where($map)
                ->field($field)
                ->order('pdt desc')
                ->page($Page->getPage() . ',' . $listrows)
                ->select();
            
            $HYJJ = '';
            $this->_levelConfirm($HYJJ, 1);
            $this->assign('voo', $HYJJ); // 会员级别
            $this->assign('list', $list); // 数据输出到模板
                                         // =================================================
            
            $this->display('frontMenber');
            exit();
        } else {
            $this->error('数据错误2!');
            exit();
        }
    }

    public function adminAgents3()
    {
        // =====================================后台服务中心管理
        $this->_Admin_checkUser();
        if ($_SESSION['UrlPTPass'] == 'MyssGuanX') {
            $fck = M('fck');
            $UserID = $_POST['UserID'];
            if (! empty($UserID)) {
                import("@.ORG.KuoZhan"); // 导入扩展类
                $KuoZhan = new KuoZhan();
                if ($KuoZhan->is_utf8($UserID) == false) {
                    $UserID = iconv('GB2312', 'UTF-8', $UserID);
                }
                unset($KuoZhan);
                $where['nickname'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
                $where['user_id'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
                $where['_logic'] = 'or';
                $map['_complex'] = $where;
                $UserID = urlencode($UserID);
            }
            // $map['is_del'] = array('eq',0);
            $map['is_agent'] = array(
                'gt',
                0
            );
            if (method_exists($this, '_filter')) {
                $this->_filter($map);
            }
            $field = '*';
            // =====================分页开始==============================================
            import("@.ORG.ZQPage"); // 导入分页类
            $count = $fck->where($map)->count(); // 总页数
            $listrows = C('ONE_PAGE_RE'); // 每页显示的记录数
            $page_where = 'UserID=' . $UserID; // 分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            // ===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show(); // 分页变量
            $this->assign('page', $show); // 分页变量输出到模板
            $list = $fck->where($map)
                ->field($field)
                ->order('idt desc,id desc')
                ->page($Page->getPage() . ',' . $listrows)
                ->select();
            $this->assign('list', $list); // 数据输出到模板
                                         // =================================================
            
            $Agent_Us_Name = C('Agent_Us_Name');
            $Aname = explode("|", $Agent_Us_Name);
            $this->assign('Aname', $Aname);
            
            $this->display('adminAgents3');
            return;
        } else {
            $this->error('数据错误!');
            exit();
        }
    }

    public function adminAgents()
    {
        // =====================================后台服务中心管理
        $this->_Admin_checkUser();
        if ($_SESSION['UrlPTPass'] == 'MyssGuanXiGua') {
            $fck = M('fck');
            $UserID = $_POST['UserID'];
            if (! empty($UserID)) {
                import("@.ORG.KuoZhan"); // 导入扩展类
                $KuoZhan = new KuoZhan();
                if ($KuoZhan->is_utf8($UserID) == false) {
                    $UserID = iconv('GB2312', 'UTF-8', $UserID);
                }
                unset($KuoZhan);
                $where['nickname'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
                $where['user_id'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
                $where['_logic'] = 'or';
                $map['_complex'] = $where;
                $UserID = urlencode($UserID);
            }
            // $map['is_del'] = array('eq',0);
            $map['l_nums'] = array(
                'gt',
                0
            );
            if (method_exists($this, '_filter')) {
                $this->_filter($map);
            }
            $field = '*';
            // =====================分页开始==============================================
            import("@.ORG.ZQPage"); // 导入分页类
            $count = $fck->where($map)->count(); // 总页数
            $listrows = C('ONE_PAGE_RE'); // 每页显示的记录数
            $page_where = 'UserID=' . $UserID; // 分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            // ===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show(); // 分页变量
            $this->assign('page', $show); // 分页变量输出到模板
            $list = $fck->where($map)
                ->field($field)
                ->order('id desc')
                ->page($Page->getPage() . ',' . $listrows)
                ->select();
            $this->assign('list', $list); // 数据输出到模板
                                         // =================================================
            
            $Agent_Us_Name = C('Agent_Us_Name');
            $Aname = explode("|", $Agent_Us_Name);
            $this->assign('Aname', $Aname);
            
            $this->display('adminAgents');
            return;
        } else {
            $this->error('数据错误!');
            exit();
        }
    }

    public function adminAgents1()
    {
        // =====================================后台服务中心管理
        $this->_Admin_checkUser();
        if ($_SESSION['UrlPTPass'] == 'MyssGuanXiGu') {
            $fck = M('fck');
            $UserID = $_POST['UserID'];
            if (! empty($UserID)) {
                import("@.ORG.KuoZhan"); // 导入扩展类
                $KuoZhan = new KuoZhan();
                if ($KuoZhan->is_utf8($UserID) == false) {
                    $UserID = iconv('GB2312', 'UTF-8', $UserID);
                }
                unset($KuoZhan);
                $where['nickname'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
                $where['user_id'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
                $where['_logic'] = 'or';
                $map['_complex'] = $where;
                $UserID = urlencode($UserID);
            }
            // $map['is_del'] = array('eq',0);
            $map['is_cc'] = array(
                'gt',
                0
            );
            if (method_exists($this, '_filter')) {
                $this->_filter($map);
            }
            $field = '*';
            // =====================分页开始==============================================
            import("@.ORG.ZQPage"); // 导入分页类
            $count = $fck->where($map)->count(); // 总页数
            $listrows = C('ONE_PAGE_RE'); // 每页显示的记录数
            $page_where = 'UserID=' . $UserID; // 分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            // ===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show(); // 分页变量
            $this->assign('page', $show); // 分页变量输出到模板
            $list = $fck->where($map)
                ->field($field)
                ->order('id desc')
                ->page($Page->getPage() . ',' . $listrows)
                ->select();
            $this->assign('list', $list); // 数据输出到模板
                                         // =================================================
            
            $Agent_Us_Name = C('Agent_Us_Name');
            $Aname = explode("|", $Agent_Us_Name);
            $this->assign('Aname', $Aname);
            
            $this->display('adminAgents1');
            return;
        } else {
            $this->error('数据错误!');
            exit();
        }
    }

    public function adminAgents2()
    {
        // =====================================后台服务中心管理
        $this->_Admin_checkUser();
        if ($_SESSION['UrlPTPass'] == 'MyssGuanXiG') {
            $fck = M('fck');
            $UserID = $_POST['UserID'];
            if (! empty($UserID)) {
                import("@.ORG.KuoZhan"); // 导入扩展类
                $KuoZhan = new KuoZhan();
                if ($KuoZhan->is_utf8($UserID) == false) {
                    $UserID = iconv('GB2312', 'UTF-8', $UserID);
                }
                unset($KuoZhan);
                $where['nickname'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
                $where['user_id'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
                $where['_logic'] = 'or';
                $map['_complex'] = $where;
                $UserID = urlencode($UserID);
            }
            // $map['is_del'] = array('eq',0);
            $map['is_cha'] = array(
                'gt',
                0
            );
            if (method_exists($this, '_filter')) {
                $this->_filter($map);
            }
            $field = '*';
            // =====================分页开始==============================================
            import("@.ORG.ZQPage"); // 导入分页类
            $count = $fck->where($map)->count(); // 总页数
            $listrows = C('ONE_PAGE_RE'); // 每页显示的记录数
            $page_where = 'UserID=' . $UserID; // 分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            // ===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show(); // 分页变量
            $this->assign('page', $show); // 分页变量输出到模板
            $list = $fck->where($map)
                ->field($field)
                ->order('id desc')
                ->page($Page->getPage() . ',' . $listrows)
                ->select();
            $this->assign('list', $list); // 数据输出到模板
                                         // =================================================
            
            $Agent_Us_Name = C('Agent_Us_Name');
            $Aname = explode("|", $Agent_Us_Name);
            $this->assign('Aname', $Aname);
            
            $this->display('adminAgents2');
            return;
        } else {
            $this->error('数据错误!');
            exit();
        }
    }

    public function adminAgentsShow()
    {
        $fck = M('fck');
        $ID = (int) $_GET['Sid'];
        $where = array();
        $where['id'] = $ID;
        $srs = $fck->where($where)->field('user_id,verify')->find();
        $this->assign('srs', $srs);
        unset($fck, $where, $srs);
        $this->display('adminAgentsShow');
        return;
    }

    public function adminAgentsAC3(){
        // 检查用户是否登录
        $this->_checkUser();
        // 处理提交按钮
        $action = $_POST['action'];
        // 获取复选框的值
        $XGid = $_POST['tabledb'];
        $fck = M('fck');
        unset($fck);
        if (! isset($XGid) || empty($XGid)) {
            $bUrl = __URL__ . '/adminAgents';
            $this->_box(0, '请选择会员！', $bUrl, 1);
            exit();
        }
        switch ($action) {
            case '确认':
                $this->_adminAgentsConfirm3($XGid);
                break;
            case '删除':
                $this->_adminAgentsDel3($XGid);
                break;
            default:
                $bUrl = __URL__ . '/adminAgents3';
                $this->_box(0, '没有该会员！', $bUrl, 1);
                break;
        }
    }

    public function adminAgentsAC()
    { // 审核服务中心(服务中心)申请
        $this->_Admin_checkUser();
        // 处理提交按钮
        $action = $_POST['action'];
        // 获取复选框的值
        $XGid = $_POST['tabledb'];
        $fck = M('fck');
        // if (!$fck->autoCheckToken($_POST)){
        // $this->error('页面过期，请刷新页面！');
        // exit;
        // }
        
        unset($fck);
        if (! isset($XGid) || empty($XGid)) {
            $bUrl = __URL__ . '/adminAgents';
            $this->_box(0, '请选择会员！', $bUrl, 1);
            exit();
        }
        switch ($action) {
            case '确认':
                $this->_adminAgentsConfirm($XGid);
                break;
            case '删除':
                $this->_adminAgentsDel($XGid);
                break;
            default:
                $bUrl = __URL__ . '/adminAgents';
                $this->_box(0, '没有该会员！', $bUrl, 1);
                break;
        }
    }

    public function adminAgentsAC1()
    { // 审核服务中心(服务中心)申请
        $this->_Admin_checkUser();
        // 处理提交按钮
        $action = $_POST['action'];
        // 获取复选框的值
        $XGid = $_POST['tabledb'];
        $fck = M('fck');
        // if (!$fck->autoCheckToken($_POST)){
        // $this->error('页面过期，请刷新页面！');
        // exit;
        // }
        unset($fck);
        if (! isset($XGid) || empty($XGid)) {
            $bUrl = __URL__ . '/adminAgents1';
            $this->_box(0, '请选择会员！', $bUrl, 1);
            exit();
        }
        switch ($action) {
            case '确认':
                $this->_adminAgentsConfirm1($XGid);
                break;
            case '删除':
                $this->_adminAgentsDel1($XGid);
                break;
            default:
                $bUrl = __URL__ . '/adminAgents1';
                $this->_box(0, '没有该会员！', $bUrl, 1);
                break;
        }
    }

    public function adminAgentsAC2()
    { // 审核服务中心(服务中心)申请
        $this->_Admin_checkUser();
        // 处理提交按钮
        $action = $_POST['action'];
        // 获取复选框的值
        $XGid = $_POST['tabledb'];
        
        $fck = M('fck');
        // if (!$fck->autoCheckToken($_POST)){
        // $this->error('页面过期，请刷新页面！');
        // exit;
        // }
        
        unset($fck);
        if (! isset($XGid) || empty($XGid)) {
            $bUrl = __URL__ . '/adminAgents1';
            $this->_box(0, '请选择会员！', $bUrl, 1);
            exit();
        }
        switch ($action) {
            case '确认':
                $this->_adminAgentsConfirm2($XGid);
                break;
            case '删除':
                $this->_adminAgentsDel2($XGid);
                break;
            default:
                $bUrl = __URL__ . '/adminAgents2';
                $this->_box(0, '没有该会员！', $bUrl, 1);
                break;
        }
    }

    private function _adminAgentsConfirm3($XGid = 0)
    {
        // ==========================================确认申请服务中心
        if ($_SESSION['UrlPTPass'] == 'MyssGuanX') {
            $fck = D('Fck');
            $where['id'] = array(
                'in',
                $XGid
            );
            $where['is_agent'] = 1;
            $rs = $fck->where($where)
                ->field('*')
                ->select();
            
            $data = array();
//             $history = M('history');
            $rewhere = array();
            // $nowdate = strtotime(date('c'));
            $nowdate = time();
            $jiesuan = 0;
            foreach ($rs as $rss) {
                
                $myreid = $rss['re_id'];
                $shoplx = $rss['shoplx'];
                
//                 $data['user_id'] = $rss['user_id'];
//                 $data['uid'] = $rss['uid'];
//                 $data['action_type'] = '申请成为服务中心';
//                 $data['pdt'] = $nowdate;
//                 $data['epoints'] = $rss['agent_no'];
//                 $data['bz'] = '申请成为服务中心';
//                 $data['did'] = 0;
//                 $data['allp'] = 0;
//                 $history->add($data);
                $fck->addencAdd($rss['id'], $rss['user_id'], $rss['agent_no'], '申请成为服务中心', 0, 0, 0,'申请成为服务中心',$rss['agent_use'],$rss['agent_cash'],$rss['agent_xf'],$rss['agent_active']);
                
                $fck->query("UPDATE __TABLE__ SET is_agent=2,adt=$nowdate,agent_max=0 where id=" . $rss['id']); // 开通
            }
            unset($fck, $where, $rs, $history, $data, $rewhere);
            $bUrl = __URL__ . '/adminAgents3';
            $this->_box(1, '确认申请！', $bUrl, 1);
            exit();
        } else {
            $this->error('错误！');
            exit();
        }
    }

    private function _adminAgentsConfirm($XGid = 0)
    {
        // ==========================================确认申请服务中心
        if ($_SESSION['UrlPTPass'] == 'MyssGuanXiGua') {
            $fck = D('Fck');
            $where['id'] = array(
                'in',
                $XGid
            );
            $where['l_nums'] = 1;
            $rs = $fck->where($where)
                ->field('*')
                ->select();
            
            $data = array();
//             $history = M('history');
            $rewhere = array();
            // $nowdate = strtotime(date('c'));
            $nowdate = time();
            $jiesuan = 0;
            foreach ($rs as $rss) {
                
                $myreid = $rss['re_id'];
                $shoplx = $rss['shoplx'];
                
//                 $data['user_id'] = $rss['user_id'];
//                 $data['uid'] = $rss['uid'];
//                 $data['action_type'] = '申请成为商家';
//                 $data['pdt'] = $nowdate;
//                 $data['epoints'] = $rss['agent_no'];
//                 $data['bz'] = '申请成为商家';
//                 $data['did'] = 0;
//                 $data['allp'] = 0;
//                 $history->add($data);
                $fck->addencAdd($rss['id'], $rss['user_id'], $rss['agent_no'], '申请成为商家', 0, 0, 0,'申请成为商家',$rss['agent_use'],$rss['agent_cash'],$rss['agent_xf'],$rss['agent_active']);
                $fck->query("UPDATE __TABLE__ SET l_nums=2,adt=$nowdate,agent_max=0 where id=" . $rss['id']); // 开通
            }
            unset($fck, $where, $rs, $history, $data, $rewhere);
            $bUrl = __URL__ . '/adminAgents';
            $this->_box(1, '确认申请！', $bUrl, 1);
            exit();
        } else {
            $this->error('错误！');
            exit();
        }
    }

    public function cate($id = 0)
    {
        $fck = M('fck');
        $res = $fck->where('id=' . $id)
            ->field('id,kt_id,is_agent')
            ->find();
        // print_r($res);die;
        
        if ($res) {
            
            if ($res['is_agent'] == 2) {
                
                $arr = $res['id'];
            } else {
                $ar = $res['kt_id'];
                $arr = $res['id'];
                $arr = $this->cate($ar);
            }
            
            return $arr;
        }
    }

    public function adminAgentsCoirmAC()
    {
        if ($_SESSION['UrlPTPass'] == 'MyssGuanXiGua') {
            // $this->_checkUser();
            $fck = M('fck');
            $content = $_POST['content'];
            $userid = trim($_POST['userid']);
            $where['user_id'] = $userid;
            // $rs=$fck->where($where)->find();
            $fck_rs = $fck->where($where)
                ->field('id,is_agent,is_pay,user_id,user_name,agent_max,is_agent')
                ->find();
            
            if ($fck_rs) {
                if ($fck_rs['is_pay'] == 0) {
                    $this->error('临时代理商不能授权服务中心!');
                    exit();
                }
                if ($fck_rs['is_agent'] == 1) {
                    $this->error('上次申请还没通过审核!');
                    exit();
                }
                if ($fck_rs['is_agent'] == 2) {
                    $this->error('该代理商已是服务中心!');
                    exit();
                }
                if (empty($content)) {
                    $this->error('请输入备注!');
                    exit();
                }
                
                if ($fck_rs['is_agent'] == 0) {
                    $nowdate = time();
                    $result = $fck->query("update __TABLE__ set verify='" . $content . "',is_agent=2,idt=$nowdate,adt={$nowdate} where id=" . $fck_rs['id']);
                }
                
                $bUrl = __URL__ . '/adminAgents';
                $this->_box(1, '授权成功！', $bUrl, 2);
            } else {
                $this->error('会员不存在！');
                exit();
            }
        } else {
            $this->error('错误！');
            exit();
        }
    }

    private function _adminAgentsDel1($XGid = 0)
    {
        // =======================================删除申请服务中心信息
        if ($_SESSION['UrlPTPass'] == 'MyssGuanXiGu') {
            $fck = M('fck');
            $rewhere = array();
            $where['is_cc'] = array(
                'gt',
                0
            );
            $where['id'] = array(
                'in',
                $XGid
            );
            $rs = $fck->where($where)->select();
            foreach ($rs as $rss) {
                $fck->query("UPDATE __TABLE__ SET is_cc=0,idt=0 where id>1 and id = " . $rss['id']);
            }
            
            // $shop->where($where)->delete();
            unset($fck, $where, $rs, $rewhere);
            $bUrl = __URL__ . '/adminAgents1';
            $this->_box('操作成功', '删除申请！', $bUrl, 1);
            exit();
        } else {
            $this->error('错误!');
            exit();
        }
    }

    private function _adminAgentsDel($XGid = 0)
    {
        // =======================================删除申请服务中心信息
        if ($_SESSION['UrlPTPass'] == 'MyssGuanXiGua') {
            $fck = M('fck');
            $rewhere = array();
            $where['l_nums'] = array(
                'gt',
                0
            );
            $where['id'] = array(
                'in',
                $XGid
            );
            $rs = $fck->where($where)->select();
            foreach ($rs as $rss) {
                $fck->query("UPDATE __TABLE__ SET l_nums=0,idt=0,adt=0,new_agent=0,shoplx=0,shop_a='',shop_b='' where id>1 and id = " . $rss['id']);
            }
            
            // $shop->where($where)->delete();
            unset($fck, $where, $rs, $rewhere);
            $bUrl = __URL__ . '/adminAgents';
            $this->_box('操作成功', '删除申请！', $bUrl, 1);
            exit();
        } else {
            $this->error('错误!');
            exit();
        }
    }

    private function _adminAgentsDel3($XGid = 0)
    {
        // =======================================删除申请服务中心信息
        $fck = M('fck');
        $rewhere = array();
        $where['is_agent'] = array(
            'gt',
            0
        );
        $where['id'] = array(
            'in',
            $XGid
        );
        $rs = $fck->where($where)->select();
        foreach ($rs as $rss) {
            $fck->query("UPDATE __TABLE__ SET is_agent=0,idt=0,adt=0,new_agent=0,shoplx=0,shop_a='',shop_b='' where id>1 and id = " . $rss['id']);
        }
        
        unset($fck, $where, $rs, $rewhere);
        $bUrl = __URL__ . '/adminAgents3';
        $this->_box('操作成功', '删除申请！', $bUrl, 1);
        exit();
    }
    // 服务中心表
    public function financeDaoChu_BD()
    {
        $this->_checkUser();
        // 导出excel
        set_time_limit(0);
        
        header("Content-Type:   application/vnd.ms-excel");
        header("Content-Disposition:   attachment;   filename=Member-Agent.xls");
        header("Pragma:   no-cache");
        header("Content-Type:text/html; charset=utf-8");
        header("Expires:   0");
        
        $fck = M('fck'); // 会员表
        $map = array();
        $map['id'] = array(
            'gt',
            0
        );
        $map['is_agent'] = array(
            'gt',
            0
        );
        $field = '*';
        $list = $fck->where($map)
            ->field($field)
            ->order('idt desc,adt desc,id desc')
            ->select();
        
        $title = "服务中心表 导出时间:" . date("Y-m-d   H:i:s");
        
        echo '<table   border="1"   cellspacing="2"   cellpadding="2"   width="50%"   align="center">';
        // 输出标题
        echo '<tr   bgcolor="#cccccc"><td   colspan="9"   align="center">' . $title . '</td></tr>';
        // 输出字段名
        echo '<tr  align=center>';
        echo "<td>序号</td>";
        echo "<td>会员编号</td>";
        echo "<td>姓名</td>";
        echo "<td>联系电话</td>";
        echo "<td>申请时间</td>";
        echo "<td>确认时间</td>";
        echo "<td>剩余注册币</td>";
        echo '</tr>';
        // 输出内容
        
        // dump($list);exit;
        
        $i = 0;
        foreach ($list as $row) {
            $i ++;
            $num = strlen($i);
            if ($num == 1) {
                $num = '000' . $i;
            } elseif ($num == 2) {
                $num = '00' . $i;
            } elseif ($num == 3) {
                $num = '0' . $i;
            } else {
                $num = $i;
            }
            
            echo '<tr align=center>';
            echo '<td>' . chr(28) . $num . '</td>';
            echo "<td>" . $row['user_id'] . "</td>";
            echo "<td>" . $row['user_name'] . "</td>";
            echo "<td>" . $row['user_tel'] . "</td>";
            echo "<td>" . date("Y-m-d H:i:s", $row['idt']) . "</td>";
            echo "<td>" . date("Y-m-d H:i:s", $row['adt']) . "</td>";
            echo "<td>" . $row['agent_cash'] . "</td>";
            echo '</tr>';
        }
        echo '</table>';
    }
}
?>