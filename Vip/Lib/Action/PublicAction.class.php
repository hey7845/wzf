<?php

class PublicAction extends CommonAction
{

    public function _initialize()
    {
        header("Content-Type:text/html; charset=utf-8");
        $this->_inject_check(1); // 调用过滤函数
        $this->_Config_name(); // 调用参数
    }
    
    // 过滤查询字段
    function _filter(&$map)
    {
        $map['title'] = array(
            'like',
            "%" . $_POST['name'] . "%"
        );
    }
    // 顶部页面
    public function top()
    {
        C('SHOW_RUN_TIME', false); // 运行时间显示
        C('SHOW_PAGE_TRACE', false);
        $this->display();
    }
    // 尾部页面
    public function footer()
    {
        C('SHOW_RUN_TIME', false); // 运行时间显示
        C('SHOW_PAGE_TRACE', false);
        $this->display();
    }
    // 菜单页面
    public function menu()
    {
        $this->_checkUser();
        $map = array();
        $id = $_SESSION[C('USER_AUTH_KEY')];
        $field = '*';
        
        $map = array();
        $map['s_uid'] = $id; // 会员ID
        $map['s_read'] = 0; // 0 为未读
        $info_count = M('msg')->where($map)->count(); // 总记录数
        $this->assign('info_count', $info_count);
        
        $fck = M('fck');
        $fwhere = array();
        $fwhere['ID'] = $_SESSION[C('USER_AUTH_KEY')];
        $frs = $fck->where($fwhere)
            ->field('*')
            ->find();
        $HYJJ = '';
        $this->_levelConfirm($HYJJ, 1);
        $this->assign('voo', $HYJJ);
        
        $this->assign('fck_rs', $frs);
        $this->display('menu');
    }
    
    // 后台首页 查看系统信息
    public function main()
    {
        $this->_checkUser();
        $id = $_SESSION[C('USER_AUTH_KEY')]; // 登录AutoId
        $fck = M('fck');
        $jiadan = M('Jiadan');
        $jiadanb = M('jiadanb');
        // 会员级别
        $urs = $fck->where('id=' . $id)
            ->field('*')
            ->find();
        $this->assign('fck_rs', $urs); // 总奖金
        // 团队人数
        $all_nn = $fck->where('re_path like "%,' . $id . ',%" and is_pay=1')->count();
        $this->assign('all_nn', $all_nn);
        // 团队总业绩
        $nowdate = strtotime(date('Y-m-d'));
        $all_nmoney = $fck->where('p_path like "%,' . $id . ',%" and is_pay=1 and pdt<' . $nowdate)->sum('cpzj');
        if (empty($all_nmoney)) {
            $all_nmoney = 0.00;
        }
        $this->assign('all_nmoney', $all_nmoney);
        // 出局分红包数
        $where = Array();
        $where['user_id'] = $urs['user_id'];
        $out_counts = $jiadan->where($where)->field("money,danshu")->find();
        if ($out_counts) {
            $outCounts = floor(bcdiv($out_counts['money'], 1000,5));
        } else {
            $outCounts = 0;
        }
        $this->assign('out_counts', $outCounts);
        // 未出局分红包数
        $where['user_id'] = $urs['user_id'];
        $in_counts = $jiadan->where($where)->field("money,danshu")->find();
        if ($in_counts) {
            $inCounts = ceil(bcdiv(($in_counts['danshu']*1000 - $in_counts['money']), 1000,5));
        } else {
            $inCounts = 0;
        }
        $this->assign('in_counts', $inCounts);
        // B网版块
        $in_countsb = $jiadanb->where($where)->field("money,danshu")->find();
        if ($in_countsb) {
            $inCountsb = ceil(bcdiv(($in_countsb['danshu']*1000 - $in_countsb['money']), 1000,5));
        } else {
            $inCountsb = 0;
        }
        $this->assign('in_countsb', $inCountsb);
        $out_countsb = $jiadanb->where($where)->field("money,danshu")->find();
        if ($out_countsb) {
            $outCountsb = floor(bcdiv($out_countsb['money'], 1000,5));
        } else {
            $outCountsb = 0;
        }
        $this->assign('out_countsb', $outCountsb);
        $netb = M('netb');
        $netb_rs = $netb->where('uid=' . $id)->field('*')->find();
        $this->assign('netB', $netb_rs);
        
        $HYJJ = "";
        $this->_levelConfirm($HYJJ, 1);
        $this->assign('voo', $HYJJ); // 会员级别
        
        $see = $_SERVER['HTTP_HOST'] . __APP__;
        $see = str_replace("//", "/", $see);
        $this->assign('server', $see);
        $this->display();
    }
    
    // 用户登录页面
    public function login()
    {
        $fee = M('fee');
        $fee_rs = $fee->field('str21,i9')->find();
        $this->assign('fflv', $fee_rs['str21']);
        $this->assign('i9', $fee_rs['i9']);
        unset($fee, $fee_rs);
        $this->display('login6');
    }

    public function index()
    {
        // 如果通过认证跳转到首页
        redirect(__APP__);
    }
    
    // 用户登出
    public function LogOut()
    {
        $_SESSION = array();
        // unset($_SESSION);
        $this->assign('jumpUrl', __URL__ . '/login/');
        $this->success('退出成功！');
    }
    
    // 登录检测
    public function checkLogin()
    {
        if (empty($_POST['account'])) {
            $this->error('请输入帐号！');
        } elseif (empty($_POST['password'])) {
            $this->error('请输入密码！');
        } elseif (empty($_POST['verify'])) {
            $this->error('请输入验证码！');
        }
        $fee = M('fee');
        // $sel = (int) $_POST['radio'];
        // if($sel <=0 or $sel >=3){
        // $this->error('非法操作！');
        // exit;
        // }
        // if($sel != 1){
        // $this->error('暂时不支持英文版登录！');
        // exit;
        // }
        
        // 生成认证条件
        $map = array();
        // 支持使用绑定帐号登录
        $map['user_id'] = $_POST['account'];
        if ($_SESSION['verify'] != md5($_POST['verify'])) {
            $this->error('验证码错误！');
        }
        
        import('@.ORG.RBAC');
        $fck = M('fck');
        $field = 'id,user_id,password,is_pay,is_lock,nickname,user_name,is_agent,user_type,last_login_time,login_count,is_boss,is_aa,remark,is_treasure_manager';
        $authInfo = $fck->where($map)
            ->field($field)
            ->find();
        // 使用用户名、密码和状态的方式进行认证
        if (false == $authInfo) {
            $this->error('帐号不存在或已禁用！');
        } else {
            if ($authInfo['password'] != md5($_POST['password'])) {
                $this->error('密码错误！');
                exit();
            }
            
            if ($_POST['lang'] == 1) {
                $this->error('英文版本暂时无法登陆，请选择中文版本！');
                exit();
            }
            
            if ($_POST['agent'] == 2 && $authInfo['is_agent'] < $_POST['agent']) {
                $this->error('您为非报单中心,请选择会员登录入口！');
                exit();
            }
            
            if ($authInfo['is_pay'] < 1) {
                $this->error('用户尚未开通，暂时不能登录系统！');
                exit();
            }
            if ($authInfo['is_lock'] != 0) {
                $this->error('用户已锁定，请与管理员联系！');
                exit();
            }
            $_SESSION[C('USER_AUTH_KEY')] = $authInfo['id'];
            $_SESSION['loginUseracc'] = $authInfo['user_id']; // 用户名
            $_SESSION['loginNickName'] = $authInfo['nickname']; // 会员名
            $_SESSION['loginUserName'] = $authInfo['user_name']; // 开户名
            $_SESSION['lastLoginTime'] = $authInfo['last_login_time'];
            $_SESSION['login_isAgent'] = $authInfo['is_agent']; // 是否报单中心
            $_SESSION['is_aa'] = $authInfo['is_aa']; // 是否为物流管理员
            $_SESSION['remark'] = $authInfo['remark']; // 是否为服务中心管理员
            $_SESSION['is_treasure_manager'] = $authInfo['is_treasure_manager']; // 是否为服务中心管理员
            $_SESSION['UserMktimes'] = mktime();
            $news = M('form');
            $news_result = $news->where('status = 1')->field('title')->select();
            $_SESSION['news'] = $news_result; // 新闻信息
            // 身份确认 = 用户名+识别字符+密码
            $_SESSION['login_sf_list_u'] = md5($authInfo['user_id'] . 'wodetp_new_1012!@#' . $authInfo['password'] . $_SERVER['HTTP_USER_AGENT']);
            
            // 登录状态
            $user_type = md5($_SERVER['HTTP_USER_AGENT'] . 'wtp' . rand(0, 999999));
            $_SESSION['login_user_type'] = $user_type;
            $where['id'] = $authInfo['id'];
            $fck->where($where)->setField('user_type', $user_type);
            // 管理员
            $parmd = $this->_cheakPrem();
            if ($authInfo['id'] == 1 || $parmd[11] == 1) {
                $_SESSION['administrator'] = 1;
            } else {
                $_SESSION['administrator'] = 2;
            }
            $fck->execute("update __TABLE__ set last_login_time=new_login_time,last_login_ip=new_login_ip,new_login_time=" . time() . ",new_login_ip='" . $_SERVER['REMOTE_ADDR'] . "' where id=" . $authInfo['id']);
            
            // 缓存访问权限
            RBAC::saveAccessList();
            $this->success('登录成功！');
        }
    }
    // 二级密码验证
    public function cody()
    {
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
        $fck = M('cody');
        $list = $fck->where("c_id=$UrlID")->getField('c_id');
        
        if (! empty($list)) {
            $this->assign('vo', $list);
            $this->display('cody');
            exit();
        } else {
            $this->error('二级密码错误!');
            exit();
        }
    }
    // 二级验证后调转页面
    public function codys()
    {
        $Urlsz = $_POST['Urlsz'];
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
                ->field('id')
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
                $_SESSION['DLTZURL02'] = 'updateUserInfo';
                $bUrl = __URL__ . '/updateUserInfo'; // 修改资料
                $this->_boxx($bUrl);
                break;
            case 2:
                $_SESSION['DLTZURL01'] = 'password';
                $bUrl = __URL__ . '/password'; // 修改密码
                $this->_boxx($bUrl);
                break;
            case 3:
                $_SESSION['DLTZURL01'] = 'pprofile';
                $bUrl = __URL__ . '/pprofile'; // 修改密码
                $this->_boxx($bUrl);
                break;
            case 4:
                $_SESSION['DLTZURL01'] = 'OURNEWS';
                $bUrl = __URL__ . '/News'; // 修改密码
                $this->_boxx($bUrl);
                break;
            default:
                $this->error('二级密码错误!');
                break;
        }
    }

    public function verify()
    {
        ob_clean();
        $type = isset($_GET['type']) ? $_GET['type'] : 'gif';
        import("@.ORG.Image");
        Image::buildImageVerify();
    }
}
?>